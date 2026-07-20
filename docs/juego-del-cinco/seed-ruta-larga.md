# Seed de la ruta larga

## Estado actual del material disponible

| Nivel | Taxonomía / Tabla | Estado | Filas | ¿Tenemos data completa? |
|:------|:------------------|:------:|:-----:|:------------------------|
| Volumen | `bc_volumen` | No creada | 5 | ✅ Sí (definido en `el-juego-del-cinco.md`) |
| División | `bc_division` | No creada | ~21 | ✅ Sí (definido en `el-juego-del-cinco.md`) |
| Libro | `bc_libro` | No creada | 88 | ✅ Sí (definido en `seed-bc-chapter.php`) |
| Parte | `bc_parte` | No creada | ? | ❌ **No definido** |
| Capítulo | `bc_chapter` | Creada, poblada | ~1674 | ✅ Sí (seed existente `seed-bc-chapter.php`) |
| Perícopa | `bc_pericopa` | No creada | ? | ❌ **No definido** |
| Versículo | `wp_bc_versiculos` | No creada | ~42.000 | ❌ **No importado** |
| Closure | `wp_bc_scripture_closure` | No creada | ~N | ❌ Depende de los demás |

---

## 1. Volúmenes y divisiones (data completa)

Definidos en `el-juego-del-cinco.md` §4. Basta con extraerlos de ahí.

### Volúmenes
| # | Nombre |
|:--|:-------|
| 1 | Antiguo Testamento |
| 2 | Nuevo Testamento |
| 3 | Libro de Mormón |
| 4 | Doctrina y Convenios |
| 5 | Perla de Gran Precio |

### Divisiones
| Volumen | División |
|:--------|:---------|
| AT | La Ley, Históricos, Poéticos, Profetas mayores, Profetas menores |
| NT | Evangelios, Históricos, Epístolas paulinas, Epístolas generales, Apocalípticos |
| LdM | Planchas mayores, Puente editorial, Planchas menores, Escritos de Mormón, Apéndices de Moroni |
| DyC | Revelaciones, Declaraciones Oficiales |
| PGP | Relacionados con AT, Relacionados con NT, Relacionados con la Restauración |

---

## 2. Libros — clasificación completa (88 libros)

### Antiguo Testamento (39)

| # | División | Libro | Capítulos |
|:--|:---------|:------|:---------:|
| 1 | La Ley | Génesis | 50 |
| 2 | La Ley | Éxodo | 40 |
| 3 | La Ley | Levítico | 27 |
| 4 | La Ley | Números | 36 |
| 5 | La Ley | Deuteronomio | 34 |
| 6 | Históricos | Josué | 24 |
| 7 | Históricos | Jueces | 21 |
| 8 | Históricos | Rut | 4 |
| 9 | Históricos | 1 Samuel | 31 |
| 10 | Históricos | 2 Samuel | 24 |
| 11 | Históricos | 1 Reyes | 22 |
| 12 | Históricos | 2 Reyes | 25 |
| 13 | Históricos | 1 Crónicas | 29 |
| 14 | Históricos | 2 Crónicas | 36 |
| 15 | Históricos | Esdras | 10 |
| 16 | Históricos | Nehemías | 13 |
| 17 | Históricos | Ester | 10 |
| 18 | Poéticos | Job | 42 |
| 19 | Poéticos | Salmos | 150 |
| 20 | Poéticos | Proverbios | 31 |
| 21 | Poéticos | Eclesiastés | 12 |
| 22 | Poéticos | Cantar de los Cantares | 8 |
| 23 | Profetas mayores | Isaías | 66 |
| 24 | Profetas mayores | Jeremías | 52 |
| 25 | Profetas mayores | Lamentaciones | 5 |
| 26 | Profetas mayores | Ezequiel | 48 |
| 27 | Profetas mayores | Daniel | 12 |
| 28 | Profetas menores | Oseas | 14 |
| 29 | Profetas menores | Joel | 3 |
| 30 | Profetas menores | Amós | 9 |
| 31 | Profetas menores | Abdías | 1 |
| 32 | Profetas menores | Jonás | 4 |
| 33 | Profetas menores | Miqueas | 7 |
| 34 | Profetas menores | Nahum | 3 |
| 35 | Profetas menores | Habacuc | 3 |
| 36 | Profetas menores | Sofonías | 3 |
| 37 | Profetas menores | Hageo | 2 |
| 38 | Profetas menores | Zacarías | 14 |
| 39 | Profetas menores | Malaquías | 4 |

### Nuevo Testamento (27)

| # | División | Libro | Capítulos |
|:--|:---------|:------|:---------:|
| 40 | Evangelios | Mateo | 28 |
| 41 | Evangelios | Marcos | 16 |
| 42 | Evangelios | Lucas | 24 |
| 43 | Evangelios | Juan | 21 |
| 44 | Históricos | Hechos | 28 |
| 45 | Epístolas paulinas | Romanos | 16 |
| 46 | Epístolas paulinas | 1 Corintios | 16 |
| 47 | Epístolas paulinas | 2 Corintios | 13 |
| 48 | Epístolas paulinas | Gálatas | 6 |
| 49 | Epístolas paulinas | Efesios | 6 |
| 50 | Epístolas paulinas | Filipenses | 4 |
| 51 | Epístolas paulinas | Colosenses | 4 |
| 52 | Epístolas paulinas | 1 Tesalonicenses | 5 |
| 53 | Epístolas paulinas | 2 Tesalonicenses | 3 |
| 54 | Epístolas paulinas | 1 Timoteo | 6 |
| 55 | Epístolas paulinas | 2 Timoteo | 4 |
| 56 | Epístolas paulinas | Tito | 3 |
| 57 | Epístolas paulinas | Filemón | 1 |
| 58 | Epístolas paulinas | Hebreos | 13 |
| 59 | Epístolas generales | Santiago | 5 |
| 60 | Epístolas generales | 1 Pedro | 5 |
| 61 | Epístolas generales | 2 Pedro | 3 |
| 62 | Epístolas generales | 1 Juan | 5 |
| 63 | Epístolas generales | 2 Juan | 1 |
| 64 | Epístolas generales | 3 Juan | 1 |
| 65 | Epístolas generales | Judas | 1 |
| 66 | Apocalípticos | Apocalipsis | 22 |

### Libro de Mormón (15)

| # | División | Libro | Capítulos |
|:--|:---------|:------|:---------:|
| 67 | Planchas mayores | 1 Nefi | 22 |
| 68 | Planchas mayores | 2 Nefi | 33 |
| 69 | Planchas mayores | Jacob | 7 |
| 70 | Planchas mayores | Enós | 1 |
| 71 | Planchas mayores | Jarom | 1 |
| 72 | Planchas mayores | Omni | 1 |
| 73 | Puente editorial | Palabras de Mormón | 1 |
| 74 | Planchas menores | Mosíah | 29 |
| 75 | Planchas menores | Alma | 63 |
| 76 | Planchas menores | Helamán | 16 |
| 77 | Planchas menores | 3 Nefi | 30 |
| 78 | Planchas menores | 4 Nefi | 1 |
| 79 | Escritos de Mormón | Mormón | 9 |
| 80 | Apéndices de Moroni | Éter | 15 |
| 81 | Apéndices de Moroni | Moroni | 10 |

### Doctrina y Convenios (2)

| # | División | Libro | Capítulos |
|:--|:---------|:------|:---------:|
| 82 | Revelaciones | Secciones | 138 |
| 83 | Declaraciones Oficiales | Declaraciones Oficiales | 2 |

### Perla de Gran Precio (5)

| # | División | Libro | Capítulos |
|:--|:---------|:------|:---------:|
| 84 | Relacionados con el AT | Moisés | 8 |
| 85 | Relacionados con el AT | Abraham | 5 |
| 86 | Relacionados con el NT | José Smith—Mateo | 1 |
| 87 | Relacionados con la Restauración | José Smith—Historia | 1 |
| 88 | Relacionados con la Restauración | Artículos de Fe | 1 |

### Notas
- **Total**: 39 + 27 + 15 + 2 + 5 = **88 libros**.
- **Cantares** (seed script) corresponde a **Cantar de los Cantares** (nombre completo).
- **Apéndice de la Traducción de José Smith** (en seed script) no se cuenta como libro en el juego del cinco (PGP tiene 5 libros, no 6).
- El libro **Secciones** contiene 138 secciones; el libro **Declaraciones Oficiales** contiene 2 declaraciones.
- El Knowledge Graph de Alejandría confirma: 88 books, 19 divisions, 5 volumes.

---

## 3. Capítulos (data completa)

1674 capítulos ya poblados en `bc_chapter` con estructura libro→capítulo.
La closure table los integrará sin necesidad de migrar los términos.

---

## 4. Partes — **PROBLEMA ABIERTO**

No existe aún una definición de cómo se dividen los libros en partes.
Cada parte es una sección temática dentro de un libro, con un rango de
capítulos. Ejemplo del evangelio de Mateo:

| Parte | Capítulos |
|:------|:----------|
| La preparación de Jesucristo | 1–4 |
| El Sermón del Monte | 5–7 |
| Ministerio en Galilea | 8–18 |
| Viaje a Jerusalén | 19–20 |
| La semana final | 21–27 |
| La Resurrección | 28 |

### Preguntas por resolver

1. ¿Quién define las partes? ¿Fuente externa (BibleHub, RV60) o criterio propio?
2. ¿Son todas necesarias para el seed inicial o podemos comenzar solo con
   volumen → división → libro → capítulo?
3. ¿Las partes se definen por libro completo o solo para algunos volúmenes?

### Prioridad sugerida

| Orden | Volumen | Libros | Dificultad |
|:------|:--------|:------:|:-----------|
| 1 | AT | 39 | Alta (libros extensos, muchas secciones) |
| 2 | NT | 27 | Media (evangelios tienen estructura clara) |
| 3 | LdM | 15 | Baja (libros más cortos, estructura narrativa) |
| 4 | PGP | 5 | Muy baja (libros breves) |
| 5 | DyC | 1 | La más baja (secciones numeradas, no requiere partes) |

---

## 5. Perícopas — **PROBLEMA ABIERTO**

Cada capítulo puede tener una o más perícopas (unidades narrativas con nombre).
Ejemplo de Génesis 19:

| Perícopa | Versículos |
|:---------|:----------|
| Los dos ángeles visitan a Lot | 1–3 |
| Los hombres de Sodoma | 4–11 |
| Lot huye de Sodoma | 12–23 |
| La destrucción de Sodoma y Gomorra | 24–29 |
| Lot y sus hijas en Zoar | 30–38 |

### Estimación

- Capítulos con una sola perícopa (epístolas, DyC): ~2000 perícopas
- Capítulos con múltiples perícopas (narrativa): añade ~500
- **Total estimado**: ~2500 perícopas

### Dificultad

Las perícopas son el nivel más granular de la clasificación. Definir ~2500
perícopas con nombres significativos es una tarea de curaduría que puede
tomar semanas si se hace manualmente.

### Alternativas viables

1. **Comenzar con una perícopa por capítulo** (mismo nombre que el capítulo)
   como placeholder y refinarlas después.
2. **Solo para volúmenes clave** (AT, NT, LdM) y dejar el resto como capítulo
   plano.
3. **Extraer de una fuente externa** (BibleHub, etc.) si existe el dato.

---

## 6. Versículos — **IMPORTACIÓN DESDE ALEJANDRÍA**

El contenido textual de los ~42.000 versículos está disponible en Alejandría
con estructura de archivos por capítulo. Rutas confirmadas:

| Volumen | Ruta ES | Ruta EN |
|:--------|:--------|:--------|
| AT | `es/scriptures/ot/{libro}/{cap}.txt` | `en/scriptures/ot/{book}/{ch}.txt` |
| NT | `es/scriptures/nt/{libro}/{cap}.txt` | `en/scriptures/nt/{book}/{ch}.txt` |
| LdM | `es/scriptures/bom/{libro}/{cap}.txt` | `en/scriptures/bom/{book}/{ch}.txt` |
| DyC | `es/scriptures/dc/secciones/{n}.txt` | `en/scriptures/dc/sections/{n}.txt` |
| PGP | `es/scriptures/pgp/{libro}/{cap}.txt` | `en/scriptures/pgp/{book}/{ch}.txt` |

Cada archivo contiene los versículos con su número. Ejemplo:
`es/scriptures/ot/genesis/1.txt` → "1 En el principio creó Dios los cielos..."

### Opciones de importación

| Fuente | Formato | Confiabilidad | Licencia |
|:-------|:--------|:-------------|:---------|
| Alejandría (FTS/local) | Archivos TXT por capítulo | ✅ Corpus propio, RV60 SUD | ✅ Sin restricción |
| BibleHub / API pública | JSON | ⚠️ Puede diferir de RV60 SUD | ⚠️ Verificar términos |

La importación requiere:
- Mapear cada capítulo (`bc_chapter`) a su directorio en Alejandría
- Parsear cada archivo: extraer pares `(número, texto)`
- Insertar en `wp_bc_versiculos`: `chapter_id`, `numero`, `contenido`
- Indexar FULLTEXT sobre `contenido`

---

## 7. Orden de seed sugerido

```
Fase 1 (independiente)
├── bc_volumen       → 5 terms
├── bc_division      → ~21 terms
├── bc_libro         → 88 terms
├── bc_chapter       → ya existe (1674 terms)
└── bc_pericopa      → ~2500 terms (depende de decisión, ver §5)

Fase 2 (depende de Fase 1)
└── wp_bc_scripture_closure  → relaciones entre todos los niveles

Fase 3 (independiente, puede ir en paralelo)
└── wp_bc_versiculos         → ~42.000 filas (importación externa)
```

---

## 8. Acciones inmediatas

- [ ] Decidir fuente y criterio para **partes** de los libros
- [ ] Decidir estrategia para **perícopas** (placeholder vs. curaduría completa)
- [x] Confirmar clasificación de **88 libros** con división y volumen (completado en §2)
- [x] Confirmar fuente de versículos: **Alejandría** (archivos TXT por capítulo, ES y EN)
- [ ] Confirmar orden: ¿seedear parcialmente (vol–div–lib–cap) o esperar a tener
      todo completo antes de empezar?
