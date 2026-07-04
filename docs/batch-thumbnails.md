# Batch de Thumbnails — Procedimiento

> Pipeline para asignar imágenes destacadas (thumbnails) a Personas (`bc_quote_author`)
> que aún no tienen. Imágenes se descargan de fuentes oficiales (churchofjesuschrist.org,
> Church News, Newsroom) y se suben a Bunny CDN.

## Requisitos

- Python 3.7+ con `pymysql`, `PIL/Pillow`, `requests`
- MySQL en `127.0.0.1:3307`, DB `bc_wp`, user `wpuser`, pass `wppass`
- Bunny CDN credenciales en `config/wp-config.php`:
  - `BUNNY_ZONE_PASSWORD` (Storage API password)
  - Storage zone: `ve-media-storage`, región: `la`
  - Pull zone: `ve-pull-zone.b-cdn.net`

## Pipeline Paso a Paso

### 1. Encontrar personas sin thumbnail

```sql
SELECT p.ID, p.post_name, p.post_title
FROM wp_posts p
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id
  AND pm.meta_key = '_thumbnail_id'
WHERE p.post_type = 'bc_quote_author'
  AND p.post_status = 'publish'
  AND pm.meta_id IS NULL
ORDER BY p.post_title;
```

### 2. Buscar imagen oficial

Fuentes en orden de preferencia:

1. **`churchofjesuschrist.org/learn/{slug}`** — buscar `og:image` en meta tags
   - URL formato: `https://www.churchofjesuschrist.org/imgs/{hash}/full/!1280%2C1600/0/default`
2. **`thechurchnews.com`** — artículos sobre la persona, extraer `og:image`
   - URL formato: `https://www.thechurchnews.com/resizer/v2/{hash}.jpg?auth={...}`
3. **`newsroom.churchofjesuschrist.org`** — `/media/640x480/{Nombre}.jpg`
4. **`history.churchofjesuschrist.org/chd/`** — Church History Database
   - URL formato: `https://history.churchofjesuschrist.org/church-history-people/bc/Church%20Leadership/{Name}/{LastName}_Profile.jpeg`
5. **`deseret.com`** — obituarios o artículos (último recurso)

Para líderes históricos (fallecidos antes de 1970): buscar en Wikipedia o
`historicalgeneralconferences.weebly.com`.

### 3. Descargar imagen

```bash
curl -L --insecure -o "wp-content/uploads/2026/07/{slug}.jpg" "{image_url}"
```

Colocar en `wp-content/uploads/2026/07/`.

### 4. Ejecutar script de creación de attachments

```bash
python dashboard/create_attachments.py
```

El script hace:

1. Abre cada imagen con PIL
2. Genera 3 tamaños: `150x150` (thumbnail), `160x160` (bc_quote_photo), `300xN` (medium)
3. Inserta `wp_posts` tipo `attachment` con GUID local
4. Inserta metadatos en `wp_postmeta`:
   - `_wp_attached_file` → ruta relativa (`2026/07/{slug}.jpg`)
   - `_wp_attachment_metadata` → serializado PHP con width, height, sizes, image_meta
   - `_indigetal_offloaded` → `complete`
   - `_indigetal_offload_manifest` → todos los sizes como `complete`
5. Asigna `_thumbnail_id` en el post `bc_quote_author`

### 5. Subir a Bunny CDN

```bash
python dashboard/upload_to_bunny.py
```

Sube todos los archivos (original + 3 sizes) a:

```
https://la.storage.bunnycdn.com/ve-media-storage/wp-content/uploads/2026/07/{filename}
```

Accesibles via: `https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{filename}`

### 6. Verificar

```bash
curl -k -s -o /dev/null -w "%{http_code}" \
  "https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{slug}.jpg"
# Debe responder 200
```

## Estructura de datos en BD

### wp_posts (attachment)

| Campo | Valor |
|-------|-------|
| `post_type` | `attachment` |
| `post_mime_type` | `image/jpeg` |
| `guid` | `http://localhost:8080/wp-content/uploads/2026/07/{slug}.jpg` |
| `post_name` | `{slug}[-{n}]` (sufijo numérico si hay conflicto) |
| `post_title` | Nombre de la persona |
| `post_status` | `inherit` |

### wp_postmeta

| meta_key | meta_value |
|----------|------------|
| `_wp_attached_file` | `2026/07/{slug}.jpg` |
| `_wp_attachment_metadata` | Serializado PHP con `width`, `height`, `file`, `sizes` (thumbnail, bc_quote_photo, medium), `image_meta` |
| `_indigetal_offloaded` | `complete` |
| `_indigetal_offload_manifest` | Serializado PHP con cada size y estado `complete` |

## Tamaños de imagen generados

| Size | Dimensiones | Crop | Uso |
|------|-------------|------|-----|
| `thumbnail` | 150×150 | Sí | Miniatura en cuadrículas |
| `bc_quote_photo` | 160×160 | Sí | Foto en single de cita |
| `medium` | 300×N | No | Vista previa en listados |

## Notas

- Las imágenes se descargan con `--insecure` por certificados autofirmados en la red corporativa
- No se necesita WordPress corriendo; todo se hace via MySQL directo + Bunny Storage API
- El password de Bunny está encriptado en la DB con AES-256-CBC usando `wp_salt()`;
  el valor plano está en `config/wp-config.php` como `BUNNY_ZONE_PASSWORD`
- Scripts en `dashboard/`: `create_attachments.py`, `upload_to_bunny.py`
