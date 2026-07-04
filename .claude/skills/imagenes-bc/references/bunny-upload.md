# Bunny CDN Upload

## Configuración

```python
STORAGE_ZONE = "ve-media-storage"
REGION = "la"
API_KEY = "0fbb00db-5a99-4c86-a41665993e2e-7c8f-438d"
PULL_ZONE = "ve-pull-zone.b-cdn.net"
API_BASE = f"https://{REGION}.storage.bunnycdn.com/{STORAGE_ZONE}"
```

## Subida de archivos

```python
import os, requests

BASE_UPLOADS = r"C:\own\wp_bc\wp-content\uploads"

def upload_file(local_path, remote_path):
    url = f"{API_BASE}/{remote_path}"
    with open(local_path, "rb") as f:
        data = f.read()
    headers = {
        "AccessKey": API_KEY,
        "Content-Type": "application/octet-stream",
    }
    r = requests.put(url, headers=headers, data=data, timeout=60, verify=False)
    return r.status_code in (200, 201)
```

## Subir todos los sizes de una persona

```python
name_no_ext = os.path.splitext(rel_path)[0]
dir_path = os.path.dirname(local_path)
files_to_upload = [f for f in os.listdir(dir_path) if f.startswith(name_no_ext)]

for fname in sorted(files_to_upload):
    local_fpath = os.path.join(dir_path, fname)
    remote_fpath = f"wp-content/uploads/{rel_path.rsplit('/', 1)[0]}/{fname}"
    upload_file(local_fpath, remote_fpath)
```

## URLs accesibles

```
https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{slug}.jpg
https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{slug}-150x150.jpg
https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{slug}-160x160.jpg
https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{slug}-300x{N}.jpg
```
