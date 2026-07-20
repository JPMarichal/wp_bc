---
name: asignar-series
description: |
  Organizar artículos (`post`) en colecciones y series usando el plugin
  `bc-content-organization`. Usar cuando se necesite: (1) crear series
  dentro de una colección, (2) asignar artículos a series, (3) establecer
  el orden de publicación dentro de una serie, (4) verificar la estructura
  jerárquica de colecciones y series. Complementa a asignar-tags y
  asignar-capitulos.
---

# Skill: Asignar Series a Artículos

## Cuándo usar
Cuando se necesite organizar artículos (`post`) en colecciones y series usando el plugin `bc-content-organization`. Incluye: crear series dentro de una colección, asignar artículos a series y establecer el orden de publicación.

## Arquitectura

El plugin `bc-content-organization` registra la taxonomía jerárquica `collection` con dos niveles:

```
Colección (parent=0, ej. "Sodoma y Gomorra")
  └── Serie (parent=collection_id, ej. "Arqueología de Sodoma")
        ├── Artículo 1 (_series_position=1)
        ├── Artículo 2 (_series_position=2)
        └── Artículo 3 (_series_position=3)
```

- **Colecciones** agrupan series. No se asignan artículos directamente a colecciones (solo a series).
- **Series** agrupan artículos. Son hijos de una colección (parent ≠ 0).
- **Posición**: cada artículo tiene `_series_position` (integer) para orden dentro de su serie.

## UI Administrativa

| Ruta | Propósito |
|:-----|:----------|
| `Posts → Colecciones` | Crear/editar colecciones y series (taxonomía) |
| `Posts → Organizar series` | Drag-and-drop para reordenar series y artículos |
| Editor de artículo (meta box) | Asignar colección, serie y posición |

## Cómo asignar un artículo a una serie (via WP-CLI)

```bash
# 1. Listar colecciones y series existentes
wp term list collection --fields=term_id,name,parent,count --format=table

# 2. Crear una serie (como hijo de una colección)
#    parent = term_id de la colección padre
wp term create collection "Nombre de la serie" --parent=<collection_id> --porcelain

# 3. Asignar artículo a la serie (reemplazando cualquier colección anterior)
wp post term set <post_id> collection <series_id>

# 4. Establecer posición dentro de la serie
wp post meta update <post_id> _series_position <position>
```

## Cómo verificar la estructura

```bash
# Ver todos los términos
wp term list collection --fields=term_id,name,parent,count --format=table

# Ver artículos de una serie (ordenados por posición)
wp post list --collection="<nombre serie>" --orderby=meta_value_num --meta_key=_series_position --fields=ID,post_title --format=table

# Ver colección/serie de un artículo
wp post term list <post_id> collection --fields=term_id,name,parent --format=table
```

## Reglas importantes

1. **Jerarquía máxima**: Colección → Serie. No se permiten sub-series ni colecciones anidadas.
2. **Un artículo pertenece a UNA sola serie**. Si se reasigna a otra serie, la anterior se reemplaza.
3. **La posición** debe ser un entero positivo ≥ 1. La UI de "Organizar series" permite reordenar con drag-and-drop.
4. **Widget de navegación**: El widget `BCCO_Frontend_Widget` ("Navegación de Serie") muestra la serie actual con navegación entre artículos. Se inyecta automáticamente en `sidebar-1` en artículos que pertenecen a una serie.
