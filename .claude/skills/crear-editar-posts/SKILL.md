---
name: crear-editar-posts
description: |
  Crear y editar artículos (post type: post) en WordPress para wp_bc.
  Usar SIEMPRE que se necesite: (1) crear un artículo nuevo desde cero,
  (2) editar el HTML/bloques de un artículo existente vía REST API,
  (3) corregir el formato de pasajes de Escrituras en bloque,
  (4) verificar que un artículo cumple las normas editoriales del proyecto.
  NO usar para biografías de personas (usar skill biografia-persona) ni
  para contenido de ubicaciones (usar skill glosario-ubicaciones-contenido).
---

# Crear / Editar Posts — wp_bc

## Investigación previa

**Antes de redactar o editar**, investigar a fondo el tema usando:

1. **Alejandría** — buscar en el corpus teológico bilingüe usando `alejandria_search_hybrid`, `alejandria_kg_find` y `alejandria_chat_ask` para encontrar pasajes, referencias cruzadas y contexto histórico
2. **Web** — buscar fuentes adicionales verificables (BibleHub, BYU RSC, Book of Mormon Central, FAIR, ChurchofJesusChrist.org, Wikipedia, etc.)
3. **Compilar bibliografía** — todas las fuentes consultadas formarán la sección **"Fuentes consultadas"** del post. Las fuentes externas deben quedar con su URL completa y enlazada.

## Workflow general

1. **Investigar** — ejecutar la investigación previa descrita arriba para reunir fuentes auténticas y verificables
2. **Leer el post actual** (si es edición) — usar `wordpress_wp_get_post` con `include_content=true` para obtener el HTML completo
3. **Redactar o editar** — todo el contenido debe basarse exclusivamente en las fuentes investigadas
4. **Identificar pasajes de Escrituras en bloque** — buscar `<blockquote class="wp-block-quote...">` que contengan citas bíblicas o de Escrituras SUD (DyC, Perla de Gran Precio, Libro de Mormón)
5. **Convertir a custom block** — reemplazar por `<blockquote class="wp-block-lds-passage-block-passage">` (ver formato exacto abajo)
6. **Considerar diagramas MerPress** — si el contenido se beneficia de un diagrama (flowchart, timeline, genealogía, jerarquía, etc.), insertar el bloque MerPress correspondiente (ver skill `merpress`)
7. **Verificar Church name policy** — aplicar las reglas completas de AGENTS.md:
    - Cada mención de la Iglesia debe incluir "Jesucristo"
    - **No usar "SUD", "mormón" ni "mormona" como adjetivo o gentilicio** en ningún contexto («doctrina SUD», «creencias mormonas», «autor SUD», etc.)
8. **Generar excerpt** — redactar un resumen de 1–2 oraciones que capture el propósito del artículo (se envía como parámetro `excerpt` en la API)
9. **Enviar vía API** — usar `wordpress_wp_update_post` con el contenido completo modificado y el excerpt
10. **Asignar taxonomías** — llamar a los skills correspondientes:
   - `asignar-tags` — para temas (`post_tag`)
   - `asignar-series` — para colecciones y series (`collection`)
   - `asignar-capitulos` — para capítulos de las Escrituras (`bc_chapter`)

## Integridad de las fuentes y citas

**Regla fundamental**: toda cita o mención que se use en el texto debe cumplir TODOS estos criterios:

- **Literal** — la cita debe ser textual, palabra por palabra del original en español. No parafrasear.
- **En español** — si la fuente original está en inglés, usar la traducción oficial al español de la Iglesia (disponible en Alejandría o ChurchofJesusChrist.org). Si no existe traducción oficial, traducir y notificarlo.
- **Validada** — verificada contra la fuente real. No inventar, inferir o completar citas.
- **Verificable** — toda cita debe poder confirmarse en la fuente citada. Incluir la referencia precisa (capítulo, versículo, página, o URL).
- **Procedente de la investigación** — toda cita debe haberse encontrado en la fase de investigación, no improvisada.

Citas de Escrituras en bloque: usar el custom block `wp-block-lds-passage-block-passage` (ver sección correspondiente).
Citas inline de Escrituras: mantenerlas dentro del párrafo con su referencia entre paréntesis, ej: «y aconteció que…» (1 Nefi 3:7).

## Sección Fuentes consultadas

Todo post debe incluir al final una sección `<h2>Fuentes consultadas</h2>` con una lista `<ul>` de las obras **no escriturarias** consultadas. Las referencias a pasajes de las Escrituras se gestionan mediante la taxonomía `bc_chapter` (ver skill `asignar-capitulos`) y no deben incluirse aquí.

Formato:

```html
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<ul class="wp-block-list">
<li><a href="URL" target="_blank" rel="noopener noreferrer">TÍTULO <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (Referencia FCD)</li>
...
</ul>
```

- Cada fuente externa debe tener su URL completa enlazada con `target="_blank"` y el icono de external-link
- **NO incluir escrituras canónicas** (Éxodo, Hechos, Alma, DyC, etc.) — esas referencias van en `bc_chapter`
- Incluir solo obras externas: discursos de conferencia, libros, artículos, manuales, sitios web, materiales de estudio, etc.
- Las fuentes consultadas vía Alejandría se desglosan por obra específica (conferencia, manual, artículo, etc.) usando formato FCD — **no citar "Alejandría" ni "corpus"** (ver skill `aplicar-citaciones`)
- No incluir fuentes no consultadas realmente

## Formato de pasajes de Escrituras en bloque

**Regla**: Los pasajes de las Escrituras que se citen en bloque deben usar el **bloque dinámico self-closing** `lds-passage-block/passage`, no `core/quote` ni HTML manual suelto.

### Formato correcto (bloque self-closing con atributos JSON)

```
<!-- wp:lds-passage-block/passage {"volume":"bom","book":"3-nefi","chapter":11,"startVerse":31,"endVerse":35} /-->
```

- El bloque se renderiza automáticamente con los datos de Escrituras desde el plugin `lds-passage-block`
- No necesita HTML interno entre apertura y cierre: es un bloque self-closing (termina en `/>`)
- Se coloca como un bloque independiente entre los bloques de párrafo/heading que lo rodean

### Volúmenes disponibles

| Volumen | Slug | Ejemplo |
|:--------|:-----|:--------|
| Antiguo Testamento | `ot` | `"volume":"ot","book":"genesis","chapter":19,"startVerse":1,"endVerse":11"` |
| Nuevo Testamento | `nt` | `"volume":"nt","book":"juan","chapter":10,"startVerse":16` |
| Libro de Mormón | `bom` | `"volume":"bom","book":"moroni","chapter":10,"startVerse":3,"endVerse":5` |
| Doctrina y Convenios | `dc` | `"volume":"dc","book":"secciones","chapter":110,"startVerse":11` |
| Perla de Gran Precio | `pgp` | `"volume":"pgp","book":"abraham","chapter":2,"startVerse":9,"endVerse":11` |

**Excepción — JST**: Si el pasaje usa la Traducción de José Smith y el texto difiere del canon estándar (no está disponible en los datos del plugin), usar `core/quote` con el texto JST manual.

**Prohibido**: NO usar `wp-block-quote` (core/quote) para Escrituras canónicas. `wp-block-quote` se reserva para citas de autores modernos o líderes de la Iglesia.

### Antes vs. Después

| Incorrecto | Correcto |
|:-----------|:---------|
| `<blockquote class="wp-block-quote wp-block-lds-passage-block-passage ...">` | `<!-- wp:lds-passage-block/passage {"volume":"...","book":"...",...} /-->` |
| HTML manual con `.verse-line` y `<cite>` | Bloque self-closing con atributos JSON |
| `core/quote` con clase añadida | Bloque dinámico que renderiza desde los datos |

### Construcción de bloques desde cero

```php
$parts[] = '<!-- wp:paragraph --><p>Texto introductorio al pasaje:</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:lds-passage-block/passage {"volume":"bom","book":"3-nefi","chapter":11,"startVerse":31,"endVerse":35} /-->';
$parts[] = '<!-- wp:paragraph --><p>Texto después del pasaje...</p><!-- /wp:paragraph -->';
```

## Nombre de la Iglesia

Reglas completas en AGENTS.md > "Nombre de la Iglesia". Resumen:

- **Primera mención**: "La Iglesia de Jesucristo de los Santos de los Últimos Días"
- **Abreviado**: "la Iglesia de Jesucristo" o "la Iglesia"
- **NO**: "la Iglesia SUD", "la Iglesia Mormona", "la Iglesia Restaurada" (sin Cristo)

## Tabla de referencias escriturarias (bc-forma-t)

**Regla**: La tabla de referencias al final del artículo debe usar HTML `<table class="bc-forma-t">`, NO el shortcode `[bc-forma-t]` con sintaxis markdown. El shortcode no se renderiza correctamente vía REST API y queda como texto plano.

**Título correcto**: `<h2>Referencias de las Escrituras</h2>` (no `bc-forma-t`)

Formato correcto:

```html
<h2>Referencias de las Escrituras</h2>
<table class="bc-forma-t">
<thead>
<tr>
<th>Referencia</th>
<th>Tema</th>
</tr>
</thead>
<tbody>
<tr>
<td>1 Nefi 3:7</td>
<td>Descripción del tema</td>
</tr>
</tbody>
</table>
```

- Columnas: `Referencia` | `Tema` (en ese orden)
- No incluir Escrituras que ya están en `bc_chapter` como referencias — la tabla es para temas específicos dentro de esos capítulos
- Si el artículo no referencia pasajes específicos, omitir esta sección

## API WordPress

- Site ID: `default` (URL: `http://localhost:8080`)
- Leer: `wordpress_wp_get_post` con `include_content=true`
- Editar: `wordpress_wp_update_post` — enviar el HTML **completo** del content
- La API corre `wpautop` automáticamente; puede añadir `<p>` alrededor de `<cite>` — es aceptable

## Skills complementarios

| Skill | Cuándo usarlo |
|:------|:--------------|
| `asignar-tags` | Después de crear/editar, para asignar `post_tag` consistentes |
| `asignar-series` | Para organizar el post en una colección y serie |
| `asignar-capitulos` | Para enlazar el post con términos `bc_chapter` de Escrituras |
| `merpress` | Para crear o corregir diagramas Mermaid.js en el post |
| `gutenbergize` | Si el post no usa bloques Gutenberg (`has_blocks()` = false) |
| `aplicar-citaciones` | Para dar formato FCD a la sección "Fuentes consultadas" |
| `normativa` | Para consultar cualquier regla del proyecto antes de actuar |
