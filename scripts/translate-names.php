<?php
/**
 * Batch translation of bc_location names from English to Spanish (RV60).
 *
 * Approach:
 *   1. If post_title already looks Spanish → update _bc_loc_name_es from it
 *   2. If English title matches $known_translations exactly → translate
 *   3. Otherwise → leave as-is (proper names same in both languages)
 *
 * No word-by-word replacement. No capitalization damage.
 *
 * Run: docker cp scripts/translate-names.php wp_bc:/tmp/translate-names.php
 *      docker exec wp_bc php /tmp/translate-names.php [--dry-run]
 */

$dry_run = in_array('--dry-run', $argv ?? []);

require_once '/var/www/html/wp-load.php';

// ── Spanish detection ──────────────────────────────────────────────

function looks_spanish($title) {
  if (preg_match('/[áéíóúüñÁÉÍÓÚÜÑ]/u', $title)) {
    return true;
  }
  $lower = ' ' . mb_strtolower($title) . ' ';
  $patterns = [
    ' de ', ' del ', ' de los ', ' de la ', ' de las ',
    ' monte ', ' valle ', ' río ', ' arroyo ', ' ciudad ', ' casa ',
    ' mar ', ' desierto ', ' fuente ', ' pozo ', ' torre ', ' puerta ',
    ' campo ', ' roca ', ' cueva ', ' llanura ', ' camino ', ' paso ',
    ' vado ', ' cuesta ', ' lugar ', ' palacio ', ' fortaleza ',
    ' huerta ', ' palmera ', ' encina ', ' higuera ',
    ' reina ', ' rey ', ' reino ',
    ' norte ', ' sur ', ' este ', ' oeste ',
    ' oriental ', ' occidental ',
    ' nueva ', ' nuevo ', ' vieja ', ' viejo ', ' gran ',
    ' sangre ', ' refugio ', ' destrucción ', ' rebaño ',
    ' caballos ', ' ovejas ', ' pescado ', ' muladar ',
    ' guardia ', ' cárcel ', ' rincón ', ' enmedio ',
    ' benjamín ', ' efraín ', ' judá ', ' josé ', ' david ',
    ' baal ', ' dagón ', ' astarot ',
    ' jehová ', ' señor ', ' dios ',
    ' aguas ', ' alto ', ' alta ',
    ' fuentes ', ' pozos ', ' torres ', ' puertas ',
    ' campamento ', ' campamentos ',
    ' lugares altos ', ' lugar alto ',
    ' arroyos ', ' ríos ', ' montes ', ' valles ',
    ' cuestas ', ' vados ',
    ' laguna ', ' estanque ', ' cisterna ',
    ' acacia ', ' almendro ', ' olivo ', ' arrayán ', ' retama ',
    ' espino ', ' zarza ', ' caña ', ' azucena ',
    ' pino ', ' ciprés ', ' sauce ',
    ' león ', ' serpiente ', ' águila ', ' paloma ', ' tórtola ',
    ' becerro ', ' buey ', ' toro ', ' oveja ', ' cabra ',
    ' santa ', ' santo ',
  ];
  foreach ($patterns as $p) {
    if (mb_strpos($lower, $p) !== false) return true;
  }
  return false;
}

// ── Key normalizer ─────────────────────────────────────────────────

function normalize_key($title) {
  $key = mb_strtolower(trim($title));
  // Normalize curly/smart apostrophes to straight
  $key = str_replace(["'", "\u{2018}", "\u{2019}", "\u{201B}"], "'", $key);
  // Strip trailing parenthesized suffixes
  $key = preg_replace('/\s*\([^)]*\)\s*$/', '', $key);
  // Strip leading "the"
  $key = preg_replace('/^the\s+/', '', $key);
  return trim($key);
}

// ── Known translations map ─────────────────────────────────────────

$known_translations = [
  // Multi-word geographic
  'sea of galilee' => 'Mar de Galilea',
  'sea of chinnereth' => 'Mar de Cineret',
  'sea of the arabah' => 'Mar del Arabá',
  'salt sea' => 'Mar Salado',
  'great sea' => 'Mar Grande',
  'western sea' => 'Mar Occidental',
  'eastern sea' => 'Mar Oriental',
  'dead sea' => 'Mar Salado',
  'red sea' => 'Mar Rojo',
  'mount of olives' => 'Monte de los Olivos',
  'mount sinai' => 'Monte Sinaí',
  'mount horeb' => 'Monte Horeb',
  'mount zion' => 'Monte Sion',
  'mount moriah' => 'Monte Moriah',
  'mount hermon' => 'Monte Hermón',
  'mount carmel' => 'Monte Carmelo',
  'mount ebal' => 'Monte Ebal',
  'mount gerizim' => 'Monte Gerizim',
  'mount gilboa' => 'Monte Gilboa',
  'mount hor' => 'Monte Hor',
  'mount nebo' => 'Monte Nebo',
  'mount tabor' => 'Monte Tabor',
  'mount seir' => 'Monte Seir',
  'mount gilead' => 'Monte Galaad',
  'wilderness of zin' => 'Desierto de Zin',
  'wilderness of paran' => 'Desierto de Parán',
  'wilderness of shur' => 'Desierto de Shur',
  'wilderness of sinai' => 'Desierto de Sinaí',
  'wilderness of judah' => 'Desierto de Judá',
  'wilderness of beersheba' => 'Desierto de Beerseba',
  'wilderness of damascus' => 'Desierto de Damasco',
  'wilderness of moab' => 'Desierto de Moab',
  'wilderness of judea' => 'Desierto de Judea',
  'wilderness of engedi' => 'Desierto de En-gadi',
  'wilderness of etham' => 'Desierto de Etam',
  'wilderness of jeruel' => 'Desierto de Jeruel',
  'wilderness of kadesh' => 'Desierto de Cades',
  'wilderness of maon' => 'Desierto de Maón',
  'wilderness of sin' => 'Desierto de Sin',
  'wilderness of ziph' => 'Desierto de Zif',
  'arabah' => 'Arabá',
  'valley of achor' => 'Valle de Acor',
  'valley of aijalon' => 'Valle de Ajalón',
  'valley of elah' => 'Valle de Ela',
  'valley of hinnom' => 'Valle de Hinom',
  'valley of jehoshaphat' => 'Valle de Josafat',
  'valley of siddim' => 'Valle de Sidim',
  'valley of shaveh' => 'Valle de Save',
  'valley of beracah' => 'Valle de Beraca',
  'valley of baca' => 'Valle de Baca',
  'valley of gerar' => 'Valle de Gerar',
  'valley of hebron' => 'Valle de Hebrón',
  'valley of iphtah-el' => 'Valle de Jefte-el',
  'valley of jezreel' => 'Valle de Jezreel',
  'valley of keziz' => 'Valle de Ceziz',
  'valley of lebanon' => 'Valle del Líbano',
  'valley of megiddo' => 'Valle de Meguido',
  'valley of rephaim' => 'Valle de Refaim',
  'valley of salt' => 'Valle de la Sal',
  'valley of sorek' => 'Valle de Sorec',
  'valley of zephathah' => 'Valle de Sefata',
  'valley of vision' => 'Valle de la Visión',
  'valley of the giants' => 'Valle de los Refaim',
  'valley of the jordan' => 'Valle del Jordán',
  'valley of the kings' => 'Valle del Rey',
  'jezreel valley' => 'Valle de Jezreel',
  'king\'s dale' => 'Valle del Rey',
  'king\'s garden' => 'Huerta del Rey',
  'king\'s pool' => 'Estanque del Rey',
  'king\'s highway' => 'Camino Real',
  'king\'s gate' => 'Puerta del Rey',
  // Plains
  'plain of jordan' => 'Llanura del Jordán',
  'plain of mamre' => 'Llanura de Mamre',
  'plain of moreh' => 'Llanura de More',
  'plain of ono' => 'Llanura de Ono',
  'plain of the pillar' => 'Llanura del Pilar',
  'plain of elah' => 'Llanura de Ela',
  'plain of sharon' => 'Llanura de Sarón',
  'sharon plain' => 'Llanura de Sarón',
  // Brooks
  'brook of egypt' => 'Arroyo de Egipto',
  'brook besor' => 'Arroyo Besor',
  'brook cherith' => 'Arroyo de Querit',
  'brook kanah' => 'Arroyo de Caná',
  'brook of willows' => 'Arroyo de los Sauces',
  'brook zered' => 'Arroyo de Zered',
  // Rivers
  'river of egypt' => 'Río de Egipto',
  'river euphrates' => 'Río Éufrates',
  'river jordan' => 'Río Jordán',
  'river kishon' => 'Río Cisón',
  'river chebar' => 'Río Quebar',
  'river ahava' => 'Río Ahava',
  'river abana' => 'Río Abana',
  'river pharpar' => 'Río Farfar',
  'river pishon' => 'Río Pisón',
  'river gihon' => 'Río Gihón',
  'river hiddekel' => 'Río Hidekel',
  'river ulai' => 'Río Ulai',
  'river kanah' => 'Río Caná',
  'river of lebanon' => 'Río del Líbano',
  'great river' => 'Río Grande',
  // Cities
  'city of david' => 'Ciudad de David',
  'city of salt' => 'Ciudad de la Sal',
  'city of palms' => 'Ciudad de las Palmeras',
  'city of moab' => 'Ciudad de Moab',
  'city of refuge' => 'Ciudad de Refugio',
  'city of destruction' => 'Ciudad de la Destrucción',
  'city of the sun' => 'Ciudad del Sol',
  'city of the moon' => 'Ciudad de la Luna',
  'city of the waters' => 'Ciudad de las Aguas',
  'city of the valley' => 'Ciudad del Valle',
  'city of the plain' => 'Ciudad de la Llanura',
  'city of judah' => 'Ciudad de Judá',
  'city of joseph' => 'Ciudad de José',
  'city of nahor' => 'Ciudad de Nacor',
  'city of the book' => 'Ciudad del Libro',
  // Houses
  'house of god' => 'Casa de Dios',
  'house of bondage' => 'Casa de Servidumbre',
  'house of the forest' => 'Casa del Bosque',
  'house of the king' => 'Casa del Rey',
  'house of the mighty' => 'Casa de los Valientes',
  'house of the lord' => 'Casa de Jehová',
  'house of baal' => 'Casa de Baal',
  'house of dagon' => 'Casa de Dagón',
  'house of ashtoreth' => 'Casa de Astarot',
  'house of the gods' => 'Casa de los Dioses',
  'house of the sun' => 'Casa del Sol',
  'house of hogs' => 'Casa de los Cerdos',
  // Springs & wells
  'spring of harod' => 'Fuente de Harod',
  'spring of jezreel' => 'Fuente de Jezreel',
  'spring of nephtoah' => 'Fuente de Neftoa',
  'spring of the waters' => 'Fuente de las Aguas',
  'spring of the sun' => 'Fuente del Sol',
  'well of harod' => 'Pozo de Harod',
  'well of beersheba' => 'Pozo de Beerseba',
  'well of the oath' => 'Pozo del Juramento',
  'well of jacob' => 'Pozo de Jacob',
  'well of living' => 'Pozo del Viviente',
  'well of water' => 'Pozo de Agua',
  'well of the living' => 'Pozo del Viviente',
  'well of sirah' => 'Pozo de Sira',
  // Towers
  'tower of babel' => 'Torre de Babel',
  'tower of edar' => 'Torre de Edar',
  'tower of lebanon' => 'Torre del Líbano',
  'tower of penuel' => 'Torre de Peniel',
  'tower of shechem' => 'Torre de Siquem',
  'tower of the flock' => 'Torre del Rebaño',
  'tower of hananel' => 'Torre de Hananeel',
  'tower of meah' => 'Torre de Meah',
  'tower of the oven' => 'Torre del Horno',
  'tower of the hundred' => 'Torre de Meah',
  // Fields
  'field of blood' => 'Campo de Sangre',
  'field of machpelah' => 'Campo de Macpela',
  'field of abram' => 'Campo de Abram',
  'field of boaz' => 'Campo de Booz',
  'field of the cave' => 'Campo de la Cueva',
  'field of the stranger' => 'Campo del Extranjero',
  'field of giants' => 'Campo de los Gigantes',
  // Gardens
  'garden of eden' => 'Huerta de Edén',
  'garden of god' => 'Huerta de Dios',
  'garden of the king' => 'Huerta del Rey',
  'garden of gethsemane' => 'Huerta de Getsemaní',
  'garden of the lord' => 'Huerta de Jehová',
  // Gates
  'gate of benjamin' => 'Puerta de Benjamín',
  'gate of ephraim' => 'Puerta de Efraín',
  'gate of the valley' => 'Puerta del Valle',
  'gate of the fountain' => 'Puerta de la Fuente',
  'gate of the guard' => 'Puerta de la Guardia',
  'gate of the horses' => 'Puerta de los Caballos',
  'gate of the sheep' => 'Puerta de las Ovejas',
  'gate of the fish' => 'Puerta del Pescado',
  'gate of the water' => 'Puerta del Agua',
  'gate of the dung' => 'Puerta del Muladar',
  'gate of the prison' => 'Puerta de la Cárcel',
  'gate of the corner' => 'Puerta del Rincón',
  'gate of the middle' => 'Puerta de Enmedio',
  'gate of the old' => 'Puerta Vieja',
  'gate of the new' => 'Puerta Nueva',
  'gate of the east' => 'Puerta Oriental',
  'gate of the north' => 'Puerta del Norte',
  'gate of the south' => 'Puerta del Sur',
  'gate of the west' => 'Puerta del Occidente',
  'gate of the inner' => 'Puerta Interior',
  // Camps
  'camp of dan' => 'Campamento de Dan',
  // Trees
  'oak of mamre' => 'Encina de Mamre',
  'oak of moreh' => 'Encina de More',
  'oak of zaanannim' => 'Encina de Zaananim',
  'oak of weeping' => 'Encina del Llanto',
  'oak of the pillar' => 'Encina del Pilar',
  'great tree' => 'Encina Grande',
  'terebinth of moreh' => 'Encina de More',
  'terebinth of zaanannim' => 'Encina de Zaananim',
  'palm of deborah' => 'Palmera de Débora',
  'palm tree of deborah' => 'Palmera de Débora',
  'palm trees of elim' => 'Palmeras de Elim',
  // Fords
  'fords of arnon' => 'Vados del Arnón',
  'fords of jabbok' => 'Vados del Jaboc',
  'fords of jordan' => 'Vados del Jordán',
  'fords of the wilderness' => 'Vados del Desierto',
  // Ascents
  'ascent of adummim' => 'Cuesta de Adumim',
  'ascent of beth-horon' => 'Cuesta de Bet-horón',
  'ascent of the scorpions' => 'Cuesta de los Escorpiones',
  'ascent of ziz' => 'Cuesta de Ziz',
  'ascent of the robbers' => 'Cuesta de los Ladrones',
  'ascent of the sun' => 'Cuesta del Sol',
  // Rocks
  'rock of elam' => 'Roca de Elam',
  'rock of escape' => 'Roca de Escape',
  'rock of etam' => 'Roca de Etam',
  'rock of horeb' => 'Roca de Horeb',
  'rock of israel' => 'Roca de Israel',
  'rock of oreb' => 'Roca de Oreb',
  'rock of the partridge' => 'Roca de la Perdiz',
  'rock of the wild goats' => 'Roca de las Cabras',
  'rock of salvation' => 'Roca de la Salvación',
  'rock of zion' => 'Roca de Sion',
  'rock of offense' => 'Roca de Tropiezo',
  // Caves
  'cave of adullam' => 'Cueva de Adulam',
  'cave of machpelah' => 'Cueva de Macpela',
  'cave of makkedah' => 'Cueva de Maceda',
  'cave of the field' => 'Cueva del Campo',
  'cave of the rock' => 'Cueva de la Roca',
  'cave of the treasure' => 'Cueva del Tesoro',
  // High places
  'high place of baal' => 'Lugar Alto de Baal',
  'high place of the gate' => 'Lugar Alto de la Puerta',
  'high place of the valley' => 'Lugar Alto del Valle',
  'high place of peor' => 'Lugar Alto de Peor',
  'high place of the sun' => 'Lugar Alto del Sol',
  'high places of arnon' => 'Lugares Altos de Arnón',
  'high places of baal' => 'Lugares Altos de Baal',
  'high places of the field' => 'Lugares Altos del Campo',
  // Countries / regions (needed in location data)
  'jerusalem' => 'Jerusalén',
  'galilee' => 'Galilea',
  'judea' => 'Judea',
  'samaria' => 'Samaria',
  'decapolis' => 'Decápolis',
  'mesopotamia' => 'Mesopotamia',
  'cappadocia' => 'Capadocia',
  'bithynia' => 'Bitinia',
  'lycaonia' => 'Licaonia',
  'pamphylia' => 'Panfilia',
  'phrygia' => 'Frigia',
  'cilicia' => 'Cilicia',
  'lydia' => 'Lidia',
  'macedonia' => 'Macedonia',
  'achaia' => 'Acaya',
  'galatia' => 'Galacia',
  'asia' => 'Asia',
  'egypt' => 'Egipto',
  'ethiopia' => 'Etiopía',
  'libya' => 'Libia',
  'cush' => 'Etiopía',
  'put' => 'Put',
  'canaan' => 'Canaán',
  'philistia' => 'Filistea',
  'edom' => 'Edom',
  'moab' => 'Moab',
  'ammon' => 'Amón',
  'aram' => 'Siria',
  'syria' => 'Siria',
  'assyria' => 'Asiria',
  'babylon' => 'Babilonia',
  'chaldea' => 'Caldea',
  'persia' => 'Persia',
  'media' => 'Media',
  'greece' => 'Grecia',
  'cyprus' => 'Chipre',
  'crete' => 'Creta',
  'malta' => 'Malta',
  'italy' => 'Italia',
  'spain' => 'España',
  'arabia' => 'Arabia',
  'sinai' => 'Sinaí',
  'horeb' => 'Horeb',
  'sion' => 'Sion',
  'zion' => 'Sion',
  'palestine' => 'Palestina',
  'idumea' => 'Idumea',
  // Major cities / towns
  'bethel' => 'Betel',
  'penuel' => 'Peniel',
  'paddan-aram' => 'Padán-aram',
  'mizpah' => 'Mizpa',
  'mizpeh' => 'Mizpa',
  'eshtemoa' => 'Estemoa',
  'hebron' => 'Hebrón',
  'kadesh-barnea' => 'Cades-barnea',
  'kadesh' => 'Cades',
  'heliopolis' => 'Heliópolis',
  'pelusium' => 'Pelusio',
  'perez-uzzah' => 'Pérez-uza',
  'balah' => 'Bala',
  'aphek' => 'Afec',
  'jokneam' => 'Jocneam',
  'jokmeam' => 'Jocmeam',
  'michmash' => 'Micmas',
  'michmas' => 'Micmas',
  'ramoth' => 'Ramot',
  'ramath' => 'Ramat',
  'ramah' => 'Ramá',
  'ramoth-gilead' => 'Ramot-galaad',
  'jabesh-gilead' => 'Jabes-galaad',
  'gilead' => 'Galaad',
  'gilgal' => 'Gilgal',
  'gilboa' => 'Gilboa',
  'carmel' => 'Carmelo',
  'tabor' => 'Tabor',
  'hermon' => 'Hermón',
  'pisgah' => 'Pisga',
  'nebo' => 'Nebo',
  'hor' => 'Hor',
  'ebal' => 'Ebal',
  'gerizim' => 'Gerizim',
  'achor' => 'Acor',
  'hinnom' => 'Hinom',
  'rephaim' => 'Refaim',
  'sorek' => 'Sorec',
  'siddim' => 'Sidim',
  'shaveh' => 'Save',
  'beracah' => 'Beraca',
  'baca' => 'Baca',
  'keziz' => 'Ceziz',
  'zephathah' => 'Sefata',
  'iphtah' => 'Jefte',
  'aijalon' => 'Ajalón',
  'elah' => 'Ela',
  'gerar' => 'Gerar',
  'jezreel' => 'Jezreel',
  'sharon' => 'Sarón',
  'moreh' => 'More',
  'mamre' => 'Mamre',
  'ono' => 'Ono',
  'besor' => 'Besor',
  'cherith' => 'Querit',
  'kanah' => 'Caná',
  'zered' => 'Zered',
  'kishon' => 'Cisón',
  'chebar' => 'Quebar',
  'abana' => 'Abana',
  'pharpar' => 'Farfar',
  'pishon' => 'Pisón',
  'gihon' => 'Gihón',
  'hiddekel' => 'Hidekel',
  'ulai' => 'Ulai',
  'euphrates' => 'Éufrates',
  'tigris' => 'Tigris',
  'jordan' => 'Jordán',
  'nile' => 'Nilo',
  'shihor' => 'Sihor',
  'david' => 'David',
  'machpelah' => 'Macpela',
  'adullam' => 'Adulam',
  'makkedah' => 'Maceda',
  'en-rogel' => 'En-rogel',
  'engedi' => 'En-gadi',
  'en-gedi' => 'En-gadi',
  'en-eglaim' => 'En-eglaim',
  'en-rimmon' => 'En-rimón',
  'en-tappuah' => 'En-tapúa',
  'en-gannim' => 'En-ganim',
  'en-haddah' => 'En-hada',
  'en-hazor' => 'En-hazor',
  'en-mishpat' => 'En-mispat',
  'en-shemesh' => 'En-semes',
  'bethlehem' => 'Belén',
  'bethsaida' => 'Betsaida',
  'bethany' => 'Betania',
  'bethel' => 'Betel',
  'beth-horon' => 'Bet-horón',
  'beth-shemesh' => 'Bet-semes',
  'beth-shan' => 'Bet-seán',
  'beth-shean' => 'Bet-seán',
  'beth-peor' => 'Bet-peor',
  'beth-dagon' => 'Bet-dagón',
  'beth-aven' => 'Bet-avén',
  'beth-car' => 'Bet-car',
  'beth-eda' => 'Bet-eda',
  'beth-eker' => 'Bet-equé',
  'beth-emek' => 'Bet-emec',
  'beth-gader' => 'Bet-gader',
  'beth-gamul' => 'Bet-gamul',
  'beth-haccherem' => 'Bet-haquerem',
  'beth-haram' => 'Bet-haram',
  'beth-hoglah' => 'Bet-hogla',
  'beth-jeshimoth' => 'Bet-jesimot',
  'beth-lebaoth' => 'Bet-lebaot',
  'beth-marcaboth' => 'Bet-marcabot',
  'beth-meon' => 'Bet-meón',
  'beth-millo' => 'Bet-milo',
  'beth-nimrah' => 'Bet-nimra',
  'beth-palet' => 'Bet-palet',
  'beth-rapha' => 'Bet-rafa',
  'beth-rehob' => 'Bet-rehob',
  'beth-zur' => 'Bet-sur',
  'bethesda' => 'Betesda',
  'beersheba' => 'Beerseba',
  'beeroth' => 'Beerot',
  'beer-lahai-roi' => 'Beer-lajai-roi',
  'beer-elim' => 'Beer-elim',
  'beeroth-bene-jaakan' => 'Beerot-bene-jaacán',
  'accho' => 'Aco',
  'achshaph' => 'Acsaf',
  'achzib' => 'Aczib',
  'acco' => 'Aco',
  'aceldama' => 'Acéldama',
  'akeldama' => 'Acéldama',
  'akrabbim' => 'Escorpiones',
  'maaleh-akrabbim' => 'Cuesta de los Escorpiones',
  'armageddon' => 'Armagedón',
  'armagedon' => 'Armagedón',
  'har-magedon' => 'Armagedón',
  'arnon' => 'Arnón',
  'aroer' => 'Aroer',
  'ashdod' => 'Asdod',
  'ashkelon' => 'Ascalón',
  'asshur' => 'Asur',
  'assos' => 'Asón',
  'athens' => 'Atenas',
  'attalia' => 'Atalia',
  'baal-hermon' => 'Baal-hermón',
  'baal-meon' => 'Baal-meón',
  'baal-peor' => 'Baal-peor',
  'baal-perazim' => 'Baal-perazim',
  'baal-shalishah' => 'Baal-salisa',
  'baal-tamar' => 'Baal-tamar',
  'baal-zebub' => 'Baal-zebub',
  'baal-zephon' => 'Baal-zefón',
  'babel' => 'Babel',
  'berea' => 'Berea',
  'berothah' => 'Berota',
  'bethabara' => 'Betábara',
  'bozrah' => 'Bosra',
  'caesarea' => 'Cesarea',
  'caesarea philippi' => 'Cesarea de Filipo',
  'capernaum' => 'Capernaúm',
  'capharnaum' => 'Capernaúm',
  'chorazin' => 'Corazín',
  'colosse' => 'Colosas',
  'corinth' => 'Corinto',
  'cos' => 'Cos',
  'cuthah' => 'Cuta',
  'cyrene' => 'Cirene',
  'dalmanutha' => 'Dalmanuta',
  'damascus' => 'Damasco',
  'dan' => 'Dan',
  'debir' => 'Debir',
  'derbe' => 'Derbe',
  'dibon' => 'Dibón',
  'dion' => 'Dión',
  'dophkah' => 'Dofca',
  'dor' => 'Dor',
  'dothan' => 'Dotán',
  'dura' => 'Dura',
  'ecbatana' => 'Ecbatana',
  'elim' => 'Elim',
  'eltekeh' => 'Elteque',
  'emmaus' => 'Emaús',
  'ephesus' => 'Éfeso',
  'ephes-dammim' => 'Efes-damim',
  'ephraim' => 'Efraín',
  'erech' => 'Erec',
  'eshcol' => 'Escol',
  'ezion-geber' => 'Ezion-geber',
  'gath' => 'Gat',
  'gath-hepher' => 'Gat-hefer',
  'gath-rimmon' => 'Gat-rimón',
  'gaza' => 'Gaza',
  'geba' => 'Geba',
  'gebal' => 'Gebal',
  'geder' => 'Geder',
  'gedor' => 'Gedor',
  'genesareth' => 'Genesaret',
  'gennesaret' => 'Genesaret',
  'gerizim' => 'Gerizim',
  'geshur' => 'Gesur',
  'gethsemane' => 'Getsemaní',
  'gezer' => 'Gezer',
  'gibeah' => 'Gabaa',
  'gibeon' => 'Gabaón',
  'giloh' => 'Gilo',
  'golan' => 'Golán',
  'golgotha' => 'Gólgota',
  'goshen' => 'Gosén',
  'gozan' => 'Gozán',
  'habor' => 'Habor',
  'halah' => 'Halah',
  'hamath' => 'Hamat',
  'harosheth' => 'Haroset',
  'havilah' => 'Havila',
  'hazazon-tamar' => 'Hazezon-tamar',
  'hazeroth' => 'Hazerot',
  'hazor' => 'Hazor',
  'helam' => 'Helam',
  'hepher' => 'Hefer',
  'heshbon' => 'Hesbón',
  'hormah' => 'Horma',
  'hukkok' => 'Hucoc',
  'iconium' => 'Iconio',
  'iim' => 'Iim',
  'ijon' => 'Ijón',
  'illyricum' => 'Ilírico',
  'ir' => 'Ir',
  'ir-nahash' => 'Ir-nahas',
  'ir-shemesh' => 'Ir-semes',
  'israel' => 'Israel',
  'jabbok' => 'Jaboc',
  'jabesh' => 'Jabes',
  'jabez' => 'Jabes',
  'jahaz' => 'Jahaza',
  'japho' => 'Jope',
  'jazer' => 'Jazer',
  'jericho' => 'Jericó',
  'jokneam' => 'Jocneam',
  'joppa' => 'Jope',
  'judah' => 'Judá',
  'kedesh' => 'Cedes',
  'kedar' => 'Cedar',
  'kidron' => 'Cedrón',
  'kir' => 'Kir',
  'kir-hareseth' => 'Kir-hares',
  'kir-heres' => 'Kir-hares',
  'kiriath' => 'Quiriat',
  'kiriath-arba' => 'Quiriat-arba',
  'kiriath-baal' => 'Quiriat-baal',
  'kiriath-huzoth' => 'Quiriat-huzot',
  'kiriath-jearim' => 'Quiriat-jearim',
  'kiriath-sannah' => 'Quiriat-sana',
  'kiriath-sepher' => 'Quiriat-sefer',
  'kittim' => 'Quitim',
  'lachish' => 'Laquis',
  'laodicea' => 'Laodicea',
  'lasea' => 'Lasea',
  'lasha' => 'Lasa',
  'lebonah' => 'Lebona',
  'lehi' => 'Lehi',
  'leshem' => 'Lesem',
  'libnah' => 'Libna',
  'lod' => 'Lod',
  'luhith' => 'Luhit',
  'luz' => 'Luz',
  'lystra' => 'Listra',
  'maacah' => 'Maaca',
  'madon' => 'Madón',
  'magadan' => 'Magdala',
  'magdala' => 'Magdala',
  'mahanaim' => 'Mahanaim',
  'makkedah' => 'Maceda',
  'maon' => 'Maón',
  'mareshah' => 'Maresa',
  'maroth' => 'Marot',
  'masrekah' => 'Masreca',
  'megiddo' => 'Meguido',
  'memphis' => 'Menfis',
  'mephaath' => 'Mefaat',
  'meribah' => 'Meriba',
  'merom' => 'Merom',
  'meroz' => 'Meroz',
  'mesha' => 'Mesa',
  'metheg-ammah' => 'Meteg-ama',
  'miletus' => 'Mileto',
  'millo' => 'Milo',
  'mitylene' => 'Mitilene',
  'mizpah' => 'Mizpa',
  'mizpeh' => 'Mizpa',
  'moladah' => 'Molada',
  'moresheth' => 'Moreset',
  'moriah' => 'Moriah',
  'moserah' => 'Mosera',
  'moseroth' => 'Moserot',
  'myra' => 'Mira',
  'mysia' => 'Misia',
  'naamah' => 'Naama',
  'nain' => 'Naín',
  'naioth' => 'Naiot',
  'naphtali' => 'Neftalí',
  'nazareth' => 'Nazaret',
  'neapolis' => 'Neápolis',
  'negeb' => 'Neguev',
  'negev' => 'Neguev',
  'nephtoah' => 'Neftoa',
  'netophah' => 'Netofa',
  'nineveh' => 'Nínive',
  'nob' => 'Nob',
  'nobah' => 'Noba',
  'noph' => 'Nof',
  'on' => 'On',
  'onam' => 'Onam',
  'ophir' => 'Ofir',
  'ophni' => 'Ofni',
  'ophrah' => 'Ofra',
  'padan-aram' => 'Padán-aram',
  'paddan-aram' => 'Padán-aram',
  'paphos' => 'Pafos',
  'paran' => 'Parán',
  'patara' => 'Patara',
  'pathros' => 'Patros',
  'patmos' => 'Patmos',
  'peniel' => 'Peniel',
  'penuel' => 'Peniel',
  'peor' => 'Peor',
  'perga' => 'Pérgamo',
  'pergamum' => 'Pérgamo',
  'pethor' => 'Petor',
  'philadelphia' => 'Filadelfia',
  'philippi' => 'Filipos',
  'pisidia' => 'Pisidia',
  'pithom' => 'Pitón',
  'pontus' => 'Ponto',
  'ptolemais' => 'Tolemaida',
  'puteoli' => 'Puteolos',
  'rabbah' => 'Raba',
  'ramah' => 'Ramá',
  'ramoth-gilead' => 'Ramot-galaad',
  'rehob' => 'Rehob',
  'rehoboth' => 'Rehobot',
  'rephidim' => 'Refidim',
  'ribiah' => 'Ribla',
  'rimmon' => 'Rimón',
  'rome' => 'Roma',
  'salamis' => 'Salamina',
  'salem' => 'Salem',
  'salim' => 'Salim',
  'samos' => 'Samos',
  'samothrace' => 'Samotracia',
  'sardis' => 'Sardis',
  'sarepta' => 'Sarepta',
  'seleucia' => 'Seleucia',
  'shechem' => 'Siquem',
  'shiloh' => 'Silo',
  'shittim' => 'Sitim',
  'shunem' => 'Sunem',
  'shur' => 'Shur',
  'siddim' => 'Sidim',
  'sidon' => 'Sidón',
  'siloam' => 'Siloé',
  'smyrna' => 'Esmirna',
  'sodom' => 'Sodoma',
  'sorek' => 'Sorec',
  'succoth' => 'Sucot',
  'susa' => 'Susa',
  'susa' => 'Susa',
  'sychar' => 'Sicar',
  'syracuse' => 'Siracusa',
  'tarsus' => 'Tarso',
  'tekoa' => 'Tecoa',
  'thessalonica' => 'Tesalónica',
  'thyatira' => 'Tiatira',
  'tiberias' => 'Tiberias',
  'timnah' => 'Timna',
  'timnath' => 'Timnat',
  'tiphasah' => 'Tifsa',
  'tirzah' => 'Tirsa',
  'tishbe' => 'Tisbe',
  'tochen' => 'Tocén',
  'togarmah' => 'Togarma',
  'tolemais' => 'Tolemaida',
  'topheth' => 'Tofet',
  'troas' => 'Troas',
  'trogyllium' => 'Tró gilio',
  'tyre' => 'Tiro',
  'ur' => 'Ur',
  'ur of the chaldees' => 'Ur de los Caldeos',
  'ur salem' => 'Ur Salem',
  'uria' => 'Urías',
  'uzzah' => 'Uza',
  'uzzen-sherah' => 'Uzen-sera',
  'zair' => 'Zair',
  'zalmon' => 'Zalmón',
  'zanchoah' => 'Zancoa',
  'zaphon' => 'Zafón',
  'zarah' => 'Zara',
  'zarathan' => 'Saretán',
  'zareah' => 'Zarea',
  'zared' => 'Zared',
  'zarephath' => 'Sarepta',
  'zaretan' => 'Saretán',
  'zareth-shahar' => 'Zaret-sahar',
  'zeboiim' => 'Zeboiim',
  'zeboim' => 'Zeboim',
  'zelah' => 'Zela',
  'zelzah' => 'Zelza',
  'zemaraim' => 'Zemaraim',
  'zenan' => 'Zenán',
  'zeeb' => 'Zeeb',
  'zephathah' => 'Sefata',
  'zephon' => 'Zefón',
  'zered' => 'Zered',
  'zereda' => 'Zereda',
  'zeredah' => 'Zereda',
  'zerebath-shahar' => 'Zeret-sahar',
  'zererah' => 'Zerera',
  'zereth' => 'Zeret',
  'zeror' => 'Zeror',
  'ziddim' => 'Zidim',
  'zidon' => 'Sidón',
  'zif' => 'Zif',
  'ziha' => 'Ziha',
  'ziklag' => 'Siclag',
  'zillar' => 'Zila',
  'zin' => 'Zin',
  'zion' => 'Sion',
  'zor' => 'Zor',
  'zorah' => 'Zora',
  'zuph' => 'Zuf',
  'zur' => 'Zur',
];

// Pre-compute lowercase keys
$known_lower = [];
foreach ($known_translations as $key => $value) {
  $known_lower[mb_strtolower($key)] = $value;
}

// ── Main ──────────────────────────────────────────────────────────

$posts = $wpdb->get_results("
  SELECT p.ID, p.post_title,
    COALESCE(es.meta_value, '') AS name_es,
    COALESCE(en.meta_value, '') AS name_en,
    COALESCE(s.meta_value, '') AS source
  FROM {$wpdb->posts} p
  LEFT JOIN {$wpdb->postmeta} es ON p.ID = es.post_id AND es.meta_key = '_bc_loc_name_es'
  LEFT JOIN {$wpdb->postmeta} en ON p.ID = en.post_id AND en.meta_key = '_bc_loc_name_en'
  LEFT JOIN {$wpdb->postmeta} s ON p.ID = s.post_id AND s.meta_key = '_bc_loc_source'
  WHERE p.post_type = 'bc_location' AND p.post_status = 'publish'
    AND s.meta_value IN ('gnosis', 'openbible')
    AND en.meta_value IS NOT NULL AND en.meta_value != ''
    AND (es.meta_value IS NULL OR es.meta_value = '' OR es.meta_value = en.meta_value)
  ORDER BY p.post_title
");

echo "Total posts to evaluate: " . count($posts) . "\n";
echo "Mode: " . ($dry_run ? "DRY RUN" : "LIVE") . "\n\n";

$updated_meta = 0;
$updated_full = 0;
$skipped_spanish = 0;
$skipped_duplicate = 0;
$skipped_noop = 0;
$no_match = 0;
$batch = 0;

foreach ($posts as $post) {
  $title = trim($post->post_title);
  $name_en = trim($post->name_en);
  $name_es = trim($post->name_es);

  // ── Case 1: Title already looks Spanish ───────────────────────
  if (looks_spanish($title)) {
    if ($name_es !== $title) {
      if (!$dry_run) {
        update_post_meta($post->ID, '_bc_loc_name_es', $title);
      }
      echo "  META: ID {$post->ID} [{$post->source}] '{$title}' — _bc_loc_name_es ← post_title\n";
      $updated_meta++;
      $batch++;
    } else {
      $skipped_spanish++;
    }
    continue;
  }

  // ── Case 2: Exact match in known_translations ─────────────────
  $lookup_key = normalize_key($title);
  $translated = null;

  if (isset($known_lower[$lookup_key])) {
    $translated = $known_lower[$lookup_key];
  } elseif (!empty($name_en)) {
    $en_key = normalize_key($name_en);
    if (isset($known_lower[$en_key])) {
      $translated = $known_lower[$en_key];
    }
  }

  if ($translated) {
    // Skip if no real change
    if ($translated === $title && $translated === $name_es) {
      $skipped_noop++;
      continue;
    }

    // Check for duplicate post
    $exists = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM {$wpdb->posts}
       WHERE post_type = 'bc_location' AND post_status = 'publish' AND ID != %d
       AND (post_title = %s OR post_name = %s)",
      $post->ID, $translated, sanitize_title($translated)
    ));

    if ($exists > 0) {
      echo "  SKIP: ID {$post->ID} [{$post->source}] '{$title}' → '{$translated}' (duplicate exists)\n";
      $skipped_duplicate++;
      continue;
    }

    // Apply
    $slug = sanitize_title($translated);
    if (!$dry_run) {
      $wpdb->update($wpdb->posts,
        ['post_title' => $translated, 'post_name' => $slug],
        ['ID' => $post->ID]
      );
      update_post_meta($post->ID, '_bc_loc_name_es', $translated);
    }

    echo "  FULL: ID {$post->ID} [{$post->source}] '{$title}' → '{$translated}' (slug: {$slug})\n";
    $updated_full++;
    $batch++;

    // ── Batch progress ──────────────────────────────────────────
    if ($batch > 0 && $batch % 50 === 0) {
      echo "\n--- {$batch} changes so far ---\n\n";
    }
    continue;
  }

  // ── Case 3: No Spanish, no translation match ──────────────────
  // Proper names that are the same in both languages.
  // Set _bc_loc_name_es if empty.
  if (empty($name_es) && !empty($name_en)) {
    if (!$dry_run) {
      update_post_meta($post->ID, '_bc_loc_name_es', $name_en);
    }
    echo "  COPY: ID {$post->ID} [{$post->source}] '{$title}' — _bc_loc_name_es ← _bc_loc_name_en\n";
    $updated_meta++;
  } else {
    $no_match++;
  }
}

echo "\n";
echo "══════════════════════════════════════════\n";
echo "  Updated meta (from post_title): {$updated_meta}\n";
echo "  Updated full (title+slug+meta): {$updated_full}\n";
echo "  Skipped (already Spanish):      {$skipped_spanish}\n";
echo "  Skipped (duplicate exists):     {$skipped_duplicate}\n";
echo "  Skipped (no-op):                {$skipped_noop}\n";
echo "  No match (English proper name): {$no_match}\n";
echo "  Total processed:                " . ($updated_meta + $updated_full + $skipped_spanish + $skipped_duplicate + $skipped_noop + $no_match) . "\n";
echo "══════════════════════════════════════════\n";
