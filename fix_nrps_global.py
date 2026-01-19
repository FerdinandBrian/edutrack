import re

def fix_nrps_globally():
    with open('db.sql', 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Identify all NRP mappings from the mahasiswa table
    insert_pattern = re.compile(r"INSERT INTO `mahasiswa` \(.*?\) VALUES(.*?);", re.DOTALL)
    mhs_match = insert_pattern.search(content)
    if not mhs_match:
        print("Could not find mahasiswa block")
        return

    mhs_values = mhs_match.group(1)
    record_pattern = re.compile(r"\((.*?)\)", re.DOTALL)
    mhs_records = record_pattern.findall(mhs_values)

    nrp_map = {} # old_nrp -> new_nrp (email prefix)
    
    def get_parts(record_inner):
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
        return parts

    for rec in mhs_records:
        parts = get_parts(rec)
        if len(parts) >= 5:
            old_nrp = parts[0].strip("'")
            email = parts[4].strip("'")
            new_nrp = email.split('@')[0]
            if old_nrp != new_nrp:
                nrp_map[old_nrp] = new_nrp

    if not nrp_map:
        print("No NRP alignment needed.")
        return

    print(f"Aligning {len(nrp_map)} NRPs...")

    # 2. Update the mahasiswa table content
    def update_mhs_record(match):
        inner = match.group(1)
        parts = get_parts(inner)
        if len(parts) >= 5:
            old_nrp = parts[0].strip("'")
            if old_nrp in nrp_map:
                parts[0] = f"'{nrp_map[old_nrp]}'"
                return "(" + ",".join(parts) + ")"
        return match.group(0)

    new_mhs_values = record_pattern.sub(update_mhs_record, mhs_values)
    content = content.replace(mhs_values, new_mhs_values)

    # 3. Update other tables that use NRP
    # We must be careful not to replace partial strings.
    # We'll target patterns like ', 'OLDNRP', ' or (ID, 'OLDNRP', ...
    for old, new in nrp_map.items():
        # Case: in middle of record ', 'OLDNRP', '
        content = content.replace(f"'{old}'", f"'{new}'")

    with open('db.sql', 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("Alignment completed successfully.")

if __name__ == "__main__":
    fix_nrps_globally()
