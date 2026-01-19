import re

def verify_passwords():
    with open('db.sql', 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the start of users table dumping
    match = re.search(r"INSERT INTO `users` .*? VALUES", content)
    if not match:
        print("Could not find INSERT INTO users")
        return

    start_pos = match.end()
    # Find the end of this INSERT statement (semicolon)
    end_pos = content.find(";", start_pos)
    if end_pos == -1:
        print("Could not find end of INSERT INTO users")
        return

    values_str = content[start_pos:end_pos]
    
    # Split by common record separator ), (
    # This is slightly fragile but usually works for SQL dumps
    records = re.findall(r"\((.*?)\)", values_str, re.DOTALL)
    
    invalid_count = 0
    for i, record in enumerate(records):
        # Extract fields. Password is usually the 4th field.
        # Format: (id, 'nama', 'email', 'password', 'role', ...
        fields = re.findall(r"'([^']*)'|\d+", record)
        # Re-evaluating field extraction. Commas might be inside names, but SQL uses escaped quotes.
        # Simple split by comma might work if we are careful, but re.findall is better for quoted strings.
        
        # Actually, let's just find the first thing that looks like a hash or password.
        # It's at index 3 (0-indexed) if the structure is (id, nama, email, password, role)
        # Let's check the schema again. 
        # (id, nama, email, password, role, created_at, updated_at)
        
        # Let's use a more robust separator
        parts = []
        current = ""
        in_string = False
        for char in record:
            if char == "'":
                in_string = not in_string
                current += char
            elif char == "," and not in_string:
                parts.append(current.strip())
                current = ""
            else:
                current += char
        parts.append(current.strip())
        
        if len(parts) >= 4:
            pw_part = parts[3]
            if pw_part.startswith("'") and pw_part.endswith("'"):
                pw = pw_part[1:-1]
                if not pw.startswith("$2y$") or len(pw) != 60:
                    print(f"Record {i+1} has invalid password: {pw_part} (ID: {parts[0]})")
                    invalid_count += 1
    
    print(f"Finished verification. Found {invalid_count} invalid passwords.")

if __name__ == "__main__":
    verify_passwords()
