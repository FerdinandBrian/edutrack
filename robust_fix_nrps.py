import re

def record_level_fix_nrps():
    with open('db.sql', 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Fix Mahasiswa table first and collect EXACT mappings per student entry
    # We'll use (old_nrp, email) as a composite key to be safe, or just iterate once.
    
    mhs_pattern = re.compile(r"INSERT INTO `mahasiswa` \(.*?\) VALUES(.*?);", re.DOTALL)
    mhs_match = mhs_pattern.search(content)
    if not mhs_match:
        print("MHS block not found")
        return

    mhs_values = mhs_match.group(1)
    record_pattern = re.compile(r"\((.*?)\)", re.DOTALL)
    
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

    nrp_map = {} # old_nrp -> new_nrp (email prefix)

    def process_mhs_record(match):
        inner = match.group(1)
        p = get_parts(inner)
        if len(p) >= 5:
            old = p[0].strip("'")
            email = p[4].strip("'")
            correct = email.split('@')[0]
            if old != correct:
                nrp_map[old] = correct
                p[0] = f"'{correct}'"
                return "(" + ",".join(p) + ")"
        return match.group(0)

    new_mhs_values = record_pattern.sub(process_mhs_record, mhs_values)
    content = content.replace(mhs_values, new_mhs_values)

    # 2. Fix other tables. Since we have non-unique old NRPs now (due to previous mess), 
    # we should ideally know which NRP belongs to which user.
    # But in other tables (dkbs, etc), we only have NRP.
    # If two users share an NRP in DKBS... well, the data is already mixed up there.
    # However, usually there's only one "actual" student per NRP in those tables.
    
    # We'll just apply the map.
    tables_to_fix = [('dkbs', 1), ('presensi', 1), ('nilai', 1), ('tagihan', 1)]
    for table_name, col_idx in tables_to_fix:
        pattern = re.compile(rf"INSERT INTO `{table_name}` \(.*?\) VALUES(.*?);", re.DOTALL)
        table_match = pattern.search(content)
        if not table_match: continue
        
        val_block = table_match.group(1)
        def replace_in_record(rec_match):
            inner = rec_match.group(1)
            parts = get_parts(inner)
            if len(parts) > col_idx:
                val = parts[col_idx].strip("'")
                if val in nrp_map:
                    parts[col_idx] = f"'{nrp_map[val]}'"
                    return "(" + ",".join(parts) + ")"
            return rec_match.group(0)

        new_val_block = record_pattern.sub(replace_in_record, val_block)
        content = content.replace(val_block, new_val_block)

    with open('db.sql', 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("Record-level fix finished.")

if __name__ == "__main__":
    record_level_fix_nrps()
