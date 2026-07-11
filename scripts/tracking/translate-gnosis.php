<?php
/**
 * Aplica traducción name_es a gnosis entries donde name_es == name_en.
 * Usa mapa curado KJV→RV60 + reglas de transformación.
 * Uso: docker exec wp_bc_cli wp eval-file scripts/tracking/translate-gnosis.php --allow-root
 */

// Mapa curado KJV→RV60 (del pipeline original)
$CURATED_MAP = [
  "Abdon" => "Abdón", "Abel-shittim" => "Abel-sitim", "Abilene" => "Abilinia",
  "Accad" => "Acad", "Achshaph" => "Acsaf", "Achzib" => "Aczib",
  "Adam" => "Adán", "Adullam" => "Adulam", "Adummim" => "Adumín",
  "Aijalon" => "Ajalón", "Ain" => "Aín", "Akrabbim" => "Acrabim",
  "Almon" => "Almón", "Almon-diblathaim" => "Almón-diblataim",
  "Alush" => "Alús", "Amalek" => "Amalec", "Aphek" => "Afec",
  "Aphekah" => "Afeca", "Areopagus" => "Areópago", "Arnon" => "Arnón",
  "Abel-beth-maacah" => "Abel de Bet-maaca", "Abel-keramim" => "Abel-queramim",
  "Achaia" => "Acaya", "Aenon" => "Enón", "Allammelech" => "Alamelec",
  "Allon-bacuth" => "Alón-bacut", "Antipatris" => "Antípatris",
  "Aram-maacah" => "Aram-maaca", "Aroer" => "Aroer",
  "Arubboth" => "Arubbot", "Ashteroth-karnaim" => "Astarot-karnaim",
  "Ataroth-addar" => "Atarot-addar", "Atroth-shophan" => "Atrot-sofán",
  "Avva" => "Ava", "Baal-gad" => "Baal-gad", "Baal-shalishah" => "Baal-salisá",
  "Baal-tamar" => "Baal-tamar", "Baale-judah" => "Baalé-judá",
  "Baharum" => "Bahurim", "Beeroth Bene-jaakan" => "Beerot-bene-jaacán",
  "Beeshterah" => "Beastera", "Bene-berak" => "Bene-berac",
  "Beth-aven" => "Bet-avén", "Beth-biri" => "Bet-birí",
  "Beth-car" => "Bet-car", "Beth-eked" => "Bet-ecod",
  "Beth-gilgal" => "Bet-gilgal", "Beth-haccherem" => "Bet-haquerem",
  "Beth-haram" => "Bet-aram", "Beth-le-aphrah" => "Bet-le-afrá",
  "Beth-pelet" => "Bet-pelet", "Beth-shan" => "Bet-seán",
  "Beth-shemesh" => "Bet-semes", "Beth-togarmah" => "Bet-togarmá",
  "Bether" => "Beter", "Bithynia" => "Bitinia", "Bor-ashan" => "Bor-asán",
  "Cappadocia" => "Capadocia", "Cauda" => "Clauda",
  "Chebar" => "Quebar", "Chephar-ammoni" => "Cefar-amoní",
  "Chitlish" => "Quitlis", "Colossae" => "Colosas",
  "Cushan" => "Cusán", "Dabbesheth" => "Dabeset",
  "Dalmanutha" => "Dalmanuta", "Destruction" => "Destrucción",
  "Dothan" => "Dotán", "Eglath-shelishiyah" => "Eglat-selisiya",
  "Elkosh" => "Elcos", "Elonbeth-hanan" => "Elón-bet-janán",
  "En-gannim" => "En-ganim", "Eneglaim" => "En-eglaim",
  "Engedi" => "En-gadi", "Ephes-dammim" => "Efes-damim",
  "Eshan" => "Esán", "Ethiopia" => "Etiopía",
  "Ezion-geber" => "Ezio-geber", "Galatia" => "Galacia",
  "Gath-hepher" => "Cat-hefer", "Gath-padalla" => "Gat-padala",
  "Gilead" => "Galaad", "Golan" => "Golán", "Goshen" => "Gosén",
  "Gozan" => "Gozán", "Hadad-rimmon" => "Hadad-rimón",
  "Haeleph" => "Haelef", "Harosheth-hagoyim" => "Haroset-ha-goim",
  "Hauran" => "Haurán", "Havvoth-jair" => "Havot-jair",
  "Hazar-enan" => "Hazar-enán", "Hazar-shual" => "Hazar-sual",
  "Hazar-susah" => "Hazar-susa", "Hazazon-tamar" => "Hazezon-tamar",
  "Hepher" => "Héfer", "Heracleopolis Parva" => "Heracleópolis Parva",
  "Hierapolis" => "Hierápolis", "Horesh" => "Hores",
  "Hukok" => "Hucoc", "Iphtah" => "Jifta",
  "Ir-nahash" => "Ir-nahas", "Jabesh-gilead" => "Jabes-galaad",
  "Jabbok" => "Jaboc", "Janoah" => "Janoa",
  "Japhia" => "Jafía", "Javan" => "Javán",
  "Jekabzeel" => "Jecabseel", "Jeshua" => "Jesúa",
  "Kain" => "Caín", "Karka" => "Carca", "Kedar" => "Cedar",
  "Kue" => "Cue", "Laban" => "Labán", "Laishah" => "Laisa",
  "Lakkum" => "Lacum", "Lasaron" => "Lasaron",
  "Lo-debar" => "Lo-debar", "Lycaonia" => "Licaonia",
  "Madmen" => "Madmén", "Magadan" => "Magadán",
  "Magdalsenna" => "Magdal-senna", "Mahaneh-dan" => "Mahane-dan",
  "Michmethath" => "Mecmetat", "Migdal-gad" => "Migdal-gad",
  "Mithkah" => "Mitca", "Monte Halak" => "Monte Halac",
  "Moreh" => "More", "Naaran" => "Naarat",
  "Nibshan" => "Nibsán", "Nicopolis" => "Nicópolis",
  "Nuhashe" => "Nujasé", "Ophrah" => "Ofra",
  "Pamphylia" => "Panfilia", "Pathros" => "Patros",
  "Pharpar" => "Farfar", "Phrygia" => "Frigia",
  "Ramoth-gilead" => "Ramot-galaad", "Rimmon-perez" => "Rimón-peres",
  "Salecah" => "Saleca", "Samothrace" => "Samotracia",
  "Shahazumah" => "Sahazima", "Shalishah" => "Salisá",
  "Shamir" => "Samir", "Shaphir" => "Safir",
  "Sharuhen" => "Saruhén", "Shephelah" => "Sefela",
  "Shikkeron" => "Sicrón", "Suph" => "Suf",
  "Suphah" => "Sufá", "Thapsacus" => "Tapsaco",
  "Thelme" => "Telme", "Timnath-heres" => "Timnat-sera",
  "Tiphsah" => "Tifsá", "Uzzen-sheerah" => "Uzen-seera",
  "Yiron" => "Yirón", "Zanoah" => "Zanoa",
  "Zarethan" => "Zaretán", "Zereth-shahar" => "Zeret-sahar",
  "Zered" => "Zered", "Zeredah" => "Zereda",
  "Pisidia" => "Pisidia", "Lycia" => "Licia",
  "Cilicia" => "Cilicia", "Nazareth" => "Nazaret",
  "Ophir" => "Ofir", "Oboth" => "Obot",
  "Pithom" => "Pitom", "Raamah" => "Raama",
  "Ramah" => "Ramá", "Ramoth" => "Ramot",
  "Rissah" => "Rissa", "Rumah" => "Ruma",
  "Rimmon" => "Rimón", "Sepharvaim" => "Sefarvaim",
  "Shaaraim" => "Saaraim", "Shihor" => "Sihor",
  "Shur" => "Sur", "Sibmah" => "Sibma",
  "Sinai" => "Sinaí", "Sitnah" => "Sitna",
  "Succoth" => "Sucot", "Tirzah" => "Tirsa",
  "Tibhath" => "Tibhat", "Trogyllium" => "Trogilio",
  "Zalmonah" => "Zalmona", "Zeredah" => "Zereda",
  "Moseroth" => "Moserot", "Naarah" => "Naara",
  "Naioth" => "Naiot", "Neah" => "Nea",
  "Maacah" => "Maaca", "Maroth" => "Marot",
  "Mashal" => "Masal", "Mesha" => "Mesa",
  "Caphtor" => "Caftor", "Chalcis" => "Calcis",
  "Charmande" => "Carmande", "Chorazin" => "Corazín",
  "Cush" => "Cus", "Cyprus" => "Chipre",
  "Decapolis" => "Decápolis", "Eridu" => "Eridú",
  "Etruria" => "Etruria", "Gergesa" => "Gergesa",
  "Gerasa" => "Gerasa", "Gomorrah" => "Gomorra",
  "Gudgodah" => "Gudgoda", "Hadrach" => "Hadrac",
  "Hahiroth" => "Hahirot", "Hapharaim" => "Hafaraim",
  "Hatti" => "Hatti", "Hauran" => "Haurán",
  "Havilah" => "Havila", "Helbah" => "Helba",
  "Hormah" => "Horma", "Javan" => "Javán",
  "Jeshua" => "Jesúa", "Geliloth" => "Gelilot",
  "Gederoth" => "Gederot", "Gaash" => "Gaas",
  "Gibeah" => "Gabaa", "Iim" => "Iim",
  "Iphedeiah" => "Ifdeías", "Irpeel" => "Irpeel",
  "Ish-tob" => "Is-tob", "Jattir" => "Jattir",
  "Jazer" => "Jazer", "Jehud" => "Jehud",
  "Jezreel" => "Jezreel", "Kadesh" => "Cades",
  "Kedemoth" => "Cedemot", "Kenath" => "Cenat",
  "Kir" => "Cir", "Kir-hareseth" => "Cir-hares",
  "Lasha" => "Lasa", "Lasea" => "Lasea",
  "Luz" => "Luz", "Madon" => "Madón",
  "Mahanaim" => "Mahanaim", "Mareshah" => "Maresa",
  "Mephaath" => "Mefaat", "Middin" => "Midín",
  "Misrephoth-maim" => "Misrefot-maim",
  "Mizpah" => "Mizpa", "Mount Seir" => "Monte Seir",
  "Nacon" => "Nacón", "Nahalal" => "Nahalal",
  "Negeb" => "Néguev", "Nephish" => "Nefis",
  "Nephthali" => "Neftalí", "Noph" => "Nof",
  "Paddan" => "Padán", "Penuel" => "Peniel",
  "Pirathon" => "Piratón", "Pochereth" => "Poqueret",
  "Punon" => "Punón", "Rakkath" => "Racat",
  "Rehob" => "Rehob", "Salmon" => "Salmón",
  "Salmone" => "Salmón", "Sansannah" => "Sansana",
  "Sechu" => "Secú", "Sepulchre" => "Sepulcro",
  "Shaalabbin" => "Saalabín", "Shaalbim" => "Saalbín",
  "Shepho" => "Sefo", "Shihor-libnath" => "Sihor-libnat",
  "Shimon" => "Simón", "Shittim" => "Sitim",
  "Shoa" => "Sóa", "Shunem" => "Sunem",
  "Sibbechai" => "Sibecai", "Succoth" => "Sucot",
  "Tappuah" => "Tapúa", "Tilon" => "Tilón",
  "Tob" => "Tob", "Toi" => "Toi",
  "Troas" => "Troas", "Tubal" => "Tubal",
  "Uphaz" => "Ufaz", "Ur" => "Ur",
  "Uzal" => "Uzal", "Vashti" => "Vasti",
  "Zalmon" => "Zalmón", "Zarephath" => "Sarepta",
  "Zareth-shahar" => "Zeret-sahar", "Zeeb" => "Zeeb",
  "Zemaraim" => "Zemaraim", "Ziddim" => "Zidín",
  "Ziklag" => "Siclag", "Zilthai" => "Ziltai",
  "Zin" => "Zin", "Zior" => "Zior",
  "Ziph" => "Zif", "Ziphron" => "Zifrón",
  "Zobah" => "Soba", "Zorah" => "Zora",
  "Midian" => "Madián", "Moab" => "Moab",
  "Antioch" => "Antioquía", "Artaxata" => "Artaxata",
  "Ashdod" => "Asdod", "Ashkelon" => "Ascalón",
  "Assos" => "Asós", "Ataroth" => "Atarot",
  "Baalath-beer" => "Baalat-beer", "Beeroth" => "Beerot",
  "Berea" => "Berea", "Besor" => "Besor",
  "Beth-anoth" => "Bet-anot", "Beth-arbel" => "Bet-arbel",
  "Ekron" => "Ecrón", "Elath" => "Elat",
  "Eltekeh" => "Elteque", "Ephraim" => "Efraín",
  "Eshcol" => "Escol", "Essa" => "Esa",
  "Euphrates" => "Éufrates", "Ezel" => "Ezel",
  "Gadara" => "Gádara", "Gath" => "Gat",
  "Gaza" => "Gaza", "Geba" => "Gueba",
  "Gilead" => "Galaad", "Gilgal" => "Gilgal",
  "Gimzo" => "Gimzo", "Habor" => "Habor",
  "Halhul" => "Halhul", "Hali" => "Hali",
  "Hamath" => "Hamath", "Hammon" => "Hamón",
  "Hanathon" => "Hanatón", "Harran" => "Harán",
  "Hazarhatticon" => "Hazar-haticón",
  "head of" => "cabeza de", "Heliopolis" => "Heliópolis",
  "Herodium" => "Herodión", "Heshbon" => "Hesbón",
  "Hinnom" => "Hinom", "Hippos" => "Hipos",
  "Hobah" => "Hoba", "Horem" => "Horem",
  "Horeb" => "Horeb", "Hukkok" => "Hucoc",
  "Hukok" => "Hucoc", "Humble" => "Humble",
  "Idalah" => "Idala", "Ijon" => "Ijon",
  "Iphiah" => "Ifías", "Jabneel" => "Jabneel",
  "Jahaz" => "Jahaza", "Jahzah" => "Jahza",
  "Janoah" => "Janoa", "Jattir" => "Jattir",
  "Jazer" => "Jazer", "Jehoshaphat" => "Josafat",
  "Jeruel" => "Jeruel", "Jeshimon" => "Jesimón",
  "Jeshua" => "Jesúa", "Jogli" => "Jogli",
  "Jokmeam" => "Jocmeam", "Jokneam" => "Jocneam",
  "Joppa" => "Jope", "Juttah" => "Juta",
  "Kabzeel" => "Cabseel", "Kadesh-barnea" => "Cades-barnea",
  "Kamon" => "Camón", "Kanah" => "Caná",
  "Karkor" => "Carcor", "Kartah" => "Carta",
  "Kartan" => "Cartán", "Kattath" => "Catat",
  "Kedemoth" => "Cedemot", "Keturah" => "Cetura",
  "Kibroth-hattaavah" => "Kibrot-hataava",
  "Kiriath-jearim" => "Quiriat-jearim",
  "Kishi" => "Quisi", "Kitron" => "Quitrón",
  "Kittim" => "Quitim", "Koa" => "Coa",
  "Labbah" => "Laba", "Lachish" => "Laquis",
  "Lahad" => "Lahad", "Lahmam" => "Lahmam",
  "Lapidoth" => "Lapidot", "Lebanon" => "Líbano",
  "Lebaoth" => "Lebaot", "Lebo-hamath" => "Entrada de Hamat",
  "Lebonah" => "Lebona", "Lehi" => "Lehi",
  "Leshem" => "Lesem", "Libnah" => "Libna",
  "Lithostrotos" => "Lithostrotos",
  "Lod" => "Lod", "Luhith" => "Luhit",
  "Maarath" => "Maarat", "Machbenah" => "Macbena",
  "Machir" => "Maquir", "Madmannah" => "Madmana",
  "Magdala" => "Magdala", "Magdiel" => "Magdiel",
  "Makkedah" => "Maqueda", "Mamre" => "Mamre",
  "Manahath" => "Manahat", "Manasseh" => "Manasés",
  "Mandrakes" => "Mandrágoras",
  "Maon" => "Maón", "Marah" => "Mara",
  "Mareshah" => "Maresa", "Masrekah" => "Masreca",
  "Massa" => "Masa", "Mattanah" => "Matana",
  "Meah" => "Mea", "Mearah" => "Meara",
  "Medeba" => "Medeba", "Megiddo" => "Meguido",
  "Melita" => "Malta", "Memphis" => "Menfis",
  "Meribah" => "Meriba", "Merom" => "Merom",
  "Meshech" => "Mesech", "Mesopotamia" => "Mesopotamia",
  "Metheg-ammah" => "Metheg-amma",
  "Midian" => "Madián", "Migdal" => "Migdal",
  "Migdal-el" => "Migdal-el", "Migdol" => "Migdol",
  "Mignon" => "Migrón", "Miletus" => "Mileto",
  "Millo" => "Milo", "Minni" => "Mini",
  "Mishal" => "Misal", "Mizpeh" => "Mizpa",
  "Mizraim" => "Mizraim", "Moresheth-gath" => "Moreset-gat",
  "Moriah" => "Moriah", "Mozah" => "Moza",
  "Naamah" => "Naama", "Naaman" => "Naamán",
  "Nahaliel" => "Nahaliel", "Nahallal" => "Nahalal",
  "Nahor" => "Nahor", "Nain" => "Naín", "Naioth" => "Naiot",
  "Naphoth-dor" => "Nafot-dor", "Nazareth" => "Nazaret",
  "Nazoreo" => "Nazareno", "Neah" => "Nea",
  "Neballat" => "Nebalat", "Nebi Samwil" => "Nebi Samwil",
  "Nebo" => "Nebo", "Necho" => "Necao",
  "Negeb" => "Néguev", "Nehelam" => "Nehelam",
  "Neiel" => "Neiel", "Nekeb" => "Nequeb",
  "Nephish" => "Nefis", "Nephtoah" => "Neftoa",
  "Nereus" => "Nereo", "Netaim" => "Netaim",
  "Netophah" => "Netofa", "Nezib" => "Nezib",
  "Nibhaz" => "Nibhaz", "Nibshan" => "Nibsán",
  "Nile" => "Nilo", "Nimrah" => "Nimra",
  "Nimrod" => "Nimrod", "Nimshi" => "Nimsi",
  "Nineveh" => "Nínive", "Nippur" => "Nippur",
  "Nisibis" => "Nisibis", "Nob" => "Nob",
  "Nobah" => "Noba", "Nod" => "Nod",
  "Noph" => "Nof", "Nophah" => "Nofa",
  "Nun" => "Nun",
];

// Reglas de transformación
function applyRules($name) {
    global $CURATED_MAP;
    if (isset($CURATED_MAP[$name])) return $CURATED_MAP[$name];
    
    $result = $name;
    $result = preg_replace('/\bBeth-/i', 'Bet-', $result);
    $result = preg_replace('/(.{3,})ah\b/i', '$1a', $result);
    $result = preg_replace('/ae/i', 'e', $result);
    
    return $result;
}

// Procesar
$posts = get_posts(array(
    'post_type' => 'bc_location',
    'post_status' => 'publish',
    'posts_per_page' => -1,
));

$updated = 0;
$skipped = 0;
$by_confidence = ['high' => 0, 'medium' => 0, 'low' => 0];

foreach ($posts as $p) {
    $name_es = get_post_meta($p->ID, '_bc_loc_name_es', true);
    $name_en = get_post_meta($p->ID, '_bc_loc_name_en', true);
    $source = get_post_meta($p->ID, '_bc_loc_source', true);
    
    if ($source !== 'gnosis') {
        $skipped++;
        continue;
    }
    if ($name_es !== $name_en) {
        // Already has different name_es
        $skipped++;
        continue;
    }
    
    $translated = applyRules($name_en);
    
    if ($translated !== $name_en) {
        $confidence = isset($CURATED_MAP[$name_en]) ? 'high' : 'medium';
        $by_confidence[$confidence]++;
    } else {
        $by_confidence['low']++;
    }
    
    update_post_meta($p->ID, '_bc_loc_name_es', $translated);
    $updated++;
    
    if ($updated <= 10 || $updated % 100 === 0) {
        echo "  ID {$p->ID}: \"{$name_en}\" → \"{$translated}\"\n";
    }
}

echo "\n--- Translation complete ---\n";
echo "Updated: $updated\n";
echo "Skipped: $skipped\n";
echo "High confidence (curated): {$by_confidence['high']}\n";
echo "Medium (rules applied): {$by_confidence['medium']}\n";
echo "Low (kept original): {$by_confidence['low']}\n";
