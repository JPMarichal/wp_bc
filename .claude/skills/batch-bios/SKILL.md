---
name: batch-bios
description: |
  Completar biografías del CPT bc_quote_author por lotes de 5.
  Para cada persona del lote: leer fuentes del corpus, redactar biografía
  según la plantilla, guardar HTML, publicar, importar foto, poblar
  metadatos del infobox, abrir comentarios y verificar integridad.
  Genera reporte final del lote.
---

# Skill: batch-bios

Completa N biografías del CPT `bc_quote_author` en un solo lote.

## Selección de candidatos

Priorizar personas que tengan en `corpus/personajes/<slug>/`:
- `ldsorg.html` (fuente prioritaria)
- `wikidata.json` con `qid` válido (para foto)
- `wikipedia.html` (fuente complementaria)

Verificar estado actual con `scripts/bio-stats.sh` o `scripts/verify-bio.sh <ID>`.

## Resolución batch en Alejandría (exhaustiva, por persona)

Antes de procesar cada persona individualmente, consultar Alejandría de forma
**exhaustiva**, no restrictiva. Usar **todos** los tipos de búsqueda disponibles,
sin limitarse a consultas predefinidas ni a unos pocos subcorpus.

El objetivo es descubrir **todo** lo que el corpus tiene sobre esa persona,
en cualquier subcorpus. No hay "suficiente" — cada persona es distinta y cada
corpus puede aportar información única.

Por cada persona del lote, lanzar:

```
1. alejandria_kg_find(query: "<nombre real>")
   → resolver entidad en el KG

2. alejandria_search_text(query: "<nombre real>")
   → sin source_filter, sin limit restrictivo — descubrir menciones en TODO el corpus

3. alejandria_search_semantic(query: "<nombre real>")
   → encontrar fuentes que el FTS no captura

4. alejandria_search_hybrid(query: "<nombre real>")
   → combinación de ambas

5. alejandria_kg_profile(entity_name: "<nombre>")
   → perfil biográfico si existe

6. alejandria_kg_relations(name: "<nombre>")
   → todas las relaciones conocidas

7. alejandria_kg_genealogy_tree(name: "<nombre>", direction: "up")
   → ancestros

8. alejandria_kg_genealogy_tree(name: "<nombre>", direction: "down")
   → descendientes
```

Además, usar `alejandria_chat_ask` con preguntas abiertas para explorar lo
que el corpus sabe de la persona. Si falla, usar `search_hybrid` como fallback.

El `entity_name` del KG se usará en consultas posteriores durante el
procesamiento individual. Los resultados textuales y semánticos revelan en
qué subcorpus hay material — pero **no pre-seleccionar subcorpus** en esta
fase; la exploración es abierta.

## Pipeline por persona

Para cada persona del lote, ejecutar en orden:

### 1. Leer fuentes del corpus

```
corpus/personajes/<slug>/
├── ldsorg.html              # prioridad 1
├── chd.html                 # prioridad 2
├── biographical-encyclopedia/  # prioridad 3
├── wikipedia.html           # prioridad 4
└── wikidata.json            # qid, image, datos básicos
```

### 1b. Consultar Alejandría (enriquecimiento profundo)

Usar el `entity_name` resuelto en el paso batch (o resolver ahora si no se
hizo antes). Seguir la estrategia multi-fase del skill `biografia-persona`:

**Fase A — Broad discovery (sin source_filter):**
```
alejandria_kg_profile(entity_name: "<Nombre>")
  → resumen biográfico, alias, pasajes clave (puede ser metadata-level)

alejandria_search_text(query: "<Nombre>", limit: 5)
  → descubrir qué documentos lo mencionan en todo el corpus

alejandria_search_semantic(query: "<Nombre> biography contribution")
  → fuentes semánticamente relacionadas
```

**Fase B — Narrow por subcorpus:**
```
alejandria_search_text(query: "<Nombre>",
  source_filter: "es/manuals/church-history-topics")
alejandria_search_text(query: "<Nombre>",
  source_filter: "es/magazines")
alejandria_search_text(query: "<Nombre>",
  source_filter: "es/manuals/saints")
```

**Fase C — Síntesis:**
```
alejandria_chat_ask(question: "¿Qué enseñó/hizo <Nombre> relevante?")
  → si falla, usar search_hybrid como fallback
```

**Fase D — Genealogía:**
```
alejandria_kg_relations(name: "<Nombre>",
    rel_types: ["FATHER_OF","MOTHER_OF","SPOUSE_OF"])
alejandria_kg_genealogy_tree(name: "<Nombre>", direction: "up", depth: 1)
alejandria_kg_genealogy_tree(name: "<Nombre>", direction: "down", depth: 1)
```

Los resultados de Alejandría son **complementarios** a las fuentes locales.
No reemplazan a `ldsorg.html` ni a `biographical-encyclopedia`.
Ver skill `alejandria-search` para detalles de cada tool.

### 2. Redactar biografía

Seguir estrictamente la **Plantilla de secciones** y **Principios de redacción**
del skill `biografia-persona` (ver `.claude/skills/biografia-persona/SKILL.md`).

Reportar estadísticas en **palabras** (`wc -w`), no en caracteres.

Secciones:
- Apertura (1-2 párrafos, sin subtítulo)
- Primeros años y conversión
- Familia
- Misiones y servicio eclesiástico
- Obra y contribuciones
- Pruebas y desafíos
- Sus últimos días
- Fuentes consultadas

### 3. Guardar HTML en uploads

```bash
cat > wp-content/uploads/bio-<slug>.html << 'BIO_EOF'
... contenido HTML ...
BIO_EOF
```

El archivo desde el host es accesible en el contenedor como:
`/var/www/html/wp-content/uploads/bio-<slug>.html`

### 4. Publicar biografía

```bash
scripts/publish-bio.sh <ID> /var/www/html/wp-content/uploads/bio-<slug>.html "<excerpt>"
```

### 5. Importar foto

```bash
scripts/import-photo.sh <ID> <QID> "<Nombre>"
```

### 6. Poblar metadatos del infobox

Campos a poblar (verificar cada uno, solo los disponibles). Para padres y
cónyuges, cotejar contra los resultados de `alejandria_kg_relations` y
`alejandria_kg_genealogy_tree` obtenidos en el paso 1b:
- `_author_description`
- `_author_is_ga` (1 si fue Autoridad General)
- `_author_birth_date`
- `_author_birth_place`
- `_author_death_date`
- `_author_death_place`
- `_author_nationality`
- `_author_father`
- `_author_mother`
- `_author_spouses` (JSON array)
- `_author_callings` (JSON array)
- `_author_witness_type` (solo para Testigos)

### 7. Verificar

```bash
scripts/verify-bio.sh <ID>
```

### 8. Reporte de persona

Registrar resultado: éxitos, fallas, checks pendientes.

## Reporte final del lote

Al terminar el lote, presentar tabla:

| Persona | ID | Contenido | Foto | Metadatos | Comentarios | Estado |
|---------|----|-----------|------|-----------|-------------|--------|
| ...     |    | ✓/✗       | ✓/✗  | ✓/✗       | ✓/✗         | OK/FAIL|

Ejecutar `scripts/bio-stats.sh` para ver el estado global actualizado.
