---
name: gutenbergize
description: Convertir artículos de WordPress de formato classic (HTML suelto sin marcadores de bloque) a bloques Gutenberg. Usar cuando: (1) un artículo existente falla `has_blocks()` o contiene bloques con `blockName === null`, (2) se necesita diagnosticar por qué bloques MerPress no se renderizan en la UI, (3) se crea un artículo nuevo y se requiere estructura Gutenberg correcta desde el inicio.
---

# Gutenbergize: Conversión a Bloques Gutenberg

## Detección del problema

```php
$blocks = parse_blocks(get_post($id)->post_content);
$has_blocks = has_blocks(get_post($id)->post_content);
```

- `has_blocks() = false` → artículo completamente classic
- `has_blocks() = true` con bloques `blockName = null` → parcialmente classic
- Bloques classic se muestran como "Classic" en el editor de Gutenberg

## Pipeline de conversión

### 1. Obtener contenido actual

```php
$post_id = 2616; // o el ID del artículo
$post = get_post($post_id);
$content = $post->post_content;
$blocks = parse_blocks($content);
```

### 2. Identificar bloques classic

En el array de bloques, los de `blockName === null` contienen HTML clásico en `innerHTML`. Extraerlos individualmente con regex y envolver cada uno en su marcador Gutenberg correspondiente.

### 3. Mapeo HTML → bloque Gutenberg

| Elemento HTML | Bloque | Marcador de apertura |
|---------------|--------|---------------------|
| `<p>...</p>` | core/paragraph | `<!-- wp:paragraph -->` |
| `<h2>...</h2>` | core/heading | `<!-- wp:heading -->` |
| `<h3>...</h3>` | core/heading | `<!-- wp:heading {"level":3} -->` |
| `<blockquote>...</blockquote>` | core/quote | `<!-- wp:quote -->` |
| `<ul>...</ul>` / `<ol>...</ol>` | core/list | `<!-- wp:list -->` |
| `<figure class="wp-block-image">...</figure>` | core/image | `<!-- wp:image -->` |

### 4. Construir el nuevo contenido

```php
$parts = [];
foreach ($html_elements as $el) {
    $block = '';
    if (preg_match('/^<h([1-6])\b/', $el, $m)) {
        $level = (int)$m[1];
        $attrs = $level === 2 ? '' : ' {"level":' . $level . '}';
        $parts[] = '<!-- wp:heading' . $attrs . ' -->' . $el . '<!-- /wp:heading -->';
    } elseif (strpos($el, '<blockquote') === 0) {
        $parts[] = '<!-- wp:quote -->' . $el . '<!-- /wp:quote -->';
    } elseif (preg_match('/^<(ul|ol)\b/', $el)) {
        $parts[] = '<!-- wp:list -->' . $el . '<!-- /wp:list -->';
    } elseif (strpos($el, '<figure') === 0) {
        $parts[] = '<!-- wp:image -->' . $el . '<!-- /wp:image -->';
    } else {
        $parts[] = '<!-- wp:paragraph -->' . $el . '<!-- /wp:paragraph -->';
    }
}
$new_content = implode("\n", $parts);
```

**Importante:** Usar `\n` simple entre bloques (no `\n\n`). Los dobles newlines crean bloques classic invisibles de whitespace.

### 5. Guardar

```php
global $wpdb;
$wpdb->update(
    $wpdb->posts,
    ['post_content' => $new_content],
    ['ID' => $post_id],
    ['%s'],
    ['%d']
);
```

Usar `$wpdb->update()` en lugar de `wp_update_post()`. `wp_update_post()` pasa por kses que elimina los comentarios HTML `<!-- wp:... -->`.

### 6. Verificar

```php
$blocks = parse_blocks(get_post($post_id)->post_content);
$classic_count = count(array_filter($blocks, fn($b) => $b['blockName'] === null));
echo "Bloques classic restantes: $classic_count\n"; // Debe ser 0 o solo whitespace
```

## Reglas críticas

1. **No mezclar classic y Gutenberg** — desde la creación, TODO el contenido debe ir con marcadores de bloque.
2. **Preservar bloques existentes** — si el contenido ya tiene `<!-- wp:merpress/mermaidjs -->` u otros bloques, no tocarlos.
3. **Listas completas** — envolver `<ul>...</ul>` completo en un solo `<!-- wp:list -->`, no separar cada `<li>`.
4. **Headings con level** — solo `h3`+ necesita `{"level":N}`. `h2` es el default y no lleva atributos.
5. **Extraer elementos individualmente** — usar `preg_match_all()` con capture de cada tag HTML. No confiar en `preg_split()`.

## Creación de artículos NUEVOS con Gutenberg

Para artículos nuevos, construir el `post_content` directamente con marcadores:

```php
$post_data = [
    'post_title'   => 'Título del artículo',
    'post_content' => '<!-- wp:paragraph --><p>Texto...</p><!-- /wp:paragraph -->' . "\n"
                    . '<!-- wp:heading --><h2>Subtítulo</h2><!-- /wp:heading -->' . "\n"
                    . '<!-- wp:paragraph --><p>Más texto...</p><!-- /wp:paragraph -->',
    'post_status'  => 'publish',
    'post_author'  => 1,
];
wp_insert_post($post_data);
```

Para contenido extenso, construir array de partes e implosionar:
```php
$parts[] = '<!-- wp:paragraph --><p>Texto</p><!-- /wp:paragraph -->';
$parts[] = '<!-- wp:heading --><h2>Título</h2><!-- /wp:heading -->';
// ...
$post_content = implode("\n", $parts);
```
