# DB Operations — Attachment Creation

## Estructura de wp_posts para attachment

| Campo | Valor |
|-------|-------|
| `post_type` | `attachment` |
| `post_mime_type` | `image/jpeg` |
| `guid` | `http://localhost:8080/wp-content/uploads/2026/07/{slug}.jpg` |
| `post_name` | `{slug}` (con sufijo `-{n}` si hay conflicto) |
| `post_title` | Nombre de la persona |
| `post_status` | `inherit` |

## Metadatos en wp_postmeta

| meta_key | meta_value |
|----------|------------|
| `_wp_attached_file` | `2026/07/{slug}.jpg` |
| `_wp_attachment_metadata` | PHP serializado: `{width, height, file, sizes:{thumbnail,bc_quote_photo,medium}, image_meta}` |
| `_indigetal_offloaded` | `complete` |
| `_indigetal_offload_manifest` | PHP serializado: cada size como `{relative_path, state:complete, remote_path, last_error}` |

## PHP serializer (Python)

```python
def serialize_php_array(data):
    if isinstance(data, dict):
        items = []
        for k, v in data.items():
            items.append(f's:{len(str(k))}:"{k}";{serialize_php_array(v)}')
        return f"a:{len(data)}:{{{''.join(items)}}}"
    elif isinstance(data, list):
        items = []
        for i, v in enumerate(data):
            items.append(f"i:{i};{serialize_php_array(v)}")
        return f"a:{len(data)}:{{{''.join(items)}}}"
    elif isinstance(data, str):
        return f's:{len(data)}:"{data}";'
    elif isinstance(data, (int, float)):
        return f"{'i' if isinstance(data, int) else 'd'}:{data};"
    elif data is None:
        return "N;"
    elif isinstance(data, bool):
        return f"b:{'1' if data else '0'};"
    return "N;"
```

## Generación de sizes

Usar PIL:

```python
from PIL import Image

img = Image.open(filepath)
w, h = img.size

sizes = {}
configs = [
    ("thumbnail", 150, 150, True),      # crop square
    ("bc_quote_photo", 160, 160, True),  # crop square
    ("medium", 300, 0, False),           # max width, proporcional
]

for sname, sw, sh, crop in configs:
    if crop:
        min_dim = min(w, h)
        left = (w - min_dim) // 2
        top = (h - min_dim) // 2
        cropped = img.crop((left, top, left + min_dim, top + min_dim))
        resized = cropped.resize((sw, sh), Image.LANCZOS)
    else:
        if w > sw:
            ratio = sw / w
            new_h = int(h * ratio)
            resized = img.resize((sw, new_h), Image.LANCZOS)
        else:
            resized = img.copy()
    sfname = f"{name_no_ext}-{sw}x{sh if crop else resized.size[1]}{ext}"
    resized.save(os.path.join(outbase, sfname), quality=85, optimize=True)
```

## Asignación de _thumbnail_id

```python
cur.execute("SELECT ID FROM wp_posts WHERE post_name = %s AND post_type = 'bc_quote_author'", (slug,))
row = cur.fetchone()
if row:
    persona_id = row[0]
    cur.execute("SELECT meta_id FROM wp_postmeta WHERE post_id = %s AND meta_key = '_thumbnail_id'", (persona_id,))
    if cur.fetchone():
        cur.execute("UPDATE wp_postmeta SET meta_value = %s WHERE post_id = %s AND meta_key = '_thumbnail_id'", (attachment_id, persona_id))
    else:
        cur.execute("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (%s, '_thumbnail_id', %s)", (persona_id, attachment_id))
```

## Prevención de conflictos de post_name

```python
cur.execute("SELECT post_name FROM wp_posts WHERE post_type='attachment'")
existing_names = set(r[0] for r in cur.fetchall())

post_name = base_slug
suffix = 2
while post_name in existing_names:
    post_name = f"{base_slug}-{suffix}"
    suffix += 1
existing_names.add(post_name)
```
