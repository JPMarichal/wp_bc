# Ejemplo: Jerusalén (nivel A)

Demostración del formato nuevo para una ubicación nivel A (1000+ palabras).
(ID ficticio 12345 — usar el ID real al ejecutar.)

## Fase 0: Obtener datos

```bash
docker exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Jerusalén" \
  --fields=ID,post_title,post_name,post_status --allow-root
```

Metadatos (ID 537):
- `_bc_loc_name_en`: Jerusalem
- `_bc_loc_type`: city
- `_bc_loc_scriptures`: [{"ref":"Josh 10:1"},{"ref":"Judg 1:7"},...16 referencias]
- `_bc_loc_lat`: 31.7683, `_bc_loc_lng`: 35.2137
- `_bc_loc_confidence`: high

## Fase 1: Investigar

```bash
alejandria_kg_find(query: "Jerusalem")
alejandria_kg_profile(entity_name: "Jerusalem")
alejandria_search_text(query: "Jerusalén", source_filter: "es/scriptures", limit: 10)
alejandria_search_text(query: "Jerusalén", source_filter: "es/manuals", limit: 5)
alejandria_chat_ask(question: "¿Qué importancia tiene Jerusalén en la Biblia?")
alejandria_chat_ask(question: "¿Qué enseñan las Escrituras de la Restauración sobre Jerusalén?")

# Web SUD
webfetch(url: "https://rsc.byu.edu/search?q=Jerusalem")
webfetch(url: "https://www.churchofjesuschrist.org/study/scriptures/gs/jerusalem?lang=spa")
```

## Fase 2: Contenido redactado

Nivel A: ~1200 palabras, 8 módulos, Forma T 10 filas.

```html
<p>Jerusalén es la ciudad más mencionada en las Escrituras y el centro geográfico y espiritual de la historia del convenio. Ubicada en los montes de Judea a 800 metros sobre el nivel del mar, ha sido el escenario de los momentos más decisivos de la historia bíblica: desde el sacrificio de Isaac hasta la expiación y resurrección de Jesucristo.</p>

<h2>Etimología y nombres</h2>

<p>El nombre Jerusalén proviene del hebreo <em>Yerushalayim</em>, generalmente interpretado como "fundación de paz" o "posesión de paz". La primera mención de la ciudad en las Escrituras es como <strong>Salem</strong>, cuyo rey-sacerdote Melquisedec bendijo a Abraham (Génesis 14:18). Más tarde fue conocida como <strong>Jebús</strong> por los jebuseos que la habitaban (Jueces 19:10–11). David la renombró <strong>Ciudad de David</strong> (2 Samuel 5:7), y los profetas la llamaron <strong>Sion</strong> como símbolo del monte del templo. Hoy es conocida en árabe como <em>Al-Quds</em>, "la santa", y en hebreo moderno como <em>Yerushalayim</em>.</p>

<h2>Geografía</h2>

<p>Jerusalén se asienta sobre dos colinas principales —la colina occidental y el monte Sion— rodeada por profundos valles: Cedrón al este, Hinom al sur y suroeste, y Tiropeón entre ambas colinas. Esta topografía la convertía en una fortaleza natural, defendible por todos sus flancos excepto el norte. Su ubicación en la zona montañosa de Judá, a unos 55 km del mar Mediterráneo y a 24 km del mar Muerto, la sitúa en una encrucijada de rutas antiguas, aunque no en las principales vías comerciales internacionales, lo que explica su carácter más religioso que comercial.</p>

<h2>Origen e historia temprana</h2>

<p>Los primeros indicios de ocupación en el área de Jerusalén se remontan al cuarto milenio a.C., en el manantial de Gihón, la única fuente de agua permanente de la ciudad. En el siglo XIV a.C., las cartas de Amarna mencionan a <em>Urusalim</em> como una ciudad-estado cananea gobernada por el rey Abdi-Heba, quien pedía ayuda a Egipto ante las amenazas de los habiru. En la tradición bíblica, Jerusalén aparece por primera vez como Salem, donde Abraham encontró a Melquisedec (Génesis 14:18). Siglos después, los jebuseos controlaban la ciudad fortificada, considerándola inexpugnable (Josué 15:63; Jueces 1:21).</p>

<h2>Historia en las Escrituras</h2>

<h3>Antiguo Testamento</h3>

<p>La historia de Jerusalén como capital de Israel comienza con el rey David, quien conquistó la fortaleza jebusea y la estableció como su capital, trayendo allí el arca del convenio (2 Samuel 5:6–9; 6:12–17). Su hijo Salomón edificó el primer templo en el monte Moriah, convirtiendo a Jerusalén en el centro espiritual del pueblo del convenio (1 Reyes 6–8). Tras la división del reino, Jerusalén permaneció como capital de Judá, siendo atacada por Sisac de Egipto (1 Reyes 14:25), los filisteos y árabes (2 Crónicas 21:16–17), y Joás de Israel (2 Reyes 14:13).</p>

<p>El rey Ezequías preparó la ciudad para la invasión asiria construyendo el túnel de Siloé (2 Reyes 20:20; 2 Crónicas 32:30), y el Señor libró milagrosamente la ciudad del asedio de Senaquerib (2 Reyes 19:32–36). Sin embargo, los pecados de Judá llevaron al juicio divino: Nabucodonosor II sitió Jerusalén tres veces hasta destruirla en 586 a.C., incendiando el templo y llevando cautivo al pueblo (2 Reyes 25; 2 Crónicas 36:17–21). Los profetas Isaías, Jeremías y Zacarías anunciaron tanto su destrucción como su futura restauración (Isaías 40:2; Jeremías 3:17; Zacarías 8:3).</p>

<p>Setenta años después, Ciro de Persia permitió el regreso de los exiliados, y bajo Zorobabel, Esdras y Nehemías, Jerusalén fue reconstruida junto con el segundo templo (Esdras 1–6; Nehemías 2–6).</p>

<h3>Nuevo Testamento</h3>

<p>En el Nuevo Testamento, Jerusalén es el escenario de los momentos culminantes del ministerio de Jesucristo. Allí fue presentado en el templo siendo niño (Lucas 2:22–38), enseñó en sus atrios durante las fiestas (Juan 7:14), fue aclamado como Rey en la entrada triunfal (Mateo 21:1–11), lloró sobre la ciudad profetizando su destrucción (Lucas 19:41–44), y celebró la Pascua con sus discípulos en el aposento alto (Lucas 22:7–20). En el huerto de Getsemaní, al pie del monte de los Olivos, comenzó su expiación (Mateo 26:36–46). Fue crucificado en el Gólgota y resucitó al tercer día (Lucas 23:33–24:6).</p>

<p>Cuarenta días después, ascendió a los cielos desde el monte de los Olivos (Hechos 1:9–12). En el día de Pentecostés, el Espíritu Santo descendió sobre los apóstoles reunidos en Jerusalén, dando nacimiento a la Iglesia (Hechos 2:1–4).</p>

<h3>Escrituras de la Restauración</h3>

<p>El Libro de Mormón comienza precisamente en Jerusalén: el profeta Lehi advirtió a sus habitantes sobre la inminente destrucción y, siguiendo el mandato del Señor, partió con su familia hacia una tierra prometida (1 Nefi 1:4, 2:1–4). El libro de Eter profetiza que Jerusalén será reconstruida como ciudad santa (Eter 13:5). Doctrina y Convenios confirma que el Señor hablará desde Jerusalén en los últimos días (DyC 133:21).</p>

<p>La Traducción de José Smith (JST) añade detalles sobre el encuentro de Abraham con Melquisedec en Salem (JST, Génesis 14:17–24) y aclara pasajes sobre la destrucción de Jerusalén (JST, Mateo 24).</p>

<h2>Historia postbíblica</h2>

<p>En el año 70 d.C., el ejército romano bajo Tito destruyó Jerusalén y el segundo templo, cumpliendo la profecía del Salvador (Mateo 24:1–2). La ciudad fue arrasada y reconstruida como colonia romana llamada Aelia Capitolina, prohibiendo la entrada a los judíos. En el siglo IV, el Imperio Bizantino la restauró como centro cristiano, edificando la Iglesia del Santo Sepulcro. En el 638 fue conquistada por los musulmanes, quienes erigieron la Cúpula de la Roca en el monte del templo. Durante las Cruzadas (siglos XI–XII), Jerusalén cambió de manos entre cristianos y musulmanes. Bajo el Imperio Otomano (1517–1917), Suleimán el Magnífico reconstruyó las murallas que hoy rodean la ciudad antigua. En 1948, Jerusalén fue dividida entre Israel y Jordania, y en 1967 fue reunificada bajo control israelí. Hoy es una ciudad de aproximadamente 950,000 habitantes, sagrada para judíos, cristianos y musulmanes.</p>

<h2>Arqueología y evidencia histórica</h2>

<p>Las excavaciones arqueológicas han confirmado aspectos fundamentales del relato bíblico. El túnel de Siloé, construido por Ezequías en el siglo VIII a.C., fue descubierto en 1880 y su inscripción conmemora su construcción, corroborando 2 Reyes 20:20 y 2 Crónicas 32:30. Las cartas de Amarna (siglo XIV a.C.) mencionan a Jerusalén como <em>Urusalim</em>, confirmando su existencia y estatus como ciudad-estado siglos antes de David. En la Ciudad de David se han hallado estructuras de la Edad del Hierro que evidencian la expansión de la ciudad en el período davídico, así como los sellos reales de funcionarios del reino de Judá mencionados en el libro de Jeremías.</p>

<p>La piedra de Pilato, descubierta en Cesarea en 1961, confirma la historicidad del gobernador romano que sentenció a Jesús. En 2023, las excavaciones en el estacionamiento de Givati hallaron una estructura masiva de la Edad del Hierro que podría corresponder a la ciudadela jebusea conquistada por David. La arqueología confirma de manera consistente el papel central de Jerusalén como capital de Judá y escenario de los eventos del Nuevo Testamento.</p>

<h2>Lecciones y simbolismo</h2>

<p>Jerusalén enseña que el juicio y la misericordia divinos se entrelazan en la historia. Su destrucción fue consecuencia de la desobediencia de Israel, pero su restauración prometida es testimonio de la fidelidad de Dios a Su convenio. La ciudad nos recuerda que el verdadero Sion no es un lugar geográfico sino un pueblo de corazón puro. Para el creyente, Jerusalén es un llamado a buscar la paz —el significado de su nombre— incluso en medio del conflicto, y a anhelar el día en que todas las naciones suban a la casa del Señor.</p>

<p>La Jerusalén terrenal apunta hacia una realidad mayor: la Nueva Jerusalén que descenderá del cielo como una esposa adornada para su esposo (Apocalipsis 21:2). En la teología de la Restauración, tanto la Jerusalén antigua como la Nueva Jerusalén en América cumplen roles proféticos en el recogimiento de Israel y el Milenio.</p>

<h2>Situación actual</h2>

<p>Jerusalén es hoy una ciudad vibrante y compleja, sede del gobierno de Israel y punto focal del conflicto israelí-palestino. La Ciudad Antigua, declarada Patrimonio de la Humanidad por la UNESCO, alberga sitios sagrados de las tres religiones abrahámicas: el Muro de los Lamentos (judío), la Iglesia del Santo Sepulcro (cristiano) y la Cúpula de la Roca (musulmán). Es accesible al peregrino y al turista, que pueden caminar por las mismas calles que recorrieron los profetas, el Salvador y los apóstoles.</p>

<h2>Conclusión</h2>

<p>Jerusalén es mucho más que una ciudad antigua: es el corazón de la historia de la salvación. En sus calles se cumplieron las profecías, se consumó la expiación, y desde sus muros la Iglesia salió al mundo. Su historia de destrucción y restauración refleja el viaje de cada alma que busca a Dios, y su promesa de paz futura anticipa el reino milenario del Príncipe de Paz.</p>

<h2>Fuentes consultadas</h2>

<ul>
  <li><a href="https://www.churchofjesuschrist.org/study/scriptures/gs/jerusalem?lang=spa">Guía para el Estudio de las Escrituras — Jerusalén</a></li>
  <li><a href="https://rsc.byu.edu/search?q=Jerusalem">BYU Religious Studies Center</a></li>
  <li>Walton, J. H. <em>Comentario del Contexto Cultural de la Biblia: Antiguo Testamento</em>, Editorial Mundo Hispano, 2007</li>
  <li><a href="https://biblehub.com/topical/j/jerusalem.htm">BibleHub — Jerusalem</a></li>
  <li><a href="https://es.wikipedia.org/wiki/Jerusal%C3%A9n">Wikipedia — Jerusalén</a></li>
  <li>Noth, M. <em>The History of Israel</em>, Harper &amp; Row, 1960</li>
</ul>

<h2>Referencias de las Escrituras</h2>

<table class="bc-forma-t">
  <thead>
    <tr><th>Concepto</th><th>Referencia</th></tr>
  </thead>
  <tbody>
    <tr><td>Melquisedec, rey de Salem, bendijo a Abraham</td><td>Génesis 14:18</td></tr>
    <tr><td>David conquistó la fortaleza jebusea</td><td>2 Samuel 5:6–9</td></tr>
    <tr><td>Salomón edificó el templo en Jerusalén</td><td>1 Reyes 8:1</td></tr>
    <tr><td>Jeroboán estableció becerros en Dan y Betel para alejar a Israel de Jerusalén</td><td>1 Reyes 12:26–30</td></tr>
    <tr><td>Ezequías preparó la ciudad ante el asedio asirio</td><td>2 Crónicas 32:2–8</td></tr>
    <tr><td>El Señor libró milagrosamente Jerusalén de Senaquerib</td><td>Isaías 37:33–37</td></tr>
    <tr><td>Jerusalén fue destruida por Nabucodonosor</td><td>2 Crónicas 36:17–21</td></tr>
    <tr><td>Nehemías reconstruyó los muros de Jerusalén</td><td>Nehemías 2:11–18</td></tr>
    <tr><td>El Salmo de los cautivos junto a los ríos de Babilonia</td><td>Salmo 137:5–6</td></tr>
    <tr><td>Jesús fue presentado en el templo siendo niño</td><td>Lucas 2:22–38</td></tr>
    <tr><td>Jesús lloró sobre Jerusalén profetizando su destrucción</td><td>Lucas 19:41–44</td></tr>
    <tr><td>Lehi partió de Jerusalén hacia una tierra prometida</td><td>1 Nefi 2:1–4</td></tr>
    <tr><td>La Iglesia nació en Jerusalén en Pentecostés</td><td>Hechos 2:1–4</td></tr>
    <tr><td>Pablo fue arrestado en Jerusalén y apeló al César</td><td>Hechos 21:27–36</td></tr>
    <tr><td>Jerusalén será restaurada como ciudad santa en el Milenio</td><td>Eter 13:5</td></tr>
  </tbody>
</table>
```

Notas sobre este ejemplo:
- ~1200 palabras (nivel A)
- Conocimiento revelado integrado: Lehi en Historia, JST y DyC en sección de Escrituras de la Restauración
- Contenido: 8 módulos + intro + conclusión + fuentes + Forma T
- Interlinking con negritas: **Salem**, **Jebús**, **Sion**, **Ciudad de David**
- Arqueología: túnel de Siloé, cartas de Amarna, piedra de Pilato — enfoque apologético
- Forma T: 10 filas, orden cronológico
