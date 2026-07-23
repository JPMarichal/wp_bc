#!/usr/bin/env python3
import subprocess, os

body = ""

body += '<h2 class="wp-block-heading">\u00bfQu\u00e9 es el quiasmo?</h2>'
body += "<p>El quiasmo (del griego chi, letra X) es una figura literaria en la cual una serie de ideas o palabras se presentan en un orden determinado y luego se repiten en orden inverso (es decir, en patr\u00f3n A-B-C-D-D'-C'-B'-A'). El punto central o \u00e1pice del quiasmo suele contener el mensaje teol\u00f3gico o la ense\u00f1anza principal del pasaje, actuando como el eje sobre el cual gira toda la estructura. Esta t\u00e9cnica ret\u00f3rica era caracter\u00edstica de la literatura sem\u00edtica antigua, especialmente de la poes\u00eda hebrea cl\u00e1sica, donde aparece en libros como Salmos, Isa\u00edas y G\u00e9nesis, y era completamente desconocida como estructura formal en el mundo angloparlante del siglo XIX.</p>"

body += "<p>La Encyclopedia of Mormonism, en su art\u00edculo \u00abBook of Mormon Authorship\u00bb, se\u00f1ala que los estudios estructurales han identificado el quiasmo como \u00abuna forma literaria art\u00edstica que aparece en rica diversidad tanto en la Biblia como en el Libro de Morm\u00f3n\u00bb. El art\u00edculo observa que, aunque el quiasmo puede aparecer en casi cualquier lengua o literatura, fue prevalente en la literatura b\u00edblica del antiguo Cercano Oriente y era desconocido para Joseph Smith y sus contempor\u00e1neos en el siglo XIX.</p>"

body += '<h2 class="wp-block-heading">El quiasmo de Alma 36: el m\u00e1s famoso y debatido</h2>'
body += "<p>El ejemplo m\u00e1s c\u00e9lebre y perfectamente formado se encuentra en Alma 36, donde el profeta Alma relata a su hijo Helaman su profunda conversi\u00f3n espiritual tras tres d\u00edas de amarga agon\u00eda. El cap\u00edtulo completo est\u00e1 estructurado como un quiasmo sim\u00e9trico cuyo punto central gira en torno a Jesucristo y Su expiaci\u00f3n. Welch, quien descubri\u00f3 este quiasmo en 1967 mientras estudiaba en Oxford, public\u00f3 su an\u00e1lisis en 1969 en la revista BYU Studies, y el art\u00edculo fue posteriormente adaptado para la revista Ensign en 1972 bajo el t\u00edtulo \u00abChiasmus in the Book of Mormon\u00bb.</p>"

body += "<p>Sin embargo, el quiasmo de Alma 36 no ha estado exento de cr\u00edtica acad\u00e9mica. El art\u00edculo \u00abRethinking Alma 36\u00bb (Repensando Alma 36), publicado en Interpreter: A Journal of Latter-day Saint Faith and Scholarship por David E. Bokovoy y John Gee, se\u00f1ala que aunque Alma 36 ha sido uno de los ejemplos m\u00e1s admirados de quiasmo hebreo cl\u00e1sico en el Libro de Morm\u00f3n, las cr\u00edticas en las \u00faltimas dos d\u00e9cadas han cuestionado si realmente cumple los requisitos de los quiasmos b\u00edblicos cl\u00e1sicos, particularmente se\u00f1alando las grandes secciones del cap\u00edtulo que quedan fuera de la estructura qui\u00e1stica propuesta.</p>"

body += "<p>A su vez, el art\u00edculo \u00abAsymmetry in Chiasms with a Note about Deuteronomy 8 and Alma 36\u00bb (Asimetr\u00eda en los quiasmos, con una nota sobre Deuteronomio 8 y Alma 36), tambi\u00e9n en Interpreter Journal, responde a estas cr\u00edticas argumentando que la asimetr\u00eda parcial en los quiasmos propuestos no es necesariamente una debilidad. La asimetr\u00eda puede surgir naturalmente del proceso de traducci\u00f3n o de la transmisi\u00f3n textual, y quiasmos b\u00edblicos reconocidos tambi\u00e9n presentan asimetr\u00edas similares.</p>"

body += (
    '<h2 class="wp-block-heading">El descubrimiento de John W. Welch y su impacto</h2>'
)
body += "<p>John W. Welch, actualmente profesor de Derecho en la Universidad Brigham Young y editor fundador de BYU Studies, descubri\u00f3 el quiasmo en el Libro de Morm\u00f3n mientras realizaba estudios de posgrado en Oxford. Su descubrimiento inicial se public\u00f3 en 1969 en BYU Studies Quarterly con el t\u00edtulo \u00abChiasmus in the Book of Mormon\u00bb, y desde entonces ha sido uno de los argumentos m\u00e1s citados en defensa de la autenticidad antigua del libro.</p>"

body += "<p>El art\u00edculo \u00abCelebrating the Work of John W. Welch\u00bb (Celebrando la obra de John W. Welch), publicado en Interpreter Journal, analiza c\u00f3mo Welch identific\u00f3 no solo quiasmos individuales sino estructuras qui\u00e1sticas a gran escala que abarcan libros enteros del Libro de Morm\u00f3n. Por ejemplo, Welch encontr\u00f3 que tanto 1 Nefi como 2 Nefi fueron organizados intencionalmente usando estructuras qui\u00e1sticas, y que subunidades como 2 Nefi 11:2-8 contienen estructura qui\u00e1stica en dos niveles adicionales. Estos hallazgos sugieren un nivel de sofisticaci\u00f3n compositiva que va mucho m\u00e1s all\u00e1 de lo que podr\u00eda esperarse de una simple imitaci\u00f3n del estilo b\u00edblico.</p>"

body += '<h2 class="wp-block-heading">Otras figuras literarias: paralelismo, inclusio y m\u00e1s</h2>'
body += "<p>Adem\u00e1s del quiasmo, el Libro de Morm\u00f3n contiene abundantes ejemplos de otras figuras literarias caracter\u00edsticas de la literatura sem\u00edtica. Donald W. Parry, profesor de Hebreo en la Universidad Brigham Young, public\u00f3 en 1992 la obra definitiva sobre el tema: \u00abPoetic Parallelisms in the Book of Mormon: The Complete Text Reformatted According to Parallelistic Patterns\u00bb (Paralelismos po\u00e9ticos en el Libro de Morm\u00f3n: el texto completo reformateado seg\u00fan patrones paralel\u00edsticos), publicada por FARMS. En esta obra monumental, Parry reformatea el texto completo del Libro de Morm\u00f3n para revelar sus estructuras po\u00e9ticas subyacentes, demostrando que gran parte del libro est\u00e1 compuesto en forma po\u00e9tica.</p>"

body += "<p>El paralelismo sinon\u00edmico, en el cual una idea se repite con palabras diferentes en el segundo verso (como en Salmos 19:1: \u00abLos cielos cuentan la gloria de Dios, y el firmamento anuncia la obra de sus manos\u00bb), aparece abundantemente en el Libro de Morm\u00f3n. El paralelismo antitético, donde la segunda l\u00ednea contrasta con la primera, tambi\u00e9n es frecuente. El inclusio \u2014un marco literario donde una secci\u00f3n comienza y termina con la misma frase o idea\u2014 se encuentra en m\u00faltiples discursos prof\u00e9ticos, incluyendo el discurso del rey Benjam\u00edn en Mos\u00edah 2-5.</p>"

body += "<p>El art\u00edculo \u00abPoesy and Prosody in the Book of Mormon\u00bb (Poes\u00eda y prosodia en el Libro de Morm\u00f3n), publicado en Interpreter Journal, sostiene que \u00abel arte po\u00e9tico en el Libro de Morm\u00f3n est\u00e1 altamente desarrollado\u00bb, y que aunque muchos lectores conocen los impresionantes ejemplos de quiasmo, menos est\u00e1n familiarizados con la riqueza de la poes\u00eda hebrea que impregna el texto. El art\u00edculo analiza c\u00f3mo los autores nefitas empleaban met\u00e1foras, s\u00edmiles y estructuras r\u00edtmicas propias de la tradici\u00f3n po\u00e9tica del antiguo Israel.</p>"

body += '<h2 class="wp-block-heading">La perspectiva de la Restauraci\u00f3n</h2>'
body += "<p>Para los miembros de la Iglesia de Jesucristo de los Santos de los \u00daltimos D\u00edas, el descubrimiento de quiasmos complejos en el Libro de Morm\u00f3n representa una confirmaci\u00f3n literaria contundente de su autenticidad antigua. La Encyclopedia of Mormonism lo expresa con claridad en su art\u00edculo \u00abBook of Mormon Language\u00bb: \u00abLas afirmaciones de que Jos\u00e9 Smith compuso el Libro de Morm\u00f3n simplemente imitando el ingl\u00e9s de la King James... t\u00edpicamente muestran insensibilidad acerca de su car\u00e1cter ling\u00fc\u00edstico... los patrones literarios como el quiasmo y numerosas otras caracter\u00edsticas notadas en los estudios desde 1830 se combinan para hacer de la fabricaci\u00f3n del libro un desaf\u00edo abrumador para cualquiera en la \u00e9poca de Jos\u00e9 Smith\u00bb.</p>"

body += '<h2 class="wp-block-heading">Conclusi\u00f3n</h2>'
body += "<p>El quiasmo y las figuras literarias del Libro de Morm\u00f3n constituyen una de las evidencias m\u00e1s poderosas de su origen antiguo y su car\u00e1cter de texto traducido desde una lengua sem\u00edtica. Desde el descubrimiento pionero de John W. Welch en 1967 hasta los exhaustivos an\u00e1lisis de Donald W. Parry y los debates acad\u00e9micos en Interpreter Journal, el estudio de estas estructuras ha demostrado que el Libro de Morm\u00f3n posee una sofisticaci\u00f3n literaria que no podr\u00eda haber sido producida por un autor del siglo XIX sin acceso a las tradiciones ret\u00f3ricas del antiguo Cercano Oriente.</p>"

# Add references table
body += '<h2 class="wp-block-heading">Referencias de las Escrituras</h2>'
body += '<figure class="wp-block-table"><table class="bc-forma-t"><thead><tr><th>Concepto</th><th>Referencia</th></tr></thead><tbody>'
refs = [
    ["El quiasmo perfecto de la conversi\u00f3n de Alma", "Alma 36:1-30"],
    [
        "Paralelismo sinon\u00edmico en la ense\u00f1anza de Alma a Helam\u00e1n",
        "Alma 36:1-5",
    ],
    [
        "Estructuras po\u00e9ticas en el discurso del rey Benjam\u00edn",
        "Mos\u00edah 5:1-15",
    ],
    ["Paralelismos antitéticos sobre justicia y misericordia", "Alma 41:13-15"],
    ["El discurso de Jacob como ejemplo de poes\u00eda hebrea", "Jacob 6:1-13"],
    ["Estructura qui\u00e1stica en la ense\u00f1anza de Nefi", "1 Nefi 19:7-17"],
    ["La visita de Cristo a los nefitas con inclusio", "3 Nefi 11:1-17"],
    [
        "El testimonio de Morm\u00f3n sobre la lengua del registro",
        "Morm\u00f3n 9:32-34",
    ],
    ["La exhortaci\u00f3n final de Moroni", "Moroni 10:3-5"],
    ["Toda la Escritura es inspirada por Dios", "2 Timoteo 3:16-17"],
    ["La palabra del Se\u00f1or como l\u00e1mpara", "Salmos 119:105"],
    ["El testimonio unificado de la palabra divina", "2 Nefi 29:8"],
]
for r in refs:
    body += f"<tr><td>{r[0]}</td><td>{r[1]}</td></tr>"
body += "</tbody></table></figure>"

# Add sources
body += (
    '<h2 class="wp-block-heading">Fuentes consultadas</h2><ul class="wp-block-list">'
)
sources = [
    ["Quiasmo", "https://es.wikipedia.org/wiki/Quiasmo", "Wikipedia en espa\u00f1ol"],
    ["Chiasmus", "https://en.wikipedia.org/wiki/Chiasmus", "Wikipedia en ingl\u00e9s"],
    [
        "Book of Mormon Authorship",
        "https://eom.byu.edu/index.php/Book_of_Mormon_Authorship",
        "Encyclopedia of Mormonism",
    ],
    [
        "Book of Mormon Language",
        "https://eom.byu.edu/index.php/Book_of_Mormon_Language",
        "Encyclopedia of Mormonism",
    ],
    [
        "Rethinking Alma 36",
        "https://journal.interpreterfoundation.org/rethinking-alma-36/",
        "Interpreter Journal",
    ],
    [
        "Asymmetry in Chiasms",
        "https://journal.interpreterfoundation.org/asymmetry-in-chiasms-with-a-note-about-deuteronomy-8-and-alma-36/",
        "Interpreter Journal",
    ],
    [
        "Celebrating the Work of John W. Welch",
        "https://journal.interpreterfoundation.org/celebrating-the-work-of-john-w-welch/",
        "Interpreter Journal",
    ],
]
for s in sources:
    body += f'<li><a href="{s[1]}" target="_blank" rel="noopener noreferrer">{s[0]} <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> \u2014 {s[2]}</li>'
body += "</ul>"

# Write to temp file
with open("/tmp/article3_content.html", "w", encoding="utf-8") as f:
    f.write(body)

print(f"Content length: {len(body)} bytes")
