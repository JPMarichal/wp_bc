# wp_bc — Definición del Codebase y Arquitectura

> Documento de referencia del proyecto. Describe **qué es** este codebase, **cómo está
> organizado** y **cómo se opera**. Es la fuente de verdad de arquitectura. Si algo aquí
> contradice la realidad del repo, gana el repo y hay que actualizar este documento.

---

## 1. Qué es

`wp_bc` es el repositorio y el entorno de despliegue de una **plataforma editorial /
enciclopédica en WordPress** sobre La Iglesia de Jesucristo de los Santos de los Últimos
Días. **No es un sitio oficial de la Iglesia** (lleva disclaimer en el footer).

> El nombre público del sitio es **Verdades Eternas**. Está configurado en `blogname`
> (base de datos) y en `llms.txt`. Cualquier referencia a "Bednarck" está obsoleta.

El sitio es **mucho más que una colección de citas**. Incluye, como mínimo:

- **Biografías de personas** (líderes y testigos) con normas editoriales estrictas.
- **Glosario de Ubicaciones bíblicas** con mapas interactivos de relieve 3D.
- **Glosario de Temas** (taxonomía navegable).
- **Artículos** monotemáticos organizados en **series y colecciones**.
- **Referencias escriturales** y bloques de pasajes.
- Un **pipeline de generación de contenido asistido por IA** (skills + RAG "Alejandría").

---

## 2. Runtime y entorno — ⚠️ PODMAN, no Docker

**Los contenedores corren en Podman, NO en Docker.** No deben entremezclarse con un Docker
"general" del sistema.

- Todo el tooling del repo (`justfile`, `docker-compose.yml`, `.mcp.json`, `opencode.json`)
  **invoca literalmente el binario `docker`**. Esto funciona porque en este entorno `docker`
  resuelve a Podman (shim / alias `podman-docker` o compat CLI). **Al operar, tener presente
  que la máquina real es Podman.**
- No mezclar redes, volúmenes ni contenedores con otro stack Docker del host.
- Los comandos de mantenimiento asumen los nombres de contenedor `wp_bc`, `wp_bc_cli`,
  `wp_bc_db` y la red `wp_bc_default`.

### Regla absoluta de este proyecto

> **Dentro de `C:\own\wp_bc` (y sus subdirectorios) NUNCA ejecutar `docker ...` ni `docker compose ...`.**
>
> - Usá `podman ...` y `podman compose ...` exclusivamente.
> - No ejecutes comandos `docker` desde terminales que estén ubicadas en `C:\own\wp_bc` o sus
>   subdirectorios, porque en este host `docker` apunta a Rancher Desktop/Moby y tocaría
>   contenedores de `C:\git`.
> - Si un script, `justfile` o herramienta invoca `docker`, verificá que en la práctica esté
>   operando contra Podman. Si dudás, corré `docker ps` desde `C:\own\wp_bc`: si aparecen
>   contenedores de `C:\git` (`web-shim`, `sso-api`, `mysql`, etc.), estás en el motor
>   equivocado.
> - Esta regla aplica a todo: desarrollo, mantenimiento, backups, MCPs, scripts, CI local y
>   cualquier operación dentro del proyecto.

### Servicios (`docker-compose.yml`)

| Servicio | Contenedor | Rol |
|----------|-----------|-----|
| `wordpress` | `wp_bc` | WordPress (imagen `wp_bc:latest`, build desde `Dockerfile`). Publica en `http://localhost:8080` |
| `db` | `wp_bc_db` | MySQL 8.4. Volumen persistente `db_data`. Puerto host `3307` |
| `cli` | `wp_bc_cli` | WP-CLI. Ejecuta `scripts/dump-db.sh` cada 10 min como backup automático continuo |

- Config montada desde `config/`: `wp-config.php`, `.htaccess`, `php.ini`, `my.cnf`.
- Variables en `.env` (`DB_NAME=bc_wp`, `WP_HOME`, `WP_SITEURL`, credenciales).

### ⚠️ Regla de oro: NO TOCAR LA BASE DE DATOS

Ver `CLAUDE.md`. Nunca modificar/detener/reiniciar/migrar la DB sin: (1) dump de
verificación, (2) confirmar tamaño/contenido del dump, (3) autorización explícita paso a
paso. **Nunca** borrar `db-data/`/`db_data`. Si MySQL no arranca, reportar y esperar
instrucciones. Si los contenedores ya corren bien, **no tocarlos**.

---

## 3. Arquitectura general

```
┌──────────────────────── Podman (red wp_bc_default) ────────────────────────┐
│                                                                             │
│   wp_bc (WordPress) ──── wp_bc_db (MySQL 8.4)      wp_bc_cli (WP-CLI)        │
│        │                       ▲                        │ dump cada 10 min   │
│        │                       └────────────────────────┘                    │
│        │                                                                      │
│        ▼                                                                      │
│   Tema fork ve-theme  +  Plugins bc-*  (frontend + CPTs)                    │
└──────────────────────────────────────────────────────────────────────────┘
         │                                   │
         ▼                                   ▼
   BunnyCDN (media, única fuente)     MCPs: alejandria (RAG) · wordpress · bunnycdn
                                             ▲
                                   Pipeline de contenido (skills + scripts/ + corpus/)
```

Capas:

1. **Infraestructura** — Podman Compose (WordPress + MySQL + WP-CLI) + BunnyCDN para media.
2. **Frontend** — Tema fork `ve-theme` (PHP modular + pipeline SCSS, fork de GeneratePress 3.6.1).
3. **Dominio/contenido** — Plugins propios `bc-*` que registran CPTs, taxonomías y bloques.
4. **Pipeline IA** — Skills (`.claude/skills/`), scripts (`scripts/`) y corpus RAG (`corpus/`
   + MCP Alejandría) para producir contenido a escala.

---

## 4. Frontend — tema fork `ve-theme`

Fork de **GeneratePress 3.6.1** fusionado con el anterior child theme `generatepress-child`.
+ **Bootstrap 5** + **Font Awesome**, con un pipeline SCSS
propio. Principios (ver `CLAUDE.md` y `docs/sistema-layouts.md`):

- **Responsabilidad única**: un partial SCSS por componente; un archivo PHP por concern.
- `functions.php` es solo **orquestador**: hace `require_once` de ~33 módulos en `inc/`
  (`enqueue`, `performance`, `setup`, `og-tags`, `schema`, `breadcrumbs`, `share-bar`,
  `toc`, `glossary`, `persona`, `media`, `layout`, `scripture-taxonomy`, `location-redirect`,
  `block-patterns`, `block-styles`, `admin-columns`, etc.).
- **Pipeline SCSS**: `src/_*.scss` → Sass → Autoprefixer → cssnano → `style-compiled.css`
  (`npm run build` / `npm run dev`). Sin source maps en producción.
- **Performance**: critical CSS inline en `<head>`, CSS no crítico diferido
  (`media="print" onload`), carga condicional por tipo de página (`is_singular`,
  `is_archive`, …). Excepción: Font Awesome y Bootstrap en todo el sitio.
- **Sistema de diseño "Jardín"**: familias de color Arena/Tierra/Jardín centralizadas en
  `_variables.scss` y mapeadas a Bootstrap + `theme.json`. Detalle completo en
  `docs/sistema-layouts.md`.
- Templates propios: `single-bc_location.php`, `single-bc_quote_author.php`,
  `archive-bc_location.php`, `archive-bc_quote_author.php`, `front-page.php`,
  `page-glosario-temas.php`, `page-landing.php`, `taxonomy-*.php`, etc.

---

## 5. Modelo de dominio — plugins propios `bc-*`

| Plugin | Registra | Descripción |
|--------|----------|-------------|
| **bc-quote-block** | CPT `bc_quote_author` + taxonomía `bc_author_calling` + bloque de cita | Personas del glosario (líderes/testigos). Slug `glosario/persona`. Llamamientos SUD como términos |
| **bc-scripture-map** | CPT `bc_location` + bloque de mapa | Ubicaciones bíblicas con mapa interactivo de relieve 3D (**MapLibre GL**). Render server-side + JS en `build/` |
| **bc-content-organization** | Taxonomía jerárquica series/colecciones | Colecciones (padres) contienen series (hijos). Orden drag-and-drop + widget de serie |
| **bc-carbon-fields** | Campos meta de autor | Metadatos de `bc_quote_author` vía Carbon Fields |
| **lds-passage-block** | Bloque de pasajes | Inserta pasajes de escritura |
| **indigetal-media-offload-for-bunny-net** | (media) | Offload automático de media a BunnyCDN (ver §7) |

Otros plugins de terceros presentes: `ai`, `ai-provider-for-google/openai`, `akismet`,
`cache-enabler`, `merpress`, `bibcit-any2html`, `regenerate-thumbnails`, `visualize`.

### 5.1 Sistema de Glosarios

Hay **tres glosarios** bajo `/glosario/`, cada uno con su propia estructura:

| Glosario | Tipo | URL | Notas |
|----------|------|-----|-------|
| **Personas** | CPT `bc_quote_author` | `/glosario/persona/{slug}`, archivo del CPT | Ordenado por título. Title dinámico "La biografía de X" (`inc/persona.php`). Infobox con llamamiento, GA, tipo de testigo |
| **Ubicaciones** | CPT `bc_location` | single `/ubicacion/{slug}`, archivo `/ubicaciones/` | 15 metadatos (lat/lng, tipo, escrituras JSON, fechas, fuente, confianza, alias). Alias sin contenido se excluyen del archivo (`inc/glossary.php`) |
| **Temas** | Taxonomía | `/glosario/temas/` | Navegación por temas (`page-glosario-temas.php`, `inc/taxonomy-temas.php`) |

El menú principal ("Glosario" con hijos "Temas" y "Ubicaciones") se auto-provisiona en
`inc/glossary.php`. El autolinkado de glosario en el contenido está en `inc/auto-links.php`.

**Metadatos clave de `bc_location`** (`bc-scripture-map/inc/class-location-cpt.php`):
`_bc_loc_lat`, `_bc_loc_lng`, `_bc_loc_type`, `_bc_loc_icon`, `_bc_loc_scriptures` (JSON),
`_bc_loc_description`, `_bc_loc_date_from/to`, `_bc_loc_source`, `_bc_loc_confidence`,
`_bc_loc_order`, `_bc_loc_name_en`, `_bc_loc_disambiguation`, `_bc_loc_alt_names` (JSON),
`_bc_loc_alias_of` (ID de la ubicación canónica).

---

## 6. Pipeline de contenido asistido por IA

Es una parte distintiva del proyecto: el contenido se produce con un flujo humano + IA.

- **Skills** (`.claude/skills/`): `alejandria-search`, `asignar-capitulos`, `asignar-series`,
  `batch-bios`, `biografia-persona`, `conteo-bios`, `crear-articulo`,
  `glosario-ubicaciones-contenido`, `gutenbergize`, `imagenes-bc`, `merpress`,
  `optimizar-tags`, `traducir-ubicaciones`. Cada `SKILL.md` documenta su pipeline
  investigación → redacción → publicación.
- **Scripts** (`scripts/`, ~35): seeders (`seed-*.php`, `seed.sh`), importadores desde el
  knowledge graph (`populate-from-kg.sh`), fixers masivos (`fix-*.php`), backup/restore
  (`backup-db.sh`, `dump-db.sh`), utilidades de bios (`bio-stats.sh`, `publish-bio.sh`).
- **Corpus RAG** (`corpus/`): `biographical-encyclopedia`, `church-news`, `eom`,
  `personajes`, además de datos Wikidata/EOM en la raíz (`wikidata-bio.json`, etc.). Es la
  base de conocimiento que sirve el MCP **Alejandría**.
- **Normas editoriales**: `docs/normas-editoriales.md` (obligatorias: denominación correcta
  de la Iglesia, estructura de biografía, prosa narrativa, etc.).

---

## 7. Media — BunnyCDN (única fuente de verdad)

> **Regla absoluta:** la media **nunca** debe almacenarse localmente — **ni en el contenedor
> ni en el host**. La CDN es la **única** fuente de verdad. El plugin de BunnyCDN existe
> precisamente para garantizar esto.

- Nunca comprimir/optimizar/modificar imágenes localmente. El original vive en la Storage
  Zone. Si algo está mal, se corrige en el origen (admin de WordPress o script Python) y se
  re-sube a la CDN.
- El plugin `indigetal-media-offload-for-bunny-net` sube original + thumbnails a BunnyCDN al
  subir por el admin, y borra los locales. Estados en `_indigetal_offloaded`: `local`,
  `partial`, `complete`, `error`.
- No habilitar el CDN Optimizer (costo por request en el edge).

### Stack de media (verificado en vivo vía MCP bunnycdn)

| Componente | Detalle |
|-----------|---------|
| Storage Zone | `ve-media-storage` — región LA, replicada a NY. ~3083 archivos, ~202 MB |
| Pull Zone | `ve-pull-zone.b-cdn.net` (ID 6050116), ForceSSL, cache slice on |
| URL pattern | `https://ve-pull-zone.b-cdn.net/wp-content/uploads/{year}/{month}/{file}` |
| Credenciales | En `opencode.json` (`BUNNY_*`) y `CLAUDE.md`. **No commitear cambios que expongan claves nuevas** |

> Nota: la cuenta BunnyCDN también contiene una zona antigua `stage-zone` / pull zone
> `bcomentarios` (proyecto previo). La media de este sitio usa **`ve-media-storage`**.

---

## 8. MCPs (Model Context Protocol) — imprescindibles para operar

El proyecto define **tres** servidores MCP. Configurados en `opencode.json` (fuente
completa) y parcialmente en `.mcp.json`.

| MCP | Config | Transporte | Propósito | Estado observado |
|-----|--------|-----------|-----------|------------------|
| **alejandria** | `.mcp.json`, `opencode.json` | remote `http://localhost:4300/mcp/` | RAG del corpus: búsqueda híbrida/semántica, knowledge graph, genealogías, chat con citas | ⚠️ **Endpoint activo pero backend caído**: responde `"Tunnels are not started. Please .start() first!"`. Requiere iniciar los túneles del servicio Alejandría |
| **wordpress** | `.mcp.json`, `opencode.json` | local `docker run docdyhr/mcp-wordpress` (v3.3.15) en red `wp_bc_default` | Gestión de WordPress vía REST: ~70 tools `wp_*` (posts, pages, media, comments, categories, tags, users, SEO) con App Password | ✅ **OPERATIVO** (verificado end-to-end: auth + lectura de posts/usuario). Requirió dos correcciones: (1) `"timeout": 60000` en `opencode.json` porque el arranque tarda ~5-6 s; (2) mu-plugin `bc-app-passwords.php` para habilitar Application Passwords sobre HTTP en entorno `production` |
| **bunnycdn** | `opencode.json` | local `npx bunnycdn-mcp` | Gestión de CDN: storage zones, pull zones, purge, DNS, etc. | ✅ **Operativo** (verificado en vivo) |

### Checklist de validación de MCPs antes de operar

Los tres MCPs deben estar accesibles para la operación normal. Antes de una sesión de trabajo:

1. **bunnycdn** — probar `bunny_list_storage_zones` o `bunny_get_account`. Debe devolver
   `ve-media-storage`.
2. **alejandria** — probar `alejandria_corpus_status`. Si devuelve
   `"Tunnels are not started"`, el RAG **no está operativo**: iniciar los túneles del
   servicio Alejandría (ver el proyecto en `C:/own/alejandria/`, permitido en `opencode.json`)
   antes de cualquier tarea que dependa de investigación de corpus.
3. **wordpress** — verificar que las tools `wordpress_*` estén cargadas y que el contenedor
   `docdyhr/mcp-wordpress` levante en la red `wp_bc_default`. Si las tools no aparecen en el
   cliente, revisar que el MCP esté habilitado y que Podman pueda ejecutar `docker run`.
   **Nota de arranque lento:** el servidor tarda ~5-6 s en estar listo (connection test con
   timeout interno de 10 s). Si el cliente MCP se rinde antes, las tools no se registran; por
   eso `opencode.json` fija `"timeout": 60000` en la entrada `wordpress`. Tras editar la
   config, hay que **recargar los MCP** en el cliente (en Kilo, `/mcps` o reiniciar la sesión).
   Cada lanzamiento vía `docker run --rm` crea un contenedor efímero; si quedan huérfanos
   (`docker ps --filter ancestor=docdyhr/mcp-wordpress`), se pueden eliminar sin afectar la app.
   **Autenticación:** el sitio corre en entorno `production` sobre HTTP, donde WordPress core
   deshabilita Application Passwords. El mu-plugin `wp-content/mu-plugins/bc-app-passwords.php`
   reactiva esa capacidad (filtros `wp_is_application_passwords_available*`). Sin ese mu-plugin,
   toda petición autenticada devuelve `401 rest_not_logged_in` aunque las credenciales sean
   correctas. La App Password vive en `opencode.json`/`.mcp.json` (`WORDPRESS_APP_PASSWORD`).

> ⚠️ **Gap conocido:** no todos los clientes de IA cargan los tres MCPs por igual. Si faltan
> las tools de `wordpress` o `alejandria` está caído, gran parte del pipeline (crear/editar
> contenido, investigar en corpus) queda bloqueado. Validar SIEMPRE los tres al empezar.

---

## 9. Estructura del repositorio (mapa rápido)

| Ruta | Contenido |
|------|-----------|
| `docker-compose.yml`, `Dockerfile`, `config/` | Infraestructura Podman + config WP/PHP/MySQL |
| `.env`, `wp-config.php` (montado) | Variables de entorno y config de WordPress |
| `justfile` | Recetas de mantenimiento (asumen contenedores `wp_bc*`) |
| `wp-content/themes/ve-theme/` | Tema fork independiente (frontend, `inc/`, `src/`, templates) |
| `wp-content/themes/generatepress/` | GeneratePress 3.6.1 original (respaldo, sin tocar) |
| `wp-content/themes/generatepress-child/` | Child original (respaldo, sin tocar) |
| `wp-content/plugins/bc-*` | Plugins de dominio propios |
| `wp-content/uploads/` | **Debe estar vacío/efímero** — media vive en BunnyCDN |
| `.claude/skills/` | Skills del pipeline de contenido IA |
| `scripts/` | Seeders, importadores KG, fixers, backup/restore |
| `corpus/` | Base de conocimiento del RAG Alejandría |
| `docs/` | Documentación (este README, normas, layouts, lotes, restore-db, etc.) |
| `backups/`, `db-data/` | Dumps y datos MySQL — **no borrar** |
| `.mcp.json`, `opencode.json` | Definición de servidores MCP |
| `CLAUDE.md` | Filosofía de build + reglas de oro (DB, media) |
| `llms.txt` | Metadatos para crawlers IA — ⚠️ contiene nombre de sitio desactualizado |

---

## 10. Documentos relacionados

- `CLAUDE.md` — Filosofía de build, reglas de oro (DB y media/CDN).
- `docs/fork-ve-theme.md` — Documentación del fork de GeneratePress a ve-theme.
- `docs/sistema-layouts.md` — Sistema de diseño, tokens, tipografía, layouts, plan de fases.
- `docs/normas-editoriales.md` — Normas obligatorias de redacción.
- `docs/restore-db.md` — Procedimiento de restauración de base de datos.
- `docs/lotes-ubicaciones.md`, `docs/plan-lotes.md`, `docs/checklist-bios.md`,
  `docs/batch-thumbnails.md`, `docs/pendientes-traduccion-locations.md` — Estado y planes de
  los lotes de contenido.

---

## 11. Pendientes / TODOs de definición

- [x] ~~Confirmar el **nombre público real del sitio**~~ — Nombre confirmado: **Verdades Eternas**.
- [ ] Asegurar que el servicio **Alejandría** inicie sus túneles (hoy responde
      "Tunnels are not started").
- [x] MCP **wordpress**: **OPERATIVO**. Corregido con (1) `"timeout": 60000` en
      `opencode.json` (arranque lento del `docker run`) y (2) mu-plugin
      `wp-content/mu-plugins/bc-app-passwords.php` (Application Passwords sobre HTTP en
      entorno `production`). Verificado end-to-end.
