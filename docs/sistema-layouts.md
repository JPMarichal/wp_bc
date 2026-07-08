# Sistema de Layouts — Plan de Implementación

## Contexto

El sitio usa GeneratePress Child Theme con Bootstrap 5, Font Awesome, y un pipeline SCSS propio (21 partials compilados a `style-compiled.css`). Actualmente carece de un sistema de layouts opinado como el que ofrecen Newspaper 7 o themes similares: grids predefinidos, templates de página variados, headers personalizables, footers con columnas, sidebars configurables por página, y block patterns reutilizables.

Este documento cataloga lo que existe, lo que falta, y la prioridad recomendada.

---

## Estado Actual

### Lo que ya existe

| Componente | Archivo(s) | Estado |
|------------|-----------|--------|
| Módulos PHP | `inc/` (26 archivos) | Bueno, modularizado |
| SCSS por componente | `src/_*.scss` (24 partials) | Bueno, responsabilidad única |
| Design tokens | `src/_variables.scss` | Paleta Arena/Tierra/Jardín completa |
| Layout presets | `src/_layout.scss`, `inc/layout.php` | 6 clases (narrow/wide/full/sidebar-left/right/no-sidebar) |
| Sidebar metabox | `inc/sidebar-metabox.php` | Elegir posición por post |
| Header personalizable | `header.php`, `template-parts/header-*.php`, `assets/header.js` | Brand + Nav rows, search toggle, user dropdown, mobile menu |
| Footer columnas | `footer.php`, `template-parts/footer.php`, `inc/footer.php` | 3 widget areas + bottom bar con disclaimer |
| Page templates | 6 templates | full-width, narrow, landing, glosario, todos-artículos, taxonomy |
| Front page builder | `front-page.php` + `inc/front-page.php` | Ticker, featured, big grids, tabs, latest grid, categorías |
| Archive layout toggle | `inc/archive-layout.php`, `src/_archive-layout.scss` | Grid/list vía `?layout=list` |
| Post navigation | `inc/post-navigation.php` | Prev/next en single post |
| Author box | `inc/author-box.php` | Caja de autor en single |
| Search + 404 | `search.php`, `404.php` | Templates propios |
| Sidebar widgets | `inc/sidebar-widgets.php` + `sidebar.php` | Funcional, condicional por tipo de página |
| Category/tag archive | `category.php`, `tag.php` | Grid Bootstrap con responsive images |
| Block patterns | `inc/block-patterns.php` | 6 patrones Gutenberg (hero, grid, cita, FAQ, TOC, sidebar) |
| Block styles | `inc/block-styles.php` | 7 estilos para core blocks |
| theme.json | `theme.json` | Paleta + tipografía + espaciado para el editor |
| Editor visual | `editor-style.css`, `inc/editor-tools.php` | Editor matchea el frontend |
| Imágenes responsive | Todos los templates | `wp_get_attachment_image()` con srcset en 16 instancias |
| Print stylesheet | `src/_print.scss` | `@media print` con ocultación de UI |
| Admin columns | `inc/admin-columns.php` | Thumbnail column en listado de posts |
| Critical CSS inline | `inc/enqueue.php` | Cubre singular, category, tag, tax, CPT archives, search, 404, home |
| CSS/JS deferred | `inc/enqueue.php` | Compiled CSS, FA, Bootstrap con defer/print onload |
| DNS-prefetch | `inc/performance.php` | cdnjs, jsdelivr, fonts.googleapis, fonts.gstatic |
| OG / Schema / Breadcrumbs | `inc/og-tags.php`, `inc/schema.php`, `inc/breadcrumbs.php` | Implementado |

### Lo que ya no falta (gaps originales cerrados)

| Sistema | Solución |
|---------|----------|
| Design tokens | `_variables.scss` — paleta completa, escala tipográfica, espaciado, radios, sombras, breakpoints |
| Layout presets | `_layout.scss` + `inc/layout.php` — 6 clases de layout |
| Page templates | 6 templates (full-width, narrow, landing, glosario, todos-artículos, taxonomy) |
| Header personalizable | Header propio brand+nav, mobile hamburger, search toggle, user dropdown |
| Footer columnas | 3 widget areas + bottom bar con disclaimer |
| Sidebar por página | Metabox en editor para elegir derecha/izquierda/sin sidebar |
| Archive layout toggle | `inc/archive-layout.php` — grid/list vía `?layout=list` |
| Block patterns | `inc/block-patterns.php` — 6 patrones Gutenberg |
| theme.json | theme.json con paleta + tipografía + espaciado |

---

## Paleta de Colores — Metáfora del Jardín

La paleta sigue tres familias que representan el sustrato visual del sitio: **Arena** (base neutra), **Tierra** (estructura y contraste), **Jardín** (acento y vitalidad). Cada familia tiene una gama de 4 a 6 tonos que mapean directamente a variables Sass y a los colores de utilidad de Bootstrap.

### Principios

- La **Arena** domina el fondo (body, cards, widgets, header, footer). Es el lienzo.
- La **Tierra** domina el texto, bordes, y elementos de estructura. Es la tinta.
- El **Jardín** domina acentos, headings, CTAs, enlaces, y elementos interactivos. Es la voz.
- Los colores de **contraste** (rojo jardín, azul cielo) se usan con moderación para etiquetas, alerts, y elementos funcionales.
- Cada familia declara una gama que permite subtlety sin recurrir a valores mágicos.

### Familia Arena (sand — fondos y base neutra)

| Token | Gama | Uso |
|-------|------|-----|
| `$arena-50` | `#fdfcfa` | Fondo más claro (body en páginas especiales) |
| `$arena-100` | `#faf8f5` | Fondo por defecto del body |
| `$arena-200` | `#f5f2ec` | Fondo de widgets, cards, sidebars |
| `$arena-300` | `#ede7db` | Bordes suaves, separadores |
| `$arena-400` | `#d6cec1` | Bordes medios (inputs, tags) |

### Familia Tierra (earth — texto, estructura, contraste)

| Token | Gama | Uso |
|-------|------|-----|
| `$tierra-100` | `#9a8a7b` | Texto secundario, metadatos, fechas |
| `$tierra-200` | `#5c4736` | Texto corporal hover, íconos |
| `$tierra-300` | `#4a3728` | Texto corporal principal |
| `$tierra-400` | `#2c1f14` | Texto de headings (alternativo) |
| `$tierra-500` | `#1a110a` | Casi negro para footer o overlays |

### Familia Jardín (garden — acentos, headings, CTAs)

| Token | Gama | Uso |
|-------|------|-----|
| `$jardin-100` | `#e8f0e6` | Background sutil de acento (tablas, alerts) |
| `$jardin-200` | `#b8d4b3` | Hover de tags, badges suaves |
| `$jardin-300` | `#7eb376` | Bordes de acento, íconos |
| `$jardin-400` | `#3d7a37` | Hover de botones, enlaces hover |
| `$jardin-500` | **`#2d5a27`** | **Color primario** — headings h2, botones, enlaces |
| `$jardin-600` | `#1e3d1a` | Texto sobre fondo claro, footer headings |

### Colores de Contraste (acentos funcionales)

| Token | Gama | Uso |
|-------|------|-----|
| `$rojo-jardin` | `#c0392b` | Etiquetas de categoría, alerts de error, broken news, botón activo en glosario |
| `$azul-cielo` | `#1e73be` | Enlaces secundarios, información, botonera de navegación en glosario |
| `$naranja` | `#e65100` | Hover de botón activo en glosario, badges de advertencia, acento secundario |

### Gamas completas de contraste

Cada contraste principal tiene su propia gama para hover, fondo suave y borde:

| Familia | 100 (bg suave) | 200 (hover bg) | 400 (base) | 500 (hover) |
|---------|---------------|----------------|------------|-------------|
| Rojo | `#fce4e4` | `#f5b7b1` | `#c0392b` | `#a93226` |
| Azul cielo | `#e8f0fe` | `#bbd4f5` | `#1e73be` | `#1558a0` |
| Naranja | `#fef0e0` | `#fddcb5` | `#e65100` | `#bf4400` |

### Mapeo a Bootstrap 5

```scss
// Theme colors
$primary:       $jardin-500;
$secondary:     $tierra-300;
$success:       $jardin-400;
$info:          $azul-cielo;
$warning:       $naranja;
$danger:        $rojo-jardin;
$light:         $arena-100;
$dark:          $tierra-500;

$body-bg:       $arena-100;
$body-color:    $tierra-300;
$headings-color: $jardin-500;

$border-color:  $arena-300;

$link-color:            $jardin-500;
$link-hover-color:      $jardin-400;
```

### Integración con las variables Sass existentes

```scss
// Reemplazar valores actuales por la paleta
$color-primary:       $jardin-500;   // antes #1e73be
$color-primary-hover: $jardin-400;   // antes #0e5a9e
$color-text:          $tierra-300;   // antes #3a3a3a
$color-heading:       $jardin-500;   // antes #1e1e1e
$color-bg:            $arena-100;    // antes #ffffff
$color-bg-alt:        $arena-200;    // antes #fafafa
$color-border:        $arena-300;    // antes #e8e8e8

// Headings green escala se mantiene pero from $jardin-500
$heading-green-base:  $jardin-500;
$heading-green-h2:    $heading-green-base;
$heading-green-h3:    lighten($heading-green-base, 12%);
$heading-green-h4:    lighten($heading-green-base, 25%);
$heading-green-h5:    lighten($heading-green-base, 38%);
$heading-green-h6:    lighten($heading-green-base, 50%);
```

### Integración con theme.json

```json
"color": {
  "palette": [
    { "slug": "arena-100", "name": "Arena claro", "color": "#faf8f5" },
    { "slug": "arena-200", "name": "Arena medio", "color": "#f5f2ec" },
    { "slug": "arena-300", "name": "Arena borde", "color": "#ede7db" },
    { "slug": "tierra-300", "name": "Tierra texto", "color": "#4a3728" },
    { "slug": "tierra-500", "name": "Tierra oscuro", "color": "#1a110a" },
    { "slug": "jardin-500", "name": "Jardín primario", "color": "#2d5a27" },
    { "slug": "jardin-300", "name": "Jardín acento", "color": "#7eb376" },
    { "slug": "rojo-jardin", "name": "Rojo jardín", "color": "#c0392b" },
    { "slug": "azul-cielo", "name": "Azul cielo", "color": "#1e73be" }
  ]
}
```

### Principio de aplicación

- **Jerarquía**: Jardín > Tierra > Arena (el acento domina, la estructura sostiene, el fondo calla)
- **Proporción**: ~60% Arena (fondos, tarjetas, widgets, header, footer), ~10% Tierra (texto, bordes, estructura), ~30% Jardín + Contraste (headings, CTAs, enlaces, botones, etiquetas, elementos interactivos)
- **Contraste WCAG AA**: Jardín-500 sobre Arena-100 = 4.8:1 (título). Tierra-300 sobre Arena-100 = 6.3:1 (texto corporal). Ambos cumplen AA.

---

## Sistema Tipográfico y de Componentes

### Familias tipográficas

Se prioriza **performance**: solo Merriweather se carga como fuente externa (gratuita de Google Fonts, un único archivo woff2 para latin). System UI stack no requiere descarga. Mono es fallback del sistema sin descarga adicional.

| Capa | Fuente | Stack CSS | Carga | Uso |
|------|--------|-----------|-------|-----|
| **Headings** (principal) | Merriweather | `'Merriweather', Georgia, 'Times New Roman', serif` | Google Fonts (1 archivo) | h1–h6, widget titles, blockquote, hero titles |
| **Display** (hero) | Merriweather Bold | Misma fuente, `font-weight: 900` | Sin costo adicional | Featured posts, landing page titles, números grandes |
| **Body** (texto) | System UI | `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif` | Nativo del SO | Texto corrido, metadatos, navegación, entries |
| **UI** (interfaz) | System UI Medium | Mismo stack, `font-weight: 500` | Nativo del SO | Botones, tabs, labels, badge text |
| **Mono** (código) | System Monospace | `'Cascadia Code', 'SF Mono', 'Fira Code', 'Consolas', 'Liberation Mono', monospace` | Nativo del SO | Código, versículos, referencias, citas textuales |
| **Small** (meta) | System UI | Mismo stack, `font-size: 0.875rem` | Nativo del SO | Fechas, comentarios, footer, créditos |

Notas:
- Merriweather se carga con `font-display: swap` y solo los pesos 400, 700, 900 (tres variantes nada más).
- No se usa ninguna fuente adicional. Con 3 pesos de Merriweather + system stack nativo se cubren absolutamente todos los casos de uso del sitio.

### Mapeo de fuentes por componente

| Componente | Font family | Font weight | Font size |
|------------|-------------|-------------|-----------|
| Page title (h1) | Merriweather | 900 (bold) | h1 |
| Post title (h1 single) | Merriweather | 900 | h1 |
| Section heading (h2) | Merriweather | 700 | h2 |
| Card title (h3) | Merriweather | 700 | h3 |
| Widget title | Merriweather | 700 | 0.95em |
| Blockquote text | Merriweather | 400 italic | body |
| Hero overlay title | Merriweather | 900 | h1 |
| Nav menu links | System UI | 500 | 0.95rem |
| Post meta (fecha, autor) | System UI | 400 | small |
| Buttons / CTAs | System UI | 600 | 0.9rem |
| Tags / badges | System UI | 500 | xs |
| Sidebar widget content | System UI | 400 | 0.88em |
| Footer text | System UI | 400 | small |
| Breadcrumbs | System UI | 400 | small |
| Excerpt text | System UI | 400 | body |
| Code / verses | System Monospace | 400 | 0.9em |

### Escala tipográfica responsive

| Token | Mobile (<768px) | Desktop (≥768px) | Uso |
|-------|----------------|-------------------|-----|
| `h1` | 1.625rem (26px) | 2.25rem (36px) | Page title, post title, hero |
| `h2` | 1.375rem (22px) | 1.75rem (28px) | Section headings, entry title |
| `h3` | 1.25rem (20px) | 1.5rem (24px) | Card titles, widget titles |
| `h4` | 1.125rem (18px) | 1.25rem (20px) | Sub-section headings |
| `h5` | 1rem (16px) | 1.125rem (18px) | Minor headings |
| `h6` | 0.875rem (14px) | 1rem (16px) | Meta headings |
| `body` | 1rem (16px) | 1rem (16px) | Texto corrido |
| `small` | 0.875rem (14px) | 0.875rem (14px) | Metadatos, fechas |
| `xs` | 0.75rem (12px) | 0.75rem (12px) | Etiquetas, badges, copyright |

```scss
// Variables Sass para la escala
$h1-mobile: 1.625rem;
$h1-desktop: 2.25rem;
$h2-mobile: 1.375rem;
$h2-desktop: 1.75rem;
$h3-mobile: 1.25rem;
$h3-desktop: 1.5rem;
$h4-mobile: 1.125rem;
$h4-desktop: 1.25rem;
$h5-mobile: 1rem;
$h5-desktop: 1.125rem;
$h6-mobile: 0.875rem;
$h6-desktop: 1rem;
```

### Line heights

| Elemento | Line-height | Notas |
|----------|-------------|-------|
| Texto corporal | `1.7` | Lectura cómoda |
| Headings h1–h2 | `1.2` | Compacto, impacto |
| Headings h3–h4 | `1.3` | Balance |
| Headings h5–h6 | `1.4` | Más aire |
| Small / meta | `1.5` | Legible en tamaño reducido |
| Blockquote | `1.6` | Cita en Merriweather italic |
| Botones | `1.2` | Texto centrado verticalmente |
| Navegación | `1.4` | Links de nav |

### Sistema de Widgets Uniforme

Todos los widgets del sitio comparten la misma apariencia visual sin importar la página donde aparezcan. Esto se logra mediante una envoltura CSS única y clases consistentes.

```php
// Envoltura unificada (inc/sidebar-widgets.php)
<section class="bc-widget">
  <h3 class="bc-widget-title">
    <i class="fas fa-{{icon}}"></i> {{title}}
  </h3>
  <div class="bc-widget-content">
    {{content}}
  </div>
</section>
```

| Clase | Propósito | Estilo |
|-------|-----------|--------|
| `.bc-widget` | Contenedor | `bg: $arena-200`, `border: 1px solid $arena-300`, `border-radius: $radius-md`, `padding: 1.25em 16px 1.5em` |
| `.bc-widget-title` | Título | `font-family: Merriweather`, `font-weight: 700`, `color: $jardin-500`, `font-size: 0.95em`, `margin: 0 0 1em` |
| `.bc-widget-content` | Cuerpo | `font-size: 0.88em`, `line-height: 1.6`, `color: $tierra-300` |
| `.bc-widget-list` | Listas | `list-style: none`, items con `border-bottom: 1px solid $arena-300` |
| `.bc-widget--search` | Variante búsqueda | Input + botón en flex row |
| `.bc-widget--series` | Variante serie | Estilos para widget de serie de artículos |
| `.bc-widget-rp` | Recent posts | Thumbnail 80×80 + título en flex |
| `.bc-widget-tags` | Tag cloud | Flex wrap con gap 6px |
| `.bc-widget-view-all` | Link "Ver todos" | `font-size: 0.85em`, `font-weight: 600`, `color: $jardin-500` |

Los widgets **no cambian de apariencia por tipo de página**. Un widget de búsqueda se ve igual en single, archive, page, o front page. La única variación permitida es la clase `bc-widget--{variante}` para ajustes menores de layout interno.

Widgets disponibles en toda página con sidebar:

1. **Búsqueda** (siempre presente)
2. **Serie actual** (solo en single post si pertenece a una colección)
3. **Artículos recientes** (siempre presente)
4. **Comentarios recientes** (siempre presente)
5. **Temas** (tags del post actual en single, o tag cloud global en archives)

### Formas y bordes

| Componente | Border-radius | Sombra | Borde |
|------------|--------------|--------|-------|
| Cards / widgets | `6px` | `$shadow-sm` en hover | `1px solid $arena-300` |
| Botones primarios | `6px` | `$shadow-sm` | none |
| Botones secundarios | `6px` | none | `1px solid $jardin-500` |
| Inputs / textarea | `4px` | none | `1px solid $arena-400` |
| Inputs focus | — | `0 0 0 2px rgba($jardin-500, 0.15)` | `1px solid $jardin-400` |
| Tags / badges | `4px` | none | `1px solid $arena-300` |
| Imágenes | `4px` | opcional | none |
| Modales / popovers | `8px` | `$shadow-lg` | none |
| Tablas | `4px` cabecera | none | `1px solid $arena-300` |
| Navegación | `0` | none | none |
| Hero overlay | `0` | gradient | none |

### Tamaños de imagen (WordPress image sizes)

```php
// En inc/setup.php
add_image_size('bc-hero',          1600, 0,    false); // Full-width hero, height auto
add_image_size('bc-featured',      1200, 630,  true);  // Featured card, 16:9 crop
add_image_size('bc-card',          600,  400,  true);  // Grid card, 3:2 crop
add_image_size('bc-card-vertical', 400,  500,  true);  // Vertical card, 4:5 crop
add_image_size('bc-list-thumb',    120,  120,  true);  // List thumbnail, square
add_image_size('bc-sidebar-thumb', 80,   80,   true);  // Sidebar widget, square
add_image_size('bc-tiny',          40,   40,   true);  // Avatar, icon
```

| Size | Dimension | Crop | Uso |
|------|-----------|------|-----|
| `bc-hero` | 1600×auto | No | Hero image en single post |
| `bc-featured` | 1200×630 | Sí 16:9 | Featured card en front page |
| `bc-card` | 600×400 | Sí 3:2 | Grid cards en archives y front page |
| `bc-card-vertical` | 400×500 | Sí 4:5 | Tarjetas verticales |
| `bc-list-thumb` | 120×120 | Sí 1:1 | Listado de artículos |
| `bc-sidebar-thumb` | 80×80 | Sí 1:1 | Widget de posts recientes |
| `bc-tiny` | 40×40 | Sí 1:1 | Avatares, iconos pequeños |

### Espaciado entre componentes

| Contexto | Gap / Margin | CSS |
|----------|-------------|-----|
| Entre cards en grid | `24px` | `gap: $space-lg` |
| Entre secciones en página | `48px` | `margin-bottom: $space-2xl` |
| Entre widgets en sidebar | `20px` | `gap: 20px` (flex column) |
| Entre párrafos | `1.25em` | `p + p { margin-top: 1.25em }` |
| Padding interno de card | `16px` | `padding: $space-lg` |
| Padding de contenedor (mobile) | `20px` | `padding: $container-padding-mobile` |
| Padding de contenedor (desktop) | `40px` | `padding: $container-padding-desktop` |
| Entre ítems de lista | `8px` | `gap: $space-sm` |
| Entre heading y contenido | `12px` | `margin-bottom: $space-sm` |

### Breakpoints

| Name | Min-width | Target |
|------|-----------|--------|
| `xs` | 0 | Mobile default |
| `sm` | 576px | Tablets vertical |
| `md` | 768px | Tablets horizontal, sidebar breaks |
| `lg` | 992px | Desktop pequeño |
| `xl` | 1200px | Desktop, layout presets |
| `xxl` | 1400px | Desktop grande |

```scss
// Mixins responsive
@mixin respond-sm   { @media (min-width: 576px)  { @content; } }
@mixin respond-md   { @media (min-width: 768px)  { @content; } }
@mixin respond-lg   { @media (min-width: 992px)  { @content; } }
@mixin respond-xl   { @media (min-width: 1200px) { @content; } }
@mixin respond-xxl  { @media (min-width: 1400px) { @content; } }
@mixin respond-max($px) { @media (max-width: $px) { @content; } }
```

---

## Prioridad de Implementación

### Fase 1 — Cimientos (arquitectura visual)

#### 1.1 Design tokens en `_variables.scss`

Todas las escalas anteriores se consolidan en `_variables.scss`: paleta completa (Arena, Tierra, Jardín, Contraste), escala tipográfica responsive, line heights, espaciado, radios, sombras, breakpoints, tamaños de imagen. `_variables.scss` pasa a ser el **único lugar** donde se definen estos valores.

#### 1.2 Layout presets en `_layout.scss`

Clases de layout que controlan el ancho del `grid-container` y la relación content/sidebar:

| Clase | Content width | Sidebar | Sidebar position | Uso |
|-------|-------------|---------|-----------------|-----|
| `.bc-layout--default` | 100% (con padding) | Sí | Right | Posts normales |
| `.bc-layout--narrow` | 680px | No | — | Lectura, artículos largos |
| `.bc-layout--wide` | 960px | No | — | Páginas de contenido ancho |
| `.bc-layout--full` | 100% | No | — | Landing pages |
| `.bc-layout--sidebar-left` | 70% | Sí 30% | **Left** | Posts con sidebar a izquierda |
| `.bc-layout--sidebar-right` | 70% | Sí 30% | **Right** | Posts con sidebar a derecha |
| `.bc-layout--no-sidebar` | 100% | No | — | Expande contenido al ancho completo |

Comportamiento responsive:
- En **mobile (<768px)**: el sidebar siempre colapsa debajo del contenido, sin importar la posición configurada.
- En **desktop (≥768px)**: la clase `bc-layout--sidebar-left` invierte el orden visual via `flex-direction: row-reverse` (o `order` en los elementos).

```scss
// Layout con sidebar
.bc-layout--sidebar-right,
.bc-layout--sidebar-left {
  .site-content {
    display: flex;
    flex-wrap: wrap;

    > .content-area {
      flex: 1 1 0;
      min-width: 0;
      width: 70%;
    }

    > .sidebar {
      width: 30%;
      flex-shrink: 0;
    }
  }
}

// Sidebar a la izquierda: invierte orden visual
.bc-layout--sidebar-left {
  .site-content {
    flex-direction: row-reverse;
  }
}

// Sin sidebar: contenido ocupa 100%
.bc-layout--no-sidebar {
  .site-content {
    display: block;

    > .content-area {
      width: 100%;
      max-width: var(--content-width, 800px);
      margin: 0 auto;
    }

    > .sidebar {
      display: none;
    }
  }
}

// Responsive: sidebar colapsa debajo del contenido en mobile
@media (max-width: 767.98px) {
  .bc-layout--sidebar-right,
  .bc-layout--sidebar-left {
    .site-content {
      flex-direction: column;

      > .content-area,
      > .sidebar {
        width: 100%;
      }
    }
  }
}
```

#### 1.3 Output classes via `body_class`

El layout se define por template y por post meta:

```php
// inc/layout.php
add_filter('body_class', function ($classes) {
  $sidebar = '';

  if (is_singular()) {
    $sidebar = get_post_meta(get_the_ID(), '_bc_sidebar_position', true);
  }

  if (is_page_template('page-full-width.php')) {
    $classes[] = 'bc-layout--no-sidebar';
  } elseif (is_page_template('page-narrow.php')) {
    $classes[] = 'bc-layout--narrow';
  } elseif ('left' === $sidebar) {
    $classes[] = 'bc-layout--sidebar-left';
  } else {
    $classes[] = 'bc-layout--sidebar-right';
  }

  return $classes;
});
```

---

### Fase 2 — Templates (estructura de páginas)

#### 2.1 Nuevos page templates

```php
/*
Template Name: Página con Sidebar
*/
// inc/template-sidebar.php
```

| Template | Archivo | Layout |
|----------|---------|--------|
| Default (con sidebar) | `page-with-sidebar.php` | bc-layout--default |
| Full-width (sin sidebar) | `page-full-width.php` | bc-layout--full |
| Narrow (lectura) | `page-narrow.php` | bc-layout--narrow |
| Landing page | `page-landing.php` | bc-layout--full + hero |
| Archivo personalizado | `page-archive-custom.php` | Según configuración |

Cada template:
- Fuerza su layout via `add_filter('generate_sidebar_layout', ...)` y `body_class`
- Usa `get_template_part()` para el contenido variable
- Utiliza las clases de layout de Fase 1

#### 2.2 Header layout

El header ocupa su propio espacio en el top del sitio con 3 zonas en filas independientes:

```
┌──────────────────────────────────────────────────┐
│  [Logo texto]                    [🔍] [👤 User] │  ← Fila 1: Brand + tools
├──────────────────────────────────────────────────┤
│  Inicio  Artículos  Temas  Glosario  Acerca de   │  ← Fila 2: Menú principal
├──────────────────────────────────────────────────┤
│  (contenido de la página)                        │
└──────────────────────────────────────────────────┘
```

**Fila 1 — Brand bar** (`template-parts/header-brand.php`):

| Elemento | Descripción | Comportamiento mobile |
|----------|-------------|----------------------|
| Logo | Texto del sitio (`<h1>` o `<p>` con clase `.bc-logo`), link a home | Se mantiene visible |
| Búsqueda | Ícono de lupa que despliega input de búsqueda. Submit via `get_search_form()` con placeholder "Buscar en el sitio…" | Ícono visible, input expandible |
| Usuario | Si hay usuario logueado: avatar 32×32 + submenú desplegable con enlaces a Dashboard, Perfil, Cerrar sesión. Si no: botón "Iniciar sesión" | Mismo comportamiento, colapsado en menú hamburguesa en mobile |

```php
<header class="bc-header">
  <div class="bc-header-brand">
    <div class="grid-container">
      <div class="bc-header-row">
        <div class="bc-header-logo">
          <a href="<?php echo home_url(); ?>" class="bc-logo"><?php bloginfo('name'); ?></a>
        </div>
        <div class="bc-header-tools">
          <div class="bc-header-search">
            <button class="bc-search-toggle" aria-label="Buscar">
              <i class="fas fa-search"></i>
            </button>
            <div class="bc-search-form-wrapper">
              <?php get_search_form(); ?>
            </div>
          </div>
          <div class="bc-header-user">
            <?php if (is_user_logged_in()) : ?>
              <?php $current_user = wp_get_current_user(); ?>
              <div class="bc-user-dropdown">
                <button class="bc-user-toggle">
                  <?php echo get_avatar($current_user->ID, 32, '', $current_user->display_name, ['class' => 'bc-user-avatar']); ?>
                  <i class="fas fa-chevron-down"></i>
                </button>
                <ul class="bc-user-submenu">
                  <li><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
                  <li><a href="<?php echo admin_url('profile.php'); ?>">Perfil</a></li>
                  <li><a href="<?php echo wp_logout_url(home_url()); ?>">Cerrar sesión</a></li>
                </ul>
              </div>
            <?php else : ?>
              <a href="<?php echo wp_login_url(); ?>" class="bc-login-btn">
                <i class="fas fa-user"></i> Iniciar sesión
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="bc-header-nav">
    <div class="grid-container">
      <button class="bc-nav-toggle" aria-label="Menú">
        <span></span><span></span><span></span>
      </button>
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'menu_class'     => 'bc-nav-menu',
        'container'      => false,
        'fallback_cb'    => false,
      ]);
      ?>
    </div>
  </div>
</header>
```

**Fila 2 — Nav bar** (`template-parts/header-nav.php`): menú principal ocupando todo el ancho. En mobile el menú colapsa detrás de un hamburger toggle.

**SCSS del header:**

```scss
// Brand bar
.bc-header {
  background: $arena-100;
  border-bottom: 1px solid $arena-300;
}

.bc-header-brand {
  padding: 12px 0;
}

.bc-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $space-md;
}

.bc-logo {
  font-family: $font-family-headings;
  font-size: 1.4rem;
  font-weight: 900;
  color: $jardin-500;
  text-decoration: none;
  white-space: nowrap;

  &:hover { color: $jardin-400; }
}

.bc-header-tools {
  display: flex;
  align-items: center;
  gap: $space-sm;
}

// Search
.bc-header-search {
  position: relative;
}

.bc-search-toggle {
  background: none;
  border: none;
  font-size: 1.1rem;
  color: $tierra-300;
  cursor: pointer;
  padding: 6px 10px;
  border-radius: $radius-sm;
  transition: color 0.15s;

  &:hover { color: $jardin-500; }
}

.bc-search-form-wrapper {
  display: none;
  position: absolute;
  right: 0;
  top: 100%;
  width: 280px;
  padding: 12px;
  background: #fff;
  border: 1px solid $arena-300;
  border-radius: $radius-md;
  box-shadow: $shadow-md;
  z-index: 100;

  &.is-open { display: block; }
}

// User dropdown
.bc-user-dropdown {
  position: relative;
}

.bc-user-toggle {
  display: flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: $radius-sm;
  transition: background 0.15s;

  &:hover { background: $arena-200; }
}

.bc-user-avatar {
  border-radius: 50%;
  width: 32px;
  height: 32px;
}

.bc-user-submenu {
  display: none;
  position: absolute;
  right: 0;
  top: 100%;
  background: #fff;
  border: 1px solid $arena-300;
  border-radius: $radius-md;
  box-shadow: $shadow-md;
  list-style: none;
  margin: 0;
  padding: 6px 0;
  min-width: 160px;
  z-index: 100;

  li { margin: 0; }

  a {
    display: block;
    padding: 8px 16px;
    font-size: 0.88rem;
    color: $tierra-300;
    text-decoration: none;
    transition: background 0.15s;

    &:hover {
      background: $arena-200;
      color: $jardin-500;
    }
  }
}

// Nav bar
.bc-header-nav {
  background: $jardin-500;
  border-top: 1px solid $jardin-600;

  .grid-container {
    display: flex;
    align-items: center;
  }
}

.bc-nav-menu {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  gap: 0;

  li { margin: 0; position: relative; }

  a {
    display: block;
    padding: 12px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: background 0.15s, color 0.15s;

    &:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }
  }

  // Submenús
  .sub-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: $tierra-500;
    border-radius: 0 0 $radius-sm $radius-sm;
    box-shadow: $shadow-md;
    list-style: none;
    margin: 0;
    padding: 4px 0;
    min-width: 200px;
    z-index: 200;

    a {
      padding: 10px 18px;
      white-space: nowrap;
    }
  }

  li:hover > .sub-menu,
  li.focus > .sub-menu { display: block; }

  .current-menu-item > a,
  .current_page_item > a {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
  }
}

// Mobile
@media (max-width: 767.98px) {
  .bc-nav-toggle {
    display: flex;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 12px 0;

    span {
      display: block;
      width: 22px;
      height: 2px;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 1px;
      transition: transform 0.2s;
    }
  }

  .bc-nav-menu {
    display: none;
    flex-direction: column;
    width: 100%;

    &.is-open { display: flex; }

    .sub-menu {
      position: static;
      box-shadow: none;
      background: rgba(0, 0, 0, 0.15);
      border-radius: 0;
    }
  }
}

@media (min-width: 768px) {
  .bc-nav-toggle { display: none; }
}
```

#### 2.3 Footer layout

El footer es oscuro con texto claro, claramente diferenciado del resto del sitio.

```
┌─────────────────────────────────────────────────┐
│  [Logo pequeño]  [Menú]  [Redes sociales]        │  ← Footer widgets (3 columnas)
│                                                   │
├─────────────────────────────────────────────────┤
│  © 2026 Juan Pablo Marichal Catalán              │
│  Este sitio no es un sitio oficial de La Iglesia  │
│  de Jesucristo de los Santos de los Últimos Días. │
│  Se ha hecho todo esfuerzo para conformar su      │
│  contenido a la doctrina y prácticas de la Iglesia.│
└─────────────────────────────────────────────────┘
```

```php
<footer class="bc-footer">
  <div class="grid-container">
    <?php if (is_active_sidebar('footer-col-1') || is_active_sidebar('footer-col-2') || is_active_sidebar('footer-col-3')) : ?>
      <div class="row g-4 bc-footer-widgets">
        <div class="col-md-4"><?php dynamic_sidebar('footer-col-1'); ?></div>
        <div class="col-md-4"><?php dynamic_sidebar('footer-col-2'); ?></div>
        <div class="col-md-4"><?php dynamic_sidebar('footer-col-3'); ?></div>
      </div>
    <?php endif; ?>

    <div class="bc-footer-bottom">
      <span class="bc-footer-copyright">
        &copy; <?php echo date('Y'); ?> Juan Pablo Marichal Catalán
      </span>
      <p class="bc-footer-disclaimer">
        Este sitio no es un sitio oficial de La Iglesia de Jesucristo de los Santos de los Últimos Días.
        Se ha hecho todo esfuerzo para conformar su contenido a la doctrina y prácticas de la Iglesia.
      </p>
    </div>
  </div>
</footer>
```

**Widget areas del footer:**

```php
register_sidebar([
  'id'            => 'footer-col-1',
  'name'          => 'Footer Columna 1',
  'before_widget' => '<div class="bc-footer-widget %2$s">',
  'after_widget'  => '</div>',
  'before_title'  => '<h4 class="bc-footer-widget-title">',
  'after_title'   => '</h4>',
]);
register_sidebar([
  'id'            => 'footer-col-2',
  'name'          => 'Footer Columna 2',
  'before_widget' => '<div class="bc-footer-widget %2$s">',
  'after_widget'  => '</div>',
  'before_title'  => '<h4 class="bc-footer-widget-title">',
  'after_title'   => '</h4>',
]);
register_sidebar([
  'id'            => 'footer-col-3',
  'name'          => 'Footer Columna 3',
  'before_widget' => '<div class="bc-footer-widget %2$s">',
  'after_widget'  => '</div>',
  'before_title'  => '<h4 class="bc-footer-widget-title">',
  'after_title'   => '</h4>',
]);
```

**SCSS del footer:**

```scss
.bc-footer {
  background: $tierra-500;
  color: rgba(255, 255, 255, 0.8);
  padding: 48px 0 32px;
  margin-top: 48px;
}

.bc-footer-widgets {
  padding-bottom: 32px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  margin-bottom: 24px;
}

.bc-footer-widget-title {
  font-family: $font-family-headings;
  font-size: 1rem;
  font-weight: 700;
  color: #fff;
  margin: 0 0 16px;
}

.bc-footer-widget {
  font-size: 0.88rem;
  line-height: 1.6;

  a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.15s;

    &:hover {
      color: $jardin-200;
      text-decoration: underline;
    }
  }

  ul {
    list-style: none;
    margin: 0;
    padding: 0;

    li {
      padding: 4px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
  }
}

.bc-footer-bottom {
  text-align: center;
}

.bc-footer-copyright {
  display: block;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
  margin-bottom: 8px;
}

.bc-footer-disclaimer {
  font-size: 0.78em;
  color: rgba(255, 255, 255, 0.45);
  line-height: 1.5;
  margin: 0 auto;
  max-width: 720px;
}
```

---

### Fase 3 — Configurabilidad por página

#### 3.1 Sidebar metabox

Metabox en posts y pages para elegir posición del sidebar:

| Opción | Clase | Efecto en desktop (≥768px) | Efecto en mobile (<768px) |
|--------|-------|---------------------------|--------------------------|
| Sidebar derecha | `bc-layout--sidebar-right` | Content 70% left, sidebar 30% right | Sidebar colapsa debajo del contenido |
| Sidebar izquierda | `bc-layout--sidebar-left` | Sidebar 30% left, content 70% right (row-reverse) | Sidebar colapsa debajo del contenido |
| Sin sidebar | `bc-layout--no-sidebar` | Content 100%, sidebar hidden, centrado | Content 100%, sidebar hidden |
| Dual sidebar | (futuro) | Both sidebars | Colapsan ambas |

Almacenar como post meta `_bc_sidebar_position` (valores: `right`, `left`, `none`).

```php
// inc/sidebar-metabox.php
function bc_sidebar_metabox() {
  add_meta_box('bc_sidebar_position', 'Posición del sidebar',
    'bc_sidebar_metabox_html', ['post', 'page'], 'side');
}
add_action('add_meta_boxes', 'bc_sidebar_metabox');

function bc_sidebar_metabox_html($post) {
  $value = get_post_meta($post->ID, '_bc_sidebar_position', true) ?: 'right';
  ?>
  <select name="bc_sidebar_position" style="width:100%">
    <option value="right" <?php selected($value, 'right'); ?>>Sidebar derecha</option>
    <option value="left"  <?php selected($value, 'left');  ?>>Sidebar izquierda</option>
    <option value="none"  <?php selected($value, 'none');  ?>>Sin sidebar</option>
  </select>
  <?php
}

function bc_save_sidebar_meta($post_id) {
  if (isset($_POST['bc_sidebar_position'])) {
    update_post_meta($post_id, '_bc_sidebar_position', sanitize_key($_POST['bc_sidebar_position']));
  }
}
add_action('save_post', 'bc_save_sidebar_meta');
```

Consumir en un filter sobre `generate_sidebar_layout` y en `body_class` (ver Fase 1):

```php
add_filter('generate_sidebar_layout', function ($layout) {
  if (is_singular()) {
    $pos = get_post_meta(get_the_ID(), '_bc_sidebar_position', true);
    if ('none' === $pos) {
      return 'no-sidebar';
    }
  }
  return $layout;
});
```

#### 3.2 Archive layout toggle

Por categoría o tag, elegir entre:
- **Grid** (actual, `row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4`)
- **List** (1 columna, excerpt completo, thumbnail a la izquierda, 120×120)

Implementar como taxonomy meta (via Carbon Fields) o filter global en `inc/archive-layout.php`. En mobile, ambos layouts colapsan a 1 columna.

---

### Fase 4 — Reutilización (Gutenberg)

#### 4.1 Block patterns

Registrar en `inc/block-patterns.php`:

| Pattern | Descripción |
|---------|-------------|
| `bc/hero-simple` | Hero con título, subtítulo, CTA |
| `bc/sidebar-content` | Columnas 2:1 contenido + sidebar |
| `bc/card-grid` | Grid de 3 tarjetas con ícono |
| `bc/quote-featured` | Cita destacada con autor |
| `bc/faq-group` | Pregunta + respuesta apilables |
| `bc/table-of-contents` | TOC visual |

#### 4.2 theme.json

```json
{
  "version": 2,
  "settings": {
    "color": {
      "palette": [
        { "slug": "arena-100", "name": "Arena claro", "color": "#faf8f5" },
        { "slug": "arena-200", "name": "Arena medio", "color": "#f5f2ec" },
        { "slug": "arena-300", "name": "Arena borde", "color": "#ede7db" },
        { "slug": "tierra-300", "name": "Tierra texto", "color": "#4a3728" },
        { "slug": "tierra-500", "name": "Tierra oscuro", "color": "#1a110a" },
        { "slug": "jardin-500", "name": "Jardín primario", "color": "#2d5a27" },
        { "slug": "jardin-300", "name": "Jardín acento", "color": "#7eb376" },
        { "slug": "rojo-jardin", "name": "Rojo jardín", "color": "#c0392b" },
        { "slug": "azul-cielo", "name": "Azul cielo", "color": "#1e73be" },
        { "slug": "naranja", "name": "Naranja", "color": "#e65100" }
      ]
    },
    "typography": {
      "fontFamilies": [
        { "slug": "base", "name": "Base", "fontFamily": "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" },
        { "slug": "headings", "name": "Headings", "fontFamily": "'Merriweather', Georgia, serif" }
      ],
      "fontSizes": [
        { "slug": "xs", "name": "XS", "size": "0.75rem" },
        { "slug": "sm", "name": "Small", "size": "0.875rem" },
        { "slug": "base", "name": "Base", "size": "1rem" },
        { "slug": "lg", "name": "Large", "size": "1.125rem" },
        { "slug": "xl", "name": "XL", "size": "1.25rem" },
        { "slug": "2xl", "name": "2XL", "size": "1.5rem" },
        { "slug": "3xl", "name": "3XL", "size": "2rem" }
      ],
      "lineHeight": true
    },
    "spacing": {
      "units": ["px", "em", "rem"]
    }
  }
}
```

---

## Inventario de Áreas Pendientes

A continuación, todos los aspectos investigados que el sitio aún no cubre, agrupados por área. Se priorizarán luego.

### A. Taxonomías y Templates de Archivo

| Template | Child theme | Parent theme | Estado |
|----------|-------------|--------------|--------|
| `taxonomy-bc_quote_author.php` | No existe | No tiene | **GAP**: cae a `archive.php` del parent |
| `search.php` | No existe | Sí existe | Usa parent, sin personalización |
| `404.php` | No existe | Sí existe | Usa parent, sin personalización |
| `author.php` | No existe | **Tampoco existe** | **GAP**: cae a `archive.php` → `index.php` |
| `archive.php` | No existe | Sí existe | Usa parent, sin personalización |
| `comments.php` | No existe | Sí existe | Usa parent, sin personalización |
| Post navigation (prev/next) | No existe en `content-single.php` | No existe en parent | **GAP**: no hay navegación entre artículos |
| Author box | No existe | No existe | **GAP**: no hay caja de autor visible |

Templates child que ya existen: `category.php`, `tag.php`, `taxonomy-bc_chapter.php`, `taxonomy-collection.php`, `archive-bc_quote_author.php`, `single-bc_quote_author.php`.

### B. Imágenes Responsive y Media

| Aspecto | Situación actual |
|---------|-----------------|
| `srcset` / `sizes` | **Ninguna imagen del tema usa `srcset`**. Todas las llamadas usan `the_post_thumbnail()` con size fijo o `the_post_thumbnail_url()` como `<img src="...">` sin atributos responsive. |
| `wp_get_attachment_image()` | **Nunca se usa** en el child theme. Es la única función que genera `srcset`/`sizes` automáticamente. |
| `the_post_thumbnail()` | 8 llamadas en todo el tema, siempre con tamaño explícito (suprime `srcset`). |
| Hand-coded `<img>` tags | 10+ instancias en `front-page.php`, `category.php`, `tag.php` que usan `the_post_thumbnail_url('medium')` — sin `srcset`. |
| WebP conversion | No hay conversión server-side. El plugin Bunny Net reescribe URLs pero no convierte. Si hay WebP, es solo vía Bunny CDN Optimizer (edge layer). |
| Bunny Net srcset rewriting | El plugin hookea `wp_calculate_image_srcset`, pero como el tema nunca genera `srcset`, el hook no tiene nada que reescribir para imágenes del tema. |
| `loading="lazy"` | Usado en casi todas las imágenes secundarias (7 instancias). |
| `fetchpriority="high"` | Usado en la hero de single post (LCP candidate). Correcto. |
| `decoding="async"` | **Nunca se usa** en ninguna imagen. |
| Hero preload | Sí, implementado en `inc/performance.php` para singular posts. |
| Print stylesheet | **No existe**. No hay `@media print` rules ni `print.css`. |

### C. Editor Gutenberg y Admin

| Aspecto | Situación actual |
|---------|-----------------|
| `editor-style.css` | No existe en child theme. GeneratePress parent sí usa `add_editor_style()`, pero child no lo overridea. El editor no ve la paleta del tema. |
| `theme.json` | **No existe** en child ni en parent. GeneratePress maneja el editor vía PHP. Sin `theme.json`, el editor no tiene acceso nativo a colores, tipografía, ni espaciado del tema. |
| `enqueue_block_editor_assets` | Sí, en `inc/editor-tools.php` — enqueue un JS (`assets/editor.js`) en pantallas de edición de posts. |
| Admin columns | **No hay** columnas personalizadas en listados (`manage_posts_columns`). No se muestra serie, thumbnail, ni otros meta en el admin. |
| Admin branding | Solo un nodo "Nuevo Artículo" en la admin bar (`inc/editor-tools.php`). No hay branding de login, admin footer, ni colores personalizados. |

### D. Performance

| Aspecto | Situación actual |
|---------|-----------------|
| CSS deferred | Sí, implementado (print onload) para compiled CSS, FA, Bootstrap. |
| Critical CSS inline | Sí, para singular, category, tag, front-page. Faltan: custom post type archives, taxonomies, search, 404. |
| HTML minification | Sí, vía output buffer en `inc/performance.php`. |
| Preconnect | Sí: Bunny CDN, cdnjs, jsdelivr. |
| Font preload | Sí: Merriweather woff2. |
| Font-display swap | Sí, en todas las fuentes self-hosted. |
| Self-hosted fonts | Sí. |
| Lazy loading images | Sí, excepto hero. |
| Lazy loading iframes | Sí, vía content filter. |
| `defer` / `async` en scripts | **No se usa** en ningún script del tema. Solo Bootstrap JS está en footer. |
| Print stylesheet | No existe. |
| DNS-prefetch | No se usa. |

### E. Post Navigation, Comentarios y Extras

| Aspecto | Situación actual |
|---------|-----------------|
| Post navigation (prev/next) | No existe en `content-single.php`. |
| Author box | No existe. |
| Comments | Solo existen en `single-bc_quote_author.php` (CPT). No hay comments en `content-single.php` (posts normales). |
| Header top-bar | Mencionado en plan pero no definido. Podría incluir: enlaces rápidos, selector de idioma, fecha actual. |
| Off-canvas mobile menu | El hamburger toggle está previsto en SCSS como menú que despliega hacia abajo. No hay off-canvas lateral. |
| Gutenberg block styles | No hay overrides de bloques core (quote, pullquote, table, code) para que matcheen la paleta del tema. |

### F. Flujo de Trabajo para Imágenes Responsive

Para cerrar el gap de imágenes responsive, el cambio es mínimo gracias a Bunny CDN:

```php
// Donde hoy se usa:
the_post_thumbnail('medium', ['class' => 'bc-fp-card-image', 'loading' => 'lazy']);

// Cambiar a:
echo wp_get_attachment_image(get_post_thumbnail_id(), 'medium', false, [
  'class'   => 'bc-fp-card-image',
  'loading' => 'lazy',
]);
```

`wp_get_attachment_image()` genera automáticamente `srcset` + `sizes` con todos los tamaños registrados. Bunny CDN hookea `wp_calculate_image_srcset` para reescribir las URLs al CDN. Esto aplica a:
- `front-page.php` (10+ imágenes)
- `category.php` y `tag.php`
- `inc/related-posts.php`
- `inc/sidebar-widgets.php`
- `inc/front-page.php`

No requiere cambios estructurales ni nuevo pipeline de build.

---

## Prioridad Recomendada para Empezar a Construir

### Táctica: Alternar bloques grandes con wins rápidos

Cada sprint produce resultados visibles. No hay fase que dependa de otra para empezar.

| Sprint | Qué | Por qué primero | Archivos | Esfuerzo |
|--------|-----|-----------------|----------|----------|
| **1** | Post navigation + Author box + Search template + 404 template | Son wins rápidos, independientes, tapan agujeros que el usuario final ve todos los días. Search y 404 hoy caen en templates genéricos del parent. Author box y prev/next no existen. | `inc/post-navigation.php`, `inc/author-box.php`, `search.php`, `404.php` | Bajo (4 archivos) |
| **2** | Header real (brand + nav rows) + Footer oscuro | Es la estructura visible más importante del sitio. Sin header propio, el sitio se ve genérico. El footer oscuro ya está especificado con widgets + disclaimer. | `template-parts/header-brand.php`, `template-parts/header-nav.php`, `inc/header.php`, `template-parts/footer.php`, `inc/footer.php` | Medio (5 archivos) |
| **3** | Design tokens + Layout presets + Sidebar metabox | Base técnica para todo lo demás. Los tokens en `_variables.scss` eliminan valores mágicos. Los layout presets en `_layout.scss` más el metabox permiten elegir sidebar por página. | `src/_variables.scss`, `src/_layout.scss`, `src/_mixins.scss`, `inc/layout.php`, `inc/sidebar-metabox.php` | Medio-alto (5 archivos, toca el sistema Sass) |
| **4** | Templates de página (full-width, narrow, landing) + taxonomy-bc_quote_author | Los templates nuevos dan variedad de layouts. La taxonomy faltante evita un fallback genérico. | `page-full-width.php`, `page-narrow.php`, `page-landing.php`, `taxonomy-bc_quote_author.php` | Bajo (4 archivos, páginas simples) |
| **5** | Imágenes responsive (srcset) + Print stylesheet | Migrar `the_post_thumbnail()` a `wp_get_attachment_image()` en todos los templates da srcset automático. Print CSS es un archivo. Sin esto, las imágenes no se benefician del CDN de Bunny ni de responsive. | 8+ templates (front-page, category, tag, related, sidebar-widgets) + `inc/print.css` | Medio (búsqueda y reemplazo en 8+ archivos) |
| **6** | theme.json + Editor block styles | theme.json le da la paleta al editor Gutenberg. Block styles hacen que quote, table, pullquote del editor matcheen el frontend. Sin esto el editor ve colores genéricos. | `theme.json`, `inc/block-styles.php`, `editor-style.scss` | Bajo-medio |
| **7** | Resto (admin columns, branding, off-canvas menu, defer scripts, critical CSS extendido) | Polish final. Mejora la experiencia de admin y performance. Dependiente de nada. | `inc/admin-columns.php`, más ajustes a `inc/enqueue.php`, `inc/performance.php` | Bajo |

### Principio rector

Cada sprint produce un entregable visible y desplegable independientemente. No hay blockers entre sprints — el orden es de impacto, no de dependencia técnica.

---

## Resumen de Archivos a Crear/Modificar

| Archivo | Acción | Fase |
|---------|--------|------|
| `src/_layout.scss` | Modificar (layout presets) | 1 |
| `inc/layout.php` | Crear (body_class + setup) | 1 |
| `page-with-sidebar.php` | Crear | 2 |
| `page-full-width.php` | Crear | 2 |
| `page-narrow.php` | Crear | 2 |
| `page-landing.php` | Crear | 2 |
| `template-parts/header-brand.php` | Crear (logo + search + user) | 2 |
| `template-parts/header-nav.php` | Crear (menú principal, su propio renglón) | 2 |
| `template-parts/footer.php` | Crear (footer oscuro con disclaimer + widget columns) | 2 |
| `inc/header.php` | Crear (orquesta brand + nav rows) | 2 |
| `inc/footer.php` | Crear (orquesta footer completo con sidebar columns) | 2 |
| `functions.php` | Modificar (require nuevos inc/) | 1-4 |
| `inc/sidebar-metabox.php` | Crear | 3 |
| `inc/archive-layout.php` | Crear | 3 |
| `inc/block-patterns.php` | Crear | 4 |
| `theme.json` | Crear (paleta + tipografía + espaciado) | 4 |
| `src/_variables.scss` | Modificar (agregar paleta completa + escala responsive) | 1 |
| `src/_mixins.scss` | Modificar (breakpoints md/lg/xl/xxl) | 1 |
| `inc/setup.php` | Modificar (nuevos image sizes) | 1 |
| `search.php` | Crear (template de búsqueda con paleta del tema) | — |
| `404.php` | Crear (template 404 con diseño propio) | — |
| `author.php` | Crear (template de autor, no existe en parent) | — |
| `taxonomy-bc_quote_author.php` | Crear (template para taxonomía faltante) | — |
| `inc/post-navigation.php` | Crear (prev/next en single) | — |
| `inc/print.css` | Crear (print stylesheet) | — |
| `inc/author-box.php` | Crear (caja de autor en single) | — |
| `inc/admin-columns.php` | Crear (columnas personalizadas en listados) | — |
| `inc/block-styles.php` | Crear (overrides Gutenberg para bloques core) | — |
| Multiple front-end files | Modificar (`the_post_thumbnail()` → `wp_get_attachment_image()` para srcset) | — |
