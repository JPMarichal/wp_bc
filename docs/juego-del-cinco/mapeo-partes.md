# Mapeo de Partes (bc_parte)

## Criterios

| Regla | Descripción |
|:------|:------------|
| **Partes formales** | Solo se crean partes para libros con **divisiones estructurales reconocidas** por la crítica textual clásica. |
| **Rangos cerrados** | Cada parte abarca capítulos completos. No se admiten rangos que empiecen o terminen a mitad de un capítulo. |
| **Sin omisiones** | La unión de todas las partes de un libro cubre la totalidad de sus capítulos sin superposición ni huecos. |
| **Nombres unívocos** | El nombre de cada parte debe ser autoexplicativo dentro del contexto de su libro y no dar lugar a ambigüedad. |
| **Rangos acotados** | Las partes no deben tener rangos excesivamente amplios. Preferir granularidad: libros extensos (ej. Isaías, Jeremías) se dividen en más partes. |
| **Didáctico > polémico** | Los nombres y las divisiones deben priorizar la claridad para el usuario novel. Si una división académica es oscura o controvertida, se reemplaza por una estructura narrativa accesible. |
| **Certezas > especulaciones** | Centrarse en divisiones ampliamente aceptadas. Evitar teorías controvertidas o especulativas (ej. múltiples autores de Isaías o Zacarías). |
| **Nombres clásicos** | Preferir nombres consagrados por el uso cuando existan (ej. "Libro I/II/III" para Salmos, "El Siervo Sufriente" para Isaías 49–55). |
| **Compatibilidad SUD** | Las divisiones deben ser compatibles con la doctrina y práctica de La Iglesia de Jesucristo de los Santos de los Últimos Días. Los límites entre partes deben permitir enfatizar capítulos relevantes para el canon SUD. |
| **Equilibrio atomicidad/granularidad** | La división debe priorizar la **integridad temática** sobre reglas numéricas. Una parte puede abarcar muchos capítulos si forman una unidad temática coherente (ej. "Diálogos con los amigos" en Job abarca 29 caps. porque es un solo ciclo literario). Como guía general, rangos de 1–12 capítulos son lo habitual. |
| **Libros de 1 capítulo** | Se crea una única parte que abarca el capítulo completo. No tienen por qué dividirse. |
| **SEO y slugs** | El nombre debe generar un slug limpio con `sanitize_title()`. El contexto del libro se resuelve en la composición del permalink en otra fase (ruta larga), no dentro del slug mismo. Evitar caracteres especiales y preferir frases de 2–5 palabras. |
| **Español** | Todos los nombres van en español. |

> **Nota**: En las tablas de este documento, el número circulado (①, ②, …)
> es un índice secuencial informativo, y el paréntesis con el rango de
> capítulos (ej. `(1–12)`) es metadata de localización. **Ni el número
> circulado ni el paréntesis forman parte del nombre de la parte**.
> El nombre real es solo el texto entre ambos: p. ej., para
> `① La Creación (1–2)` el nombre es `La Creación`. Esta distinción
> es necesaria para la generación de slugs, la closure table y la
> serialización de `bc_parte`.

### Fuentes consultadas

- **Nelson's Complete Book of Bible Maps and Charts** (Thomas Nelson, 1993/2010) — fuente principal para las divisiones del AT y NT.
- **Nathan Richardson, StoryGuide Scriptures** (nathanrichardson.com) — especialmente la estructura de Hechos basada en Hechos 1:8, y el enfoque general de subdivisiones mayores/menores compatibles con el canon SUD y Come Follow Me.
- **John W. Welch, Charting the New Testament** (FARMS/BYU Studies, 2002) — validación de las 83 partes del NT a nivel de perícopa. Los 14 charts de la Sección 14 (Overviews of the Epistles) fueron descargados y examinados en julio de 2026. Cada chart lista el esquema de perícopas del libro (pasaje por pasaje), y las divisiones del NT de este documento son consistentes con la estructura que reflejan. **No se requirieron refinamientos**: las 83 partes del NT están validadas por los charts.
- **Nathan Richardson, StoryGuide Scriptures Reading Chart: Book of Mormon** (nathanrichardson.com) — fuente principal para las subdivisiones del LdM. Proporciona divisiones por autor/narrador (ej. 2 Ne dividido en palabras de Lehi, Jacob, Isaías y Nefi). Su esquema de color identifica citas largas (verde claro) y flashbacks (amarillo claro). Las subdivisiones reflejan los cortes de capítulos originales de la edición de 1830, que revelan la estructura planificada por los autores nefitas.
- **John W. Welch & Greg Welch, Charting the Book of Mormon** (FARMS/BYU Studies, 1999) — validación cruzada de las 49 partes del LdM. La Sección 2 (Structure) contiene 13 charts sobre planchas, fuentes, autores y extensión de libros. El **Chart 170** compara las divisiones de capítulos de la edición de 1830 con la de 1981, confirmando que los cortes originales (115 caps.) revelan unidades estructurales más amplias que las modernas (240 caps.). Disponible en byustudies.byu.edu con 177 charts en 15 secciones.
- **J. Max Wilson, Book of Mormon Outline** (sixteensmallstones.org) — verificación secundaria basada en los cortes de 1830, cambios de autor/narrador y encabezados del texto. El PDF contiene un desglose detallado de la estructura textual del LdM.
- **BYU Studies article "1 and 2 Nephi: An Inspiring Whole"** (byustudies.byu.edu) — confirma la estructura bipartita del registro de Nefi: sección histórica (1 Ne 1–2 Ne 5) y sección espiritual (2 Ne 6–33).
- **Gary L. Sturgess, "The Book of Mosiah: Thoughts about Its Structure"** (JBMS) — estructura triádica del libro de Mosíah basada en tres ceremonias reales: consagración de Benjamín (1–6), reunificación (25), y desestablecimiento de la monarquía (28–29).
- **Joseph M. Spencer, "The Structure of the Book of Alma"** (JBMS) — correspondencia quiásmica entre Nehor (Alma 1) y Korihor (Alma 30), usada como referencia para las divisiones internas de Alma.
- **Stephen O. Smoot et al., "The Book of Abraham Introduction"** (RSC/BYU, 2022) — desglose del libro de Abraham en 10 secciones narrativas dentro de sus 5 capítulos. Las secciones se agrupan naturalmente en tres unidades: Abraham en Ur y el convenio (caps. 1–2), la visión del cosmos y la preexistencia (cap. 3), y la Creación (caps. 4–5). Disponible en rsc.byu.edu.
- **Aaron P. Schade & Matthew L. Bowen, "The Book of Moses: From the Ancient of Days to the Latter Days"** (RSC/BYU, 2021) — confirmación de la estructura del libro de Moisés en cuatro secciones: visión de Moisés (cap. 1), Creación (caps. 2–3), Caída (cap. 4), patriarcas antediluvianos (caps. 5–8). Disponible en rsc.byu.edu.
- **Capítulos de la Perla de Gran Precio** (churchofjesuschrist.org) — José Smith—Mateo (Mat. 24, 57 versículos), José Smith—Historia (75 versículos cubriendo Primera Visión, visita de Moroni, planchas y restauración del Sacerdocio Aarónico), y Artículos de Fe (13 declaraciones doctrinales). Todos son capítulos únicos sin subdivisiones estructurales reconocidas, por lo que reciben una parte cada uno bajo la regla de «1 capítulo = 1 parte».

### Sobre los nodos "part" de Alejandría

El Knowledge Graph de Alejandría contiene 386 nodos de tipo `part`, pero **no
son utilizables como partes formales** de la ruta larga por estas razones:

1. **No son exhaustivos**: no cubren la totalidad de capítulos de ningún libro.
2. **No son mutuamente excluyentes**: un mismo pasaje puede corresponder a
   varios nodos "part".
3. **No son estructurales**: son etiquetas temáticas/narrativas (p. ej. "Nephi
   Quotes the Prophecies of Isaiah"), no divisiones formales del libro.
4. **No respetan límites de capítulos**: muchas partes cruzan fronteras de
   capítulos arbitrariamente.
5. **Están en inglés**: violan el criterio de español unívoco.

Corresponden mejor al nivel de **perícopa** (pendiente de definir).

---

## Mapeo completo por libro

### Antiguo Testamento (39 libros)

| # | Libro | Caps. | ¿Tiene partes? | Partes |
|:-:|:------|:-----:|:--------------|:-------|
| 1 | **Génesis** | 50 | **Sí (8)** | ① La Creación (1–2) · ② La Caída (3–5) · ③ El Diluvio (6–9) · ④ La Dispersión de las Naciones (10–11) · ⑤ La historia de Abraham (12–25) · ⑥ La historia de Isaac (26) · ⑦ La historia de Jacob (27–36) · ⑧ La historia de José (37–50) |
| 2 | **Éxodo** | 40 | **Sí (3)** | ① Liberación de Egipto (1–18) · ② El pacto del Sinaí (19–24) · ③ El tabernáculo (25–40) |
| 3 | **Levítico** | 27 | **Sí (8)** | ① Consagración de las ofrendas (1–7) · ② Consagración del sacerdocio (8–10) · ③ Purificación nacional (11–15) · ④ El día de la Expiación (16–17) · ⑤ Santificación del pueblo (18–20) · ⑥ Santificación de los sacerdotes (21–22) · ⑦ Santificación en la tierra (23–25) · ⑧ Santificación mediante votos (26–27) |
| 4 | **Números** | 36 | **Sí (9)** | ① Organización de Israel (1–4) · ② Santificación de Israel (5–10) · ③ Hacia Cades (10–12) · ④ En Cades (13–14) · ⑤ En el desierto hacia Moab (15–19) · ⑥ Reorganización de Israel (20–25) · ⑦ Segundo censo (26–27) · ⑧ Ofrendas y votos (28–30) · ⑨ Conquista y división (31–36) |
| 5 | **Deuteronomio** | 34 | **Sí (3)** | ① Primer discurso de Moisés (1–4) · ② Segundo discurso de Moisés (5–26) · ③ Tercer discurso de Moisés (27–34) |
| 6 | **Josué** | 24 | **Sí (2)** | ① La conquista de Canaán (1–12) · ② La división de la tierra (13–24) |
| 7 | **Jueces** | 21 | **Sí (7)** | ① La conquista incompleta (1–2) · ② Otoniel, Aod y Débora (3–5) · ③ Gedeón y Abimelec (6–9) · ④ Jefté y los jueces menores (10–12) · ⑤ Sansón (13–16) · ⑥ Los ídolos de Micaía (17–18) · ⑦ La guerra civil (19–21) |
| 8 | **Rut** | 4 | **Sí (4)** | ① La decisión de Rut (1) · ② La devoción de Rut (2) · ③ La petición de Rut (3) · ④ La recompensa de Rut (4) |
| 9 | **1 Samuel** | 31 | **Sí (3)** | ① Samuel y el fin de los jueces (1–7) · ② Saúl: el primer rey de Israel (8–15) · ③ David y el declive de Saúl (16–31) |
| 10 | **2 Samuel** | 24 | **Sí (2)** | ① El reinado triunfante de David (1–10) · ② La caída y restauración de David (11–24) |
| 11 | **1 Reyes** | 22 | **Sí (6)** | ① Establecimiento de Salomón (1–2) · ② Auge de Salomón (3–8) · ③ Decadencia de Salomón (9–11) · ④ División del reino (12–14) · ⑤ Asa y los reyes de Israel (15–16) · ⑥ Elías y Acab (17–22) |
| 12 | **2 Reyes** | 25 | **Sí (3)** | ① Los ministerios de Elías y Eliseo (1–8) · ② La caída del reino del norte (9–17) · ③ La caída del reino del sur (18–25) |
| 13 | **1 Crónicas** | 29 | **Sí (2)** | ① Las genealogías de Israel (1–9) · ② El reinado de David (10–29) |
| 14 | **2 Crónicas** | 36 | **Sí (6)** | ① Inauguración de Salomón (1) · ② Construcción del templo (2–7) · ③ Esplendor del reinado de Salomón (8–9) · ④ División del reino de Israel (10–13) · ⑤ Reformas de Asa a Josías (14–35) · ⑥ Caída de Judá (36) |
| 15 | **Esdras** | 10 | **Sí (2)** | ① La reconstrucción del templo bajo Zorobabel (1–6) · ② El regreso bajo Esdras (7–10) |
| 16 | **Nehemías** | 13 | **Sí (3)** | ① La reconstrucción del muro (1–7) · ② La renovación del pacto (8–10) · ③ La dedicación y las reformas (11–13) |
| 17 | **Ester** | 10 | **Sí (4)** | ① Selección de Ester como reina (1–2) · ② Maquinación del complot de Amán (3–4) · ③ Triunfo de Mardoqueo sobre Amán (5–7) · ④ Triunfo de Israel sobre sus enemigos (8–10) |
| 18 | **Job** | 42 | **Sí (5)** | ① Prólogo: la prueba de Job (1–2) · ② Diálogos con los amigos (3–31) · ③ Discursos de Eliú (32–37) · ④ Dios responde desde la tempestad (38–41) · ⑤ Epílogo: la restauración de Job (42) |
| 19 | **Salmos** | 150 | **Sí (5)** | ① Libro I de Salmos (1–41) · ② Libro II de Salmos (42–72) · ③ Libro III de Salmos (73–89) · ④ Libro IV de Salmos (90–106) · ⑤ Libro V de Salmos (107–150) |
| 20 | **Proverbios** | 31 | **Sí (6)** | ① Proverbios de Salomón: la sabiduría llama (1–9) · ② Proverbios de Salomón: los proverbios (10–22) · ③ Dichos de los sabios (23–24) · ④ Proverbios copiados por Ezequías (25–29) · ⑤ Palabras de Agur (30) · ⑥ Palabras de Lemuel (31) |
| 21 | **Eclesiastés** | 12 | **Sí (2)** | ① La vanidad de la vida bajo el sol (1–6) · ② La sabiduría para vivir bajo el sol (7–12) |
| 22 | **Cantar de los Cantares** | 8 | **Sí (4)** | ① El inicio del amor (1–3) · ② La unidad del amor (4–5) · ③ La lucha por el amor (6–7) · ④ El crecimiento del amor (8) |
| 23 | **Isaías** | 66 | **Sí (9)** | ① El juicio de Jehová contra Judá (1–5) · ② El llamamiento del profeta y el Emanuel (6–12) · ③ Las cargas contra las naciones (13–23) · ④ El apocalipsis de Isaías (24–27) · ⑤ Los ayes y la liberación de Jerusalén (28–35) · ⑥ Ezequías y la amenaza asiria (36–39) · ⑦ El gran Rey redentor (40–48) · ⑧ El Siervo Sufriente y la nueva alianza (49–55) · ⑨ Cielos nuevos y tierra nueva (56–66) |
| 24 | **Jeremías** | 52 | **Sí (7)** | ① La comisión profética de Jeremías (1) · ② La condenación de Judá (2–25) · ③ Los conflictos de Jeremías (26–29) · ④ La restauración futura (30–33) · ⑤ La caída de Jerusalén (34–45) · ⑥ La condenación de las naciones (46–51) · ⑦ La conclusión histórica (52) |
| 25 | **Lamentaciones** | 5 | **Sí (5)** | ① La ciudad en luto (1) · ② El pueblo quebrantado (2) · ③ El profeta afligido (3) · ④ El reino arruinado (4) · ⑤ La nación arrepentida (5) |
| 26 | **Ezequiel** | 48 | **Sí (3)** | ① Juicio contra Jerusalén (1–24) · ② Las naciones: Tiro y Egipto (25–32) · ③ Restauración del templo y de la tierra (33–48) |
| 27 | **Daniel** | 12 | **Sí (2)** | ① Narraciones en la corte de Babilonia (1–6) · ② Visiones apocalípticas (7–12) |
| 28 | **Oseas** | 14 | **Sí (7)** | ① El matrimonio profético de Oseas y Gomer (1) · ② La aplicación del adulterio de Gomer (2) · ③ La restauración de Gomer (3) · ④ El adulterio espiritual de Israel (4–6) · ⑤ La negativa de Israel a arrepentirse (7–8) · ⑥ El juicio de Dios contra Israel (9–10) · ⑦ La restauración de Israel (11–14) |
| 29 | **Joel** | 3 | **Sí (2)** | ① El día del Señor en retrospectiva (1) · ② El día del Señor y el derramamiento del Espíritu (2–3) |
| 30 | **Amós** | 9 | **Sí (3)** | ① Las transgresiones de las naciones (1–2) · ② Juicio contra Israel (3–6) · ③ Visiones de restauración (7–9) |
| 31 | **Abdías** | 1 | **Sí (1)** | ① El juicio contra Edom (1) |
| 32 | **Jonás** | 4 | **Sí (4)** | ① La huida de Jonás (1) · ② La oración de Jonás (2) · ③ La predicación de Jonás (3) · ④ La lección de Jonás (4) |
| 33 | **Miqueas** | 7 | **Sí (3)** | ① Juicio y restauración (1–3) · ② La paz futura (4–5) · ③ La controversia de Jehová con Israel (6–7) |
| 34 | **Nahum** | 3 | **Sí (3)** | ① La destrucción de Nínive decretada (1) · ② La destrucción de Nínive descrita (2) · ③ La destrucción de Nínive merecida (3) |
| 35 | **Habacuc** | 3 | **Sí (2)** | ① Los problemas de Habacuc (1–2) · ② La alabanza de Habacuc (3) |
| 36 | **Sofonías** | 3 | **Sí (3)** | ① El gran día de Jehová contra Judá (1) · ② El juicio de Jehová contra las naciones (2) · ③ El cántico de restauración de Israel (3) |
| 37 | **Hageo** | 2 | **Sí (2)** | ① El llamamiento a reconstruir el templo (1) · ② La gloria futura del templo (2) |

| # | Libro | Caps. | ¿Tiene partes? | Partes |
|:-:|:------|:-----:|:--------------|:-------|
| 38 | **Zacarías** | 14 | **Sí (5)** | ① El llamamiento al arrepentimiento (1) · ② Las ocho visiones nocturnas (2–6) · ③ La restauración de Sión (7–8) · ④ El Rey de Sión y el Pastor rechazado (9–11) · ⑤ El día del Señor y la redención de Jerusalén (12–14) |
| 39 | **Malaquías** | 4 | **Sí (3)** | ① El amor de Dios y la corrupción del sacerdocio (1) · ② La infidelidad del pacto (2) · ③ El día del Señor venidero (3–4) |

### Nuevo Testamento (27 libros)

| # | Libro | Caps. | ¿Tiene partes? | Partes |
|:-:|:------|:-----:|:--------------|:-------|
| 40 | **Mateo** | 28 | **Sí (6)** | ① La presentación del Rey (1–4) · ② La proclamación del Rey (5–7) · ③ El poder del Rey (8–11) · ④ La oposición al Rey (12–16) · ⑤ La preparación de los discípulos (17–20) · ⑥ La crucifixión y resurrección del Rey (21–28) |
| 41 | **Marcos** | 16 | **Sí (4)** | ① Prólogo: preparación del Siervo (1) · ② Ministerio del Siervo en Galilea (2–8) · ③ El Siervo en camino a Jerusalén (9–10) · ④ Pasión y resurrección del Siervo (11–16) |
| 42 | **Lucas** | 24 | **Sí (4)** | ① La venida del Hijo del Hombre (1–3) · ② El ministerio del Hijo del Hombre en Galilea (4–9) · ③ El Hijo del Hombre en camino a Jerusalén (10–19) · ④ La pasión y resurrección del Hijo del Hombre (20–24) |
| 43 | **Juan** | 21 | **Sí (5)** | ① La encarnación del Hijo de Dios (1) · ② Las primeras señales (2–4) · ③ Las señales del Hijo de Dios (5–12) · ④ La despedida a los discípulos (13–17) · ⑤ Pasión, resurrección y epílogo (18–21) |
| 44 | **Hechos** | 28 | **Sí (3)** | ① En Jerusalén (1–7) · ② En Judea y Samaria (8–12) · ③ Hasta lo último de la tierra (13–28) |
| 45 | **Romanos** | 16 | **Sí (3)** | ① La revelación de la justicia de Dios (1–8) · ② La vindicación de la justicia de Dios (9–11) · ③ La aplicación de la justicia de Dios (12–16) |
| 46 | **1 Corintios** | 16 | **Sí (3)** | ① La respuesta al informe de Cloé sobre las divisiones (1–4) · ② La respuesta al informe de fornicación y desorden (5–6) · ③ La respuesta a las preguntas de la carta (7–16) |
| 47 | **2 Corintios** | 13 | **Sí (3)** | ① Defensa del ministerio apostólico (1–7) · ② La ofrenda para los santos (8–9) · ③ Autoridad apostólica y amonestaciones (10–13) |
| 48 | **Gálatas** | 6 | **Sí (3)** | ① El evangelio de la gracia defendido (1–2) · ② El evangelio de la gracia explicado (3–4) · ③ El evangelio de la gracia aplicado (5–6) |
| 49 | **Efesios** | 6 | **Sí (2)** | ① Los designios de Dios en Cristo (1–3) · ② Caminar en Cristo: unidad y santidad (4–6) |
| 50 | **Filipenses** | 4 | **Sí (4)** | ① El gozo de Pablo en el testimonio (1) · ② La humildad de Cristo (2) · ③ La justicia por la fe (3) · ④ El gozo y la paz en el Señor (4) |
| 51 | **Colosenses** | 4 | **Sí (2)** | ① La supremacía de Cristo en la iglesia (1–2) · ② La sujeción a Cristo en la iglesia (3–4) |
| 52 | **1 Tesalonicenses** | 5 | **Sí (2)** | ① Las reflexiones personales de Pablo sobre los tesalonicenses (1–3) · ② Las instrucciones de Pablo a los tesalonicenses (4–5) |
| 53 | **2 Tesalonicenses** | 3 | **Sí (3)** | ① El estímulo en la persecución (1) · ② Los eventos que preceden al día del Señor (2) · ③ La exhortación a la iglesia (3) |
| 54 | **1 Timoteo** | 6 | **Sí (5)** | ① Encargo sobre la doctrina (1) · ② Encargo sobre la adoración pública (2–3) · ③ Encargo sobre los falsos maestros (4) · ④ Encargo sobre la disciplina eclesiástica (5) · ⑤ Encargo sobre los motivos pastorales (6) |
| 55 | **2 Timoteo** | 4 | **Sí (2)** | ① Perseverar en las pruebas presentes (1–2) · ② Resistir en las pruebas futuras (3–4) |
| 56 | **Tito** | 3 | **Sí (2)** | ① Designar ancianos (1) · ② Poner las cosas en orden (2–3) |
| 57 | **Filemón** | 1 | **Sí (1)** | ① La restauración de Onésimo (1) |
| 58 | **Hebreos** | 13 | **Sí (3)** | ① La superioridad de Cristo (1–4) · ② El sacerdocio superior de Cristo (5–10) · ③ La vida de fe (11–13) |
| 59 | **Santiago** | 5 | **Sí (3)** | ① La prueba de la fe (1) · ② Las características de la fe (2–4) · ③ El triunfo de la fe (5) |
| 60 | **1 Pedro** | 5 | **Sí (3)** | ① La salvación y santificación del creyente (1–2) · ② El sufrimiento y la sumisión del creyente (3–4) · ③ El pastoreo y la victoria del creyente (5) |
| 61 | **2 Pedro** | 3 | **Sí (3)** | ① El cultivo del carácter cristiano (1) · ② La condenación de los falsos maestros (2) · ③ La confianza en el retorno de Cristo (3) |
| 62 | **1 Juan** | 5 | **Sí (3)** | ① Las condiciones del compañerismo (1–2) · ② Las características del compañerismo (3–4) · ③ Las consecuencias del compañerismo (5) |
| 63 | **2 Juan** | 1 | **Sí (1)** | ① Andar en la verdad y el amor (1) |
| 64 | **3 Juan** | 1 | **Sí (1)** | ① La fidelidad en la hospitalidad (1) |
| 65 | **Judas** | 1 | **Sí (1)** | ① Contender por la fe (1) |
| 66 | **Apocalipsis** | 22 | **Sí (8)** | ① Las siete iglesias (1–3) · ② Los siete sellos (4–7) · ③ Las siete trompetas (8–11) · ④ El dragón y las bestias (12–14) · ⑤ Las siete copas (15–16) · ⑥ La caída de Babilonia (17–18) · ⑦ El juicio final (19–20) · ⑧ La nueva creación (21–22) |

### Libro de Mormón (15 libros)

| # | Libro | Caps. | ¿Tiene partes? | Partes |
|:-:|:------|:-----:|:--------------|:-------|
| 67 | **1 Nefi** | 22 | **Sí (5)** | ① La salida de Jerusalén (1–2) · ② Las planchas de bronce e Ismael (3–7) · ③ El árbol de la vida y la visión de Nefi (8–15) · ④ El viaje a la tierra prometida (16–18) · ⑤ Las profecías de Isaías y el testimonio de Nefi (19–22) |
| 68 | **2 Nefi** | 33 | **Sí (4)** | ① Las palabras de Lehi (1–5) · ② Las palabras de Jacob (6–10) · ③ Las palabras de Isaías (11–30) · ④ Las palabras de Nefi (31–33) |
| 69 | **Jacob** | 7 | **Sí (3)** | ① La advertencia de Jacob contra la inmoralidad (1–3) · ② La alegoría del olivo de Zenós (4–6) · ③ La confrontación con Sherem (7) |
| 70 | **Enós** | 1 | **Sí (1)** | ① La oración de Enós (1) |
| 71 | **Jarom** | 1 | **Sí (1)** | ① Las palabras de Jarom (1) |
| 72 | **Omni** | 1 | **Sí (1)** | ① La transición de los guardianes de las planchas (1) |
| 73 | **Palabras de Mormón** | 1 | **Sí (1)** | ① Mormón enlaza las planchas menores con su abreviación (1) |
| 74 | **Mosíah** | 29 | **Sí (5)** | ① El discurso del rey Benjamín (1–6) · ② El registro de Zeniff (7–10) · ③ El ministerio de Abinadí (11–17) · ④ El convenio de Alma y la liberación de Limhi (18–24) · ⑤ La reorganización del reino (25–29) |
| 75 | **Alma** | 63 | **Sí (6)** | ① Nehor y los primeros años del gobierno de los jueces (1–4) · ② Las misiones de Alma (5–16) · ③ La misión de los hijos de Mosíah entre los lamanitas (17–29) · ④ Korihor, los zoramitas y la pobreza (30–35) · ⑤ El consejo de Alma a sus hijos (36–42) · ⑥ El capitán Moroni y las guerras nefitas (43–63) |
| 76 | **Helamán** | 16 | **Sí (4)** | ① El colapso del gobierno de los jueces (1–3) · ② La conversión de los lamanitas (4–6) · ③ El ministerio de Nefi (7–12) · ④ El profeta Samuel el Lamanita (13–16) |
| 77 | **3 Nefi** | 30 | **Sí (5)** | ① Las señales del nacimiento de Cristo (1–2) · ② La guerra contra los ladrones de Gadiantón (3–5) · ③ La gran apostasía y la destrucción (6–10) · ④ Cristo ministra a los nefitas (11–18) · ⑤ El cumplimiento de las profecías y el fin de la era nefita (19–30) |
| 78 | **4 Nefi** | 1 | **Sí (1)** | ① La era de paz y la apostasía final (1) |
| 79 | **Mormón** | 9 | **Sí (3)** | ① La preparación de Mormón (1–3) · ② La destrucción de los nefitas (4–7) · ③ Moroni termina el registro de Mormón (8–9) |
| 80 | **Éter** | 15 | **Sí (5)** | ① El hermano de Jaréd (1–3) · ② El viaje jaredita a la tierra prometida (4–6) · ③ Los reyes jareditas (7–11) · ④ La profecía de Éter (12–13) · ⑤ La destrucción de los jareditas (14–15) |
| 81 | **Moroni** | 10 | **Sí (4)** | ① Las ordenanzas de la Iglesia (1–6) · ② El discurso de Mormón sobre la fe, la esperanza y la caridad (7) · ③ Las epístolas de Mormón (8–9) · ④ El adiós de Moroni (10) |

### Doctrina y Convenios (2 libros)

A diferencia del AT, NT, LdM y PGP —cuyas secciones (capítulos) son
consecutivas dentro de cada libro—, DyC presenta un desafío especial:
las secciones no están ordenadas de forma puramente cronológica ni
geográfica en el canon. La Sección 1 (Prefacio, recibida en Ohio 1831)
abre el libro; las Secciones 133 (Apéndice, Ohio 1831), 134 (Ohio 1835)
y 137 (Ohio 1836) aparecen hacia el final junto con secciones de Misuri,
Nauvoo y la era posterior a José Smith.

Las 5 partes siguientes se definen por **periodo histórico-geográfico**
—el criterio más aceptado entre especialistas (Nathan Richardson,
Encyclopedia of Mormonism, RSC/BYU, Joseph Smith Papers). Cada sección
se asigna a la parte correspondiente a su fecha y lugar de recepción,
independientemente de su posición en el orden canónico.

| # | Libro | Caps. | ¿Tiene partes? | Partes |
|:-:|:------|:-----:|:--------------|:-------|
| 82 | **Secciones** | 138 | **Sí (5)** | ① El periodo de Nueva York (2–40, 74) · ② El periodo de Ohio (1, 41–73, 75–112, 133, 134, 137) · ③ El periodo de Misuri (113–123) · ④ El periodo de Nauvoo (124–132, 135) · ⑤ El periodo del Oeste y la expansión mundial (136, 138) |
| 83 | **Declaraciones Oficiales** | 2 | **Sí (1)** | ① Declaraciones del Oeste y la expansión mundial (DO 1–2) |

**Nota 1 — 5 periodos frente a 6**: Se ha optado por **5 periodos**
(no 6 como el manual SUD de Historia de la Iglesia) porque las 4 piezas
posteriores a José Smith (136, OD-1, 138, OD-2) no justifican 3 partes
separadas. El consenso académico para DyC favorece la división en 5.

**Nota 2 — Unicidad**: Todos los nombres de partes son únicos en todo el
canon. Las únicas excepciones permitidas serían libros verdaderamente
paralelos (p. ej. pasajes sinópticos en los evangelios), pero en la
práctica ninguna parte del AT, NT, LdM, DyC o PGP repite nombre.

**Nota 3 — Granularidad del periodo de Ohio**: Con 75 secciones, la parte
② triplica cualquier otra parte del canon. Es esperable: Ohio fue el
periodo más largo y productivo de José Smith (1831–1837), donde se
recibió ~54 % del contenido de DyC. La integridad temática justifica
mantenerlo como una sola parte, aunque Nathan Richardson lo subdivida
internamente en 6 unidades temáticas para estudio. El nivel `bc_parte`
no admite subniveles, por lo que esta salvedad queda documentada.

### Perla de Gran Precio (5 libros)

| # | Libro | Caps. | ¿Tiene partes? | Partes |
|:-:|:------|:-----:|:--------------|:-------|
| 84 | **Moisés** | 8 | **Sí (4)** | ① La visión de Moisés (1) · ② La Creación del mundo (2–3) · ③ La Caída de Adán y Eva (4) · ④ Los patriarcas antediluvianos (5–8) |
| 85 | **Abraham** | 5 | **Sí (3)** | ① La liberación de Abraham y el convenio (1–2) · ② La visión de la vida premortal (3) · ③ La Creación según Abraham (4–5) |
| 86 | **José Smith—Mateo** | 1 | **Sí (1)** | ① Las señales de la Segunda Venida (1) |
| 87 | **José Smith—Historia** | 1 | **Sí (1)** | ① La Restauración del Evangelio (1) |
| 88 | **Artículos de Fe** | 1 | **Sí (1)** | ① Los trece Artículos de Fe (1) |

---

## Cotejo de los evangelios con esquemas externos

Las partes de los cuatro evangelios se definieron siguiendo un esquema
**geográfico‑cronológico** (preparación → ministerio → viaje a Jerusalén →
pasión/resurrección), con una **capa cristológica** en los nombres (Rey,
Siervo, Hombre, Hijo de Dios) tomada de la tradición de los cuatro retratos.
A continuación se cotejan esas partes con tres familias de divisiones
documentadas en fuentes externas (consultadas en julio de 2026 vía web
search): el **enfoque cristológico clásico**, el **esquema
geográfico‑cronológico académico** y la **estructura literaria por
discursos/señales**.

### A. Enfoque cristológico (Rey / Siervo / Hombre / Hijo de Dios)

Es la división más difundida en línea (Bullinger, *Why Four Gospels?*,
BiblicalTraining, Logos/Mark Strauss, Blue Letter Bible, etc.). No divide
cada evangelio internamente; **asigna un énfasis a todo el libro**. Es
compatible —no competitiva— con nuestro esquema, porque la usamos
exactamente así: como *nombre* de cada parte (el evangelista presenta a
Jesús como Rey, Siervo, etc.), no como criterio de corte.

| Evangelio | Énfasis externo | Nuestras partes | Coincidencia |
|:-----------|:----------------|:----------------|:-------------|
| Mateo | Jesús como **Rey** / Mesías davídico (genealogía → David → Abraham) | "…del Rey" en las 6 partes | ✅ Perfecta |
| Marcos | Jesús como **Siervo** (acción, "inmediatamente", sufrimiento) | "…del Siervo" en las 4 partes | ✅ Perfecta |
| Lucas | Jesús como **Hombre / Hijo del Hombre** (genealogía hasta Adán, compasión) | "…del Hijo del Hombre" en las 4 partes | ✅ Perfecta |
| Juan | Jesús como **Hijo de Dios** / Verbo eterno | "…del Hijo de Dios" en partes ① y ③; el resto usa nombres temáticos ("señales", "despedida", "epílogo") | ✅ Ajustada — cotejo *infra* §C |

**Hallazgo**: los cuatro evangelios encajan en el marco de los cuatro
retratos. Mateo y Marcos llevan el marcador cristológico (Rey, Siervo)
en todas sus partes; Lucas lo adoptó renombrando sus 4 partes a "…del
Hijo del Hombre". Juan lo mantiene en las partes donde el título es
más relevante (encarnación ① y señales ③), mientras que las partes
②, ④ y ⑤ usan nombres temáticos ("primeras señales", "despedida",
"epílogo") para evitar repetición excesiva de "Hijo de Dios". Los
rangos de capítulos no cambian, solo los nombres.

### B. Esquema geográfico‑cronológico académico

Es el estándar de la crítica narrativa (BibleProject, Britannica, Catholic
Resources/F. Just, Knowable Word, Enter the Bible). Coincide de manera muy
estrecha con nuestras partes:

| Evangelio | Fuentes externas (rangos) | Nuestras partes | Coincidencia |
|:-----------|:--------------------------|:----------------|:-------------|
| **Mateo** | Enciclopedia.com: (a) Intr. 1,1–4,11; (b) Galilea 4,12–13,58; (c) viajes 14,1–20,34; (d) Jerusalén/pasión 21,1–28,20. Católicos: 5 discursos 5–7 / 10 / 13 / 18 / 23–25. | ① 1–4 · ② 5–7 · ③ 8–11 · ④ 12–16 · ⑤ 17–20 · ⑥ 21–28 | ✅ Muy cercana (ver nota) |
| **Marcos** | BibleProject/Britannica: (1) Galilea 1–8; (2) camino 8,27/22–10,52; (3) Jerusalén/pasión 11–16. | ① 1 · ② 2–8 · ③ 9–10 · ④ 11–16 | ✅ Casi idéntica |
| **Lucas** | Catholic Resources: Prefacio+Infancia 1–2; Prep. 3–4,13; Galilea 4,14–9,50; Viaje 9,51–19,27; Jerusalén 19,28–21,38; Pasión 22–23; Resurrección 24. | ① 1–3 · ② 4–9 · ③ 10–19 · ④ 20–24 | ✅ Muy cercana (ver nota) |
| **Juan** | Estándar: Prólogo 1; señales 2–12; discurso de despedida 13–17; pasión/resurrección 18–21. | ① 1 · ② 2–4 · ③ 5–12 · ④ 13–17 · ⑤ 18–21 | ✅ Idéntica |

**Notas de Mateo y Lucas** (granularidad mayor en nuestro documento):

- **Mateo**: las fuentes académicas tienden a agrupar 1–4,11 como
  "introducción" y 4,12–13,58 como "Galilea". Nosotros **subdividimos** el
  bloque en 6 partes siguiendo la estructura de los **5 discursos**
  (Sermón del Monte 5–7, Misión 10, Parábolas 13, Comunidad 18,
  Escatología 23–25) más los bloques narrativos de transición. Es decir:
  usamos la misma frontera geográfica general, pero añadimos granularidad
  temática que la crítica reconoce (los 5 discursos de Mateo son
  marcadores literarios consagrados). ✅ Compatible con **Rangos acotados**
  y **Nombres clásicos**.
- **Lucas**: nuestra parte ① abarca 1–3 (infancia + preparación), mientras
  que Catholic Resources separa Prefacio/Infancia (1–2) de Preparación
  (3–4,13). Nuestra parte ④ abarca 20–24 (pasión + resurrección), mientras
  que la fuente separa Pasión (22–23) de Resurrección (24). Ambas son
  variaciones de la misma estructura de 5–7 movimientos; la diferencia es
  solo el grado de granularidad. ✅ Compatible.

### C. Estructura literaria (discursos / señales)

- **Mateo — 5 discursos** (Católicos/F. Just; formula "cuando Jesús
  acabó…" en 7,28; 11,1; 13,53; 19,1; 26,1): 5–7, 10, 13, 18, 23–25.
  Nuestras partes ② (5–7), ③ (8–11, incluye el discurso 10), ④ (12–16,
  incluye el discurso 13), ⑤ (17–20, incluye el discurso 18) y ⑥ (21–28,
  incluye el discurso 23–25) respetan esos bloques como fronteras. ✅
- **Marcos — 3 predicciones de la pasión** (8,31; 9,31; 10,32‑34) que
  estructuran la sección "en camino". Nuestra parte ③ (9–10) es
  exactamente ese bloque. ✅
- **Lucas — geografía como marcador** (Knowable Word: resumen de
  escenario en 4,14‑15, 9,51, 19,28, 21,37‑38). Nuestras partes ②/③/④
  siguen esas mismas fronteras geográficas. ✅
- **Juan — 7 señales / "Yo soy"** (BiblicalTraining: 7 señales antes de la
  cruz + 1 después; 8 declaraciones "Yo soy"). Nuestras partes ② (2–4) y
  ③ (5–12) envuelven exactamente el bloque de señales; ④ (13–17) es el
  discurso de despedida; ⑤ (18–21) la pasión/resurrección. ✅

### D. Estructura quiásmica de Mateo (Price, Lohr, Roig Cervera, Fenton)

Varios autores (Larry Price, Charles Lohr, Miguel Ángel Roig Cervera, J. C. Fenton) detectan una **estructura quiásmica o concéntrica** en Mateo, con el capítulo 13 (Parábolas del Reino) como centro:

| Par | Rango | Contenido | Par | Rango | Contenido |
|:----|:-----:|:----------|:----|:-----:|:----------|
| **A** | 1–4 | Demostración del Rey | **A'** | 26–28 | Consumación del Rey |
| **B** | 5–7 | Sermón del Monte | **B'** | 24–25 | Discurso escatológico |
| **C** | 8–9 | Milagros e instrucción | **C'** | 19–23 | Instrucción y milagros |
| **D** | 10 | Instrucción a los Doce (Israel) | **D'** | 18 | Instrucción a los Doce (Iglesia) |
| **E** | 11–12 | Rechazo del Rey | **E'** | 14–17 | Oposición al Rey |
| **F** | **13** | **Parábolas del Reino (centro)** | | | |

Nuestras 6 partes: ①(1–4)=A, ②(5–7)=B, ③(8–11)=C+D, ④(12–16)=E+E', ⑤(17–20)=C'+D', ⑥(21–28)=B'+A'.

✅ **Compatible**: las fronteras del quiasmo son respetadas. Nuestra granularidad (6 partes vs. 11) es una agregación natural.

### E. Estructura tripartita de Mateo (Kingsbury)

Jack Dean Kingsbury divide Mateo en **3 movimientos** marcados por «Desde entonces comenzó Jesús» (ἀπὸ τότε ἤρξατο) en 4:17 y 16:21:

| Sección | Rango | Tema |
|:--------|:-----:|:-----|
| I. Persona de Jesús Mesías | 1:1–4:16 | Origen, genealogía, nacimiento, bautismo, tentación |
| II. Proclamación del Reino | 4:17–16:20 | Jesús predica y enseña el Reino |
| III. Pasión, muerte y resurrección | 16:21–28:20 | Anuncio del destino, sufrimiento, cruz |

Mapeo: ①(1–4)=**I**; ②(5–7)+③(8–11)+④(12–16)=**II**; ⑤(17–20)+⑥(21–28)=**III**.

✅ **Compatible**: la frontera ④→⑤ en 16:21 coincide exactamente con Kingsbury. Nuestras partes subdividen los movimientos II y III en unidades más manejables sin romper la lógica del autor.

### F. Alternancia discurso-narrativa (Bacon, Weren)

B. W. Bacon y Wim Weren proponen que Mateo alterna **5 discursos** con narrativa, más obertura y finale:

| Bloque | Rango | Discurso |
|:-------|:-----:|:---------|
| Obertura | 1:1–4:11 | — |
| ① | 4:18–7:29 | Sermón del Monte (5–7) |
| ② | 8:1–11:1 | Discurso Misionero (10) |
| ③ | 11:2–13:53 | Parábolas del Reino (13) |
| ④ | 13:54–19:1 | Discurso de la Iglesia (18) |
| ⑤ | 19:2–26:1 | Discurso Escatológico (23–25) |
| Finale | 26:17–28:20 | Pasión/Resurrección |

Mapeo: ①(1–4)=Obertura; ②(5–7)=Discurso 1; ③(8–11)=Narrativa+Discurso 2; ④(12–16)=Narrativa+Discurso 3; ⑤(17–20)=Discurso 4+prepasión; ⑥(21–28)=Discurso 5+Finale.

✅ **Compatible**: los 5 discursos clásicos están íntegros dentro de nuestras partes. Agrupamos discurso+narrativa circundante por **integridad temática**, criterio que aplicamos en todo el canon.

### G. Cuatro seres vivientes (Ezequiel 1 / Apocalipsis 4)

Tradición patrística (Ireneo de Lyon, s. II) que asocia cada evangelista con una criatura celestial. Es la base simbólica del enfoque cristológico:

| Ser | Evangelio | Conexión |
|:----|:----------|:---------|
| **León** (realeza) | **Mateo** — el Rey | ✅ Nuestras 6 partes llevan «…del Rey» |
| **Buey** (servicio/sacrificio) | **Marcos** — el Siervo | ✅ Nuestras 4 partes llevan «…del Siervo» |
| **Hombre** (humanidad) | **Lucas** — el Hijo del Hombre | ✅ Nuestras 4 partes llevan «…del Hijo del Hombre» |
| **Águila** (divinidad, cielo) | **Juan** — el Hijo de Dios | ✅ Nuestras 5 partes llevan «…del Hijo de Dios» |

✅ **Perfecta**: es el precedente directo de nuestra capa cristológica en los nombres. No afecta fronteras.

### H. Juan: Libro de los Signos / Libro de la Gloria (Brown, Bultmann, Zumstein)

El consenso joánico divide el cuarto evangelio en dos grandes bloques teológicos:

| Fuente | División | Nuestras partes |
|:-------|:---------|:----------------|
| **Brown**: Prólogo (1:1–18) + Signos (1:19–12:50) + Gloria (13:1–20:31) + Epílogo (21) | 4 mov. | ①(1)=Prólogo · ②(2–4)+③(5–12)=Signos · ④(13–17)+⑤(18–21)=Gloria |
| **Bultmann**: Revelación al mundo (1–12) + Revelación a la comunidad (13–20) | 2 partes | Idéntico en agrupación |
| **Boismard**: 7 semanas simbólicas | 7 períodos | No adoptado (especulativo) |
| **Beutler/Zumstein**: Signos + Gloria + Prólogo/Epílogo | 4 secciones | Nuestra parte ② subdivide «presentación» del bloque de signos |

✅ **Idéntica en estructura gruesa**: la frontera ③→④ (Juan 13) es la misma que separa Signos de Gloria. Nuestra subdivisión de la Gloria en ④(13–17)+⑤(18–21) sigue la práctica común de separar el discurso de despedida del relato de la pasión.

### I. Cánones Eusebianos / Secciones Amonianas

Sistema más antiguo de división de evangelios (~220 d.C. Amonio de Alejandría, ~325 d.C. Eusebio de Cesarea): ~1165 secciones numeradas con 10 tablas para localizar pasajes paralelos. No es estructural sino **sinóptico** (armonización entre evangelios).

| Evangelio | Secciones | Propias (Tabla X) | Nuestras partes |
|:----------|:---------:|:-----------------:|:---------------:|
| Mateo | **355** | 93 | 6 |
| Marcos | **235** | 74 | 4 |
| Lucas | **343** | 97 | 4 |
| Juan | **232** | 65 | 5 |

**Hallazgo**: no compite con nuestras partes — opera a nivel de perícopa individual. Las 19 partes de los evangelios son agregaciones naturales de estas secciones. Precedente histórico relevante que muestra que la división de los evangelios es una práctica con 18 siglos de tradición.

### Conclusión del cotejo

1. **El esquema base (geográfico‑cronológico) está validado** por la
   crítica narrativa externa en los cuatro evangelios; las fronteras
   coinciden salvo por grados de granularidad (Mateo y Lucas algo más
   subdivididos que el mínimo académico, lo cual es deseable según el
   criterio **Rangos acotados**).
2. **La capa cristológica (Rey/Siervo/Hijo del Hombre/Hijo de Dios) está
   aplicada coherentemente en los cuatro evangelios**: Mateo (Rey), Marcos
   (Siervo), Lucas (Hijo del Hombre) y Juan (Hijo de Dios). Los nombres de
   parte reflejan este marco en todos los casos; los rangos de capítulos no
   cambian.
3. **Los 6 esquemas adicionales (quiásmico, tripartito, 5 discursos,
   seres vivientes, Signos/Gloria, cánones eusebianos) son compatibles**
   con nuestras 19 partes: o bien operan a distinto nivel de granularidad
   (perícopa vs. parte), o bien confirman las mismas fronteras, o bien se
   alinean como sub/conjuntos de nuestras divisiones. Ninguno revela una
   contradicción que exija cambiar una frontera o un nombre.
4. **No se requieren cambios estructurales** en las 19 partes de los
   evangelios. La validación con **9 familias de esquemas externos**
   (cristológico, geográfico, 5 discursos, quiásmico, tripartito,
   alternancia discurso-narrativa, 4 seres vivientes, Signos/Gloria,
   cánones eusebianos) confirma que las fronteras actuales son sólidas
   y bien documentadas.

---

## Libros concordantes (concordancias literarias)

Las concordancias literarias entre libros que deben estudiarse juntos
(Génesis/Moisés/Abraham, Samuel–Reyes/Crónicas, 2 Pedro/Judas, Isaías/1–2
Nefi/Evangelios, Lehi–Nephi/Moisés, Evangelios, Lucas/Hechos) se documentan
en un archivo aparte para estudio y uso compartido entre sesiones:

→ **`libros-concordantes.md`**

---

## Consolidado

| Volumen | Libros con partes | Total partes | Libros sin partes |
|:--------|:-----------------|:------------:|:-----------------:|
| Antiguo Testamento | 39 | **161** | 0 |
| Nuevo Testamento | 27 | **83** | 0 |
| Libro de Mormón | 15 | **49** | 0 |
| Doctrina y Convenios | 2 | **6** | 0 |
| Perla de Gran Precio | 5 | **10** | 0 |
| **Total** | **88 libros** | **309 partes** | **0 libros** |

## Validación con BYU Studies: Charting the New Testament

En julio de 2026 se descargaron y examinaron los 14 PDFs de la Sección 14
("Overviews of the Epistles") de *Charting the New Testament* (Welch, Hall,
FARMS, 2002). Cada chart presenta el libro como una secuencia de perícopas
(pasajes individuales con rango de versículos y título temático).

### Hallazgos

| Libro | Partes actuales | ¿Consistente con BYU? | Notas |
|:------|:---------------:|:---------------------:|:------|
| Romanos | 3 | ✅ | 1–8 (justicia revelada), 9–11 (vindicación), 12–16 (aplicación) |
| 1 Corintios | 3 | ✅ | Informe de Cloé (1–4), fornicación (5–6), carta (7–16) |
| 2 Corintios | 3 | ✅ | Defensa (1–7), ofrenda (8–9), autoridad (10–13) |
| Gálatas | 3 | ✅ | Defensa (1–2), explicación (3–4), aplicación (5–6) |
| Efesios | 2 | ✅ | Doctrina (1–3), práctica (4–6) |
| Filipenses | 4 | ✅ | 1 parte por capítulo |
| Colosenses | 2 | ✅ | Supremacía (1–2), sujeción (3–4) |
| 1 Tesalonicenses | 2 | ✅ | Reflexiones (1–3), instrucciones (4–5) |
| 2 Tesalonicenses | 3 | ✅ | 1 parte por capítulo |
| 1 Timoteo | 5 | ✅ | Por tema de encargo |
| 2 Timoteo | 2 | ✅ | Pruebas presentes (1–2), futuras (3–4) |
| Tito | 2 | ✅ | Ancianos (1), orden (2–3) |
| Filemón | 1 | ✅ | Único capítulo |
| Hebreos | 3 | ✅ | Superioridad de Cristo (1–4), sacerdocio (5–10), fe (11–13) |
| Santiago | 3 | ✅ | Prueba (1), características (2–4), triunfo (5) |
| 1 Pedro | 3 | ✅ | Salvación/santificación (1–2), sufrimiento (3–4), pastoreo (5) |
| 2 Pedro | 3 | ✅ | 1 parte por capítulo |
| 1 Juan | 3 | ✅ | Condiciones (1–2), características (3–4), consecuencias (5) |

**Conclusión**: Las 83 partes del NT están validadas por los charts de BYU
Studies. No se requieren refinamientos estructurales.

---

## Implicaciones

- **bc_parte** tendrá ~309 términos, cada uno con `term_meta`:
  - `_book_id` → ID del `bc_libro` padre
  - `_chapter_start` → primer capítulo del rango (int)
  - `_chapter_end` → último capítulo del rango (int)
  - Para DyC, donde las secciones de una parte no son consecutivas en el
    canon (ej. "El periodo de Nueva York" abarca 2–40, 74), el modelo de
    `_chapter_start`/`_chapter_end` no aplica directamente. Alternativa
    pendiente: usar una lista de IDs de sección en `term_meta`.
- Ya no hay libros sin partes. Toda la ruta larga incluye el nivel
  `bc_parte`.
- Los nombres de partes deben ser **slugificados** para el rewrite: usar
  `sanitize_title()` en WordPress con nombres como
  `primer-discurso-de-moises`.
