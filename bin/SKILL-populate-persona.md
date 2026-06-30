# Skill: populate-persona

Completa datos biográficos de una persona (CPT `bc_quote_author`) desde el corpus local.

## Dependencias

| Herramienta | Uso |
|-------------|-----|
| `es` (Everything) | Ubicar carpeta de la persona en el corpus |
| `rg` (ripgrep) | Extraer datos de archivos HTML |
| `docker` | Ejecutar wp-cli en `wp_bc_cli` |
| `wp-cli` | Actualizar post meta en WordPress |

## Uso

```bash
./bin/populate-persona.sh <slug|ID|nombre>
```

### Ejemplos

```bash
# Por slug
./bin/populate-persona.sh michael-cziesla

# Por ID numérico
./bin/populate-persona.sh 3089

# Por nombre (busca en título)
./bin/populate-persona.sh "Michael Cziesla"
```

## Flujo

1. Resuelve `post_id` desde el argumento
2. Obtiene el `slug` del post
3. Busca la carpeta `corpus/personajes/<slug>/` con `es` o directo
4. Parsea `ldsorg.html` (fuente primaria: churchofjesuschrist.org)
5. Parsea `wikipedia.html` si existe (fuente secundaria)
6. Extrae: birth_date, birth_place, spouse, description, calling, og:image
7. Traduce fechas (EN → ES) y descripción/llamamiento (EN → ES)
8. Descarga imagen destacada desde URL `og:image`
9. Actualiza meta en WordPress vía `wp-cli`
10. Sincroniza términos de taxonomía `bc_author_calling`
11. Sincroniza meta raw (backward compat con JSON antiguo)

## Campos que completa

| Meta key | Tipo | Origen |
|----------|------|--------|
| `_author_description` | text | Rol extraído del texto ("was sustained as a/an X") + traducción EN→ES |
| `_author_birth_date` | text | `<dd>` infobox o "born in X on Y" |
| `_author_birth_place` | text | `<dd>` infobox o "born in X on Y" |
| `_author_death_date` | text | Desde infobox |
| `_author_death_place` | text | Desde infobox |
| `_author_nationality` | text | Inferido de birth_place |
| `_author_is_ga` | checkbox | Si description contiene "General Authority" |
| `_author_witness_type` | select | Si description menciona "three/eight witnesses" |
| `_author_spouses` | complex | Desde "married X in Y" en párrafo |
| `_author_callings` | complex | Desde lista de llamamientos en ldsorg |
| `_thumbnail_id` | image | Desde `og:image` meta tag de ldsorg.html |

## Notas

- Los datos se toman de `ldsorg.html` (Church website) como fuente primaria.
- `wikipedia.html` se usa como complemento si ldsorg no tiene suficiente info.
- Las fechas en inglés se traducen automáticamente al español.
- El llamamiento/descripción se traduce EN→ES con `translate_calling()`.
- Los países se traducen a nacionalidades (Germany → Alemana, etc.).
- La imagen destacada se descarga desde `og:image` URL e importa con `wp media import`.
- Los campos complejos (spouses, callings) se guardan con Carbon Fields y también como JSON raw para compatibilidad.
- Usa `MSYS_NO_PATHCONV=1` internamente en comandos Docker para evitar traducción de rutas en Git Bash.
