# Plan de Armonización de los Evangelios

## Contexto y Objetivo

Crear una armonía integral de los evangelios canónicos (Mateo, Marcos, Lucas, Juan) que:
- Use la tabla oficial de "Concordancia entre los Evangelios" de la Iglesia como base estructural
- Integre la Joseph Smith Translation (JST) de forma exhaustiva en los 4 evangelios
- Incluya correlación máxima con AT, Libro de Mormón, DyC y PGP (incluyendo alusiones indirectas)
- Mantenga granularidad completa de perícopas (387 perícopas ya curadas)
- Respete la posición marcana (Marcos como fuente más antigua según 2SH)
- Facilite el estudio y la concordancia inter-canónica

## Decisiones de Diseño Resueltas

| Decisión | Resolución |
|:---------|:-----------|
| **Entregable** | Dos artefactos: (1) Nuevo documento `plan-armonia-evangelios.md`, (2) Actualizar `plan-pericopas-nt-evangelios.md` con referencias cruzadas |
| **Estructura** | Híbrido: encabezado por acontecimiento + sub-filas por perícopa |
| **Orden de secuencia** | Híbrido: Marcos guía lo compartido, resto sin forzar |
| **Alcance JST** | Investigación exhaustiva de las 4 JST (no solo Mateo 24) |
| **Correlación canon** | Máxima (incluye alusiones indirectas) |
| **Fases de implementación** | 8 fases por periodo de vida de Cristo |
| **Catálogo de eventos** | Crear `catalogo-eventos-canonicos.md` en esta implementación |

## Estructura del Documento `plan-armonia-evangelios.md`

### Formato de cada sección

```markdown
## [Número]. [Nombre del Acontecimiento]

**Referencia oficial**: [URL de la tabla de la Iglesia]

| # | Evangelio | Versículos | Título de Perícopa | _evento_canonico | Notas JST | Notas Canon |
|:-:|:---------:|:----------:|:-------------------|:-----------------|:----------|:------------|
| 1 | Mt | 3:13-17 | Juan bautiza a Jesús | bautismo-jesus | — | — |
| 2 | Mc | 1:9-11 | Bautismo de Jesús | bautismo-jesus | — | — |
| 3 | Lc | 3:21-22 | Juan bautiza a Jesús | bautismo-jesus | — | — |
| 4 | Jn | 1:31-34 | El Cordero de Dios | bautismo-jesus | — | 1 Ne 10:7-10; 2 Ne 31:4-21 |
```

### Columnas

- **#**: Número secuencial dentro del acontecimiento
- **Evangelio**: Mt, Mc, Lc, Jn (o JST cuando aplique)
- **Versículos**: Rango de versículos
- **Título de Perícopa**: Nombre de la perícopa (idéntico para el mismo evento en diferentes evangelios)
- **_evento_canonico**: ID del evento canónico maestro
- **Notas JST**: Divergencias JST relevantes (con referencia a JST y versículos)
- **Notas Canon**: Correlaciones con AT/BdM/DyC/PGP usando qualifiers (`tipo:`, `cita:`, `tematico:`, `paralelo:`, `expansion:`, `comentario:`, `historico:`)

### Convenciones

- **Título idéntico**: Para el mismo evento en diferentes evangelios, el título es idéntico (mecanismo de concordancia)
- **Orden**: Marcos guía el orden de eventos compartidos; material exclusivo de Mateo/Lucas/Juan se inserta en el punto cronológico más plausible
- **JST**: Se documentan solo cambios doctrinales o estructuralmente relevantes (no diferencias menores de traducción)
- **Correlación canon**: Se incluyen alusiones indirectas y paralelos temáticos amplios, no solo citas textuales

## Fases de Implementación

### Fase 1: Genealogías y Nacimiento (Mt 1-2, Lc 1-2, Jn 1:1-18)

**Acontecimientos**:
1. Genealogías de Jesús (Mt 1:1-17, Lc 3:23-38)
2. Anunciación a María (Lc 1:26-38)
3. Nacimiento de Juan el Bautista (Lc 1:57-80)
4. Nacimiento de Jesús (Mt 2:1-15, Lc 2:6-7)
5. Pastores y ángeles (Lc 2:8-20)
6. Magos de oriente (Mt 2:1-12)
7. Presentación en el templo (Lc 2:21-38)
8. Huida a Egipto (Mt 2:13-15)
9. Matanza de los inocentes (Mt 2:16-18)
10. Regreso a Nazaret (Mt 2:19-23, Lc 2:39-40)

**Correlaciones clave**:
- Mt 1:1-17 ↔ 1 Cr 1-3 (genealogía AT)
- Lc 2:1-7 ↔ Isa 7:14 (profecía del nacimiento virginal)
- Mt 2:15 ↔ Oseas 11:1 ("de Egipto llamé a mi hijo")

**JST**: Investigar divergencias en Mt 1-2 y Lc 1-2 (si existen)

### Fase 2: Ministerio de Juan y Bautismo/Tentación (Mt 3-4, Mc 1, Lc 3-4, Jn 1:19-51)

**Acontecimientos**:
1. Predicación de Juan el Bautista (Mt 3:1-12, Mc 1:1-8, Lc 3:1-18, Jn 1:19-28)
2. Bautismo de Jesús (Mt 3:13-17, Mc 1:9-11, Lc 3:21-22, Jn 1:31-34)
3. Tentación de Jesús (Mt 4:1-11, Mc 1:12-13, Lc 4:1-13)
4. Testimonio de Juan (Jn 1:29-34)
5. Primeros discípulos (Jn 1:35-51)

**Correlaciones clave**:
- Bautismo ↔ 1 Ne 10:7-10; 2 Ne 31:4-21 (doctrina del bautismo en BdM)
- Tentación ↔ Moisés 4:4 (Satanás tienta); 2 Ne 2:27 (albedrío)
- Jn 1:1-18 ↔ Moisés 1; Abraham 3:22-28 (Verbo preexistente)

**JST**: Investigar divergencias en Mt 3-4, Mc 1, Lc 3-4, Jn 1

### Fase 3: Ministerio Temprano en Galilea (Mt 4:12-9:34, Mc 1:14-3:35, Lc 4:14-5:39, Jn 2-4)

**Acontecimientos**:
1. Bodas de Caná (Jn 2:1-11)
2. Primera purificación del templo (Jn 2:13-25)
3. Nicodemo (Jn 3:1-21)
4. Mujer samaritana (Jn 4:1-42)
5. Rechazo en Nazaret (Lc 4:16-30)
6. Llamamiento de los primeros discípulos (Mt 4:18-22, Mc 1:16-20)
7. Sanidad del leproso (Mt 8:1-4, Mc 1:40-45, Lc 5:12-16)
8. Sanidad del paralítico (Mt 9:1-8, Mc 2:1-12, Lc 5:17-26)
9. Llamamiento de Mateo/Leví (Mt 9:9-13, Mc 2:13-17, Lc 5:27-32)
10. Controversia sobre el sábado (Mt 12:1-14, Mc 2:23-3:6, Lc 6:1-11)

**Correlaciones clave**:
- Jn 2:13-25 ↔ Mt 21:12-17; Mc 11:15-19; Lc 19:45-48 (segunda purificación)
- Jn 3:3-8 ↔ Moisés 6:55-63 (nacer de nuevo)
- Mc 2:27-28 ↔ DyC 59:9-10 (sábado)

**JST**: Investigar divergencias en este bloque

### Fase 4: Ministerio Posterior en Galilea - Discursos y Parábolas (Mt 10-18, Mc 4-9, Lc 6-9, Jn 5-10)

**Acontecimientos**:
1. Sermón del Monte (Mt 5-7) / Sermón del Plano (Lc 6:17-49)
2. Parábolas del reino (Mt 13, Mc 4, Lc 8)
3. Alimentación de los 5,000 (Mt 14:13-21, Mc 6:30-44, Lc 9:10-17, Jn 6:1-15)
4. Jesús anda sobre el agua (Mt 14:22-33, Mc 6:45-52, Jn 6:16-21)
5. Pan de vida (Jn 6:22-71)
6. Confesión de Pedro (Mt 16:13-20, Mc 8:27-30, Lc 9:18-21)
7. Transfiguración (Mt 17:1-13, Mc 9:2-13, Lc 9:28-36)
8. Buen pastor (Jn 10:1-21)

**Correlaciones clave**:
- Mt 5-7 ↔ 3 Ne 12-14 (Sermón del Templo en BdM)
- Mt 13:3-9 ↔ DyC 86:1-7 (trigo y cizaña)
- Jn 6:35-58 ↔ 3 Ne 18:1-11 (sacramento)
- Mt 17:1-13 ↔ DyC 63:20-21; 110:11-13 (transfiguración)

**JST**: Investigar divergencias en Mt 10-18, Mc 4-9, Lc 6-9, Jn 5-10

### Fase 5: Ministerio en Judea/Perea camino a Jerusalén (Mt 19-20, Mc 10, Lc 10-19, Jn 7-11)

**Acontecimientos**:
1. Jesús en la fiesta de los tabernáculos (Jn 7-8)
2. Ciego de nacimiento (Jn 9)
3. Resurrección de Lázaro (Jn 11:1-44)
4. Enseñanzas sobre el divorcio (Mt 19:1-12, Mc 10:1-12)
5. Jesús y los niños (Mt 19:13-15, Mc 10:13-16, Lc 18:15-17)
6. Joven rico (Mt 19:16-30, Mc 10:17-31, Lc 18:18-30)
7. Parábolas únicas de Lucas (hijo pródigo, buen samaritano, etc.) (Lc 10-18)
8. Zaqueo (Lc 19:1-10)

**Correlaciones clave**:
- Jn 11:25-26 ↔ 2 Ne 9:6-7; Alma 42:5-16 (resurrección)
- Lc 15:11-32 ↔ Moisés 7:28-41 (compasión divina)
- Mt 19:28 ↔ DyC 29:27-29 (doce tronos)

**JST**: Investigar divergencias en este bloque

### Fase 6: Semana Final y Última Cena (Mt 21-26, Mc 11-14, Lc 19-22, Jn 12-17)

**Acontecimientos**:
1. Entrada triunfal (Mt 21:1-11, Mc 11:1-11, Lc 19:28-44, Jn 12:12-19)
2. Purificación del templo (Mt 21:12-17, Mc 11:15-19, Lc 19:45-48)
3. Controversias en el templo (Mt 21:23-23:39, Mc 11:27-12:44, Lc 20:1-21:4)
4. Discurso del Olivar (Mt 24-25, Mc 13, Lc 21:5-38) **con división JST**
5. Unción en Betania (Mt 26:6-13, Mc 14:3-11, Jn 12:2-8)
6. Última Cena (Mt 26:17-30, Mc 14:12-26, Lc 22:7-20, Jn 13-17)
7. Lavamiento de pies (Jn 13:1-20)
8. Discurso del Aposento Alto (Jn 14-17)

**Correlaciones clave**:
- Mt 24 ↔ JS-M 1 (división temporal siglo I vs. últimos días)
- Mc 13 ↔ JS-M 1 (alineación con Mt 24)
- Lc 21:5-38 ↔ JS-M 1 (alineación con Mt 24)
- Mt 26:26-29 ↔ 3 Ne 18:1-11 (sacramento instituido)
- Jn 14-17 ↔ Moroni 7:25-48 (caridad)

**JST**: JS-M 1 ya integrado; investigar otras divergencias en Mt 21-26, Mc 11-14, Lc 19-22, Jn 12-17

### Fase 7: Pasión y Crucifixión (Mt 26:36-27:66, Mc 14:32-15:47, Lc 22:39-23:56, Jn 18-19)

**Acontecimientos**:
1. Getsemaní (Mt 26:36-46, Mc 14:32-42, Lc 22:39-46, Jn 18:1)
2. Arresto de Jesús (Mt 26:47-56, Mc 14:43-52, Lc 22:47-53, Jn 18:2-14)
3. Juicio ante Anás/Caifás (Mt 26:57-68, Mc 14:53-65, Lc 22:54-71, Jn 18:13-27)
4. Negación de Pedro (Mt 26:69-75, Mc 14:66-72, Lc 22:54-62, Jn 18:15-18, 25-27)
5. Jesús ante Pilato (Mt 27:1-26, Mc 15:1-15, Lc 23:1-25, Jn 18:28-19:16)
6. Jesús ante Herodes (Lc 23:6-12)
7. Crucifixión (Mt 27:27-56, Mc 15:16-41, Lc 23:26-49, Jn 19:17-37)
8. Muerte de Jesús (Mt 27:45-56, Mc 15:33-41, Lc 23:44-49, Jn 19:28-37)
9. Sepultura (Mt 27:57-66, Mc 15:42-47, Lc 23:50-56, Jn 19:38-42)

**Correlaciones clave**:
- Mt 26:26-29; Mc 14:22-25; Lc 22:15-20 ↔ 3 Ne 18:1-11 (sacramento)
- Crucifixión ↔ Hel 14:20-27; 3 Ne 8:5-22; 10:9 (señales de la muerte de Cristo)
- Getsemaní ↔ 2 Ne 9:21-22; Mosíah 3:5-12; DyC 19:1-24 (expiación)

**JST**: Investigar divergencias en la narrativa de pasión

### Fase 8: Resurrección y Apariciones (Mt 28, Mc 16, Lc 24, Jn 20-21)

**Acontecimientos**:
1. Resurrección (Mt 28:1-10, Mc 16:1-8, Lc 24:1-12, Jn 20:1-10)
2. Aparición a María Magdalena (Mc 16:9-11, Jn 20:11-18)
3. Camino a Emaús (Lc 24:13-35)
4. Aparición a los discípulos (Mc 16:14, Lc 24:36-49, Jn 20:19-23)
5. Tomás incrédulo (Jn 20:24-29)
6. Aparición junto al lago (Jn 21:1-14)
7. Restauración de Pedro (Jn 21:15-19)
8. Gran Comisión (Mt 28:16-20, Mc 16:15-18)
9. Ascensión (Mc 16:19-20, Lc 24:50-53)

**Correlaciones clave**:
- Resurrección ↔ Alma 11:43-45; 3 Ne 11; DyC 129-130
- Gran Comisión ↔ DyC 18; 84:62-63
- Ascensión ↔ DyC 88:1-13

**JST**: Investigar divergencias en las narrativas de resurrección

## Investigación JST Exhaustiva

Para cada fase, se debe investigar:

1. **Fuentes primarias**:
   - JST Appendix en churchofjesuschrist.org
   - Robert J. Matthews, "A Plainer Translation": Joseph Smith's Translation of the Bible
   - BYU Studies: Joseph Smith Translation resources
   - Manuales SUD (Come, Follow Me)

2. **Criterios de inclusión**:
   - Solo cambios doctrinales o estructuralmente relevantes
   - No diferencias menores de traducción o estilo
   - Documentar con referencia exacta (JST libro:versículos)

3. **Formato de notas JST**:
   ```
   JST: [breve descripción del cambio] (JST [libro] [versículos])
   ```

## Creación de `catalogo-eventos-canonicos.md`

Estructura del catálogo:

```markdown
# Catálogo de Eventos Canónicos

## [ID del evento]

**Título**: [Nombre legible del evento]
**Categoría**: [milagro, enseñanza, profecía, acontecimiento, parábola, etc.]
**Perícopas asignadas**:
- `mateo-3-juan-bautiza-a-jesus` (Mt 3:13-17)
- `marcos-1-bautismo-de-jesus` (Mc 1:9-11)
- `lucas-3-juan-bautiza-a-jesus` (Lc 3:21-22)
- `juan-1-el-cordero-de-dios` (Jn 1:31-34)

**Referencias principales**: Mt 3:13-17; Mc 1:9-11; Lc 3:21-22; Jn 1:31-34
**Referencias secundarias**: 1 Ne 10:7-10; 2 Ne 31:4-21
**Notas**: [Descripción breve del evento y su importancia]
```

Se poblara incrementalmente con cada fase de la armonía.

## Actualización de `plan-pericopas-nt-evangelios.md`

Para cada perícopa existente, añadir:

1. **Columna `_relacion_paralela`** (si no existe ya):
   - `paralelo:` para relatos paralelos
   - `expansion:` para expansiones revelatorias
   - `cita:` para citas textuales
   - `tematico:` para paralelos temáticos
   - `tipo:` para tipología

2. **Notas JST** (si aplica):
   - Referencia a divergencia JST relevante

3. **Referencia al documento de armonía**:
   - Enlace a la sección correspondiente en `plan-armonia-evangelios.md`

## Validación

Cada fase debe validarse:

1. **Cobertura completa**: Todos los versículos de los 4 evangelios están cubiertos
2. **Disjunción**: Ningún versículo pertenece a dos perícopas
3. **Concordancia**: Eventos compartidos tienen `_evento_canonico` idéntico
4. **JST**: Todas las divergencias JST relevantes están documentadas
5. **Correlación**: Todas las correlaciones AT/BdM/DyC/PGP están documentadas con qualifiers correctos
6. **Orden**: La secuencia sigue el principio híbrido (Marcos guía lo compartido)

## Riesgos y Mitigaciones

| Riesgo | Mitigación |
|:-------|:-----------|
| Límite de tokens al generar el documento | Dividir en 8 fases secuenciales, cada una como archivo temporal que se concatena al final |
| Divergencias JST no documentadas en fuentes estándar | Usar solo fuentes SUD autorizadas; no especular |
| Correlaciones débiles o especulativas | Incluir solo correlaciones con evidencia documentada en fuentes SUD o académicas |
| Inconsistencia entre documentos | Validar que `_evento_canonico` sea consistente entre `plan-armonia-evangelios.md`, `plan-pericopas-nt-evangelios.md`, y `catalogo-eventos-canonicos.md` |

## Entregables Finales

1. `docs/juego-del-cinco/plan-armonia-evangelios.md` - Documento de armonía completo
2. `docs/juego-del-cinco/catalogo-eventos-canonicos.md` - Catálogo maestro de eventos canónicos
3. `docs/juego-del-cinco/plan-pericopas-nt-evangelios.md` - Actualizado con referencias cruzadas

## Orden de Implementación

1. Crear estructura de `plan-armonia-evangelios.md` con convenciones y leyendas
2. Implementar Fase 1 (Genealogías y Nacimiento)
3. Crear entradas iniciales en `catalogo-eventos-canonicos.md`
4. Implementar Fases 2-8 secuencialmente
5. Actualizar `plan-pericopas-nt-evangelios.md` con referencias cruzadas
6. Validación final de consistencia entre los tres documentos
