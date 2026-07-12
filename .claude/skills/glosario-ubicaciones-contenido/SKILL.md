---
name: glosario-ubicaciones-contenido
description: |
  Generar contenido narrativo (post_content) del CPT bc_location para el
  Glosario de Ubicaciones bíblicas. Usar cuando se necesite redactar la
  descripción de una ubicación desde fuentes del corpus de Alejandría,
  Guía para el Estudio de las Escrituras, BibleHub, o Wikipedia como
  último recurso. Proporciona el pipeline completo de extracción →
  redacción → publicación, plantilla de secciones y principios de
  redacción congruentes con la doctrina de la Restauración.
---

# Skill: glosario-ubicaciones-contenido

Genera el contenido narrativo (`post_content`) de una ubicación del CPT
`bc_location`. El contenido se despliega en la columna principal de
`single-bc_location.php`, mientras el sidebar muestra los metadatos
(tipo, coordenadas, confianza, referencia de ejemplo).

## Pipeline completo

### Fase 0: Obtener datos de la ubicación

Identificar el ID y metadatos de la ubicación:

```bash
# Buscar ubicación por nombre
docker exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Nombre de la ubicación" \
  --fields=ID,post_title,post_name,post_status --allow-root

# Obtener metadatos completos
docker exec wp_bc_cli wp post meta list <ID> --allow-root

# Ver contenido actual (si existe)
docker exec wp_bc_cli wp post get <ID> --field=post_content --allow-root
```

Metadatos disponibles en `bc_location`:

| Meta key | Descripción | Tipo |
|----------|-------------|------|
| `_bc_loc_name_en` | Nombre en inglés (KJV) | string |
| `_bc_loc_disambiguation` | Contexto desambiguante para homónimos (ej: Siria, Pisidia, América) | string |
| `_bc_loc_type` | Tipo: city, region, wilderness, sea, river, mountain, settlement, landmark | string |
| `_bc_loc_scriptures` | Array JSON de referencias escriturales inglesas | string (JSON) |
| `_bc_loc_description` | Descripción breve (usualmente basura tipo "Fuente: openbible") | string |
| `_bc_loc_lat` | Latitud | number |
| `_bc_loc_lng` | Longitud | number |
| `_bc_loc_source` | Fuente de datos: openbible, gnosis, church-history | string |
| `_bc_loc_confidence` | Confianza: high, medium, low | string |
| `_bc_loc_date_from` | Año inicial (aprox.) | integer |
| `_bc_loc_date_to` | Año final (aprox.) | integer |
| `_bc_loc_alt_names` | Nombres alternativos (JSON array: `["Jebús","Salem"]`) | string (JSON) |
| `_bc_loc_alias_of` | ID de la ubicación principal si esta entry es un nombre alternativo de otra (ej: Jebús → Jerusalén) | integer |

Anotar el ID, el nombre en inglés (`_bc_loc_name_en`), el tipo, y las
referencias escriturales (`_bc_loc_scriptures`). Estas serán la base
de la investigación.

### Fase 1: Investigar con Alejandría

La prioridad es **Alejandría como fuente principal**. Lanzar múltiples
consultas en paralelo para obtener la máxima cobertura:

```bash
# 1. Buscar la ubicación en el Knowledge Graph
alejandria_kg_find(query: "<nombre inglés o español>")
  → resuelve la entidad en el KG

# 2. Buscar en todo el corpus (sin source_filter)
alejandria_search_text(query: "<nombre>", limit: 10)
alejandria_search_semantic(query: "<nombre> location biblical")
  → descubre qué documentos del corpus mencionan la ubicación

# 3. Buscar en escrituras (más probable)
alejandria_search_text(query: "<nombre>",
  source_filter: "es/scriptures")
alejandria_search_text(query: "<nombre>",
  source_filter: "en/scriptures")

# 4. Buscar en guías de estudio y manuales
alejandria_search_text(query: "<nombre>",
  source_filter: "es/manuals")
alejandria_search_text(query: "<nombre>",
  source_filter: "en/manuals")

# 5. Preguntar directamente via RAG
alejandria_chat_ask(question: "¿Qué importancia tiene <nombre> en la Biblia?")
  → si falla (error conocido del pipeline RAG), usar search_hybrid como fallback
```

**Reglas de integración con Alejandría:**
- Alejandría es la fuente **primaria** — agotar todas las búsquedas antes de ir a fallback
- Hacer búsquedas tanto en español como en inglés
- Los resultados de `chat_ask` pueden incluir contexto teológico valioso
- Si `kg_find` no encuentra la entidad, no significa que el corpus no tenga información — la ubicación puede estar mencionada en textos sin ser entidad del KG
- Si una búsqueda con `source_filter` no da resultados, probar sin filter

### Fase 2: Fallback — Guía para el Estudio de las Escrituras

Si Alejandría no tiene suficiente información, consultar la **Guía para
el Estudio de las Escrituras** (LDS):

```
https://www.churchofjesuschrist.org/study/scriptures/gs/<slug>?lang=spa
```

Ejemplo: https://www.churchofjesuschrist.org/study/scriptures/gs/adam?lang=spa

Usar `webfetch` para obtener el contenido en texto/markdown.

### Fase 3: Fallback — BibleHub

Si la Guía para el Estudio de las Escrituras no tiene entrada, consultar **BibleHub**:

```
https://biblehub.com/topical/<slug>.htm
```

Ejemplo: https://biblehub.com/topical/a/abel-shittim.htm

Usar `webfetch` para obtener el contenido. BibleHub está en inglés;
traducir y adaptar al español.

### Fase 4: Fallback — Wikipedia

Último recurso. Consultar Wikipedia en español:

```
https://es.wikipedia.org/wiki/<nombre>
```

Usar `webfetch` para obtener el contenido. Verificar que la
información sea factual y no contradiga la doctrina de la Restauración.

### Fase 5: Redactar el contenido

Escribir `post_content` en **HTML** siguiendo la **Plantilla de
secciones** y los **Principios de redacción** abajo.

Reglas clave:
- **Contenido nuevo**, no copia textual de ninguna fuente
- **Doctrina de la Restauración**: la información debe ser congruente con la teología
  de La Iglesia de Jesucristo de los Santos de los Últimos Días
- **Sintetizar**, no concatenar fuentes
- El contenido se despliega en la columna principal de la single
  (dentro de un card con fondo blanco). El sidebar aparte muestra
  tipo, coordenadas, confianza y referencia de ejemplo — **no repetir**
  esos datos en el contenido narrativo
- Las referencias escriturales deben mostrarse en español. Usar el mapa
  `en_to_es` definido en `single-bc_location.php:369-391`
- Longitud recomendada: **150–400 palabras** (suficiente para ser
  informativo sin abrumar en un glosario)

### Fase 6: Publicar en el CPT

```bash
# Guardar contenido HTML a un archivo temporal
# (usar ruta accesible desde el contenedor)
cat > /tmp/contenido-<ID>.html << 'EOF'
<h2>Sección 1</h2>
<p>Contenido...</p>
EOF

# Publicar el contenido (post_content)
docker exec -i wp_bc_cli wp post update <ID> \
  --post_content="$(cat /tmp/contenido-<ID>.html)" \
  --allow-root

# Alternativa: pasar el contenido directamente
docker exec wp_bc_cli wp post update <ID> \
  --post_content='<h2>Ubicación</h2><p>Contenido...</p>' \
  --allow-root
```

**Nota importante sobre shell escaping en Windows/Git Bash:**
Si el contenido tiene caracteres especiales, usar un archivo temporal
y redirigir con `<` en vez de pasar el HTML inline:

```bash
# Escribir contenido a archivo (desde PowerShell o CMD)
# Luego leerlo desde el contenedor:
type C:\ruta\al\archivo.html | docker exec -i wp_bc_cli wp post update <ID> --post_content="$(cat /dev/stdin)" --allow-root

# O más simple, escribir directo desde PowerShell:
docker exec wp_bc_cli wp post update <ID> --post_content="<h2>Sección</h2><p>Contenido</p>" --allow-root
```

### Fase 7: Verificar

```bash
# Verificar que el contenido se haya guardado
docker exec wp_bc_cli wp post get <ID> \
  --field=post_content --allow-root

# Verificar la página en el frontend
# https://<site>/ubicacion/<slug>/
```

### Procesamiento por lotes (wrapper)

Para procesar varias ubicaciones, usar un bucle shell. Ejemplo:

```bash
# Lista de IDs a procesar
IDS=(123 456 789)

for ID in "${IDS[@]}"; do
  echo "=== Procesando ID $ID ==="

  # Obtener datos
  TITLE=$(docker exec wp_bc_cli wp post get $ID --field=post_title --allow-root)
  NAME_EN=$(docker exec wp_bc_cli wp post meta get $ID _bc_loc_name_en --allow-root)

  echo "Ubicación: $TITLE ($NAME_EN)"

  # [Aquí el agente investiga y redacta para cada ubicación]

  # Publicar
  docker exec wp_bc_cli wp post update $ID \
    --post_content="<contenido>" --allow-root
done
```

Cada iteración del bucle requiere que el agente investigue y redacte
individualmente. El bucle es solo el wrapper mecánico.

## Plantilla de secciones

El contenido se estructura en **párrafos narrativos** con títulos HTML
(`<h2>`) para las secciones. No usar listas ni tablas en el cuerpo
(excepto en la sección "Referencias de las Escrituras", que usa tabla
Forma T).

### Encabezado descriptivo (sin título) — obligatorio

1–3 párrafos que describen la ubicación: qué es, dónde está (contexto
geográfico general), por qué es significativa. No repetir coordenadas
ni el tipo (eso va en el sidebar).

**⚠️ Sin `<h2>`:** el template (`single-bc_location.php:454-458`) toma
todo lo que está antes del primer `<h2>` como la introducción, e inserta
el mapa interactivo entre esa introducción y la primera sección con
título. Si el contenido arranca con `<h2>`, la introducción queda vacía
y el mapa aparece antes que cualquier texto.

**Esta sección es obligatoria.** El mapa se inserta automáticamente
entre el final del encabezado descriptivo y el primer `<h2>`, siempre
que la ubicación tenga coordenadas (`_bc_loc_lat`, `_bc_loc_lng`).

Incluir **significado etimológico del nombre** de forma breve en la
primera mención, con la fórmula «cuyo nombre significa» o «su nombre
proviene de»:

- Jerusalén → «cuyo nombre significa "fundación de paz"»
- Belén → «cuyo nombre significa "casa de pan"»
- Betania → «su nombre significa "casa de higos" o "casa de aflicción"»

Si la ubicación tuvo nombres anteriores (ej: Jerusalén fue Salem y
Jebús antes de llamarse Jerusalén), mencionarlos brevemente.

Ejemplo: "Jerusalén —cuyo nombre significa 'fundación de paz'— es la
ciudad más significativa de la historia bíblica, ubicada en los montes
de Judea. Fue conocida originalmente como Salem (Génesis 14:18) y
posteriormente como Jebús (Jueces 19:10–11), por los jebuseos que la
habitaban."

### Contexto histórico y bíblico

Qué eventos ocurrieron aquí en las Escrituras. Personajes asociados.
Relevancia en la narrativa bíblica. Mencionar pasajes clave.

Ejemplo: "En el Nuevo Testamento, Betania es conocida como el hogar
de María, Marta y Lázaro. Fue aquí donde Jesús resucitó a Lázaro
(Juan 11:1–44), y donde fue ungido por María seis días antes de la
Pascua (Juan 12:1–8)."

### Significado en la teología de la Restauración

Relevancia doctrinal o histórica desde la perspectiva de la Restauración. Cómo se
interpreta esta ubicación en el marco del Evangelio restaurado.
Conexiones con otras escrituras (DyC, Libro de Mormón, Perla de
Gran Precio) si aplican.

Ejemplo: "Para los Santos de los Últimos Días, Betania tiene especial
significado porque fue testigo de una de las más grandes muestras del
poder divino de Jesucristo: la resurrección de Lázaro. Este milagro
prefiguró la propia resurrección del Salvador y es una poderosa
declaración de Su divinidad."

### Nombres alternativos (alias)

Muchas ubicaciones bíblicas tienen nombres alternativos (Jerusalén también
es Salem, Jebús, Sión). El sistema distingue dos tipos:

**Alias simples** (texto plano): nombres que no tienen historia independiente
y no merecen entrada propia. Ej: "Ciudad de David" para Jerusalén.
Se configuran como string JSON en `_bc_loc_alt_names`:
```bash
docker exec wp_bc_cli wp post meta set <ID> _bc_loc_alt_names '["Nombre1","Nombre2"]'
```

**Alias con entrada propia**: nombres que merecen su propio artículo porque
tienen suficiente contenido histórico. La entry secundaria se crea con
`_bc_loc_alias_of` apuntando a la principal:
```bash
docker exec wp_bc_cli wp post create --post_type=bc_location --post_title="Jebús" --post_status=publish
docker exec wp_bc_cli wp post meta set <new_ID> _bc_loc_alias_of <main_ID>
```
Además, el nombre debe agregarse al `_bc_loc_alt_names` de la ubicación
principal para que aparezca en los badges.

**Auto-linking**: cuando una ubicación tiene `_bc_loc_alt_names`, el template
busca automáticamente si cada alias coincide exactamente con el título de
otra entry de `bc_location`. Si hay exactamente 1 coincidencia, se renderiza
como link; si hay 0 o >1 (homónimos), se renderiza como texto plano.

**Visualización**:
- Badges semi-transparentes en el hero (justo después del título)
- Fila "También conocido como:" en el infobox lateral (icono `fa-tag`)
- Las entries con `_bc_loc_alias_of` muestran un bloque "Nombre alternativo
  de [link]" entre la navegación y el hero (caja blanca con borde izquierdo
  azul, mismo estilo que "Otras acepciones")

**Decisión**: si un nombre alternativo puede sustentar 2+ párrafos con
fuentes propias, merece entrada independiente. Si es solo un nombre
alternativo sin historia separada, queda como alias simple en
`_bc_loc_alt_names`.

**Importación masiva desde gnosis-places.json**: el dataset `gnosis-places.json`
contiene 1023 entradas con aliases en inglés (variant names de KJV/ESV). Se
importaron automáticamente a 445 posts de `bc_location`, emparejando por
`_bc_loc_name_en`, `post_name`, y el ID del gnosis. Los alias demostrativos
(gentilicios como "Ammonite", "Gileadite") se excluyeron. El import mergea con
alias existentes sin duplicar:

```bash
docker exec wp_bc_cli wp eval --allow-root '
  $gnosis = json_decode(file_get_contents("/var/www/html/wp-content/plugins/bc-scripture-map/data/gnosis-places.json"), true);
  // ... Matching logic in /tmp/import-gnosis-aliases.php
'
```

El meta `_bc_loc_alt_names` debe almacenarse como **string JSON** (no como
serialized PHP array). `register_post_meta()` con `type => string` + `show_in_rest`
requiere JSON. Si se usa `wp post meta set`, pasar el JSON como argumento:
`wp post meta set <ID> _bc_loc_alt_names '["Alias1","Alias2"]'`.

El template en `single-bc_location.php:338-340` maneja ambos formatos:
```php
$alt_names_raw = get_post_meta( $pid, '_bc_loc_alt_names', true );
$alt_names = is_array( $alt_names_raw ) ? $alt_names_raw
  : ( $alt_names_raw ? json_decode( $alt_names_raw, true ) : array() );
```

**Alias manuales configurados** (ubicaciones cuyos `_bc_loc_name_en` están en
español y no matchearon con gnosis):

| Ubicación | ID | Alias configurados |
|-----------|----|--------------------|
| Jerusalén | 537 | "Ciudad de David", "Jebús", "Salem", "Sión", "Zion" |
| Hebrón | 237 | "Quiriat-arba", "Hebron" |
| Betel | 81 | "Luz", "Casa de Dios", "Beth-el", "Bethel" |
| Belén de Judea | 73 | "Efrata", "Belén Efrata", "Bethlehem" |
| Jericó | 274 | "Ciudad de las Palmas", "Jericho" |
| Nazaret | 360 | "Nazareno", "Nazareth" |
| Siquem | 434 | "Sichem", "Siquén", "Siquemites" |
| Capernaúm | 118 | "Cafarnaúm", "Capernaum" |
| Cesarea de Filipos | 114 | "Panías", "Paneas", "Cesarea de Filipo" |

### Otras acepciones (homónimos)

Si el nombre de la ubicación designa lugares diferentes en las Escrituras
(ej: Jerusalén de Judea vs. Jerusalén de América; Antioquía de Siria vs.
Antioquía de Pisidia), **no incluir** esa información en el contenido
principal. El template `single-bc_location.php` detecta automáticamente
los homónimos (mismo `post_title`, distinto ID) y genera un bloque
"Otras acepciones" con enlaces a las otras entradas.

Tu responsabilidad es:
1. Redactar el contenido de cada entrada como si fuera independiente
2. Poblar `_bc_loc_disambiguation` con el contexto que la distingue
   (ej: "Siria", "Pisidia", "América")

El bloque automático de homónimos se renderiza justo después del título
(estilo diccionario/enciclopedia), antes del contenido narrativo.

### Referencias de las Escrituras — Forma T (obligatorio)

Presentar las referencias más significativas como una **Forma T**:
formato didáctico de tres elementos (título, objetivo, tabla) donde
cada fila captura una idea respaldada por su(s) referencia(s).
La Forma T reemplaza la lista genérica de referencias; leer solo la
columna de conceptos debe constituir una lección coherente.

**Estructura HTML:**

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
      <td>Idea completa en 15 palabras o menos</td>
      <td>Libro capítulo:versículo</td>
    </tr>
    <tr>
      <td>Siguiente concepto didáctico sin punto final</td>
      <td>Siguiente referencia</td>
    </tr>
  </tbody>
</table>
```

**Reglas:**
- **Título**: siempre `<h2>Referencias de las Escrituras</h2>`
- **Tabla**: `<table class="bc-forma-t">` con `<thead>` (Concepto |
  Referencia) y `<tbody>` con filas
- **Concepto**: ≤15 palabras, idea completa y didáctica. Sin punto
  final. Debe responder "¿qué enseña esta referencia sobre la ubicación?"
- **Referencia**: libro capítulo:versículo en español, traducido con el
  mapa `en_to_es` de `single-bc_location.php:369-391`
- **Orden**: secuencia lógica — cronológica, temática o narrativa —
  para que la columna de conceptos fluya como lección
- **Cantidad**: 4–8 filas para ubicaciones mayores, 2–4 para menores.
  No incluir TODAS las referencias de `_bc_loc_scriptures`; solo las
  más significativas

**Generación de conceptos con Alejandría:**

Usar Alejandría para derivar el concepto de cada referencia clave:

```bash
# Preguntar qué evento o significado relaciona la escritura con la ubicación:
alejandria_chat_ask(question: "¿Qué evento ocurrió en <ubicación> según <ref>?")

# Buscar el pasaje directamente:
alejandria_search_text(query: "<ubicación> <libro> <capítulo>",
  source_filter: "es/scriptures")
```

El concepto es una **síntesis didáctica** del significado de ese pasaje
para la ubicación, no un título ni una cita textual.

**Ejemplos de concepto correcto:**

| Concepto (≤15 palabras, idea completa) | Referencia |
|:---------------------------------------|:-----------|
| David conquistó la ciudad y la hizo su capital | 2 Samuel 5:6–9 |
| Jesús fue crucificado y resucitó aquí | Lucas 23:33–24:6 |
| Ciudad del gran Rey y centro de adoración verdadera | Salmo 48:1–2 |

**Nota sobre el template PHP:**
`single-bc_location.php` ya no renderiza referencias automáticas desde
`_bc_loc_scriptures`. La Forma T en `post_content` es la única fuente
de referencias escriturales. Si no hay Forma T, no se muestra nada.

## Principios de redacción

| Principio | Aplicación |
|-----------|------------|
| **Narrativo** | Prosa fluida en párrafos HTML (`<p>`), sin viñetas. Tabla solo en "Referencias de las Escrituras" (Forma T) |
| **Tono congruente con la doctrina de la Restauración** | Lenguaje respetuoso, favorable a la Iglesia. No incluir teorías especulativas o críticas textuales que contradigan la fe |
| **Sin copy-paste** | Ningún párrafo debe ser idéntico al de una fuente. Redacción original, voz propia |
| **Traducir referencias** | Toda referencia escritural debe ir en español usando el mapa de traducción inglés→español |
| **No repetir sidebar** | No incluir coordenadas, tipo, confianza, fuente ni referencia de ejemplo en el contenido narrativo (ya están en el infobox lateral) |
| **150–400 palabras** | Suficiente para ser útil sin ser exhaustivo — es un glosario, no una monografía |
| **Longitud flexible** | Ubicaciones mayores (Jerusalén, Belén) pueden tener 400–600 palabras; las menores (una aldea mencionada una vez) pueden tener 100–150 |
| **Información factual** | Basarse en fuentes verificables. Si hay dudas sobre un dato, omitirlo |
| **Jerarquía de fuentes** | Alejandría (KG + corpus) → Guía para el Estudio de las Escrituras → BibleHub → Wikipedia (último recurso) |
| **HTML semántico** | Usar `<h2>` para títulos de sección, `<p>` para párrafos. No usar `<h1>` (el título de la página ya es `<h1>`) |
| **Citas trazables** | Si se incluye una cita textual, debe ir con referencia verificable y en español |
| **No doctrinal** | No inventar doctrina. Limitarse a lo que las Escrituras y fuentes autorizadas dicen |
| **SUD/mormón no son gentilicios** | No usar "SUD", "mormón" ni "mormona" como adjetivo modificador de un sustantivo (ej: «Escrituras SUD», «doctrina mormona»). Usar frases descriptivas: "Escrituras de la Restauración", "doctrina de la Iglesia", "teología de la Restauración". La excepción es el nombre formal «Santos de los Últimos Días» como sustantivo |
| **Enfoque en la ubicación** | Si `_bc_loc_type` es un tipo de lugar (city, region, river...), el contenido debe tratar solo la ubicación. No incluir datos del personaje homónimo. Si un personaje se relaciona con el lugar (ej: David con Jerusalén), mencionarlo solo en función de esa relación |
| **No mencionar el sidebar** | El contenido debe ser auto-contenido — no decir "como se ve en la tabla lateral" ni referencias al layout de la página |

## Fallback chain (resumen)

```
Alejandría (KG + search_text + search_semantic + chat_ask)
  → Guía para el Estudio de las Escrituras (churchofjesuschrist.org)
    → BibleHub (topical)
      → Wikipedia (español)
        → Si nada funciona: "<Nombre> es [tipo] mencionada en [escrituras]. No se dispone de más información."
```

Alejandría es siempre la PRIMERA fuente. Agotar todas las búsquedas
de Alejandría (text, semantic, hybrid, kg_find, chat_ask) antes de
recurrir a fuentes externas.

## Datos prácticos

- **Contenedor WP-CLI**: `wp_bc_cli`. Comando: `docker exec wp_bc_cli wp ... --allow-root`
- **CPT**: `bc_location`, registrado en `bc-scripture-map/inc/class-location-cpt.php`
- **Single template**: `single-bc_location.php` — grid responsivo de 2 columnas (1fr + 300px)
- **Archive template**: `archive-bc_location.php` — glosario con filtros por letra, texto y tipo
- **`_bc_loc_disambiguation`**: meta string para desambiguar homónimos (ej: "Siria", "América"). Se muestra como badge en single y archive
- **`_bc_loc_alt_names`**: JSON array de strings con nombres alternativos. Se renderizan como badges en hero y fila en infobox. Auto-linking: si un alias coincide exactamente con el título de otra entry (1 sola coincidencia), se renderiza como link
- **`_bc_loc_alias_of`**: integer con el ID de la ubicación principal. La entry muestra bloque "Nombre alternativo de [link]" entre nav y hero. NO se usa si la entry es independiente (ej: Sión)
- **Homónimos automáticos**: el single template detecta entradas con igual `post_title` y renderiza bloque "Otras acepciones" con enlaces
- **Alias con entry propia**: crear entry con `_bc_loc_alias_of`, y agregar el nombre al `_bc_loc_alt_names` de la principal
  - Ejemplos existentes: Jebús (ID 2698, alias_of=537), Salem (ID 2699, alias_of=537), ambas con contenido narrativo completo y coordenadas
  - Sión (ID 2700) es **independiente** (sin `_bc_loc_alias_of`) por su complejidad doctrinal en la teología de la Restauración, sin coordenadas
- **Alias simples**: solo en `_bc_loc_alt_names`, sin entry aparte
- **Import masivo**: 445 posts recibieron alias del dataset gnosis-places.json (variantes inglesas)
- **Mapa EN→ES**: definido en `single-bc_location.php:369-391` como `$en_to_es`
- **Mapa interactivo (MapLibre GL)**: se inserta automáticamente entre el encabezado descriptivo y el primer `<h2>` si la ubicación tiene `_bc_loc_lat` y `_bc_loc_lng`. Renderizado por `bc_scripture_map_render_single($post_id)` en `bc-scripture-map.php`. Plugin enqueue los assets (Maplibre GL CSS + frontend.js) en `is_singular('bc_location')`. Altura: 400px, tiles satelitales, relieve 3D
- **Tipos**: city → Ciudad, region → Región, wilderness → Desierto, sea → Mar/Lago, river → Río, mountain → Montaña, settlement → Asentamiento, landmark → Lugar emblemático
- **Contenido se despliega** dentro de un `<div class="bc-location-content">` con fondo blanco y borde
- **Sidebar**: sticky en desktop, contiene tipo, confianza (coloreada: green/orange/red), fuente, fechas, coordenadas con link a Google Maps, y referencia de ejemplo
- **El contenido se almacena** en `post_content` como HTML apto para Gutenberg
- **Sobrescribir sin preguntar**: si la ubicación ya tiene `post_content`, reemplazarlo
- **No hay paginación en el archive**: todas las ubicaciones se cargan en una sola página
- **remove_accents()**: las ubicaciones con É se agrupan con E en el glosario
- **Alejandría**: MCP tools disponibles en este entorno (ver skill `alejandria-search` para detalles)
- **MSYS_NO_PATHCONV en Windows**: si Git Bash altera rutas, anteponer `MSYS_NO_PATHCONV=1`

## Casos especiales

### Ubicaciones con el mismo nombre que personas
Algunas ubicaciones comparten nombre con personajes bíblicos (ej: "Adam" es
tanto persona como lugar — "Adán" la persona, "Adam" la ciudad mencionada en
DyC 107:53-54). Verificar el tipo y las escrituras para distinguir.

**Regla:** si `_bc_loc_type` es `city`, `region`, `river`, etc., el contenido
debe centrarse **exclusivamente en la ubicación**. No incluir biografía del
personaje homónimo aunque comparta nombre. Si el personaje es relevante para
la historia del lugar (ej: David y Jerusalén), mencionarlo solo en función de
su relación con la ubicación. Las referencias escriturales de la Forma T deben
corresponder solo a pasajes que mencionen el lugar, no al personaje.

### Ubicaciones sin información
Si ninguna fuente tiene información sobre una ubicación (caso raro, pero
posible con ubicaciones "gnosis" de una sola mención), escribir un párrafo
corto:

> <Nombre> es un(a) [tipo] mencionado(a) en las Escrituras. Aparece en
> [referencia traducida]. No se dispone de información detallada sobre
> esta ubicación.

### Ubicaciones con nombre en inglés sin traducción conocida
Si el nombre inglés (`_bc_loc_name_en`) no tiene una forma española
establecida, usar el nombre del título (que ya está en español por el
skill `traducir-ubicaciones`).
