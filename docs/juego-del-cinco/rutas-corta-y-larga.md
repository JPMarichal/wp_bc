# Rutas de notación

## 1. Ruta corta (tradicional)

Volumen → Libro → Capítulo → Versículos

Ejemplo: `Juan 3:16-17`

| Nivel | Valor |
|:------|:------|
| Volumen | Nuevo Testamento |
| Libro | Juan |
| Capítulo | 3 |
| Versículos | 16–17 |

Es el lenguaje del usuario común. `Juan 3:16-17`, `DyC 9:15`, `Moisés 7:18`.

---

## 2. Ruta larga (clasificador interno)

Volumen → División → Libro → Parte → Capítulo → Perícopa → Versículos

| Nivel | Descripción | Ejemplo |
|:------|:------------|:--------|
| **Volumen** | Uno de los 5 volúmenes canónicos | Nuevo Testamento |
| **División** | Agrupación de libros dentro del volumen | Los evangelios |
| **Libro** | Libro individual | Mateo |
| **Parte** | Sección temática dentro del libro | La preparación de Jesucristo |
| **Capítulo** | Capítulo numerado | 1 |
| **Perícopa** | Unidad narrativa con nombre dentro del capítulo | La genealogía de Jesucristo |
| **Versículos** | Versículo(s) continuos (inicial y final) | 12 |

Ejemplo completo:
`NT / Los evangelios / Mateo / La preparación de Jesucristo / 1 / La genealogía de Jesucristo / 12`

La ruta larga opera como **clasificador interno del sistema** (wp_bc u otro).
Los niveles responden a la pregunta: **"¿dónde está esto?"**. Son estrictos,
disjuntos y ordenados: cada libro pertenece a una sola división, cada capítulo
a una sola parte, cada versículo a una sola perícopa.

---

## 3. Relación entre ambas rutas

La ruta corta es una **proyección** de la ruta larga. No es un camino
independiente: se obtiene omitiendo los niveles intermedios y navegando la
cadena jerárquica hacia arriba.

| Larga → Corta | Niveles omitidos |
|:--------------|:-----------------|
| Volumen → ~~División~~ → Libro → ~~Parte~~ → Capítulo → ~~Perícopa~~ → Versículos | División, Parte, Perícopa |

En base de datos solo se modela la ruta larga; la corta se deriva.

---

## 4. Pasaje (No estricto)

El **pasaje** es el único nivel con naturaleza **M:N** (muchos a muchos).
Responde a la pregunta **"¿de qué estoy hablando?"**. A diferencia de los
clasificadores, se solapa libremente:

> Se puede mencionar `Juan 3:16`, `Juan 3:16-17` y `Juan 3:15-18` en una
> sola conversación, con significados distintos, e incluso el mismo pasaje
> con significados distintos. Eso no pasa con los clasificadores.

---

## 5. Mapeo a base de datos

### 5.1 Los versículos (nivel atómico)

El **versículo** es la unidad mínima de contenido. Se almacena en una **tabla
personalizada** `wp_bc_versiculos`:

```sql
wp_bc_versiculos
├── id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
├── chapter_id    BIGINT          (FK → wp_term_taxonomy.term_id de bc_chapter)
├── numero        INT UNSIGNED    (número del versículo dentro del capítulo)
├── contenido     LONGTEXT        (texto completo del versículo)
├── created_at    DATETIME
└── updated_at    DATETIME

INDEX (chapter_id, numero) UNIQUE
INDEX (chapter_id)
```

| Atributo | Descripción | Ejemplo |
|:---------|:------------|:--------|
| `id` | PK autogenerada | 1 |
| `chapter_id` | FK al término de `bc_chapter` | term_id de "Génesis 19" |
| `numero` | Número del versículo | 16 |
| `contenido` | Texto completo del versículo (RV60) | "Y cuando el sol salió sobre la tierra..." |

**Relaciones**:
- 1:N con capítulo (1 capítulo → N versículos)
- Perícopas y pasajes referencian versículos por rango (`v_inicio`, `v_fin`) sin FK directa.
- No es CPT, no es post, no es term, no es meta: es una tabla plana para datos estructurados masivos.

**¿Por qué tabla propia y no term_meta?**
| Razón | Explicación |
|:------|:------------|
| Volumen de datos | ~42.000 filas (vs. <1500 en cualquier otro nivel) |
| Contenido pesado | `LONGTEXT` con el texto completo del versículo |
| Full-text search | Se necesita `MATCH ... AGAINST` para buscar en el contenido bíblico; `wp_termmeta` no tiene índices FULLTEXT |
| Consultas por rango | `WHERE numero BETWEEN v_inicio AND v_fin` es más eficiente en tabla plana que en meta |

### 5.2 Metadatos de otros niveles (term_meta nativo)

Para volumen, división, libro, parte, perícopa —niveles con <1500 filas,
metadatos clave-valor y sin búsqueda por contenido— se usa `term_meta`
nativo (`get_term_meta` / `update_term_meta`). Ejemplo para libro:

```php
add_term_meta($libro_id, 'autor', 'Moisés');
add_term_meta($libro_id, 'fecha_redaccion', '~1446 a.C.');
add_term_meta($libro_id, 'objetivo', 'Relatar la creación y el pacto con Israel');
```

| Ventaja | Por qué aplica |
|:--------|:---------------|
| Sin dependencias | Core de WP, sin vendor |
| Eficiente para <1500 filas | Las meta queries no escalan a 35.000, pero a 88 libros son instantáneas |
| WP-CLI nativo | `wp term meta list <taxonomy> <term_id>` |
| Patrón existente | Ya se usa en `collection` con `_series_order` |
| Extensible a futuro | Si se necesita UI admin, Carbon Fields la da gratis sobre term_meta

### 5.3 Clasificadores — relaciones jerárquicas estrictas (1:N)

Cada nivel tiene un solo padre y contiene muchos hijos, sin solapamiento:

| Nivel | Tipo de dato | Dependencia |
|:------|:-------------|:------------|
| Volumen | taxonomía (`bc_volumen`) | — |
| División | taxonomía (`bc_division`) | → volumen |
| Libro | taxonomía (`bc_libro`) | → división |
| Parte | taxonomía (`bc_parte`) | → libro (con rango de capítulos) |
| Capítulo | taxonomía (`bc_chapter`, existente) | → parte |
| Perícopa | taxonomía (`bc_pericopa`) | → capítulo (term meta `v_inicio`, `v_fin`) |
| Versículo | tabla personalizada `wp_bc_versiculos` | → capítulo por `chapter_id`

### 5.4 Pasaje — relación libre (M:N)

| Tabla | Propósito |
|:------|:----------|
| `bc_pasajes` | Definición del pasaje (`id`, `nombre`, `slug`) |
| `bc_pasaje_versiculos` | Tabla puente (`pasaje_id`, `chapter_id`, `v_inicio`, `v_fin`) |

Un mismo versículo puede aparecer en múltiples pasajes. A diferencia de la
perícopa (que es estricta y no se solapa), el pasaje es libre.

### 5.5 En WordPress

Las taxonomías son **independientes** (cada una tiene su propio `parent`
interno). No se puede usar `parent` entre taxonomías distintas, así que
la relación entre niveles se maneja con una closure table.

#### Taxonomías nuevas

| Taxonomía | Nivel | Jerarquía interna |
|:----------|:------|:------------------|
| `bc_volumen` | Volumen | `parent=0` |
| `bc_division` | División | `parent=0` |
| `bc_libro` | Libro | `parent=0` |
| `bc_parte` | Parte | `parent=0` |
| `bc_chapter` | Capítulo | libro → capítulo (existente, 2 niveles) |
| `bc_pericopa` | Perícopa | `parent=0`, con term meta `v_inicio` y `v_fin` |

#### Closure table: `wp_bc_scripture_closure`

Conecta todas las taxonomías en una sola tabla:

```sql
wp_bc_scripture_closure
├── ancestor_id         BIGINT
├── ancestor_taxonomy   VARCHAR(32)
├── descendant_id       BIGINT
├── descendant_taxonomy VARCHAR(32)
├── depth               INT UNSIGNED
└── PRIMARY KEY (ancestor_id, descendant_id, ancestor_taxonomy, descendant_taxonomy)
```

Cada par ancestro-descendiente es una fila:

| ancestor | descendant | depth |
|----------|-----------|-------|
| NT (bc_volumen) | NT (self) | 0 |
| NT (bc_volumen) | Evangelios (bc_division) | 1 |
| NT (bc_volumen) | Mateo (bc_libro) | 2 |
| NT (bc_volumen) | Mateo 1 (bc_chapter) | 4 |
| Mateo (bc_libro) | Mateo 1 (bc_chapter) | 2 |

**Subir (ruta corta)**: `WHERE descendant_id = ? AND descendant_taxonomy = 'bc_chapter' ORDER BY depth DESC` → 1 query.

**Bajar (ruta larga)**: `WHERE ancestor_id = ? AND ancestor_taxonomy = 'bc_volumen' ORDER BY depth ASC` → 1 query.

La closure se seedea una sola vez (catálogo fijo). Sin hooks de mantenimiento.

#### Pasaje

Tabla personalizada `wp_bc_pasajes` + tabla puente `wp_bc_pasaje_versiculos`.
Las taxonomías no permiten solapamiento, por eso se usa tabla aparte.

---

## 6. Arquitectura de implementación

### Decisiones tomadas

| Aspecto | Decisión | Razón |
|:--------|:---------|:------|
| Metadatos de entidades de ruta | `term_meta` nativo (`get_term_meta`/`update_term_meta`) | Sin dependencias extra, ya es el patrón del proyecto (`_series_order` en `collection`), liviano, compatible con WP-CLI |
| Framework de campos | Carbon Fields no es necesario | Su ventaja es UI admin; los catálogos se seedean una vez y rara vez se editan |
| Relaciones entre niveles | Closure table `wp_bc_scripture_closure` | Consulta única para subir o bajar la ruta completa; índices directos |
| Mantenimiento de la closure | Seed one-time (sin hooks de sync) | Catálogo fijo (88 libros, 5 volúmenes, 1674 capítulos) — no crece |

### Costo estimado: ~1 semana

---

## 7. Acciones pendientes

- [x] Agregar `bc_location` a los post types registrados en la taxonomía
      `bc_chapter` (ve-theme y generatepress-child). Hecho.
- [ ] Crear las taxonomías de la ruta larga en WordPress:
      `bc_volumen`, `bc_division`, `bc_libro`, `bc_parte`, `bc_pericopa`, con
      `hierarchical=true`.
- [ ] Crear la closure table `wp_bc_scripture_closure`.
- [ ] Poblar las 5 taxonomías nuevas con los términos definidos en
      `el-juego-del-cinco.md` (secciones 4 y 5).
- [ ] Seedear la closure table con todas las relaciones ancestro-descendiente
      (one-time, sin hooks de mantenimiento).
