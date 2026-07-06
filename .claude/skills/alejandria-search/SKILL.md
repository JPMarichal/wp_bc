---
name: alejandria-search
description: >
  Buscar en Alejandría (biblioteca textual bilingüe ES/EN) desde wp_bc usando el
  MCP server en localhost:4300. Proporciona búsqueda semántica, textual (FTS) y
  consultas al Knowledge Graph (KG) de escrituras SUD, conferencias, biografías,
  manuales, EOM y literatura afín. Útil para enriquecer biografías, verificar
  datos históricos, encontrar relaciones entre personas/ lugares/ conceptos, y
  cruzar información del corpus de wp_bc con el corpus teológico de Alejandría.
---
# Skill: alejandria-search

Integración con el MCP server de **Alejandría** (`localhost:4300`).
Permite que el agente, trabajando desde el contexto de wp_bc, haga búsquedas
avanzadas y consulte el Knowledge Graph.

## MCP Tools Disponibles

| Tool | Uso |
|------|-----|
| `alejandria_search_text` | Búsqueda textual exacta (FTS5 — frases literales, keywords) |
| `alejandria_search_semantic` | Búsqueda semántica por embeddings (significado, no keywords) |
| `alejandria_search_hybrid` | Búsqueda híbrida textual + semántica fusionada con RRF |
| `alejandria_kg_find` | Buscar entidades (personas, lugares, conceptos) por nombre parcial |
| `alejandria_kg_profile` | Perfil completo de entidad (resumen, alias, pasajes clave, temas) |
| `alejandria_kg_relations` | Relaciones tipadas de una entidad (familia, profecía, autoría) |
| `alejandria_kg_neighbors` | Vecinos y aristas del grafo para una entidad |
| `alejandria_kg_docs` | Documentos que mencionan una entidad |
| `alejandria_kg_summary` | Estadísticas del KG |
| `alejandria_kg_genealogy_tree` | Árbol genealógico jerárquico (ancestros/descendientes) |
| `alejandria_kg_genealogy_path` | Camino genealógico entre dos personas |
| `alejandria_chat_ask` | Pipeline RAG completo (search + KG + rerank + respuesta LLM con citas) |
| `alejandria_chat_classify` | Clasificar complejidad de una pregunta |
| `alejandria_corpus_status` | Salud del sistema (documentos, vectores, grafo) |

## Estrategia general: búsqueda multi-tipo e iterativa

**Principio**: NO usar un solo tipo de búsqueda. Cada tipo encuentra cosas distintas.
La estrategia correcta es **lanzar varios tipos en paralelo** y luego sintetizar.

Para investigación biográfica, el workflow recomendado es:

1. **Broad primero**: buscar sin `source_filter` para descubrir qué existe en el corpus
2. **Narrow después**: repetir con `source_filter` en subcorpus prometedores
3. **Sintetizar**: combinar hallazgos de todos los tipos de búsqueda

### Búsqueda textual (`search_text`)
- FTS5 — encuentra frases literales y palabras exactas
- Ej: `"Spencer W. Kimball" "lengthen your stride"`
- Útil para: verificar citas textuales, localizar documentos que mencionan a una persona, buscar términos específicos
- **Limitación**: no encuentra conceptos si se expresan con otras palabras

### Búsqueda semántica (`search_semantic`)
- Embeddings — encuentra por significado, no por palabras exactas
- Ej: `"Spencer W. Kimball childhood youth education family"`
- Útil para: encontrar documentos conceptualmente relacionados aunque no mencionen el nombre exacto, descubrir fuentes inesperadas
- **Fortaleza**: encuentra lo que el FTS no ve (especialmente en corpus bilingüe)

### Búsqueda híbrida (`search_hybrid`)
- Combina FTS + embeddings con RRF (Reciprocal Rank Fusion)
- Acepta `text_weight` y `semantic_weight` (default 0.4 / 0.6)
- Útil para: búsqueda general cuando no sabes qué tipo usar
- **Recomendada como default para primera pasada**

### Knowledge Graph
- `kg_find`: resolver un nombre parcial o alternativo a entidad del KG
- `kg_profile`: resumen biográfico, alias, pasajes clave, menciones. **Atención: si el KG está en fase de enriquecimiento, puede devolver solo metadata-level** (sin resumen, sin relaciones). Eso es un dato válido — significa que la entidad está identificada pero el KG aún no tiene su perfil completo.
- `kg_relations`: conexiones familiares, proféticas, de autoría
- `kg_neighbors`: vecindario de una entidad en el grafo
- `kg_genealogy_tree`: árbol familiar jerárquico (dirección: up/down/both, profundidad configurable)
- `kg_genealogy_path`: ruta familiar entre dos personas
- `kg_docs`: documentos que mencionan una entidad

### Chat RAG (`chat_ask`)
- Pipeline completo: hybrid search → KG lookup → cross-references → reranking → respuesta LLM con citas
- Ideal para preguntas teológicas complejas que requieren síntesis multi-fuente
- **⚠️ Limitación conocida**: puede fallar con `"cannot unpack non-iterable NoneType object"` cuando el pipeline RAG interno encuentra un error. En ese caso, **recurrir a `search_hybrid`** con la misma pregunta como fallback.
- Preferir sobre búsquedas manuales cuando la pregunta es compleja

### `source_filter`: privilegiar sin restringir

`source_filter` permite limitar la búsqueda a un subcorpus (ej. `en/manuals/church-history-topics`). Útil cuando sabes que el subcorpus es relevante. Pero **no limitarse a un solo filter** — hacer también búsquedas sin filter para descubrir fuentes en otros subcorpus.

Ejemplo de estrategia multi-filter:
```
1. search_hybrid(query: "Spencer W. Kimball")  # sin filter — todo el corpus
2. search_text(query: "Spencer W. Kimball", source_filter: "es/manuals/church-history-topics")
3. search_text(query: "Spencer W. Kimball", source_filter: "es/magazines/liahona")
4. search_text(query: "Spencer W. Kimball", source_filter: "es/manuals/saints")
```

## Mapeo wp_bc → Alejandría

### Personajes (bc_quote_author / corpus/personajes/)
- El slug de wp_bc puede diferir del nombre en Alejandría
- Usar `kg_find` con el nombre real (no el slug) para encontrar la entidad
- Las biografías de Jenson (biographical-encyclopedia) existen en ambos proyectos

### Encyclopedia of Mormonism (corpus/eom/)
- Alejandría NO tiene EOM en su corpus, pero su KG sí cubre personas/eventos que aparecen en EOM
- Usar `search_hybrid` con el título del artículo EOM para encontrar contexto teológico relacionado

### Church News (corpus/church-news/)
- Alejandría no tiene church-news, pero su KG puede tener entidades mencionadas allí
- Usar `kg_find` con nombres de personas que aparecen en church-news

### Datos de Wikidata (corpus/personajes/*/wikidata.json)
- Alejandría usa su propio sistema de entidades (no QIDs de Wikidata directamente)
- Preferir `kg_find` sobre búsqueda por QID

## Ejemplos de uso

### Investigación biográfica completa (multi-estrategia)

Para una persona como Spencer W. Kimball, el workflow completo es:

**Fase A — Broad discovery (sin source_filter):**
```
1. alejandria_kg_find(query: "Spencer W. Kimball")
   → confirma que la entidad existe en el KG
2. alejandria_search_text(query: "Spencer W. Kimball", limit: 10)
   → descubre qué documentos del corpus lo mencionan
3. alejandria_search_semantic(query: "Spencer W. Kimball biography president")
   → encuentra fuentes semánticamente relacionadas
```

**Fase B — Narrow por subcorpus:**
```
4. alejandria_search_text(query: "Spencer W. Kimball",
     source_filter: "es/manuals/church-history-topics")
   → biografía estandarizada del tópico de historia de la Iglesia
5. alejandria_search_text(query: "Spencer W. Kimball",
     source_filter: "es/magazines")
   → artículos de Liahona con anécdotas y relatos personales
6. alejandria_search_text(query: "Spencer W. Kimball",
     source_filter: "es/manuals/saints")
   → referencia en Santos tomo 4 (narrativa histórica oficial)
```

**Fase C — Síntesis vía chat_ask (con fallback):**
```
7. alejandria_chat_ask(question: "What were Spencer W. Kimball's significant contributions?")
   → si falla (error conocido), usar search_hybrid como fallback
8. alejandria_search_hybrid(query: "Spencer W. Kimball contributions revelation 1978")
```

**Fase D — Verificación de datos:**
```
9. alejandria_search_text(query: "Spencer W. Kimball 1957 throat cancer")
   → verificar cronología de cirugías
10. alejandria_search_text(query: "Spencer W. Kimball Gila River flood 1941")
    → verificar datos históricos
```

### Enriquecer una biografía con contexto teológico
```
1. alejandria_kg_find(query: "Amasa Lyman")
2. alejandria_kg_profile(entity_name: "Amasa Lyman")
3. alejandria_kg_relations(name: "Amasa Lyman")
4. alejandria_search_hybrid(query: "Amasa Lyman apostasy atonement")
```

### Verificar un hecho histórico
```
1. alejandria_search_text(query: "Martin Harris lost 116 pages")
2. alejandria_kg_find(query: "Martin Harris")
3. alejandria_kg_docs(entity_name: "Martin Harris")
```

### Explorar conexiones entre personas
```
1. alejandria_kg_find(query: "Brigham Young")
2. alejandria_kg_neighbors(name: "Brigham Young")
```

### Genealogía para infobox (padres, cónyuges)
```
1. alejandria_kg_find(query: "John Taylor")
2. alejandria_kg_genealogy_tree(name: "John Taylor", direction: "up", depth: 1)
3. alejandria_kg_relations(name: "John Taylor", rel_types: ["SPOUSE_OF", "FATHER_OF", "MOTHER_OF"])
```

### Citas textuales para la sección "Fuentes consultadas"
```
1. alejandria_search_text(query: "John Taylor died Kaysville")
2. alejandria_chat_ask(question: "What did John Taylor say about the martyrdom of Joseph Smith?")
```

## Notas importantes
- Alejandría es **bilingüe (ES/EN)** — las queries en cualquiera de los dos idiomas funcionan
- El corpus de Alejandría prioriza fuentes SUD canónicas y autorizadas, NO incluye Wikipedia
- `source_filter` acepta prefijos como `en/scriptures/bom`, `en/manual`, `en/conference`, `es/conferencia`, `en/biographies`, `es/magazines`, `es/manuals/saints`
- **Siempre hacer múltiples tipos de búsqueda** (text + semantic + hybrid) — cada tipo encuentra documentos distintos
- **No limitarse a un solo `source_filter`** — hacer rondas con diferentes filters y también sin filter
- `chat_ask` puede fallar con error interno del pipeline RAG; tener `search_hybrid` como fallback preparado
- El KG puede estar en fase de enriquecimiento — si `kg_profile` devuelve solo `status: "metadata"`, es un dato válido (la entidad está identificada pero el perfil no está completo). Usar fuentes locales para los datos faltantes.
- Alejandría debe estar corriendo (`docker compose up` en `alejandria/docker/`) para que el MCP esté disponible
- El `kg_genealogy_path` es útil para encontrar conexiones familiares inesperadas entre personajes de wp_bc
- **Medir en palabras**, no en caracteres, cuando se reporten estadísticas de longitud de texto
