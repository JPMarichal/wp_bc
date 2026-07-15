---
name: glosario-ubicaciones-contenido
description: |
  Generar contenido narrativo (post_content) del CPT bc_location para el
  Glosario de Ubicaciones bíblicas. Usar cuando se necesite redactar la
  descripción de una ubicación desde fuentes de Alejandría, BYU RSC,
  Maxwell Institute, FAIR, Book of Mormon Central, sitio oficial SUD,
  BibleHub, diccionarios bíblicos y Wikipedia. Proporciona el pipeline
  completo de investigación exhaustiva → redacción → publicación,
  plantilla modular y principios de redacción congruentes con la
  doctrina de la Restauración.
---

> ## ⚠️ REGLA INNEGABLE: USA SIEMPRE `podman`, NUNCA `docker`
>
> Los contenedores de este proyecto (`wp_bc_db`, `wp_bc`, `wp_bc_cli`)
> se gestionan con **podman**. Usar `docker exec` contamina el entorno y
> puede mezclar/romper los contenedores. **SIEMPRE, SIEMPRE, SIEMPRE**
> usa `podman` en lugar de `docker` para cualquier interacción:
> `podman exec wp_bc_cli wp ... --allow-root`, `podman exec wp_bc_db ...`,
> `podman ps`, `podman start`, etc. No existe excepción. Si ves `docker`
> en un comando, corrígelo a `podman` antes de ejecutarlo.

# Skill: glosario-ubicaciones-contenido

Genera el contenido narrativo (`post_content`) de una ubicación del CPT
`bc_location`. El contenido se despliega en la columna principal de
`single-bc_location.php`, mientras el sidebar muestra los metadatos
(tipo, coordenadas, confianza, referencia de ejemplo).

---

## Pipeline completo

### Fase 0: Obtener datos de la ubicación

Identificar el ID y metadatos de la ubicación:

```bash
podman exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Nombre" \
  --fields=ID,post_title,post_name,post_status --allow-root

podman exec wp_bc_cli wp post meta list <ID> --allow-root

podman exec wp_bc_cli wp post get <ID> --field=post_content --allow-root
```

Metadatos disponibles:

| Meta key | Descripción | Tipo |
|----------|-------------|------|
| `_bc_loc_name_en` | Nombre en inglés (KJV) | string |
| `_bc_loc_disambiguation` | Contexto desambiguante (ej: Siria, Pisidia, América) | string |
| `_bc_loc_type` | Tipo: city, region, wilderness, sea, river, mountain, settlement, landmark | string |
| `_bc_loc_scriptures` | Referencias escriturales (JSON) | string |
| `_bc_loc_description` | Descripción breve | string |
| `_bc_loc_lat` | Latitud | number |
| `_bc_loc_lng` | Longitud | number |
| `_bc_loc_source` | Fuente: openbible, gnosis, church-history | string |
| `_bc_loc_confidence` | Confianza: high, medium, low | string |
| `_bc_loc_date_from` | Año inicial aprox. | integer |
| `_bc_loc_date_to` | Año final aprox. | integer |
| `_bc_loc_alt_names` | Nombres alternativos (JSON array) | string |
| `_bc_loc_alias_of` | ID de ubicación principal si es nombre alternativo | integer |

Anotar el ID, nombre en inglés (`_bc_loc_name_en`), tipo, nivel
estimado (A/B/C), y referencias escriturales (`_bc_loc_scriptures`).

---

### Fase 0b: Validar tipo y detectar duplicados

**Antes de investigar**, verificar dos cosas:

#### 1. Validar `_bc_loc_type`

Comparar el tipo asignado con la realidad de la ubicación. Tipos
correctos: `city`, `region`, `wilderness`, `sea`, `river`, `mountain`,
`settlement`, `landmark`.

Corregir si está mal:

```bash
podman exec wp_bc_cli wp post meta update <ID> _bc_loc_type <tipo_correcto> --allow-root
```

Casos conocidos de tipo incorrecto:
- **Egipto** (ID 2827) estaba como `water` → debe ser `region`
- `valley`, `island`, `path` no son tipos válidos → reclasificar

#### 2. Detectar duplicados y alias

Verificar si la ubicación tiene `_bc_loc_alias_of` seteado. Si es alias
de otra, **no procesarla**: su contenido se genera desde la principal.

```bash
podman exec wp_bc_cli wp post meta get <ID> _bc_loc_alias_of --allow-root
```

Para detectar duplicados no marcados, buscar por `_bc_loc_name_en`:

```bash
podman exec -e MYSQL_PWD=wppass wp_bc_db mysql -uwpuser bc_wp -e "
SELECT p.ID, p.post_title, pm.meta_value as name_en
FROM wp_posts p
JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bc_loc_name_en'
WHERE pm.meta_value = '<NAME_EN>'
ORDER BY p.ID"
```

Si hay dos posts con el mismo `_bc_loc_name_en`:
- El de menor ID es el **principal**
- El de mayor ID se marca como **alias**:
  ```bash
  podman exec wp_bc_cli wp post meta update <ID_MAYOR> _bc_loc_alias_of <ID_MENOR> --allow-root
  ```

**No procesar ubicaciones que sean alias de otra.** Saltarlas en el
batch loop.

---

### Fase 0c: Obtener nivel desde relevancia pre-llenada

La columna `relevancia` en `tracking/locations.db` ya está pre-computada
para todas las ubicaciones. No determinar el nivel durante la generación:
leerlo de la DB según este mapeo:

| `relevancia` | Nivel | Mín. palabras | Módulos típicos | Forma T |
|--------------|-------|:-------------:|-----------------|:-------:|
| **3 — Alta** | **A** | 1000+ | Todos aplicables. Historia subdividida con `<h3>` | 8–15 filas |
| **2 — Media** | **B** | 400–800 | 4–6 módulos | 4–8 filas |
| **1 — Baja** | **C** | 150–300 | Intro + Historia + Lecciones + Fuentes + Forma T | 2–4 filas |

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

---

### Fase 0d: Plantilla de investigación por tipo

Usar la plantilla correspondiente al `_bc_loc_type` de la ubicación:

| Tipo | Enfoque de investigación |
|------|--------------------------|
| `city`, `settlement` | Fundación, menciones bíblicas, arqueología, situación actual |
| `region`, `wilderness` | Límites geográficos, eventos clave, importancia estratégica |
| `sea`, `river` | Geografía, eventos escriturales, simbolismo |
| `mountain` | Eventos clave, simbolismo, menciones |
| `landmark` | Descripción, eventos, significado doctrinal (DyC/JST si aplica) |

---

### Fase 0e: Checklist de fuentes

Documentar en `Fuentes consultadas`:

1. Alejandría (KG + search_text + chat_ask)
2. Una fuente web oficial SUD o churchofjesuschrist.org
3. Una enciclopedia bíblica (BibleHub o Wikipedia)
4. Si relevancia Alta/Media: al menos una fuente adicional (BYU, Maxwell, FAIR, etc.)

Scoring de calidad de fuentes (no es fallback, es scoring):
1. Escrituras + conocimiento revelado (DyC, JST, PGP, Libro de Mormón)
2. Alejandría (corpus propio)
3. Fuentes oficiales SUD (churchofjesuschrist.org, lds.org)
4. Diccionarios bíblicos académicos (Holman, Smith, Easton)
5. Wikipedia (español/inglés)
6. Otras fuentes académicas (BYU RSC, Maxwell, FAIR)

---

### Fase 1: Investigación exhaustiva

Consultar **todas** las fuentes aplicables. No es una cadena de
fallbacks sino un sistema de recolección exhaustiva: cada fuente
aporta datos que las otras no tienen. El orden de consulta lo
determina el criterio, no una jerarquía fija.

```bash
# ── Alejandría ──────────────────────────────────────
alejandria_kg_find(query: "<nombre>")
alejandria_search_text(query: "<nombre>", limit: 10)
alejandria_search_text(query: "<nombre>", source_filter: "es/scriptures")
alejandria_search_text(query: "<nombre>", source_filter: "en/scriptures")
alejandria_search_text(query: "<nombre>", source_filter: "es/manuals")
alejandria_search_text(query: "<nombre>", source_filter: "en/manuals")
alejandria_chat_ask(question: "¿Qué importancia tiene <nombre> en la Biblia?")
alejandria_chat_ask(question: "¿Qué enseñan las Escrituras de la Restauración sobre <nombre>?")
alejandria_search_semantic(query: "<nombre> biblical significance")

# ── Sitios especializados SUD ────────────────────────
# BYU Religious Studies Center, Maxwell Institute,
# FAIR, Book of Mormon Central, Insights
webfetch(url: "https://rsc.byu.edu/search?q=<nombre>")
webfetch(url: "https://mi.byu.edu/search?q=<nombre>")
webfetch(url: "https://www.fairlatterdaysaints.org/search?q=<nombre>")
webfetch(url: "https://archive.bookofmormoncentral.org/search?q=<nombre>")

# ── Sitio oficial ────────────────────────────────────
# Guía para el Estudio de las Escrituras
webfetch(url: "https://www.churchofjesuschrist.org/study/scriptures/gs/<slug>?lang=spa")

# ── BibleHub y léxicos ───────────────────────────────
webfetch(url: "https://biblehub.com/topical/<slug>.htm")

# ── Enciclopedias y diccionarios bíblicos ─────────────
# Holman, Smith, Easton, International Standard

# ── Wikipedia ────────────────────────────────────────
webfetch(url: "https://es.wikipedia.org/wiki/<nombre>")
```

**Reglas:**
- Extraer de cada fuente lo que aporta, sintetizar, no concatenar
- El conocimiento revelado (Perla de Gran Precio, Libro de Mormón,
  DyC, JST) se integra donde sea más oportuno dentro del contenido,
  no en una sección separada
- No entretenerse en datos polémicos. Si hay dudas sobre ubicación
  o historia, presentar solo los datos últimos y certeros
- Si existe respuesta satisfactoria a una pregunta potencial, darla
  sin polemizar
- El tono debe ser espiritual, narrativo, ameno. Dirigido al ser
  humano, no al erudito

---

### Fase 2: Redactar el contenido

Escribir `post_content` en **HTML** siguiendo la estructura modular
según el nivel de la ubicación (ver más abajo).

Reglas clave:
- **Contenido original**, no copia textual de ninguna fuente
- **Doctrina de la Restauración**: congruente con la teología de
  La Iglesia de Jesucristo de los Santos de los Últimos Días
- **Integrar el conocimiento revelado** (Perla de Gran Precio, Libro
  de Mormón, DyC, JST) en las secciones donde sea más oportuno,
  no en una sección separada forzada
- **Sintetizar**, no concatenar fuentes
- **No repetir sidebar**: no incluir coordenadas, tipo, confianza,
  fuente ni referencia de ejemplo (ya están en el infobox)
- **Referencias en español**: usar el mapa `$en_to_es` de
  `single-bc_location.php:350-372` (ver tabla completa abajo)
- **SEO en todo momento**: palabras clave de cola larga,
  interlinking con negritas en menciones de otros lugares, sintaxis
  semántica (`<strong>` para énfasis, `<em>` para extranjerismos)

#### Datos clave

El template inyecta automáticamente un bloque "Datos clave" antes del
contenido. No incluir este bloque en el `post_content`.

#### Estructura de introducción

Un solo `<p>` sin `<h2>`. Debe contener:
- Definición de qué es la ubicación
- Dónde se encuentra
- Su relevancia principal (1–2 frases)
- Palabra clave principal para SEO

#### Módulos obligatorios vs. opcionales

| Nivel | Obligatorios | Opcionales |
|-------|--------------|------------|
| A | Etimología, Geografía, Historia en Escrituras, Lecciones, Situación actual | Origen temprano, Historia postbíblica, Arqueología |
| B | Etimología, Historia en Escrituras, Lecciones, Situación actual | Geografía, Arqueología |
| C | Historia en Escrituras, Lecciones, Situación actual | Etimología, Geografía |

#### FAQ (solo nivel A y B)

No incluir FAQ en el `post_content`. El template genera el FAQ schema
automáticamente a partir de preguntas **SEO reales**, no genéricas.

**Criterios:**
- Usar las **6W** sobre el contenido específico de la ubicación
- Preguntas de **cola larga** que un usuario real escribiría en Google
- Máximo 3 preguntas por artículo
- Responder directamente desde el contenido ya redactado
- Evitar preguntas genéricas como "¿dónde está?" o "¿qué es?"
- Priorizar preguntas con alto potencial de tráfico orgánico

Ejemplos de buenas preguntas SEO:
- "¿Por qué Capernaúm es conocida como la ciudad de Jesús?"
- "¿Qué pasó en el monte Carmelo con Elías y los profetas de Baal?"
- "¿Cuál es el significado del Cedrón en la Biblia?"
- "¿Dónde se encuentra el río Quebar mencionado en Ezequiel?"

El FAQ schema se genera en `single-bc_location.php` a partir de estas
preguntas y sus respuestas.

---

### Fase 3: Publicar

```bash
podman exec wp_bc_cli wp post update <ID> \
  --post_content='<HTML>' --allow-root
```

O para contenido largo, usar archivo temporal:

```bash
cat > /tmp/contenido-<ID>.html << 'EOF'
<HTML>
EOF
podman exec -i wp_bc_cli wp post update <ID> \
  --post_content="$(cat /tmp/contenido-<ID>.html)" --allow-root
```

---

### Fase 4: Verificar

```bash
podman exec wp_bc_cli wp post get <ID> --field=post_content --allow-root | head -c 200
```

---

## Estructura modular del contenido

### Nivel 1 — Intro (obligatorio, 1 párrafo)

Un solo `<p>` que responde **"¿Qué es esta ubicación?"**. Es la fuente
del excerpt y la meta description. Debe contener la palabra clave
principal y ser atractivo para motores de búsqueda.

```
<p>[Nombre] es [qué es], ubicada en [dónde]. Es conocida por [relevancia
bíblica principal].</p>
```

**Sin `<h2>`:** el template (`single-bc_location.php:454-458`) toma
todo lo que está antes del primer `<h2>` como introducción, e inserta
el mapa interactivo entre esta introducción y la primera sección.
Si el contenido arranca con `<h2>`, la introducción queda vacía.

**Sin etimología ni historia aquí.** Eso va en los módulos siguientes.

---

### Nivel 2 — Módulos de contenido (seleccionar según aplique)

Cada módulo es `<h2>` + párrafos. **Solo se incluyen si hay
información suficiente** para llenarlos con contenido sustantivo.

| # | Módulo | `<h2>` | Cuándo incluirlo |
|---|--------|--------|------------------|
| 1 | **Etimología y nombres** | `Etimología y nombres` | Siempre que haya datos sobre el significado del nombre |
| 2 | **Geografía** | `Geografía` | Si la ubicación existe hoy o hay coordenadas conocidas |
| 3 | **Origen e historia temprana** | `Origen e historia temprana` | Si hay datos sobre fundación o menciones prebíblicas |
| 4 | **Historia en las Escrituras** | `Historia en las Escrituras` | **Siempre** — la sección principal. Puede subdividirse con `<h3>` |
| 5 | **Historia postbíblica** | `Historia postbíblica` | Si hay datos relevantes (romano, bizantino, islámico, moderno) |
| 6 | **Arqueología y evidencia** | `Arqueología y evidencia histórica` | Solo si hay hallazgos relevantes que aporten certeza |
| 7 | **Lecciones y simbolismo** | `Lecciones y simbolismo` | Siempre que haya lecciones espirituales, prácticas o morales |
| 8 | **Situación actual** | `Situación actual` | Si la ubicación existe hoy y tiene relevancia contemporánea |

**El conocimiento revelado se integra donde sea más oportuno.** Por
ejemplo:
- **Egipto**: Abraham 1:23 (Egyptus = prohibido) en "Origen"; egipcio
  reformado en "Historia en las Escrituras"
- **Jerusalén**: Lehi (1 Nefi) en "Historia en las Escrituras";
  Nueva Jerusalén en "Lecciones y simbolismo"
- **Babilonia**: DyC 133:5–14 ("salir de Babilonia") en "Lecciones"

---

### Nivel 3 — Conclusión (obligatorio)

Un párrafo `<p>` de cierre efectivo. Sintetiza el significado
perdurable de la ubicación o conecta con el plan de salvación.

---

### Nivel 4 — Fuentes consultadas (obligatorio)

```html
<h2>Fuentes consultadas</h2>
<ul>
  <li><a href="https://...">Título de la obra o artículo</a></li>
  <li>Autor, <em>Título del libro</em>, Editorial, Año</li>
</ul>
```

- Formato: `<ul>` con `<li>` por fuente
- **Verídico**: solo obras realmente consultadas. Nada inventado
- **Online**: enlaces funcionales (sin 404, sin redirecciones rotas)
- **Offline**: autor, título, editorial, año
- **Sin referencias internas**: usar títulos reales de obras, no
  "corpus" ni "Alejandría"
- **SEO**: Google valida la calidad de los enlaces salientes

---

### Nivel 5 — Referencias de las Escrituras (obligatorio)

Forma T. Es un formato de estudio escritural de tres elementos:
**título + objetivo + tabla de dos columnas**. En esta plantilla
se omiten título y objetivo del estudio; solo se usa la tabla.

```html
<h2>Referencias de las Escrituras</h2>

<table class="bc-forma-t">
  <thead>
    <tr>
      <th>Concepto</th>
      <th>Referencia</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Idea didáctica en ≤15 palabras sin punto final</td>
      <td>Libro capítulo:versículo</td>
    </tr>
  </tbody>
</table>
```

**Reglas:**
- **Título de sección**: siempre `<h2>Referencias de las Escrituras</h2>`
- **Concepto**: ≤15 palabras, idea completa, sin punto final. Cada fila
  captura una idea respaldada por su(s) referencia(s) escritural(es)
- **Orden didáctico**: cronológico o temático. La columna de conceptos,
  leída de principio a fin, debe constituir una lección coherente
- **Referencia**: libro capítulo:versículo en español (traducido)
- **Cantidad**: nivel A 8–15, nivel B 4–8, nivel C 2–4 filas

---

## Niveles de profundidad

Los niveles A/B/C se derivan de la columna `relevancia` pre-llenada en
`tracking/locations.db`:

| `relevancia` | Nivel | Perfil | Mín. palabras | Módulos típicos | Forma T |
|--------------|-------|--------|:-------------:|-----------------|:-------:|
| **3 — Alta** | **A** | Jerusalén, Babilonia, Egipto, Roma, Asiria, Nínive, Ur, Edén, Sión, Sinaí | 1000+ | Todos aplicables. Historia subdividida con `<h3>` | 8–15 filas |
| **2 — Media** | **B** | Hebrón, Betania, Capernaum, Damasco, Tiro, Belén, Siquem, la mayoría de ciudades | 400–800 | 4–6 módulos | 4–8 filas |
| **1 — Baja** | **C** | Aldeas de 1–2 menciones (Abel-mehola, Aczib, Adulam) | 150–300 | Intro + Historia + Lecciones + Fuentes + Referencias | 2–4 filas |

---

## Principios de redacción

| Principio | Aplicación |
|-----------|------------|
| **Narrativo y ameno** | Prosa fluida en `<p>`, dirigida al ser humano. Interesante, explicativa, no enciclopédica |
| **Espiritual** | Tono respetuoso que reconoce la mano de Dios en la historia |
| **Sin polémica** | No entretenerse en debates. Datos últimos y certeros. Responder preguntas potenciales sin polemizar |
| **Conocimiento revelado integrado** | Perla de Gran Precio, Libro de Mormón, DyC, JST se integran donde es oportuno, no en sección aparte |
| **Sin copy-paste** | Redacción original, voz propia. Citas textuales con referencia verificable |
| **Traducir referencias** | Toda referencia escritural en español usando `$en_to_es` |
| **No repetir sidebar** | No incluir coordenadas, tipo, confianza ni fuente |
| **SEO** | Palabras clave de cola larga, `<strong>` para énfasis en nombres de otros lugares, `<h2>/<h3>` semántico, enlaces salientes verificados, metadata description desde el intro |
| **SUD/mormón no son gentilicios** | Usar frases descriptivas: "Escrituras de la Restauración", "doctrina de la Iglesia", "teología de la Restauración". Excepción: «Santos de los Últimos Días» como sustantivo |
| **Enfoque en la ubicación** | Si `_bc_loc_type` es place type, solo la ubicación. No incluir biografía del personaje homónimo |
| **Sintaxis semántica** | `<em>` para palabras extranjeras o transliteraciones. `<strong>` para énfasis en nombres de lugares. `<h3>` para subdivisiones naturales |

---

## Mapa de referencias EN→ES

Toda referencia escritural en inglés debe traducirse a español usando
este mapa (definido en `single-bc_location.php:350-372`):

| Inglés | Español | Inglés | Español |
|--------|---------|--------|---------|
| Acts | Hechos | Genesis | Génesis |
| Exodus | Éxodo | Leviticus | Levítico |
| Numbers | Números | Deuteronomy | Deuteronomio |
| Joshua | Josué | Judges | Jueces |
| Ruth | Rut | Samuel | Samuel |
| Kings | Reyes | Chronicles | Crónicas |
| Ezra | Esdras | Nehemiah | Nehemías |
| Esther | Ester | Job | Job |
| Psalms | Salmos | Proverbs | Proverbios |
| Ecclesiastes | Eclesiastés | Song of Solomon | Cantares |
| Isaiah | Isaías | Jeremiah | Jeremías |
| Lamentations | Lamentaciones | Ezekiel | Ezequiel |
| Daniel | Daniel | Hosea | Oseas |
| Joel | Joel | Amos | Amós |
| Obadiah | Abdías | Jonah | Jonás |
| Micah | Miqueas | Nahum | Nahúm |
| Habakkuk | Habacuc | Zephaniah | Sofonías |
| Haggai | Hageo | Zechariah | Zacarías |
| Malachi | Malaquías | Matthew | Mateo |
| Mark | Marcos | Luke | Lucas |
| John | Juan | Romans | Romanos |
| Corinthians | Corintios | Galatians | Gálatas |
| Ephesians | Efesios | Philippians | Filipenses |
| Colossians | Colosenses | Thessalonians | Tesalonicenses |
| Timothy | Timoteo | Titus | Tito |
| Philemon | Filemón | Hebrews | Hebreos |
| James | Santiago | Peter | Pedro |
| Jude | Judas | Revelation | Apocalipsis |

**Nota:** Para libros del Libro de Mormón, DyC y Perla de Gran Precio,
usar los nombres en español estándar (1 Nefi, 2 Nefi, Jacob, Enós,
Jarom, Omni, Palabras de Mormón, Mosíah, Alma, Helamán, 3 Nefi,
4 Nefi, Mormón, Éter, Moroni; Doctrina y Convenios; Moisés, Abraham,
José Smith—Mateo, José Smith—Historia, Artículos de Fe).

---

## Datos prácticos

- **Contenedor WP-CLI**: `wp_bc_cli`. Comando: `podman exec wp_bc_cli wp ... --allow-root`
- **CPT**: `bc_location`, registrado en `bc-scripture-map/inc/class-location-cpt.php`
- **Single template**: `wp-content/themes/generatepress-child/single-bc_location.php` — grid 2 columnas (1fr + 300px)
- **Mapa interactivo**: se inserta automáticamente entre la intro y el primer `<h2>`
- **Zoom del mapa**: 40km (configurado por tipo en `bc-scripture-map.php:130-139`)
 - **Mapa EN→ES**: definido en `single-bc_location.php:350-372` como `$en_to_es`
- **Tipos**: city→Ciudad, region→Región, wilderness→Desierto, sea→Mar/Lago,
  river→Río, mountain→Montaña, settlement→Asentamiento, landmark→Lugar emblemático
- **Contenido**: HTML apto para Gutenberg en `post_content`
- **Sobrescribir**: si ya tiene `post_content`, reemplazarlo
 - **Tracking**: `tracking/locations.db` (SQLite) para progreso entre sesiones
 - **Relevancia**: columna `relevancia` pre-llenada en `locations.db` (1=Baja/C, 2=Media/B, 3=Alta/A). No recalcular durante generación. El infobox de la página muestra la relevancia justo antes de la confianza. El template inyecta automáticamente un bloque "Datos clave" y FAQ schema para Alta/Media.
 - **Backup**: `backups/db-latest.sql` se actualiza automáticamente cada 10 min
- **Alejandría**: MCP tools disponibles vía `alejandria_*` (ver skill `alejandria-search`)
- **MSYS_NO_PATHCONV**: anteponer en Git Bash si altera rutas

---

## Contenido existente

**Sobrescribir siempre.** Aunque la ubicación ya tenga `post_content`
del formato anterior (~98 ubicaciones), se reemplaza con el nuevo
formato modular. No conservar nada del formato anterior.

```bash
# Verificar si tiene contenido existente
podman exec wp_bc_cli wp post get <ID> --field=post_content --allow-root | wc -w
```

---

## Flujo de batch processing

Para procesar las ~1,349 ubicaciones, usar este pipeline por lotes de
10, en orden de relevancia (Alta → Media → Baja):

### Preparación

```bash
# 1. Obtener IDs pendientes ordenados por relevancia DESC, wp_id ASC
python -c "
import tracking.tracker as t
queue = t.get_regeneration_queue(batch_size=10)
ids = [r['wp_id'] for r in queue]
print('IDs:', ids)
"
```

### Por cada lote

1. Registrar lote en SQLite:
   ```bash
   python -c "
   import tracking.tracker as t
   b = t.create_batch()
   print(f'Batch {b} created')
   ids = [5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
   t.add_locations(b, ids)
   t.mark_processing(ids)
   "
   ```

 2. Lanzar agentes de generación (2 agentes × 5 ubicaciones cada uno)

 3. Cada agente ejecuta Fase 0→5 para sus 5 ubicaciones:
    - Fase 0: Obtener datos + validar tipo + alias
    - Fase 0b: Validar tipo, corregir si es necesario
    - Fase 0c: Leer relevancia pre-llenada y derivar nivel A/B/C
    - Fase 0d: Aplicar plantilla de investigación por tipo
    - Fase 0e: Ejecutar checklist de fuentes
    - Fase 1: Investigar (Alejandría → web)
    - Fase 2: Redactar en HTML (datos clave auto, intro, módulos, FAQ si aplica, fuentes, Forma T)
    - Fase 3: Publicar con `wp post update`
    - Fase 4: Verificar
    - Fase 5: Marcar completed en tracking

 4. Cerrar batch en tracking:
    ```bash
    python -c "
    import tracking.tracker as t
    t.complete_batch(<BATCH_ID>, '<COMMIT_HASH>')
    "
    ```

 5. Dump DB y commit:
    ```bash
    podman exec wp_bc_cli wp db export /var/www/html/backups/db-latest.sql --allow-root
    git add -A && git commit -m "feat(glosario): lote N — 10 ubicaciones"
    ```

### Tracking

```bash
python tracking/tracker.py stats        # Ver progreso global
python tracking/tracker.py last-batch   # Ver último lote
```

### Colas de regeneración

```bash
# Obtener siguiente batch automáticamente (10 Alta, luego 10 Media, etc.)
python -c "
import tracking.tracker as t
queue = t.get_regeneration_queue(batch_size=10, relevance_filter=3)  # Alta primero
print(f'Siguientes {len(queue)} Alta')
queue = t.get_regeneration_queue(batch_size=10, relevance_filter=2)  # Luego Media
print(f'Siguientes {len(queue)} Media')
queue = t.get_regeneration_queue(batch_size=10, relevance_filter=1)  # Final Baja
print(f'Siguientes {len(queue)} Baja')
"
```


### Qué NO hacer

- No procesar ubicaciones con `_bc_loc_alias_of` seteado
- No regenerar thumbnails ni tocar imágenes
- No modificar la base de datos fuera de `wp post update` y corrección
  de metadatos
- No detener los contenedores existentes
- No forzar recreación de contenedores

---

## Casos especiales

### Ubicaciones con el mismo nombre que personas
Si `_bc_loc_type` es city/region/river/etc., centrarse exclusivamente
en la ubicación. No incluir biografía del personaje homónimo.
Si el personaje es relevante para la historia del lugar, mencionarlo
solo en función de esa relación.

### Ubicaciones sin información
```html
<p>[Nombre] es un(a) [tipo] mencionado(a) en las Escrituras. Aparece en
[referencia]. No se dispone de información detallada sobre esta
ubicación.</p>
```

### Tipo incorrecto

Si `_bc_loc_type` no corresponde a la realidad de la ubicación,
corregirlo antes de redactar. Tipos válidos: `city`, `region`,
`wilderness`, `sea`, `river`, `mountain`, `settlement`, `landmark`.

```bash
podman exec wp_bc_cli wp post meta update <ID> _bc_loc_type city --allow-root
```

### Interlinking
Usar **negritas** en menciones de otros lugares del glosario. En
ausencia de interlinking automático, las negritas son la técnica
recomendada para énfasis semántico y SEO.
