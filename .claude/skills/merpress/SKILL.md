---
name: merpress
description: Crear y manipular diagramas Mermaid.js usando el plugin MerPress en WordPress, dentro del tema wp_bc. Usar SIEMPRE que se necesite: (1) crear un diagrama nuevo en un artículo, (2) reemplazar diagramas ASCII existentes por Mermaid, (3) corregir errores de sintaxis Mermaid, o (4) diagnosticar por qué un diagrama no se renderiza en editor o frontend.
---

# MerPress para wp_bc

## Regla fundamental: Gutenberg blocks desde el inicio

Cada elemento del artículo debe ser su propio bloque Gutenberg. NO usar HTML clásico suelto. Artículos creados como classic block causan que los bloques MerPress no se rendericen en la UI.

### Cómo crear un artículo correcto

1. Usar `wp_insert_post()` + `wp_update_post()` con `post_content` que contenga marcadores de bloque (`<!-- wp:paragraph -->`, `<!-- wp:heading -->`, etc.)
2. Cada párrafo, heading, imagen, lista, cita y diagrama va envuelto en su propio `<!-- wp:block-name -->...<!-- /wp:block-name -->`
3. NO usar `$wpdb->update()` para crear — solo para corregir contenido existente. Usar `wp_update_post()` para artículos nuevos.
4. Verificar con `has_blocks($post->post_content)` que devuelva `true` después de la creación

### Estructura de bloques comunes

```
<!-- wp:paragraph --><p>Texto del párrafo.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Título</h2><!-- /wp:heading -->
<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Subtítulo</h3><!-- /wp:heading -->
<!-- wp:image --><figure class="wp-block-image"><img src="..." alt="..."/></figure><!-- /wp:image -->
<!-- wp:quote --><blockquote class="wp-block-quote"><p>Cita</p></blockquote><!-- /wp:quote -->
<!-- wp:list --><ol class="wp-block-list"><li>Item</li></ol><!-- /wp:list -->
```

## Bloque MerPress: sintaxis

### Formato correcto del bloque

```
<!-- wp:merpress/mermaidjs -->
<div class="wp-block-merpress-mermaidjs diagram-source-mermaid"><pre class="mermaid">MERMAID_CODE</pre></div>
<!-- /wp:merpress/mermaidjs -->
```

Los comentarios HTML `<!-- wp:... -->` son CRÍTICOS. Sin ellos el bloque no es reconocido por Gutenberg.

### Reglas de sintaxis Mermaid 11.9.0 para wp_bc

1. **Cada declaración en su propia línea** con `\n`. NO poner todo en una línea.
2. **Sin indentación** al inicio de línea. El primer carácter después de `\n` debe ser el ID del nodo, no un espacio.
3. **Evitar paréntesis `()`** en etiquetas de nodo. Usar `A[Texto sin parentesis]` no `A["Texto (con parentesis)"]`.
4. **Evitar comillas dobles `"`** alrededor de etiquetas y labels. Usar `A[Texto]` no `A["Texto"]`.
5. **Evitar caracteres especiales**: emojis, `&lt;br/&gt;`, `&amp;`, etc. dentro del contenido Mermaid.
6. **Evitar palabras clave reservadas** como `graph`, `call`, `end` como IDs de nodo.

### Formato que funciona

```
graph LR
A[Placa Africana Sinai] -->|N-S| B[Fosa del Mar Muerto]
B -->|S-N| C[Placa Arabiga]
```

- `graph` (o `flowchart`) + orientación (`LR`, `TB`, `RL`, `BT`)
- Nodos con `ID[Etiqueta]` para rectángulo
- Flechas con `-->` (sin etiqueta) o `-->|etiqueta|` (con etiqueta)
- Sin espacios de indentación después del `\n`

## Tipos de diagrama disponibles

MerPress (Mermaid.js v11) soporta 20+ tipos de diagramas. Todos se crean con el mismo bloque:

```
<!-- wp:merpress/mermaidjs -->
<div class="wp-block-merpress-mermaidjs diagram-source-mermaid"><pre class="mermaid">TIPO_DIAGRAMA
...sintaxis...</pre></div>
<!-- /wp:merpress/mermaidjs -->
```

### Diagramas para wp_bc

#### 1. Flowchart — procesos, rutas, flujos de decisión
```
flowchart TD
    A[Inicio] --> B{¿Decisión?}
    B -->|Sí| C[Acción]
    B -->|No| D[Alternativa]
```
**Casos de uso wp_bc:**
- Rutas de migración o viajes (Éxodo, viajes de Lehi, travesías misionales)
- Procesos: cómo se tradujo el Libro de Mormón, cómo se organizó la Iglesia
- Decisiones: el proceso de revelación, pasos para el arrepentimiento
- Secuencias de acontecimientos históricos

#### 2. Timeline — eventos cronológicos
```
timeline
    title Historia de Israel
    1800 a.C. : Abraham : Isaac : Jacob
    1400 a.C. : Éxodo : Sinaí
    1000 a.C. : David : Salomón
```
**Casos de uso wp_bc:**
- Líneas de vida de profetas y personajes bíblicos
- Cronología de la Restauración
- Sucesión de imperios o reinos
- Evolución de un concepto doctrinal a través del tiempo

#### 3. Sequence — interacciones entre actores
```
sequenceDiagram
    participant Dios
    participant Profeta
    participant Pueblo
    Dios->>Profeta: Revelación
    Profeta->>Pueblo: Enseñanza
    Pueblo-->>Profeta: Pregunta
    Profeta-->>Dios: Súplica
```
**Casos de uso wp_bc:**
- Diálogos entre personajes bíblicos
- Secuencia de visiones (Lehi, Nefi, Juan)
- Interacciones profeta-pueblo-Dios
- Ciclo de apostasía-arrepentimiento-restauración

#### 4. Mindmap — jerarquías y mapas conceptuales
```
mindmap
  root((Escrituras))
    Antiguo Testamento
      Pentateuco
      Profetas
      Escritos
    Nuevo Testamento
      Evangelios
      Epístolas
    Libro de Mormón
      Placa menor
      Placa mayor
```
**Casos de uso wp_bc:**
- Estructura de libros canónicos
- Jerarquías conceptuales: dones del Espíritu, atributos divinos
- Organización de temas doctrinales
- Desglose de genealogías extensas (árboles conceptuales)
- Estratos sociales: desde rey hasta siervo

#### 5. Quadrant — comparación bidimensional
```
quadrantChart
    title Énfasis doctrinal
    x-axis "Doctrina" --> "Práctica"
    y-axis "Antiguo" --> "Nuevo"
    quadrant-1 "Profetas mayores"
    quadrant-2 "Evangelios"
    quadrant-3 "Epístolas"
    quadrant-4 "Apocalipsis"
```
**Casos de uso wp_bc:**
- Comparar énfasis entre libros o autores
- Mapear enseñanzas: doctrina vs. práctica, antiguo vs. moderno
- Categorizar personajes por dos ejes (fidelidad, influencia)

#### 6. Gantt — cronogramas con duración
```
gantt
    title Ministerios proféticos
    dateFormat YYYY
    section Israel
    Oseas           :750, 715
    Isaías          :740, 680
    section Judá
    Jeremías        :627, 580
    Ezequiel        :593, 570
```
**Casos de uso wp_bc:**
- Duración de ministerios proféticos
- Cronograma de construcción del templo
- Períodos de reinado de reyes
- Etapas de la vida de personajes históricos

#### 7. State — estados y transiciones
```
stateDiagram-v2
    [*] --> Fiel
    Fiel --> Apostasía: Olvido
    Apostasía --> Arrepentimiento: Profeta
    Arrepentimiento --> Restauración: Pacto
    Restauración --> Fiel
```
**Casos de uso wp_bc:**
- Ciclo de apostasía y restauración
- Etapas del progreso espiritual
- Proceso de conversión
- Transformación de organizaciones o reinos
- Cambios en la cadena de mando

#### 8. Entity Relationship — relaciones entre entidades
```
erDiagram
    PROFETA ||--o{ REVELACION : recibe
    REVELACION ||--o{ LIBRO : produce
    LIBRO ||--o{ CAPITULO : contiene
    CAPITULO ||--o{ VERSICULO : contiene
```
**Casos de uso wp_bc:**
- Genealogías familiares (quién es hijo de quién, quién se casó con quién)
- Relaciones entre personas, lugares y eventos bíblicos
- Cadenas de mando: quién llamó a quién, quién presidió a quién
- Conexiones entre conceptos teológicos
- Organización de la Iglesia: estructura jerárquica

#### 9. Pie — proporciones y distribución
```
pie
    title Distribución del Libro de Mormón
    "1 Nefi" : 22
    "2 Nefi" : 33
    "Jacob" : 7
    "Enós" : 1
    "Mosíah" : 29
    "Alma" : 63
```
**Casos de uso wp_bc:**
- Distribución de contenido por libro o sección
- Proporción de temas en un conjunto de escrituras
- Porcentaje de cobertura geográfica

#### 10. XY Chart — barras, líneas, dispersión
```
xychart-beta
    title "Menciones de 'pacto' por libro"
    x-axis ["Génesis", "Éxodo", "Deuteronomio", "Salmos"]
    y-axis "Menciones" 0 --> 20
    bar [15, 18, 10, 14]
```
**Casos de uso wp_bc:**
- Frecuencia de palabras o conceptos por libro
- Comparaciones cuantitativas entre textos
- Distribución de datos estadísticos

#### 11. Sankey — flujos y migraciones
```
sankey-beta
    Ur, 100
    Harán, 60
    Canaán, 40
    Ur Harán, 40
    Harán Canaán, 50
    Ur Egipto, 10
```
**Casos de uso wp_bc:**
- Migraciones de pueblos (Abraham, Israel, pueblo de Lehi)
- Difusión del cristianismo primitivo
- Flujo de linajes genealógicos
- Propagación del Evangelio en la Restauración

#### 12. Architecture — estructura de sistemas y jerarquías
```
architecture-beta
    group Iglesia(cloud)[Iglesia de Jesucristo]
    service Profeta(server)[Profeta] in Iglesia
    service Doce(database)[Cuórum de los Doce] in Iglesia
    service Setenta(disk)[Setenta] in Iglesia
    Profeta:R --> L Doce:L
    Doce:R --> L Setenta:L
```
**Casos de uso wp_bc:**
- Estructura de la Iglesia: cadenas de mando y presidencia
- Organización del sacerdocio
- Jerarquía de llamamientos eclesiásticos
- Estratos sociales en la antigüedad
- Estructura de reinos, provincias y ciudades
- Organización del templo y sus ordenanzas

#### 13. Block — diagramas de bloques y sistemas
```
block-beta
    columns 3
    Dios["Dios"] space Cristo["Jesucristo"]
    space Espiritu["Espíritu Santo"] space
    Profeta["Profeta"]:3
    Doce["Apóstoles"]:3
```
**Casos de uso wp_bc:**
- Organigrama de la Iglesia
- Relaciones entre miembros de la Trinidad
- Cadena de transmisión de la revelación
- Jerarquía administrativa

#### 14. Git Graph — ramificaciones y bifurcaciones
```
gitGraph
    commit id: "Abraham"
    branch Isaac
    commit id: "Jacob"
    branch Jose
    commit id: "Efraín"
    checkout main
    commit id: "Ismael"
```
**Casos de uso wp_bc:**
- Linajes familiares con ramas y divergencias
- Bifurcaciones textuales en manuscritos
- Separación de reinos (Israel y Judá)
- Divergencias doctrinales o cismas
- Genealogías complejas con múltiples ramas

#### 15. User Journey — viaje o trayectoria
```
journey
    title Viaje de Abraham
    section Ur de los Caldeos
      Vida en Ur: 3: Abraham
      Llamamiento: 5: Abraham
    section Harán
      Estancia en Harán: 4: Abraham
    section Canaán
      Llegada: 5: Abraham
      Altar: 4: Abraham
```
**Casos de uso wp_bc:**
- Trayectoria de personajes bíblicos (viajes, peregrinaciones)
- Experiencia espiritual del creyente
- Progresión en la vida de un profeta
- Viajes misionales

### Resumen rápido

| Keyword | Diagrama | Mejor para |
|:--------|:---------|:-----------|
| `flowchart` | Flowchart | Procesos, decisiones, rutas |
| `timeline` | Timeline | Cronología de eventos |
| `sequenceDiagram` | Secuencia | Interacciones, diálogos |
| `mindmap` | Mindmap | Jerarquías conceptuales |
| `quadrantChart` | Cuadrante | Comparación 2D |
| `gantt` | Gantt | Cronogramas con duración |
| `stateDiagram-v2` | Estado | Ciclos, transformaciones |
| `erDiagram` | ER | Relaciones, genealogías |
| `pie` | Pastel | Proporciones |
| `xychart-beta` | XY | Barras, líneas |
| `sankey-beta` | Sankey | Flujos, migraciones |
| `architecture-beta` | Arquitectura | Jerarquías, cadenas de mando |
| `block-beta` | Bloques | Organigramas |
| `gitGraph` | Git Graph | Ramas genealógicas |
| `journey` | Journey | Trayectorias vitales |

## Diagnóstico de errores

| Síntoma | Causa probable | Solución |
|---------|---------------|----------|
| "Syntax error" en editor | Paréntesis, comillas, o caracteres especiales | Simplificar etiquetas, quitar paréntesis |
| Se ve en editor, no en UI | blockName incorrecto (`core/html` en vez de `merpress/mermaidjs`) | Revisar con `parse_blocks()`, corregir blockName |
| No se ve ni en editor ni UI | Faltan comentarios de bloque `<!-- wp:... -->` | Reconstruir con `serialize_blocks()` |
| Artículo es un solo bloque classic | Se usó HTML suelto sin marcadores de bloque | Ejecutar "Convert to Blocks" o reescribir con bloques |

## Pipeline de creación

1. Redactar `post_content` con marcadores de bloque Gutenberg
2. Usar `wp_insert_post()` para crear
3. Verificar con `has_blocks()` y `parse_blocks()`
4. No tocar con `$wpdb->update()` a menos que sea estrictamente necesario (solo para corregir contenido legacy)
