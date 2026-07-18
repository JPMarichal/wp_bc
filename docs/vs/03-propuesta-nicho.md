# Propuesta de Nicho — Verdades Eternas

## Declaración de Nicho

> **Verdades Eternas** es la primera **enciclopedia bíblica de la Restauración en español**:
> una plataforma de referencia y herramientas de estudio para Ven, Sígueme que integra
> geografía interactiva, biografías profundas, conexiones con las Escrituras de la
> Restauración y recursos descargables, todo desde una perspectiva SUD y en español.

## Posicionamiento

### Para el usuario (alumno de Ven, Sígueme)

> "Cuando estudies el Antiguo Testamento esta semana y quieras saber **dónde** pasó,
> **quién** era ese personaje, **qué** dice el Libro de Mormón al respecto y **cómo**
> visualizarlo todo, ven a Verdades Eternas."

### Para el maestro de Escuela Dominical

> "Cuando tengas que preparar tu clase de Ven, Sígueme y necesites un mapa para mostrar,
> una biografía para contextualizar, una conexión con la Restauración para profundizar
> y un PDF para imprimir, Verdades Eternas es tu kit de herramientas."

### Diferencia con la competencia

| Sitio | Enfoque | Lo que NO hace |
|-------|---------|----------------|
| Scripture Central | KnoWhys + video | Mapas interactivos, español nativo, biografías sistematizadas |
| Bible Central | Guías capítulo por capítulo | Mapas 3D, integración semanal CFM, español |
| Interpreter Foundation | Análisis académico | Contenido visual, descargables, español |
| CFM Corner | Guía de estudio integral | Mapas, biografías, español |
| Church materials | Oficial | Geografía, biografías profundas, herramientas propias |
| **Verdades Eternas** | **Geografía + Biografías + Conexiones + Herramientas** | — |

## Los 6 Pilares de Contenido

### Pilar 1: Enciclopedia de Personajes Bíblicos

**Qué es**: Biografías estructuradas de figuras del Antiguo Testamento con infobox, mapa de
ubicaciones asociadas, escrituras clave, galería de imágenes, árbol genealógico.

**Formato** (por personaje):
- Biografía narrativa (misma plantilla que `bc_quote_author`)
- Infobox con datos: nombre, fechas, rol, familia, escrituras asociadas
- Mapa de ubicaciones donde vivió/ministró
- Conexiones con la Restauración
- Galería de arte
- PDF descargable

**Alcance**: 100+ personajes (principales y secundarios)

**Cómo escala**: Framework `bc_quote_author` ya existe. Skill `biografia-persona` modificado
para personajes bíblicos. Alejandría como fuente de investigación.

**Valor único**: No existe en español. En inglés está disperso (Bible Central tiene algunos,
pero no sistematizados con infobox y mapas).

### Pilar 2: Geografía Bíblica Interactiva

**Qué es**: Mapas 3D interactivos de ubicaciones, rutas, territorios, eventos del AT.

**Formatos**:
- Página individual por ubicación (CPT `bc_location` existente): mapa 3D, datos, escrituras
- Mapas de rutas: Éxodo, viajes de Abraham, conquista, exilios
- Mapas de territorios: 12 tribus, reinos de Israel y Judá, imperios
- Mapas incrustados en cada guía semanal

**Alcance**: 300+ ubicaciones, 15+ rutas/eventos mapeados

**Cómo escala**: CPT `bc_location` + MapLibre GL ya existen. Escalar agregando ubicaciones
del AT con el skill `glosario-ubicaciones-contenido`.

**Valor único**: **Ningún sitio de CFM en ningún idioma** ofrece mapas interactivos 3D
integrados con el estudio semanal. Es el diferenciador más fuerte de la plataforma.

### Pilar 3: Conexiones con la Restauración

**Qué es**: Para cada bloque semanal o personaje, pasajes paralelos del Libro de Mormón,
Doctrina y Convenios y Perla de Gran Precio que iluminan el texto del AT.

**Formato**:
- Por bloque semanal: tabla de conexiones con enlaces
- Por personaje: cómo aparece en otras Escrituras
- Artículos temáticos: "Cómo Isaías se cumple en las Américas", "La Pascua como tipo de Cristo"

**Alcance**: 52 guías semanales + 100 personajes + 30+ artículos temáticos

**Cómo escala**: Alejandría (búsqueda semántica) identifica conexiones. Skill nuevo para
generar las páginas de conexiones.

**Valor único**: Los sitios en inglés tienen esto disperso. En español nadie lo ha hecho
sistemáticamente.

### Pilar 4: Herramientas de Estudio Visuales

**Qué es**: Infografías, cronologías, genealogías interactivas, tablas comparativas.

**Formatos**:
- Cronología del AT (desde Adán hasta Malaquías, con eventos clave)
- Genealogías interactivas (árboles: Abraham → Isaac → Jacob → 12 tribus, etc.)
- Tabla de reyes de Israel y Judá
- Tabla de profetas (cuándo y dónde profetizaron)
- Línea de tiempo de imperios (Egipto, Asiria, Babilonia, Persia)
- Paralelismos: eventos del AT → eventos del LdM

**Alcance**: 20+ herramientas visuales, actualizables cada ciclo

**Cómo escala**: Datos estructurados en JSON → scripts de generación de gráficos/tablas.
Una vez construidos los datos, son reutilizables cada 4 años.

**Valor único**: Nadie en español produce estas herramientas para CFM. Son el tipo de
recurso que los maestros imprimen y pegan en su pared.

### Pilar 5: Biblioteca de Recursos Descargables

**Qué es**: PDFs imprimibles por semana y por tema.

**Formatos**:
- Calendario de lectura semanal (marcador)
- Guía de estudio semanal (1 hoja, frente y dorso)
- Hoja de actividades para la familia
- Tabla de personajes de la semana
- Mapa para colorear (niños)
- Journal/diario de estudio

**Alcance**: 52 semanas × 4-5 recursos = 200+ PDFs

**Cómo escala**: Templates HTML + CSS → PDF generado por lote con scripts. Contenido
extraído de los datos estructurados de los otros pilares.

**Valor único**: Los printables en español son casi inexistentes. Solo Latter Day Kids
tiene para niños pequeños. No hay nada para adultos, jóvenes o maestros.

**Modelo**: Detrás de registro (incentivo para crear cuenta).

### Pilar 6: Contexto Histórico y Cultural

**Qué es**: Explicaciones breves del trasfondo histórico, cultural y arqueológico del AT.

**Subcategorías**:
- **Contexto histórico**: Reinos, imperios, fechas clave
- **Cultura del Cercano Oriente**: Costumbres, leyes, vida cotidiana
- **Arqueología**: Hallazgos que corroboran el registro bíblico
- **Idiomas**: Estudios básicos de palabras hebreas relevantes

**Alcance**: 52 fichas semanales + 30+ artículos de contexto

**Cómo escala**: Contenido investigado vía Alejandría + skills. Las fichas semanales se
generan en batch. Los artículos profundos son contenido editorial.

**Valor único**: En español no existe nada similar como contenido sistematizado y alineado
con la perspectiva de la Restauración.

## Arquitectura del Sitio

```
VERDADES ETERNAS (verdadeseternas.org)
│
├── /apoyo/                      ← Pilar 3+6 (guías semanales + contexto)
│   ├── /semana-01-introduccion/
│   ├── /semana-02-moises-1-abraham-3/
│   └── … (52 semanas)
│
├── /personajes/                 ← Pilar 1 (CPT nuevo: bc_bible_character)
│   ├── /abraham/
│   ├── /moises/
│   ├── /david/
│   ├── /ester/
│   └── … (100+)
│
├── /ubicaciones/                ← Pilar 2 (CPT bc_location existente)
│   ├── /eden/
│   ├── /sinai/
│   ├── /jerico/
│   ├── /babilonia/
│   └── … (300+)
│
├── /conexiones/                 ← Pilar 3 (artículos temáticos)
│   ├── /tipos-de-cristo-en-el-at/
│   ├── /isaías-en-el-libro-de-mormon/
│   └── … (30+)
│
├── /herramientas/               ← Pilar 4
│   ├── /cronologia-del-at/
│   ├── /genealogia-abraham/
│   ├── /tabla-reyes-israel/
│   └── … (20+)
│
├── /recursos/                   ← Pilar 5 (descargables)
│   ├── /calendario-lectura-2026.pdf
│   ├── /semana-08-guia.pdf
│   └── … (200+ PDFs)
│
└── /mi-cuenta/                  ← Portal de usuario
    ├── progreso
    ├── notas
    ├── favoritos
    └── descargas
```

## Modelo de Página Semanal (`/apoyo/semana-NN/`)

Cada página semanal es un **dashboard** que agrega contenido de todos los pilares:

```
┌─────────────────────────────────────────────┐
│  Semana 8 · 16–22 febrero 2026              │
│  "Serás más fiel de la rectitud"            │
│  Génesis 12–17; Abraham 1–2                │
├─────────────────────────────────────────────┤
│                                             │
│  📍 Ubicaciones de la semana               │
│  [Mapa interactivo: Ur, Harán, Siquem,     │
│   Bet-el, Hebrón]  → /ubicaciones/         │
│                                             │
│  👤 Personaje destacado: Abraham           │
│  [Resumen + enlace a biografía completa]    │
│  → /personajes/abraham                     │
│                                             │
│  🔗 Conexiones con la Restauración         │
│  • Abraham 1–2 (PGP)                       │
│  • 1 Nefi 17:40 (Dios guía a Abraham)      │
│  • DyC 132 (naturaleza del convenio)        │
│                                             │
│  📜 Contexto histórico                      │
│  Ur de los caldeos, rutas comerciales,     │
│  costumbres del pacto en el Cercano Oriente │
│                                             │
│  📥 Recursos descargables                  │
│  [Guía semanal PDF] [Mapa para colorear]   │
│  [Marcador de lectura]                     │
│                                             │
│  💬 Discusión                              │
│  [Comentarios de la comunidad]             │
└─────────────────────────────────────────────┘
```

## Hooks de Registro de Usuario

| Funcionalidad | Sin registro | Con registro |
|---|---|---|
| Navegar contenido | ✅ Lectura completa | ✅ Todo |
| Descargar PDFs | ❌ | ✅ |
| Marcar semanas completadas | ❌ | ✅ |
| Guardar personajes/ubicaciones favoritos | ❌ | ✅ |
| Notas de estudio personales por semana | ❌ | ✅ |
| Newsletter semanal personalizado | ❌ | ✅ |
| Comentarios y discusión | Solo lectura | ✅ Participar |
| Estadísticas de estudio (rachas, progreso) | ❌ | ✅ |

## Hooks de Retorno Frecuente

1. **Ciclo semanal de contenido**:
   - Lunes: Se publica la guía de la semana (alineada al calendario CFM)
   - Miércoles: Personaje destacado (artículo profundo)
   - Viernes: Geografía interactiva (mapa + datos)

2. **Newsletter semanal**:
   - "Esta semana en Ven, Sígueme: [título]" con enlace directo
   - Contenido exclusivo para suscriptores

3. **Acumulación de valor**:
   - El usuario construye su biblioteca personal (semanas marcadas, notas, favoritos)
   - Mientras más semanas participa, más valioso se vuelve el sitio para él

4. **Comunidad**:
   - Discusión semanal en comentarios
   - Preguntas destacadas de la comunidad
   - Testimonios y reflexiones compartidas

5. **Calendario perpetuo**:
   - Contenido reutilizable cada 4 años (cuando el ciclo regrese al AT)
   - El usuario puede retomar donde dejó

## Tipos de Usuario y Sus Necesidades

| Tipo | Necesidad principal | Contenido relevante |
|------|---------------------|---------------------|
| **Alumno individual** | Profundizar su estudio personal | Guía semanal, contexto, conexiones |
| **Familia** | Estudiar con hijos | Actividades, mapas para colorear, historias |
| **Maestro Esc. Dominical** | Preparar su clase | Mapa, biografía, contexto, descargables |
| **Maestra Primaria** | Actividades para niños | Printables, mapas, hojas de actividad |
| **Líder joven** | Preparar lección de jóvenes | Conexiones, reflexiones, herramientas visuales |
| **Investigador** | Referencia rápida | Enciclopedia de personajes, ubicaciones, cronologías |
