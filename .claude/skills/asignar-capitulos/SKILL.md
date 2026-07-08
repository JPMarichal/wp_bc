# Skill: Asignar Capítulos de las Escrituras a Artículos

## Cuándo usar
Cuando un artículo mencione, cite o haga referencia a pasajes de las Escrituras y necesite asignarle los términos de la taxonomía `bc_chapter` correspondientes.

## Arquitectura de bc_chapter

Taxonomía jerárquica con dos niveles:

```
Libro (parent=0, URL: /libro/genesis/)
  └── Capítulo (parent=libro_id, URL: /capitulo/genesis-19/)
```

| Propiedad | Valor |
|-----------|-------|
| Taxonomy | `bc_chapter` |
| Post types | `post`, `bc_quote_author` |
| URL libro | `/libro/{slug}/` |
| URL capítulo | `/capitulo/{slug}/` |

Los slugs se forman como:
- **AT/NT**: libro-capitulo (ej. `genesis-19`, `mateo-5`)
- **DyC**: `dyc-{numero}` (ej. `dyc-133`)
- **Perla de Gran Precio**: slug completo (ej. `moises-1`, `abraham-3`, `js-h-1`, `fe-1`)
- **Libros**: slug del libro (ej. `genesis`, `1-nefi`)

## Pipeline

```
1. Leer el artículo completo
2. Identificar TODAS las referencias escriturarias (explícitas e implícitas)
3. Mapear cada referencia al término bc_chapter correspondiente
4. Validar contra Alejandría si hay dudas
5. Aplicar via WP-CLI
6. Verificar en frontend
```

## Paso 1: Leer el artículo

```bash
wp post get <ID> --field=post_content --format=content | head -c 10000
wp post term list <ID> bc_chapter --fields=term_id,name,parent,slug --format=table
```

## Paso 2: Identificar referencias escriturarias

### Patrones de referencia explícita

Buscar en el contenido estos patrones (regex):

| Patrón | Ejemplo | Captura |
|--------|---------|---------|
| `Génesis \d+` | "Génesis 19" | libro=Génesis, cap=19 |
| `Gn \d+` | "Gn 19" | libro=Génesis, cap=19 |
| `Éxodo \d+` | "Éxodo 20" | libro=Éxodo, cap=20 |
| `Ex \d+` | "Ex 20" | libro=Éxodo, cap=20 |
| `Levítico \d+` | "Levítico 18" | libro=Levítico |
| `Lv \d+` | "Lv 18" | libro=Levítico |
| `Números \d+` | "Números 14" | libro=Números |
| `Nm \d+` | "Nm 14" | libro=Números |
| `Deuteronomio \d+` | "Deuteronomio 6" | libro=Deuteronomio |
| `Dt \d+` | "Dt 6" | libro=Deuteronomio |
| `Mateo \d+` | "Mateo 5" | libro=Mateo |
| `Mr \d+` | "Mr 5" | libro=Mateo |
| `Juan \d+` | "Juan 3" | libro=Juan |
| `Hechos \d+` | "Hechos 7" | libro=Hechos |
| `Romanos \d+` | "Romanos 8" | libro=Romanos |
| `Apocalipsis \d+` | "Apocalipsis 12" | libro=Apocalipsis |
| `Ap \d+` | "Ap 12" | libro=Apocalipsis |
| `1 Nefi \d+` | "1 Nefi 3" | libro=1 Nefi |
| `2 Nefi \d+` | "2 Nefi 2" | libro=2 Nefi |
| `Mosíah \d+` | "Mosíah 3" | libro=Mosíah |
| `Alma \d+` | "Alma 32" | libro=Alma |
| `3 Nefi \d+` | "3 Nefi 11" | libro=3 Nefi |
| `Doctrina y Convenios \d+` | "Doctrina y Convenios 133" | libro=DyC |
| `DyC \d+` | "DyC 133" | capítulo=dyc-133 |
| `D. y C. \d+` | "D. y C. 133" | capítulo=dyc-133 |
| `sección \d+` (en contexto DyC) | "sección 133" | capítulo=dyc-133 |
| `Moisés \d+` | "Moisés 1" | libro=Moisés (PGP) |
| `Abraham \d+` | "Abraham 3" | libro=Abraham (PGP) |
| `José Smith—\w+ \d+` | "José Smith—Historia 1" | libro=JS-H |

### Referencias implícitas

También buscar:
- **Citas textuales**: "escrito está: «No solo de pan vivirá el hombre»" → Mateo 4:4, Deuteronomio 8:3
- **Alusiones**: "como en los días de Noé" → Génesis 6-8
- **Paráfrasis**: "Pablo enseñó a los romanos que..." → Romanos
- **Personajes que narrativamente pertenecen a un capítulo**: si se habla extensamente de la destrucción de Sodoma → Génesis 19

## Paso 3: Mapear cada referencia al término bc_chapter

### Buscar los términos existentes

```bash
# Buscar libros
wp term list bc_chapter --search="Génesis" --fields=term_id,name,slug,parent --format=table

# Buscar capítulos específicos (name__like busca parcial)
wp term list bc_chapter --search="19" --fields=term_id,name,slug,parent --format=table

# Ver estructura completa de un libro
PARENT_ID=$(wp term list bc_chapter --search="Génesis" --field=term_id --format=ids)
wp term list bc_chapter --parent=$PARENT_ID --fields=term_id,name,slug --format=table
```

### Reglas de mapeo

| Referencia en artículo | Slug del capítulo |
|------------------------|-------------------|
| Génesis 19 | `genesis-19` |
| Génesis 19:1-13 | `genesis-19` |
| Génesis 19:1, 5, 24-28 | `genesis-19` |
| Gn 19 | `genesis-19` |
| DyC 133 | `dyc-133` |
| Doctrina y Convenios 133 | `dyc-133` |
| 1 Nefi 3 | `1-nefi-3` |

**Importante**: asignar el **capítulo**, no el versículo. La taxonomía es a nivel de capítulo. Si un artículo menciona versículos de diferentes capítulos, asignar todos los capítulos relevantes.

### Casos especiales

- **Rango de capítulos** (ej. "Génesis 18-19"): asignar ambos capítulos
- **Libro completo** (ej. "el libro de Mormón enseña..."): no asignar capítulos a menos que se mencionen específicamente
- **Referencia vaga** (ej. "como dice Pablo"): solo si el contexto permite identificar el capítulo
- **Múltiples referencias**: asignar todos los capítulos identificados (máximo recomendado: 10 capítulos por artículo)

## Paso 4: Validar contra Alejandría

Si hay duda sobre si un pasaje o referencia es correcta:

```
alejandria_search_text — buscar el pasaje textualmente
alejandria_kg_find — buscar la entidad (persona, lugar)
alejandria_chat_ask — preguntar "¿En qué capítulo de Génesis aparece la destrucción de Sodoma?"
```

## Paso 5: Aplicar términos

```bash
# Asignar capítulos (reemplaza cualquier asignación anterior de bc_chapter)
wp post term set <post_id> bc_chapter <term_id_1> <term_id_2>

# O agregar sin perder los anteriores
CURRENT=$(wp post term list <post_id> bc_chapter --field=term_id --format=csv)
wp post term set <post_id> bc_chapter $CURRENT,<term_id_n>

# Verificar
wp post term list <post_id> bc_chapter --fields=term_id,name,parent,slug --format=table
```

## Paso 6: Verificar en frontend

1. Abrir `/{slug-del-articulo}/`
2. Confirmar que al final del artículo aparece "Capítulos referenciados" con los capítulos asignados
3. Hacer clic en el enlace del capítulo → debe llevar a `/capitulo/{slug}/`
4. Confirmar que el artículo aparece listado en la página del capítulo

## Notas importantes

1. **No sobre-asignar**: solo asignar capítulos que el artículo realmente referencie de manera significativa. Una mención al pasar no justifica la asignación.
2. **Precisión sobre cantidad**: es mejor 2-3 capítulos bien identificados que 10 dudosos.
3. **Versículos sueltos**: si el artículo menciona un versículo específico, asignar el capítulo que lo contiene.
4. **Contexto del capítulo**: si el artículo trata un tema que corresponde claramente al contenido de un capítulo aunque no lo cite explícitamente (ej. un artículo sobre la Creación → Génesis 1), se puede asignar justificándolo.
5. **No asignar el libro como capítulo**: la taxonomía usa los capítulos (hijos), no los libros (padres). Si solo se menciona el libro, no asignar bc_chapter.
