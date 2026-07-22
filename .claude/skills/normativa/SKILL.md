---
name: normativa
description: |
  Consultar la normativa del proyecto wp_bc de forma rápida y fiable.
  Usar cuando se necesite verificar reglas de búsqueda, citas, formato,
  estilo editorial, contenedores, deployment, o cualquier convención
  del proyecto antes de actuar. Evita adivinar: toda acción sensible
  debe estar respaldada por una regla documentada.
---

# Skill: Normativa del Proyecto

## Cuándo usar
- Antes de ejecutar búsquedas en Alejandría o web
- Antes de atribuir citas o referencias
- Antes de publicar contenido o modificar archivos
- Cuando haya duda sobre convenciones de estilo, formato o proceso
- Antes de crear/modificar skills, scripts o configuración

## Fuentes de normativa (orden de consulta)

### 1. Skills específicas del proyecto
Cada skill define reglas vinculantes para su dominio:

| Skill | Dominio |
|-------|---------|
| `glosario-ubicaciones-contenido` | Contenido de ubicaciones bíblicas |
| `asignar-capitulos` | Taxonomía bc_chapter |
| `asignar-tags` | Tags de artículos |
| `asignar-serie` | Categorías/series |
| `biografia-persona` | Biografías de personas |
| `batch-bios` | Procesamiento por lotes de biografías |
| `conteo-bios` | Conteo de biografías |
| `merpress` | Diagramas Mermaid |
| `gutenbergize` | Bloques Gutenberg |
| `imagenes-bc` | Imágenes destacadas |
| `traducir-ubicaciones` | Traducción de nombres de ubicaciones |
| `aplicar-citaciones` | Formato de citas bibliográficas (FCD) |
| `crear-editar-posts` | Creación y edición de artículos con todas las normas editoriales |

### 2. Documentos base del proyecto
| Documento | Ruta | Contenido |
|-----------|------|-----------|
| Filosofía de construcción | `CLAUDE.md` | Reglas CSS, PHP, Sass, media, container tooling |
| Normas editoriales | `docs/normas-editoriales.md` | Estilo, citas, estructura de artículos |
| Plan editorial | `docs/rv60/plan.md` | Series, artículos, estructura de colecciones |
| Plan de lotes | `docs/plan-lotes.md` | Progreso de biografías |

### 3. Correcciones acumuladas
| Documento | Ruta | Contenido |
|-----------|------|-----------|
| Correcciones | `corrections.md` | Reglas estrictas surgidas de errores previos |

### 4. Correcciones acumuladas
| Documento | Ruta | Contenido |
|-----------|------|-----------|
| Correcciones | `corrections.md` | Reglas estrictas surgidas de errores previos |

## Pipeline de consulta

```
1. Identificar el dominio de la duda
2. Buscar skill específica primero
3. Si no existe skill, buscar en docs/normas-editoriales.md
4. Si aplica, buscar en corrections.md
5. Si aún hay duda, buscar en memoria del proyecto
6. Si no se encuentra normativa, preguntar al usuario
```

## Reglas de oro

| Regla | Aplicación |
|-------|------------|
| **No adivinar** | Si no hay normativa clara, preguntar antes de actuar |
| **Una sola regla por consulta** | No mezclar dominios en una misma búsqueda |
| **Citar la fuente** | Toda acción basada en normativa debe poder señalar la regla exacta |
| **Actualizar la normativa** | Si se detecta un vacío o error, documentarlo en `corrections.md` o en la skill correspondiente |

## Ejemplo de uso

**Caso:** Necesito saber si puedo buscar "Tres Testigos Ocho Testigos" juntos en Alejandría.

```
1. Dominio: búsqueda en Alejandría
2. Skill: glosario-ubicaciones-contenido
3. Regla encontrada: "Queries ESTRECHAS ABSOLUTAS: un solo concepto por búsqueda"
4. Decisión: Buscar primero "Tres Testigos", luego "Ocho Testigos" como queries separadas.
```
