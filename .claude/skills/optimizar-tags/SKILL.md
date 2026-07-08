# Skill: Optimizar Tags de Artículos

## Cuándo usar
Cuando se necesite analizar un artículo existente y recomendarle los mejores tags (`post_tag`) para maximizar:
- **Relación temática**: que los tags representen fielmente el contenido
- **SEO**: que ayuden a motores de búsqueda a entender el tema
- **Interlinking**: que conecten artículos relacionados entre sí

## Pipeline

```
1. Leer el artículo completo (título + contenido)
2. Analizar contenido vs. tags existentes
3. Consultar corpus de Alejandría para identificar conceptos/entidades presentes
4. Cruzar con tags ya existentes en el sitio
5. Recomendar tags óptimos con justificación
6. Aplicar via WP-CLI
```

## Paso 1: Leer el artículo

```bash
wp post list --post_type=post --search="<título o ID>" --field=ID --format=ids
wp post get <ID> --field=post_content --format=content | head -c 5000
wp post term list <ID> post_tag --fields=term_id,name,slug --format=table
```

## Paso 2: Analizar contenido

Extraer del artículo:
- **Tema principal**: ¿de qué trata el artículo en una frase?
- **Personajes/figuras**: ¿qué personas se mencionan? (Abraham, Moisés, José Smith, etc.)
- **Lugares**: ¿qué ubicaciones geográficas? (Sodoma, Egipto, Jerusalén, etc.)
- **Conceptos teológicos**: ¿qué doctrinas o principios? (expiación, convenio, fe, etc.)
- **Eventos**: ¿qué sucesos históricos o escriturarios? (destrucción de Sodoma, éxodo, etc.)
- **Términos especializados**: ¿qué términos únicos del contenido?

## Paso 3: Consultar Alejandría

Usar `alejandria_kg_find` y `alejandria_search_text` para identificar las entidades presentes y sus relaciones. Esto permite:
- Confirmar que los conceptos existen en el corpus del sitio
- Descubrir entidades relacionadas que podrían ser buenos tags
- Encontrar conexiones inesperadas que enriquezcan el interlinking

## Paso 4: Cruzar con tags existentes

```bash
# Ver todos los tags existentes (ordenados por popularidad)
wp tag list --orderby=count --order=desc --fields=name,count,slug --format=table
```

### Categorías de tags

Identificar en qué categoría cae cada tag candidato:

| Categoría | Ejemplos | Prioridad |
|-----------|----------|-----------|
| **Doctrina** | expiación, gracia, fe, arrepentimiento | Alta |
| **Personaje** | Abraham, Moisés, Pablo, José Smith | Alta |
| **Lugar** | Sodoma, Egipto, Jerusalén, Cumorah | Alta |
| **Evento** | destrucción de Sodoma, éxodo, visita del ángel Moroni | Alta |
| **Libro/Pasaje** | Génesis, Éxodo, Apocalipsis | Media |
| **Tema** | profecía, tipología, convenio Abraham, poligamia | Alta |
| **Colección** | Sodoma y Gomorra, Historia SUD | Baja |

## Paso 5: Recomendación de tags

### Reglas

1. **Mínimo 3, máximo 8 tags** por artículo.
2. **Siempre priorizar tags existentes** sobre crear nuevos. Solo crear tag nuevo si no existe ninguno que capture el concepto.
3. **Un tag por concepto**, no combinar conceptos en un tag ("Sodoma y Gomorra" está bien porque es un topónimo compuesto; "fe y obras" no, separar en "fe" y "obras").
4. **SEO**: el tag debe reflejar el término de búsqueda probable. Preferir términos que un lector buscaría.
5. **Interlinking**: preferir tags con 3-20 artículos ya asignados. Tags con 0 artículos (nuevos) crearán aislamiento; tags con >50 artículos se saturan y no aportan interlinking diferenciado.
6. **No duplicar la categoría**: si el artículo ya pertenece a una categoría o colección que se llama igual que un tag candidato, evaluar si el tag aporta valor adicional.
7. **Específico > genérico**: "destrucción de Sodoma" > "destrucción", "convenio abrahámico" > "convenio".

### Formato de recomendación

Para cada tag recomendar:

```
+ [tag name] (existente, X artículos) — justificación breve
+ [tag name] (NUEVO) — justificación breve
- [tag name] (existente) — por qué quitarlo si estaba mal asignado
```

## Paso 6: Aplicar tags

```bash
# Reemplazar todos los tags de un artículo
wp post term set <post_id> post_tag <tag_id_1> <tag_id_2> ... <tag_id_n>

# O agregar tags sin perder los existentes
CURRENT=$(wp post term list <post_id> post_tag --field=term_id --format=csv)
wp post term set <post_id> post_tag $CURRENT,<nuevo_tag_id>

# Ver resultado
wp post term list <post_id> post_tag --fields=term_id,name,slug --format=table
```

## Verificación post-aplicación

```bash
# Confirmar que los tags se asignaron
wp post term list <post_id> post_tag --fields=name --format=csv

# Verificar que el artículo aparece al buscar por cada tag
wp post list --tag=<tag_slug> --post_type=post --fields=ID,post_title --format=table
```

## Notas importantes

- Los tags son **no jerárquicos**. No confundir con la taxonomía `collection` que sí es jerárquica.
- No usar tags para:
  - El título mismo del artículo (es obvio)
  - Términos demasiado genéricos ("introducción", "conclusión", "análisis")
  - El nombre del autor
- Sí usar tags para: conceptos que un lector buscaría para encontrar contenido relacionado.
