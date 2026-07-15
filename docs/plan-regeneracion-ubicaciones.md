# Plan de Regeneración Completa — Glosario de Ubicaciones

> Objetivo: regenerar todos los contenidos de las 1,349 ubicaciones
> no-alias desde cero, aplicando la columna `relevancia` y el nivel
> A/B/C pre-llenado, incorporando mejoras al template, investigación
> y estructura del contenido. El tracking se mantiene por SQLite.
>
> **NO se modifica el tracking existente: solo se marcan ubicaciones
> como `processing`/`completed`/`error`.**

---

## 1. Estado actual

- **1,349 ubicaciones** en `tracking/locations.db` (171 completadas, 1,178 pendientes)
- Columna `relevancia` pre-llenada:
  - Alta (3): 268
  - Media (2): 363
  - Baja (1): 718
- Lista curada `data/high-relevance-locations.json` con 91 wp_ids
- Skill actualizado: Fase 0c lee `relevancia` desde DB
- Infobox ya muestra relevancia antes de confianza
- **Orden de procesamiento**: Alta → Media → Baja, dentro de cada nivel por `wp_id` ASC

---

## 2. Mejoras al template

### 2.1 Datos clave al inicio del artículo (dentro del flujo)

Ubicación: después del cierre del hero (`</div>` del hero) y antes del párrafo de introducción.

Formato:
```html
<div class="bc-location-key-facts">
  <span class="bc-key-fact">
    <i class="fas fa-map-marker-alt"></i>
    <?php echo esc_html( $type_labels[ $type ] ?? 'Ubicación' ); ?>
  </span>
  <?php if ( $name_en ) : ?>
    <span class="bc-key-fact">
      <i class="fas fa-globe"></i>
      <?php echo esc_html( $name_en ); ?>
    </span>
  <?php endif; ?>
  <span class="bc-key-fact">
    <i class="fas fa-star"></i>
    Relevancia: <?php echo esc_html( $relevancia_labels[ $relevancia ] ); ?>
  </span>
  <span class="bc-key-fact">
    <i class="fas fa-book-open"></i>
    <?php echo esc_html( $ref_count ); ?> referencia<?php echo $ref_count === 1 ? '' : 's'; ?> escritural<?php echo $ref_count === 1 ? '' : 'es'; ?>
  </span>
</div>
```

CSS sugerido:
```css
.bc-location-key-facts {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
  align-items: center;
  margin-bottom: 1rem;
  padding: .6rem .8rem;
  background: rgba(30, 58, 95, .04);
  border-left: 3px solid #1e3a5f;
  border-radius: 0 4px 4px 0;
  font-size: .85rem;
  color: #444;
}
.bc-key-fact {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
}
.bc-key-fact i {
  color: #1e3a5f;
  font-size: .8rem;
}
```

Justificación: el infobox de la derecha es complementario; este bloque entrega contexto inmediato en el flujo de lectura principal, mejora la escaneabilidad y ayuda a motores de búsqueda a extraer hechos estructurados.

### 2.2 FAQ schema (solo Alta y Media)

Ubicación: después de `Referencias de las Escrituras`, dentro del artículo.

Reglas:
- Máximo 3 preguntas por ubicación
- Preguntas basadas en el contenido real, no genéricas
- Formato `<details><summary>Pregunta</summary><p>Respuesta</p></details>`
- JSON-LD embebido al final del artículo

```html
<div class="bc-faq" itemscope itemtype="https://schema.org/FAQPage">
  <h2>Preguntas frecuentes</h2>
  <details itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
    <summary itemprop="name">¿Dónde se encontraba [nombre]?</summary>
    <div itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">
      <p itemprop="text">Respuesta concisa...</p>
    </div>
  </details>
  ...
</div>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "¿Dónde se encontraba [nombre]?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Respuesta concisa..."
      }
    }
  ]
}
</script>
```

Condición: solo incluir si `relevancia >= 2`.

### 2.3 Mejoras adicionales al template

| Mejora | Descripción |
|--------|-------------|
| Schema.org `Place` | Mantener; añadir `additionalProperty` con relevancia y nivel |
| Tiempo de lectura | Añadir `<span class="bc-reading-time">` después del hero |
| Breadcrumbs | Ya existen; verificar incluyan `bc_location` |
| Imagen featured | Si no hay featured image, generar placeholder con icono de tipo |

---

## 3. Mejoras a la investigación

### 3.1 Plantillas de investigación por tipo

En lugar de una búsqueda genérica, usar plantillas específicas:

| Tipo | Enfoque de investigación |
|------|--------------------------|
| `city`, `settlement` | Fundación, menciones bíblicas, arqueología, situación actual |
| `region`, `wilderness` | Límites geográficos, eventos clave, importancia estratégica |
| `sea`, `river` | Geografía, eventos escriturales, simbolismo |
| `mountain` | Eventos clave, simbolismo, menciones |
| `landmark` | Descripción, eventos, significado doctrinal (DyC/JST si aplica) |

### 3.2 Checklist obligatoria por investigación

Para cada ubicación, documentar en `Fuentes consultadas`:

1. Alejandría (KG + search_text + chat_ask)
2. Una fuente web oficial SUD o churchofjesuschrist.org
3. Una enciclopedia bíblica (BibleHub o Wikipedia)
4. Si relevancia Alta/Media: al menos una fuente adicional (BYU, Maxwell, FAIR, etc.)

### 3.3 Scoring de calidad de fuentes

Priorizar fuentes en este orden (no es fallback, es scoring):
1. Escrituras + conocimiento revelado (DyC, JST, PGP, Libro de Mormón)
2. Alejandría (corpus propio)
3. Fuentes oficiales SUD (churchofjesuschrist.org, lds.org)
4. Diccionarios bíblicos académicos (Holman, Smith, Easton)
5. Wikipedia (español/inglés)
6. Otras fuentes académicas (BYU RSC, Maxwell, FAIR)

---

## 4. Mejoras al tratamiento del contenido

### 4.1 Estructura de introducción

Regla estricta: **un solo `<p>` sin `<h2>`**. Este párrafo debe contener:
- Definición de qué es la ubicación
- Dónde se encuentra
- Su relevancia principal (1–2 frases)
- Palabra clave principal para SEO

Ejemplo:
```html
<p>Jerusalén es la ciudad más mencionada en las Escrituras y el centro
geográfico y espiritual de la historia del convenio. Ubicada en los
montes de Judea, ha sido el escenario de los momentos más decisivos
de la historia bíblica: desde el sacrificio de Isaac hasta la
expiación y resurrección de Jesucristo.</p>
```

### 4.2 Módulos obligatorios vs. opcionales

| Nivel | Obligatorios | Opcionales |
|-------|--------------|------------|
| A | Etimología, Geografía, Historia en Escrituras, Lecciones, Situación actual | Origen temprano, Historia postbíblica, Arqueología |
| B | Etimología, Historia en Escrituras, Lecciones, Situación actual | Geografía, Arqueología |
| C | Historia en Escrituras, Lecciones, Situación actual | Etimología, Geografía |

### 4.3 Integración de conocimiento revelado

- **No** crear sección separada "Perla de Gran Precio" o "Doctrina de la Restauración"
- Integrar menciones donde sea más oportuno:
  - Egipto: Abraham 1:23 en "Origen e historia temprana"
  - Jerusalén: Lehi (1 Nefi) en "Historia en las Escrituras"
  - Cumorah: Moroni en "Arqueología y evidencia"
- Usar nombres españoles de libros de la Restauración: 1 Nefi, DyC, JST, etc.

### 4.4 FAQ por nivel

| Nivel | FAQ |
|-------|-----|
| A | 3 preguntas (dónde, por qué relevante, situación actual) |
| B | 2 preguntas (dónde, por qué relevante) |
| C | Sin FAQ (demasiado contenido para poco texto) |

---

## 5. Tracking por SQLite

### 5.1 Estado actual

Tabla `locations`:
- `wp_id` (PK)
- `title`
- `name_en`
- `level` (A/B/C)
- `relevancia` (1/2/3)
- `batch_id`
- `status` (pending/processing/completed/error)
- `word_count`
- `error`
- `created_at`
- `updated_at`

### 5.2 Cambios propuestos para tracking

| Cambio | Descripción |
|--------|-------------|
| Nueva columna `rewritten` | BOOLEAN/TEXT para marcar si requiere regeneración |
| Nueva columna `regeneration_reason` | TEXT: "new_relevance", "content_quality", "full_refresh" |
| Índice en `status + relevancia` | Para consultas rápidas de pendientes por relevancia |

**No se modifica la estructura existente hasta después de aprobación.**

### 5.3 Tracking de regeneración

```python
def mark_for_regeneration(wp_id, reason="new_relevance"):
    conn = get_conn()
    conn.execute(
        "UPDATE locations SET status='pending', rewritten='yes', regeneration_reason=?, updated_at=datetime('now') WHERE wp_id=?",
        (reason, wp_id),
    )
    conn.commit()
    conn.close()

def get_regeneration_queue(batch_size=10, relevance_filter=None):
    conn = get_conn()
    query = "SELECT * FROM locations WHERE status='pending'"
    params = []
    if relevance_filter:
        query += " AND relevancia=?"
        params.append(relevance_filter)
    query += " ORDER BY relevancia DESC, wp_id ASC LIMIT ?"
    params.append(batch_size)
    cur = conn.execute(query, params)
    rows = cur.fetchall()
    conn.close()
    return [dict(r) for r in rows]
```

---

## 6. Pipeline de regeneración

### 6.1 Orden de procesamiento

1. **Alta (3)** primero: 189 ubicaciones pendientes → **19 lotes**
2. **Media (2)** segundo: 310 ubicaciones pendientes → **31 lotes**
3. **Baja (1)** último: 680 ubicaciones pendientes → **68 lotes**

Dentro de cada nivel, orden por `wp_id` ASC.

**Total: 118 lotes** de 10 ubicaciones cada uno.

### 6.2 Lotes

- **Tamaño**: 10 ubicaciones por lote
- **Tracking**: crear batch en SQLite, marcar ubicaciones como `processing`
- **Paralelismo**: 2 agentes por lote, 5 ubicaciones cada uno

### 6.3 Flujo por ubicación

```
Fase 0: Leer relevancia desde SQLite → derivar nivel
Fase 0b: Validar tipo + alias
Fase 1: Investigación exhaustiva (plantilla por tipo)
Fase 2: Redactar contenido (datos clave + intro + módulos + FAQ si aplica + fuentes + Forma T)
Fase 3: Publicar (wp post update)
Fase 4: Verificar (word count, formato HTML, presencia de FAQ si corresponde)
Fase 5: Marcar completed en SQLite
```

### 6.4 Comandos

```bash
# Crear batch
python -c "
import tracking.tracker as t
b = t.create_batch()
ids = [5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
t.add_locations(b, ids)
print(f'Batch {b} created')
"

# Marcar completed
python -c "
import tracking.tracker as t
t.mark_completed(5, 1200)
"

# Stats
python tracking/tracker.py stats
```

---

## 7. Archivos a modificar/crear

| Archivo | Acción |
|---------|--------|
| `docs/plan-regeneracion-ubicaciones.md` | Crear (este documento) |
| `wp-content/themes/generatepress-child/single-bc_location.php` | Añadir datos clave + FAQ schema |
| `wp-content/themes/generatepress-child/inc/high-relevance-locations.php` | Ya existe |
| `.claude/skills/glosario-ubicaciones-contenido/SKILL.md` | Actualizar Fase 2 con datos clave + FAQ |
| `tracking/tracker.py` | Añadir `mark_for_regeneration`, `get_regeneration_queue` |
| `tracking/schema.sql` | Añadir columnas `rewritten`, `regeneration_reason` |
| `data/high-relevance-locations.json` | Ya existe |

---

## 8. Criterios de aceptación

- [ ] Template muestra datos clave en todas las ubicaciones
- [ ] FAQ schema presente en todas las ubicaciones Alta/Media
- [ ] Contenido regenerado respeta nivel A/B/C según relevancia
- [ ] Tracking SQLite marca correctamente cada batch
- [ ] No se pierde progreso de batches existentes
- [ ] FAQ solo en Alta/Media, máximo 3 preguntas

---

## 9. Riesgos y mitigaciones

| Riesgo | Mitigación |
|--------|------------|
| Regenerar contenido existente puede romper enlaces internos | Usar mismo slug; verificar enlaces tras publicación |
| FAQ schema inválido | Validar JSON-LD antes de publicar |
| Tiempo de generación alto | Procesar en orden de relevancia; Alta primero |
| Inconsistencia entre DB y WP | Usar siempre `wp_id` como referencia; batch completo antes de commit |

---

## 10. Próximos pasos

1. Aprobar este plan
2. Modificar template (`single-bc_location.php`)
3. Actualizar skill (`SKILL.md`) con nuevas reglas de contenido
4. Actualizar `tracker.py` y `schema.sql`
5. Ejecutar regeneración por lotes de 10, Alta → Media → Baja
6. Verificar en producción
7. Commit y push
