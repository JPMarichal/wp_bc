# Correcciones acumuladas

## 2026-07-21: "SUD" y "mormón" como adjetivo

**Problema**: En el post 3701 se usó "doctrina SUD" como adjetivo, violando la norma de Church name policy.

**Regla**: "SUD", "mormón" y "mormona" **no deben usarse como adjetivos o gentilicios** en ningún contexto. Esto incluye (pero no se limita a):
- «doctrina SUD» → incorrecto
- «creencias mormonas» → incorrecto
- «autor SUD» → incorrecto
- «historia mormona» → incorrecto
- «miembros SUD» → incorrecto

**Alternativas correctas**:
- «doctrina de la Iglesia de Jesucristo» o «doctrina de la Restauración»
- «miembros de la Iglesia de Jesucristo»
- «historia de la Restauración»

**Acción**: Corregido en post 3701 (heading y excerpt) y añadida regla explícita a AGENTS.md y skill `crear-editar-posts`.

---

## 2026-07-22: Artículos sin intro, Escrituras en bloque incorrecto, contenido classic

**Problema**: Los artículos #13–#16 de la Serie 4 "Doctrinas clave del Libro de Mormón" (IDs 3735, 3738, 3739, 3742) tenían tres errores:
1. **Sin intro**: #13, #14, #16 empezaban directamente con `<h2>` sin párrafo introductorio.
2. **Scripture blocks en formato incorrecto**: #15 y #16 usaban `wp-block-quote` con clase adicional `wp-block-lds-passage-block-passage` (mezcla inválida) en lugar del bloque self-closing dinámico `lds-passage-block/passage`. #13 usaba HTML clásico suelto.
3. **Contenido classic**: #13 y #14 tenían `has_blocks() = false` (completamente classic). #15 y #16 tenían bloques classic mezclados con Gutenberg.

**Reglas derivadas**:

### 1. Intro obligatoria
Todo artículo DEBE comenzar con un `core/paragraph` (párrafo introductorio) ANTES del primer `core/heading`. No hay excepciones. La intro debe resumir el tema y enganchar al lector.

### 2. Scripture blocks: solo bloque self-closing dinámico
Los pasajes de Escrituras en bloque deben usar exclusivamente:
```
<!-- wp:lds-passage-block/passage {"volume":"...","book":"...","chapter":N,"startVerse":N,"endVerse":N} /-->
```
- ❌ No usar `core/quote` (`wp-block-quote`) para Escrituras — está reservado para citas de autores modernos
- ❌ No usar HTML manual dentro de `<blockquote class="wp-block-lds-passage-block-passage">` — el bloque dinámico renderiza desde los datos
- ❌ No mezclar clases (`wp-block-quote wp-block-lds-passage-block-passage`) — es una combinación inválida

### 3. Gutenberg desde la creación
- Todo contenido debe construirse con marcadores `<!-- wp:... -->` desde el inicio
- Verificar con `has_blocks()` y `parse_blocks()` — cero bloques classic no intencionales
- Guardar siempre con `$wpdb->update()`, nunca `wp_update_post()` (kses elimina los comentarios HTML)

**Skills afectadas**: `gutenbergize`, `crear-editar-posts`, `crear-articulo`

**Skills actualizadas**:
- `crear-editar-posts/SKILL.md`: sección "Formato de pasajes de Escrituras en bloque" reescrita para usar el bloque self-closing dinámico en lugar del HTML manual obsoleto
- `gutenbergize/SKILL.md`: agregadas secciones 3b (mapeo de Escrituras), 3c (verificar intro), reglas críticas 6-8, y patrón correcto para artículos nuevos con Escrituras

**Causa raíz**: El skill `crear-editar-posts` aún documentaba el formato manual obsoleto con `<blockquote class="wp-block-lds-passage-block-passage">` + HTML interno, en lugar del bloque self-closing dinámico que el plugin realmente usa. El skill `gutenbergize` no contemplaba el bloque `lds-passage-block/passage` ni la verificación de intro.
