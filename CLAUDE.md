# WordPress Build Philosophy — wp_bc

## Principios Fundamentales

### 1. Componentes Sass con Responsabilidad Única
- Un partial SCSS por componente visual (`.page-hero`, `.page-share-bar`, `.entry-content`)
- No mezclar componentes distintos en un mismo partial
- No organizar por tipo de regla; organizar por componente
- Excepción: partials de infraestructura (`_variables`, `_mixins`, `_base`)

### 2. Carga Condicional (Lazy Loading)
- Cargar CSS/JS solo en las páginas donde se necesita
- Usar `is_singular()`, `is_archive()`, `is_front_page()` según corresponda
- **Excepción**: Font Awesome y Bootstrap deben estar disponibles en **todo el sitio**

### 3. Defer de CSS No Crítico
- `media="print" onload="this.media='all'"` a todo CSS no crítico
- CSS compilado del tema, FA y Bootstrap usan deferred loading

### 4. Critical CSS Inline
- Lo visible sin scroll va inline en `<head>` via `wp_head` con `<style id="bc-critical-css">`
- Solo en páginas donde esos componentes existen
- Valores hardcodeados (estáticos) para evitar dependencias de Sass

### 5. Modularización PHP por Responsabilidad
- Un archivo PHP por concern en `inc/`
- `functions.php` es solo orquestador con `require_once`

### 6. Pipeline Sass
- Sass → Autoprefixer → cssnano
- `npm run build` para compilar, `npm run dev` para watch
- Sin source maps en producción

## Archivos Clave

| Propósito | Ruta |
|:----------|:-----|
| Orquestador PHP | `functions.php` |
| Enqueue + performance | `inc/enqueue.php` |
| Configuración del tema | `inc/setup.php` |
| Meta tags OG/Twitter | `inc/og-tags.php` |
| Share bar render | `inc/share-bar.php` |
| Entry point SCSS | `src/style.scss` |
| Variables Sass | `src/_variables.scss` |
| Mixins responsive | `src/_mixins.scss` |
| Layout base | `src/_layout.scss` |
| Hero componente | `src/_hero.scss` |
| Share bar componente | `src/_share-bar.scss` |
| Entry content componente | `src/_entry-content.scss` |
| Salida compilada | `style-compiled.css` |
| Single post template | `content-single.php` |

### 7. Principio de Responsabilidad Monotemática en Artículos
- Cada artículo cubre un solo tema
- Si un aspecto secundario requiere más de 2–3 párrafos o tiene sus propias fuentes, merece artículo propio
- Reducir el tratamiento original a 1–2 párrafos con enlace al nuevo artículo
- Documentado en detalle en `.claude/skills/crear-articulo/SKILL.md` (Regla 0)

## ⚠️ Regla de Oro: NO TOCAR LA BASE DE DATOS

**NUNCA** ejecutar comandos que modifiquen, detengan, reinicien o migren la base de datos sin:
1. Hacer un dump de verificación (`mysqldump --all-databases`) primero
2. Confirmar que el dump tiene el tamaño y contenido esperados
3. Tener autorización explícita del usuario paso a paso

**NUNCA** eliminar `db-data/` ni su contenido. **NUNCA** forzar recreación de contenedores (`compose up -d` cuando ya están corriendo). Si MySQL no arranca, reportar el error y esperar instrucciones — no intentar reparaciones múltiples.

**Si los contenedores ya están corriendo y funcionando, no tocarlos.**

## 🖼️ Arquitectura de Media (BunnyCDN)

### Regla de Oro del CDN
- **Los archivos de media (uploads/) NO deben existir localmente**. La CDN es la única fuente de verdad.
- Nunca comprimir, optimizar, o modificar imágenes localmente — la CDN/Storage zone tiene el original.
- El plugin `indigetal-media-offload-for-bunny-net` se encarga de offloadear automáticamente las imágenes subidas vía WordPress admin.
- Para batches de imágenes (thumbnails de personas), el skill `imagenes-bc` sube directamente via Python a BunnyCDN Storage API.

### Stack de Media
| Componente | Detalle |
|:-----------|:--------|
| Storage Zone | `ve-media-storage` (LA, replicado a NY) |
| Pull Zone | `ve-pull-zone.b-cdn.net` (ID: 6050116) |
| Storage Password | `0fbb00db-5a99-4c86-a41665993e2e-7c8f-438d` |
| Region | `la` (Los Ángeles) |
| Plugin | `indigetal-media-offload-for-bunny-net` v1.0.5 (Free) |
| CDN Optimizer | **Deshabilitado** (no usar — costo adicional) |
| URL Pattern | `https://ve-pull-zone.b-cdn.net/wp-content/uploads/{year}/{month}/{file}` |

### Cómo funciona el offload
1. **Subida por WordPress admin**: El plugin escucha `update_attached_file` y `wp_update_attachment_metadata`, sube original + thumbnails a BunnyCDN Storage, y borra los locales si `indigetal_offload_remove_local_files = 1`.
2. **Batch por Python** (`imagenes-bc` skill): Descarga imágenes, crea attachments via MySQL, y sube a BunnyCDN Storage via `requests.put()` con `AccessKey` header. Luego setea `_indigetal_offloaded = complete` en postmeta para que el plugin sepa que ya está offloadeado.
3. **Estados de offload** (`_indigetal_offloaded` meta): `local` → sin offloadear, `partial` → original subido pero thumbnails no, `complete` → todo en CDN, `error` → falló completamente.

### ⚠️ Lo que NO debe hacerse
- No regenerar thumbnails (los tamaños actuales no están definidos)
- No comprimir/optimizar archivos locales (la CDN debe servir los originales)
- No habilitar CDN Optimizer (costo por request en el edge)
- No modificar imágenes de uploads/ — si algo está mal, se corrige desde el origen (WordPress admin o script Python) y se re-subé a la CDN
