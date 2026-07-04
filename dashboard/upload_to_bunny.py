import pymysql, os, requests, base64

conn = pymysql.connect(
    host="127.0.0.1", port=3307, user="wpuser", password="wppass", database="bc_wp"
)
cur = conn.cursor()

# Bunny CDN config
STORAGE_ZONE = "ve-media-storage"
REGION = "la"
API_KEY = "0fbb00db-5a99-4c86-a41665993e2e-7c8f-438d"
PULL_ZONE = "ve-pull-zone.b-cdn.net"

BASE_UPLOADS = r"C:\own\wp_bc\wp-content\uploads"
API_BASE = f"https://{REGION}.storage.bunnycdn.com/{STORAGE_ZONE}"

# Get all attachments (IDs 2411-2455 = 21 cached + last run dups)
cur.execute("""
    SELECT p.ID, p.post_title, pm.meta_value AS attached_file
    FROM wp_posts p
    JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
    WHERE p.ID >= 2456 AND p.ID <= 2570
""")

attachments = cur.fetchall()
print(f"Found {len(attachments)} attachments to upload")

total_files = 0
success = 0
errors = []

for att_id, title, rel_path in attachments:
    # Upload original
    local_path = os.path.join(BASE_UPLOADS, rel_path.replace("/", os.sep))
    remote_path = f"wp-content/uploads/{rel_path}"

    # Also find all generated sizes from the directory
    name_no_ext = os.path.splitext(os.path.basename(rel_path))[0]
    dir_path = os.path.dirname(local_path)

    files_to_upload = [f for f in os.listdir(dir_path) if f.startswith(name_no_ext)]

    for fname in sorted(files_to_upload):
        local_fpath = os.path.join(dir_path, fname)
        remote_fpath = f"wp-content/uploads/{rel_path.rsplit('/', 1)[0]}/{fname}"

        total_files += 1
        url = f"{API_BASE}/{remote_fpath}"

        with open(local_fpath, "rb") as f:
            data = f.read()

        headers = {
            "AccessKey": API_KEY,
            "Content-Type": "application/octet-stream",
        }

        try:
            r = requests.put(url, headers=headers, data=data, timeout=60, verify=False)
            if r.status_code in (200, 201):
                success += 1
                print(f"  OK  [{att_id}] {fname} -> {PULL_ZONE}/{remote_fpath}")
            else:
                errors.append(f"{fname}: HTTP {r.status_code} {r.text[:100]}")
                print(f"  FAIL [{att_id}] {fname}: HTTP {r.status_code}")
        except Exception as e:
            errors.append(f"{fname}: {str(e)}")
            print(f"  ERROR [{att_id}] {fname}: {e}")

conn.close()
print(f"\nDone! {success}/{total_files} files uploaded successfully.")
if errors:
    print(f"Errors ({len(errors)}):")
    for e in errors[:10]:
        print(f"  - {e}")
