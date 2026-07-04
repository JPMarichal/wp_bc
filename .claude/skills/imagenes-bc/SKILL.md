---
name: imagenes-bc
description: |
  Buscar, descargar y asignar imágenes destacadas (thumbnails) para Personas
  del CPT bc_quote_author en wp_bc. Regla fundamental: para cada persona se
  deben AGOTAR TODAS las fuentes disponibles en orden secuencial sin saltar
  ninguna — el hecho de que una fuente no haya funcionado antes no justifica
  omitirla. Cubre: 8 fuentes de búsqueda (learn pages, Wikipedia API, Wikipedia
  via webfetch, conference talks, Church News/Newsroom, Church History Database,
  colecciones de líderes, Deseret News/BYU/FamilySearch), descarga a
  wp-content/uploads/2026/07/, creación de attachments via MySQL directo (3
  sizes: thumbnail 150×150 crop, bc_quote_photo 160×160 crop, medium 300×N),
  asignación de _thumbnail_id, y subida a Bunny CDN via Storage API. Usar
  siempre que se necesite encontrar imágenes para personas sin thumbnail,
  corregir imágenes existentes, o procesar un batch de thumbnails.
---
# Skill: imagenes-bc

Pipeline para asignar imágenes destacadas a Personas (`bc_quote_author`) que
aún no tienen. Las imágenes se descargan de fuentes oficiales, se procesan
con PIL, se registran en MySQL como attachments, y se suben a Bunny CDN.

## Estado del proyecto

- Upload dir: `wp-content/uploads/2026/07/`
- Attachment IDs usados hasta ahora: 2262–2346
- Batch 4: 52 personas (IDs 2262–2313) completado
- Batch 5: 50 personas, 16 procesadas + Spencer W. Kimball corregido
- ~34 personas restantes sin thumbnail

## Pipeline completo

### 1. Encontrar personas sin thumbnail

```sql
SELECT p.ID, p.post_name, p.post_title
FROM wp_posts p
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
WHERE p.post_type = 'bc_quote_author' AND p.post_status = 'publish' AND pm.meta_id IS NULL
ORDER BY p.post_title;
```

### 2. Buscar imagen oficial

**Principio**: Para CADA persona, agotar TODAS las fuentes en orden secuencial sin saltar ninguna. El hecho de que una fuente no haya dado resultado para otras personas NO es razón para omitirla. Cada persona es un caso distinto y una fuente diferente puede tener su imagen.

Ejecutar las 6 fases en orden. Si una no encuentra imagen, pasar a la siguiente inmediatamente. Solo detenerse cuando se encuentre una imagen o se hayan agotado todas las fases.

| Orden | Fuente | Método |
|-------|--------|--------|
| 1 | churchofjesuschrist.org/learn/people/{slug} | requests.GET + og:image scraping |
| 2 | Wikipedia API | `action=query&prop=pageimages&pithumbsize=800` |
| 3 | Wikipedia via webfetch | webfetch con format=html, extraer og:image |
| 4 | Conference talks | Escanear `/study/general-conference/{year}/{month}/` y extraer og:image de cada talk |
| 5 | Church News / Newsroom | thechurchnews.com, newsroom.churchofjesuschrist.org |
| 6 | Church History Database | history.churchofjesuschrist.org/chd/ |
| 7 | Colección de líderes | `/media/collection/church-leaders-images` (RSC streaming) |
| 8 | Deseret News / BYU / FamilySearch | deseret.com, byu.edu, familysearch.org |

**Colección de líderes** (`/media/collection/church-leaders-images`): El sitio usa Next.js con RSC streaming. El HTML contiene chunks `self.__next_f.push(...)` que hay que decode (unicode_escape) para parsear. Las APIs retornan 503 desde Python. No es práctico sin headless browser. Aun así, INTENTARLO.

**Receta para Wikipedia via webfetch:**
1. Construir URL: `https://en.wikipedia.org/wiki/{Name}_(Mormon)` o `https://en.wikipedia.org/wiki/{Name}_(Latter_Day_Saints)`
2. Usar webfetch con `format=html`
3. Extraer `og:image` del HTML
4. La URL de imagen comienza con `https://upload.wikimedia.org/`
5. Descargar con requests (puede dar 429 — reintentar con sleep de 3s)

**Receta para conference talks:**
1. Obtener lista de charlas recientes desde `/study/general-conference/{year}/{month}/?lang=eng`
2. Extraer links a talks modernos: `href="/study/general-conference/{year}/{month}/{number}{lastname}?lang=eng"`
3. Fetch cada talk y extraer og:image
4. Resize URL: cambiar `/full/!{w},{h}/` a `/full/!1280,1600/`

**OG Image URL resizing:**
```python
import re
full_url = re.sub(r"/full/![0-9]+,[0-9]+/", "/full/!1280,1600/", og_url)
```

### 3. Descargar imagen

```bash
curl -L --insecure -o "wp-content/uploads/2026/07/{slug}.jpg" "{image_url}"
```

O desde Python con `requests.get(url, timeout=30, verify=False)`.

### 4. Crear attachments en BD

IMPORTANTE: Después de descargar nuevas imágenes, ACTUALIZAR `dashboard/create_attachments.py` y `dashboard/upload_to_bunny.py` con los slugs y rangos de IDs correctos.

Ejecutar:
```bash
python dashboard/create_attachments.py
```

El script:
1. Abre cada imagen con PIL
2. Genera 3 sizes: `{slug}-150x150.jpg`, `{slug}-160x160.jpg`, `{slug}-300x{N}.jpg`
3. Inserta registro en `wp_posts` tipo `attachment`
4. Inserta metadatos: `_wp_attached_file`, `_wp_attachment_metadata` (PHP serializado), `_indigetal_offloaded`, `_indigetal_offload_manifest`
5. Asigna `_thumbnail_id` en el post `bc_quote_author` (UPDATE si existe, INSERT si no)

Ver `references/db-operations.md` para la estructura exacta de datos.

### 5. Subir a Bunny CDN

```bash
python dashboard/upload_to_bunny.py
```

Sube original + 3 sizes a `https://la.storage.bunnycdn.com/ve-media-storage/wp-content/uploads/2026/07/{filename}`

Ver `references/bunny-upload.md` para configuración.

### 6. Verificar

```bash
curl -k -s -o /dev/null -w "%{http_code}" "https://ve-pull-zone.b-cdn.net/wp-content/uploads/2026/07/{slug}.jpg"
# Debe responder 200
```

SQL:
```sql
SELECT p.post_name, pm.meta_value FROM wp_postmeta pm
JOIN wp_posts p ON p.ID = pm.post_id
WHERE pm.meta_key = '_thumbnail_id' AND pm.meta_value IS NOT NULL
ORDER BY p.post_title;
```

## Conexión MySQL

```
Host: 127.0.0.1:3307
DB: bc_wp
User: wpuser
Pass: wppass
```

## Bunny CDN

```
Storage zone: ve-media-storage
Region: la
Pull zone: ve-pull-zone.b-cdn.net
API Key: 0fbb00db-5a99-4c86-a41665993e2e-7c8f-438d
```

## Rutas clave

| Propósito | Ruta |
|-----------|------|
| Script de attachments | `dashboard/create_attachments.py` |
| Script de upload | `dashboard/upload_to_bunny.py` |
| Upload dir | `wp-content/uploads/2026/07/` |
| Documentación | `docs/batch-thumbnails.md` |
| Fase 1 (learn pages) | `dashboard/batch5_phase1_learn.py` |
| Fase 2b (Wikipedia) | `dashboard/batch5_phase2_wiki.py` |
| Fase 3 (conferencias) | `dashboard/batch5_fullscan.py` |
| Colecciones líderes | `dashboard/batch5_collections.py` |
| Find sin thumbnails | `dashboard/_batch5_find.py` |

## Gotchas conocidas

1. **Wikipedia rate limit**: Desde Python requests incluso con verify=False, Wikipedia API da 429. Usar webfetch como alternativa.
2. **Upload Wikimedia rate limit**: Descargar imágenes de `upload.wikimedia.org` también da 429. Esperar 3s entre descargas.
3. **RSC streaming**: Churchofjesuschrist.org usa Next.js RSC. El payload no es parseable sin headless browser.
4. **IDs secuenciales**: Los attachment IDs se asignan secuencialmente. Al actualizar scripts, mantener el rango correcto.
5. **Conflictos de post_name**: Si dos attachments tienen el mismo slug, el segundo se sufija (`{slug}-2`). El script maneja esto.
6. **Nombres con puntos**: Los names de personas con iniciales (J. Devn Cornish) tienen `.` en wp_posts.post_title.
