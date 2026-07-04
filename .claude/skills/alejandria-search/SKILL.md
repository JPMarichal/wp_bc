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

Integración con el MCP server de **Alejandría** (`localhost:4300/mcp/`).
Permite que el agente, trabajando desde el contexto de wp_bc, haga búsquedas
avanzadas y consulte el Knowledge Graph.

## MCP Tools Disponibles

| Tool | Uso |
|------|-----|
| `mcp__alejandria__kg_relations` | Relaciones tipadas de una entidad (familia, profecía, autoría, etc.) |
| `mcp__alejandria__kg_profile` | Perfil completo de entidad (resumen, alias, pasajes clave, temas) |
| `mcp__alejandria__kg_find` | Buscar entidades por nombre parcial |
| `mcp__alejandria__search_hybrid` | Búsqueda híbrida en todo el corpus (textual + semántica fusionada) |
| `mcp__alejandria__search_text` | Búsqueda textual exacta (FTS5 — frases literales, keywords) |
| `mcp__alejandria__kg_neighbors` | Vecinos y aristas del grafo para una entidad |
| `mcp__alejandria__kg_docs` | Documentos que mencionan una entidad |
| `mcp__alejandria__kg_summary` | Estadísticas del KG |
| `mcp__alejandria__chat_ask` | Pipeline RAG completo (search + KG + rerank + respuesta LLM) |
| `mcp__alejandria__chat_classify` | Clasificar complejidad de una pregunta |
| `mcp__alejandria__corpus_status` | Salud del sistema (documentos, vectores, grafo) |

## Cuándo usar cada búsqueda

### Búsqueda textual (`search_text`)
- Cuando necesitas citas textuales, versículos exactos o frases literales
- Ej: `"king Benjamin" speech` o `"first vision" account`

### Búsqueda semántica/híbrida (`search_hybrid`)
- Cuando buscas conceptos o temas sin palabras exactas
- Ej: "what did early saints believe about gathering to Zion"
- El corpus incluye fuentes que el LLM no cubre bien (conferencias, manuales, biografías SUD)

### Knowledge Graph
- `kg_find`: cuando tienes un nombre parcial o alternativo y quieres el QID de Alejandría
- `kg_profile`: para obtener el resumen biográfico, alias, y pasajes clave de una persona
- `kg_relations`: para encontrar conexiones familiares, proféticas, de autoría, etc.
- `kg_neighbors`: para explorar el vecindario de una entidad en el grafo

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

### Enriquecer una biografía con contexto teológico
```
1. mcp__alejandria__kg_find(name: "Amasa Lyman")
2. mcp__alejandria__kg_profile(id: "<id_del_paso_1>")
3. mcp__alejandria__kg_relations(id: "<id>")
4. mcp__alejandria__search_hybrid(query: "Amasa Lyman apostasy atonement")
```

### Verificar un hecho histórico
```
1. mcp__alejandria__search_text(query: "Martin Harris lost 116 pages")
2. mcp__alejandria__kg_find(name: "Martin Harris")
3. mcp__alejandria__kg_docs(id: "<id>")
```

### Explorar conexiones entre personas
```
1. mcp__alejandria__kg_find(name: "Brigham Young")
2. mcp__alejandria__kg_neighbors(id: "<id>")
```

## Notas importantes
- Alejandría es **bilingüe (ES/EN)** — las queries en cualquiera de los dos idiomas funcionan
- El corpus de Alejandría prioriza fuentes SUD canónicas y autorizadas, NO incluye Wikipedia
- Para preguntas teológicas complejas, prefiere `chat_ask` sobre búsquedas manuales
- Alejandría debe estar corriendo (`docker compose up` en `alejandria/docker/`) para que el MCP esté disponible
