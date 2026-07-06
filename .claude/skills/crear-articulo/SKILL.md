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

### 1. Intro sin título
El primer bloque del artículo debe ser un `core/paragraph` con el párrafo de introducción. No debe llevar un heading antes. La intro captura el tema y engancha al lector.

### 2. TOC generado automáticamente
El tema ya incluye un TOC automático (vía `inc/toc.php`) que se inyecta antes del primer `<h2>` al vuelo mediante el filtro `the_content`. **No incluir un TOC manual.** El TOC se genera a partir de los títulos h2 usando `sanitize_title()` para los anclajes. No es necesario agregar `anchor` ni `id` a los headings.

### 3. Pasajes de escritura
Cuando un pasaje de las Escrituras se cite **en su propio bloque** (outline, no inline dentro de un párrafo), usar el bloque `lds-passage-block/passage` en lugar de `core/quote`.

Formato del bloque (self-closing, dinámico):
```
<!-- wp:lds-passage-block/passage {"volume":"ot","book":"genesis","chapter":19,"startVerse":31,"endVerse":32} /-->
```

**Volúmenes disponibles:** `ot` (Antiguo Testamento), `nt` (Nuevo Testamento), `bom` (Libro de Mormón), `dc` (Doctrina y Convenios), `pgp` (Perla de Gran Precio).

**Excepción:** Si el pasaje usa la Traducción de José Smith (JST) y el texto difiere del canon estándar, mantener `core/quote` con el texto JST.

### 4. Títulos sin numeración automática
No numerar los títulos por defecto (ej. "1. La gracia..." no, solo "La gracia..."). La numeración solo se justifica si hay una secuencia explícita nombrada en el texto (ej. un listado de pasos o principios numerados dentro del contenido mismo).

### 5. Fuentes consultadas
Si el artículo cita o se basa en fuentes, incluir una sección final titulada "Fuentes consultadas" con un listado. Usar `core/list`.

Formato de cada cita: **Autor, "Título", Editorial o detalle, Año.** Usar comillas dobles alrededor del título, no itálicas. Las citas inline en el texto también deben seguir este formato entre paréntesis, ej: (Talmage, "A Study of the Articles of Faith", 1899).

```
<!-- wp:heading -->
<h2 class="wp-block-heading">Fuentes consultadas</h2>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Talmage, James E., "A Study of the Articles of Faith", 1899.</li>
<li>Kimball, Spencer W., "The Miracle of Forgiveness", Bookcraft, 1969.</li>
</ul>
<!-- /wp:list -->
```

### 6. Bloques permitidos
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

### 7. Persistencia del contenido
Tanto para crear como para actualizar artículos, usar `$wpdb->insert()` o `$wpdb->update()` en lugar de `wp_insert_post()` / `wp_update_post()`, ya que estos últimos eliminan los comentarios HTML (marcadores de bloque Gutenberg) mediante el filtro kses.

## Pipeline de creación

1. Definir título y ángulo del artículo
2. Escribir contenido con la estructura arriba indicada
3. Preparar bloques como bloques Gutenberg (con marcadores `<!-- wp:... -->`)
4. Insertar/actualizar via `$wpdb` para preservar marcadores
5. Verificar con `has_blocks()` y `parse_blocks()` que no haya bloques classic no intencionales
6. Verificar visualmente en el editor de WordPress
