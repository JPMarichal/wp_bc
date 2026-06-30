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

### 2. Redactar biografía

Seguir estrictamente la **Plantilla de secciones** y **Principios de redacción**
del skill `biografia-persona` (ver `.claude/skills/biografia-persona/SKILL.md`).

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

Campos a poblar (verificar cada uno, solo los disponibles):
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
