# Fork ve-theme — Tema independiente

## Origen

`ve-theme` es un **fork completo** de [GeneratePress](https://generatepress.com/) v**3.6.1**,
fusionado con las personalizaciones del anterior child theme `generatepress-child`.

Dejó de ser un child theme para ser un tema **standalone** (autónomo, sin dependencia de
GeneratePress como padre).

## Línea base

| Atributo | Valor |
|----------|-------|
| Tema base | GeneratePress 3.6.1 |
| Fecha del fork | 18-jul-2026 |
| Rama Git | `feat/alias-locations-map` |
| Commits del fork | `433f269` (creación), `5051140` (text domain) |
| Tags | `ve-theme/v1.0.0` — versión inicial del fork |

## ¿Qué incluye?

- **GeneratePress 3.6.1 completo** (templates, inc/, customizer, assets, theme.json)
- **Todas las personalizaciones del child** (34 archivos en `inc/`, pipeline SCSS propio,
  templates propios, critical CSS inline, CSS diferido)
- **`style.css`** con `Theme Name: ve-theme`, sin `Template:`, `Text Domain: ve-theme`

## Cambios sobre GP original

1. **`style.css`** — cabecera propia, sin herencia de parent.
2. **`functions.php`** — fusionado: los `require_once` de GP + los 34 del child.
3. **`inc/enqueue.php`** — handle `ve-theme`, sin dependencia de `generate-style`.
   Fix aplicado: desduplicación del filtro `style_loader_tag` (priority 10 y 11).
4. **`inc/media.php`** — `@package` actualizado a `ve-theme`.
5. **Text domain** — `'generatepress'` → `'ve-theme'` en 48 archivos PHP.
   El dominio `'bc'` (cadenas propias del child) se conserva intacto.
6. **Templates PHP** — todos los del child (header, footer, sidebar, front-page,
   content-single, comments, single-bc_location, single-bc_quote_author, etc.)
   sobrescriben a los de GP.
7. **Pipeline SCSS** — `src/*.scss` → `style-compiled.css` (Sass + Autoprefixer + cssnano).

## Mantenimiento

### Seguridad
Al ser un fork, **no recibe actualizaciones automáticas de seguridad de GeneratePress**.
Revisar periódicamente el changelog de GP y aplicar parches críticos manualmente.

### Proceso recomendado para mergear cambios de GP upstream

```bash
# Agregar GP como remote (una sola vez)
git remote add gp-upstream https://github.com/tomusborne/generatepress.git

# Fetch latest
git fetch gp-upstream

# Comparar cambios en un área específica
git diff gp-upstream/master -- wp-content/themes/generatepress/inc/structure/

# Aplicar manualmente los parches relevantes
```

### Tags
Los tags del fork usan el prefijo `ve-theme/`:
- `ve-theme/v1.0.0` — fork inicial
- `ve-theme/v1.x.x` — releases futuros

## Respaldos

Los temas originales se conservan como respaldo:
- `wp-content/themes/generatepress/` — GP 3.6.1 original (sin tocar)
- `wp-content/themes/generatepress-child/` — child original (sin tocar)
