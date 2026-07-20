# Skill: Crear Artículo

## Cuándo usar
Cuando se necesite crear un artículo nuevo (no una biografía de Persona) desde cero en el sitio wp_bc, o cuando se necesite reestructurar un artículo existente para alinearlo a este formato.

## Estructura obligatoria del artículo

```
1. Párrafo introductorio (sin título, un solo párrafo)
2. Secciones con h2 (el TOC se genera automáticamente vía el tema)
3. (opcional) Fuentes consultadas al final
```

## Reglas

### 0. Principio de responsabilidad monotemática — OBLIGATORIO
Cada artículo cubre un solo tema. Cuando la información sobre un aspecto excede la capacidad del artículo actual (por ejemplo, porque merece su propio análisis, fuentes y estructura), debe planearse un artículo nuevo siguiendo el pipeline completo de este skill en lugar de inflar el artículo existente.

#### Cómo detectar que un tema merece artículo propio
- El aspecto ocupa más de 2–3 párrafos dentro del artículo actual
- Tiene sus propias fuentes y tradiciones interpretativas
- Puede formularse como una pregunta o tesis independiente (ej. "¿Sara, hermana o sobrina de Abraham?")

#### Qué hacer
1. Reducir el tratamiento en el artículo actual a 1–2 párrafos con un enlace al nuevo artículo
2. Crear el nuevo artículo siguiendo el pipeline completo de este skill
3. Documentar la decisión en la sesión actual para que quede registro

#### Ejemplo
- Artículo original: «¿Quién fue Lot?» — incluye un apartado sobre Sara
- Detección: el tema Sara/Harán tiene tradiciones textuales divergentes con fuentes propias
- Acción: se reduce a 2 párrafos con enlace, se crea artículo independiente «¿Sara, hermana o sobrina de Abraham?»

### 1. Intro sin título
El primer bloque del artículo debe ser un `core/paragraph` con el párrafo de introducción. No debe llevar un heading antes. La intro captura el tema y engancha al lector.

### 2. TOC generado automáticamente
El tema ya incluye un TOC automático (vía `inc/toc.php`) que se inyecta antes del primer `<h2>` al vuelo mediante el filtro `the_content`. **No incluir un TOC manual.** El TOC se genera a partir de los títulos h2 usando `sanitize_title()` para los anclajes. No es necesario agregar `anchor` ni `id` a los headings.

### 3. Verificación de fuentes — OBLIGATORIO
Toda cita textual, atribución escrituraria o referencia a una fuente debe ser verificada contra una fuente confiable antes de incluirse en el artículo. Las herramientas de IA generativa (incluyendo este asistente) pueden inventar citas, referencias y atribuciones que parecen verosímiles pero son falsas.

#### Pipeline de verificación
1. **Buscar primero en Alejandría** — usar `alejandria_search_text` (FTS) y `alejandria_search_semantic` para localizar la fuente en el corpus local.
2. **Si no está en Alejandría, buscar en web** — buscar en josephsmithpapers.org, churchofjesuschrist.org, archive.org, speeches.byu.edu, etc.
3. **JST**: Verificar siempre contra el Apéndice JST de la edición SUD o josephsmithpapers.org. No asumir que el JST tiene cambios en ningún pasaje. La mayoría de los capítulos no tienen ninguna revisión. Los cambios conocidos del JST en el AT cubren solo versículos específicos (ej. Gn 19:9-15, no 19:27-28).
4. **Si no se puede verificar, no incluir.** Es preferible texto original propio o una cita escrituraria verificable (RV1960) a una cita de autoridad no verificable.

#### Checklist de verificación para cada cita
- [ ] **Autor confirmado** — la persona realmente dijo/escribió lo que se le atribuye
- [ ] **Fuente exacta** — título del libro/discurso, año, página (si aplica) verificados
- [ ] **Texto textual** — las palabras atribuidas coinciden con la fuente
- [ ] **JST verificado** — si se atribuye al JST, confirmar que el cambio existe (contra Apéndice JST o josephsmithpapers.org)
- [ ] **Alejandría consultada** — se buscó primero en el corpus local

### 4. Pasajes de escritura
Cuando un pasaje de las Escrituras se cite **en su propio bloque** (outline, no inline dentro de un párrafo), usar el bloque `lds-passage-block/passage` en lugar de `core/quote`.

Formato del bloque (self-closing, dinámico):
```
<!-- wp:lds-passage-block/passage {"volume":"ot","book":"genesis","chapter":19,"startVerse":31,"endVerse":32} /-->
```

**Volúmenes disponibles:** `ot` (Antiguo Testamento), `nt` (Nuevo Testamento), `bom` (Libro de Mormón), `dc` (Doctrina y Convenios), `pgp` (Perla de Gran Precio).

**Excepción:** Si el pasaje usa la Traducción de José Smith (JST) y el texto difiere del canon estándar, mantener `core/quote` con el texto JST.

### 5. Títulos sin numeración automática
No numerar los títulos por defecto (ej. "1. La gracia..." no, solo "La gracia..."). La numeración solo se justifica si hay una secuencia explícita nombrada en el texto (ej. un listado de pasos o principios numerados dentro del contenido mismo).

### 6. Títulos de autoridades generales — OBLIGATORIO
Toda mención a una Autoridad General (Apóstol, Setenta, Presidencia) debe usar su título completo. Nunca referirse solo por el apellido.

| Incorrecto | Correcto |
|------------|----------|
| "Holland dijo..." | "el presidente Holland dijo..." |
| "Maxwell describió..." | "el élder Maxwell describió..." |
| "Jeffrey R. Holland, Lo mejor aún está por venir" | "presidente Jeffrey R. Holland, Lo mejor aún está por venir" |
| "Hinckley enseñó..." | "el presidente Gordon B. Hinckley enseñó..." |

- **Primera mención**: título completo + nombre completo + cargo ("el presidente Jeffrey R. Holland, del Cuórum de los Doce Apóstoles")
- **Menciones subsecuentes**: título abreviado + apellido ("el presidente Holland", "el élder Maxwell")
- **En fuentes consultadas**: incluir título ("presidente Jeffrey R. Holland")

### 7. Fuentes consultadas
Si el artículo cita o se basa en fuentes, incluir una sección final titulada "Fuentes consultadas" con un listado. Usar `core/list`.

#### Formato de citas — nombres de personas
**Regla absoluta: los nombres de personas siempre van con nombre + apellido, nunca apellido + nombre.**

| Incorrecto | Correcto |
|------------|----------|
| Muhlestein, Kerry; "Ruth, Redemption..." | Kerry Muhlestein, "Ruth, Redemption..." |
| Christofferson, D. Todd, "Redención"... | D. Todd Christofferson, "Redención"... |
| Holzapfel, Richard Neitzel y otros... | Richard Neitzel Holzapfel y otros... |

Esto aplica tanto en las citas inline dentro del texto como en el listado de "Fuentes consultadas". El formato es: **Nombre Apellido, "Título", en Publicación, editores, año.** Usar comillas dobles alrededor del título, no itálicas.

Ejemplos del formato correcto (tomados del manual Scripture Helps en Alejandría):
- Kerry Muhlestein, "Ruth, Redemption, Covenant, and Christ", en *The Gospel of Jesus Christ in the Old Testament*, editado por D. Kelly Ogden y otros, Deseret Book, 2009.
- D. Todd Christofferson, "Redención", *Liahona*, mayo de 2013.
- Richard Neitzel Holzapfel y otros, *Jehovah and the World of the Old Testament*, Deseret Book, 2009.
- Bible Dictionary, "Moab", "Chemosh".

Solo incluir fuentes que hayan pasado el checklist de verificación.

```
<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Nombre Apellido, "Título", en Publicación, editores, Año.</li>
</ul>
<!-- /wp:list -->
```

### 8. Enlaces a fuentes de internet — OBLIGATORIO
Toda fuente consultada que esté disponible en internet debe incluir un enlace funcional (`<a href="...">`) en la lista de "Fuentes consultadas". Los enlaces a sitios externos deben abrirse en nueva ventana/pestaña usando `target="_blank" rel="noopener noreferrer"`.

Los enlaces aseguran que el lector pueda:
- Verificar la fuente directamente
- Acceder al contexto completo del que se extrajo una cita
- Consultar la publicación original (especialmente importante para artículos académicos en RSC/BYU, BYU Studies, y churchofjesuschrist.org)

#### Donde NO aplicar enlaces
- Libros impresos sin versión digital (ej. Anchor Bible, JPS Commentary)
- Obras clásicas de dominio público sin edición digital estándar
- Referencias internas de canon (Biblia, Perla de Gran Precio) — el lector ya las tiene en su ejemplar

#### URLs conocidas de fuentes frecuentes en wp_bc
| Fuente | URL base |
|--------|----------|
| RSC/BYU | `https://rsc.byu.edu/` |
| BYU Studies | `https://byustudies.byu.edu/` |
| ChurchofJesusChrist (manuales) | `https://www.churchofjesuschrist.org/study/manual/` |
| ChurchofJesusChrist (GDN) | `https://www.churchofjesuschrist.org/study/scriptures/gs` |
| ChurchofJesusChrist (Bible Dictionary) | `https://www.churchofjesuschrist.org/study/scriptures/bd` |
| ChurchofJesusChrist (Gospel Library) | `https://www.churchofjesuschrist.org/study/` |

#### Formato correcto
```html
<li>John Gee, <a href="https://rsc.byu.edu/book/introduction-book-abraham" target="_blank" rel="noopener noreferrer"><em>An Introduction to the Book of Abraham</em></a>, RSC/BYU, 2017, p. 102.</li>
```

### 9. Fuentes públicas, no referencias internas
Toda fuente citada en el artículo debe ser una fuente pública verificable por el lector. **No citar archivos internos del proyecto** (rutas de plugin, nombres de bases de datos locales, archivos de configuración) como si fueran fuentes. En lugar de "según el archivo gnosis-places.json del plugin bc-scripture-map", citar las fuentes públicas originales que esos archivos consumen (por ejemplo, "OpenBible (openbible.co)" o "Theographic (theographic.com)").

#### Ejemplos

| Incorrecto | Correcto |
|------------|----------|
| "según gnosis-places.json del plugin bc-scripture-map" | "según las bases de datos OpenBible (openbible.co) y Theographic" |
| "como se documenta en nuestro archivo interno" | "como documenta la Encyclopedia of Mormonism" |
| "el plugin bc-scripture-map registra estas coordenadas" | "los sistemas de datos geográficos OpenBible y Theographic registran estas coordenadas" |

### 10. Bloques permitidos
Usar exclusivamente estos bloques:
- `core/paragraph` — párrafos
- `core/heading` — títulos (h2 para secciones, h3 para subsecciones)
- `core/quote` — citas textuales (no escriturarias) o JST
- `core/list` — listas (TOC, fuentes)
- `core/image` — imágenes destacadas
- `lds-passage-block/passage` — pasajes de escritura en bloque propio
- `bc/cita` — citas de autoridades SUD (si está disponible)
- `merpress/mermaidjs` — diagramas Mermaid (solo si aplica)

No usar HTML clásico suelto fuera de bloques. Cada elemento debe ser su propio bloque Gutenberg.

### 11. Persistencia del contenido
Tanto para crear como para actualizar artículos, usar `$wpdb->insert()` o `$wpdb->update()` en lugar de `wp_insert_post()` / `wp_update_post()`, ya que estos últimos eliminan los comentarios HTML (marcadores de bloque Gutenberg) mediante el filtro kses.

### 12. Taxonomías obligatorias — OBLIGATORIO
Todo artículo nuevo debe asignarse a las taxonomías disponibles del sitio. No dejar ninguna sin poblar.

| Taxonomía | Slug | Cardinalidad | Regla |
|-----------|------|-------------|-------|
| **Categoría** | `category` | 1 (fija) | Solo existe la categoría 1 ("Sin categoría"). Asignar siempre `[1]`. |
| **Temas** | `post_tag` | 2–6 | Temas específicos que abarca el artículo (nunca decir "tags", decir "temas") |
| **Capítulos** | `bc_chapter` | 1–10 | Capítulos escriturales referenciados |
| **Serie (colección)** | `collection` | 0–1 | Solo si el artículo pertenece a una **serie** (término HIJO), nunca a una colección (término padre). |

#### Asignación vía MCP de WordPress
Para categoría y temas, usar el MCP `wordpress_wp_update_post`:

```
# Categoría (siempre [1]) y Temas — vía update_post
wordpress_wp_update_post(id: <ID>, categories: [1], tags: [<tag_id1>, <tag_id2>])
```

Nota: el MCP no soporta taxonomías personalizadas (`bc_chapter`, `collection`). Esas requieren wp-cli.

#### Asignación vía wp-cli (usar SIEMPRE podman, NUNCA docker)
```bash
# Categoría (siempre la 1)
podman exec wp_bc_cli wp post term set <ID> category 1

# Temas (varios)
podman exec wp_bc_cli wp post term set <ID> post_tag "<tema1>" "<tema2>" "<tema3>"

# Capítulos (varios) — solo wp-cli
podman exec wp_bc_cli wp post term set <ID> bc_chapter "<capítulo1>" "<capítulo2>"

# Serie (solo HIJO de collection, nunca el padre)
podman exec wp_bc_cli wp post term set <ID> collection "<slug-de-la-serie>"
```

**Nota:** `wp post term set` reemplaza todos los términos de esa taxonomía. Para agregar sin reemplazar, usar `wp post term add` por cada uno. Preferir `set` para categoría (1 sola) y `add` para temas y capítulos cuando se concatenan.

**Cuidado con IDs numéricos en `collection`:** `wp post term add 3637 collection 3767` NO usa el ID, sino que CREA un nuevo término con nombre "3767". Usar siempre el slug textual (ej. `"el-origen-del-libro-de-mormon"`).

#### Asignación vía MySQL directo (alternativa)
Si se necesita asignación massiva o precisa:
```sql
INSERT INTO wp_term_relationships (object_id, term_taxonomy_id, term_order)
VALUES (<ID>, <term_taxonomy_id>, 0);
```

#### Nombres correctos de fuentes SUD
Usar los nombres oficiales verificados. Errores comunes:

| Incorrecto | Correcto |
|------------|----------|
| "Ayudas para el Estudio de las Escrituras" | "Guía para el Estudio de las Escrituras" (GEE) o "Ayudas para las Escrituras: Antiguo Testamento" según corresponda |
| "Guía de Estudio de las Escrituras" | "Guía para el Estudio de las Escrituras" (GEE) |

La GEE (churchofjesuschrist.org/study/scriptures/gs) y las Ayudas para las Escrituras (churchofjesuschrist.org/study/manual/scripture-helps-old-testament) son publicaciones distintas. Verificar siempre el título exacto contra la fuente oficial antes de incluirla.

#### Terminología de la interfaz
- `post_tag` aparece en el admin como **"Temas"**
- `bc_chapter` aparece en el admin como **"Capítulos"** (metabox personalizado)
- `collection` aparece en el admin como **"Colecciones y Series"**
- `category` aparece en el admin como **"Categorías"** (usar siempre la única: "Sin categoría")

### 14. Tabla de referencias escriturarias — OBLIGATORIO
Todo artículo que cite o aluda a 2 o más pasajes de las Escrituras debe incluir, inmediatamente después de la Conclusión y antes de «Fuentes consultadas», una sección titulada **«Referencias de las Escrituras»** con una tabla `<table class="bc-forma-t">` que correlacione cada concepto con su referencia escrituraria.

Formato correcto:
```html
<h2 class="wp-block-heading">Referencias de las Escrituras</h2>
<table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>
<tr><td>Descripción del concepto</td><td>Libro Capítulo:Versículo</td></tr>
<tr><td>Otro concepto</td><td>Libro Capítulo:Versículo</td></tr>
</tbody></table>
```

Reglas de la tabla:
- La primera columna (Concepto) describe el tema en 2–8 palabras.
- La segunda columna (Referencia) contiene la cita escrituraria abreviada (ej. «1 Nefi 8:2», «Alma 12:31», «Mosíah 1–3»).
- No incluir tablas con una sola fila. Si solo hay una referencia relevante, mencionarla inline en el texto.
- Si el artículo no tiene referencias escriturarias directas o solo tiene una, omitir esta sección.

### 13. Verificación de enlaces externos — OBLIGATORIO
Cada enlace externo incluido en las "Fuentes consultadas" (o en cualquier parte del artículo) **DEBE** ser verificado con una solicitud HTTP real ANTES de publicar el artículo. Los enlaces rotos (404, dominio caído, página movida) dañan la credibilidad del sitio y la experiencia del lector.

#### Pipeline de verificación
1. **Probar cada URL con `webfetch`** o con `curl -I` (HEAD request). Si la URL responde 200, es válida.
2. **Si la URL responde 404**, buscar la página correcta:
   - Usar `websearch` con `site:` para encontrar la página en el mismo dominio
   - Ejemplo: si `https://rsc.byu.edu/book-chapter/lehis-wilderness-journey` da 404, buscar `site:rsc.byu.edu Lehi wilderness journey`
3. **Actualizar la URL** en el HTML del artículo antes de publicar.
4. **Enlaces conocidos que cambian frecuentemente:**
   - RSC/BYU: las URLs de capítulos de libros usan el patrón `rsc.byu.edu/{book-slug}/{chapter-slug}`, NO `book-chapter/`
   - Church manuals: usar el patrón `churchofjesuschrist.org/study/manual/{manual-name}/chapter-{n}-{slug}?lang=es`
5. **No incluir enlaces no verificados.** Un enlace roto es peor que ninguna fuente.

1. Definir título y ángulo del artículo
2. Determinar qué taxonomías aplican (categoría, temas, capítulos, colección/serie)
3. Escribir contenido con la estructura arriba indicada
4. **Verificar cada cita y referencia contra fuentes confiables** (usar Alejandría primero)
5. **Verificar cada enlace externo con una solicitud HTTP real** (usar `webfetch` o `curl -I`). No publicar enlaces rotos (404).
6. Preparar bloques como bloques Gutenberg (con marcadores `<!-- wp:... -->`)
7. Insertar via podman con `wp post create` (artículos nuevos). Para actualizaciones, usar SQL directo:
   ```php
   global $wpdb;
   $wpdb->query($wpdb->prepare(
       "UPDATE {$wpdb->posts} SET post_content = %s WHERE ID = %d",
       $new_content,
       $post_id
   ));
   ```
   **No** usar `$wpdb->update()` porque colapsa los marcadores Gutenberg.
8. Asignar taxonomías vía `wp post term set/add`
9. Verificar con `has_blocks()` y `parse_blocks()` que no haya bloques classic no intencionales
10. **Verificar integridad de bloques después de ediciones** — después de modificar el contenido de un artículo existente, verificar que los bloques Gutenberg no se hayan colapsado:
    - Ejecutar `has_blocks()` — debe devolver `YES`
    - Si devuelve `NO`, el contenido se aplanó a classic. Solución: restaurar desde el archivo HTML original usando consulta SQL directa (`$wpdb->query()` con `$wpdb->prepare()`, no `$wpdb->update()`)
11. Verificar visualmente en el editor de WordPress que cada bloque se renderice individualmente
