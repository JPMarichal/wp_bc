---
name: glosario-ubicaciones-contenido
description: |
  Generar contenido narrativo (post_content) del CPT bc_location para el
  Glosario de Ubicaciones bíblicas. Usar cuando se necesite redactar la
  descripción de una ubicación desde fuentes del corpus de Alejandría,
  BYU/FAIR, BibleHub, Wikipedia o diccionarios bíblicos. Proporciona el
  pipeline completo de extracción → redacción → publicación, plantilla
  modular según el nivel de la ubicación y principios de redacción
  congruentes con la doctrina de la Restauración.
---

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
docker exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Nombre" \
  --fields=ID,post_title,post_name,post_status --allow-root

docker exec wp_bc_cli wp post meta list <ID> --allow-root

docker exec wp_bc_cli wp post get <ID> --field=post_content --allow-root
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
docker exec wp_bc_cli wp post meta update <ID> _bc_loc_type <tipo_correcto> --allow-root
```

Casos conocidos de tipo incorrecto:
- **Egipto** (ID 2827) estaba como `water` → debe ser `region`
- `valley`, `island`, `path` no son tipos válidos → reclasificar

#### 2. Detectar duplicados y alias

Verificar si la ubicación tiene `_bc_loc_alias_of` seteado. Si es alias
de otra, **no procesarla**: su contenido se genera desde la principal.

```bash
docker exec wp_bc_cli wp post meta get <ID> _bc_loc_alias_of --allow-root
```

Para detectar duplicados no marcados, buscar por `_bc_loc_name_en`:

```bash
docker exec -e MYSQL_PWD=wppass wp_bc_db mysql -uwpuser bc_wp -e "
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
  docker exec wp_bc_cli wp post meta update <ID_MAYOR> _bc_loc_alias_of <ID_MENOR> --allow-root
  ```

**No procesar ubicaciones que sean alias de otra.** Saltarlas en el
batch loop.

---

### Fase 0c: Determinar nivel

Clasificar la ubicación como A, B o C según estas reglas:

| Factor | A (1000+ palabras) | B (400–800 palabras) | C (150–300 palabras) |
|--------|:------------------:|:--------------------:|:--------------------:|
| Menciones escriturales | 10+ | 3–9 | 1–2 |
| Referencias en `_bc_loc_scriptures` | 8+ | 3–7 | 1–2 |
| Relevancia histórica | Capitales, imperios, regiones mayores | Ciudades medianas, valles, montañas notables | Aldeas, parajes menores |
| Ejemplos | Jerusalén, Babilonia, Egipto, Roma, Nínive, Ur, Edén, Sión, Sinaí, Asiria | Hebrón, Betania, Capernaum, Damasco, Tiro, Belén, Siquem, Jordán | Abel-mehola, Aczib, Adulam, Buz, Cabul |
| Módulos típicos | Todos aplicables (8). Historia subdividida con `<h3>` | 4–6 módulos | Intro + Historia + Lecciones + Fuentes + Forma T |

**Regla práctica**: si tiene dudas, elegir B. Siempre se puede ajustar
según el material encontrado durante la investigación.

---

### Fase 1: Investigar

**Alejandría es la fuente primaria.** Agotar todas las búsquedas antes
de ir a fuentes externas.

```bash
# 1. Knowledge Graph
alejandria_kg_find(query: "<nombre>")

# 2. Corpus textual
alejandria_search_text(query: "<nombre>", limit: 10)
alejandria_search_text(query: "<nombre>", source_filter: "es/scriptures")
alejandria_search_text(query: "<nombre>", source_filter: "en/scriptures")
alejandria_search_text(query: "<nombre>", source_filter: "es/manuals")
alejandria_search_text(query: "<nombre>", source_filter: "en/manuals")

# 3. RAG
alejandria_chat_ask(question: "¿Qué importancia tiene <nombre> en la Biblia?")
alejandria_chat_ask(question: "¿Qué enseñan las Escrituras de la Restauración sobre <nombre>?")

# 4. Semántica
alejandria_search_semantic(query: "<nombre> biblical significance")
```

**Examinar si el sitio es mencionado por Autoridades Generales o
eruditos SUD (BYU, Maxwell Institute, FAIR).** Esto permite integrar
el conocimiento revelado donde sea más oportuno.

Si Alejandría no es suficiente:

```bash
# Web SUD
webfetch(url: "https://rsc.byu.edu/search?q=<nombre>")
webfetch(url: "https://mi.byu.edu/search?q=<nombre>")
webfetch(url: "https://www.fairlatterdaysaints.org/search?q=<nombre>")
webfetch(url: "https://archive.bookofmormoncentral.org/search?q=<nombre>")

# Web general
webfetch(url: "https://www.churchofjesuschrist.org/study/scriptures/gs/<slug>?lang=spa")
webfetch(url: "https://biblehub.com/topical/<slug>.htm")
webfetch(url: "https://es.wikipedia.org/wiki/<nombre>")
```

**Reglas:**
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
  `single-bc_location.php:320-342` (ver tabla completa abajo)
- **SEO en todo momento**: palabras clave de cola larga,
  interlinking con negritas en menciones de otros lugares, sintaxis
  semántica (`<strong>` para énfasis, `<em>` para extranjerismos)

---

### Fase 3: Publicar

```bash
docker exec wp_bc_cli wp post update <ID> \
  --post_content='<HTML>' --allow-root
```

O para contenido largo, usar archivo temporal:

```bash
cat > /tmp/contenido-<ID>.html << 'EOF'
<HTML>
EOF
docker exec -i wp_bc_cli wp post update <ID> \
  --post_content="$(cat /tmp/contenido-<ID>.html)" --allow-root
```

---

### Fase 4: Verificar

```bash
docker exec wp_bc_cli wp post get <ID> --field=post_content --allow-root | head -c 200
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

Forma T. Hasta **15 filas** para ubicaciones nivel A.

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
- **Título**: siempre `<h2>Referencias de las Escrituras</h2>`
- **Concepto**: ≤15 palabras, idea completa. Sin punto final
- **Referencia**: libro capítulo:versículo en español (traducido)
- **Orden**: cronológico o temático. La columna de conceptos debe
  fluir como una lección coherente
- **Cantidad**: nivel A 8–15, nivel B 4–8, nivel C 2–4 filas

---

## Niveles de profundidad

| Nivel | Perfil | Mín. palabras | Módulos típicos | Forma T |
|-------|--------|:-------------:|-----------------|:-------:|
| **A** | Jerusalén, Babilonia, Egipto, Roma, Asiria, Nínive, Ur, Edén, Sión, Sinaí | 1000+ | Todos aplicables. Historia subdividida con `<h3>` | 8–15 filas |
| **B** | Hebrón, Betania, Capernaum, Damasco, Tiro, Belén, Siquem, la mayoría de ciudades | 400–800 | 4–6 módulos | 4–8 filas |
| **C** | Aldeas de 1–2 menciones (Abel-mehola, Aczib, Adulam) | 150–300 | Intro + Historia + Lecciones + Fuentes + Referencias | 2–4 filas |

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
este mapa (definido en `single-bc_location.php:320-342`):

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

## Fallback chain

```
Alejandría (KG → search_text → search_semantic → chat_ask)
  → Web SUD (BYU → Maxwell Institute → FAIR → Book of Mormon Central)
    → Guía para el Estudio de las Escrituras (churchofjesuschrist.org)
      → BibleHub (topical)
        → Wikipedia (español)
          → Diccionarios bíblicos (Holman, Smith, Easton)
```

---

## Datos prácticos

- **Contenedor WP-CLI**: `wp_bc_cli`. Comando: `docker exec wp_bc_cli wp ... --allow-root`
- **CPT**: `bc_location`, registrado en `bc-scripture-map/inc/class-location-cpt.php`
- **Single template**: `wp-content/themes/generatepress-child/single-bc_location.php` — grid 2 columnas (1fr + 300px)
- **Mapa interactivo**: se inserta automáticamente entre la intro y el primer `<h2>`
- **Zoom del mapa**: 40km (configurado por tipo en `bc-scripture-map.php:130-139`)
- **Mapa EN→ES**: definido en `single-bc_location.php:320-342` como `$en_to_es`
- **Tipos**: city→Ciudad, region→Región, wilderness→Desierto, sea→Mar/Lago,
  river→Río, mountain→Montaña, settlement→Asentamiento, landmark→Lugar emblemático
- **Contenido**: HTML apto para Gutenberg en `post_content`
- **Sobrescribir**: si ya tiene `post_content`, reemplazarlo
- **Tracking**: `tracking/locations.db` (SQLite) para progreso entre sesiones
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
docker exec wp_bc_cli wp post get <ID> --field=post_content --allow-root | wc -w
```

---

## Flujo de batch processing

Para procesar las ~1,752 ubicaciones, usar este pipeline por lotes de
20:

### Preparación

```bash
# 1. Consultar primeras N ubicaciones no-alias, ordenadas por ID
docker exec -e MYSQL_PWD=wppass wp_bc_db mysql -uwpuser bc_wp -e "
  SELECT p.ID, p.post_title, p.post_name,
         COALESCE(pm_name.meta_value, '') as name_en,
         COALESCE(pm_type.meta_value, '') as loc_type,
         COALESCE(pm_refs.meta_value, '[]') as scriptures
  FROM wp_posts p
  LEFT JOIN wp_postmeta pm_name ON p.ID=pm_name.post_id AND pm_name.meta_key='_bc_loc_name_en'
  LEFT JOIN wp_postmeta pm_type ON p.ID=pm_type.post_id AND pm_type.meta_key='_bc_loc_type'
  LEFT JOIN wp_postmeta pm_refs ON p.ID=pm_refs.post_id AND pm_refs.meta_key='_bc_loc_scriptures'
  LEFT JOIN wp_postmeta pm_alias ON p.ID=pm_alias.post_id AND pm_alias.meta_key='_bc_loc_alias_of'
  WHERE p.post_type='bc_location' AND p.post_status='publish'
    AND pm_alias.meta_id IS NULL
  ORDER BY p.ID
  LIMIT 20 OFFSET <offset>;"
```

### Por cada lote

1. Registrar lote en SQLite:
   ```bash
   python tracking/tracker.py
   ```
   (usar `import tracker` desde Python para llamar `create_batch()`,
   `add_locations()`, etc.)

2. Lanzar agentes de generación (5 agentes × 4 ubicaciones cada uno)

3. Cada agente ejecuta Fase 0→4 para sus 4 ubicaciones:
   - Fase 0: Obtener datos + validar tipo + alias
   - Fase 0b: Validar tipo, corregir si es necesario
   - Fase 0c: Determinar nivel A/B/C
   - Fase 1: Investigar (Alejandría → web)
   - Fase 2: Redactar en HTML
   - Fase 3: Publicar con `wp post update`
   - Fase 4: Verificar

4. Marcar en tracking:
   ```python
   tracker.mark_completed(wp_id, word_count)
   ```

5. Verificar contenido de todas las ubicaciones del lote

6. Hacer dump de BD:
   ```bash
   docker exec wp_bc_cli wp db export /var/www/html/backups/db-latest.sql --allow-root
   ```

7. Cerrar lote en tracking:
   ```python
   tracker.complete_batch(batch_id, commit_hash)
   ```

8. Commit y push:
   ```bash
   git add -A && git commit -m "feat(glosario): lote N — 20 entradas" && git push
   ```

### Tracking

```bash
python tracking/tracker.py stats        # Ver progreso global
python tracking/tracker.py last-batch   # Ver último lote
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
docker exec wp_bc_cli wp post meta update <ID> _bc_loc_type city --allow-root
```

### Interlinking
Usar **negritas** en menciones de otros lugares del glosario. En
ausencia de interlinking automático, las negritas son la técnica
recomendada para énfasis semántico y SEO.
