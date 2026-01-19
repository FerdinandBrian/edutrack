import re

def analyze_mismatches():
    with open('db.sql', 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the INSERT INTO `mahasiswa` block
    insert_pattern = re.compile(r"INSERT INTO `mahasiswa` \(.*?\) VALUES(.*?);", re.DOTALL)
    match = insert_pattern.search(content)
    if not match:
        print("Could not find INSERT INTO `mahasiswa` block")
        return

    values = match.group(1)
    record_pattern = re.compile(r"\((.*?)\)", re.DOTALL)
    records = record_pattern.findall(values)

    mismatches = []
    for record_inner in records:
        # Simple split by comma, works as long as no commas in strings (rare in this dump)
        # For data accuracy, let's use a better split
        parts = []
        current = ""
        in_string = False
        for char in record_inner:
            if char == "'":
                in_string = not in_string
                current += char
            elif char == "," and not in_string:
                parts.append(current.strip())
                current = ""
            else:
                current += char
        parts.append(current.strip())

        if len(parts) >= 5:
            nrp = parts[0].strip("'")
            email = parts[4].strip("'")
            email_prefix = email.split('@')[0]
            
            if nrp != email_prefix:
                mismatches.append((nrp, email_prefix, parts[2].strip("'")))

    if mismatches:
        print(f"Found {len(mismatches)} mismatches:")
        for old, new, name in mismatches:
            print(f"- {name}: NRP {old} vs Email {new}")
    else:
        print("No mismatches found between NRP and Email prefix.")

if __name__ == "__main__":
    analyze_mismatches()
