---
name: asignar-tags
description: |
  Asignar etiquetas (tags) a artículos de wp_bc de forma consistente.
  Usar cuando se necesite: (1) asignar tags a un artículo nuevo o
  existente, (2) corregir tags incorrectos (IDs numéricos, nombres
  duplicados, tags huérfanos), (3) crear tags que no existen, (4)
  asegurar consistencia de nomenclatura dentro de una serie o colección.
  Complementa a asignar-capitulos y asignar-serie.
---

# Skill: Asignar Tags a Artículos

## Cuándo usar
- Al publicar un artículo nuevo y necesitar asignarle tags
- Al corregir artículos existentes con tags incorrectos
- Cuando un tag no existe y debe crearse
- Para mantener consistencia de nomenclatura en series

## Taxonomía

| Propiedad | Valor |
|-----------|-------|
| Taxonomy | `post_tag` |
| Post types | `post`, `bc_quote_author`, `bc_location` |
| Formato de nombres | lowercase, guiones medios, sin espacios |
| Ejemplos válidos | `libro-de-mormon`, `jose-smith`, `planchas-de-oro` |
| Ejemplos inválidos | `Libro de Mormón`, `Jose Smith`, `planchas de oro` |

## Pipeline

### Paso 1: Listar tags existentes

```bash
podman exec wp_bc_cli wp term list post_tag --fields=term_id,name,slug --format=table --allow-root
```

### Paso 2: Buscar si el tag existe

```bash
# Por nombre exacto
podman exec wp_bc_cli wp term list post_tag --search="libro-de-mormon" --fields=term_id,name,slug --format=table --allow-root

# Por slug
podman exec wp_bc_cli wp term list post_tag --slug="libro-de-mormon" --fields=term_id,name,slug --format=table --allow-root
```

### Paso 3: Crear tag si no existe

```bash
podman exec wp_bc_cli wp term create post_tag "libro-de-mormon" --slug=libro-de-mormon --allow-root
```

### Paso 4: Asignar tags al post

```bash
# Reemplaza todos los tags del post
podman exec wp_bc_cli wp post term set <POST_ID> post_tag <slug1> <slug2> <slug3> --allow-root

# Verificar asignación
podman exec wp_bc_cli wp post term list <POST_ID> post_tag --fields=term_id,name,slug --format=table --allow-root
```

## Reglas de nomenclatura

| Regla | Ejemplo |
|-------|---------|
| lowercase | `libro-de-mormon`, no `Libro de Mormón` |
| guiones medios | `jose-smith`, no `jose_smith` ni `josesmith` |
| sin acentos en slugs | `nuevo-testamento`, no `nuevotestamento` |
| nombres descriptivos | `traduccion-libro-mormon`, no `traduccion` |
| evitar siglas sin contexto | `bm` no, `libro-de-mormon` sí |

## Casos especiales

### Tags duplicados por ID numérico
Si `wp post term set` se usó con IDs numéricos en lugar de slugs, crea términos duplicados con nombres numéricos. Detectarlos:

```bash
podman exec wp_bc_cli wp term list post_tag --fields=term_id,name,slug --format=table --allow-root | grep -E "^[0-9]+\t[0-9]+\t[0-9]+$"
```

Eliminar duplicados:

```bash
podman exec wp_bc_cli wp term delete post_tag <TERM_ID> --allow-root
```

### Tags huérfanos
Verificar tags sin posts:

```bash
podman exec wp_bc_cli wp term list post_tag --fields=term_id,name,count --format=table --allow-root | grep "0$"
```

Considerar eliminarlos o reasignarlos.

### Nombres alternativos
Mantener consistencia dentro de series:
- `jose-smith` (no `joseph-smith`, no `josé-smith` con acento en slug)
- `libro-de-mormon` (no `libromormon`, no `lm`)
- `planchas-de-oro` (no `planchas_oro`)

## Checklist de validación

- [ ] Todos los tags usan slugs lowercase con guiones
- [ ] No hay tags duplicados por ID numérico
- [ ] No hay tags huérfanos (count=0) salvo intencionales
- [ ] La nomenclatura es consistente con tags existentes en la serie
- [ ] Cada post tiene al menos 1 tag y máximo 10 tags
