---
name: biografia-persona
description: |
  Escribir o revisar la biografía narrativa (post_content) del CPT bc_quote_author
  (Persona). Usar cuando se necesite: redactar la biografía de una persona desde las
  fuentes del corpus, revisar el tono y estructura de una biografía existente, o
  generar contenido nuevo para single-bc_quote_author. Proporciona la plantilla de
  secciones, principios de redacción, el pipeline completo de extracción → redacción
  → publicación, y un ejemplo completo.
---

# Skill: biografia-persona

Guía para redactar la biografía narrativa del CPT `bc_quote_author` (Persona). La
biografía se almacena en `post_content` (editor de WordPress activado) y se despliega
en la página de detalle individual debajo del card de presentación.

## Pipeline completo

### Fase 0: Detección de post existente (OBLIGATORIO — evitar duplicados)

**Siempre buscar primero. Esta fase es obligatoria antes de cualquier otra acción.**

```bash
docker exec wp_bc wp post list \
  --post_type=bc_quote_author \
  --s="Nombre Completo" \
  --fields=ID,post_title,post_name,post_status --allow-root
```

Reglas:
- Usar `--s="Nombre Exacto"` (no `grep`, no `--field=` con formato incorrecto)
- Si el post existe → usar ese ID. NO crear uno nuevo.
- Si NO existe → crear con `wp post create --post_type=bc_quote_author ...`
- Anotar el ID encontrado/creado y usarlo en TODAS las fases siguientes
- Verificar `post_content`: si está vacío es un stub y se puede sobrescribir

Razón: El slug de WordPress asigna `-2`, `-3`, etc. cuando se crea un duplicado.
Esto ya ocurrió con Gordon B. Hinckley (se usó 2614 en vez de 1203).
Un slug con `-2` es evidencia inequívoca de que se omitió la detección.

### Fase 1: Extraer y verificar fuentes del corpus

1. Identificar el slug de la persona (ej: `amasa-m.-lyman`, `martin-harris`)
2. Leer las fuentes en `corpus/personajes/<slug>/`:
   - `ldsorg.html` — prioridad 1 (historia oficial de la Iglesia)
   - `biographical-encyclopedia/vol*.txt` — prioridad 2 (Andrew Jenson)
   - `chd.html` — prioridad 3 (Church History Database — puede ser SPA sin texto embebido; verificar)
   - `wikipedia.html` — prioridad 4 (artículo completo de Wikipedia)
   - `wikidata.json` — datos estructurados básicos
3. Si existen otros archivos (`church-news/`, etc.) revisarlos también
4. **Consultar Alejandría** para enriquecer y verificar. Estrategia multi-fase (no una sola consulta):

    **Fase A — Broad discovery (sin source_filter):**
    ```
    alejandria_kg_find(query: "<Nombre Real>")
      → resuelve la entidad en el KG
    alejandria_search_text(query: "<Nombre Real>", limit: 10)
      → descubre qué documentos del corpus lo mencionan
    alejandria_search_semantic(query: "<Nombre Real> biography contribution")
      → encuentra fuentes semánticamente relacionadas (puede encontrar cosas que el FTS no ve)
    ```

    **Fase B — Narrow con source_filter (ejemplos, no lista cerrada):**
    Explorar subcorpus con `source_filter`. Los siguientes son ejemplos —
    no limitarse a ellos. Cualquier subcorpus que pueda tener información
    sobre la persona es válido: conference talks, manuals, magazines,
    church-history-topics, saints, scriptures, books, etc.
    ```
    alejandria_search_text(query: "<Nombre Real>",
      source_filter: "es/manuals/church-history-topics")
    alejandria_search_text(query: "<Nombre Real>",
      source_filter: "es/magazines")
    alejandria_search_text(query: "<Nombre Real>",
      source_filter: "es/manuals/saints")
    alejandria_search_text(query: "<Nombre Real>",
      source_filter: "es/general-conference")
    alejandria_search_text(query: "<Nombre Real>",
      source_filter: "en/general-conference")
    ```

    **Fase C — Síntesis y verificación:**
    ```
    alejandria_chat_ask(question: "¿Qué enseñó <Nombre> sobre...?")
      → contexto teológico. Si falla (error conocido del pipeline RAG), usar:
    alejandria_search_hybrid(query: "<Nombre> <tema clave>")
      → fallback para chat_ask
    ```

    **Fase D — Genealogía para infobox:**
    ```
    alejandria_kg_profile(entity_name: "<Nombre>")
      → resumen biográfico (puede ser metadata-level si KG está en enriquecimiento)
    alejandria_kg_relations(name: "<Nombre>",
      rel_types: ["FATHER_OF","MOTHER_OF","SPOUSE_OF"])
      → padres y cónyuges
    alejandria_kg_genealogy_tree(name: "<Nombre>", direction: "up", depth: 1)
      → ancestros directos
    alejandria_kg_genealogy_tree(name: "<Nombre>", direction: "down", depth: 1)
      → hijos
    ```

    Ver skill `alejandria-search` para detalles de cada tool.
5. **Verificar que las fuentes no sean defectuosas**:
   - **Wikipedia**: Si `wikipedia.html` es una página de desambiguación (solo lista de enlaces, sin infobox ni biografía), descargar el artículo correcto desde `https://en.wikipedia.org/wiki/<Nombre>_(Latter_Day_Saints)` y actualizar `corpus/personajes/<slug>/wikipedia.html`
   - **Wikidata**: Si `wikidata.json` describe una desambiguación (`"description": "Wikimedia disambiguation page"`), obtener el QID correcto desde el artículo de Wikipedia corregido (enlace "Wikidata item" en sidebar) y descargar `https://www.wikidata.org/wiki/Special:EntityData/<QID>.json`. Actualizar `corpus/personajes/<slug>/wikidata.json` y `corpus/personajes/<slug>/wikipedia-meta.json`
6. **Buscar fuentes faltantes en línea** cuando no existan en el corpus:
   - Joseph Smith Papers: `https://www.josephsmithpapers.org/person/<slug>` (prioridad máxima)
   - Encyclopedia of Mormonism (BYU): buscar en `corpus/eom/` para la persona, o descargar desde `https://eom.byu.edu`
   - Church News: buscar menciones en `corpus/church-news/`
   - LDS.org / ChurchofJesusChrist.org: buscar biografías oficiales
7. Extraer todo el texto narrativo de cada fuente (no resumir)
8. **Regla**: No conformarse con fuentes defectuosas. Si un archivo del corpus está incompleto, es una página de desambiguación, o contiene datos incorrectos, descargar la fuente correcta y actualizar el corpus. Esto aplica a TODAS las fuentes, no solo Wikipedia.

#### Integración con Alejandría — estrategia multi-consulta por fase

**Principios fundamentales**:
- Usar **múltiples tipos de búsqueda** en paralelo (text + semantic + hybrid). Cada tipo encuentra documentos distintos. No limitarse a uno solo.
- **Broad primero, narrow después**: buscar sin `source_filter` para descubrir, luego repetir con filtros en subcorpus prometedores.
- **Privilegiar sin restringir**: `source_filter` ayuda a enfocar, pero siempre hacer también búsquedas sin filter para no perder fuentes de otros subcorpus.
- **`chat_ask` con fallback**: si el pipeline RAG falla, recurrir a `search_hybrid`.

| Momento | Consultas MCP (lanzar varias en paralelo) | Propósito |
|---------|-------------------------------------------|-----------|
| Durante Fase 1 | `alejandria_kg_find(query: "Nombre Real")` | Resolver la entidad en el KG |
| Durante Fase 1 | `alejandria_kg_profile` + `kg_relations` + `kg_genealogy_tree` (up+depth=1, down+depth=1) | Perfil, familia, genealogía |
| Durante Fase 1 | `alejandria_search_text(query: "Nombre Real", limit: 10)` | Descubrir documentos en todo el corpus |
| Durante Fase 1 | `alejandria_search_semantic(query: "Nombre Real biography contribution")` | Fuentes semánticamente relacionadas |
| Durante Fase 1 | `alejandria_search_text(query: "Nombre Real", source_filter: "es/manuals/church-history-topics")` | Biografía estandarizada GA |
| Durante Fase 1 | `alejandria_search_text(query: "Nombre Real", source_filter: "es/magazines")` | Artículos de Liahona/Ensign |
| Durante Fase 1 | `alejandria_search_text(query: "Nombre Real", source_filter: "es/manuals/saints")` | Narrativa de Santos |
| Antes de Fase 2 | `alejandria_chat_ask(question: "¿Qué hizo/enseñó X?")` | Síntesis teológica (si falla: `search_hybrid`) |
| Durante Fase 2 | `alejandria_search_text(query: "frase célebre de la persona")` | Verificar citas textuales |
| Durante Fase 5 | repetir `kg_relations` + `kg_genealogy_tree` | Cruzar metadatos del infobox contra KG |

**Reglas de integración**:
- Alejandría es **complemento**, no reemplazo de las fuentes del corpus local
- Los datos del KG (padres, cónyuges) deben cotejarse con `wikidata.json` y `ldsorg.html` — si hay discrepancia, la fuente local tiene prioridad
- `chat_ask` puede fallar con `"cannot unpack non-iterable NoneType object"` (error interno del pipeline RAG); en ese caso recurrir a `search_hybrid` como alternativa
- `kg_profile` puede devolver solo `status: "metadata"` si el KG está en fase de enriquecimiento — es un dato válido, no un error. Usar fuentes locales para los datos faltantes
- Cuando `kg_relations` o `kg_genealogy_tree` devuelven vacío, es un dato válido (el KG no tiene esa información). No significa que el MCP falló. Usar fuentes locales (wikidata.json, Wikipedia, Jenson) para esos datos
- En la sección «Fuentes consultadas», NO escribir «Corpus de Alejandría» genérico. Cada cita o referencia obtenida vía Alejandría debe listar la fuente específica: título del libro, revista o conferencia (ej: «Liahona, abril de 2010» o «Doctrina y Convenios: Declaración Oficial 2»)

### Fase 2: Redactar la biografía

Seguir la **Plantilla de secciones** y los **Principios de redacción** abajo.

Reglas clave:
- La biografía debe ser un **producto nuevo**, no copia de ninguna fuente
- Priorizar fuentes en este orden: ldsorg → biographical-encyclopedia → chd → church-news → Wikipedia
- Sintetizar, no concatenar: fusionar la información en un solo relato cronológico
- Para personajes con desafíos doctrinales o disciplinares (como Amasa Lyman), mantener tono favorable a la Iglesia sin ser tendencioso: reconocer el error doctrinal, mostrar el proceso de disciplina, y señalar la restauración final si aplica
- El resumen rápido de un párrafo va en `post_excerpt`

### Fase 3: Publicar en el CPT

Usar los scripts de `scripts/` — encapsulan los workarounds de Docker/Git Bash/WP-CLI:

```bash
# 1. Guardar la biografía en HTML a un archivo en wp-content/uploads/ (volumen montado)
#    (El archivo desde el host es accesible como /var/www/html/wp-content/uploads/ en el contenedor)

# 3. Publicar biografía, excerpt y abrir comentarios
scripts/publish-bio.sh <ID> /var/www/html/wp-content/uploads/biografia.html "Resumen rápido"

# 4. Verificar integridad post-publicación
scripts/verify-bio.sh <ID>

# 5. Asignar foto desde Wikidata
scripts/import-photo.sh <ID> <QID> "Nombre"
```

#### Si el post NO existe, crearlo:
```bash
docker exec wp_bc wp post create \
  --post_type=bc_quote_author \
  --post_title="Nombre Completo" \
  --post_status=publish \
  --post_excerpt="<resumen>" \
  --comment_status=open --allow-root
# Luego publish-bio.sh para el contenido (más confiable que --post_content inline)
scripts/publish-bio.sh <ID_devuelto> /var/www/html/wp-content/uploads/biografia.html
```

### Fase 4: Verificación post-publicación (obligatorio)

```bash
scripts/verify-bio.sh <ID>
```

El script reporta cada check y falla con exit code 1 si algo está mal. Verifica:
- `post_content` > 500 caracteres (~80+ palabras, no corrupto)
- `_author_father`, `_author_mother`, `_author_birth_date`, `_author_death_date` poblados
- `_thumbnail_id` existe (foto asignada)
- `comment_status` es `open`

### Fase 5: Poblar metadatos del infobox (completar TODOS los campos aplicables)

**Foto/thumbnail** via script desde Wikidata QID (sin calcular hash MD5 a mano):
```bash
scripts/import-photo.sh <ID> <QID> "Nombre"
```

**Campos de metadatos** — verificar cada uno y poblar lo que falte. El infobox se alimenta de estos meta_keys:
```bash
docker exec wp_bc wp post meta update <ID> _author_description "Cargo o descripción breve" --allow-root
docker exec wp_bc wp post meta update <ID> _author_is_ga 1                              --allow-root  # solo si es Autoridad General
docker exec wp_bc wp post meta update <ID> _author_birth_date "18 de mayo de 1783"       --allow-root
docker exec wp_bc wp post meta update <ID> _author_birth_place "Easton, Nueva York"      --allow-root
docker exec wp_bc wp post meta update <ID> _author_death_date "10 de julio de 1875"      --allow-root
docker exec wp_bc wp post meta update <ID> _author_death_place "Clarkston, Utah"         --allow-root
docker exec wp_bc wp post meta update <ID> _author_nationality "Estadounidense"          --allow-root
docker exec wp_bc wp post meta update <ID> _author_father "Nathan Harris"                --allow-root  # SIEMPRE verificar
docker exec wp_bc wp post meta update <ID> _author_mother "Rhoda Lapham"                  --allow-root  # SIEMPRE verificar
docker exec wp_bc wp post meta update <ID> _author_witness_type "three-witnesses"        --allow-root  # three-witnesses | eight-witnesses
# _author_spouses requiere JSON array
docker exec wp_bc wp post meta update <ID> _author_spouses '[{"name":"Lucy Harris","marriage_year":1808,"end_year":1836}]' --allow-root
# _author_callings requiere JSON array — usar slug "testigo" para Witnesses no-GA
docker exec wp_bc wp post meta update <ID> _author_callings '[{"calling":"testigo","org":"Testigo del Libro de Mormón"}]' --allow-root
```
**Importante**: 
- `_author_father` y `_author_mother` se despliegan en el infobox — **siempre** verificar.
- `_author_is_ga` debe ser `1` SOLO si la persona fue Autoridad General.
- `_author_callings` slug debe coincidir con un término registrado. Los Testigos no-GA usan `"testigo"`, NO `"apostol"`.
- La foto se asigna via `import-photo.sh` (o `wp media import --featured_image`).
- Después de poblar los metadatos, verificar con `wp post meta list <ID>` y `scripts/verify-bio.sh <ID>`.

#### Estructura de la página (orden en single-bc_quote_author.php):
```
back-nav
share bar (top)         — "Comparte esta biografía"
card (foto + nombre + desc)
share bar (top)         — segunda instancia (entre card y biografía) — NOTA: actualmente hay SOLO UNA share bar arriba de la card
summary (excerpt)       — Lora italic, con borde
biography body          — post_content
share bar (bottom)      — centrado
comments                — condicional: comments_open() || get_comments_number()
```

**Correcto**: La share bar superior va ANTES de la card (back nav → share bar → card).  
**No mover**: No poner la share bar entre card y biografía — eso fue un error revertido.

## Plantilla de secciones

La biografía usa **títulos de sección claros y entendibles** (no metafóricos). El
cuerpo es **prosa narrativa continua** —no listas, no tablas, no datos sueltos.
Las listas solo se permiten en "Fuentes consultadas".

### Apertura (sin subtítulo)

1–2 párrafos que atrapan al lector. Declaran quién fue esta persona en esencia,
con tono inspirado y casi de relato. No es un resumen de datos —es una declaración
de identidad y significado. Debe poder leerse en voz alta.

**Evitar petrogrulladas**. «Hubo un hombre que...», «Nació en un humilde hogar...»,
«Su nombre quedó grabado...» son frases hechas que se repiten entre biografías.
Preferir aperturas directas con el nombre de la persona y un gancho sustantivo
(ej: «Siendo pequeño y de salud frágil, Spencer W. Kimball llegó a presidir...»).
Leer la apertura en voz alta para detectar lugares comunes.

### Primeros años y conversión

Nacimiento, padres, entorno familiar, infancia, educación, conversión (si no nació
en la Iglesia). El énfasis está en cómo estas experiencias moldearon a la persona.
Incluir desafíos tempranos, pérdidas familiares, momentos de búsqueda espiritual.
Tono: íntimo, mostrando la mano del Señor en los detalles formativos.

### Familia

Matrimonio(s), hijos. Contado como historia de amor y sacrificio, no como lista.
Cómo la familia fue pilar de su ministerio. Pérdidas familiares si las hubo.
Tono: conmovedor, humano, que muestre el costado personal.

### Misiones y servicio eclesiástico

El corazón de la biografía. Servicio en la Iglesia como progresión de fidelidad:
llamamientos humildes hasta los más altos. Incluir misiones, presidencias,
llamamientos de Autoridad General. Hechos notables, decisiones difíciles, momentos
de prueba y de fe. Tono: edificante, inspirador. Cada llamamiento es oportunidad
de servicio, no un escalón.

### Obra y contribuciones

¿Qué hizo que esta persona fuera única? ¿Qué cambió gracias a ella? Escritos,
discursos, templos, programas, iniciativas. Tono: testimonial, con peso histórico
y espiritual. Mostrar el legado como algo vivo.

### Pruebas y desafíos

Persecuciones, exilios, pérdidas, dificultades económicas, oposición. Una biografía
real muestra las pruebas —eso es lo que la hace edificante. Tono: honesto, sin
amargura, mostrando la fidelidad en la adversidad. Incluir también herejías o
errores doctrinales si los hubo (ej: Amasa Lyman negó la expiación, fue despojado
del apostolado y excomulgado). Narración objetiva, reconociendo el error, mostrando
el proceso de disciplina y la restauración final si aplica.

### Sus últimos días

Muerte y partida como culminación de una vida de convenio, no como hecho clínico.
Paz al final, fidelidad hasta el fin. Tono: solemne pero esperanzador.

### Fuentes consultadas

**Única sección que permite lista de balas.** Atribución breve de las fuentes
utilizadas para la síntesis. Sin enlaces largos ni citas académicas.

**No poner «Corpus de Alejandría» genérico**. Si se usó Alejandría para encontrar
una cita, listar la fuente específica que Alejandría devolvió: título de la revista,
del libro, de la conferencia, o la referencia canónica (ej: «Liahona, mayo de 2002»
en lugar de «Corpus de Alejandría: revistas de la Iglesia»).

## Principios de redacción

| Principio | Aplicación |
|-----------|------------|
| **Narrativo puro** | Prosa continua, sin viñetas, sin tablas, sin datos sueltos en el cuerpo |
| **Edificante pero no grandilocuente** | Tono espiritual, favorable a la Iglesia, sin ser pomposo. Evitar frases como "su nombre sigue resonando" o interpretaciones subjetivas. Preferir lenguaje directo y objetivo |
| **Sin fechas sueltas** | Las fechas van integradas en la oración: "Nacido en 1805 en Vermont..." |
| **Desafíos incluidos** | Mostrar pruebas y persecuciones con objetividad, sin dramatizar |
| **Errores doctrinales tratados con honestidad** | Si la persona enseñó doctrina falsa o fue disciplinada, narra el hecho con objetividad, reconociendo el error y el proceso correctivo. No endulzar ni ocultar |
| **Equilibrado y respetuoso** | Nunca peyorativo hacia otras creencias. Favorecer a la Iglesia sin ser tendencioso |
| **Evitar controversias innecesarias** | No incluir referencias, nombres o teorías que puedan levantar controversia sin aportar valor edificante a la narrativa. Una cosa es narrar hechos históricos validados y otra es introducir controversias especulativas que distraen del propósito de la biografía. Ejemplo: evitar mencionar a Ethan Smith o View of the Hebrews en biografías de los primeros Santos, pues implica teorías no oficiales sobre el origen del Libro de Mormón. Preguntarse siempre: ¿esto edifica al lector o lo distrae con polémicas? |
| **Sin copy-paste** | Ningún párrafo debe ser idéntico al de una fuente. Redacción original, voz propia |
| **Citas trazables** | Toda cita textual debe: (1) tener citación explícita verificable (ej: «texto» (History of the Church, 3:232).), (2) corresponder a la fuente original, (3) ser leal al original (traducir con precisión: "mean" ≠ "vile"), (4) ir en español. Si no se puede verificar, mejor parafrasear |
| **Citas despectivas: omitir si no edifican** | Si una cita es despectiva, denigrante o negativa sin aportar contexto edificante a la narrativa, omitirla. Exigencia adicional: la cita debe tener citación verificable. Las citas parentales de José Smith («too mean to mention») NO deben incluirse si son despectivas y no aportan contexto. La cita original dice "too mean" NO "too vile" — verificar siempre la fuente original antes de traducir |
| **Prioridad de fuentes** | Joseph Smith Papers → ldsorg → biographical-encyclopedia (Jenson) → BYU RSC → CHD → church-news → **Alejandría (KG + corpus)** → Dialogue/BYU Studies → Utah History Encyclopedia → Wikipedia/Wikidata (último recurso) |
| **Wikipedia no es fuente primaria** | Wikipedia y Wikidata solo deben usarse como complemento, no como fuente principal. Siempre consultar fuentes estables: Joseph Smith Papers, CHD, Jenson, RSC/BYU. Si esas no existen en el corpus, buscarlas en línea |
| **No conformarse con fuentes defectuosas** | Si `wikipedia.html` es una página de desambiguación, descargar el artículo correcto (`https://en.wikipedia.org/wiki/<Nombre>_(Latter_Day_Saints)` o similar). Si `wikidata.json` es de desambiguación, obtener el QID correcto. Si `chd.html` es un SPA sin texto, buscar fuente alternativa. **Nunca** usar una fuente incorrecta como si fuera válida |
| **Búsqueda exhaustiva** | Si una fuente prioritaria (Joseph Smith Papers, Jenson, EOM, CHD) no existe en el corpus, descargarla en línea. No limitarse a lo que ya está descargado. Usar websearch/webfetch para encontrar la fuente correcta |
| **Todas las fuentes disponibles** | Consultar tantas fuentes como existan en `corpus/personajes/<slug>/`. No limitarse a 2–3. Extraer el texto completo de cada una, no solo resúmenes |
| **Síntesis, no collage** | No concatenar fuentes. Fusionar en un solo relato cronológico |
| **Completar el infobox** | Después de publicar la biografía, poblar TODOS los metadatos del infobox (`_author_description`, `_author_birth_date`, `_author_birth_place`, `_author_death_date`, `_author_death_place`, `_author_nationality`, `_author_father`, `_author_mother`, `_author_spouses`, `_author_witness_type`, `_author_is_ga`) y asignar foto vía `wp media import --featured_image`. Verificar con `wp post meta list <ID>` |
| **Verificar GA correctamente** | No asumir que un Testigo del Libro de Mormón fue Autoridad General. De los Tres Testigos, solo Oliver Cowdery fue Autoridad General (Segundo Élder / Asistente Presidente). Martin Harris y David Whitmer NO fueron GA. `_author_is_ga` debe ser `1` SOLO si la persona realmente tuvo un llamamiento formal como Autoridad General. El slug "apostol" en `_author_callings` es incorrecto para quienes nunca fueron apóstoles — usar "otro" en su lugar |
| **Actualizar el corpus** | Siempre que se descargue una fuente correcta (porque la del corpus era incorrecta), actualizar el archivo en `corpus/personajes/<slug>/` para que las futuras sesiones tengan la fuente correcta |
| **Page title** | Usar "La biografía de [nombre]" via `bc_persona_biography_title()` (filtro `document_title_parts`) |

## Datos prácticos

- **Estadísticas de longitud**: reportar en **palabras**, no en caracteres. Usar `wc -w` en lugar de `wc -c`.
- **Helper functions** en `inc/persona.php`:
  - `bc_persona_biography_title( $post_id )` → retorna "La biografía de X"
  - `bc_render_persona_infobox()` → sidebar con datos estructurados
  - Filtro `document_title_parts` aplica automáticamente el título de biografía
- **Share bar** en `inc/share-bar.php`: texto cambia a "Comparte esta biografía" en singular
- **OG/Twitter tags** en `inc/og-tags.php`: detectan `bc_quote_author` y usan título de biografía
- **Schema ProfilePage** en `inc/schema.php`
- **Critical CSS**: `is_singular( 'bc_quote_author' )` en `inc/enqueue.php` inyecta CSS inline
- **CSS persona**: `src/_persona.scss` → compilar con `npm run build`
- **Comentarios**: `comment_status` debe ser `open` en el post. Verificar con `wp post get <ID> --field=comment_status`. Abrir con `wp post update <ID> --comment_status=open`
- **WP-CLI**: Disponible en DOS contenedores: `docker exec wp_bc wp ... --allow-root` (contenedor web principal) y `docker exec wp_bc_cli wp ...` (contenedor CLI dedicado). Ambos funcionan. Preferir `wp_bc` cuando sea más simple.
- **MSYS_NO_PATHCONV en Windows**: Git Bash convierte automáticamente rutas Unix (empezadas con `/`) a rutas Windows. Para comandos `docker exec` con rutas absolutas, anteponer `MSYS_NO_PATHCONV=1`. Ej: `MSYS_NO_PATHCONV=1 docker exec wp_bc wp post meta list <ID> --allow-root`.
- **Auto-links (negative lookahead)**: La función `bc_auto_link_glossary` en `inc/auto-links.php` puede matchear nombres parciales incorrectamente (ej: "José Smith" dentro de "José Smith padre"). La solución es agregar negative lookahead en el regex: `\b(preg_quote($name))\b(?!\s+(?:padre|hijo|sr\.?|jr\.?))` si el problema se repite con otros nombres.
- **Scripts de automatización** en `scripts/`:
  - `publish-bio.sh <ID> <archivo.html> [excerpt]` — publica contenido, excerpt, abre comentarios, verifica
  - `verify-bio.sh <ID>` — verifica integridad del post (checks: contenido, metadatos, foto, comentarios)
  - `import-photo.sh <ID> <QID> <nombre>` — descarga foto desde Wikidata QID y la asigna como featured image
- **Slug `testigo`**: Término de `bc_author_calling` para Testigos del Libro de Mormón que no son Autoridad General. Ya registrado en `bc-quote-block.php` y protegido por safety net en `seed-authors.php` y `migrate-classifications.php` (si un Witness no-GA tiene slug `apostol`, se fuerza automáticamente a `testigo`).
- **`authors.json` corregido**: David Whitmer y Martin Harris ya usan `"calling":"testigo"` en lugar de `"calling":"apostol"`.

## Nota histórica: Tres Testigos y retorno a la Iglesia

De los Tres Testigos del Libro de Mormón:
- **Oliver Cowdery**: Fue Autoridad General (Segundo Élder). Excomulgado en 1838, readmitido por bautismo en 1848. Falleció en la Iglesia.
- **Martin Harris**: Nunca fue GA. Excomulgado en 1837, readmitido en 1842, se separó del cuerpo principal, emigró a Utah en 1870 y fue readmitido en 1870. Falleció en la Iglesia.
- **David Whitmer**: Nunca fue GA. Excomulgado en 1838, nunca regresó a la Iglesia SUD. Fue el más entrevistado de los tres pero también el único que permaneció separado hasta su muerte. Nunca negó su testimonio del Libro de Mormón.

## Ejemplo completo

Ver `references/ejemplo-george-q-cannon.md` para una biografía completa que
sigue esta plantilla, usando a George Q. Cannon como caso demostrativo.
