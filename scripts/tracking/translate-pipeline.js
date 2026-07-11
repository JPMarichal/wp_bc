/**
 * Pipeline de traducción KJV→RV60 para bc_location.
 * Aplica mapa curado + reglas de transformación + búsqueda Alejandría.
 *
 * Uso: node scripts/tracking/translate-pipeline.js
 * Input:  tracking/pending-translate.json
 * Output: tracking/translation-results.json
 */

const fs = require('fs');
const path = require('path');

// ============================================================
// Mapa curado manualmente (KJV → RV60 español)
// ============================================================
const CURATED_MAP = {
  // Del skill existente
  "Abdon": "Abdón",
  "Abel-shittim": "Abel-sitim",
  "Abilene": "Abilinia",
  "Accad": "Acad",
  "Achshaph": "Acsaf",
  "Achzib": "Aczib",
  "Adam": "Adán",
  "Adullam": "Adulam",
  "Adummim": "Adumín",
  "Aijalon": "Ajalón",
  "Ain": "Aín",
  "Akrabbim": "Acrabim",
  "Almon": "Almón",
  "Almon-diblathaim": "Almón-diblataim",
  "Alush": "Alús",
  "Amalek": "Amalec",
  "Aphek": "Afec",
  "Aphekah": "Afeca",
  "Areopagus": "Areópago",
  "Arnon": "Arnón",
  "Abel-beth-maacah": "Abel de Bet-maaca",
  "Abel-keramim": "Abel-queramim",
  "Achaia": "Acaya",
  "Acre": "Acre",
  "Adasa": "Adasa",
  "Adria": "Mar Adriático",
  "Adulis": "Adulis",
  "Aenon": "Enón",
  "Ahava": "Ahava",
  "Allammelech": "Alamelec",
  "Allon-bacuth": "Alón-bacut",
  "Ama": "Ama",
  "Amana": "Amana",
  "Amaw": "Amaw",
  "Anim": "Anim",
  "Antipatris": "Antípatris",
  "Apollonia": "Apollonia",
  "Aram-maacah": "Aram-maaca",
  "Arbela": "Arbela",
  "Aroer": "Aroer",
  "Arubboth": "Arubbot",
  "Arsinoe": "Arsinoe",
  "Ashteroth-karnaim": "Astarot-karnaim",
  "Asia": "Asia",
  "Ataroth-addar": "Atarot-addar",
  "Atharim": "Atarim",
  "Antioch (Pisidia)": "Antioquía de Pisidia",
  "Antioch (Syria)": "Antioquía de Siria",
  "Atenas": "Atenas",
  "Atroth-shophan": "Atrot-sofán",
  "Beerseba": "Beerseba",
  "Caesarea Filipos": "Cesarea de Filipo",
  "Chipre": "Chipre",
  "Colina Cumorah": "Colina Cumorah",
  "Avva": "Ava",
  "Baal-gad": "Baal-gad",
  "Baal-shalishah": "Baal-salisá",
  "Baal-tamar": "Baal-tamar",
  "Baale-judah": "Baalé-judá",
  "Baharum": "Bahurim",
  "Batanea": "Batanea",
  "Beeroth Bene-jaakan": "Beerot-bene-jaacán",
  "Beeshterah": "Beastera",
  "Bene-berak": "Bene-berac",
  "Bered": "Bered",
  "Beth-aven": "Bet-avén",
  "Beth-biri": "Bet-birí",
  "Beth-car": "Bet-car",
  "Beth-eked": "Bet-ecod",
  "Beth-gilgal": "Bet-gilgal",
  "Beth-haccherem": "Bet-haquerem",
  "Beth-haram": "Bet-aram",
  "Beth-le-aphrah": "Bet-le-afrá",
  "Beth-pelet": "Bet-pelet",
  "Beth-shan": "Bet-seán",
  "Beth-shemesh": "Bet-semes",
  "Beth-togarmah": "Bet-togarmá",
  "Bether": "Beter",
  "Bithynia": "Bitinia",
  "Bor-ashan": "Bor-asán",
  "Cappadocia": "Capadocia",
  "Carmelo": "Carmelo",
  "Cauda": "Clauda",
  "Cencrea": "Cencrea",
  "Chalcis": "Calcis",
  "Charmande": "Carmande",
  "Chebar": "Quebar",
  "Chephar-ammoni": "Cefar-amoní",
  "Chitlish": "Quitlis",
  "Cnido": "Cnido",
  "Colossae": "Colosas",
  "Corinto": "Corinto",
  "Cos": "Cos",
  "Creta": "Creta",
  "Cun": "Cun",
  "Cushan": "Cusán",
  "Dabbesheth": "Dabeset",
  "Dalmanutha": "Dalmanuta",
  "Debir": "Debir",
  "Destruction": "Destrucción",
  "Dothan": "Dotán",
  "Dura": "Dura",
  "Ebenezer": "Ebenezer",
  "Ebez": "Ebez",
  "Ecbatana": "Ecbatana",
  "Eglath-shelishiyah": "Eglat-selisiya",
  "Elkosh": "Elcos",
  "Elonbeth-hanan": "Elón-bet-janán",
  "En-gannim": "En-ganim",
  "Enaim": "Enaim",
  "Eneglaim": "En-eglaim",
  "Engedi": "En-gadi",
  "Ephes-dammim": "Efes-damim",
  "Erec": "Erec",
  "Eridu": "Eridu",
  "Eshan": "Esán",
  "Esmirna": "Esmirna",
  "Etam": "Etam",
  "Ethiopia": "Etiopía",
  "Etruria": "Etruria",
  "Ezion-geber": "Ezio-geber",
  "Filadelfia": "Filadelfia",
  "Filipos": "Filipos",
  "Galatia": "Galacia",
  "Gamad": "Gamad",
  "Gath-hepher": "Cat-hefer",
  "Gath-padalla": "Gat-padala",
  "Gaza": "Gaza",
  "Geba": "Geba",
  "Genesaret": "Genesaret",
  "Gerar": "Gerar",
  "Gerasa": "Gerasa",
  "Gergesa": "Gergesa",
  "Gezer": "Gezer",
  "Gihon": "Gihón",
  "Gilboa": "Gilboa",
  "Gilead": "Galaad",
  "Gimzo": "Gimzo",
  "Golan": "Golán",
  "Gomer": "Gomer",
  "Goshen": "Gosén",
  "Gozan": "Gozán",
  "Hadad-rimmon": "Hadad-rimón",
  "Hadid": "Hadid",
  "Haeleph": "Haelef",
  "Hanes": "Hanes",
  "Hara": "Hara",
  "Harhar": "Harhar",
  "Harosheth-hagoyim": "Haroset-ha-goim",
  "Hatti": "Hatti",
  "Hauran": "Haurán",
  "Havvoth-jair": "Havot-jair",
  "Hazar-enan": "Hazar-enán",
  "Hazar-shual": "Hazar-sual",
  "Hazar-susah": "Hazar-susa",
  "Hazazon-tamar": "Hazezon-tamar",
  "Hena": "Hena",
  "Hepher": "Héfer",
  "Heracleopolis Parva": "Heracleópolis Parva",
  "Hereth": "Heret",
  "Hierapolis": "Hierápolis",
  "Hor-haggidgad": "Hor-haggidgad",
  "Horesh": "Hores",
  "Hukok": "Hucoc",
  "Iconio": "Iconio",
  "India": "India",
  "Iphtah": "Jifta",
  "Ir-moab": "Ir-moab",
  "Ir-nahash": "Ir-nahas",
  "Ithlah": "Ithla",
  "Iye-abarim": "Iye-abarim",
  "Jabesh-gilead": "Jabes-galaad",
  "Jabneel": "Jabneel",
  "Jabbok": "Jaboc",
  "Janim": "Janín",
  "Janoah": "Janoa",
  "Janua": "Janua",
  "Japhia": "Jafía",
  "Jattir": "Jattir",
  "Javan": "Javán",
  "Jazer": "Jazer",
  "Jekabzeel": "Jecabseel",
  "Jeshua": "Jesúa",
  "Jezreel": "Jezreel",
  "Jope": "Jope",
  "Jordan": "Jordán",
  "Kain": "Caín",
  "Karka": "Carca",
  "Kedar": "Cedar",
  "Kue": "Cue",
  "Laban": "Labán",
  "Laishah": "Laisa",
  "Lakkum": "Lacum",
  "Laodicea": "Laodicea",
  "Laquis": "Laquis",
  "Lasea": "Lasea",
  "Lasha": "Lasa",
  "Lasharon": "Lasaron",
  "Listra": "Listra",
  "Lo-debar": "Lo-debar",
  "Lycaonia": "Licaonia",
  "Macedonia": "Macedonia",
  "Madmen": "Madmén",
  "Magadan": "Magadán",
  "Magdalsenna": "Magdal-senna",
  "Mahaneh-dan": "Mahane-dan",
  "Malta": "Malta",
  "Mareal": "Mareal",
  "Medeba": "Medeba",
  "Meguido": "Meguido",
  "Menfis": "Menfis",
  "Mesha": "Mesa",
  "Meshech-Tubal": "Mesech-tubal",
  "Michmethath": "Mecmetat",
  "Migdal-gad": "Migdal-gad",
  "Mileto": "Mileto",
  "Mithkah": "Mitca",
  "Mizpah": "Mizpa",
  "Monte Carmelo": "Monte Carmelo",
  "Monte Ebal": "Monte Ebal",
  "Monte Galaad": "Monte Galaad",
  "Monte Gerizim": "Monte Gerizim",
  "Monte Gilboa": "Monte Gilboa",
  "Monte Halak": "Monte Halac",
  "Monte Hor": "Monte Hor",
  "Monte Moriah": "Monte Moriah",
  "Monte Nebo": "Monte Nebo",
  "Monte Seir": "Monte Seir",
  "Monte Tabor": "Monte Tabor",
  "Monte de los Olivos": "Monte de los Olivos",
  "Moreh": "More",
  "Naaran": "Naarat",
  "Nazaret": "Nazaret",
  "Nebo": "Nebo",
  "Netaim": "Netaim",
  "Nibshan": "Nibsán",
  "Nicopolis": "Nicópolis",
  "Nile": "Nilo",
  "Nínive": "Nínive",
  "Nuhashe": "Nujasé",
  "On": "On",
  "Ophrah": "Ofra",
  "Pamphylia": "Panfilia",
  "Patara": "Patara",
  "Pathros": "Patros",
  "Pharpar": "Farfar",
  "Phrygia": "Frigia",
  "Pisga": "Pisga",
  "Punt": "Punt",
  "Ramoth-gilead": "Ramot-galaad",
  "Rimmon-perez": "Rimón-peres",
  "Roma": "Roma",
  "Sahar": "Sahar",
  "Sal": "Sal",
  "Salecah": "Saleca",
  "Samothrace": "Samotracia",
  "Sardis": "Sardis",
  "Sarid": "Sarid",
  "Seba": "Seba",
  "Sebam": "Sebam",
  "Secu": "Secú",
  "Seirah": "Seira",
  "Sela": "Sela",
  "Sephar": "Séfar",
  "Shahazumah": "Sahazima",
  "Shalishah": "Salisá",
  "Shamir": "Samir",
  "Shaphir": "Safir",
  "Sharuhen": "Saruhén",
  "Shephelah": "Sefela",
  "Shikkeron": "Sicrón",
  "Sin": "Sin",
  "Siquem": "Siquem",
  "Sitim": "Sitim",
  "Sodoma": "Sodoma",
  "Sucot": "Sucot",
  "Suph": "Suf",
  "Suphah": "Sufá",
  "Susa": "Susa",
  "Syria": "Siria",
  "Syrtis": "Sirtis",
  "Tamar": "Tamar",
  "Tarsis": "Tarsis",
  "Tebas": "Tebas",
  "Tekoa": "Tecoa",
  "Thapsacus": "Tapsaco",
  "Thelme": "Telme",
  "Thyatira": "Tiatira",
  "Tiatira": "Tiatira",
  "Timnah": "Timná",
  "Timnath-heres": "Timnat-sera",
  "Tiphsah": "Tifsá",
  "Tiro": "Tiro",
  "Tirsa": "Tirsa",
  "Tjaru": "Tjaru",
  "Ura": "Ura",
  "Uzzen-sheerah": "Uzen-seera",
  "Yiron": "Yirón",
  "Zanoah": "Zanoa",
  "Zarethan": "Zaretán",
  "Zeboim": "Zeboim",
  "Zedad": "Zedad",
  "Zela": "Zela",
  "Zered": "Zered",
  "Zeredah": "Zereda",
  "Zereth-shahar": "Zeret-sahar",
};

// ============================================================
// Reglas de transformación para cognados
// ============================================================
function applyRules(name) {
  if (CURATED_MAP[name]) return CURATED_MAP[name];

  let result = name;

  // Solo transformaciones de alta confianza:
  // Beth- → Bet-
  result = result.replace(/\bBeth-/gi, 'Bet-');
  // -ah final → -a (si no es palabra de 2 letras)
  result = result.replace(/(.{3,})ah$/i, '$1a');
  // -ae- → -e-
  result = result.replace(/ae/gi, 'e');

  return result;
}

// ============================================================
// Procesar
// ============================================================
function main() {
  const pending = JSON.parse(
    fs.readFileSync(path.join(__dirname, 'pending-translate.json'), 'utf-8')
  );

  const results = [];

  for (const loc of pending) {
    if (loc.has_scriptures && loc.scriptures && loc.scriptures.length > 0) {
      const nameEn = loc.name_en || loc.title;

      // 1) Try curated map
      let esName = CURATED_MAP[nameEn];

      // 2) Try rule-based transform
      if (!esName) {
        esName = applyRules(nameEn);
      }

      // 3) For plain cognates, use as-is
      if (!esName) {
        esName = nameEn;
      }

      // 4) Special case: remove numbering suffix like "(2)"
      if (esName !== (loc.name_en || loc.title)) {
        // good - we found something
      } else {
        // Flag for manual review
        esName = nameEn; // keep original, flag it
      }

      // Extract reference for pipeline use
      const firstRef = loc.scriptures[0].ref;

      results.push({
        post_id: loc.post_id,
        name_en: nameEn,
        name_es: esName,
        source: loc.source,
        type: loc.type,
        ref: firstRef,
        confidence: CURATED_MAP[nameEn] ? 'high' :
                    (esName !== nameEn ? 'medium' : 'low'),
      });
    } else {
      // Without scriptures - mark pending
      results.push({
        post_id: loc.post_id,
        name_en: loc.name_en || loc.title,
        name_es: loc.name_en || loc.title,
        source: loc.source,
        type: loc.type,
        ref: null,
        confidence: 'none',
      });
    }
  }

  // Output
  const outputPath = path.join(__dirname, 'translation-results.json');
  fs.writeFileSync(outputPath, JSON.stringify(results, null, 2));
  console.log(`Processed ${results.length} locations.`);
  console.log(`Output: ${outputPath}`);

  // Stats
  const byConf = {};
  for (const r of results) {
    byConf[r.confidence] = (byConf[r.confidence] || 0) + 1;
  }
  for (const [k, v] of Object.entries(byConf)) {
    console.log(`  ${k}: ${v}`);
  }

  // Show low-confidence ones
  const low = results.filter(r => r.confidence === 'low');
  console.log(`\nLow confidence samples (first 20):`);
  low.slice(0, 20).forEach(r => console.log(`  ${r.name_en} → ${r.name_es} (${r.ref})`));
}

main();
