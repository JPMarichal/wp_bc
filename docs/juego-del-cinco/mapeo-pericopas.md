# Mapeo de Perícopas (bc_pericopa)

> **Estado**: COMPLETADO — Se finalizó la Fase A con el diseño completo del mapa de pericopas de Doctrina y Convenios, la optimización y validación estricta de sus slugs para SEO de manera unívoca, la creación de la taxonomía `bc_pericopa` en código y base de datos con campos REST de metadatos, y el poblamiento exitoso de las 644 perícopas correspondientes. listo para iniciar la Fase B (Construcción del Frontend, Carga Condicional y Visualización).

## Definición

La **perícopa** es la unidad narrativa con nombre dentro de un capítulo.
Nivel más granular de la ruta larga, justo por encima del versículo:

Volumen → División → Libro → Parte → Capítulo → **Perícopa** → Versículos

| Atributo | Descripción |
|:---------|:------------|
| Taxonomía | `bc_pericopa`, `hierarchical=false` (parent=0) |
| Slug | `{libro}-{capitulo}-{slug-de-la-pericopa}` ej. `genesis-19-dos-angeles-visitan-lot` |
| Metadata | `v_inicio` (INT), `v_fin` (INT) — rango de versículos dentro del capítulo |
| Relación | 1:N con capítulo (1 capítulo → N perícopas) |
| Restricción | **Estricta, disjunta, sin solapamiento**: cada versículo pertenece a una sola perícopa |
| Excepción | Perícopas que cruzan capítulos se **particionan** por límite de capítulo (ver sección Casos Edge) |

## Criterios de nombrado

1. **Español**: nombres en español latinoamericano neutro.
2. **Slugs limpios**: sin artículos, preposiciones ni signos. Máximo 60 caracteres para el slug completo.
3. **Univocidad global**: el nombre de la perícopa debe ser unívoco **en todos los volúmenes**, no solo dentro del capítulo. Esto se logra mediante el sistema de dos niveles (ver sección siguiente).
4. **Consistentes**: misma persona/lugar/evento tiene mismo nombre en todas las perícopas.
5. **Descriptivos**: nombran la acción o escena, no solo el actor.
   - ✅ «Los dos ángeles visitan a Lot»
   - ❌ «Ángeles y Lot»

## Sistema de nombramiento para univocidad global y concordancia

### El problema

Un mismo evento aparece en múltiples libros. Si nombramos igual la perícopa en cada libro, los slugs colisionan; si nombramos distinto, perdemos el rastro de la concordancia. Ejemplos:

| Evento | Mateo | Marcos | Lucas | Juan | 3 Nefi |
|:-------|:-----|:-------|:------|:-----|:-------|
| La hija de Jairo | 9:18–26 | 5:21–43 | 8:40–56 | — | — |
| El Sermón del Monte | 5–7 | — | 6:17–49 | — | 12–14 |
| Jesús anda sobre el mar | 14:22–33 | 6:45–52 | — | 6:16–21 | — |
| El bautismo de Jesús | 3:13–17 | 1:9–11 | 3:21–22 | — | — |
| La primera visión de José Smith | — | — | — | — | — (JS—H 1) |

Además, la concordancia entre volúmenes es aún más amplia: Isaías 53 se cita en Mosíah 14; el Éxodo se tipifica en el ministerio de Cristo; Apocalipsis se refleja en DyC 77; etc.

### Distinción fundamental: título vs slug

| Elemento | Propósito | Ejemplo |
|:---------|:-----------|:--------|
| **Título** (WP `name`) | Nombre legible del evento canónico. **Puro**: sin referencia a libro, capítulo ni rango de versículos. | «Jesús resucita a la hija de Jairo» |
| **Slug** (WP `slug`) | Identificador URL único global. Incluye libro y capítulo para garantizar univocidad. | `mateo-9-jesus-resucita-a-la-hija-de-jairo` |
| **Descripción** (WP `description`) | Rango de versículos referenciado. | «Mateo 9:18–26» |
| **`_evento_canonico`** (WP `termmeta`) | ID del evento canónico maestro para agrupar concordancias. | `sanidad-hija-de-jairo` |

El título **nunca** lleva paréntesis ni referencia libresca. El libro, capítulo y rango no forman parte del título: se infieren del slug, de la descripción, o del contexto de navegación (breadcrumb, término padre, etc.).

### Solución: sistema de dos niveles con catálogo de eventos canónicos

#### Nivel 1 — Perícopa local (slug + título)

Cada perícopa tiene:

- **Título**: nombre puro del evento canónico. Para el mismo evento en diferentes libros, el título es **exactamente el mismo**.
- **Slug**: `{libro}-{capitulo}-{nombre-del-evento}`. La parte `{nombre-del-evento}` es idéntica a la del título normalizada a slug.

| Título | Slug | ¿Unívoco? |
|:-------|:-----|:----------|
| Jesús resucita a la hija de Jairo | `mateo-9-jesus-resucita-a-la-hija-de-jairo` | ✅ El slug es único por el prefijo `mateo-9-` |
| Jesús resucita a la hija de Jairo | `marcos-5-jesus-resucita-a-la-hija-de-jairo` | ✅ `marcos-5-` desambigua |
| Jesús resucita a la hija de Jairo | `lucas-8-jesus-resucita-a-la-hija-de-jairo` | ✅ `lucas-8-` desambigua |

El título idéntico en los tres casos es **intencional**: es el mecanismo para que las concordancias sean evidentes a simple vista.

#### Nivel 2 — Catálogo de eventos canónicos (`_evento_canonico`)

Es un catálogo maestro de ~300–400 eventos/enseñanzas únicos que aparecen en las 5 obras canónicas. Cada entrada tiene:

| Campo | Ejemplo |
|:------|:--------|
| **ID** (slug del evento) | `sanidad-hija-de-jairo` |
| **Título canónico** | «Jesús resucita a la hija de Jairo» |
| **Categoría** | milagro, enseñanza, profecía, acontecimiento, parábola |
| **Perícopas asignadas** | `mateo-9-jesus-resucita-a-la-hija-de-jairo`, `marcos-5-jesus-resucita-a-la-hija-de-jairo`, `lucas-8-jesus-resucita-a-la-hija-de-jairo` |
| **Referencias principales** | Mt 9:18–26; Mc 5:21–43; Lc 8:40–56 |
| **Referencias secundarias** | 3 Ne 26:15 (alusión general al ministerio de Cristo) |

En WordPress, esto se implementa como metadata `_evento_canonico` en cada término `bc_pericopa`, que contiene el ID del evento canónico al que pertenece.

**Para qué sirve:**
- **Concordancias**: un query de todos los términos con `_evento_canonico = sanidad-hija-de-jairo` devuelve los 3 slugs y sus rangos de versículos.
- **Homologación AT↔NT**: la Pascua hebrea (Éxodo 12) y la crucifixión de Cristo comparten un evento canónico `pascua-redencion`.
- **Homologación Biblia↔LdM**: el sermón del templo (3 Ne 12–14) comparte evento canónico `sermon-del-monte` con Mt 5–7.
- **Homologación DyC↔Apocalipsis**: DyC 77 comparte evento canónico `vision-del-cordero` con Ap 5–6.

#### Particiones para perícopas que cruzan capítulos

Cuando una perícopa mayor cruza capítulos, cada partición hereda el título base **sin sufijo**, pero el slug lleva sufijo `-p{numero}`. **Todas apuntan al mismo evento canónico**:

| Título | Slug | Evento canónico |
|:-------|:-----|:----------------|
| El Sermón del Monte | `mateo-5-sermon-del-monte-p1` | `sermon-del-monte` |
| El Sermón del Monte | `mateo-6-sermon-del-monte-p2` | `sermon-del-monte` |
| El Sermón del Monte | `mateo-7-sermon-del-monte-p3` | `sermon-del-monte` |
| El Sermón en el Templo | `3-nefi-12-sermon-en-el-templo-p1` | `sermon-del-monte` |
| El Sermón en el Templo | `3-nefi-13-sermon-en-el-templo-p2` | `sermon-del-monte` |
| El Sermón en el Templo | `3-nefi-14-sermon-en-el-templo-p3` | `sermon-del-monte` |

Notar que aquí el título es **distinto** («del Monte» vs «en el Templo») porque son contextos geográficos y de audiencia diferentes. El catálogo de eventos canónicos es quien declara la equivalencia (`_evento_canonico = sermon-del-monte` para ambos).

### Ejemplo completo: rastreo de una concordancia

Tomemos el caso de la Pascua:

| Título | Slug | Evento canónico |
|:-------|:-----|:----------------|
| La primera Pascua | `exodo-12-la-primera-pascua` | `pascua-redencion` |
| La Última Cena | `mateo-26-la-ultima-cena` | `pascua-redencion` |
| La Última Cena | `marcos-14-la-ultima-cena` | `pascua-redencion` |
| La Última Cena | `lucas-22-la-ultima-cena` | `pascua-redencion` |
| Cristo, nuestra Pascua | `1-corintios-5-cristo-nuestra-pascua` | `pascua-redencion` |
| El sacramento instituido | `3-nefi-18-el-sacramento-instituido` | `pascua-redencion` |
| La redención de Enoc | `moses-7-la-redencion-de-enoc` | `pascua-redencion` |

Un usuario que navega Éxodo 12 puede ver, a través del evento canónico, que esa perícopa tiene concordancias en NT, LdM y PGP. Navegando a cualquiera de ellas, se establece la concordancia inversa.

### Implementación en WordPress

```sql
-- En la tabla wp_termmeta para cada término bc_pericopa:
-- meta_key = '_evento_canonico'
-- meta_value = 'sanidad-hija-de-jairo'  (slug del evento canónico)

-- El catálogo maestro puede ser:
-- 1) Una taxonomy separada 'bc_evento_canonico' (hace cross-reference explícito)
-- 2) Un simple post type o archivo YAML externo
-- 3) Metadata plana sin taxonomy (más simple, el catálogo existe como convención)
```

**Recomendación inicial**: usar metadata plana (`_evento_canonico`) sin taxonomy separada, para no multiplicar las taxonomías. El catálogo maestro se documenta en un archivo `docs/juego-del-cinco/catalogo-eventos-canonicos.md` que se construye incrementalmente fase por fase.

### Tipos de relación cross-canónica

`_evento_canonico` por sí solo no basta. Existen tres tipos de relación entre perícopas de distintos libros, y cada una necesita un tratamiento metadata distinto:

#### Tipo A: Misma fuente, mismo evento — testimonio múltiple («sinóptico»)

El mismo evento histórico narrado en múltiples libros. Los títulos son idénticos.

| Relación | Ejemplo | `_evento_canonico` | Título |
|:---------|:--------|:-------------------|:-------|
| Mt 9, Mc 5, Lc 8 — Hija de Jairo | Idéntico evento histórico | `sanidad-hija-de-jairo` | Idéntico en los 3 |
| Mt 5–7, 3 Ne 12–14 — Sermón del Monte/Templo | Mismo sermón, distinta audiencia | `sermon-del-monte` | Diferente («del Monte» vs «en el Templo») |

#### Tipo B: Misma fuente escrituraria, distinta encarnación textual — relatos paralelos («pericopa gemela»)

El mismo suceso primordial o patriarcal recibido por revelación a profetas diferentes, en tiempos y contextos distintos. **No es** el mismo texto canónico — son textos independientes que describen el mismo suceso de fondo.

**Caso paradigmático: la Creación en Génesis, Moisés y Abraham**

| Perícopa | Slug | Contenido específico | `_evento_canonico` | Relación con Gen |
|:---------|:-----|:---------------------|:-------------------|:-----------------|
| Gn 1:1–2:3 — «La Creación» | `genesis-1-la-creacion-p1` | Creación en 6 días + reposo | `la-creacion` | — |
| Gn 2:4–25 — «Adán y Eva en el Edén» | `genesis-2-adan-y-eva-en-el-eden` | Edén, árboles, costoilla, unidad | `la-creacion` | — |
| Mo 2:1–3:25 — «La Creación» | `moses-2-la-creacion-p1` | Creación + Edén (paralelo a Gn 1–2) | `la-creacion` | Paralelo directo (contenido adicional: Mo 1 es único) |
| Abr 4:1–5:21 — «La Creación» | `abraham-4-la-creacion-p1` | Creación narrada por Abraham (dioses, concilio premortal) | `la-creacion` | Paralelo directo (Abr 3:22–28 sobre concilio es único) |
| Mo 1:1–39 — «Moisés ve la Creación» | `moses-1-moises-ve-la-creacion` | Visión de Moisés: creación, propósito, grandeza de Dios | `la-creacion` | Paralelo indirecto (visión marco, no texto paralelo) |
| Abr 3:22–28 — «El concilio premortal» | `abraham-3-el-concilio-premortal` | Los nobles y grandes, elección de Cristo, rebelión de Satanás | `concilio-premortal` | Sin paralelo directo en Gn (implícito en Gn 1:26 «hagamos») |

**Reglas para Tipo B:**
- Cada perícopa tiene **slug y título propios** (reflejan su contenido particular y voz narrativa).
- El título **no tiene por qué ser idéntico** — de hecho en Moisés 1 y Abraham 3 es diferente porque el contenido es diferente.
- `_evento_canonico` = el mismo para todas las perícopas que describen el mismo suceso de fondo. Esto permite agruparlas para concordancias.
- Metadata adicional `_relacion_con_genesis`, `_relacion_con_moises`, etc. puede documentar la relación específica (paralelo directo, paralelo indirecto, único en este libro).

| Perícopa | `_evento_canonico` | `_relacion_paralela` |
|:---------|:-------------------|:---------------------|
| `genesis-1-la-creacion-p1` | `la-creacion` | — (es la fuente canónica de referencia) |
| `moses-2-la-creacion-p1` | `la-creacion` | `paralelo-directo: genesis-1-la-creacion-p1` |
| `abraham-4-la-creacion-p1` | `la-creacion` | `paralelo-directo: genesis-1-la-creacion-p1` |
| `moses-1-moises-ve-la-creacion` | `la-creacion` | `paralelo-indirecto: genesis-1-la-creacion-p1` |
| `abraham-3-el-concilio-premortal` | `concilio-premortal` | `paralelo-indirecto: genesis-1-la-creacion-p1` (Gn 1:26 «hagamos») |

#### Tipo C: Cita explícita de un libro en otro

Un pasaje de un libro canónico se cita textualmente en otro. Ejemplos clásicos:

| Cita en | Cita de | Slug de la cita | `_cita_de` |
|:--------|:--------|:----------------|:-----------|
| Mosíah 14 | Isaías 53 | `mosiah-14-abinadi-cita-a-isaias` | `isaias-53-el-siervo-sufriente` |
| 1 Ne 20–21 | Isaías 48–49 | `1-nefi-20-nefi-cita-a-isaias` | `isaias-48-la-rebelion-de-israel` |
| DyC 77 | Apocalipsis 5–6 | `dyc-77-preguntas-sobre-apocalipsis` | `apocalipsis-5-el-rollo-y-el-cordero` |
| 3 Ne 12–14 | Mateo 5–7 | `3-nefi-12-sermon-en-el-templo-p1` | ver Tipo A arriba (`_evento_canonico`) |
| Mateo 2:6 | Miqueas 5:2 | `mateo-2-la-estrella-y-los-magos` | `miqueas-5-belén-y-el-gobernante-de-israel` |

**Reglas para Tipo C:**
- Metadata `_cita_de` apunta al **slug de la perícopa citada** en el libro original.
- La perícopa citante tiene su propio título y `_evento_canonico` (es su propio evento en su propio libro), más `_cita_de` que enlaza a la fuente.
- Una misma perícopa puede tener múltiples `_cita_de` si cita varias fuentes.
- Las citas pueden anidarse: Mosíah 14 cita Isaías 53, que a su vez se relaciona con `_evento_canonico = el-siervo-sufriente`.

#### Implementación en WordPress

```sql
-- wp_termmeta para bc_pericopa:

-- Tipo A: Mismo evento, testimonio múltiple
-- meta_key = '_evento_canonico'
-- meta_value = 'slug-del-evento'  (compartido entre todas las perícopas del mismo evento)

-- Tipo B: Relato paralelo (Creación en Gen/Moisés/Abraham)
-- meta_key = '_evento_canonico'
-- meta_value = 'la-creacion'
-- meta_key = '_relacion_paralela'
-- meta_value = 'paralelo-directo: genesis-1-la-creacion-p1'  (apunta al pasaje de referencia)

-- Tipo C: Cita explícita
-- meta_key = '_cita_de'
-- meta_value = 'slug-de-la-pericopa-citada'  (opcional: puede haber múltiples entradas)
```

**Ejemplo concreto — la Creación:**

| Slug | `_evento_canonico` | `_relacion_paralela` |
|:-----|:-------------------|:---------------------|
| `genesis-1-la-creacion-p1` | `la-creacion` | — (canon de referencia) |
| `moses-2-la-creacion-p1` | `la-creacion` | `paralelo-directo: genesis-1-la-creacion-p1` |
| `abraham-4-la-creacion-p1` | `la-creacion` | `paralelo-directo: genesis-1-la-creacion-p1` |

**Ejemplo concreto — cita AT en LdM:**

| Slug | `_evento_canonico` | `_cita_de` |
|:-----|:-------------------|:-----------|
| `mosiah-14-abinadi-cita-a-isaias` | `abel-siervo-sufriente` | `isaias-53-el-siervo-sufriente` |

Un query que busca `_evento_canonico = la-creacion` devuelve las 5 perícopas de Génesis, Moisés y Abraham que describen la Creación. Un query adicional por `_relacion_paralela` permite saber cuáles son paralelos directos (mismo esquema narrativo) y cuáles indirectos (visión marco o concilio).

### Mapeo de casos cross-canónicos reales

Para verificar que el sistema cubre la variedad de relaciones reales, aquí está el mapeo explícito de cada caso que debe soportar:

#### Reyes–Crónicas–Samuel (intra-AT, relato histórico paralelo)

El mismo período histórico (reino unificado de Israel) narrado con distinto enfoque teológico. **Tipo B intra-canónico.**

| Perícopa | Slug | `_evento_canonico` | `_relacion_paralela` |
|:---------|:-----|:-------------------|:---------------------|
| David es ungido rey (1 S 16) | `1-samuel-16-david-es-ungido-rey` | `david-es-ungido-rey` | — |
| David es ungido rey (1 Cr 11) | `1-cronicas-11-david-es-ungido-rey` | `david-es-ungido-rey` | `historico: 1-samuel-16-david-es-ungido-rey` |
| David y Goliat (1 S 17) | `1-samuel-17-david-y-goliat` | `david-y-goliat` | — |
| David y Goliat (1 Cr 20) | `1-cronicas-20-david-y-goliat` | `david-y-goliat` | `historico: 1-samuel-17-david-y-goliat` |

**Regla**: el qualifier `historico:` en `_relacion_paralela` indica que es el mismo suceso histórico, no el mismo texto revelatorio. El título puede ser idéntico o reflejar el énfasis particular de cada cronista.

#### Judas y 2 Pedro (intra-NT, dependencia textual)

Dos epístolas que comparten material textual extenso. **Tipo C intra-canónico.**

| Perícopa | `_evento_canonico` | `_cita_de` |
|:---------|:-------------------|:-----------|
| `judas-1-advertencia-contra-los-apostatas` | `advertencia-contra-la-apostasia` | — |
| `2-pedro-2-advertencia-contra-los-falsos-maestros` | `advertencia-contra-la-apostasia` | `cita: judas-1-advertencia-contra-los-apostatas` |

**Regla**: comparten `_evento_canonico` (misma enseñanza) y se añade `_cita_de` para la dependencia textual.

#### Enoc: de Génesis 5 a Moisés 6–7 (expansión revelatoria)

Génesis 5:21–24 da 4 versículos sobre Enoc («caminó con Dios»). Moisés 6–7 expande esto en 2 capítulos de profecía, visión y ministerio. **Tipo B, subtipo expansión.**

| Perícopa | `_evento_canonico` | `_relacion_paralela` |
|:---------|:-------------------|:---------------------|
| `genesis-5-enoc-camino-con-dios` | `enoc-el-profeta` | — (es la fuente comprimida) |
| `moses-6-el-llamamiento-de-enoc` | `enoc-el-profeta` | `expansion: genesis-5-enoc-camino-con-dios` |
| `moses-7-la-vision-de-enoc` | `enoc-el-profeta` | `expansion: genesis-5-enoc-camino-con-dios` |

**Regla**: `_relacion_paralela = expansion:{slug}` indica que esta perícopa desarrolla y expande una más breve. El título de la expansión NO es idéntico al de la fuente comprimida, porque el contenido es cualitativamente mayor.

#### DyC 77 y Apocalipsis (comentario inspirado)

DyC 77 es una sección de preguntas y respuestas que versa versículo por versículo sobre Apocalipsis 4–13. **Tipo C extendido.**

| Perícopa | `_evento_canonico` | `_cita_de` |
|:---------|:-------------------|:-----------|
| `dyc-77-preguntas-sobre-apocalipsis-4-a-7` | `los-sellos-y-el-cordero` | `comentario: apocalipsis-5-el-rollo-y-el-cordero` |
| `dyc-77-preguntas-sobre-apocalipsis-8-a-13` | `los-sellos-y-el-cordero` | `comentario: apocalipsis-6-los-seis-primeros-sellos` |

**Regla**: se añade el qualifier `comentario:` al valor de `_cita_de` (o a `_relacion_paralela`) para indicar que no es una cita textual sino una explicación inspirada. Esto permite que en la interfaz se muestre como «Comentario sobre Apocalipsis 5» en lugar de «Cita de Apocalipsis 5».

#### DyC 84 (sacerdocio), DyC 107 (patriarcas), Génesis 5 (genealogía) — paralelo temático

DyC 84 da la línea del sacerdocio desde Moisés hasta Adán. DyC 107 da el orden patriarcal desde Adán hasta Moisés. Génesis 5 da la genealogía de Adán a Noé. Los tres tocan **el mismo tema teológico** (la transmisión del sacerdocio) pero NO describen el mismo evento ni se citan mutuamente. **Ningún Tipo A/B/C existente cubre esto directamente.**

**Extensión**: se introduce el qualifier `tematico:` en `_relacion_paralela`.

| Perícopa | `_evento_canonico` | `_relacion_paralela` |
|:---------|:-------------------|:---------------------|
| `genesis-5-genealogia-de-adan-a-noe` | `genealogia-de-adan` | — |
| `dyc-84-la-linea-del-sacerdocio` | `genealogia-de-adan` | `tematico: genesis-5-genealogia-de-adan-a-noe` |
| `dyc-107-el-orden-patriarcal` | `genealogia-de-adan` | `tematico: genesis-5-genealogia-de-adan-a-noe` |
| Éxodo 40 (orden del sacerdocio levítico) | `genealogia-de-adan` | `tematico: genesis-5-genealogia-de-adan-a-noe` |

**Regla**: comparten `_evento_canonico` = el tema teológico de fondo. `_relacion_paralela = tematico:{slug}` indica que la relación es conceptual, no histórica ni textual.

#### Tipología: Melquisedec como tipo de Cristo

Un sacerdocio rey (Melquisedec, Gn 14) que prefigura a Cristo (Sal 110, He 7). **Ningún Tipo existente lo cubre.**

**Extensión**: qualifier `tipo:` en `_relacion_paralela`.

| Perícopa | `_evento_canonico` | `_relacion_paralela` |
|:---------|:-------------------|:---------------------|
| `genesis-14-melquisedec-bendice-a-abraham` | `melquisedec` | — |
| `salmos-110-el-sacerdocio-de-melquisedec` | `melquisedec` | `tipo: genesis-14-melquisedec-bendice-a-abraham` |
| `hebreos-7-melquisedec-tipo-de-cristo` | `melquisedec` | `tipo: genesis-14-melquisedec-bendice-a-abraham` |
| `dyc-107-el-orden-patriarcal` | `melquisedec` | `tipo: genesis-14-melquisedec-bendice-a-abraham` |

#### Resumen de qualifiers para `_relacion_paralela`

| Qualifier | Relación | Ejemplo |
|:----------|:---------|:--------|
| `paralelo:` | Relato paralelo del mismo suceso de fondo (Tipo B) | `paralelo: genesis-1-la-creacion-p1` |
| `expansion:` | Expansión revelatoria de un pasaje comprimido | `expansion: genesis-5-enoc-camino-con-dios` |
| `historico:` | Mismo suceso histórico, distinto libro canónico (Reyes/Crónicas) | `historico: 1-samuel-16-david-es-ungido-rey` |
| `cita:` | Cita textual de otro pasaje (Tipo C) | `cita: isaias-53-el-siervo-sufriente` |
| `comentario:` | Comentario inspirado sobre otro pasaje (DyC 77 → Ap) | `comentario: apocalipsis-5-el-rollo-y-el-cordero` |
| `tematico:` | Paralelo temático o teológico (mismo tema, distinto evento) | `tematico: genesis-5-genealogia-de-adan-a-noe` |
| `tipo:` | Tipología: una persona/evento prefigura otra | `tipo: genesis-14-melquisedec-bendice-a-abraham` |

**Regla general**: `_relacion_paralela` puede repetirse múltiples veces por perícopa. Cada entrada documenta un vínculo independiente. La interfaz puede usar el qualifier para mostrar el tipo de relación («Paralelo histórico en 1 Crónicas», «Expansión en Moisés», «Ver también sobre el sacerdocio en DyC 84»).

### Resumen: reglas de univocidad

| Regla | Aplica a | Ejemplo |
|:------|:---------|:--------|
| El **título** es el nombre puro del evento canónico, sin referencia a libro, capítulo ni rango. | Título | ✅ «Jesús resucita a la hija de Jairo» — ❌ «Jesús resucita a la hija de Jairo (Mateo 9)» |
| Para el mismo evento en distintos libros, el **título es idéntico**. | Título | «Jesús resucita a la hija de Jairo» en Mt, Mc, Lc |
| El **slug** completo `{libro}-{capitulo}-{nombre}` es globalmente único. | Slug | `mateo-9-jesus-resucita-a-la-hija-de-jairo` ≠ `marcos-5-jesus-resucita-a-la-hija-de-jairo` |
| La parte `{nombre}` del slug se deriva del título normalizado (idéntico para el mismo evento). | Slug → nombre | `jesus-resucita-a-la-hija-de-jairo` en Mt, Mc, Lc |
| El `{nombre}` debe diferenciar eventos similares en el mismo libro. | Slug → nombre | ✅ `jesus-sana-a-un-leproso` vs `jesus-sana-a-dos-ciegos` |
| El `_evento_canonico` agrupa todas las perícopas del mismo evento a través de todos los volúmenes. | Metadata | `pascua-redencion` agrupa Ex 12, Mt 26, Mc 14, Lc 22, 1 Co 5, 3 Ne 18, Mo 7 |
| Particiones de cruce de capítulo: mismo título de perícopa que continúa, slug con sufijo `-p{numero}`, mismo `_evento_canonico`. | Particiones | Título: «Consagración de los primogénitos» — Slug: `exodo-13-consagracion-de-los-primogenitos-p1`|
| Discursos largos: dividir por unidad temática, no por capítulo. Cada enseñanza/parábola es una perícopa separada. | Atomicidad | Sermón del Monte: ~20 perícopas, no 3 |

## Criterios expandidos: ¿cómo se distingue una perícopa?

Una perícopa se define por un cambio en uno o más de estos ejes:

### 1. Cambio temático
La escena cambia de asunto: enseñanza → milagro → controversia → parábola.
- Ejemplo: Mateo 13 — «Parábola del sembrador» (v1–9) → «Propósito de las parábolas» (v10–17) → «Parábola del trigo y la cizaña» (v24–30)

### 2. Cambio de tiempo
Avance o retroceso temporal explícito («Y aconteció después», «En aquellos días», «Pasados algunos días»).
- Ejemplo: Lucas 2 — «El nacimiento de Jesús» → «A los ocho días» → «Cuando se cumplieron los días de la purificación»

### 3. Cambio de lugar/espacio
Traslado geográfico: «Subió a Jerusalén», «Pasó al otro lado del lago», «Llegó a la región de los gadarenos».
- Ejemplo: Marcos 5 — «Jesús sana al endemoniado gadareno» (v1–20, región de Gadara) → «Jesús vuelve a Capernaúm» (v21, cruce del lago)

### 4. Cambio de personajes/interlocutores
Entra o sale un personaje principal, o cambia el grupo con quien Jesús interactúa.
- Ejemplo: Juan 3 — «Jesús y Nicodemo» (v1–21) → «Juan el Bautista da testimonio» (v22–36)

### 5. Cambio de género literario
Transición entre narrativa, discurso/enseñanza, parábola, profecía, poesía, epístola.
- Ejemplo: Lucas 15 — «Parábola de la oveja perdida» → «Parábola de la moneda perdida» → «Parábola del hijo pródigo»

### 6. Cambio de voz narrativa / narrador
especialmente relevante en el Libro de Mormón, donde la fuente (Nefi, Jacob, Mormón) cambia dentro de un mismo libro.

## Atomicidad: ¿qué tan amplias o atómicas deben ser las perícopas?

### Principio general
La perícopa debe ser **la unidad narrativa mínima que tiene sentido por sí misma**. Debe poder leerse de forma independiente y entenderse su trama o argumento sin depender del contexto anterior.

### Reglas prácticas

| Nivel | Tamaño típico | Uso |
|:------|:-------------|:----|
| **Perícopa completa** | Capítulo entero | Epístolas, DyC secciones cortas, libros proféticos breves |
| **Perícopa estándar** | 5–25 versículos | Narrativa de evangelios, AT, LdM — el rango más común |
| **Micro-perícopa** | 2–5 versículos | Transiciones breves, conectores narrativos, introducciones |

- **SÍ dividir discursos largos** por cada cambio temático dentro del discurso (ver Atomicidad en discursos).
- **NO reducir a micro-perícopas** cuando el único cambio es de interlocutor sin cambio de tema ni escena (un diálogo continuo sobre el mismo asunto es una sola perícopa).
- **NO fusionar** escenas que ocurren en lugares o tiempos diferentes bajo un mismo nombre genérico.
- **SÍ dividir** cuando hay interrupción narrativa (ver Casos Edge).

### Atomicidad en discursos

Los discursos largos (Sermón del Monte, Discurso del Aposento Alto, Sermón en el Templo nefita, etc.) se dividen por **unidad temática**, no por capítulo. Cada enseñanza o parábola distinta dentro del discurso es una perícopa separada.

**Ejemplo — Mateo 5–7 (Sermón del Monte), granularidad fina:**

| Perícopa | Cap | Versículos | `_evento_canonico` |
|:---------|:---:|:----------:|:-------------------|
| Introducción y Bienaventuranzas | 5 | 5:1–12 | `sermon-del-monte` |
| Sal de la tierra y luz del mundo | 5 | 5:13–16 | `sermon-del-monte` |
| Jesús y la Ley | 5 | 5:17–20 | `sermon-del-monte` |
| La ira y el homicidio | 5 | 5:21–26 | `sermon-del-monte` |
| El adulterio y la lujuria | 5 | 5:27–30 | `sermon-del-monte` |
| El divorcio | 5 | 5:31–32 | `sermon-del-monte` |
| Los juramentos | 5 | 5:33–37 | `sermon-del-monte` |
| Ojo por ojo | 5 | 5:38–42 | `sermon-del-monte` |
| Amor a los enemigos | 5 | 5:43–48 | `sermon-del-monte` |
| La limosna | 6 | 6:1–4 | `sermon-del-monte` |
| La oración y el Padrenuestro | 6 | 6:5–15 | `sermon-del-monte` |
| El ayuno | 6 | 6:16–18 | `sermon-del-monte` |
| Tesoros en el cielo | 6 | 6:19–24 | `sermon-del-monte` |
| No os afanéis | 6 | 6:25–34 | `sermon-del-monte` |
| No juzguéis | 7 | 7:1–6 | `sermon-del-monte` |
| Pedid, buscad, llamad | 7 | 7:7–12 | `sermon-del-monte` |
| La puerta estrecha | 7 | 7:13–14 | `sermon-del-monte` |
| Los falsos profetas | 7 | 7:15–23 | `sermon-del-monte` |
| Los dos cimientos | 7 | 7:24–27 | `sermon-del-monte` |
| Conclusión: la autoridad de Jesús | 7 | 7:28–29 | `sermon-del-monte` |

**Reglas para este nivel de granularidad:**
- Una parábola completa y autónoma es una perícopa (ej. Los dos cimientos).
- Una enseñanza sobre un tema específico es una perícopa (ej. No os afanéis).
- NO se divide por cada frase o cada interlocutor — el diálogo sobre un mismo tema es una unidad.
- El `_evento_canonico` agrupa todas las piezas del mismo discurso («Sermón del Monte»), y cada pieza lleva `_relacion_paralela = expansion:nivel-superior` apuntando a la perícopa mayor que la engloba.

**Consecuencia**: con esta granularidad, la estimación de perícopas sube significativamente, porque discursos largos que antes se contarían como 1 perícopa por capítulo ahora aportan 5–20 micro-perícopas cada uno. La estimación final (~5050) se detalla en la sección de Estimación de Volumen y Plan de Fases.

### División guiada por correlación: criterio recíproco

Las perícopas de un libro deben dividirse teniendo en cuenta cómo otros libros canónicos las referencian, y viceversa. La división debe diseñarse para que cada referencia cruzada pueda apuntar a una perícopa **completa y con límites que coincidan con el punto de referencia**.

#### Caso 1: DyC 77 y Apocalipsis

DyC 77 es una sección de preguntas y respuestas que comenta versículo por versículo Apocalipsis 4–14. Cada pregunta de DyC 77 define un punto de referencia. Si las perícopas de Apocalipsis no se alinean con esas preguntas, la correlación pierde precisión.

**DyC 77 pregunta sobre:** | **Apocalipsis debe dividirse en:**
:---------------------------|:-------------------------------
77:1 — El libro de los 7 sellos | Ap 4:1–5:14 — «El trono de Dios y el libro de los 7 sellos»
77:2 — El ángel fuerte | (misma perícopa, v. 5:2)
77:3 — Los 4 animales | (misma perícopa, v. 4:6–9)
77:4 — Los 24 ancianos | (misma perícopa, v. 4:4, 10–11)
77:5 — El mar de vidrio | (misma perícopa, v. 4:6)
77:6 — Las 6 alas de los animales | (misma perícopa, v. 4:8)
77:7 — La gloria de los animales | (misma perícopa, v. 4:9–11)
77:8 — El Cordero abre el libro | Ap 6:1–17 — «Los primeros 6 sellos»
77:12 — Los 144,000 sellados | Ap 7:1–8 — «El sellamiento de los 144,000»
— | Ap 7:9–17 — «La multitud de todas las naciones»
77:9 — Los mil años y el Evangelio eterno | Ap 8:1–6 — «El séptimo sello y la preparación de las trompetas»
77:10 — Las 7 trompetas | Ap 8:7–9:21 — «Las trompetas»
77:13 — El templo de Dios y el arca | Ap 11:19 — «El templo y el arca» (parte de las trompetas)
77:14 — Miguel y la guerra | Ap 12:1–17 — «La mujer, el dragón y la guerra en el cielo»
77:15 — El dragón | (misma perícopa)

Cada pregunta de DyC 77 cae sobre una perícopa completa de Apocalipsis. **Ninguna pregunta cae a media perícopa.** Esto no es casual: la división de Apocalipsis se diseñó con las preguntas de DyC 77 como guía de corte.

**Regla**: al dividir Apocalipsis, consultar primero DyC 77 para identificar los puntos de referencia. Cada pregunta debe tener su perícopa destino completa y alineada.

#### Caso 2: DyC 84 (Sacerdocio) y las referencias del AT

DyC 84:6–17 traza una línea genealógica del sacerdocio que toca estos puntos del AT:

| DyC 84 | Referencia AT | Perícopa AT necesaria |
|:-------|:--------------|:----------------------|
| v. 6 — Moisés de Levy | Éx 2:1–10 — «El nacimiento de Moisés» | ✅ Perícopa propia |
| v. 7 — Levy | Gn 29:31–35 — «Nacimiento de Levy» | ✅ Perícopa propia |
| v. 7 — Jacob | Gn 28:10–22 — «La escalera de Jacob» | ✅ Perícopa propia |
| v. 8 — Isaac de Abraham | Gn 22:1–19 — «La atadura de Isaac» | ✅ Perícopa propia |
| v. 8 — Abraham | Gn 12:1–9 — «El llamamiento de Abram» | ✅ Perícopa propia |
| v. 10 — Melquisedec | Gn 14:17–24 — «Melquisedec bendice a Abraham» | ✅ Perícopa propia |
| v. 14 — El sacerdocio mayor o santo según el orden del Hijo de Dios | Sal 110:4 — «El sacerdocio de Melquisedec» | ✅ Perícopa propia |
| v. 15–17 — Línea desde Adán | Gn 5:1–32 — «Genealogía de Adán a Noé» | ✅ Perícopa propia |

Aquí DyC 84 no fuerza divisiones nuevas en el AT porque las referencias ya caen sobre perícopas naturales. Pero la **comprobación recíproca** garantiza que así sea.

#### Caso 3: DyC 107 y el orden patriarcal

DyC 107:40–57 detalla el orden patriarcal desde Adán hasta Moisés. Las referencias más densas son:

| DyC 107 | Referencia AT | Implicación para división |
|:--------|:--------------|:--------------------------|
| v. 42–44 — Set, Enós, Cainán, Mahalaleel, Jared, Enoc | Gn 5:6–24 | Gn 5:1–32 debe tener al menos una perícopa que cubra la genealogía completa +
notas de cada patriarca |
| v. 45 — Matusalén | Gn 5:25–27 | |
| v. 46–50 — Sem, Noé | Gn 6:9–10 (Noé), Gn 10:21–31 (Sem) | Gn 6 debe dividirse entre genealogía y narrativa |
| v. 51–53 — Abraham, Isaac, Jacob, José | Gn 12–50 (múltiples) | Cada patriarca debe tener su perícopa |

**Consecuencia**: la genealogía de Gn 5 no puede ser una sola perícopa «Genealogía de Adán a Noé» porque DyC 107 referencia patriarcas individuales (Set, Enós, Enoc, Matusalén). Gn 5 necesita perícopas por cada patriarca que DyC 107 menciona explícitamente:

| Perícopa | Versículos | Referenciado por |
|:---------|:----------:|:-----------------|
| Adam | 5:1–5 | DyC 107:42 |
| Set | 5:6–8 | DyC 107:42 |
| Enós | 5:9–11 | DyC 107:43 |
| Cainán | 5:12–14 | DyC 107:43 |
| Mahalaleel | 5:15–17 | DyC 107:43 |
| Jared | 5:18–20 | DyC 107:43 |
| Enoc | 5:21–24 | DyC 107:44, Mo 6–7 (expansión) |
| Matusalén | 5:25–27 | DyC 107:45 |
| Lamec | 5:28–31 | — |
| Noé | 5:32 | DyC 107:46 |

**Regla general de correlación recíproca**:
1. Antes de dividir cualquier libro, identificar qué otros libros canónicos lo referencian (citas, comentarios, expansiones, tipologías, paralelos temáticos).
2. Para cada referencia, verificar que el punto de referencia caiga sobre el inicio o final de una perícopa en el libro referenciado, no a medio versículo ni a medio bloque.
3. Si una referencia importante no tiene una perícopa que la contenga exactamente, ajustar la división — incluso si eso significa crear una perícopa que, desde el punto de vista puramente interno del libro, no existiría.
4. Este proceso es **recíproco**: al dividir el libro A, considerar cómo B lo referencia; al dividir B, considerar cómo A lo referencia. La división final debe ser un equilibrio que sirva a ambos lados de la concordancia.

### Casos documentados donde la Escritura restauracionista fuerza granularidad en la Biblia

La investigación académica existente (Lindsay, Reynolds, Bradshaw) ha identificado múltiples casos donde el texto restauracionista exige una división más fina de la Biblia de lo que la tradición haría.

#### Caso 1 — Moisés 1: la teofanía que Génesis no tiene

Génesis comienza directamente con «En el principio creó Dios». Moisés 1 añade una visión-teofanía completa: Moisés habla con Dios cara a cara, ve el cosmos, es tentado por Satanás, y recibe el propósito de la Creación. **Génesis no tiene un paralelo directo para Moisés 1**, pero el contenido de Moisés 1 fuerza una pregunta sobre Gn 1: ¿debemos leer Gn 1:1–2:3 como una perícopa independiente o como parte de una revelación mayor a Moisés?

**Solución**: Gn 1:1–2:3 es una perícopa propia con `_evento_canonico = la-creacion`. Moisés 1 es una perícopa separada con `_relacion_paralela = expansion: genesis-1-la-creacion-p1` porque expande el **marco** en que la Creación fue revelada, no el contenido de la Creación misma.

#### Caso 2 — Moisés 6–7 (Enoc) vs Génesis 5:21–24

Bradshaw demuestra que Moisés 6–7 sigue una estructura de «temple text» con cuatro fases del pacto (obediencia, evangelio, castidad, consagración) que **no existe en Génesis 5**. La tradición rabínica trata Gn 5:21–24 como 4 versículos; la tradición SUD los expande a 2 capítulos completos.

**Implicación**: Gn 5 no puede dividirse solo por su estructura literaria interna (genealogía). La existencia de Moisés 6–7 fuerza que cada patriarca mencionado en Gn 5 tenga su propia perícopa, para que Moisés 6–7 pueda referenciarla individualmente (ver tabla de DyC 107 → Gn 5 arriba).

#### Caso 3 — Abraham 3:22–28 (el concilio premortal) y la lectura de Gn 1:26

Gn 1:26 dice «Hagamos al hombre a nuestra imagen». La tradición judía lee esto como plural mayestático. Abraham 3:22–28 revela que es un concilio literal de los hijos de Dios. Lindsay (186 parallels) muestra que esta lectura afecta cómo se divide Gn 1:

| Gn 1 sin Abr 3 | Gn 1 con Abr 3 |
|:---------------|:---------------|
| 1:1–2:3 — una sola perícopa «La Creación» | 1:1–25 — «La creación física» |
| | 1:26–31 — «La creación del hombre a imagen de Dios» (perícopa separada, pues Abr 3 revela que el v. 26 se refiere al concilio premortal) |

#### Caso 4 — Los 186 paralelos Lindsay Moisés–LdM

El catálogo de Jeff Lindsay (186 paralelos, actualizado a mayo 2026) documenta relaciones específicas Moisés ↔ LdM. Está organizado en 6 grupos que reflejan la progresión de la investigación. La tabla completa se incluye aquí como referencia para la curaduría de perícopas — cada paralelo implica que las perícopas correspondientes en Moisés y LdM deben existir con `_evento_canonico` compartido y `_relacion_paralela` cruzada.

**Fuente**: `jefflindsay.com/lds/parallels-book-of-moses-book-of-mormon/` y `interpreterfoundation.org` (Lindsay, 2023–2026).

**Grupo 1 — Conceptos que NO aparecen juntos en la KJV (Reynolds original, 33 paralelos)**

| # | Concepto | Moisés | LdM |
|:-:|:---------|:-------|:----|
| 1 | transgression-fall, fall-death | 6:59 | 2 Ne 9:6 |
| 2 | order-days-years-eternity (order + "from eternity to all eternity") | 6:67 | Alma 13:7 |
| 3 | The Lord "from all eternity to all eternity" | 6:67, 7:29, 31 | Mosíah 3:5, 8:18 |
| 4 | God-gave-man-agency | 7:32 | 2 Ne 2:16 |
| 5 | Lord's Spirit-withdraws-from-man | 1:15 | Alma 34:35; Hel 4:24, 6:35, 13:8; Mosíah 2:36 |
| 6 | children-whole-from foundation | 6:54 | Moroni 8:8, 12 |
| 7 | "Only name" "whereby salvation shall come unto the children of men" + salvation of children | 6:52 | Mosíah 3:16–18 |
| 8 | devil-father-of all lies | 4:4 | 2 Ne 2:18, 9:9; Éter 8:25 |
| 9 | devil-lead-captive-his will | 4:4 | 2 Ne 2:27, 29; Alma 12:11, 40:13 |
| 10 | devil-deceive-blind-lead | 4:4 | 3 Ne 2:2 |
| 11 | lies-lead-will-deceive-eyes | 4:4 | 1 Ne 16:38 |

**Grupo 2 — Conceptos que SÍ tienen conexiones KJV (Reynolds, 22 paralelos, #12–33)**

| # | Concepto | Moisés | LdM |
|:-:|:---------|:-------|:----|
| 12 | earth-groans; rocks-rend ("tumultuous noises") | 7:56 | 1 Ne 12:4, 19:12; 3 Ne 10:9 |
| 13 | plan of salvation | 6:62 | 2 Ne 9:6,13; Jacob 6:8; Alma 12:25–33, 42:5–16 + muchos más |
| 14 | eternal life | 1:39 | 2 Ne 2:27–28, 31:18–20; Jacob 6:11; Mosíah 5:15, 15:23–25; Alma 1:4, 11:40; Moroni 9:25 y muchos más |
| 15 | unclean-dwell-presence-God | 6:57 | 1 Ne 10:21, 15:34; Alma 7:21 |
| 16 | call on-all men-to repent | 5:14, 6:23 | 2 Ne 2:21, 26:27; Alma 5:49, 12:33; 3 Ne 11:32; Moroni 7:31 |
| 17 | nowise-inherit-kingdom of God | 6:57 | Mosíah 27:26; Alma 5:51, 9:12, 39:9; 3 Ne 11:38 |
| 18 | things-temporal-spiritual (subconjunto de #184) | 6:63 | 1 Ne 15:32, 22:3; 2 Ne 9:11–12; Mosíah 2:41; Alma 7:23, 37:43; Hel 14:16 |
| 19 | people-dwell-in righteousness | 7:16 | 1 Ne 22:26 |
| 20 | mine Only Begotten Son | 6:52 | Jacob 4:5, 11; Alma 12:33 |
| 21 | works of darkness | 5:55 | 2 Ne 9:9, 10:15, 25:2, 26:10,22; Alma 37:21,23; Hel 6:28,30; Mormón 8:27 |
| 22 | secret combination(s) | 5:51 | 2 Ne 26:22; Alma 37:30–31; Hel 6:38; 3 Ne 4:29, 7:6–9; Éter 8:18–27 y muchos más |
| 23 | wars and bloodshed | 6:15 | Jacob 7:24; Alma 35:15, 62:35; Mormón 8:8; Éter 14:21 y muchos más |
| 24 | shut out-from presence-God | 5:4, 41, 6:49 | 2 Ne 9:9 |
| 25 | murder-get gain | 5:31 | Hel 2:8, 7:21; Éter 8:16 |
| 26 | seeking for power | 6:15 | Alma 46:4 |
| 27 | carnal, sensual, devilish | 5:13 | Mosíah 16:3; Alma 41:13, 42:10 |
| 28 | hearts-wax-hard | 6:27 | Alma 35:15 |
| 29 | lifted up-imagination-his heart | 8:22 | Alma 1:6 |
| 30 | natural man | 1:14 | Mosíah 3:19; Alma 26:21 |
| 31 | Omner (*coincidencia, será retirado*) | 7:9 | Mosíah 27:34 |
| 32 | Shum (*coincidencia, será retirado*) | 7:5 | Alma 11:5 |
| 33 | and thus-it was (is)-Amen | 5:59 | 1 Ne 9:6, 14:30, 22:31; Alma 13:9; Hel 12:26 |

**Grupo 3 — Hallazgos posteriores de "Strong Like unto Moses" (Lindsay & Reynolds, 2023, 53 paralelos, #34–97)**

| # | Concepto | Moisés | LdM |
|:-:|:---------|:-------|:----|
| 34 | The strength of Moses (commanding the Red Sea) | 1:20–21, 25 | 1 Ne 4:2 |
| 35 | Chains of darkness, chains of hell, chains of the devil | 7:26, 57 | 2 Ne 1:13,23, 9:45, 28:19,22; Alma 5:7–10, 12:6–17, 26:14–15, 36:18 |
| 36 | Veil of darkness | 7:26, 61 | Alma 19:6 |
| 37 | Song of redeeming love/everlasting joy vs chains of darkness | 7:53–57 | Alma 5:7,9,26, 26:13–15, 36:18,22 |
| 38 | Satanic oaths/covenants in secret combinations | 5:29, 49–52; 6:28–29 | Alma 37:27,29; Hel 6:21,25,26; 4 Ne 1:42; Éter 8:15–16,20 |
| 39 | Great antiquity of secret combinations | 5:28–31, 49; 6:15 | 2 Ne 26:22; Hel 6:27; Éter 8:9, 9:26, 10:33 |
| 40 | Cain's secret combination to hide murder | 5:29 | Hel 6:27 |
| 41 | Satan's secret combination with Cain and followers | 5:25, 29, 51–52 | Hel 6:27; Éter 8:20–26 |
| 42 | Knowing/distinguishing brothers in secret covenants | 5:51 | Hel 6:22 |
| 43 | Shaking, trembling of heavens, earth, Satan, the wicked | 1:21, 6:47–49, 7:41, 61 | 2 Ne 1:13,23, 9:44–45, 28:18–19 |
| 44 | Misery (for Satan or his followers) | 7:37,41 | 2 Ne 2:5–27, 9:9,46; Mosíah 3:25; Alma 3:26, 40:15–21, 42:1–26 |
| 45 | Misery and woe | 6:48 | 2 Ne 1:13; Alma 9:11; Hel 5:12, 7:16 |
| 46 | Infinite atonement / bowels of mercy | 7:28–41 | Bowels of mercy: Mosíah 15:9; Alma 7:12, 34:15; Infinite atonement: 2 Ne 9:7; Alma 34:10,14 |
| 47 | Rage and Satan's dominion over hearts | 6:15 | 1 Ne 12:17; Mosíah 3:6; Alma 8:9, 12:11; Hel 6:21, 16:22–23; 3 Ne 1:22, 11:29; Éter 8:15–26 |
| 48 | Administering death | 6:15 | Alma 57:19 |
| 49 | Word returning "void" (Edén/Caída context) | 4:30 | Alma 12:22–23,26, 42:2–5 |
| 50 | "Esteeming" scripture as "naught" | 1:40–41 | 1 Ne 19:6–9; 2 Ne 33:2–3 |
| 51 | "Raising up" a prophet to restore ancient scripture | 1:41 | 2 Ne 3:6–7,12,24 |
| 52 | Workmanship of God's hands | 1:4; 7:32,36,37,40 | Jacob 4:9 |
| 53 | Men ordained after the order (of the Son of God) | 8:19 | 2 Ne 6:2; Alma 5:48–49, 13:1–9; Hel 8:18 |
| 54 | Natural (man, eye, frame) vs spiritual/spirit | 1:10–11, 3:5,9, 6:36 | Mosíah 3:19; Alma 19:6, 26:21, 41:4 |
| 55 | Roles of a seer | 6:35–36; chs. 6–7 | Mosíah 8:13–17, 28:10–16; Alma 37:22–26 |
| 56 | Perished in their sins | 7:1 | Mosíah 15:26; 1 Ne 16:39 |
| 57 | Sins answered upon heads of parents/children | 6:54, 7:37 | 2 Ne 4:6; Jacob 1:19, 3:10; Mosíah 29:30–31 |
| 58 | Glory of God and eternal life | 1:39; 6:59,61 | 2 Ne 1:25; Jacob 4:4,11; Alma 22:14, 29:9, 36:28; Moroni 9:25 |
| 59 | Weeping, wailing, and gnashing of teeth | 1:22 | Mosíah 16:2; Alma 40:13 |
| 60 | Satan laughs and his angels rejoice | 7:26 | 3 Ne 9:2 |
| 61 | The God/Lord who weeps/grieves for the lost | 7:28–29,37,39–40 | Jacob 5:7,11,13,32,46–47,51,66 |
| 62 | "All things" bear witness of the Creator | 6:63 | Alma 30:41,44; Hel 8:23–24 |
| 63 | Power, wisdom, mercy, and justice | 6:61–62 | 2 Ne 2:12, 11:5; Jacob 4:10; Mosíah 5:15 |
| 64 | Commanding the earth / power of the word | 7:13 | 1 Ne 17:29; 2 Ne 1:26; Jacob 4:6,9; Words of Mormon 1:17; Alma 17:4,17, 31:5 |
| 65 | Spreading abominations and works (of darkness) | 5:52 | Hel 6:28 |
| 66 | "Powers of heaven" and heavenly ascent/descent | 7:27 | 3 Ne 20:22, 21:23–25, 28:7–8 |
| 67 | Salvation/damnation by "a firm decree" | 5:15 | Alma 9:24, 29:4 |
| 68 | Angels bearing testimony | 7:27 | Moroni 7:31 |
| 69 | Residue of men + angels bearing testimony | 7:27–28 | Moroni 7:31–32 |
| 70 | Prepared from the foundation of the world | 5:57 | 1 Ne 10:18; Mosíah 4:6–7, 18:13; Alma 12:30, 42:26; Éter 3:14 |
| 71 | Gathered from the four quarters of the earth | 7:62 | 1 Ne 19:16, 22:25; 3 Ne 5:24,26, 16:5; Éter 13:11 |
| 72 | Counsel + "ye yourselves" | 6:43 | Jacob 4:10 |
| 73 | Fearful looking for fiery indignation of wrath | 7:1 | Alma 40:14 |
| 74 | Numerous upon the face of the land | 6:15 | Jarom 1:6; Mosíah 27:6; Mormón 1:7; Éter 7:11 |
| 75 | Record + baptism by fire and the Holy Ghost | 6:66 | 3 Ne 11:35, 19:14 |
| 76 | Caught up/away to an exceedingly high mountain | 1:1 | 1 Ne 11:1 |
| 77 | The devil blinds and leads (*reemplaza compound parallel 1*) | 4:4 | 1 Ne 12:17; 3 Ne 2:2 |
| 78 | Secret combinations + works of darkness | 5:51,55 | 2 Ne 9:9 |
| 79 | Plan of salvation + temporal and spiritual | 6:62–63 | 2 Ne 9:10–13 |
| 80 | The devil, blindness, and captivity (*reemplaza CP4*) | 4:4 | 1 Ne 14:7 |
| 81 | The devil leads captive (*reemplaza CP5*) | 4:4 | 3 Ne 18:15 |
| 82 | Creation of "all things" + wisdom, power, justice, mercy | 6:61 | Mosíah 5:15 |
| 83 | After the order + without beginning of days or end of years | 6:67 | Alma 13:9 |
| 84 | New Jerusalem + gathered from four quarters | 7:62 | Éter 13:10–11 |
| 85 | The devil leads (subconjunto de #9,77,81: versículos nuevos) | 4:4 | 1 Ne 14:3; 2 Ne 26:22, 28:21; Alma 34:39, 39:11; Hel 3:29 |
| 86 | Samuel el Lamanita ecos del llamamiento de Enoc (*ex-CP10*) | 6:26–41 | Hel 13–16 |
| 87 | Declared by angels | 5:58 | Mosíah 3:2–4; Alma 9:25, 13:22–25; Hel 5:11, 16:14; Moroni 7:31 |
| 88 | "For mine own purpose" | 1:3,33 | Jacob 5:36,53,55 |
| 89 | Fulfilling covenants | 8:2 | 1 Ne 14:17, 15:18; 2 Ne 6:12, 10:15; 3 Ne 5:25, 10:7, 15:8, 20:12–46; Éter 13:11 |
| 90 | Peaceable things of immortal glory/Heaven | 6:59 | Moroni 7:3 |
| 91 | For the space of many hours | 1:10 | 1 Ne 8:8; Hel 14:21,26 |
| 92 | Joy through the fall of man | 5:10 | 2 Ne 2:22–25 |
| 93 | Dwell in safety forever | 7:20 | 2 Ne 1:9 |
| 94 | Visions on the mount + "look" | 7:3–4 | 1 Ne 11:1–14:18 |
| 95 | Pierced by God's eye | 7:36 | Jacob 2:15 |
| 96 | Other combinations with "full of grace and truth" | 1:6,32; 5:7; 6:52; 7:11 | 2 Ne 2:5–6; Alma 13:8,9 |
| 97 | The Lord preserving His people during final tribulations | 7:61 | 1 Ne 22:17 |

**Grupo 4 — Hallazgos de "Further Evidence" (Lindsay, 2024, 36 paralelos, #98–133)**

| # | Concepto | Moisés | LdM |
|:-:|:---------|:-------|:----|
| 98 | "All things prepared," including fruit | 3:9 | 1 Ne 17:5, 18:6 |
| 99 | Covenanting with a prophet regarding future prophet among his descendants | 8:2 | 2 Ne 3:4–7 |
| 100 | A "sore curse" | 5:56, 8:4 | 1 Ne 2:23; 2 Ne 1:22, 5:21; Jacob 2:33, 3:3 |
| 101 | "Secret works" | 6:15 | Alma 37:21–25; 3 Ne 3:7; Hel 2:4 |
| 102 | A record in the language of an ancestor | 6:5–6 | 1 Ne 1:2, 3:19; Mosíah 1:2 |
| 103 | Secret combinations + getting gain, seeking power | 5:31,50 | Hel 2:8, 7:4–5,25; Éter 8:16,22–23, 11:15–16 |
| 104 | "For the space of many generations" | 7:4 | 2 Ne 1:18, 25:16 |
| 105 | Hell/prison "prepared" for the wicked | 6:29, 7:38 | 1 Ne 15:29,34–35; 2 Ne 9:16, 28:23; Mosíah 26:27; Hel 3:29 |
| 106 | The order of God/Son of God | 6:67, 8:19 | Alma 7:22, 8:4, 13:1–18, 43:2, 49:30; Hel 8:18; Éter 12:10 |
| 107 | "After the order" + preach/declare/teach gospel + repentance | 8:19–20 | Alma 5:48–49, 13:18, 49:30 |
| 108 | Bearing testimony | 7:27,62 | 2 Ne 27:13; Alma 4:19 |
| 109 | Carried away/caught up (by the Spirit) | 6:64 | 1 Ne 11:1 |
| 110 | Enoch and the Three Nephites "caught up" | 6:35–36, 7:27,69 | 3 Ne 28:13,15,36 |
| 111 | "Angels descending out of heaven" | 7:27 | 3 Ne 17:24 |
| 112 | "Drawn away" after him (Satan) | 4:5–6 | Mosíah 29:7; Éter 7:4, 9:11 |
| 113 | "All things manifest" by the Spirit/Holy Ghost | 8:24 | 1 Ne 22:2; Mosíah 5:3 |
| 114 | "The power of the Lord was upon him" | 8:18 | 1 Ne 13:16–18, 17:48–49; Jacob 7:15,21; Alma 14:25, 17:17–36; Hel 10:16 |
| 115 | "Seeking to take away life" | 8:17–18 | 1 Ne 1:20, 2:1, 4:11,28, 7:14–17, 17:44; 2 Ne 1:24, 5:2–19 |
| 116 | Left to oneself, weak | 1:9–10 | Mormón 2:26 |
| 117 | Crowned at the right hand of God | 7:56–57 | Mosíah 26:23–24; Alma 5:57–58, 28:11–12 |
| 118 | "Write the words" of spoken scripture | 2:1 | 2 Ne 29:11; Alma 45:9; 3 Ne 24:1 |
| 119 | "As many as will" | 5:9 | 2 Ne 25:13–14; Jacob 6:4 |
| 120 | Covenant + remnant of the seed | 7:52–53 | 1 Ne 13:30–39, 15:14; 3 Ne 21:4 |
| 121 | Remnant of the seed + the rock | 7:52–53 | 1 Ne 13:36–37, 15:13–15 |
| 122 | Gathering of scattered remnants with modern scripture | 7:62 | 2 Ne 30:3–8 |
| 123 | "Remnant of the seed" + "gather from four quarters" | 7:52,62 | 3 Ne 5:23–26, 16:4–5, 20:12–13 |
| 124 | Gathered remnants and the New Jerusalem | 7:51–53,62 | 3 Ne 21:4,22–24,26; Éter 13:6–10 |
| 125 | Agents unto themselves | 4:3, 6:56 | 2 Ne 2:26, 10:23; Alma 12:13; Hel 14:30 |
| 126 | The rock + "blessed are they" | 7:53 | 1 Ne 13:36–37 |
| 127 | Swallowed up in water / "floods" that swallow up | 7:43 | 1 Ne 18:10,15,20; 2 Ne 1:2; Alma 36:28; Hel 8:11; Éter 2:25 |
| 128 | Withering in divine presence + left without strength | 1:9–11 | 1 Ne 16:47–48,52 |
| 129 | Servant "receiving strength" to defeat opponent | 1:20–21 | 1 Ne 4:31, 7:16–17, 15:6 |
| 130 | Servant "receiving strength" after divine encounter | 1:10 | Mosíah 27:22–23; Alma 36:22–23 |
| 131 | Repent + "this is the plan of salvation/redemption" + Only Begotten | 6:23,50–52,61–62 | Alma 12:33 |
| 132 | God "conversed" with man | 6:22 | Alma 12:30 |
| 133 | Tool of Satan (serpent/Zeezrom) seeking to destroy with lies | 4:3–6 | Alma 12:1–7 |

**Grupo 5 — Hallazgos de 2025 (Lindsay, 13 paralelos, #134–146)**

| # | Concepto | Moisés | LdM |
|:-:|:---------|:-------|:----|
| 134 | "The Lord could not withhold" | 7:51 | Éter 3:25–26, 12:21 |
| 135 | Vision of "all things" + "even unto the end" | 7:67 | Éter 3:25–26 |
| 136 | Prophet shown "all the inhabitants of the earth" | 1:8,27–28, 7:21,67 | Éter 3:25 |
| 137 | Satan + beguile Eve/first parents + destroy the world | 4:6 | Éter 8:25 |
| 138 | Secret combinations + blood of victims cries from ground | 5:29–35 | Mormón 8:27,40; Éter 8:22 |
| 139 | Lord promises to justify prophet's words, including moving mountains | 6:32–34; 7:13 | Hel 10:4–10 |
| 140 | "Secret works" described as "abominations" | 5:25,51–52; 6:15,28–29 | 2 Ne 10:15; Alma 37:21–29; Éter 11:25; Mormón 8:40 |
| 141 | A seer that sees spirits or a spirit body | 6:35–36 | Éter 3:16 |
| 142 | God raises up a seer | 6:48 | 2 Ne 3:6–7,11 |
| 143 | Adam and Eve could have had neither children nor joy before the Fall | 5:10–11 | 2 Ne 2:23 |
| 144 | Adam fell that men/we might be | 6:48 | 2 Ne 2:25 |
| 145 | Shaking upon pondering misery/torment of the wicked | 7:41 | Mosíah 28:3 |
| 146 | Wicked wish to "lay hands" on prophet but dare not | 6:39 | Hel 5:22–26, 8:4–10; 1 Ne 17:48,52; Mosíah 13:2–3 |

**Grupo 6 — Hallazgos de 2026 (Lindsay, 40 paralelos, #147–186)**

| # | Concepto | Moisés | LdM |
|:-:|:---------|:-------|:----|
| 147 | Swollen hearts | 7:41 | Alma 12:48, 17:29, 19:13, 32:28, 33:23; Hel 13:22; 3 Ne 4:33 |
| 148 | Swollen hearts + agony of soul over wickedness | 7:41,44 | Alma 24:24; Hel 7:6 |
| 149 | Secret combination: get gain + glory + murder + leadership | 5:31,33 | Hel 7:4–5; Éter 8:7–9,22–23 |
| 150 | Born of the Spirit + changed/quickened + becoming sons/daughters of God | 6:65,68 | Mosíah 27:24–25 |
| 151 | Born again/baptized + the order of God | 6:59,64–65,67 | Alma 5:49,54, 8:4–5 |
| 152 | "This earth" (in contrast to other worlds) | 1:33–36,40; 2:1 | 1 Ne 17:38, 22:18 |
| 153 | Repent or be damned | 5:15 | 2 Ne 9:24; Éter 4:18; Mormón 2:13 |
| 154 | Unalterable decrees | 7:52 | Alma 29:4, 41:8 |
| 155 | "All mankind" can be "redeemed" | 5:9 | Alma 19:13, 34:9–15; Hel 14:16–17; Éter 3:13–14 |
| 156 | Satanic counter: all mankind redeemed without Christ | 4:1 | Alma 1:4 |
| 157 | Fullness of joy over saved souls | 7:67 | Alma 26:11–16,30, 29:10–16, 36:20–26; 3 Ne 17:20, 27:30–31, 28:9–10 |
| 158 | Receiving + fullness of joy | 7:67 | Mosíah 4:3; Alma 22:15 |
| 159 | Receive the Holy Ghost + ask + all things | 6:52 | 2 Ne 32:3–5 |
| 160 | Wicked men shall remove scripture portions, restored later | 1:41 | 1 Ne 13:26–35 |
| 161 | At Christ's death: rocks rend, saints rise | 7:56 | Hel 14:20–25 |
| 162 | "Jesus Christ … who shall come" | 6:57 | Mosíah 4:2; Alma 5:48, 45:4; Hel 5:9, 13:6; 3 Ne 11:10 |
| 163 | "Christ to come in the meridian of time / fulness of time" | 5:57; 6:57,62; 7:46 | 2 Ne 2:3,26; 11:27 |
| 164 | "Baptize + Christ, full of grace and truth" | 7:11 | Alma 9:26–27 |
| 165 | "Teach/taught unto the children of men" | 6:23 | Alma 13:6; 16:16 |
| 166 | "This is my glory + bringing souls to repentance/eternal life" | 1:39 | Alma 29:9 |
| 167 | "All things + knoweth all things + wisdom" | 6:61 | 2 Ne 2:24 |
| 168 | "The Lamb, lifted up and slain" | 7:47 | 1 Ne 11:32–34 |
| 169 | "The Lamb, from the foundation/beginning of the world" | 7:47 | 1 Ne 12:18 |
| 170 | "Zion and the Lamb" | 7:47 | 1 Ne 13:37 |
| 171 | "The day that the Lord shall come + the Lamb" | 7:47 | Mormón 9:2 |
| 172 | The premortal image of Christ's body | 1:6, 2:26–27, 6:8–9 | Éter 3:15–16; Mosíah 7:27 |
| 173 | Humans transfigured | 1:2–5,11, 6:35–36, 7:3–4 | 3 Ne 28:13–17; Hel 5:23–48, 3 Ne 17:23–24, 19:13–14 |
| 174 | Repent + "in the name of the/his Son" | 5:8 | Alma 12:33 |
| 175 | Sacrifice offered is a "similitude" of the "Only Begotten" | 5:6–7 | Jacob 4:5 |
| 176 | Baptism/covenant + Born of Spirit + changed = sons/daughters of God | 6:59,64–65,68 | Mosíah 5:7 |
| 177 | "They brought upon themselves" a curse, death, or destruction | 6:29 | Mosíah 5:5; Alma 3:19; Hel 14:29 |
| 178 | Holy Ghost/Comforter + "the truth of all things" | 6:61 | Moroni 10:5 |
| 179 | Joy + redemption | 5:11, 7:67 | Alma 26:36 |
| 180 | Joy + eternal life | 5:10–11 | Enós 1:3; Alma 22:15 |
| 181 | Fall/fallen + redeem/Redeemer/redemption | 5:9 | 1 Ne 10:6; 2 Ne 2:26; Mosíah 16:4–5; Alma 22:13; Hel 14:16; Mormón 9:12; Éter 3:13 |
| 182 | Moses bore record that the Son of God should come | 1:6, 4:1–2, 5:6–9, 6:52,57–59,62; 7:45–47,53–55 | Mosíah 13:33; Hel 8:13–16 |
| 183 | Brass plates contain info on fall of Satan | 4:1–6 | 2 Ne 2:17–18 |
| 184 | Temporal and spiritual (versículos adicionales a #18) | 6:63 | 1 Ne 14:7; 2 Ne 2:5, 9:11–12; Mosíah 4:26, 18:29; Alma 36:4, 42:7,9; Mormón 2:15 |
| 185 | "Began to prophesy" | 5:10, 7:2 | 1 Ne 1:18, 5:17; Mosíah 11:20, 12:1; Alma 8:32; Hel 16:7; Éter 12:2 |
| 186 | Born (again/of God) + eternal life | 5:59 | Alma 22:15 |

**Regla de uso**: cuando un paralelo documentado por Lindsay cruza dos cánones, ambas perícopas deben existir como términos `bc_pericopa` con `_evento_canonico` compartido y `_relacion_paralela` cruzada. Esto garantiza que el paralelo sea trazable por el sistema de concordancias. La tabla completa se usará durante las fases E (AT / Moisés) y F (LdM) para verificar que cada par de versículos tenga perícopas trazables en ambos cánones.

**Impacto en la división**: los 186 paralelos Lindsay implican que la división de Moisés y del LdM debe hacerse de forma coordinada. No se puede dividir uno sin considerar el otro. La fase F (LdM) y la subfase de Moisés dentro de la fase B (PGP) deben ejecutarse en tándem.

#### Caso 5 — Bradshaw: Estructura de pacto en Moisés como guía para AT

Bradshaw propone que Moisés 1–8 se organiza como un texto del templo con cuatro mandamientos del pacto. Esta estructura podría informar la división de pasajes paralelos del AT:

| Mandamiento del pacto (Bradshaw) | Moisés | Paralelo AT |
|:---------------------------------|:-------|:------------|
| Obediencia | Mo 2–3 (Creación) | Gn 1–2 |
| Evangelio | Mo 5 (Adán ofrece sacrificios) | Gn 4 |
| Castidad | Mo 5 (Caín y Abel) | Gn 4 |
| Consagración | Mo 6–7 (Enoc y Sion) | Gn 5:21–24 |

**Aplicación**: la división de Génesis 1–5 debe contemplar que Moisés 5 trata el evangelio y la castidad como perícopas separadas, aunque Génesis 4 las narre como un solo relato. Gn 4 debe dividirse al menos en dos perícopas (Caín y Abel, y la línea de Set) para alinearse con la estructura de Moisés.

### Caso guía: Jesús el Cristo de Talmage

El libro Jesús el Cristo (Talmage) no tiene una tabla de perícopas, pero su estructura de 42 capítulos revela las divisiones narrativas naturales de la vida de Cristo — cada capítulo corresponde a una o dos perícopas mayores. Su índice es un mapa implícito de perícopas evangélicas validado por la erudición SUD:

| Capítulo | Contenido = Perícopa(s) |
|:---------|:------------------------|
| 8 | El Niño de Belén |
| 9 | La presentación en el templo |
| 12 | El bautismo de Jesús |
| 13 | Las tentaciones |
| 14 | Las bodas de Caná (primer milagro) |
| 17 | Nicodemo |
| 19 | La mujer samaritana |
| 23 | El Sermón del Monte |
| 31 | La resurrección del hijo de la viuda de Naín |
| 32 | La tempestad calmada y el endemoniado gadareno |
| 33 | La hija de Jairo y la mujer con flujo de sangre |
| 36 | En la morada de los espíritus |
| 38 | La crucifixión |
| 39 | El ministerio en el hemisferio occidental (3 Nefi) |
| 42 | El segundo advenimiento |

**Utilidad**: El índice de Jesús el Cristo sirve como **validación cruzada** para las perícopas de los Evangelios y 3 Nefi: si Talmage dedicó un capítulo entero a un evento, ese evento es una perícopa válida.

## Casos Edge

### 1. Narrativa intercalada («Marcan sandwich»)

Marcos usa con frecuencia la técnica de intercalar una historia dentro de otra. El caso clásico es **la hija de Jairo y la mujer con flujo de sangre** (Marcos 5:21–43):

| Perícopa | Versículos | Nota |
|:---------|:----------:|:-----|
| Jairo busca a Jesús | 5:21–24 | Comienza la historia A |
| **La mujer con flujo de sangre** | **5:25–34** | **Se intercala la historia B** |
| Jesús resucita a la hija de Jairo | 5:35–43 | Se reanuda y concluye la historia A |

**Regla**: Cada interrupción narrativa crea una perícopa separada. Aunque la historia A se «reanuda», el segmento de la historia B es una perícopa independiente por su cambio de personajes, escena y desenlace. El resultado son **3 perícopas**, no 1.

Otros ejemplos de intercalación en Marcos:
- Marcos 3:20–21 → 3:22–30 (fariseos y Beelzebú) → 3:31–35 (la familia de Jesús)
- Marcos 6:7–13 (envío de los Doce) intercalado con 6:14–29 (muerte de Juan el Bautista)
- Marcos 11:12–14 (higo estéril) → 11:15–18 (purificación del templo) → 11:19–25 (vuelta al higo)

### 2. Perícopas que cruzan capítulos

Muchas perícopas comienzan en un capítulo y terminan en otro. Esto ocurre por dos razones:

**a) División artificial de capítulos**: Las divisiones de capítulos medievales (Stephen Langton, ~1227) no siempre respetan las unidades narrativas. Ejemplos:

- Hechos 8: el encuentro con el eunuco etíope comienza en el v26.
- Apocalipsis 12–14: visión profética continua.

**b) Perícopas genuinamente largas**: Relatos extensos que cubren varios capítulos (ej. la Pasión en los cuatro evangelios).

**Nota**: Con la granularidad fina (discursos divididos por tema), el caso más común de cruce de capítulo ya no es el discurso largo sino **la narración continua** que no respeta el corte de capítulo.

#### Regla para perícopas que cruzan capítulos

**Se particionan por límite de capítulo**, porque nuestra taxonomía `bc_pericopa` pertenece al capítulo. El slug de cada partición recibe un sufijo numerado `-p{numero}`. **Ambas particiones apuntan al mismo `_evento_canonico`**.

**Ejemplo — La Pasión de Cristo (Lucas 22–23):**

| Slug | Capítulo | Versículos | `_evento_canonico` |
|:-----|:--------:|:----------:|:-------------------|
| `lucas-22-la-traicion-de-judas` | 22 | 22:1–6 | `pasion-de-cristo` |
| `lucas-22-la-ultima-cena` | 22 | 22:7–38 | `pasion-de-cristo` |
| `lucas-22-el-getsemani-y-el-arresto` | 22 | 22:39–53 | `pasion-de-cristo` |
| `lucas-22-el-juicio-ante-el-sanedrin` | 22 | 22:54–71 | `pasion-de-cristo` |
| `lucas-23-jesus-ante-pilato` | 23 | 23:1–25 | `pasion-de-cristo` |
| `lucas-23-la-crucifixion` | 23 | 23:26–49 | `pasion-de-cristo` |
| `lucas-23-el-sepulcro` | 23 | 23:50–56 | `pasion-de-cristo` |

Aquí ninguna perícopa individual cruza capítulo porque la Pasión se ha dividido en sus eventos naturales. El `_evento_canonico = pasion-de-cristo` las agrupa a todas.

**Regla del sufijo `-p{numero}`**: se usa solo cuando una **misma unidad temática** cruza el límite del capítulo. No cuando se divide por tema dentro del capítulo.

**Ejemplo de uso genuino de `-p`**: Si Éxodo 12 (La Pascua) se divide así:

| Perícopa | Versículos | Nota |
|:---------|:----------:|:-----|
| Institución de la Pascua | 12:1–28 | Completa dentro del capítulo — sin `-p` |
| Muerte de los primogénitos y salida | 12:29–42 | Completa dentro del capítulo — sin `-p` |
| Reglamento de la Pascua | 12:43–51 | Completa dentro del capítulo — sin `-p` |
| Consagración de los primogénitos | **13:1–16** | **Cruza a capítulo 13** — lleva `-p` |

| Slug | Capítulo | `_evento_canonico` |
|:-----|:--------:|:-------------------|
| `exodo-12-institucion-de-la-pascua` | 12 | `pascua-redencion` |
| `exodo-12-muerte-de-los-primogenitos-y-salida` | 12 | `pascua-redencion` |
| `exodo-12-reglamento-de-la-pascua` | 12 | `pascua-redencion` |
| `exodo-13-consagracion-de-los-primogenitos-p1` | 13 | `pascua-redencion` |

Aquí las primeras tres perícopas están completas dentro del capítulo 12. La cuarta comienza en 12:43–51 (ya cubierta como tercera) pero su continuación temática «Consagración de los primogénitos» está en 13:1–16 y cruza a capítulo 13, por lo que la partición del capítulo 13 lleva `-p1`.

### 3. Estructura literaria: epístolas, secciones de DyC, profecías y poesía

**NO existe el concepto de "capítulo con una sola perícopa".** Todo capítulo o sección canónica tiene estructura literaria interna que debe reflejarse en la división pericopal. Las perícopas deben ayudar a entender el plan editorial del autor, no solo etiquetar el bloque completo.

**Epístolas** (Pablo, Santiago, Pedro, Juan, Judas): cada epístola tiene una estructura retórica reconocible que produce múltiples perícopas por capítulo:

| Componente | Ejemplo en Romanos | Perícopa |
|:-----------|:-------------------|:---------|
| Salutación / dirección | Ro 1:1–7 | «Saludo de Pablo a los romanos» |
| Acción de gracias | Ro 1:8–15 | «Pablo anhela visitar Roma» |
| Tesis / declaración temática | Ro 1:16–17 | «El evangelio es poder de Dios» |
| Cuerpo doctrinal (múltiples secciones) | Ro 1:18–11:36 | Varias perícopas por cambio temático |
| Exhortaciones / aplicación | Ro 12:1–15:13 | Varias perícopas por tema |
| Proyectos personales | Ro 15:14–33 | «Los planes de Pablo» |
| Saludos finales | Ro 16:1–16 | «Saludos de Pablo» |
| Advertencia final / bendición | Ro 16:17–27 | «Exhortación final y doxología» |

Filemón (~25 versículos) se divide en al menos 4 perícopas:

| Perícopa | Versículos | Función |
|:---------|:----------:|:--------|
| Saludo a Filemón, Apia y Arquipo | 1–3 | Salutación epistolar |
| Pablo da gracias por Filemón | 4–7 | Acción de gracias |
| Pablo intercede por Onésimo | 8–21 | Cuerpo: súplica por Onésimo |
| Despedida y bendición final | 22–25 | Cierre epistolar |

**Doctrina y Convenios**: las secciones largas se dividen por tema (DyC 76 tiene 5 visiones distinguibles; DyC 88 tiene múltiples secciones temáticas). Incluso secciones cortas tienen estructura: encabezado histórico + cuerpo revelatorio + promesa/cierre.

**Libros proféticos** (Abdías, Nahúm, etc.): cada oracle o cambio de tema es una perícopa separada: juicio contra naciones, llamado al arrepentimiento, promesa de restauración, etc.

**Poesía / Sabiduría** (Salmos, Proverbios): los salmos individuales son perícopas completas. Proverbios se divide por colecciones (Proverbios de Salomón, Palabras de los sabios, etc.) y dentro de cada colección por dichos individuales o temas.

**Regla**: el nombre de la perícopa debe reflejar la función literaria o el tema específico dentro de la estructura del libro, NO el título del capítulo ni un genérico.

| Función literaria | Ejemplo concreto | Estructura que revela |
|:------------------|:-----------------|:----------------------|
| ✅ «Saludo de Pablo a los romanos» | Ro 1:1–7 | La epístola comienza con saludo formal |
| ✅ «Pablo intercede por Onésimo» | Filemón 8–21 | El cuerpo de la carta es una súplica |
| ✅ «La visión de los tres grados de gloria» | DyC 76:1–10 | Hay una introducción antes de la visión |
| ❌ «Romanos 1» | Ro 1:1–32 | No revela estructura |
| ❌ «Sección 76» | DyC 76 | Oculta que hay 5+ unidades temáticas |

### 4. Parábolas múltiples en secuencia

Cuando Jesús enseña varias parábolas seguidas (ej. Mateo 13, Lucas 15), **cada parábola es una perícopa separada**. La atomicidad se define por:

- Cambio de historia o analogía
- Cada parábola termina con «El que tiene oídos para oír, oiga»
- Cambio de audiencia (discípulos vs multitud)

### 5. Discursos largos — granularidad fina por tema

Discursos como el Sermón del Monte (Mateo 5–7), el Sermón en el Templo nefita (3 Nefi 11–18), o el Discurso del Aposento Alto (Juan 13–17) **se dividen por unidad temática**, no por capítulo. Cada enseñanza, parábola o sección distinguible del discurso es una perícopa separada.

**Ejemplo — Discurso del Aposento Alto (Juan 13–17):**

| Perícopa | Cap | Versículos |
|:---------|:---:|:----------:|
| Jesús lava los pies a los discípulos | 13 | 13:1–20 |
| Jesús anuncia la traición de Judas | 13 | 13:21–30 |
| El mandamiento nuevo | 13 | 13:31–38 |
| Jesús consuela a sus discípulos | 14 | 14:1–14 |
| La promesa del Consolador | 14 | 14:15–31 |
| La vid verdadera | 15 | 15:1–17 |
| El odio del mundo | 15 | 15:18–27 |
| La obra del Consolador | 16 | 16:1–15 |
| La tristeza se convertirá en gozo | 16 | 16:16–33 |
| La oración intercesora | 17 | 17:1–26 |

Todas comparten `_evento_canonico = discurso-del-aposento-alto`, y cada una lleva `_relacion_paralela = expansion:nivel-superior` apuntando a la perícopa mayor que engloba el discurso completo.

## Estimación de volumen

**NO hay "capítulos de 1 perícopa".** Todo bloque canónico tiene estructura literaria interna. Esto incrementa significativamente la estimación previa:

| Tipo de material | Capítulos / secciones | Perícopas estimadas | Rango promedio por capítulo |
|:-----------------|:---------------------:|:-------------------:|:---------------------------:|
| **Narrativa densa** (AT histórico, Evangelios, Hechos, LdM narrativo) | ~400 | ~1600 | 3–6 por capítulo |
| **Epístolas** (NT: Pablo, Santiago, Pedro, Juan, Judas; Heb; Apocalipsis) | ~200 | ~600 | 2–5 por capítulo |
| **Profecía** (AT profetas mayores y menores) | ~250 | ~600 | 2–4 por capítulo |
| **Ley y códigos** (Levítico, Deuteronomio, parte de Éxodo y Números) | ~150 | ~400 | 2–4 por capítulo |
| **Poesía / Sabiduría** (Salmos, Proverbios, Eclesiastés, Cantares, Lamentaciones) | ~300 | ~500 | 1–3 por capítulo (cada salmo es 1 perícopa; Proverbios se divide por colección) |
| **Doctrina y Convenios** | ~140 secciones | ~350 | 2–4 por sección larga; 1 en cortas |
| **Perla de Gran Precio** | ~30 | ~100 | 2–4 por capítulo |
| **Particiones por cruce de capítulo** | variable | ~100 | — |
| **Total estimado (por género, solapado)** | **~1470** | **~4250** | *Las filas no son disjuntas (discursos largos ya cuentan en narrativa/profecía)* |
| **Total estimado (por libro, no solapado)** | **~1674 capítulos** | **~5050 perícopas** | *Ver tabla de fases abajo — estimación operativa* |

## Fuentes identificadas

### Desde Alejandría

| Fuente | Ubicación en corpus | Contenido | Utilidad |
|:-------|:--------------------|:----------|:---------|
| **Concordancia entre los Evangelios** | `es/study-aids/harmony-of-the-gospels/harmony-table.txt` | ~50+ eventos (perícopas) de la vida de Cristo en orden cronológico, con referencias cruzadas a Mateo, Marcos, Lucas, Juan y escrituras SUD | Fuente primaria para NT: lista de perícopas evangélicas ya traducida al español, organizada cronológicamente. Incluye referencias al LdM (1 Ne 11, 3 Ne). |
| **Jesús el Cristo (Talmage)** | `es/manuals/jesus-the-christ/` (42 archivos, capítulos 1–42) | Capítulos organizados por evento narrativo que cubren desde la preexistencia hasta el segundo advenimiento. El índice funciona como mapa implícito de perícopas. | Validación cruzada para perícopas de los evangelios y 3 Nefi. No tiene tabla explícita pero su estructura de capítulos revela perícopas naturales. |
| **Manual del Instituto — Nuevo Testamento** | `es/manuals/institute/new-testament-teacher/` | Divisiones de enseñanza por bloque narrativo | Validación de perícopas desde la perspectiva del sistema educativo SUD |
| **Manual del Seminario (AT, NT, LdM, DyC, PGP)** | `es/manuals/seminary/` (múltiples archivos) | Bloques de enseñanza que se corresponden con perícopas | Estructura validada a nivel mundial por la Iglesia |

### De investigación previa

| Fuente | Tipo | Cobertura | Estado |
|:-------|:-----|:----------|:-------|
| **BYU Studies — Charting the NT** (Welch, 2002) | Libro | NT: 290 perícopas evangelios (Chart 7-6) + epístolas | Consultado en Alejandría vía corpus de BYU Studies. Charting the NT disponible parcialmente. |
| **BYU Studies — Charting the BoM** (Welch, 1999) | Libro | LdM: estructura, chart 170 (capítulos 1830 vs 1981) | Consultado en Alejandría. |
| **Nathan Richardson — StoryGuide Scriptures** | Edición impresa | LdM + DyC 1–40 con encabezados multi-nivel | Requiere extracción manual. |
| **Nelson's Complete Book of Bible Maps and Charts** | Libro | AT + NT | Cubre ambos testamentos, por adquirir. |
| **Encabezados oficiales del DyC** (churchofjesuschrist.org) | Web | Encabezados de cada sección que dividen por tema | Base pericopal primaria para Fase A. No es suficiente: algunos encabezados son genéricos y fusionan temas que nuestra atomicidad separaría. |
| **Manual del Instituto — DyC** | Manual web | 138 secciones con divisiones temáticas | Útil como refinamiento de los encabezados. |
| **Smoot et al. — Pearl of Great Price: A Study Edition** | Libro | Moisés y Abraham | Disponible en inglés. |
| **Manual del Instituto — PGP** | Manual web | 29 bloques de enseñanza en español | Disponible. |

### Mapa de cobertura de fuentes por volumen

| Volumen | Fuente primaria | Validación cruzada | Desde Alejandría |
|:--------|:----------------|:-------------------|:-----------------|
| **AT** | Nelson's + div. litúrgica judía (Pentateuco) | BibleProject | Manuales Seminario AT |
| **NT Evangelios** | Concordancia entre los Evangelios (harmony table) | Jesús el Cristo (Talmage), BYU Charting NT, Manuales Instituto/Seminario | ✅ harmony-table.txt, ✅ Jesús el Cristo, ✅ Manuales |
| **NT Epístolas** | BYU Charting NT + Nelson's | Manuales Instituto | ✅ Manuales |
| **LdM** | StoryGuide (Richardson) | BYU Charting BoM, J. Max Wilson, Manuales Seminario | ✅ Manuales Seminario LdM |
| **DyC** | Manual del Instituto DyC + StoryGuide 1–40 | Revelations in Context | ✅ Manuales |
| **PGP** | Manual del Instituto PGP + Smoot Study Ed. | Manuales Seminario | ✅ Manuales |

## Estado del arte: lo que existe y lo que falta

### Listas de perícopas existentes (enfoque SUD)

| Fuente | Qué cubre | Formato | Limitación |
|:-------|:----------|:--------|:-----------|
| **BYU Studies — Charting the NT** (Welch & Hall, 2002) — Chart 7-6 | **290 perícopas de los 4 evangelios** siguiendo el orden de Mateo, con JST incluido. Marca en cursiva las compartidas por los sinópticos y señala contenido único de Juan. | Lista en un chart, con referencias cruzadas | Solo evangelios. Las epístolas tienen análisis estructural (charts 14–29) pero no una lista de perícopas explícita. |
| **ISBE — International Standard Bible Encyclopedia** (presente en Alejandría) | Divisiones pericopales del Pentateuco: 10 toledoth en Génesis, 10 perícopas en Levítico, 10 en Éx 1:8–7:7. | Entradas enciclopédicas | Enfoque pre-crítico (asume unidad del texto). No cubre LdM, DyC ni PGP. |
| **Nathan Richardson — StoryGuide Scriptures** | Tabla de contenidos del DyC dividida en Partes geográficas (NY, Ohio, Missouri, Illinois) y Unidades narrativas. También edición del LdM con encabezados multi-nivel. | PDF descargable, edición impresa | Solo DyC (secciones 1–40) y LdM. No cubre AT, NT ni PGP. Las divisiones son implícitas (encabezados), no una lista explícita de perícopas. |
| **John W. Welch — Charting the Book of Mormon** (FARMS, 1999) — Sección 2 | Análisis estructural: placas, fuentes, cambios de autor, extensión por libro. Charts 53–55 listan capítulos doctrinales clave. | Charts en libro | Es análisis a nivel de libro/bloque, no de perícopa individual dentro del capítulo. |
| **Manuales del Instituto y del Seminario SUD** (churchofjesuschrist.org) | Divisiones de enseñanza por bloque narrativo para AT, NT, LdM, DyC y PGP. | Manuales web | Son guías pedagógicas, no listas de perícopas. Las divisiones son prácticas, no sistemáticas. |

### Trabajo académico sobre estructura y correlación

| Investigador | Aporte | Relevancia para este proyecto |
|:-------------|:-------|:-----------------------------|
| **John W. Welch** (BYU/FARMS) | *Charting the NT* (290 perícopas) + *Charting the BoM* (análisis estructural). Descubridor del quiasmo en LdM. | La única lista existente de perícopas evangélicas desde un marco SUD. Su criterio de inclusión (JST armonizado con los 4 evangelios) es un precedente directo. |
| **Jeff Lindsay** (Independiente) | Catálogo de 186 paralelos Moisés–Libro de Mormón (actualizado a 2026). Trabajo sobre Janus Parallelism y pares de palabras. | Fuente metodológica principal para «reverse correlation»: cómo el texto restauracionista provee el marco interpretativo para entender alusiones en el otro canon. |
| **Noel B. Reynolds** (BYU) | «The Brass Plates Version of Genesis» (1990, FARMS). Demostró que las relaciones Moisés–LdM no se explican por autor común sino por dependencia unidireccional. | Fundamento académico de que la división del AT debe considerar que «algo como el Moisés actual» estaba en las planchas de bronce que Nefi poseía. |
| **Jeffrey M. Bradshaw** (Independiente, templethemes.net) | Análisis del Libro de Moisés como «temple text» con estructura bipartita (Creación–Caída → Enoc–Sion) siguiendo patrón de pacto: obediencia, evangelio, castidad, consagración. | Muestra que Moisés tiene su propia estructura pericopal interna (basada en el pacto del templo), independiente de Génesis. Vital para la división del PGP. |
| **J. Max Wilson** (Independiente) | «An Outline of the Textual Structure of the Book of Mormon» con desglose de voces, cabeceras del texto, límites de capítulos 1830 vs 1981. | Fuente para división del LdM que respeta los cambios de autor/narrador (Nefi, Jacob, Mormón, Moroni) — un eje que no existe en la Biblia. |
| **Kevin L. Barney / James T. Duke** (Independientes) | Catálogos de pares de palabras y dicción poética en el LdM vs hebreo bíblico. | Útil para la validación lingüística de que estructuras hebreas (paralelismo, quiasmo) cruzan los cánones. |

### El vacío que este proyecto llena

**No existe actualmente:**
- Una lista unificada de perícopas/narrative units para los 4 standard works completos (~5050 perícopas).
- Una metodología explícita y publicada para derivar perícopas del LdM, DyC o PGP.
- Un estudio titulado «Reverse Correlation» que sistematice cómo la Escritura restauracionista informa la división pericopal de la Biblia.

**Este proyecto es el primero en:**
1. Producir una lista exhaustiva de perícopas para los 5 volúmenes canónicos.
2. Aplicar el principio de correlación recíproca (la división de cada libro considera cómo otros libros lo referencian).
3. Unificar en una taxonomía de WordPress las relaciones cross-canónicas (testimonio múltiple, relato paralelo, cita explícita, expansión revelatoria, paralelo histórico, comentario inspirado, paralelo temático, tipología).

## Estrategia y plan de fases

### Decisión fundamental

**NO placeholders.** Cada perícopa será curada con nombre descriptivo real y rango preciso de versículos desde el inicio. El volumen de datos (~5050 términos) se maneja con fases, no con atajos.

### Orden de curaduría (de más fácil a más complejo)

| Fase | Volumen | Capítulos | Perícopas estimadas | Fuente primaria | Dificultad |
|:-----|:--------|:---------:|:-------------------:|:----------------|:----------:|
| **A** | Doctrina y Convenios | ~140 | ~350 | Encabezados oficiales del DyC (base) + StoryGuide 1–40 + Manual del Instituto (refinamiento). Cada sección larga dividida por estructura literaria (encabezado, cuerpo visionario, promesa/cierre). | ★ Baja — estructura ya dividida en secciones. Refinar: dividir secciones largas por tema. |
| **B** | Perla de Gran Precio | ~30 | ~100 | Manual del Instituto + Smoot Study Ed. + Bradshaw (estructura de pacto en Moisés). | ★ Baja — volumen pequeño pero alta densidad de correlación con AT y LdM. |
| **C** | Nuevo Testamento — Epístolas | ~200 | ~600 | BYU Charting NT + análisis de estructura retórica (salutación, acción de gracias, cuerpo, exhortación, despedida). Filemón (~4 perícopas) es el caso mínimo. | ★★ Media — requiere identificar estructura literaria de cada epístola. |
| **D** | Nuevo Testamento — Evangelios | ~90 | ~700 | Concordancia entre los Evangelios (harmony table) + Jesús el Cristo (Talmage) + análisis de intercalaciones. Cada evangelio incluye sus perícopas introductorias (prólogo de Lucas, genealogía de Mateo, etc.). | ★★ Media — perícopas ya traducidas, organizadas y validadas en Alejandría. |
| **E** | Antiguo Testamento | ~929 | ~2500 | Nelson's + divisiones litúrgicas judías (Pentateuco) + estructura retórica de profetas (oráculos, juicios, promesas) + colecciones de sabiduría. | ★★★ Alta — volumen grande, mezcla de géneros: narrativa, ley, poesía, profecía. |
| **F** | Libro de Mormón | ~240 | ~800 | StoryGuide + BYU Charting BoM + cambios de autor/narrador (Nefi, Jacob, Mormón, Moroni). Discursos largos (Benjamín, Abinadí, Alma a Coriantón, Samuel) divididos por unidad temática. | ★★★ Alta — requiere extracción manual y verificación de cambios de voz narrativa. |

**Total**: ~5050 perícopas en 6 fases.

### Estrategia de seed por fase

Cada fase consiste en:

1. **Preparación**: extraer lista de perícopas de la fuente primaria (tabla o extracción manual). Para NT Evangelios, la Concordancia entre los Evangelios ya está en Alejandría como tabla estructurada.
2. **Traducción/localización**: pasar nombres al español según criterios de nombrado (la harmony table ya está en español, lo que reduce trabajo).
3. **Curaduría**: verificar que cada perícopa sea disjunta, que cubra el capítulo completo y que los rangos de versículos sean exactos. Resolver casos edge (intercalaciones, cruces de capítulo).
4. **Seed**: insertar términos en WP vía script SQL/CLI con slug, nombre, descripción (rango de versículos) y metadata `v_inicio`/`v_fin`.
5. **Validación**: verificar que la suma de perícopas por capítulo = capítulo completo sin solapamientos.

### Estrategia de validación

- **Validación automática**: script que por cada capítulo verifique que `SUM(v_fin - v_inicio + 1)` = total de versículos del capítulo.
- **Validación de intercalaciones**: para evangelios, identificar narrativas tipo «Marcan sandwich» y verificar que cada capa tenga perícopa separada.
- **Validación de cruces de capítulo**: para perícopas que cruzan, verificar que las particiones estén correctamente numeradas.
- **Validación cruzada**: comparar perícopas de evangelios con la Concordancia entre los Evangelios y con el índice de Jesús el Cristo.
- **Validación narrativa**: para LdM, verificar que las divisiones respeten los cambios de autor/narrador (Nefi, Jacob, Mormón, Moroni).

## Metadatos de relación entre secciones de DyC (sección ↔ sección)

Para resolver la necesidad de "cómo se relacionan unas secciones con otras", se agrega una capa de metadatos de grafo a nivel de sección (además de la capa pericopal).

### Objetivo

- Mantener el plan de perícopas como unidad textual fina.
- Añadir una red de dependencias y continuidades entre secciones de DyC.
- Permitir consultas como: "qué prepara", "qué desarrolla", "qué aplica" o "qué cumple" una sección.

### Modelo propuesto

#### A) Metadatos base por sección (`dyc_section_meta`)

| Campo | Tipo | Ejemplo |
|:------|:-----|:--------|
| `section` | int | `84` |
| `fecha_recepcion` | string ISO | `1832-09-22` |
| `lugar_recepcion` | string | `Kirtland, Ohio` |
| `bloque_geo` | enum | `ohio` |
| `bloque_tema_l2` | string | `sacerdocio` |
| `bloque_tema_l3` | string | `juramento-y-convenio` |
| `actores_clave` | array[string] | `["jose-smith", "elderes"]` |

#### B) Aristas de relación (`dyc_section_edges`)

Cada fila representa una relación dirigida `from_section -> to_section`.

| Campo | Tipo | Ejemplo |
|:------|:-----|:--------|
| `from_section` | int | `84` |
| `to_section` | int | `107` |
| `tipo_relacion` | enum | `desarrollo-doctrinal` |
| `evidencia` | string corto | `Ambas tratan llaves y orden del sacerdocio` |
| `peso` | int 1..5 | `5` |
| `bidireccional` | bool | `false` |

### Tipos de relación recomendados

| Tipo | Cuándo usarlo |
|:-----|:--------------|
| `continuidad-historica` | Misma etapa o secuencia histórica inmediata |
| `desarrollo-doctrinal` | Sección posterior amplía doctrina de otra |
| `aplicacion-administrativa` | Traduce doctrina a organización/práctica |
| `cumplimiento-profetico` | Una sección cumple/explica promesa previa |
| `contexto-compartido` | Misma crisis, persecución, viaje o proyecto |
| `ordenanza-templo` | Relación por templo/ordenanzas/llaves |
| `eco-tematico` | Paralelo conceptual sin dependencia fuerte |

### Semilla inicial (ejemplos DyC)

| from | to | tipo | peso | evidencia resumida |
|:-----|:---|:-----|:----:|:-------------------|
| 20 | 21 | aplicacion-administrativa | 5 | Constitución de la Iglesia -> primeras normas operativas |
| 84 | 107 | desarrollo-doctrinal | 5 | Sacerdocio: juramento/convenio -> orden y quórumes |
| 109 | 110 | ordenanza-templo | 5 | Dedicación de Kirtland -> aparición y entrega de llaves |
| 121 | 122 | continuidad-historica | 5 | Cartas desde Liberty Jail en el mismo marco de aflicción |
| 121 | 123 | aplicacion-administrativa | 4 | Del principio espiritual -> mandato documental/legal |
| 124 | 127 | ordenanza-templo | 4 | Instrucción sobre templo de Nauvoo -> práctica bautismos por muertos |
| 127 | 128 | desarrollo-doctrinal | 5 | Práctica inicial -> doctrina extensa y fundamento teológico |
| 1 | 133 | cumplimiento-profetico | 4 | Prefacio (DyC 1) y apéndice (DyC 133) como marco profético |

### Implementación WordPress (mínima)

- Mantener `bc_pericopa` como está.
- Crear almacenamiento para secciones DyC (tabla o JSON versionado en `docs/juego-del-cinco`).
- Guardar aristas en tabla simple (`from_section`, `to_section`, `tipo_relacion`, `peso`, `evidencia`).
- En UI/consultas, exponer:
   - "Secciones relacionadas" (outbound)
   - "Secciones que apuntan a esta" (inbound)
   - Filtros por `tipo_relacion`.

### Consulta útil de ejemplo

Pregunta: "¿Qué secciones expanden DyC 84?"

1. Buscar `dyc_section_edges` donde `from_section = 84` y `tipo_relacion in (desarrollo-doctrinal, aplicacion-administrativa)`.
2. Ordenar por `peso DESC`.
3. Devolver `to_section` + `evidencia`.

Con esto, DyC deja de verse como lista plana y pasa a verse como red doctrinal e histórica navegable.

## Mejoras adoptadas de Richardson (fase de mapeo)

Estas mejoras se incorporan en fase de diseño y curaduría, sin entrar aún a seed técnico.

### 1) Doble lectura para DyC: canónica y cronológica

- Mantener la lectura en orden de secciones (1–138 + declaraciones oficiales).
- Añadir una vista paralela cronológica para análisis de contexto.
- Usar la cronología para explicar continuidad entre secciones, no para renumerar ni reorganizar la estructura canónica.

### 2) Bloques macro de contexto (sobre la capa pericopal)

- Definir bloques geográficos y temáticos (nivel alto) por periodo.
- Usar esos bloques como "marco narrativo" para comprender cadenas de revelaciones.
- Mantener las perícopas como unidad textual fina dentro de cada sección.

### 3) Puntos naturales de parada

- Marcar cortes recomendados de estudio por cierre temático y carga de lectura.
- No usar esos cortes como sustituto de perícopa ni como partición canónica.

### 4) Trazabilidad de relaciones sección ↔ sección

- Registrar relaciones de continuidad, desarrollo doctrinal y aplicación administrativa.
- Priorizar evidencia breve y explícita de por qué se relacionan dos secciones.
- Permitir consultas de vecindad doctrinal/histórica para acompañar la lectura de perícopas.

### 5) Regla sobre impacto en rangos de versículos

**Aplicación por defecto: no cambia rangos.**

La capa Richardson mejora contexto, navegación y comprensión de relaciones, pero **no obliga** a redibujar rangos pericopales ya curados. Solo se reconsideran rangos cuando aparezca una de estas señales:

1. Un rango actual fusiona dos unidades temáticas claramente separables.
2. El encabezado oficial y la evidencia contextual coinciden en un corte más natural.
3. Hay ruptura fuerte de función literaria (mandato -> promesa, visión -> interpretación, historia -> doctrina).

Si no hay esas señales, el rango vigente se conserva.

### 6) Criterio operativo para esta fase

- Primero: terminar el refinamiento de perícopas (nombres, slugs, rangos, atomicidad).
- Segundo: etiquetar contexto y relaciones (sin seed).
- Tercero: revisar casos frontera donde contexto sugiera posible recorte adicional.

### 7) Casos frontera DyC (estado actual)

Derivado de una pasada automática sobre `plan-pericopas-dyc.md` (rangos largos + posible mezcla de función literaria). Esta tabla conserva la priorización original y agrega el estado actual de resolución.

| Prioridad | Sección | Rango actual | Señal de frontera | Estado | Acción sugerida |
|:----------|:--------|:-------------|:------------------|:-------|:----------------|
| Alta | 124 | 103–122 | Bloque extenso con múltiples cierres en "Amén" y cambio de destinatarios | Resuelto (subdividido) | Ninguna |
| Alta | 107 | 22–38 | Posible transición interna entre estructura de presidencias y reglas de decisión | Resuelto (subdividido) | Ninguna |
| Alta | 20 | 1–16 | Segmento fundacional largo con densidad doctrinal e histórica | Resuelto (conservar) | Ninguna |
| Alta | 76 | 81–95 | Tramo amplio dentro de visión de reinos; puede contener subunidad interna | Resuelto (subdividido) | Ninguna |
| Alta | 109 | 22–33 | Oración dedicatoria con posibles subpeticiones diferenciadas | Resuelto (conservar) | Ninguna |
| Media | 93 | 41–53 | Mandatos familiares a varios líderes en un solo bloque | Resuelto (subdividido por destinatario y giro temático) | Ninguna |
| Media | 104 | 54–66 | Tesorería y administración; posible cambio de énfasis operativo | Resuelto (conservar) | Ninguna |
| Media | 101 | 63–75 | Bloque de congregación/protección con densidad temática | Resuelto (conservar) | Ninguna |
| Media | 103 | 29–40 | Organización del Campo de Sion y promesa condicional | Resuelto (subdividido por destinatario y función) | Ninguna |
| Media | 132 | 28–39 | Promesas de exaltación con posible inflexión doctrinal interna | Resuelto (subdividido por giro doctrinal y ejemplos patriarcales) | Ninguna |
| Media | 133 | 41–51 | Juicio/venida del Señor; posible cambio de foco retórico | Resuelto (subdividido por cambio retórico en la teofanía) | Ninguna |
| Media | 136 | 17–27 | Normas de vida con potencial subbloques éticos | Resuelto (subdividido por paso de exhortación general a normas prácticas) | Ninguna |

Regla de control para estos casos:

1. Si no hay ruptura literaria clara, se conserva el rango actual.
2. Si hay ruptura clara, se propone subdivisión con título/slug nuevos.
3. Toda propuesta debe mantener cobertura completa del capítulo/sección sin solapamientos.

### 8) Resolución de casos de prioridad alta (curaduría)

Resultado de la revisión uno a uno (con cambios aplicados en `plan-pericopas-dyc.md` para los casos resueltos):

| Sección | Rango actual | Decisión | Fundamento | Propuesta concreta |
|:--------|:-------------|:---------|:-----------|:-------------------|
| 124 | 103–122 | **Subdividir (aplicado)** | El texto presenta cierres explícitos en "Amén" (110, 114, 118, 120, 122) y cambio de destinatario entre subbloques | 103–110 / 111–114 / 115–118 / 119–120 / 121–122 |
| 107 | 22–38 | **Subdividir (aplicado)** | Transición clara entre composición/validez de decisiones de cuórumes (22–32) y funciones operativas de Doce/Setenta/sumo consejo viajante (33–38) | 22–32 y 33–38 |
| 20 | 1–16 | **Conservar** | Unidad doctrinal estable (testimonio del Libro de Mormón y divinidad de la obra) sin ruptura fuerte comprobable | Sin cambio |
| 76 | 81–95 | **Subdividir (aplicado)** | El bloque cambia de foco en 91: de destinatarios y ministración telestial (81–90) a comparación explícita con glorias terrestre/celestial y su presencia (91–95) | 81–90 y 91–95 |
| 109 | 22–33 | **Conservar** | Petición litúrgica continua en la oración dedicatoria, sin quiebre temático suficientemente nítido | Sin cambio |

Nota metodológica:

- Toda subdivisión aplicada fue validada con lectura puntual del texto por versículo.
- Se conserva la regla de cobertura completa por sección, sin solapamientos ni huecos.

Detalle aplicado a DyC 124:103–122 (lectura puntual):

1. 103–110: instrucciones a Sidney Rigdon (residencia, proclamación, permanencia en Nauvoo) -> cierre en "Así sea. Amén.".
2. 111–114: instrucciones a Amos Davies (acciones del Mesón de Nauvoo, fidelidad, mayordomía) -> cierre en "Así sea. Amén.".
3. 115–118: instrucciones a Robert D. Foster (casa para José, arrepentimiento, acciones, obediencia) -> cierre en "Así sea. Amén.".
4. 119–120: regla general de elegibilidad para comprar acciones (creencia en el Libro de Mormón y revelaciones) -> cierre en "Así sea. Amén.".
5. 121–122: remuneración y sostenimiento de trabajadores/cuórum del Mesón -> cierre en "Así sea. Amén.".

Propuesta de títulos y slugs (DyC 124:103–122):

| Nuevo rango | Título propuesto | Slug propuesto |
|:------------|:-----------------|:---------------|
| 103–110 | Instrucciones a Sidney Rigdon para permanecer en Nauvoo y apoyar la proclamación | `dyc-124-instrucciones-sidney-rigdon-permanecer-nauvoo-apoyar-proclamacion` |
| 111–114 | Amos Davies debe invertir en el Mesón de Nauvoo y humillarse ante el Señor | `dyc-124-amos-davies-debe-invertir-meson-nauvoo-humillarse-senor` |
| 115–118 | Robert D. Foster debe edificar para José y obedecer a las autoridades de Sion | `dyc-124-robert-d-foster-debe-edificar-jose-obedecer-autoridades-sion` |
| 119–120 | Solo quienes crean en el Libro de Mormón y las revelaciones pueden comprar acciones | `dyc-124-solo-crean-libro-mormon-revelaciones-pueden-comprar-acciones` |
| 121–122 | Los obreros del Mesón de Nauvoo deben recibir justa remuneración | `dyc-124-obreros-meson-nauvoo-deben-recibir-justa-remuneracion` |

Nota de aplicación:

- La propuesta de DyC 124:103–122 ya fue incorporada en `plan-pericopas-dyc.md`.

Resultado esperado: mejor mapa narrativo de DyC con estabilidad en la segmentación textual.

### 9) Bloque operativo de seguimiento: correlación recíproca y casos cross-canónicos

Este bloque convierte en seguimiento operativo dos marcos metodológicos ya definidos en este documento:

1. `División guiada por correlación: criterio recíproco`.
2. `Mapeo de casos cross-canónicos reales`.

Objetivo operativo:

- Verificar que la segmentación actual permita concordancia precisa (referencias que caen en perícopas completas, no en fracciones ambiguas).
- Confirmar que los metadatos definidos (`_evento_canonico`, `_relacion_paralela`, `_cita_de`) sean suficientes para el tipo de relación esperado.

Estado de cumplimiento (2026-07-20):

| Bloque | Cobertura en perícopas actuales | Evidencia local | Estado | ¿Suficiente para concordancia esperada? | Brecha principal |
|:-------|:--------------------------------|:----------------|:-------|:-----------------------------------------|:-----------------|
| DyC 77 ↔ Apocalipsis (criterio recíproco) | DyC segmentado y etiquetado; contraparte Apocalipsis documentada como criterio | `plan-pericopas-dyc.md` (sección 77) + este mapeo | Parcial | Parcial | Falta plan pericopal operativo del lado Apocalipsis en este workspace |
| DyC 84 / DyC 107 ↔ AT (Gn, Éx, Sal) | DyC segmentado y con evento temático; AT definido como destino de correlación | `plan-pericopas-dyc.md` (secciones 84 y 107) + este mapeo | Parcial | Parcial | Falta verificación pericopal ejecutada en los libros AT referenciados |
| Mapeo de casos cross-canónicos reales | Tipos y qualifiers definidos (historico, expansion, comentario, tematico, tipo) | sección `Mapeo de casos cross-canónicos reales` | Definido (no ejecutado) | No aún | Falta ejecución en términos reales (`bc_pericopa`) y validación de consultas de concordancia |

Auditoría crítica de división (lectura manual de secciones DyC):

| Sección | División revisada | Cuestionamiento aplicado | Resultado |
|:--------|:------------------|:-------------------------|:----------|
| DyC 77 | 1–4 / 5–7 / 8–10 / 11 / 12–14 / 15 | Se verificó si los cortes eran solo por encabezado o por unidad real de pregunta-respuesta y referencia a Apocalipsis | **Se conserva**. Cada bloque mantiene unidad temática y referencia apocalíptica coherente |
| DyC 84 | tramo 62–76 | Se cuestionó el corte previo 62–68 / 69–76 por romper a mitad la lista de señales (65–73) | **Se ajustó** en el plan a 62–64 / 65–73 / 74–76 para respetar función literaria |
| DyC 107 | 22–32 / 33–38 y 39–57 | Se evaluó si la transición interna de cuórumes y luego orden patriarcal justificaba corte adicional | **Se conserva**. 22–32 -> estructura/validez; 33–38 -> función viajante; 39–57 -> bloque genealógico-histórico consistente |

Conclusión de suficiencia tras auditoría:

1. Para DyC interno, la división queda metodológicamente consistente tras el ajuste de DyC 84:62–76.
2. Para concordancia recíproca completa, aún falta ejecución del lado no-DyC (Apocalipsis y bloques AT referenciados).
3. Por tanto, el bloque sigue en estado `Parcialmente cumplido` a nivel cross-canónico, aunque ya está `Cumplido` en curaduría DyC.

Criterios de aceptación de este bloque:

1. Cada referencia recíproca crítica (DyC 77/84/107) apunta a una perícopa destino completa y estable.
2. Los casos del mapeo cross-canónico se materializan con metadatos reales (no solo ejemplo documental).
3. Una consulta por `_evento_canonico` y qualifiers devuelve rutas de concordancia sin ambigüedad de límites.
4. El resultado puede auditarse con una matriz fuente -> destino -> tipo de relación -> estado.

Veredicto operativo actual:

- El marco metodológico sí se cumple de forma suficiente para la curaduría interna ya aplicada en DyC.
- Aún no es suficiente para declarar satisfecha la concordancia cross-canónica objetivo de punta a punta.
- Se considera `Parcialmente cumplido` hasta ejecutar y validar las contrapartes fuera de DyC y su materialización en taxonomía/termmeta.

## Estado actual

| Aspecto | Estado |
|:--------|:-------|
| Taxonomía `bc_pericopa` en WP | ✅ Creada |
| Términos definidos | ❌ Ninguno |
| Fuentes identificadas | ✅ 5 volúmenes cubiertos |
| Criterios de nombrado | ✅ Definidos |
| Criterios expandidos (distingo, atomicidad, edge cases, cruces) | ✅ 6 ejes, 5 casos edge documentados |
| Validación desde Alejandría | ✅ Concordancia entre los Evangelios + Jesús el Cristo + Manuales disponibles en corpus |
| Plan de fases | ✅ 6 fases definidas |
| Seed ejecutable | ⏳ Pendiente — siguiente paso tras crear la taxonomía |

## Próximo paso

Con la resolución de DyC 132, 133 y 136, la subfase de curaduría DyC queda cerrada. El siguiente paso para cerrar Fase A es poblar `bc_pericopa` con el seed de DyC y validar cobertura completa sin solapes ni huecos.

## Resumen consolidado de criterios aplicados (DyC)

Esta consolidación resume el criterio realmente aplicado en la resolución de casos frontera de DyC (124, 107, 76, 93, 103, 132, 133, 136), con el mismo estándar de precisión solicitado en la curaduría manual.

1. **Cobertura estricta y disjunta**
   - Todo ajuste conserva cobertura total del rango original, sin huecos ni solapamientos.
   - Cada versículo queda en una sola perícopa.

2. **Subdividir solo con evidencia textual clara**
   - Se subdivide cuando existe un cambio verificable de función literaria, destinatario o foco retórico.
   - Si no hay quiebre nítido, el rango se conserva.

3. **Jerarquía de señales de corte**
   - Señal fuerte: cierre explícito (p. ej., "Amén") junto con inicio de bloque nuevo.
   - Señal media: cambio de destinatario (de instrucción general a mandato personal, o viceversa).
   - Señal media: pivote doctrinal (promesa -> ejemplo histórico -> advertencia).
   - Señal media: cambio de escena retórica (teofanía/invocación -> declaración de identidad -> juicio).

4. **Prioridad al cambio de destinatario y función**
   - En tramos de mandatos a personas o grupos, el cambio de destinatario se trata como candidato primario de frontera.
   - En tramos doctrinales extensos, el cambio de función (exhortación, promesa, juicio, instrucción práctica) define el corte.

5. **Granularidad controlada (ni macro ni micro artificial)**
   - Se evita dejar bloques demasiado amplios que mezclen unidades distintas.
   - Se evita microfragmentar cuando el discurso sigue siendo una misma unidad temática.

6. **Títulos y slugs alineados al bloque final**
   - Todo bloque nuevo recibe título y slug que describen exactamente su función principal.
   - La nomenclatura debe reflejar el nuevo límite narrativo/doctrinal introducido.

7. **Trazabilidad de decisión**
   - Cada caso frontera queda con estado explícito (Resuelto: subdividido o conservar) y fundamento breve en esta guía.
   - Las decisiones en `plan-pericopas-dyc.md` y en este mapeo se mantienen sincronizadas.

**Cambio clave**: la eliminación del supuesto de "1 perícopa = 1 capítulo en epístolas y libros no narrativos" elevó la estimación total de ~3400 a ~5050 perícopas (~48% de incremento). Esto refleja que la división por estructura literaria aplica a TODOS los géneros canónicos, no solo a la narrativa densa.
