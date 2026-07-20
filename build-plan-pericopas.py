#!/usr/bin/env python
"""Build plan-pericopas-dyc.md from churchofjesuschrist.org + refinement.

Pipeline:
  1. Scrape each D&C section from churchofjesuschrist.org
  2. Extract official pericope breakdown (p.study-summary) via BeautifulSoup
  3. Apply refinements per mapeo-pericopas.md (granularity, atomicidad, correlación)
  4. Output refined plan to docs/plan-pericopas-dyc.md
"""

import requests, warnings, re, sys, os, json
from bs4 import BeautifulSoup

warnings.filterwarnings("ignore")
try:
    sys.stdout.reconfigure(encoding="utf-8")
except Exception:
    pass

HEADERS = {"User-Agent": "Mozilla/5.0"}
OUTPUT = os.path.join(
    os.path.dirname(__file__), "docs", "juego-del-cinco", "plan-pericopas-dyc.md"
)

# ── Refined pericopes for sections 1-20 (from manual curaduría: juego-del-cinco) ──
# Each entry: list of {title, slug, v_start, v_end, evento, notas}
# Titles: natural Spanish, active voice, human-readable
# Slugs: SEO-friendly, short, keyword-dense, no stop words

OD_REFINED = {
    "od1": [
        (
            "El Manifiesto: cesa el matrimonio plural",
            "dyc-od1-manifiesto-cese-matrimonio-plural",
            1,
            3,
            "",
            "Declaración formal del presidente Wilford Woodruff",
        ),
    ],
    "od2": [
        (
            "Se otorga el sacerdocio a todos los varones dignos",
            "dyc-od2-sacerdocio-todos-varones-dignos",
            1,
            2,
            "",
            "Revelación a Spencer W. Kimball, 1 de junio de 1978",
        ),
    ],
}

MANUAL_REFINED_1_20 = {
    1: [
        (
            "La voz de amonestación se dirige a todo pueblo",
            "dyc-1-voz-amonestacion",
            1,
            7,
            "",
            "",
        ),
        (
            "Apostasía y maldad preceden a la Segunda Venida",
            "dyc-1-apostasia-maldad-segunda-venida",
            8,
            16,
            "",
            "",
        ),
        (
            "José Smith es llamado a restaurar el Evangelio",
            "dyc-1-jose-smith-restaurar-evangelio",
            17,
            23,
            "",
            "",
        ),
        (
            "Sale a luz el Libro de Mormón y nace la Iglesia",
            "dyc-1-libro-mormon-iglesia-verdadera",
            24,
            33,
            "",
            "",
        ),
        ("La paz será quitada de la tierra", "dyc-1-paz-quitada", 34, 36, "", ""),
        (
            "Escudriñad estos mandamientos",
            "dyc-1-escudrinad-mandamientos",
            37,
            39,
            "",
            "",
        ),
    ],
    2: [
        (
            "Elías el Profeta restaurará el Sacerdocio",
            "dyc-2-elias-restaura-sacerdocio",
            1,
            1,
            "elias-promesa",
            "Mal 4:5-6; JS—H 1:36-39",
        ),
        (
            "Promesas a los padres echarán raíz en el corazón de los hijos",
            "dyc-2-promesas-padres-corazon-hijos",
            2,
            3,
            "elias-promesa",
            "",
        ),
    ],
    3: [
        (
            "La vía del Señor es un giro eterno",
            "dyc-3-via-senor-giro-eterno",
            1,
            4,
            "",
            "",
        ),
        (
            "José debe arrepentirse o perderá el don de traducir",
            "dyc-3-jose-arrepentirse-perdera-don",
            5,
            15,
            "",
            "",
        ),
        (
            "El Libro de Mormón salvará a la posteridad de Lehi",
            "dyc-3-libro-mormon-salvara-lehi",
            16,
            20,
            "",
            "",
        ),
    ],
    4: [
        (
            "Quien sirve a Dios fielmente se salva",
            "dyc-4-servicio-fiel-salvacion",
            1,
            4,
            "",
            "",
        ),
        (
            "Atributos divinos califican para el ministerio",
            "dyc-4-atributos-divinos-ministerio",
            5,
            6,
            "",
            "",
        ),
        ("Procurad las cosas de Dios", "dyc-4-procurad-cosas-dios", 7, 7, "", ""),
    ],
    5: [
        (
            "La palabra del Señor viene por José Smith",
            "dyc-5-palabra-senor-jose-smith",
            1,
            10,
            "",
            "",
        ),
        (
            "Tres testigos darán testimonio del Libro de Mormón",
            "dyc-5-tres-testigos-testimonio",
            11,
            18,
            "",
            "",
        ),
        (
            "La palabra del Señor se cumplirá",
            "dyc-5-palabra-senor-cumplira",
            19,
            20,
            "",
            "",
        ),
        (
            "Martin Harris debe humillarse para ver",
            "dyc-5-martin-harris-humillarse",
            21,
            28,
            "",
            "Encabezado oficial unifica 21-35; se divide en v. 29 por cambio de tema",
        ),
        (
            "Martin Harris será testigo si es fiel",
            "dyc-5-martin-harris-testigo-fiel",
            29,
            35,
            "",
            "",
        ),
    ],
    6: [
        (
            "Los obreros del Señor logran la salvación",
            "dyc-6-obreros-senor-salvacion",
            1,
            6,
            "",
            "",
        ),
        (
            "No hay don más grande que la salvación",
            "dyc-6-don-mas-grande-salvacion",
            7,
            13,
            "",
            "",
        ),
        (
            "Oliver Cowdery ha recibido instrucción del Espíritu",
            "dyc-6-oliver-instruccion-espiritu",
            14,
            18,
            "",
            "Encabezado oficial unifica 14-27; se divide en tres",
        ),
        (
            "Oliver tiene un testimonio de Cristo",
            "dyc-6-oliver-testimonio-cristo",
            19,
            24,
            "",
            "",
        ),
        (
            "Oliver recibirá el don de traducir",
            "dyc-6-oliver-don-traducir",
            25,
            27,
            "",
            "",
        ),
        (
            "Cristo promete estar con los fieles",
            "dyc-6-cristo-fieles",
            28,
            37,
            "",
            "",
        ),
    ],
    7: [
        (
            "Juan el Amado vivirá hasta la venida del Señor",
            "dyc-7-juan-amado-vivira",
            1,
            3,
            "juan-el-amado",
            "Jn 21:20-24",
        ),
        (
            "Pedro, Santiago y Juan poseen las llaves del ministerio",
            "dyc-7-pedro-santiago-juan-llaves",
            4,
            8,
            "juan-el-amado",
            "",
        ),
    ],
    8: [
        (
            "La revelación viene por el Espíritu Santo",
            "dyc-8-revelacion-espiritu-santo",
            1,
            5,
            "",
            "",
        ),
        (
            "Los misterios de Dios se conocen por la fe",
            "dyc-8-misterios-fe",
            6,
            12,
            "",
            "",
        ),
    ],
    9: [
        (
            "Oliver Cowdery traducirá otros anales antiguos",
            "dyc-9-anales-antiguos-traducidos",
            1,
            6,
            "",
            "",
        ),
        (
            "Cómo recibir revelación: estudiar y sentir confirmación del Espíritu",
            "dyc-9-estudio-confirmacion-espiritual",
            7,
            14,
            "",
            "",
        ),
    ],
    10: [
        (
            "El don de traducir es restaurado",
            "dyc-10-don-traducir-restaurado",
            1,
            5,
            "",
            "Encabezado oficial unifica 1-26; se divide en tres",
        ),
        (
            "Los inicuos alteraron las palabras de los anales",
            "dyc-10-inicuos-alteraron-palabras",
            6,
            13,
            "",
            "",
        ),
        (
            "El Señor confundirá el plan de Satanás",
            "dyc-10-senor-confunde-satanas",
            14,
            26,
            "",
            "",
        ),
        (
            "Satanás procura destruir las almas",
            "dyc-10-satanas-destruye-almas",
            27,
            33,
            "",
            "",
        ),
        (
            "José Smith traducirá las planchas de Nefi",
            "dyc-10-traduce-planchas-nefi",
            34,
            42,
            "",
            "Encabezado oficial unifica 34-52; se divide en dos",
        ),
        (
            "El Evangelio llegará a los lamanitas",
            "dyc-10-evangelio-lamanitas",
            43,
            52,
            "",
            "",
        ),
        (
            "Cristo establece Su Iglesia",
            "dyc-10-cristo-establece-iglesia",
            53,
            63,
            "",
            "",
        ),
        (
            "Los arrepentidos serán recogidos",
            "dyc-10-arrepentidos-recogidos",
            64,
            70,
            "",
            "",
        ),
    ],
    11: [
        (
            "Los obreros del Señor logran la salvación",
            "dyc-11-obreros-salvacion",
            1,
            6,
            "",
            "",
        ),
        (
            "Hyrum debe buscar sabiduría y proclamar el arrepentimiento",
            "dyc-11-busca-sabiduria-arrepentimiento",
            7,
            14,
            "",
            "",
        ),
        (
            "Guarda los mandamientos y estudia la palabra",
            "dyc-11-guarda-mandamientos-palabra",
            15,
            22,
            "",
            "",
        ),
        (
            "No niegues la revelación ni la profecía",
            "dyc-11-no-niegues-revelacion-profecia",
            23,
            27,
            "",
            "",
        ),
        (
            "Quien recibe a Cristo es hijo de Dios",
            "dyc-11-recibir-cristo-hijos-dios",
            28,
            30,
            "",
            "",
        ),
    ],
    12: [
        (
            "Los obreros del Señor logran la salvación",
            "dyc-12-obreros-salvacion",
            1,
            6,
            "",
            "",
        ),
        (
            "Todos pueden ayudar en la obra del Señor",
            "dyc-12-todos-ayudar-obra-senor",
            7,
            9,
            "",
            "",
        ),
    ],
    13: [
        (
            "Se restauran las llaves y el poder del Sacerdocio Aarónico",
            "dyc-13-restauran-llaves-sacerdocio-aaronico",
            1,
            1,
            "restauracion-sacerdocio-aaronico",
            "JS—H 1:68-72",
        ),
    ],
    14: [
        (
            "Los obreros del Señor logran la salvación",
            "dyc-14-obreros-salvacion",
            1,
            6,
            "",
            "",
        ),
        (
            "La vida eterna es el mayor de los dones",
            "dyc-14-vida-eterna-mayor-don",
            7,
            8,
            "",
            "",
        ),
        (
            "Cristo creó los cielos y la tierra",
            "dyc-14-cristo-creo-cielos-tierra",
            9,
            11,
            "",
            "",
        ),
    ],
    15: [
        (
            "El brazo del Señor cubre la tierra",
            "dyc-15-brazo-senor-cubre-tierra",
            1,
            2,
            "",
            "",
        ),
        (
            "Predicar y salvar almas es lo más valioso",
            "dyc-15-predicar-salvar-almas",
            3,
            6,
            "",
            "",
        ),
    ],
    16: [
        (
            "El brazo del Señor cubre la tierra",
            "dyc-16-brazo-senor-cubre-tierra",
            1,
            2,
            "",
            "",
        ),
        (
            "Predicar y salvar almas es lo más valioso",
            "dyc-16-predicar-salvar-almas",
            3,
            6,
            "",
            "",
        ),
    ],
    17: [
        (
            "Los Tres Testigos verán las planchas y el ángel",
            "dyc-17-tres-testigos-veran-planchas",
            1,
            4,
            "testigos-del-libro-de-mormon",
            "Éter 5:2-4",
        ),
        (
            "Cristo mismo testifica del Libro de Mormón",
            "dyc-17-cristo-testifica-libro-mormon",
            5,
            9,
            "testigos-del-libro-de-mormon",
            "",
        ),
    ],
    18: [
        (
            "Las Escrituras enseñan a edificar la Iglesia",
            "dyc-18-escrituras-edificar-iglesia",
            1,
            5,
            "",
            "",
        ),
        ("El mundo madura en iniquidad", "dyc-18-mundo-iniquidad", 6, 8, "", ""),
        (
            "El valor de las almas es grande ante Dios",
            "dyc-18-valor-almas-grande",
            9,
            16,
            "",
            "",
        ),
        (
            "Tomad sobre vosotros el nombre de Cristo",
            "dyc-18-tomar-nombre-cristo",
            17,
            25,
            "",
            "",
        ),
        (
            "El llamamiento y la misión de los Doce Apóstoles",
            "dyc-18-llamamiento-doce-apostoles",
            26,
            36,
            "",
            "",
        ),
        (
            "Oliver Cowdery y David Whitmer buscarán a los Doce",
            "dyc-18-oliver-david-buscan-doce",
            37,
            39,
            "",
            "",
        ),
        ("Arrepentíos y bautizaos", "dyc-18-arrepentios-bautizaos", 40, 47, "", ""),
    ],
    19: [
        (
            "Cristo posee todo poder en el cielo y en la tierra",
            "dyc-19-cristo-todo-poder",
            1,
            3,
            "",
            "",
        ),
        (
            "Todos deben arrepentirse o padecer",
            "dyc-19-arrepentirse-padecer",
            4,
            5,
            "",
            "",
        ),
        (
            "El castigo eterno es el castigo de Dios",
            "dyc-19-castigo-eterno-dios",
            6,
            12,
            "",
            "",
        ),
        (
            "Cristo padeció para que nosotros no tengamos que padecer",
            "dyc-19-cristo-padecio",
            13,
            20,
            "",
            "",
        ),
        (
            "Predicad el arrepentimiento a todo el mundo",
            "dyc-19-predicad-arrepentimiento",
            21,
            28,
            "",
            "",
        ),
        (
            "Declarad las buenas nuevas a toda criatura",
            "dyc-19-declarad-buenas-nuevas",
            29,
            41,
            "",
            "",
        ),
    ],
    20: [
        (
            "El Libro de Mormón prueba la divinidad de la obra",
            "dyc-20-libro-mormon-prueba-divinidad",
            1,
            16,
            "",
            "",
        ),
        (
            "La Creación, la Caída y la Expiación",
            "dyc-20-creacion-caida-expiacion",
            17,
            28,
            "",
            "",
        ),
        (
            "Arrepentimiento, justificación y bautismo",
            "dyc-20-arrepentimiento-justificacion-bautismo",
            29,
            37,
            "",
            "",
        ),
        (
            "Deberes de los élderes y apóstoles",
            "dyc-20-deberes-elderes-apostoles",
            38,
            44,
            "",
            "Encabezado oficial unifica 38-67; se divide en tres",
        ),
        ("Deberes de los presbíteros", "dyc-20-deberes-presbiteros", 45, 52, "", ""),
        (
            "Deberes de los maestros y diáconos",
            "dyc-20-deberes-maestros-diaconos",
            53,
            60,
            "",
            "",
        ),
        (
            "Conferencias y ordenaciones",
            "dyc-20-conferencias-ordenaciones",
            61,
            67,
            "",
            "",
        ),
        (
            "Deberes de los miembros y la forma de bautizar",
            "dyc-20-deberes-miembros-bautismo",
            68,
            74,
            "",
            "",
        ),
        ("Oraciones de la Santa Cena", "dyc-20-oraciones-santa-cena", 75, 84, "", ""),
    ],
}

# ── Manual refinements for key sections 21-138 ──
# These override auto-generation for sections where official headers
# need significant rework per mapeo-pericopas.md criteria.

MANUAL_REFINED_21_138 = {
    76: [
        (
            "El Señor es Dios",
            "dyc-76-senor-es-dios",
            1,
            4,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Los misterios del reino serán revelados a los fieles",
            "dyc-76-misterios-reino-fieles",
            5,
            10,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Dos resurrecciones: la de los justos y la de los injustos",
            "dyc-76-dos-resurrecciones",
            11,
            17,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Cristo redime a los habitantes de muchos mundos",
            "dyc-76-cristo-redime-muchos-mundos",
            18,
            24,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "La caída de Lucifer, hijo de la mañana",
            "dyc-76-caida-lucifer",
            25,
            29,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Los hijos de perdición",
            "dyc-76-hijos-perdicion",
            30,
            43,
            "vision-de-los-tres-reinos-de-gloria",
            "20 v. — considerar subdivisión",
        ),
        (
            "Un grado de salvación para quienes niegan la verdad",
            "dyc-76-salvacion-niegan-verdad",
            44,
            49,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "La gloria del reino celestial",
            "dyc-76-gloria-celestial",
            50,
            70,
            "vision-de-los-tres-reinos-de-gloria",
            "21 v. — considerar subdivisión",
        ),
        (
            "Los que heredan el reino terrenal",
            "dyc-76-reino-terrenal",
            71,
            80,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Los que heredan el reino telestial",
            "dyc-76-reino-telestial",
            81,
            95,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Los que no reciben el Evangelio ni el testimonio de Jesús",
            "dyc-76-no-reciben-evangelio",
            96,
            106,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Tres glorias comparadas",
            "dyc-76-tres-glorias",
            107,
            113,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
        (
            "Los fieles verán los misterios por el Espíritu",
            "dyc-76-misterios-espiritu",
            114,
            119,
            "vision-de-los-tres-reinos-de-gloria",
            "",
        ),
    ],
    84: [
        (
            "Nueva Jerusalén y templo en Misuri",
            "dyc-84-nueva-jerusalen-misuri",
            1,
            5,
            "sacerdocio",
            "",
        ),
        (
            "La línea del sacerdocio: de Moisés a Adán",
            "dyc-84-linea-sacerdocio-moises-adan",
            6,
            17,
            "sacerdocio",
            "",
        ),
        (
            "El sacerdocio mayor revela a Dios",
            "dyc-84-sacerdocio-mayor",
            18,
            25,
            "sacerdocio",
            "",
        ),
        (
            "El sacerdocio menor prepara el camino",
            "dyc-84-sacerdocio-menor-prepara",
            26,
            32,
            "sacerdocio",
            "",
        ),
        (
            "El juramento y convenio del sacerdocio",
            "dyc-84-juramento-convenio-sacerdocio",
            33,
            44,
            "sacerdocio",
            "",
        ),
        (
            "El Espíritu de Cristo ilumina a todo hombre",
            "dyc-84-espiritu-cristo-ilumina",
            45,
            53,
            "sacerdocio",
            "",
        ),
        (
            "Los santos deben testificar de la verdad recibida",
            "dyc-84-santos-testificar",
            54,
            61,
            "sacerdocio",
            "",
        ),
        (
            "Predicad el Evangelio; las señales seguirán",
            "dyc-84-predicad-evangelio-senales",
            62,
            76,
            "sacerdocio",
            "15 v. — considerar subdivisión",
        ),
        (
            "Salid sin bolsa ni alforja",
            "dyc-84-salid-sin-bolsa",
            77,
            91,
            "sacerdocio",
            "15 v. — considerar subdivisión",
        ),
        (
            "Plagas para quienes rechacen el Evangelio",
            "dyc-84-plagas-rechazan-evangelio",
            92,
            97,
            "sacerdocio",
            "",
        ),
        ("El nuevo cántico de Sion", "dyc-84-cantico-sion", 98, 102, "sacerdocio", ""),
        (
            "Cada cual en su propio oficio",
            "dyc-84-cada-cual-oficio",
            103,
            110,
            "sacerdocio",
            "",
        ),
        (
            "Proclamad la abominación desoladora",
            "dyc-84-abominacion-desoladora",
            111,
            120,
            "sacerdocio",
            "",
        ),
    ],
    88: [
        (
            "El Consolador promete vida eterna",
            "dyc-88-consolador-vida-eterna",
            1,
            5,
            "",
            "",
        ),
        ("La Luz de Cristo gobierna toda cosa", "dyc-88-luz-cristo", 6, 13, "", ""),
        (
            "La Resurrección viene por la Redención",
            "dyc-88-resurreccion-redencion",
            14,
            16,
            "",
            "",
        ),
        (
            "La ley prepara para el reino de gloria",
            "dyc-88-ley-prepara-reino",
            17,
            31,
            "",
            "15 v. — considerar subdivisión",
        ),
        ("El pecado inmundo será quitado", "dyc-88-pecado-inmundo", 32, 35, "", ""),
        ("Todo reino se rige por ley", "dyc-88-reino-rige-ley", 36, 41, "", ""),
        (
            "Dios ha dado ley a todas las cosas",
            "dyc-88-dios-ley-todas-cosas",
            42,
            45,
            "",
            "",
        ),
        (
            "El hombre llegará a comprender a Dios",
            "dyc-88-hombre-comprendera-dios",
            46,
            50,
            "",
            "",
        ),
        (
            "Parábola del hombre que visita a sus siervos",
            "dyc-88-parabola-hombre-siervos",
            51,
            61,
            "",
            "",
        ),
        ("Allegaos al Señor y ved Su faz", "dyc-88-allegaos-senor", 62, 73, "", ""),
        (
            "Santificaos y enseñaos la doctrina",
            "dyc-88-santificaos-doctrina",
            74,
            80,
            "",
            "",
        ),
        ("Amonestad a vuestro prójimo", "dyc-88-amonestad-projimo", 81, 85, "", ""),
        (
            "Señales y ángeles preceden la venida del Señor",
            "dyc-88-senales-venida",
            86,
            94,
            "",
            "",
        ),
        (
            "Trompetas angelicales llaman a los muertos",
            "dyc-88-trompetas-muertos",
            95,
            102,
            "",
            "",
        ),
        (
            "Restauración, caída de Babilonia y batalla final",
            "dyc-88-restauracion-babilonia-batalla",
            103,
            116,
            "",
            "14 v. — considerar subdivisión",
        ),
        (
            "Buscad conocimiento y vestíos de caridad",
            "dyc-88-buscad-conocimiento-caridad",
            117,
            126,
            "",
            "",
        ),
        (
            "La Escuela de los Profetas y el lavamiento de pies",
            "dyc-88-escuela-profetas",
            127,
            141,
            "",
            "15 v. — considerar subdivisión",
        ),
    ],
    89: [
        (
            "Prohibido el alcohol, el tabaco y las bebidas calientes",
            "dyc-89-prohibido-alcohol-tabaco",
            1,
            9,
            "",
            "",
        ),
        (
            "Hierbas, frutas, carne y grano para el uso del hombre",
            "dyc-89-alimentos-santos",
            10,
            17,
            "",
            "",
        ),
        (
            "Bendiciones para quienes guardan la Palabra de Sabiduría",
            "dyc-89-bendiciones-palabra-sabiduria",
            18,
            21,
            "",
            "",
        ),
    ],
    93: [
        ("Los fieles verán al Señor", "dyc-93-fieles-veran-senor", 1, 5, "", ""),
        (
            "Juan testifica: Cristo recibió gracia sobre gracia",
            "dyc-93-juan-testifica-cristo",
            6,
            18,
            "",
            "13 v. — considerar subdivisión",
        ),
        (
            "De gracia en gracia hasta recibir Su plenitud",
            "dyc-93-gracia-plenitud",
            19,
            20,
            "",
            "",
        ),
        ("La Iglesia del Primogénito", "dyc-93-iglesia-primogenito", 21, 22, "", ""),
        (
            "Cristo recibió la plenitud de la verdad",
            "dyc-93-cristo-plenitud-verdad",
            23,
            28,
            "",
            "",
        ),
        (
            "El hombre fue con Dios desde el principio",
            "dyc-93-hombre-dios-principio",
            29,
            32,
            "",
            "",
        ),
        ("Los elementos son eternos", "dyc-93-elementos-eternos", 33, 35, "", ""),
        (
            "La gloria de Dios es la inteligencia",
            "dyc-93-gloria-dios-inteligencia",
            36,
            37,
            "",
            "",
        ),
        (
            "Los niños son inocentes por la redención de Cristo",
            "dyc-93-ninos-inocentes",
            38,
            40,
            "",
            "",
        ),
        (
            "Los líderes deben poner en orden sus familias",
            "dyc-93-lideres-familias",
            41,
            53,
            "",
            "",
        ),
    ],
    121: [
        (
            "El Profeta clama por los santos que padecen",
            "dyc-121-profeta-clama-santos",
            1,
            6,
            "",
            "",
        ),
        ("El Señor le habla paz al alma", "dyc-121-senor-habla-paz", 7, 10, "", ""),
        (
            "Malditos quienes acusan falsamente a los santos",
            "dyc-121-malditos-falsos-acusadores",
            11,
            17,
            "",
            "",
        ),
        (
            "Los falsos acusadores perderán el sacerdocio",
            "dyc-121-falsos-acusadores-sacerdocio",
            18,
            25,
            "",
            "",
        ),
        (
            "Revelaciones gloriosas para quienes perseveran",
            "dyc-121-revelaciones-perseverantes",
            26,
            32,
            "",
            "",
        ),
        (
            "Muchos son llamados, pocos los escogidos",
            "dyc-121-muchos-llamados-pocos-escogidos",
            33,
            40,
            "",
            "",
        ),
        (
            "El sacerdocio solo debe ejercerse con rectitud",
            "dyc-121-sacerdocio-rectitud",
            41,
            46,
            "",
            "",
        ),
    ],
    132: [
        (
            "La exaltación viene por el nuevo y sempiterno convenio",
            "dyc-132-exaltacion-nuevo-convenio",
            1,
            6,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "Las condiciones del nuevo y sempiterno convenio",
            "dyc-132-condiciones-convenio",
            7,
            14,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "El matrimonio celestial y la continuación de la familia",
            "dyc-132-matrimonio-celestial",
            15,
            20,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "El camino estrecho que lleva a la vida eterna",
            "dyc-132-camino-estrecho-vida-eterna",
            21,
            25,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "La blasfemia contra el Espíritu Santo",
            "dyc-132-blasfemia-espiritu-santo",
            26,
            27,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "Promesas de exaltación a los profetas de todas las edades",
            "dyc-132-promesas-profetas",
            28,
            39,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "José Smith recibe poder para sellar en la tierra y en el cielo",
            "dyc-132-poder-sellar",
            40,
            47,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "El Señor sella la exaltación de José",
            "dyc-132-sella-exaltacion-jose",
            48,
            50,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "Emma Smith es llamada a la fidelidad",
            "dyc-132-emma-fidelidad",
            51,
            57,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
        (
            "Las leyes del matrimonio plural",
            "dyc-132-matrimonio-plural",
            58,
            66,
            "nuevo-y-sempiterno-convenio",
            "",
        ),
    ],
    133: [
        (
            "Preparaos para la Segunda Venida",
            "dyc-133-preparaos-segunda-venida",
            1,
            6,
            "",
            "",
        ),
        ("Huid de Babilonia, id a Sion", "dyc-133-huid-babilonia-sion", 7, 16, "", ""),
        (
            "Cristo vendrá y las tribus perdidas volverán",
            "dyc-133-cristo-viene-tribus-vuelven",
            17,
            35,
            "",
            "19 v. — considerar subdivisión",
        ),
        (
            "El Evangelio restaurado será predicado en todo el mundo",
            "dyc-133-evangelio-restaurado",
            36,
            40,
            "",
            "",
        ),
        (
            "El Señor vendrá con venganza sobre los inicuos",
            "dyc-133-senor-venganza-inicuos",
            41,
            51,
            "",
            "",
        ),
        ("El año de los redimidos", "dyc-133-ano-redimidos", 52, 56, "", ""),
        (
            "El Evangelio: salvación para los santos, destrucción para los inicuos",
            "dyc-133-evangelio-salvacion",
            57,
            74,
            "",
            "18 v. — considerar subdivisión",
        ),
    ],
    136: [
        (
            "Organización del Campamento de Israel para el viaje al oeste",
            "dyc-136-campamento-israel",
            1,
            16,
            "",
            "16 v. — considerar subdivisión",
        ),
        (
            "Los santos deben vivir las normas del Evangelio",
            "dyc-136-normas-evangelio",
            17,
            27,
            "",
            "",
        ),
        (
            "Cantad, bailad, orad y aprended sabiduría",
            "dyc-136-cantad-orad-sabiduria",
            28,
            33,
            "",
            "",
        ),
        (
            "Los profetas mueren para honra de los justos",
            "dyc-136-profetas-mueren",
            34,
            42,
            "",
            "",
        ),
    ],
    138: [
        (
            "José F. Smith medita sobre la visita de Cristo al mundo de los espíritus",
            "dyc-138-smith-medita-visita-cristo",
            1,
            10,
            "",
            "",
        ),
        (
            "Cristo visita a los justos en el paraíso",
            "dyc-138-cristo-visita-paraiso",
            11,
            24,
            "",
            "14 v. — considerar subdivisión",
        ),
        (
            "La predicación del Evangelio entre los espíritus",
            "dyc-138-predicacion-espiritus",
            25,
            37,
            "",
            "13 v. — considerar subdivisión",
        ),
        (
            "Adán, Eva y los profetas en el mundo de los espíritus",
            "dyc-138-adan-eva-espiritus",
            38,
            52,
            "",
            "15 v. — considerar subdivisión",
        ),
        (
            "Los muertos justos de esta dispensación continúan su obra",
            "dyc-138-muertos-justos-obra",
            53,
            60,
            "",
            "",
        ),
    ],
}

# ── Known _evento_canonico assignments for D&C ──
EVENTOS_CANONICO = {
    2: "elias-promesa",
    7: "juan-el-amado",
    13: "restauracion-sacerdocio-aaronico",
    17: "testigos-del-libro-de-mormon",
    27: "elias-promesa",
    76: "vision-de-los-tres-reinos-de-gloria",
    77: "preguntas-sobre-apocalipsis",
    84: "sacerdocio",
    107: "orden-patriarcal",
    110: "restauracion-llaves",
    124: "templo-de-nauvoo",
    128: "bautismo-por-los-muertos",
    132: "nuevo-y-sempiterno-convenio",
    137: "vision-de-los-tres-reinos-de-gloria",
}


def slugify(text):
    """Normalize to URL-safe slug."""
    s = text.lower().strip()
    s = (
        s.replace("ñ", "n")
        .replace("á", "a")
        .replace("é", "e")
        .replace("í", "i")
        .replace("ó", "o")
        .replace("ú", "u")
    )
    s = (
        s.replace("ü", "u")
        .replace("ä", "a")
        .replace("ë", "e")
        .replace("ö", "o")
        .replace("ï", "i")
    )
    s = s.replace("–", "-").replace("—", "-").replace("/", "-")
    s = re.sub(r"[^a-z0-9-]", "-", s)
    s = re.sub(r"-+", "-", s)
    s = s.strip("-")
    return s[:60]


STOP_WORDS = {
    "de",
    "del",
    "la",
    "las",
    "lo",
    "los",
    "el",
    "en",
    "por",
    "para",
    "con",
    "que",
    "a",
    "e",
    "y",
    "al",
    "su",
    "un",
    "una",
    "se",
    "le",
    "les",
    "sus",
    "entre",
    "sobre",
    "tras",
    "sin",
    "como",
    "mas",
    "pero",
}


def seo_slug(text, section_num, prefix="dyc"):
    """SEO-friendly slug: short, keyword-dense, no stop words."""
    s = slugify(text.replace("–", "-").replace("—", "-"))
    words = s.split("-")
    words = [w for w in words if w and w not in STOP_WORDS]
    slug = "-".join(words)
    slug = re.sub(r"-+", "-", slug).strip("-")
    full = f"{prefix}-{section_num}-{slug}"
    return full[:60]


def naturalize_title(desc):
    """Transform official header into natural, active, human-readable Spanish title.

    Per mapeo-pericopas.md:
    - Active voice, human-readable, short
    - Describes action/scene, not just actor
    - No meta-framing (no 'esta revelación', 'dichos principios')
    - Standalone without context
    """
    t = desc.strip().rstrip(".")
    stage2_done = False

    # Proper capitalization: uppercase first letter only, preserve original case
    def cap(s):
        return s[0].upper() + s[1:] if s and len(s) > 0 else (s.upper() if s else "")

    # ── Stage 1: Remove meta-framing phrases ──
    meta = [
        r"que se (?:mencionan|encuentran|dan a conocer) en esta revelación",
        r"mediante lo cual se (?:daría a conocer|darían a conocer) [^,;]+",
        r"(?:de|por) dich[oa] (?:convenio|promesa|mandamiento|principio)",
        r"que se (?:indican|establecen|declaran) en (?:esta|dicha) (?:revelación|sección)",
    ]
    for p in meta:
        t = re.sub(p, "", t, flags=re.IGNORECASE).strip()
        t = re.sub(r"\s+,", ",", t).strip()

    # ── Stage 2: Remove passive/official-header framing ──
    # Order matters: most specific patterns first.
    # Each branch sets t and stage2_done = True instead of returning.

    # Name patterns: 1 word for common nouns, 1-2 for proper names
    NAME2 = r"(\w+(?:\s+\w+)?)"  # 1-2 words for person names

    # "Se manda al [pers] que [verb]" → "[Pers] [verb]" (instruction, no "debe")
    if not stage2_done:
        pm = re.match(r"^Se manda al " + NAME2 + r" que (\w+)", t, re.IGNORECASE)
        if pm:
            pers = pm.group(1).strip().capitalize()
            acc = pm.group(2).strip()
            rest = t[pm.end() :].strip()
            action = f"{acc} {rest}" if rest else acc
            t = f"{pers} {action}"
            stage2_done = True

    # "Se manda al [pers] que se [verb]" → "[Pers] [verb]se" (instruction)
    if not stage2_done:
        pm = re.match(r"^Se manda al (\w+) que se (\w+)", t, re.IGNORECASE)
        if pm:
            pers = pm.group(1).strip().capitalize()
            acc = pm.group(2).strip()
            t = f"{pers} {acc}se"
            stage2_done = True

    # "Se manda a (los|las) [group] [verb]" → "[Group] deben [verb]"
    if not stage2_done:
        pm = re.match(r"^Se manda a (los|las) (\w+) (\w+)", t, re.IGNORECASE)
        if pm:
            grupo = pm.group(2).strip()
            acc = pm.group(3).strip()
            rest = t[pm.end() :].strip()
            action = f"{acc} {rest}" if rest else acc
            t = f"{cap(grupo)} deben {action}"
            # Reorder "no": "Santos deben no matar" → "Santos no deben matar"
            t = re.sub(r"\bdeben no ", "no deben ", t)
            stage2_done = True

    # "Se manda a [pers] que se [verb]" → "[Pers] debe [verb]se"
    if not stage2_done:
        pm = re.match(r"^Se manda a " + NAME2 + r" que se (\w+)", t, re.IGNORECASE)
        if pm:
            pers = pm.group(1).strip().capitalize()
            acc = pm.group(2).strip()
            t = f"{pers} debe {acc}se"
            stage2_done = True

    # "Se manda a [pers] [verb]" → "[Pers] debe [verb]"
    if not stage2_done:
        pm = re.match(r"^Se manda a " + NAME2 + r" (\w+)", t, re.IGNORECASE)
        if pm:
            pers = pm.group(1).strip().capitalize()
            acc = pm.group(2).strip()
            rest = t[pm.end() :].strip()
            action = f"{acc} {rest}" if rest else acc
            t = f"{pers} debe {action}"
            t = re.sub(r"\bdebe no ", "no debe ", t)
            stage2_done = True

    # "Se le manda a [pers] [verb]" → "[Pers] debe [verb]"
    if not stage2_done:
        pm = re.match(r"^Se le manda a " + NAME2 + r" (\w+)", t, re.IGNORECASE)
        if pm:
            pers = pm.group(1).strip().capitalize()
            acc = pm.group(2).strip()
            rest = t[pm.end() :].strip()
            action = f"{acc} {rest}" if rest else acc
            t = f"{pers} debe {action}"
            t = re.sub(r"\bdebe no ", "no debe ", t)
            stage2_done = True

    # Pattern-based stripping (only if no specific pattern matched above)
    if not stage2_done:
        # "Se les/le manda [acc]" → extract core action
        t = re.sub(r"^Se (?:les|le) manda ", "", t)
        t = re.sub(r"^Se manda ", "", t)
        t = re.sub(r"^Se declaran ", "", t)
        t = re.sub(r"^Se declara ", "", t)
        t = re.sub(r"^Se revelan ", "", t)
        t = re.sub(r"^Se revela ", "", t)
        t = re.sub(r"^Se indican ", "", t)
        t = re.sub(r"^Se indica ", "", t)
        t = re.sub(r"^Se dan a conocer ", "", t)
        t = re.sub(r"^Se da a conocer ", "", t)
        t = re.sub(r"^Se enumeran ", "", t)
        t = re.sub(r"^Se condenan ", "", t)
        t = re.sub(r"^Se explica ", "", t)
        t = re.sub(r"^Se confirma ", "", t)
        t = re.sub(r"^Se predicen ", "", t)
        t = re.sub(r"^Se predice ", "", t)
        t = re.sub(r"^Se convoca a (los|las) (\w+)", r"\2 convocados", t)
        pm = re.match(r"^Se reprende a (\w+) (.+)", t, re.IGNORECASE)
        if pm:
            pers = pm.group(1).strip().capitalize()
            resto = pm.group(2).strip().lstrip(",").strip()
            t = f"{pers} es reprendido {resto}"
        t = re.sub(r"^Se dan ", "", t)
        t = re.sub(r"^Se da ", "", t)
        t = re.sub(r"^Se \w+ ", "", t)
        t = re.sub(r"^También es ", "Es ", t)
        t = re.sub(r"^También se ", "Se ", t)
        t = re.sub(r"^También ", "", t)

    # ── Stage 3: Style improvements ──
    t = re.sub(r"^Los que ", "Quienes ", t)
    t = re.sub(r"^El que ", "Quien ", t)
    t = re.sub(r"^La que ", "Quien ", t)
    t = re.sub(r"^Aquellos que ", "Quienes ", t)
    t = re.sub(r"^Todo hombre que ", "Quien ", t)
    # "Han de [verb]" → "deben [verb]" (run regardless of stage2_done)
    t = re.sub(r"\bhan de ", "deben ", t)
    t = re.sub(r"^Han de ", "", t)

    # ── Stage 4: Unified cleanup ──
    t = re.sub(r"\s+,", ", ", t)
    t = re.sub(r",\s*,", ", ", t)
    t = re.sub(r"\s+", " ", t).strip()
    if t:
        t = t[0].upper() + t[1:]
    t = t.rstrip(" ,;:")

    # ── Stage 5: Truncate if still too long ──
    truncated = False
    if len(t) > 80:
        for sep in [",", ";", ":", " y ", " pero ", " mas "]:
            if sep in t:
                parts = t.split(sep)
                first = parts[0].strip().rstrip(".,;:")
                if len(first) > 20 and len(first) < 75:
                    t = first
                    truncated = True
                    break

    # ── Stage 6: Short-title guard (only if NOT truncated) ──
    # If we truncated above, the short result is intentional (concision).
    # Only apply guard when the short title is due to over-stripping.
    if not truncated and len(t) < 30 and desc != t and desc.strip().rstrip(".") != t:
        candidate = desc.strip().rstrip(".")
        candidate = re.sub(r"^Se \w+\s+", "", candidate)
        candidate = re.sub(r"\s+", " ", candidate).strip()
        if candidate:
            candidate = candidate[0].upper() + candidate[1:]
        if len(candidate) > len(t):
            t = candidate

    return t


def fetch_soup(num, is_od=False):
    if is_od:
        url = f"https://www.churchofjesuschrist.org/study/scriptures/dc-testament/od/{num}?lang=spa"
    else:
        url = f"https://www.churchofjesuschrist.org/study/scriptures/dc-testament/dc/{num}?lang=spa"
    r = requests.get(url, headers=HEADERS, verify=False, timeout=30)
    r.encoding = "utf-8"
    return BeautifulSoup(r.text, "html.parser")


def parse_verses(text):
    """Parse '1–7' → (1, 7); '1' → (1, 1)."""
    m = re.match(r"(\d+)\s*[–\-]\s*(\d+)", text)
    if m:
        return int(m.group(1)), int(m.group(2))
    m = re.match(r"(\d+)", text)
    if m:
        v = int(m.group(1))
        return v, v
    return None, None


def split_pericope_text(text):
    """Parse '1–7, Desc; 8–16, Desc' into [(v_start, v_end, desc), ...]."""
    if not text:
        return []
    parts = [
        p.strip()
        for p in text.replace("\u2013", "-").replace("\u2014", "-").split(";")
        if p.strip()
    ]
    rows = []
    for part in parts:
        part = part.strip().lstrip(",-–;\u200b")
        m = re.match(r"(\d+(?:\s*[–\-]\s*\d+)?)\s*[,:\u2013]\s*(.*)", part)
        if m:
            vs, v_end = parse_verses(m.group(1))
            desc = m.group(2).strip().rstrip(".")
            if vs is not None:
                rows.append((vs, v_end if v_end else vs, desc))
            else:
                rows.append((None, None, desc))
        else:
            rows.append((None, None, part.strip()))
    return rows


def generate_refined(ss_rows, section_num, last_verse, is_od=False):
    """Apply refinement rules to official pericopes.

    - Use manual refinements for sections 1-20
    - For others: keep official, flag large units
    """
    if is_od:
        key = f"od{section_num}"
        if key in OD_REFINED:
            return OD_REFINED[key]
        # Fallback: single pericope for ODs without manual data
        return [
            (
                "(texto completo)",
                f"dyc-od{section_num}",
                1,
                last_verse or 3,
                "",
                "Sin encabezado oficial",
            )
        ]

    if section_num in MANUAL_REFINED_1_20:
        return MANUAL_REFINED_1_20[section_num]

    if section_num in MANUAL_REFINED_21_138:
        return MANUAL_REFINED_21_138[section_num]

    refined = []
    evento = EVENTOS_CANONICO.get(section_num, "")

    if not ss_rows:
        # Auto-generate single pericope for sections without study-summary
        lv = max(last_verse, 1)
        return [
            (
                "(texto completo)",
                f"dyc-{section_num}-texto-completo",
                1,
                lv,
                evento,
                "Sin encabezado oficial de perícopas",
            )
        ]

    for vs, ve, desc in ss_rows:
        if vs is None:
            if refined:
                prev = list(refined[-1])
                prev_desc = prev[0] + ". " + desc
                prev[0] = prev_desc
                refined[-1] = tuple(prev)
            continue
        rng = ve - vs + 1
        title = naturalize_title(desc)
        slug = seo_slug(desc, section_num)

        notes = []
        if rng > 12:
            notes.append(f"{rng} v. — considerar subdivisión")
        if evento:
            notes.append(f"evento: {evento}")

        refined.append((title, slug, vs, ve, evento, "; ".join(notes)))

    return refined


def process_section(num, is_od=False):
    soup = fetch_soup(num, is_od)

    if is_od:
        label = f"Declaración Oficial {num}"
    else:
        label = f"Sección {num}"

    title_el = soup.find("p", class_="title-number")
    intro_el = soup.find("p", class_="study-intro")
    ss_el = soup.find("p", class_="study-summary")

    intro_text = intro_el.get_text(strip=True) if intro_el else ""

    # Count verses
    verses = soup.find_all("p", class_="verse")
    last_verse = 0
    for v in verses:
        m = re.match(r"^(\d+)", v.get_text(strip=True))
        if m:
            last_verse = max(last_verse, int(m.group(1)))

    if ss_el:
        ss_text = ss_el.get_text(strip=True)
        ss_rows = split_pericope_text(ss_text)
    else:
        ss_rows = []

    refined = generate_refined(ss_rows, num, last_verse, is_od)

    return {
        "label": label,
        "num": num,
        "intro": intro_text,
        "official_pericopes": ss_rows,
        "refined": refined,
        "has_official": ss_el is not None,
        "last_verse": last_verse,
    }


def format_refined_section(data):
    lines = []
    lines.append(f"### {data['label']}")
    lines.append("")
    if data["intro"]:
        lines.append(f"{data['intro']}")
        lines.append("")
    if data["official_pericopes"]:
        lines.append(
            f"**Encabezado oficial ({len(data['official_pericopes'])} perícopas):** "
            + "; ".join(
                f"{vs}–{ve}, {desc}" for vs, ve, desc in data["official_pericopes"]
            )
        )
        lines.append("")
    elif not data["has_official"]:
        lines.append(
            "*(No tiene encabezado oficial de perícopas en el sitio de la Iglesia.)*"
        )
        lines.append("")

    lines.append("| # | Título | Slug | v | `_evento_canonico` | Notas |")
    lines.append("|:-:|:-------|:-----|:-:|:-------------------|:------|")

    if data["refined"]:
        for i, (title, slug, v_start, v_end, evento, notas) in enumerate(
            data["refined"], 1
        ):
            v_range = f"{v_start}–{v_end}" if v_start == v_end else f"{v_start}–{v_end}"
            evento_val = f"`{evento}`" if evento else "—"
            lines.append(
                f"| {i} | {title} | `{slug}` | {v_range} | {evento_val} | {notas} |"
            )
    else:
        lines.append("| — | _(pendiente de definir)_ | — | — | — | |")

    lines.append("")
    lines.append("---")
    lines.append("")
    return "\n".join(lines)


def compute_metrics(all_data):
    total_per = sum(len(d["refined"]) for d in all_data)
    with_evento = sum(1 for d in all_data for _, _, _, _, e, _ in d["refined"] if e)
    verses_total = 0
    per_count = 0
    for d in all_data:
        for _, _, vs, ve, _, _ in d["refined"]:
            verses_total += ve - vs + 1
            per_count += 1
    avg_range = round(verses_total / per_count, 1) if per_count else 0
    avg_per_section = round(total_per / len(all_data), 1) if all_data else 0
    return total_per, with_evento, avg_range, avg_per_section


def main():
    sections = list(range(1, 139))
    ods = [1, 2]

    all_data = []

    print(f"Procesando {len(sections)} secciones y {len(ods)} OD...")

    for num in sections:
        data = process_section(num)
        all_data.append(data)
        n_off = len(data["official_pericopes"])
        n_ref = len(data["refined"])
        print(f"  Sec {num:3d}: {n_off} oficial → {n_ref} refinadas")

    for num in ods:
        data = process_section(num, is_od=True)
        all_data.append(data)
        n_ref = len(data["refined"])
        print(f"  OD {num}: {n_ref} refinadas")

    total_per, with_evento, avg_range, avg_per_section = compute_metrics(all_data)

    # Build markdown
    md = []
    md.append("# Plan de Perícopas — Doctrina y Convenios")
    md.append("")
    md.append("> Generado desde los encabezados oficiales de churchofjesuschrist.org")
    md.append("> con refinamientos según criterios de `mapeo-pericopas.md`")
    md.append("> (granularidad, atomicidad, especificidad, concordancia).")
    md.append(">")
    md.append(
        "> **Fuente**: https://www.churchofjesuschrist.org/study/scriptures/dc-testament?lang=spa"
    )
    md.append("")
    md.append("## Leyendas")
    md.append("")
    md.append("| Columna | Significado |")
    md.append("|:--------|:------------|")
    md.append("| `#` | Número de orden de la perícopa dentro de la sección |")
    md.append(
        "| `Título` | Título en español natural activo (refinado según `mapeo-pericopas.md`) |"
    )
    md.append("| `Slug` | Slug SEO-friendly para taxonomía `bc_pericopa` |")
    md.append("| `v` | Rango de versículos que abarca |")
    md.append(
        "| `_evento_canonico` | Identificador de evento que abarca varios libros canónicos |"
    )
    md.append(
        "| `Notas` | Advertencias: subdivisión recomendada, evento asignado, etc. |"
    )
    md.append("")
    md.append("**Convenciones:**")
    md.append(
        '- Títulos en **voz activa**, sin metalenguaje (".se declara", "se indica")'
    )
    md.append(
        "- Slugs sin stop words (de, del, la, los, el, en, por, para, con, que, a, etc.)"
    )
    md.append(
        "- `_evento_canonico` vincula perícopas que narran el mismo evento en varios libros"
    )
    md.append(
        '- "considerar subdivisión" = perícopa de >12 versículos que podría dividirse'
    )
    md.append("")
    md.append("## Resumen")
    md.append("")
    md.append("| Métrica | Valor |")
    md.append("|:--------|:------|")
    md.append(f"| Secciones procesadas | {len(all_data)} |")
    md.append(f"| Perícopas totales | **{total_per}** |")
    md.append(f"| Promedio por sección | {avg_per_section} |")
    md.append(f"| Perícopas con `_evento_canonico` | {with_evento} |")
    md.append(f"| Rango de versículos promedio | {avg_range} v. por perícopa |")
    md.append("")
    md.append("---")
    md.append("")

    for data in all_data:
        md.append(format_refined_section(data))

    content = "\n".join(md)

    os.makedirs(os.path.dirname(OUTPUT), exist_ok=True)
    with open(OUTPUT, "w", encoding="utf-8") as f:
        f.write(content)

    print(f"\nEscrito: {OUTPUT}")
    print(f"Tamaño: {os.path.getsize(OUTPUT)} bytes")
    print(
        f"\nResumen: {len(all_data)} secciones, {total_per} perícopas, {with_evento} con evento canónico"
    )


if __name__ == "__main__":
    main()
