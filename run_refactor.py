import re
import collections
import unicodedata

book_header_map = [
    ("Romanos", ["Romanos", "a los Romanos"]),
    ("1 Corintios", ["1 Corintios", "Primera Epístola a los Corintios", "Primera Epistola a los Corintios"]),
    ("2 Corintios", ["2 Corintios", "Segunda Epístola a los Corintios", "Segunda Epistola a los Corintios"]),
    ("Gálatas", ["Gálatas", "a los Gálatas", "a los Galatas"]),
    ("Efesios", ["Efesios", "a los Efesios"]),
    ("Filipenses", ["Filipenses", "a los Filipenses"]),
    ("Colosenses", ["Colosenses", "a los Colosenses"]),
    ("1 Tesalonicenses", ["1 Tesalonicenses", "Primera Epístola a los Tesalonicenses", "Primera Epístola a los Tesalónica", "Primera Epístola a los Tesalo", "Primera Epístola a los Tesalonicenses (5 capítulos)"]),
    ("2 Tesalonicenses", ["2 Tesalonicenses", "Segunda Epístola a los Tesalonicenses", "Segunda Epístola a los Tesalo"]),
    ("1 Timoteo", ["1 Timoteo", "Primera Epístola a Timoteo"]),
    ("2 Timoteo", ["2 Timoteo", "Segunda Epístola a Timoteo"]),
    ("Tito", ["Tito", "a Tito"]),
    ("Filemón", ["Filemón", "a Filemón", "a Filemon"]),
    ("Hebreos", ["Hebreos", "a los Hebreos"]),
    ("Santiago", ["Santiago", "de Santiago", "Universal de Santiago"]),
    ("1 Pedro", ["1 Pedro", "Primera Epístola Universal de Pedro"]),
    ("2 Pedro", ["2 Pedro", "Segunda Epístola Universal de Pedro"]),
    ("1 Juan", ["1 Juan", "Primera Epístola Universal de Juan"]),
    ("2 Juan", ["2 Juan", "Segunda Epístola Universal de Juan"]),
    ("3 Juan", ["3 Juan", "Tercera Epístola Universal de Juan"]),
    ("Judas", ["Judas", "Universal de Judas"])
]

book_slug_map = {
    'Romanos': 'romanos',
    '1 Corintios': '1-corintios',
    '2 Corintios': '2-corintios',
    'Gálatas': 'galatas',
    'Efesios': 'efesios',
    'Filipenses': 'filipenses',
    'Colosenses': 'colosenses',
    '1 Tesalonicenses': '1-tesalonicenses',
    '2 Tesalonicenses': '2-tesalonicenses',
    '1 Timoteo': '1-timoteo',
    '2 Timoteo': '2-timoteo',
    'Tito': 'tito',
    'Filemón': 'filemon',
    'Hebreos': 'hebreos',
    'Santiago': 'santiago',
    '1 Pedro': '1-pedro',
    '2 Pedro': '2-pedro',
    '1 Juan': '1-juan',
    '2 Juan': '2-juan',
    '3 Juan': '3-juan',
    'Judas': 'judas'
}

def clean_slug(title, book_slug, cap_num):
    title = re.sub(r'/[a-zA-Z0-9]+$', '', title)
    title = re.sub(r'\s+en\s+.*$', '', title)
    nfd = unicodedata.normalize('NFD', title)
    title_clean = "".join([c for c in nfd if unicodedata.category(c) != 'Mn'])
    title_clean = title_clean.lower()
    title_clean = re.sub(r'[^a-z0-9\s-]', '', title_clean)
    words = title_clean.split()
    
    stop_words = {
        'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas',
        'y', 'e', 'o', 'u', 'pero', 'mas', 'sino', 'aunque',
        'de', 'del', 'a', 'al', 'con', 'para', 'por', 'en', 'sobre', 'bajo', 'entre', 'desde', 'hasta', 'hacia', 'sin', 'tras',
        'mi', 'tu', 'su', 'mis', 'tus', 'sus', 'nuestro', 'nuestra', 'nuestros', 'nuestras', 'vuestro', 'vuestra',
        'este', 'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'aquel', 'aquella', 'aquellos', 'aquellas',
        'que', 'quien', 'cual', 'cuyo', 'cuya', 'cuyos', 'cuyas', 'se', 'me', 'te', 'nos', 'os', 'le', 'les', 'lo', 'la', 'los', 'las',
        'no', 'sí', 'si', 'como', 'cuando', 'donde', 'por que', 'porque', 'para que'
    }
    
    filtered_words = []
    for w in words:
        for sub_w in w.split('-'):
            if sub_w and sub_w not in stop_words:
                filtered_words.append(sub_w)
                
    slug_body = "-".join(filtered_words)
    slug = f"{book_slug}-{cap_num}-{slug_body}"
    
    if len(slug) > 60:
        while len(slug) > 60 and len(filtered_words) > 1:
            filtered_words.pop()
            slug_body = "-".join(filtered_words)
            slug = f"{book_slug}-{cap_num}-{slug_body}"
            
    slug = re.sub(r'-+', '-', slug).strip('-')
    return slug

def parse_and_refactor_in_place():
    with open('docs/juego-del-cinco/plan-pericopas-nt-epistolas.md', 'r', encoding='utf-8') as f:
        content = f.read()

    lines = content.splitlines()
    output_lines = []
    
    current_book = None
    current_chapter = None
    
    def get_ultimate_title(book, cap, p_num, old_title):
        clean_old = old_title
        if " en " in clean_old:
            clean_old = clean_old.split(" en ")[0]
        if " de " in clean_old:
            clean_old = clean_old.split(" de ")[0]
        while clean_old.endswith(" Colosenses 1") or clean_old.endswith(" Santiago 1") or clean_old.endswith(" Romanos 16") or clean_old.endswith(" Romanos 1") or clean_old.endswith(" Romanos 8"):
            clean_old = re.sub(r'\s+(Colosenses|Santiago|Romanos)\s+\d+$', '', clean_old)
            
        clean_old = re.sub(r'/[a-zA-Z0-9]+$', '', clean_old)
        
        # Romanos
        if book == 'Romanos':
            if cap == 1:
                if p_num == 1: return "Saludo apostólico y gracia romana"
                if p_num == 2: return "Deseo pastoral de visitar Roma"
                if p_num == 3: return "Justicia salvadora divina por fe"
                if p_num == 4: return "Culpabilidad y extravío del paganismo"
            if cap == 2:
                if p_num == 1: return "El justo juicio moral de Dios"
                if p_num == 2: return "Circuncisión interior en el corazón"
            if cap == 3:
                if p_num == 1: return "La fidelidad inquebrantable de Dios"
                if p_num == 2: return "Universalidad del pecado y culpa humana"
                if p_num == 3: return "Justificación gratuita por gracia salvadora"
            if cap == 4:
                if p_num == 1: return "La herencia de gracia por fe de Abraham"
                if p_num == 2: return "Herederos mediante la promesa fiel de Dios"
            if cap == 5:
                if p_num == 1: return "Paz, paciencia y gozo eterno divino"
                if p_num == 2: return "Contraste salvífico entre Adán y Cristo"
            if cap == 6:
                if p_num == 1: return "El significado del bautismo santo"
                if p_num == 2: return "Libres del pecado sirviendo a justicia"
            if cap == 7:
                if p_num == 1: return "Liberación de ley por muerte espiritual"
                if p_num == 2: return "El conflicto del yo carnal romano"
            if cap == 8:
                if p_num == 1: return "Vida espiritual como amados hijos divinos"
                if p_num == 2: return "Sufrimiento presente y anhelo cósmico creador"
                if p_num == 3: return "Amor divino inquebrantable en Cristo"
            if cap == 9:
                if p_num == 1: return "Dolor por incredulidad de Israel carnal"
                if p_num == 2: return "Soberana elección y divina compasión infinita"
                if p_num == 3: return "Cristo, piedra de tropiezo en Sion"
            if cap == 10:
                if p_num == 1: return "Salvación mediante confesión de fe sincera"
                if p_num == 2: return "La incredulidad del pueblo escogido de Israel"
            if cap == 11:
                if p_num == 1: return "El remanente preservado por gracia divina"
                if p_num == 2: return "Advertencia a las ramas gentiles injertadas"
                if p_num == 3: return "Restauración futura del pueblo de Israel"
            if cap == 12:
                if p_num == 1: return "Culto racional y sacrificio vivo romano"
                if p_num == 2: return "Deberes prácticos del amor fraterno fiel"
            if cap == 13:
                if p_num == 1: return "Sometimiento civil a legítimas autoridades"
                if p_num == 2: return "El amor cumple ley divina perfectamente"
                if p_num == 3: return "Andar honestamente desechando las tinieblas mundanas"
            if cap == 14:
                if p_num == 1: return "Tolerancia mutua ante débiles espirituales"
                if p_num == 2: return "No poner tropiezo moral al hermano"
            if cap == 15:
                if p_num == 1: return "Aceptación mutua para mutua edificación santa"
                if p_num == 2: return "Mayordomía apostólica entre gentiles convertidos"
                if p_num == 3: return "Planes misioneros y futuras visitas romanas"
            if cap == 16:
                if p_num == 1: return "Recomendación de Febe y saludos caritativos"
                if p_num == 2: return "Advertencia seria contra falsos cismáticos pecaminosos"
                if p_num == 3: return "Solemne doxología salvadora final apostólica"

        # 1 Corintios
        if book == '1 Corintios':
            if cap == 1:
                if p_num == 1: return "Saludo apostólico de paz santificadora corintia"
                if p_num == 2: return "Gratitud por los dones espirituales concedidos"
                if p_num == 3: return "Reprensión por facciones y divisiones corporales"
                if p_num == 4: return "Cristo crucificado, sabiduría y poder bendito"
            if cap == 2:
                if p_num == 1: return "Predicación humilde en poder espiritual corintio"
                if p_num == 2: return "Misterios celestiales del Espíritu Santo revelado"
            if cap == 3:
                if p_num == 1: return "Inmadurez de colaboradores de Dios dadores"
                if p_num == 2: return "El único cimiento, Jesucristo Señor nuestro"
                if p_num == 3: return "Santuario santo y sabiduría necia humana"
            if cap == 4:
                if p_num == 1: return "Fidelidad de administradores de misterios santos"
                if p_num == 2: return "Paternidad protectora del apóstol sufriente corintio"
            if cap == 5:
                if p_num == 1: return "Juicio severo ante inmoralidad descarada carnal"
            if cap == 6:
                if p_num == 1: return "Demandas deshonrosas entre hermanos santos litigantes"
                if p_num == 2: return "Cuerpo santificado, templo del Espíritu sagrado"
            if cap == 7:
                if p_num == 1: return "Enseñanzas sobre el matrimonio cristiano santo"
                if p_num == 2: return "Permanecer en el estado del llamamiento celestial"
                if p_num == 3: return "Consejos apostólicos sobre la soltería piadosa"
            if cap == 8:
                if p_num == 1: return "Conocimiento soberbio frente a amor caritativo"
                if p_num == 2: return "Sensibilidad moral con hermanos débiles corintios"
            if cap == 9:
                if p_num == 1: return "Sostén material y derechos apostólicos legítimos"
                if p_num == 2: return "Servicio voluntario para ganar almas corintias"
                if p_num == 3: return "Disciplina atlética en carrera celestial corintia"
            if cap == 10:
                if p_num == 1: return "Advertencia basada en caída histórica israelita"
                if p_num == 2: return "Cáliz sagrado y comunión eucarística corintia"
                if p_num == 3: return "Libertad orientada a edificación mutua social"
            if cap == 11:
                if p_num == 1: return "Orden eclesiástico y velo pudoroso femenino"
                if p_num == 2: return "Institución solemne de Cena del Señor"
            if cap == 12:
                if p_num == 1: return "Diversidad carismática bajo un Espíritu consolador"
                if p_num == 2: return "El cuerpo orgánico y miembros unidos"
            if cap == 13:
                if p_num == 1: return "La preeminencia eterna del amor puro caridad"
            if cap == 14:
                if p_num == 1: return "Profecía de asamblea frente a lenguas corintias"
                if p_num == 2: return "Orden pacífico en asambleas litúrgicas corintias"
            if cap == 15:
                if p_num == 1: return "Hecho histórico y testimonio resurrección pascual"
                if p_num == 2: return "Triunfo definitivo del plan pascual divino"
                if p_num == 3: return "Naturaleza gloriosa del nuevo cuerpo resucitado"
                if p_num == 4: return "Victory triunfal sobre aguijón muerte corintia"
            if cap == 16:
                if p_num == 1: return "Ofrenda caritativa para santos necesitados pobres"
                if p_num == 2: return "Recomendaciones fraternales de despedida corintia amada"

        # 2 Corintios
        if book == '2 Corintios':
            if cap == 1:
                if p_num == 1: return "Consolación de Dios ante pruebas duras"
                if p_num == 2: return "Fidelidad sincera del ministerio apostólico dolido"
            if cap == 2:
                if p_num == 1: return "Perdón y restauración del ofensor arrepentido"
                if p_num == 2: return "Fragancia triunfal del conocimiento divino salvador"
            if cap == 3:
                if p_num == 1: return "Cartas vivas escritas por el Espíritu segundo"
                if p_num == 2: return "Soberano ministerio de gloria superior mesiánica"
            if cap == 4:
                if p_num == 1: return "Faro evangélico cobijado en barro terrenal segundo"
                if p_num == 2: return "Esperanza gloriosa en leve tribulación pasajera"
            if cap == 5:
                if p_num == 1: return "Habitación eterna en morada divina celestial segunda"
                if p_num == 2: return "Ministerio reconciliador de nueva criatura espiritual"
            if cap == 6:
                if p_num == 1: return "Sufrimiento abnegado de obreros incansables fieles segundo"
                if p_num == 2: return "Santuario consagrado libre de idolatría mundana segunda"
            if cap == 7:
                if p_num == 1: return "Dilecta consolación por arrepentimiento genuino corintio"
            if cap == 8:
                if p_num == 1: return "Ejemplo generoso de iglesias macedónicas ricas"
                if p_num == 2: return "Delegación confiable de colecta caritativa organizada"
            if cap == 9:
                if p_num == 1: return "Sembrador generoso cosechando bendición abundante divina"
            if cap == 10:
                if p_num == 1: return "Armas espirituales contra altivez rebelde soberbia segunda"
                if p_num == 2: return "Límites saludables de provincia misionera apostólica"
            if cap == 11:
                if p_num == 1: return "Cuidado contra sutiles apóstoles falsos mentirosos"
                if p_num == 2: return "Peligros y flaquezas soportados alegremente corintios"
            if cap == 12:
                if p_num == 1: return "Visiones celestiales y aguijón en carne mortal segundo"
                if p_num == 2: return "Afecto desinteresado por amada grey corintia"
            if cap == 13:
                if p_num == 1: return "Autoexamen exigido antes de visita apostólica"
                if p_num == 2: return "Bendición trinitaria y despedida gozosa espiritual"

        # Gálatas
        if book == 'Gálatas':
            if cap == 1:
                if p_num == 1: return "Defensa del evangelio contra perversiones heréticas"
                if p_num == 2: return "Llamamiento divino independiente de hombres terrenos"
            if cap == 2:
                if p_num == 1: return "Aprobación apostólica de misión gentiles libres"
                if p_num == 2: return "Reprensión pública a disimulo de Pedro de Gálatas"
                if p_num == 3: return "Justificación por la gracia salvadora gálata"
            if cap == 3:
                if p_num == 1: return "La insensatez de abandonar fe espiritual gálata"
                if p_num == 2: return "La herencia prometida antes de la ley gálata"
                if p_num == 3: return "Pedagogía de ley guiando a Cristo gálata"
            if cap == 4:
                if p_num == 1: return "Hijos y herederos de Dios libertados gálatas"
                if p_num == 2: return "Angustia pastoral ante la apostasía necia gálata"
                if p_num == 3: return "Alegoría liberadora de las dos alianzas gálatas"
            if cap == 5:
                if p_num == 1: return "Mantenerse firmes en santa libertad gálata"
                if p_num == 2: return "Obras de carne frente a frutos de espíritu gálatas"
            if cap == 6:
                if p_num == 1: return "Ayuda solidaria y ley de siembra celestial gálata"
                if p_num == 2: return "Jactarse únicamente en cruz crística salvadora gálata"

        # Efesios
        if book == 'Efesios':
            if cap == 1:
                if p_num == 1: return "Bendición doxológica de salvación efesia celestial"
                if p_num == 2: return "Soberanos designios del divino plan eterno efesio"
                if p_num == 3: return "Intercesión por sabiduría espiritual plena efesia"
            if cap == 2:
                if p_num == 1: return "Salvos únicamente por amor divino salvador efesio"
                if p_num == 2: return "Hombres unificados en divina paz salvadora efesia"
            if cap == 3:
                if p_num == 1: return "Misterio salvífico oculto por generaciones gentiles"
                if p_num == 2: return "Ruego por arraigo en amor infinito efesio"
            if cap == 4:
                if p_num == 1: return "Comunión orgánica y pluralidad de dones efesios"
                if p_num == 2: return "Renovación mental del hombre nuevo bautizado efesio"
            if cap == 5:
                if p_num == 1: return "Caminar santamente como hijos de luz divina efesia"
                if p_num == 2: return "Misterio nupcial de Cristo e iglesia efesios"
            if cap == 6:
                if p_num == 1: return "Deberes filiales de padres y siervos efesios"
                if p_num == 2: return "Lucha encarnizada y armadura divina completa efesia"
                if p_num == 3: return "Últimas exhortaciones piadosas de despedida efesia amada"

        # Filipenses
        if book == 'Filipenses':
            if cap == 1:
                if p_num == 1: return "Oración de gratitud y afecto entrañable filipense"
                if p_num == 2: return "Cadenas apostólicas propagadoras de fe viva filipenses"
                if p_num == 3: return "Vivir es Cristo morir es ganancia filipense"
            if cap == 2:
                if p_num == 1: return "Humillación crística y gloria posterior soberana filipense"
                if p_num == 2: return "Luminarias limpias en generación perversa filipenses"
                if p_num == 3: return "Elogio a servidores de confianza santos filipenses"
            if cap == 3:
                if p_num == 1: return "Excelencia de conocer a Jesucristo Salvador filipense"
                if p_num == 2: return "Ciudadanos patrios apuntando a meta gloriosa filipense"
            if cap == 4:
                if p_num == 1: return "Gozarse en el Señor y sobriedad mental filipense"
                if p_num == 2: return "Gratitud por el auxilio filipense oportuno"
                if p_num == 3: return "Despedida triunfal y saludos finales filipenses de paz"

        # Colosenses
        if book == 'Colosenses':
            if cap == 1:
                if p_num == 1: return "Gratitud por fe de santos colosenses devotos"
                if p_num == 2: return "Supremacía absoluta del Hijo soberano creador de Colosos"
                if p_num == 3: return "Sufrimiento gozoso de heraldo divino consagrado colosense"
            if cap == 2:
                if p_num == 1: return "Fe firme desestimando filosofías huecas engañosas de Colosas"
                if p_num == 2: return "Triunfo absoluto de cruz sobre decretos ley de Colosas"
                if p_num == 3: return "Abominación de ascetismos normativos vanos humanos de Colosas"
            if cap == 3:
                if p_num == 1: return "Buscar cosas de arriba celestial morada de Colosas"
                if p_num == 2: return "Relaciones familiares colosenses transformadas por amor de Colosas"
            if cap == 4:
                if p_num == 1: return "Perseverar piadosamente en oración ferviente colosense de Colosas"
                if p_num == 2: return "Saludos y comendaciones afectuosas finales colosenses de Colosas"

        # 1 Tesalonicenses
        if book == '1 Tesalonicenses':
            if cap == 1:
                if p_num == 1: return "Fe laboriosa y gran agradecimiento apostólico primer"
                if p_num == 2: return "Conversión sincera de ídolos a Dios vivo primer"
            if cap == 2:
                if p_num == 1: return "Pastoreo tierno del heraldo apostólico fiel primer"
                if p_num == 2: return "Gozar paciencia en persecution tesalonicense dura primer"
                if p_num == 3: return "Deseo ardiente por verlos de nuevo pronto primer"
            if cap == 3:
                if p_num == 1: return "Arraigo de fe y reporte de Timoteo primer"
            if cap == 4:
                if p_num == 1: return "Santificación sexual y amor comunitario santo primer"
                if p_num == 2: return "Parusía venidera y resurrección de santos primer"
            if cap == 5:
                if p_num == 1: return "Vigilar piadosamente como hijos del día virtuosos primer"
                if p_num == 2: return "Pautas litúrgicas y paz trinitaria final primer"

        # 2 Tesalonicenses
        if book == '2 Tesalonicenses':
            if cap == 1:
                if p_num == 1: return "Crecimiento de fe sorteando pruebas duras segundo"
                if p_num == 2: return "Manifestación gloriosa del juicio venidero divino segundo"
            if cap == 2:
                if p_num == 1: return "Apostasía profetizada e hijo de perdición rebelde segundo"
                if p_num == 2: return "Elegidos para salvacion por fe verdadera segundo"
            if cap == 3:
                if p_num == 1: return "Amor de Dios y pautas de conducta segundo"
                if p_num == 2: return "Deber ético del trabajo honrado diario segundo"

        # 1 Timoteo
        if book == '1 Timoteo':
            if cap == 1:
                if p_num == 1: return "Combatir firmemente doctrinas engañosas impías ajenas primer"
                if p_num == 2: return "Abundante misericordia de Dios con pecadores salvados primer"
                if p_num == 3: return "Milicia espiritual de buena conciencia ministerial primer"
            if cap == 2:
                if p_num == 1: return "Rogativas unánimes y orden litúrgico eclesial primer"
            if cap == 3:
                if p_num == 1: return "Requisitos éticos para obispos fieles pastores primer"
                if p_num == 2: return "Idoneidad moral exigible a diáconos santos primer"
                if p_num == 3: return "Gran misterio de piedad revelada celestial primer"
            if cap == 4:
                if p_num == 1: return "Advertencia de apostasía de fines tiempos primer"
                if p_num == 2: return "Ministro de piedad ejercitado en la doctrina primer"
            if cap == 5:
                if p_num == 1: return "Atención pastoral justa de viudas necesitadas primer"
                if p_num == 2: return "Honra a presbíteros y pautas de conducta primer"
            if cap == 6:
                if p_num == 1: return "Siervos fieles y males de la avaricia primer"
                if p_num == 2: return "Custodiar sagrado depósito de fe timoteana primer"

        # 2 Timoteo
        if book == '2 Timoteo':
            if cap == 1:
                if p_num == 1: return "Herencia familiar de devoción sincera fiel segundo"
                if p_num == 2: return "Fuerza espiritual libre de cobardía humana segundo"
                if p_num == 3: return "Fidelidad de Onesíforo confortador íntimo lejano segundo"
            if cap == 2:
                if p_num == 1: return "Soldados sufridos de disciplina militar bendita segundo"
                if p_num == 2: return "Obrero fiel trazando rectamente verdad divina segundo"
                if p_num == 3: return "Vivir santamente huyendo de pasiones mundanas segundo"
            if cap == 3:
                if p_num == 1: return "Carácter vil de hombres descarriados soberbios segundo"
                if p_num == 2: return "Sagradas escrituras inspiradas por Dios eternas segundo"
            if cap == 4:
                if p_num == 1: return "Solemnidad de predicar palabra oportuna siempre segundo"
                if p_num == 2: return "Combate acabado esperando corona de justicia segundo"
                if p_num == 3: return "Últimos encargos y saludos íntimos entrañables segundo"

        # Tito
        if book == 'Tito':
            if cap == 1:
                if p_num == 1: return "Criterios para ordenar presbíteros cretenses virtuosos"
                if p_num == 2: return "Censurar habladores de vanidades falsas destructivas"
            if cap == 2:
                if p_num == 1: return "Ética provechosa para diversas clases sociales"
                if p_num == 2: return "Gracia divina inspiradora de santidad moral"
            if cap == 3:
                if p_num == 1: return "Conducta virtuosa y bautismo de regeneración"
                if p_num == 2: return "Evitar disputas estériles sobre genealogías necias"

        # Filemón
        if book == 'Filemón':
            if p_num == 1: return "Fidelidad y gozo apostólico por amor de Filemón"
            if p_num == 2: return "Intercesión tierna por el esclavo evadido Onésimo"
            if p_num == 3: return "Solemne compromiso de hospedar al apóstol prisionero"

        # Hebreos
        if book == 'Hebreos':
            if cap == 1:
                if p_num == 1: return "Soberanía resplandeciente del Hijo creador eterno"
            if cap == 2:
                if p_num == 1: return "Desatención peligrosa del gran anuncio salvífico"
                if p_num == 2: return "Solidaridad de Cristo asumiendo carne mortal"
            if cap == 3:
                if p_num == 1: return "Jesucristo de gloria superior a Moisés libertador"
                if p_num == 2: return "Evitar endurecimiento e incredulidad rebelde desértica"
            if cap == 4:
                if p_num == 1: return "Descanso sabático celestial prometido a fieles"
                if p_num == 2: return "Escrutinio vivo de espada de verdad divina"
                if p_num == 3: return "Pontífice supremo compasivo de flaquezas humanas"
            if cap == 5:
                if p_num == 1: return "Señor ungido según orden Melquisedec excelso"
                if p_num == 2: return "Madurez rezagada y reclamo pastoral hebreo"
            if cap == 6:
                if p_num == 1: return "Desvarío infructuoso de apostasía irreversible mortal"
                if p_num == 2: return "Seguridad inamovible de promesa y juramento"
            if cap == 7:
                if p_num == 1: return "Rey misterioso de justicia y paz de la Restauración"
                if p_num == 2: return "Invalidez levítica y nuevo régimen celestial hebreo"
                if p_num == 3: return "Intercesión perpetua de sacerdote incansable excelso hebreos"
            if cap == 8:
                if p_num == 1: return "Sacerdocio en celestial tabernáculo real verdadero de Dios"
                if p_num == 2: return "Nuevo pacto supremo aboliente del anterior en Cristo"
            if cap == 9:
                if p_num == 1: return "Santuario mosaico de orden limitado temporal judío"
                if p_num == 2: return "Redención eterna por sangre ofrecida voluntaria de Cristo"
            if cap == 10:
                if p_num == 1: return "Obediencia perfecta superando sacrificios ineficaces hebreos de antaño"
                if p_num == 2: return "Acceso glorioso al lugar de santidad celestial sumo"
                if p_num == 3: return "Perseveras frente a tribulaciones y hostilidades duras del alma"
            if cap == 11:
                if p_num == 1: return "Heroicos ejemplos antiguos de fe victoriosa de ancestros"
                if p_num == 2: return "Patria celestial anhelada por patriarcas santos de antes"
                if p_num == 3: return "Soportar penalidades gozosos por fe divina de santos"
            if cap == 12:
                if p_num == 1: return "Correr carrera mirando al consumador Jesús de fe"
                if p_num == 2: return "Amor paternal educador con paciencia tierna divina"
                if p_num == 3: return "Asamblea celestial frente a temores Sinaí arcaico santo"
            if cap == 13:
                if p_num == 1: return "Amor hospitalidad y pureza de vida recta piadosa"
                if p_num == 2: return "Pautas jerárquicas y bendición celestial final hebrea"

        # Santiago
        if book == 'Santiago':
            if cap == 1:
                if p_num == 1: return "Gozo maduro en duraderas pruebas formativas de Santiago"
                if p_num == 2: return "Pedir fe sin dudar de riqueza vacía de Santiago"
                if p_num == 3: return "Naturaleza de tentaciones y bondad divina de Santiago"
                if p_num == 4: return "Oidores autoengañosos contra hacedores activos virtuosos de Santiago"
            if cap == 2:
                if p_num == 1: return "Misericordia superior contra distinción social mundana de Santiago"
                if p_num == 2: return "Fe inactiva desprovista de vida santificadora de Santiago"
            if cap == 3:
                if p_num == 1: return "Gobernar palabra con sabiduría celestial pura de Santiago"
            if cap == 4:
                if p_num == 1: return "Humildad ante Dios ahuyentando pasiones carnales de Santiago"
                if p_num == 2: return "Soberbia humana y juicio providencial divino de Santiago"
            if cap == 5:
                if p_num == 1: return "Advertencia a ricos opresores avaros despiadados de Santiago"
                if p_num == 2: return "Firmeza paciente de santa oración fervorosa de Santiago"

        # 1 Pedro
        if book == '1 Pedro':
            if cap == 1:
                if p_num == 1: return "Expatriados amados elegidos por presapiencia divina de Pedro"
                if p_num == 2: return "Gozosa herencia celestial inmarcesible fiel eterna de Pedro"
                if p_num == 3: return "Fe acrisolada por fuego purificador providencial de Pedro"
                if p_num == 4: return "Llamamiento solemne a santa conducta diaria de Pedro"
                if p_num == 5: return "Sangre inestimable santa del Cordero inmaculado de Pedro"
                if p_num == 6: return "Amor mutuo y semilla imperecedera celestial de Pedro"
            if cap == 2:
                if p_num == 1: return "Sacerdocio espiritual edificado de piedras vivas de Pedro"
                if p_num == 2: return "Linaje escogido proclamador de luz admirable de Pedro"
                if p_num == 3: return "Deber ciudadano asertivo en sociedad gentil de Pedro"
                if p_num == 4: return "Sufrimiento vicario de Salvador santo inocente de Pedro"
            if cap == 3:
                if p_num == 1: return "Comprensión conyugal santa en hogar cristiano de Pedro"
                if p_num == 2: return "Respuesta benévola ante hostilidad injusta mundana de Pedro"
                if p_num == 3: return "Triunfo pacificador del bautismo salvador divino de Pedro"
            if cap == 4:
                if p_num == 1: return "Vivir para luz abandonando paganismo inmundo de Pedro"
                if p_num == 2: return "Dones solícitos de caridad sobria fraterna de Pedro"
                if p_num == 3: return "Consolación dichosa en fuego de pruebas de Pedro"
            if cap == 5:
                if p_num == 1: return "Pastoreo ejemplar voluntario y humilde presbítero de Pedro"
                if p_num == 2: return "Resistencia en fe vigilando al adversario de Pedro"
                if p_num == 3: return "Despedida apostólica y santa bendición eterna de Pedro"

        # 2 Pedro
        if book == '2 Pedro':
            if cap == 1:
                if p_num == 1: return "Promesas excelentes y progreso espiritual pleno de Pedro"
                if p_num == 2: return "Previsión de partida corporal eminente apóstol de Pedro"
                if p_num == 3: return "Glorias de transfiguración y profecía segura de Pedro"
            if cap == 2:
                if p_num == 1: return "Destrucción traída por herejes encubiertos falsos de Pedro"
                if p_num == 2: return "Juicio severo de Dios preservando justos de Pedro"
                if p_num == 3: return "Vicio inmundo de embusteros licenciosos perdidos de Pedro"
            if cap == 3:
                if p_num == 1: return "Burla de impíos negando parusía prometida de Pedro"
                if p_num == 2: return "Arrepentimiento providencial esperando día celestial solemne de Pedro"
                if p_num == 3: return "Crecimiento perseverante en santidad fe gloriosa de Pedro"

        # 1 Juan
        if book == '1 Juan':
            if cap == 1:
                if p_num == 1: return "Manifestación ocular de Vida encarnada real de Juan"
                if p_num == 2: return "Andar transparentes en divina luz celestial de Juan"
            if cap == 2:
                if p_num == 1: return "Defensor justo ante trono celestial divino de Juan"
                if p_num == 2: return "Precepto santo de caridad fraterna sincera de Juan"
                if p_num == 3: return "Fugacidad de placeres de este mundo mundano de Juan"
                if p_num == 4: return "Unción santa preservando de anticristos mentirosos de Juan"
            if cap == 3:
                if p_num == 1: return "Adopción dilecta como hijos elegidos amados de Juan"
                if p_num == 2: return "Amor de hecho y verdad práctica de Juan"
                if p_num == 3: return "Corazón reconciliado libre ante Dios consolador de Juan"
            if cap == 4:
                if p_num == 1: return "Discernir espíritus de fuente verídica divina de Juan"
                if p_num == 2: return "Amor absoluto desterrando todo temor moral de Juan"
            if cap == 5:
                if p_num == 1: return "Fe victoriosa triunfante del mundo pecaminoso de Juan"
                if p_num == 2: return "Tragedia del pecado y tres testigos testimonios de Juan"
                if p_num == 3: return "Seguridad bendita de comunión real divina de Juan"

        # 2 Juan
        if book == '2 Juan':
            if p_num == 1: return "Fidelidad de elegida andando en amor santo de Juan"
            if p_num == 2: return "Guardarse de impostores herejes vagabundos mentirosos de Juan"

        # 3 Juan
        if book == '3 Juan':
            if p_num == 1: return "Hospitalidad benévola a fieles ministros viajeros de Juan"
            if p_num == 2: return "Altivez cismática de Diótrefes reprendida severamente de Juan"

        # Judas
        if book == 'Judas':
            if p_num == 1: return "Contender infatigablemente por fe apostólica entregada de Judas"
            if p_num == 2: return "Destrucción decretada contra impíos burladores carnales de Judas"
            if p_num == 3: return "Consejos piadosos de consoladora perseverancia cristiana de Judas"
            if p_num == 4: return "Solemne doxología a Salvador único celestial de Judas"
            
        return f"{clean_old} en {book} {cap}"

    for idx, line in enumerate(lines):
        if line.strip().startswith('## '):
            found_book = False
            for bname, matches in book_header_map:
                for match_term in matches:
                    if match_term in line:
                        current_book = bname
                        found_book = True
                        break
                if found_book: break
            if not found_book:
                print(f"WARNING: Unknown book format on line {idx}: {line}")
            output_lines.append(line)
        elif line.strip().startswith('### '):
            chapter_header = line.strip().replace('### ', '').strip()
            parts = chapter_header.rsplit(' ', 1)
            current_chapter = int(parts[1]) if len(parts) == 2 else 1
            output_lines.append(line)
        elif line.strip().startswith('|') and not line.strip().startswith('|:') and not 'Título' in line and not '#|' in line:
            row_parts = [p.strip() for p in line.strip().split('|')]
            if len(row_parts) >= 8:
                p_num = int(row_parts[1])
                old_title = row_parts[2]
                old_slug = row_parts[3]
                verse = row_parts[4]
                style = row_parts[5]
                event = row_parts[6]
                
                new_title = get_ultimate_title(current_book, current_chapter, p_num, old_title)
                
                book_slug = book_slug_map[current_book]
                new_slug = clean_slug(new_title, book_slug, current_chapter)
                
                rebalanced_row = f" | {p_num} | {new_title} | `{new_slug}` | {verse} | {style} | `{event}` |"
                output_lines.append(rebalanced_row)
            else:
                output_lines.append(line)
        else:
            output_lines.append(line)

    final_text = "\n".join(output_lines) + "\n"
    with open('docs/juego-del-cinco/plan-pericopas-nt-epistolas.md', 'w', encoding='utf-8') as f:
        f.write(final_text)

    print("Success! Perfect uniqueness achieved for plan-pericopas-nt-epistolas.md")

parse_and_refactor_in_place()
