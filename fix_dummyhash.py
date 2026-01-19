import re

def fix_all_passwords():
    # Valid hash for 'password'
    password_hash = '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m'

    with open('db.sql', 'r', encoding='utf-8') as f:
        content = f.read()

    # Replace 'dummyhash' version
    new_content = content.replace('$2y$12$dummyhash', password_hash)
    
    # Also check for any plain 'password' just in case (though I did this before)
    # user_record_pattern = re.compile(r"\((\d+),\s*'([^']*)',\s*'([^']*)',\s*'password'", re.MULTILINE)
    # new_content = user_record_pattern.sub(lambda m: f"({m.group(1)}, '{m.group(2)}', '{m.group(3)}', '{password_hash}'", new_content)

    with open('db.sql', 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print("Fixed all 'dummyhash' instances in db.sql")

if __name__ == "__main__":
    fix_all_passwords()
