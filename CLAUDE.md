# WordPress Build Philosophy — wp_bc

## ⚠️ REGLA CRÍTICA: Usar SIEMPRE podman, NUNCA docker

**NUNCA usar comandos `docker` bajo ninguna circunstancia.** Cualquier comando que pueda correrse como `podman` debe correrse como `podman`, no como `docker`. Los contenedores de este proyecto corren en **podman (Podman Desktop)**. Ejecutar comandos `docker` mezcla los contenedores con los de Docker Desktop y los corrompe, haciéndolos desaparecer de la interfaz de Podman Desktop.

En lugar de:
```bash
docker exec wp_bc_cli wp post get 2651
```
Usar:
```bash
podman exec wp_bc_cli wp post get 2651
```

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

## 📝 Estilo Editorial: Nombre de la Iglesia

**Regla**: Cada vez que se mencione La Iglesia de Jesucristo de los Santos de los Últimos Días, el nombre de Jesucristo debe estar presente explícitamente.

| Uso | Ejemplo | ¿Aceptable? |
|:----|:--------|:-----------:|
| Nombre completo (primera mención) | «La Iglesia de Jesucristo de los Santos de los Últimos Días» | ✅ Preferido |
| Abreviado con Cristo | «la Iglesia de Jesucristo» | ✅ Preferido |
| Con «restaurada» + Cristo | «la Iglesia restaurada de Jesucristo» | ✅ Aceptable |
| Genérico con mayúscula | «la Iglesia» | ✅ Aceptable (segunda mención+) |
| «la Iglesia Restaurada» sola | ❌ **No** — omite el nombre de Cristo |
| «la Iglesia SUD» | ❌ **No** — omite el nombre de Cristo |
| «la Iglesia Mormona» | ❌ **No** — término desaconsejado por la Primera Presidencia |

**Fundamento**: Presidente Russell M. Nelson (Conferencia Gral. oct. 2018): «Quitar el nombre del Señor de la Iglesia del Señor es una gran victoria para Satanás». Guía de estilo de Newsroom (2019): «When a shortened reference is needed, the terms "the Church" or the "Church of Jesus Christ" are encouraged. The "restored Church of Jesus Christ" is also accurate and encouraged.»

## 🏗️ Arquitectura de Contenido

### Glosarios (CPTs)

| CPT | Label | Archive (índice) | Single (detalle) | Rewrite slug |
|:---|:-----|:---------|:----------|:-------------|
| `bc_quote_author` | Personas | `/glosario/persona/` → `archive-bc_quote_author.php` | `/glosario/persona/{slug}/` → `single-bc_quote_author.php` | `glosario/persona` |
| `bc_location` | Ubicaciones | `/ubicaciones/` → `archive-bc_location.php` | `/ubicacion/{slug}/` → `single-bc_location.php` | `ubicacion` |

### Taxonomías

| Taxonomy | Label | Tipo | Post types | Rewrite | Uso |
|:---------|:------|:-----|:-----------|:--------|:----|
| `category` | Categorías | jerárquica | `post` | `/category/` | Solo existe **1** ("Sin categoría"). No crear más. |
| `post_tag` | **Temas** | no jerárquica | `post` | `/tag/` | Se nombran como **"temas"** (no "tags"). Etiquetan artículos por asunto. 59 temas actuales. **Ver criterios de creación abajo.** |
| `collection` | Colecciones y Series | jerárquica (2 niveles) | `post`, `page` | `/coleccion/` | **Ver sección abajo**. |
| `bc_chapter` | Capítulos de las Escrituras | jerárquica | `post`, `bc_quote_author` | (*none*) | Enlaza personas y posts con escrituras. 1674 términos. Sin rewrite público. |
| `bc_author_calling` | Llamamientos | no jerárquica | `bc_quote_author` | (*none*) | Llamamientos eclesiásticos de personas. 16 términos. No público. |

### Colecciones y Series (`collection`)

**Regla fundamental**: Los posts se asignan SIEMPRE a una **serie** (término hijo), NUNCA a una colección (término padre).

| Nivel | Término | Ejemplo |
|:------|:--------|:--------|
| **Padre (Colección)** | `parent=0` | "La historia de la Biblia" (ID 3751) |
| **Hijo (Serie)** | `parent={colección}` | "El origen de la Biblia" (ID 3760) → posts aquí |

- **Las series se llaman "serie"** no "sub-colección".
- Los posts tienen `_series_position` (int) para orden dentro de la serie.
- Las series tienen `_series_order` (int) para orden dentro de la colección.

Estructura actual:

| Colección (padre) | Series (hijos) |
|:------------------|:---------------|
| **La vida de Abraham** (ID 21) | *(sin series aún)* |
| **Sodoma y Gomorra** (ID 22) | La historia de Lot, Arqueología e historia de Sodoma, El pecado de Sodoma |
| **La historia de la Biblia** (ID 3751) | El origen de la Biblia, Las grandes versiones de la Biblia, La Biblia en la Iglesia Restaurada, La creencia de los SUD en la Biblia |
| **La historia del Libro de Mormón** (ID 3766) | El origen del Libro de Mormón, El texto del Libro de Mormón, El Libro de Mormón en la Iglesia de Jesucristo, Doctrinas clave del Libro de Mormón |

### Convenciones de nomenclatura

- **"Temas"** = `post_tag` (no decir "tags")
- **"Colección"** = término padre de `collection`
- **"Serie"** = término hijo de `collection`
- **"Categoría"** = `category` (solo existe 1)
- **"Personas"** = CPT `bc_quote_author`
- **"Ubicaciones"** = CPT `bc_location`

### Criterios para temas (`post_tag`)

**Regla general**: Reaprovechar temas existentes siempre que sea posible. Crear nuevos solo si cumplen TODOS estos criterios:

1. **Reutilizable** — el término servirá para etiquetar otros artículos futuros, no solo el actual.
2. **SEO / cola larga** — corresponde a una búsqueda real que un usuario haría en Google. Preferir frases de 2–4 palabras.
3. **Terminología del evangelio** — es vocabulario propio del evangelio restaurado o del cristianismo general (o ambos).

**Ejemplo correcto**: `ley-de-los-testigos` — término del cristianismo general pero más usado en la Restauración, búsqueda real, reutilizable en otros artículos sobre testigos del Libro de Mormón.

**Ejemplo incorrecto**: `introduccion-al-libro-de-mormon` — demasiado específico al artículo "¿Qué es el Libro de Mormón?", no reutilizable en otros contextos.

**Antes de crear uno nuevo**: consultar los 59 temas existentes para verificar que ninguno cubra ya el concepto.

### Páginas especiales

| Propósito | URL |
|:----------|:----|
| Glosario (portal) | `/glosario/` |
| Glosario de Temas | `/glosario/temas/` |
| Todos los artículos | `/todos-los-articulos/` |
| Front Page | *(ninguna asignada)* |

### URLs típicas

| Tipo | URL Pattern |
|:-----|:------------|
| Artículo (post) | `/{slug}/` |
| Colección | `/coleccion/{slug}/` |
| Serie (hijo) | `/coleccion/{parent-slug}/{child-slug}/` ? *(ver rewrite real)* |
| Persona | `/glosario/persona/{slug}/` |
| Ubicación | `/ubicacion/{slug}/` |
| Tema (tag) | `/tag/{slug}/` |

### Plugin responsable

| Plugin | Función |
|:-------|:--------|
| `bc-content-organization` | Registra `collection`, meta boxes en editor, página "Organizar series" en admin, widget de navegación de serie en frontend |

**⚠️ REGLA CRÍTICA: NO TOCAR LA BASE DE DATOS**

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
