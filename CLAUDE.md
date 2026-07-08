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
