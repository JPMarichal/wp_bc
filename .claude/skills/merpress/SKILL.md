---
name: merpress
description: Crear y manipular diagramas Mermaid.js usando el plugin MerPress en WordPress, dentro del tema wp_bc. Usar SIEMPRE que se necesite: (1) crear un diagrama nuevo en un artículo, (2) reemplazar diagramas ASCII existentes por Mermaid, (3) corregir errores de sintaxis Mermaid, o (4) diagnosticar por qué un diagrama no se renderiza en editor o frontend.
---

# MerPress para wp_bc

## Regla fundamental: Gutenberg blocks desde el inicio

Cada elemento del artículo debe ser su propio bloque Gutenberg. NO usar HTML clásico suelto. Artículos creados como classic block causan que los bloques MerPress no se rendericen en la UI.

### Cómo crear un artículo correcto

1. Usar `wp_insert_post()` + `wp_update_post()` con `post_content` que contenga marcadores de bloque (`<!-- wp:paragraph -->`, `<!-- wp:heading -->`, etc.)
2. Cada párrafo, heading, imagen, lista, cita y diagrama va envuelto en su propio `<!-- wp:block-name -->...<!-- /wp:block-name -->`
3. NO usar `$wpdb->update()` para crear — solo para corregir contenido existente. Usar `wp_update_post()` para artículos nuevos.
4. Verificar con `has_blocks($post->post_content)` que devuelva `true` después de la creación

### Estructura de bloques comunes

```
<!-- wp:paragraph --><p>Texto del párrafo.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Título</h2><!-- /wp:heading -->
<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Subtítulo</h3><!-- /wp:heading -->
<!-- wp:image --><figure class="wp-block-image"><img src="..." alt="..."/></figure><!-- /wp:image -->
<!-- wp:quote --><blockquote class="wp-block-quote"><p>Cita</p></blockquote><!-- /wp:quote -->
<!-- wp:list --><ol class="wp-block-list"><li>Item</li></ol><!-- /wp:list -->
```

## Bloque MerPress: sintaxis

### Formato correcto del bloque

```
<!-- wp:merpress/mermaidjs -->
<div class="wp-block-merpress-mermaidjs diagram-source-mermaid"><pre class="mermaid">MERMAID_CODE</pre></div>
<!-- /wp:merpress/mermaidjs -->
```

Los comentarios HTML `<!-- wp:... -->` son CRÍTICOS. Sin ellos el bloque no es reconocido por Gutenberg.

### Reglas de sintaxis Mermaid 11.9.0 para wp_bc

1. **Cada declaración en su propia línea** con `\n`. NO poner todo en una línea.
2. **Sin indentación** al inicio de línea. El primer carácter después de `\n` debe ser el ID del nodo, no un espacio.
3. **Evitar paréntesis `()`** en etiquetas de nodo. Usar `A[Texto sin parentesis]` no `A["Texto (con parentesis)"]`.
4. **Evitar comillas dobles `"`** alrededor de etiquetas y labels. Usar `A[Texto]` no `A["Texto"]`.
5. **Evitar caracteres especiales**: emojis, `&lt;br/&gt;`, `&amp;`, etc. dentro del contenido Mermaid.
6. **Evitar palabras clave reservadas** como `graph`, `call`, `end` como IDs de nodo.

### Formato que funciona

```
graph LR
A[Placa Africana Sinai] -->|N-S| B[Fosa del Mar Muerto]
B -->|S-N| C[Placa Arabiga]
```

- `graph` (o `flowchart`) + orientación (`LR`, `TB`, `RL`, `BT`)
- Nodos con `ID[Etiqueta]` para rectángulo
- Flechas con `-->` (sin etiqueta) o `-->|etiqueta|` (con etiqueta)
- Sin espacios de indentación después del `\n`

## Tipos de diagrama disponibles

### Flowchart (graph) — el más usado

```
graph TD
A[Inicio] --> B{Decision}
B -->|Si| C[Accion]
B -->|No| D[Alternativa]
```

### Sequence diagram

```
sequenceDiagram
A->>B: Mensaje
B-->>A: Respuesta
```

### Otros disponibles en MerPress
- `classDiagram` — diagramas de clases UML
- `stateDiagram-v2` — diagramas de estado
- `gantt` — diagramas de Gantt
- `pie` — gráficos de pastel

## Diagnóstico de errores

| Síntoma | Causa probable | Solución |
|---------|---------------|----------|
| "Syntax error" en editor | Paréntesis, comillas, o caracteres especiales | Simplificar etiquetas, quitar paréntesis |
| Se ve en editor, no en UI | blockName incorrecto (`core/html` en vez de `merpress/mermaidjs`) | Revisar con `parse_blocks()`, corregir blockName |
| No se ve ni en editor ni UI | Faltan comentarios de bloque `<!-- wp:... -->` | Reconstruir con `serialize_blocks()` |
| Artículo es un solo bloque classic | Se usó HTML suelto sin marcadores de bloque | Ejecutar "Convert to Blocks" o reescribir con bloques |

## Pipeline de creación

1. Redactar `post_content` con marcadores de bloque Gutenberg
2. Usar `wp_insert_post()` para crear
3. Verificar con `has_blocks()` y `parse_blocks()`
4. No tocar con `$wpdb->update()` a menos que sea estrictamente necesario (solo para corregir contenido legacy)
