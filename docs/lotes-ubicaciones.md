# Lotes — Glosario de Ubicaciones (bc_location)

> Procesamiento por lotes del CPT `bc_location`. Cada lote contiene 10
> ubicaciones procesadas secuencialmente. El progreso se persiste en
> `tracking/locations.db` (SQLite) para reanudar entre sesiones.

---

> ## ⚠️ REGLA INNEGABLE: USA SIEMPRE `podman`, NUNCA `docker`
>
> Los contenedores de este proyecto (`wp_bc_db`, `wp_bc`, `wp_bc_cli`)
> se gestionan con **podman**. Usar `docker exec` contamina el entorno y
> puede mezclar/romper los contenedores. **SIEMPRE, SIEMPRE, SIEMPRE**
> usa `podman` para cualquier interacción (`podman exec wp_bc_cli wp ...`,
> `podman ps`, etc.). Si ves `docker` en un comando, corrígelo a
> `podman` antes de ejecutarlo. No existe excepción.

---

## Tracking: esquema SQLite

Archivo: `tracking/locations.db`

### Tabla `batches`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | INTEGER PK | ID del lote |
| `status` | TEXT | `in_progress`, `completed`, `failed` |
| `started_at` | TEXT | ISO timestamp de inicio |
| `completed_at` | TEXT | ISO timestamp de finalización |
| `commit_hash` | TEXT | Hash del commit con que se cerró el lote |

### Tabla `locations`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `wp_id` | INTEGER PK | ID del post en WordPress |
| `title` | TEXT | Título en español |
| `name_en` | TEXT | Nombre en inglés (KJV) |
| `level` | TEXT | Nivel estimado A/B/C |
| `relevancia` | INTEGER | Relevancia pre-llenada: 1=Baja, 2=Media, 3=Alta |
| `batch_id` | INTEGER | Lote al que pertenece |
| `status` | TEXT | `pending`, `in_progress`, `completed`, `failed` |
| `word_count` | INTEGER | Conteo de palabras del contenido |
| `error` | TEXT | Mensaje de error si falló |
| `created_at` | TEXT | ISO timestamp de registro |
| `updated_at` | TEXT | ISO timestamp de última actualización |

### Tabla `activity_log`

Historial de operaciones realizadas.

---

## Cómo determinar el próximo lote

```bash
# Ver estado de todos los lotes
python tracking/tracker.py stats

# Ver último lote completado
python tracking/tracker.py last-batch

# Consulta directa a SQLite
python -c "
import sqlite3
conn = sqlite3.connect('tracking/locations.db')
cur = conn.cursor()
cur.execute('SELECT id, status, started_at FROM batches ORDER BY id')
for r in cur.fetchall():
    print(r)
"
```

Si no hay `in_progress`, crear nuevo lote con `batch_id = max + 1`.

---

## Pipeline por ubicación

Cada ubicación del lote se procesa secuencialmente:

### Fase 0: Obtener datos

```bash
podman exec wp_bc_cli wp post get <ID> --field=post_title --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_name_en --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_type --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_confidence --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_lat --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_lng --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_scriptures --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_alt_names --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_alias_of --allow-root
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_disambiguation --allow-root
```

### Fase 0b: Validar

- **Título en español**: verificar que `post_title` no sea inglés
- **Alias**: si `_bc_loc_alias_of` está seteado, **no procesar** — saltar
- **Tipo**: validar que `_bc_loc_type` sea uno de: `city`, `region`, `wilderness`, `sea`, `river`, `mountain`, `settlement`, `landmark`
- **Duplicados**: buscar si hay otro post con el mismo `_bc_loc_name_en`. Si existe, marcar el de mayor ID como alias del menor
- **Disambiguation**: si el título tiene homónimos (otro post con igual título), verificar que `_bc_loc_disambiguation` esté poblado

### Fase 0c: Obtener nivel desde relevancia pre-llenada

La columna `relevancia` en `tracking/locations.db` ya está pre-computada
para todas las ubicaciones. No determinar el nivel durante la generación:
leerlo de la DB según este mapeo:

| `relevancia` | Nivel | Mín. palabras | Módulos típicos | Forma T |
|--------------|-------|:-------------:|-----------------|:-------:|
| **3 — Alta** | **A** | 1000+ | Todos aplicables. Historia subdividida con `<h3>` | 8–15 filas |
| **2 — Media** | **B** | 400–800 | 4–6 módulos | 4–8 filas |
| **1 — Baja** | **C** | 150–300 | Intro + Historia + Lecciones + Fuentes + Referencias | 2–4 filas |

```bash
python -c "
import sqlite3
conn = sqlite3.connect('tracking/locations.db')
cur = conn.cursor()
cur.execute('SELECT relevancia FROM locations WHERE wp_id=?', (<ID>,))
row = cur.fetchone()
relevancia = row[0] if row else 1
print(f'Relevancia: {relevancia} ({chr(65+3-relevancia)})')
conn.close()
```

### Fase 1: Investigación exhaustiva

Consultar en orden:
1. Alejandría (KG, search_text, search_semantic, chat_ask)
2. BYU RSC, Maxwell Institute, FAIR, Book of Mormon Central
3. Guía para el Estudio de las Escrituras (churchofjesuschrist.org)
4. BibleHub topical
5. Diccionarios bíblicos (Holman, Smith, Easton)
6. Wikipedia (español)

### Fase 2: Redactar

HTML con:
- Intro (1 párrafo sin `<h2>`, SEO, funciona como excerpt)
- Módulos según nivel (etimología, geografía, historia, lecciones, etc.)
- Conocimiento revelado integrado donde sea oportuno (no sección separada)
- `<h2>Fuentes consultadas</h2>` con `<ul>`
- `<h2>Referencias de las Escrituras</h2>` con `<table class="bc-forma-t">`
- **FAQ SEO** (nivel A y B): máximo 3 preguntas de cola larga derivadas
  del contenido, usando las 6W. No incluir en `post_content`; el template
  genera el FAQ schema automáticamente.

### Fase 3: Publicar

```bash
podman exec wp_bc_cli wp post update <ID> \
  --post_content='<HTML>' --allow-root
```

### Fase 4: Verificar

```bash
podman exec wp_bc_cli wp post get <ID> --field=post_content --allow-root | head -c 200
```

---

## Forma T

La tabla de referencias escriturales debe cumplir:

```html
<h2>Referencias de las Escrituras</h2>
<table class="bc-forma-t">
  <thead>
    <tr><th>Concepto</th><th>Referencia</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>Idea didáctica en ≤15 palabras, sin punto final</td>
      <td>Libro capítulo:versículo en español</td>
    </tr>
  </tbody>
</table>
```

- Los conceptos, leídos en orden, deben formar una lección coherente
- Cada fila = una idea respaldada por su(s) referencia(s)
- Máximo ~15 palabras por concepto

---

## Inicio de un nuevo lote

```bash
# 1. Obtener IDs no procesados (sin contenido, sin alias)
podman exec wp_bc_cli wp eval --allow-root '
$posts = get_posts(array(
  "post_type" => "bc_location",
  "posts_per_page" => -1,
  "post_status" => "publish",
  "orderby" => "ID",
  "order" => "ASC"
));
$pending = array();
foreach ($posts as $p) {
  if (strlen(trim($p->post_content)) < 50) {
    $alias = get_post_meta($p->ID, "_bc_loc_alias_of", true);
    if (!$alias) $pending[] = $p->ID;
  }
}
echo "Pendientes: " . count($pending) . "\n";
echo "Primeros 10: " . implode(", ", array_slice($pending, 0, 10)) . "\n";
'
```

```bash
# 2. Marcar batch como in_progress en SQLite
python -c "
import tracking.tracker as t
b = t.create_batch()
print(f'Batch {b} created')
ids = [102, 103, 104, 105, 106, 107, 108, 109, 110, 111]
t.add_locations(b, ids)
"
```

```bash
# 3. Procesar cada ubicación (Fase 0→4)
```

```bash
# 4. Cerrar batch
python -c "
import tracking.tracker as t
t.complete_batch(<BATCH_ID>, '<COMMIT_HASH>')
"
```

```bash
# 5. Dump DB y commit
podman exec wp_bc_cli wp db export /var/www/html/backups/db-latest.sql --allow-root
git add -A && git commit -m "feat(glosario): lote N — 10 ubicaciones"
```

---

## Reanudar sesión interrumpida

```bash
# 1. Ver batch in_progress
python -c "
import sqlite3
conn = sqlite3.connect('tracking/locations.db')
cur = conn.cursor()
cur.execute(\"SELECT id FROM batches WHERE status='in_progress'\")
row = cur.fetchone()
if row: print(f'Batch {row[0]} en progreso')
else: print('No hay batch activo')
"

# 2. Ver ubicaciones pendientes en ese batch
python -c "
import sqlite3
conn = sqlite3.connect('tracking/locations.db')
cur = conn.cursor()
cur.execute(\"SELECT wp_id, title, status FROM locations WHERE batch_id=<BATCH_ID>\")
for r in cur.fetchall():
    print(r)
"
```

---

## Comandos útiles

```bash
# Ver progreso global
python tracking/tracker.py stats

# Último lote
python tracking/tracker.py last-batch

# Buscar ubicación por nombre
podman exec wp_bc_cli wp post list --post_type=bc_location \
  --s="Nombre" --fields=ID,post_title,post_name --allow-root

# Ver metadatos completos
podman exec wp_bc_cli wp post meta list <ID> --allow-root

# Ver slug
podman exec wp_bc_cli wp post get <ID> --field=post_name --allow-root

# Ver contenido
podman exec wp_bc_cli wp post get <ID> --field=post_content --allow-root
```

---

## Reglas clave

- **No procesar** ubicaciones con `_bc_loc_alias_of` seteado
- **No regenerar** thumbnails ni tocar imágenes
- **No modificar** la BD fuera de `wp post update` y corrección de metadatos
- **Sobrescribir** siempre: reemplazar `post_content` sin conservar nada anterior
- **Intro**: un solo `<p>` sin `<h2>`, funciona como excerpt y meta description
- **Conocimiento revelado**: integrado en las secciones donde es oportuno, no en sección separada
- **T-form**: conceptos en orden didáctico, ≤15 palabras, sin punto final
- **Lotes de 10**: secuencial, una ubicación a la vez
