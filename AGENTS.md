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
| Persona | `/glosario/persona/{slug}/` |
| Ubicación | `/ubicacion/{slug}/` |
| Tema (tag) | `/tag/{slug}/` |

### Plugin responsable

| Plugin | Función |
|:-------|:--------|
| `bc-content-organization` | Registra `collection`, meta boxes en editor, página "Organizar series" en admin, widget de navegación de serie en frontend |

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

## 🖼️ Arquitectura de Media (BunnyCDN)

| Componente | Detalle |
|:-----------|:--------|
| Storage Zone | `ve-media-storage` (LA, replicado a NY) |
| Pull Zone | `ve-pull-zone.b-cdn.net` (ID: 6050116) |
| Region | `la` (Los Ángeles) |
| Plugin | `indigetal-media-offload-for-bunny-net` v1.0.5 (Free) |
| URL Pattern | `https://ve-pull-zone.b-cdn.net/wp-content/uploads/{year}/{month}/{file}` |

### Reglas del CDN
- Los archivos de media NO deben existir localmente. La CDN es la única fuente de verdad.
- No comprimir/optimizar imágenes localmente.
- El plugin `indigetal-media-offload-for-bunny-net` offloadea automáticamente.
- Para batches de thumbnails de personas, usar el skill `imagenes-bc`.
