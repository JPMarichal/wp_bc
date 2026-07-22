# Diagramas Mermaid en wp_bc

## Propósito

Los diagramas Mermaid (vía plugin MerPress) permiten representar visualmente conceptos, procesos, genealogías, jerarquías y cronologías en los artículos. Se recomienda su uso cuando un diagrama **simplifica o aclara** una explicación que de otro modo requeriría varios párrafos.

## Cuándo usar un diagrama

Usar diagramas cuando el contenido involucre:

- **Procesos** — pasos de un acontecimiento, secuencia de una revelación, etapas de un viaje
- **Genealogías** — árboles familiares, linajes, ramas de descendencia
- **Jerarquías** — cadenas de mando, organización eclesiástica, estratos sociales, estructura de reinos
- **Cronologías** — líneas de tiempo, duración de ministerios, sucesión de gobernantes
- **Relaciones** — conexiones entre personas, lugares, conceptos y eventos
- **Flujos** — migraciones de pueblos, difusión de enseñanzas, movimiento de población
- **Comparaciones** — distribución de temas, énfasis doctrinal, proporciones
- **Ciclos** — apostasía-arrepentimiento-restauración, progreso espiritual, transformación

## Tipos de diagrama disponibles

### 1. Flowchart (`flowchart`)
Procesos, rutas y flujos de decisión. El tipo más versátil.

```
flowchart TD
    A[Inicio] --> B{¿Decisión?}
    B -->|Sí| C[Acción]
    B -->|No| D[Alternativa]
```

**Ejemplos wp_bc:**
- Ruta del Éxodo: Egipto → Sinaí → Canaán
- Proceso de traducción del Libro de Mormón
- Viaje de Lehi: Jerusalén → Desierto → Mar → Tierra prometida
- Esquema de las dispensaciones

### 2. Timeline (`timeline`)
Eventos en orden cronológico. Ideal para sucesiones y secuencias históricas.

```
timeline
    title Profetas del Reino del Norte
    930 a.C. : Jeroboam I
    900 a.C. : Nadab
    880 a.C. : Omri : Acab
    850 a.C. : Eliseo
    750 a.C. : Oseas
```

**Ejemplos wp_bc:**
- Línea de vida de profetas y personajes
- Cronología de la Restauración (1820–1844)
- Sucesión de reyes de Israel y Judá
- Evolución de un concepto doctrinal a través del tiempo
- Eventos de la Semana Santa

### 3. Sequence (`sequenceDiagram`)
Interacciones entre actores a lo largo del tiempo.

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

**Ejemplos wp_bc:**
- Diálogos entre personajes bíblicos (Abraham y Dios, Moisés y Faraón)
- Secuencia de visiones (Lehi, Nefi, Juan)
- Ciclo profeta-pueblo-Dios en el libro de los Jueces
- Interacciones en concilios de la Iglesia

### 4. Mindmap (`mindmap`)
Jerarquías conceptuales y mapas de ideas.

```
mindmap
  root((Sacerdocio))
    Sacerdocio de Melquisedec
      Apóstol
      Profeta
      Patriarca
      Sumo Sacerdote
      Setenta
    Sacerdocio Aarónico
      Obispo
      Sacerdote
      Maestro
      Diácono
```

**Ejemplos wp_bc:**
- Estructura de libros canónicos
- Jerarquía de llamamientos eclesiásticos
- Dones del Espíritu: categorías y subcategorías
- Atributos divinos
- Estratos sociales (rey → noble → campesino → siervo)
- Temas y subtemas doctrinales

### 5. Quadrant (`quadrantChart`)
Comparación bidimensional de conceptos.

```
quadrantChart
    title Enseñanzas de los profetas
    x-axis "Levítico" --> "Evangelio"
    y-axis "Juicio" --> "Misericordia"
    quadrant-1 "Isaías"
    quadrant-2 "Oseas"
    quadrant-3 "Amós"
    quadrant-4 "Miqueas"
```

**Ejemplos wp_bc:**
- Comparar énfasis entre libros proféticos
- Mapear personajes por dos ejes (fidelidad-influencia, antigüedad-trascendencia)
- Categorizar enseñanzas (ley-vs-gracia, juicio-vs-misericordia)

### 6. Gantt (`gantt`)
Cronogramas con duración y solapamiento.

```
gantt
    title Reyes de Israel
    dateFormat YYYY
    section Reino del Norte
    Jeroboam I     :930, 910
    Nadab          :910, 909
    Baasa          :909, 886
    section Reino del Sur
    Roboam         :930, 913
    Abías          :913, 911
    Asa            :911, 870
```

**Ejemplos wp_bc:**
- Duración de ministerios proféticos
- Construcción del templo (etapas)
- Períodos de reinado
- Vida de personajes históricos
- Etapas de la historia de la Iglesia

### 7. State (`stateDiagram-v2`)
Estados y transiciones entre ellos.

```
stateDiagram-v2
    [*] --> Fiel
    Fiel --> Apostasía: Olvido
    Apostasía --> Arrepentimiento: Profeta
    Arrepentimiento --> Restauración: Pacto
    Restauración --> Fiel: Fidelidad
```

**Ejemplos wp_bc:**
- Ciclo de apostasía y restauración
- Etapas del progreso espiritual
- Proceso de conversión (investigador → miembro → discípulo)
- Transformación de organizaciones o reinos
- Cambios en la cadena de mando (sucesión)

### 8. Entity Relationship (`erDiagram`)
Relaciones entre entidades. Ideal para genealogías y conexiones.

```
erDiagram
    ABRAHAM ||--o{ ISAAC : padre
    ISAAC ||--o{ JACOB : padre
    JACOB ||--o{ JOSE : padre
    JACOB ||--o{ RUBEN : padre
    JOSE ||--o{ EFRAIN : padre
    JOSE ||--o{ MANASES : padre
```

**Ejemplos wp_bc:**
- Genealogías familiares (árboles de personajes bíblicos)
- Relaciones entre personas, lugares y eventos
- Cadenas de mando (quién llamó a quién, quién presidió)
- Conexiones entre conceptos teológicos
- Organización de la Iglesia
- Red de contactos de un personaje histórico

### 9. Pie (`pie`)
Proporciones y distribución.

```
pie
    title Libro de Mormón por autor
    "Nefi" : 55
    "Jacob" : 7
    "Enós" : 1
    "Mosíah" : 29
    "Alma" : 63
    "Mormón" : 50
```

**Ejemplos wp_bc:**
- Distribución de contenido por libro o sección
- Proporción de temas en un conjunto de escrituras
- Porcentaje de cobertura geográfica
- Distribución de citas del Antiguo Testamento en el Nuevo

### 10. XY Chart (`xychart-beta`)
Visualización cuantitativa: barras, líneas, dispersión.

```
xychart-beta
    title "Menciones de 'fe' por libro del NT"
    x-axis ["Mateo", "Marcos", "Lucas", "Juan", "Hechos", "Romanos"]
    y-axis "Menciones" 0 --> 30
    bar [8, 5, 12, 9, 15, 25]
```

**Ejemplos wp_bc:**
- Frecuencia de palabras clave por libro
- Comparaciones cuantitativas entre textos
- Crecimiento numérico de la Iglesia en sus inicios
- Distribución de datos estadísticos

### 11. Sankey (`sankey-beta`)
Flujos y migraciones entre puntos de origen y destino.

```
sankey-beta
    'Ur', 100
    'Harán', 60
    'Canaán', 40
    'Egipto', 10
    'Ur Harán', 40
    'Harán Canaán', 50
    'Harán Egipto', 10
    'Canaán Egipto', 10
```

**Ejemplos wp_bc:**
- Migraciones de pueblos (Abraham, Israel, Lehi, Mulek)
- Difusión del cristianismo primitivo
- Propagación del Evangelio en la Restauración
- Flujo de linajes genealógicos
- Dispersión de las tribus de Israel

### 12. Architecture (`architecture-beta`)
Estructura de sistemas, jerarquías y organización.

```
architecture-beta
    group Iglesia(cloud)[Iglesia de Jesucristo]
    service Profeta(server)[Profeta] in Iglesia
    service Doce(database)[Cuórum de los Doce] in Iglesia
    service Setenta(disk)[Setenta] in Iglesia
    Profeta:R --> L Doce:L
    Doce:R --> L Setenta:L
```

**Ejemplos wp_bc:**
- Organigrama de la Iglesia
- Cadena de transmisión de la revelación
- Jerarquía de llamamientos y oficios
- Estratos sociales en la antigüedad
- Estructura de reinos, provincias y ciudades
- Organización del templo y sus ordenanzas

### 13. Block (`block-beta`)
Organigramas y diagramas de bloques.

```
block-beta
    columns 3
    Dios["Dios"] space Cristo["Jesucristo"]
    space Espiritu["Espíritu Santo"] space
    Profeta["Profeta"]:3
    Doce["Apóstoles"]:3
```

**Ejemplos wp_bc:**
- Organigrama de la Iglesia
- Relaciones entre miembros de la Trinidad
- Estructura de la Primera Presidencia y el Cuórum de los Doce
- Jerarquía administrativa de un reino

### 14. Git Graph (`gitGraph`)
Ramificaciones y bifurcaciones. Ideal para linajes con divisiones.

```
gitGraph
    commit id: "Abraham"
    branch Isaac
    commit id: "Jacob"
    branch Juda
    commit id: "David"
    checkout Isaac
    branch Ismael
    commit id: "Naciones árabes"
```

**Ejemplos wp_bc:**
- Linajes familiares con ramas
- Separación de Israel y Judá
- Bifurcaciones textuales en manuscritos
- Divergencias doctrinales o cismas
- Genealogías complejas con múltiples ramas

### 15. Journey (`journey`)
Trayectoria o experiencia de un personaje.

```
journey
    title Viaje de Abraham
    section Ur
      Vida en Ur: 3: Abraham
      Llamamiento: 5: Abraham
    section Harán
      Estancia: 4: Abraham
    section Canaán
      Llegada a Siquem: 5: Abraham
      Altar en Bet-el: 4: Abraham
```

**Ejemplos wp_bc:**
- Trayectoria de personajes bíblicos
- Viajes misionales de Pablo
- Peregrinaciones
- Experiencia espiritual del creyente

## Criterios para decidir

| Pregunta | Si la respuesta es sí... |
|:---------|:-------------------------|
| ¿El concepto tiene 3+ pasos o etapas? | Usar `flowchart` o `timeline` |
| ¿Hay personas/entidades interactuando? | Usar `sequenceDiagram` |
| ¿Hay una jerarquía de 3+ niveles? | Usar `mindmap`, `architecture-beta` o `block-beta` |
| ¿Hay relaciones familiares entre personas? | Usar `erDiagram` o `gitGraph` |
| ¿Hay un antes/después con transformación? | Usar `stateDiagram-v2` |
| ¿Hay flujo de un lugar a otro? | Usar `sankey-beta` |
| ¿Hay datos cuantitativos que comparar? | Usar `pie`, `xychart-beta` o `quadrantChart` |
| ¿Hay una trayectoria vital? | Usar `journey` |

## Formato del bloque en HTML

```
<!-- wp:merpress/mermaidjs -->
<div class="wp-block-merpress-mermaidjs diagram-source-mermaid"><pre class="mermaid">flowchart TD
    A[Inicio] --> B[Fin]</pre></div>
<!-- /wp:merpress/mermaidjs -->
```

Los comentarios `<!-- wp:... -->` son obligatorios para que Gutenberg reconozca el bloque.

## Reglas de sintaxis

1. **Cada declaración en su propia línea** separada por `\n`
2. **Sin indentación** al inicio de línea
3. **Evitar paréntesis `()`** en etiquetas de nodo
4. **Evitar comillas dobles `"`** alrededor de etiquetas
5. **Evitar caracteres especiales**: emojis, `<br/>`, `&amp;`
6. **Evitar palabras clave reservadas** como `graph`, `call`, `end` como IDs

## Referencias

- [Mermaid.js Documentation](https://mermaid.js.org/intro/)
- [Mermaid Live Editor](https://mermaid.live/)
- Plugin: MerPress v1.1.11 (bloque `merpress/mermaidjs`)
