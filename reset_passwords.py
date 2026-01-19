import re

def set_all_to_123456():
    # Hash for "123456"
    target_hash = '$2y$10$W3G4opYBZQdceCSnoZNJKu/WgIPbZMKSkIw3gKFVnWd0DrpwgkBbe'

    with open('db.sql', 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the INSERT INTO `users` block
    insert_pattern = re.compile(r"(INSERT INTO `users` \(.*?\) VALUES)(.*?);", re.DOTALL)
    
    def process_insert(match):
        header = match.group(1)
        values = match.group(2)
        
        # In the values section, records are separated by ), (
        # Each record is (id, 'nama', 'email', 'password', 'role', ...)
        # We can find the 4th field.
        
        # A safer way is to find each record and replace the field.
        record_pattern = re.compile(r"\((.*?)\)", re.DOTALL)
        
        def process_record(rec_match):
            record_inner = rec_match.group(1)
            # Split record fields manually to handle potential commas in strings
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
            
            # The password is the 4th part (index 3)
            if len(parts) >= 4:
                parts[3] = f"'{target_hash}'"
                
            return "(" + ",".join(parts) + ")"
        
        new_values = record_pattern.sub(process_record, values)
        return header + new_values + ";"

    new_content = insert_pattern.sub(process_insert, content)

    with open('db.sql', 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print("Updated all existing user passwords in db.sql to '123456'")

if __name__ == "__main__":
    set_all_to_123456()
