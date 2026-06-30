---
name: conteo-bios
description: |
  Contar biografías completas y pendientes del CPT bc_quote_author.
  Proporciona un reporte tabular del estado de todas las personas, cuáles
  están completas (con post_content, metadatos, foto y comentarios abiertos)
  y cuáles tienen campos faltantes.
---

# Skill: conteo-bios

Reporta el estado de todas las biografías en el CPT `bc_quote_author`.

## Uso

```bash
scripts/bio-stats.sh
```

El script recorre todos los posts `bc_quote_author` y produce una tabla como esta:

```
Estado de biografías — bc_quote_author
=======================================
Completas:          42
Sin contenido:       5
Sin foto:            3
Sin padre/madre:     2
Comentarios cerrados: 1
Total personas:     47
```

## Qué verifica por post

| Check | Criterio |
|-------|----------|
| Contenido | `post_content` > 500 caracteres |
| Foto | `_thumbnail_id` existe y no es 0 |
| Padre/madre | `_author_father` y `_author_mother` no vacíos |
| Comentarios | `comment_status` = `open` |

Un post se considera **Completa** si pasa todos los checks.
