# Plan de Perícopas — Evangelios del Nuevo Testamento (Fase D)

> Diseñado y curado meticulosamente siguiendo los criterios de [mapeo-pericopas.md](docs/juego-del-cinco/mapeo-pericopas.md).
> Fuente primaria: Concordancia entre los Evangelios (harmony table) en Alejandría + Jesús el Cristo (Talmage) + Manuales SUD.
> Granularidad fina en discursos largos (Sermón del Monte, Discurso del Aposento Alto).

## Leyendas y Convenciones

| Columna | Significado |
|:--------|:------------|
| # | Número de orden de la perícopa dentro del capítulo |
| Título | Título breve en español, sin referencia a libro, capítulo ni rango |
| Slug | Slug SEO-friendly unívoco para la taxonomía bc_pericopa (formato {libro}-{capitulo}-{slug-tematico}) |
| v | Rango de versículos (cobertura física estricta, disjunta y sin solapamiento) |
| Estilo | Género literario: narrativa, milagro, parábola, discurso, profecía, etc. |
| _evento_canonico | Identificador de evento canónico maestro para agrupar concordancias entre los 4 evangelios |
| _relacion_paralela | (Opcional) Relación con otras obras: sinoptico:, expansion:, cita:, etc. |

## Mapeo sinóptico (concordancia entre los 4 evangelios)

Cada evento compartido entre Mateo, Marcos, Lucas y/o Juan usa el mismo `_evento_canonico`. Para el mismo evento, el título es **idéntico** en los 4 evangelios (intencional para hacer evidentes las concordancias).

**Eventos con triple tradición (Mt-Mc-Lc)**: tipo A (testimonio múltiple sinóptico).
**Eventos propios de Juan**: sin concordancia sinóptica.
**Eventos propios de Mateo/Lucas**: perícopas únicas con `_evento_canonico` propio.

## Estilo editorial

**Regla de nombres**: títulos breves, gramaticalmente correctos, human-readable, sin referencia a libro/capítulo. Para el mismo evento en diferentes evangelios, el título es idéntico (mecanismo de concordancia).

**Estilos retóricos de los evangelios** (ejes de granularidad):
- **Narrativa**: relatos de hechos (curaciones, milagros, viajes)
- **Discurso/Enseñanza**: bloques de enseñanza de Jesús (Sermón del Monte, Olivo, Aposento Alto)
- **Parábola**: historias con analogía
- **Profecía**: anuncios escatológicos (Apocalipsis sinóptico, Olivo)
- **Genealogía/Prólogo**: introducciones (Mt 1, Lc 1, Jn 1)

---

## 1. Evangelio según San Mateo (28 capítulos)

### Mateo 1
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Genealogía de Jesucristo | mateo-1-genealogia-de-jesucristo | 1–17 | Genealogía | genealogia-jesus |
| 2 | José acepta a María como esposa | mateo-1-jose-acepta-a-maria-como-esposa | 18–25 | Narrativa | jose-acepta-maria |

### Mateo 2
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Los magos de oriente | mateo-2-los-magos-de-oriente | 1–12 | Narrativa | magos-oriente |
| 2 | La huida a Egipto | mateo-2-la-huida-a-egipto | 13–15 | Narrativa | huida-egipto |
| 3 | La matanza de los inocentes | mateo-2-la-matanza-de-los-inocentes | 16–18 | Narrativa | matanza-inocentes |
| 4 | El regreso de Egipto y Nazaret | mateo-2-el-regreso-de-egipto-y-nazaret | 19–23 | Narrativa | regreso-egipto-nazaret |

### Mateo 3
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Juan bautiza en el desierto | mateo-3-juan-bautiza-en-el-desierto | 1–6 | Narrativa | juan-bautista-predica |
| 2 | Juan predica arrepentimiento | mateo-3-juan-predica-arrepentimiento | 7–12 | Discurso | juan-bautista-predica |
| 3 | Juan bautiza a Jesús | mateo-3-juan-bautiza-a-jesus | 13–17 | Narrativa | bautismo-jesus |

### Mateo 4
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Tentación de Jesús en el desierto | mateo-4-tentacion-de-jesus-en-el-desierto | 1–11 | Narrativa | tentacion-jesus |
| 2 | Jesús comienza su ministerio en Galilea | mateo-4-jesus-comienza-su-ministerio-en-galilea | 12–17 | Narrativa | inicio-ministerio-galilea |
| 3 | Llamamiento de los primeros discípulos | mateo-4-llamamiento-de-los-primeros-discípulos | 18–22 | Narrativa | llamado-primeros-discípulos |
| 4 | Jesús enseña y sana por toda Galilea | mateo-4-jesus-ensena-y-sana-por-toda-galilea | 23–25 | Narrativa | ministerio-galilea-inicial |

### Mateo 5
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Bienaventuranzas y sal de la tierra | mateo-5-bienaventuranzas-y-sal-de-la-tierra | 1–16 | Discurso | sermon-del-monte |
| 2 | Jesús y la ley | mateo-5-jesus-y-la-ley | 17–20 | Discurso | sermon-del-monte |
| 3 | La ira y el homicidio | mateo-5-la-ira-y-el-homicidio | 21–26 | Discurso | sermon-del-monte |
| 4 | El adulterio y la lujuria | mateo-5-el-adulterio-y-la-lujuria | 27–30 | Discurso | sermon-del-monte |
| 5 | El divorcio | mateo-5-el-divorcio | 31–32 | Discurso | sermon-del-monte |
| 6 | Los juramentos | mateo-5-los-juramentos | 33–37 | Discurso | sermon-del-monte |
| 7 | Ojo por ojo y amor a enemigos | mateo-5-ojo-por-ojo-y-amor-a-enemigos | 38–48 | Discurso | sermon-del-monte |

### Mateo 6
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La limosna | mateo-6-la-limosna | 1–4 | Discurso | sermon-del-monte |
| 2 | La oración y el Padrenuestro | mateo-6-la-oracion-y-el-padrenuestro | 5–15 | Discurso | sermon-del-monte |
| 3 | El ayuno | mateo-6-el-ayuno | 16–18 | Discurso | sermon-del-monte |
| 4 | Tesoros en el cielo | mateo-6-tesoros-en-el-cielo | 19–24 | Discurso | sermon-del-monte |
| 5 | No os afanéis | mateo-6-no-os-afaneis | 25–34 | Discurso | sermon-del-monte |

### Mateo 7
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | No juzguéis | mateo-7-no-juzgueis | 1–6 | Discurso | sermon-del-monte |
| 2 | Pedid, buscad, llamad | mateo-7-pedid-buscad-llamad | 7–12 | Discurso | sermon-del-monte |
| 3 | La puerta estrecha | mateo-7-la-puerta-estrecha | 13–14 | Discurso | sermon-del-monte |
| 4 | Los falsos profetas | mateo-7-los-falsos-profetas | 15–23 | Discurso | sermon-del-monte |
| 5 | Los dos cimientos | mateo-7-los-dos-cimientos | 24–27 | Discurso | sermon-del-monte |
| 6 | Conclusión: la autoridad de Jesús | mateo-7-conclusion-la-autoridad-de-jesus | 28–29 | Discurso | sermon-del-monte |

### Mateo 8
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al leproso | mateo-8-jesus-sana-al-leproso | 1–4 | Milagro | jesus-sana-leproso |
| 2 | El siervo del centurión | mateo-8-el-siervo-del-centurion | 5–13 | Milagro | siervo-centurion |
| 3 | Jesús sana a la suegra de Pedro | mateo-8-jesus-sana-a-la-suegra-de-pedro | 14–17 | Milagro | jesus-sana-suegra-pedro |
| 4 | Las multitudes siguen a Jesús | mateo-8-las-multitudes-siguen-a-jesus | 18–22 | Narrativa | seguidores-multitud |
| 5 | Jesús calma la tempestad | mateo-8-jesus-calma-la-tempestad | 23–27 | Milagro | jesus-calma-tempestad |

### Mateo 9
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al paralítico | mateo-9-jesus-sana-al-paralitico | 1–8 | Milagro | jesus-sana-paralitico |
| 2 | Llamamiento de Mateo | mateo-9-llamamiento-de-mateo | 9–13 | Narrativa | llamado-mateo |
| 3 | La pregunta sobre el ayuno | mateo-9-la-pregunta-sobre-el-ayuno | 14–17 | Discurso | pregunta-ayuno |
| 4 | La hija de Jairo y la mujer con flujo | mateo-9-la-hija-de-jairo-y-la-mujer-con-flujo | 18–26 | Milagro | sanidad-hija-de-jairo |
| 5 | Jesús sana a dos ciegos | mateo-9-jesus-sana-a-dos-ciegos | 27–31 | Milagro | jesus-sana-dos-ciegos |
| 6 | Jesús sana al mudo endemoniado | mateo-9-jesus-sana-al-mudo-endemoniado | 32–34 | Milagro | jesus-sana-mudo-endemoniado |

### Mateo 10
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús envía a los doce apóstoles | mateo-10-jesus-envia-a-los-doce-apostoles | 1–15 | Discurso | envio-apostoles |
| 2 | Persecuciones venideras | mateo-10-persecuciones-venideras | 16–23 | Discurso | envio-apostoles |
| 3 | Confiar en Dios, no en el hombre | mateo-10-confiar-en-dios-no-en-el-hombre | 24–33 | Discurso | envio-apostoles |
| 4 | La espada de Jesús | mateo-10-la-espada-de-jesus | 34–39 | Discurso | envio-apostoles |
| 5 | Recompensa al que recibe a los discípulos | mateo-10-recompensa-al-que-recibe-a-los-discipulos | 40–42 | Discurso | envio-apostoles |

### Mateo 11
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Pregunta de Juan el Bautista | mateo-11-pregunta-de-juan-el-bautista | 1–6 | Narrativa | pregunta-juan-bautista |
| 2 | Jesús elogia a Juan el Bautista | mateo-11-jesus-elogia-a-juan-el-bautista | 7–15 | Discurso | elogio-juan-bautista |
| 3 | Ay de las ciudades impenitentes | mateo-11-ay-de-las-ciudades-impenitentes | 16–24 | Discurso | ciudades-impenitentes |
| 4 | Venid a mí todos los cansados | mateo-11-venid-a-mi-todos-los-cansados | 25–30 | Discurso | venid-a-mi |

### Mateo 12
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Controversia sobre el sábado: las espigas | mateo-12-controversia-sobre-el-sabado-las-espigas | 1–8 | Controversia | controversia-sabado-espigas |
| 2 | Controversia: el hombre de la mano seca | mateo-12-controversia-el-hombre-de-la-mano-seca | 9–14 | Controversia | controversia-mano-seca |
| 3 | El Siervo de Dios escogido | mateo-12-el-siervo-de-dios-escogido | 15–21 | Narrativa | siervo-dios-escogido |
| 4 | Jesús y Beelzebú | mateo-12-jesus-y-beelzebu | 22–32 | Controversia | jesus-beelzebu |
| 5 | El árbol y sus frutos | mateo-12-el-arbol-y-sus-frutos | 33–37 | Discurso | arbol-frutos |
| 6 | La señal de Jonás | mateo-12-la-senal-de-jonas | 38–45 | Discurso | senal-jonas |
| 7 | La madre y los hermanos de Jesús | mateo-12-la-madre-y-los-hermanos-de-jesus | 46–50 | Narrativa | madre-hermanos-jesus |

### Mateo 13
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola del sembrador | mateo-13-parabola-del-sembrador | 1–9 | Parábola | parabola-sembrador |
| 2 | Propósito de las parábolas | mateo-13-proposito-de-las-parabolas | 10–17 | Discurso | proposito-parabolas |
| 3 | Parábola del sembrador explicada | mateo-13-parabola-del-sembrador-explicada | 18–23 | Parábola | parabola-sembrador |
| 4 | Parábola del trigo y la cizaña | mateo-13-parabola-del-trigo-y-la-cizana | 24–30 | Parábola | parabola-trigo-cizana |
| 5 | Parábola del grano de mostaza | mateo-13-parabola-del-grano-de-mostaza | 31–32 | Parábola | parabola-grano-mostaza |
| 6 | Parábola de la levadura | mateo-13-parabola-de-la-levadura | 33 | Parábola | parabola-levadura |
| 7 | Cumplimiento de las profecías | mateo-13-cumplimiento-de-las-profecías | 34–35 | Narrativa | cumplimiento-profecías |
| 8 | Parábola del trigo y cizaña explicada | mateo-13-parabola-del-trigo-y-cizana-explicada | 36–43 | Parábola | parabola-trigo-cizana |
| 9 | Parábola del tesoro escondido | mateo-13-parabola-del-tesoro-escondido | 44 | Parábola | parabola-tesoro-escondido |
| 10 | Parábola de la perla de gran precio | mateo-13-parabola-de-la-perla-de-gran-precio | 45–46 | Parábola | parabola-perla |
| 11 | Parábola de la red | mateo-13-parabola-de-la-red | 47–52 | Parábola | parabola-red |
| 12 | Jesús en Nazaret de nuevo | mateo-13-jesus-en-nazaret-de-nuevo | 53–58 | Narrativa | jesus-nazaret-segunda |

### Mateo 14
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Muerte de Juan el Bautista | mateo-14-muerte-de-juan-el-bautista | 1–12 | Narrativa | muerte-juan-bautista |
| 2 | Alimentación de los cinco mil | mateo-14-alimentacion-de-los-cinco-mil | 13–21 | Milagro | alimentacion-cinco-mil |
| 3 | Jesús anda sobre el agua | mateo-14-jesus-anda-sobre-el-agua | 22–33 | Milagro | jesus-anda-sobre-agua |
| 4 | Jesús sana en Genesaret | mateo-14-jesus-sana-en-genesaret | 34–36 | Milagro | jesus-sana-genesaret |

### Mateo 15
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Lo que contamina al hombre | mateo-15-lo-que-contamina-al-hombre | 1–20 | Discurso | contaminacion-interior |
| 2 | La mujer cananea | mateo-15-la-mujer-cananea | 21–28 | Milagro | jesus-mujer-cananea |
| 3 | Jesús sana a muchos junto al lago | mateo-15-jesus-sana-a-muchos-junto-al-lago | 29–31 | Milagro | curaciones-multitud-lago |
| 4 | Alimentación de los cuatro mil | mateo-15-alimentacion-de-los-cuatro-mil | 32–39 | Milagro | alimentacion-cuatro-mil |

### Mateo 16
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La señal del cielo | mateo-16-la-senal-del-cielo | 1–4 | Discurso | senal-cielo |
| 2 | La levadura de los fariseos | mateo-16-la-levadura-de-los-fariseos | 5–12 | Discurso | levaduras-fariseos |
| 3 | Confesión de Pedro | mateo-16-confesion-de-pedro | 13–20 | Narrativa | confesion-pedro |
| 4 | Jesús anuncia su muerte | mateo-16-jesus-anuncia-su-muerte | 21–23 | Discurso | anuncio-muerte-primera |
| 5 | El reino y el discipulado | mateo-16-el-reino-y-el-discipulado | 24–28 | Discurso | discipulado-costo |

### Mateo 17
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La transfiguración | mateo-17-la-transfiguracion | 1–13 | Narrativa | transfiguracion |
| 2 | Jesús sana al muchacho epiléptico | mateo-17-jesus-sana-al-muchacho-epileptico | 14–21 | Milagro | jesus-sana-epileptico |
| 3 | Jesús anuncia de nuevo su muerte | mateo-17-jesus-anuncia-de-nuevo-su-muerte | 22–23 | Discurso | anuncio-muerte-segunda |
| 4 | La moneda en la boca del pez | mateo-17-la-moneda-en-la-boca-del-pez | 24–27 | Milagro | moneda-pez-templo |

### Mateo 18
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El mayor en el reino | mateo-18-el-mayor-en-el-reino | 1–6 | Discurso | mayor-reino |
| 2 | El escándalo y la mano que tropieza | mateo-18-el-escandalo-y-la-mano-que-tropieza | 7–9 | Discurso | escandalo-tropiezo |
| 3 | La oveja perdida | mateo-18-la-oveja-perdida | 10–14 | Parábola | parabola-oveja-perdida |
| 4 | Corrección fraterna | mateo-18-correccion-fraterna | 15–20 | Discurso | correccion-fraterna |
| 5 | Parábola del siervo sin perdón | mateo-18-parabola-del-siervo-sin-perdon | 21–35 | Parábola | parabola-siervo-deudor |

### Mateo 19
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El divorcio | mateo-19-el-divorcio | 1–12 | Discurso | ensenanza-divorcio |
| 2 | Jesús y los niños | mateo-19-jesus-y-los-ninos | 13–15 | Narrativa | jesus-bendice-ninos |
| 3 | El joven rico | mateo-19-el-joven-rico | 16–22 | Narrativa | joven-rico |
| 4 | El peligro de las riquezas | mateo-19-el-peligro-de-las-riquezas | 23–30 | Discurso | peligro-riquezas |

### Mateo 20
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola de los trabajadores de la viña | mateo-20-parabola-de-los-trabajadores-de-la-vina | 1–16 | Parábola | parabola-trabajadores-vina |
| 2 | Jesús anuncia su muerte por tercera vez | mateo-20-jesus-anuncia-su-muerte-por-tercera-vez | 17–19 | Discurso | anuncio-muerte-tercera |
| 3 | La madre de los hijos de Zebedeo | mateo-20-la-madre-de-los-hijos-de-zebedeo | 20–28 | Narrativa | madre-hijos-zebedeo |
| 4 | Jesús sana a dos ciegos en Jericó | mateo-20-jesus-sana-a-dos-ciegos-en-jerico | 29–34 | Milagro | jesus-sana-ciegos-jerico |

### Mateo 21
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La entrada triunfal en Jerusalén | mateo-21-la-entrada-triunfal-en-jerusalen | 1–11 | Narrativa | entrada-jerusalen |
| 2 | Jesús purifica el templo | mateo-21-jesus-purifica-el-templo | 12–17 | Narrativa | purificacion-templo |
| 3 | La higuera estéril | mateo-21-la-higuera-esteril | 18–22 | Milagro | higuera-esteril |
| 4 | La autoridad de Jesús cuestionada | mateo-21-la-autoridad-de-jesus-cuestionada | 23–27 | Controversia | autoridad-jesus-cuestionada |
| 5 | Parábola de los dos hijos | mateo-21-parabola-de-los-dos-hijos | 28–32 | Parábola | parabola-dos-hijos |
| 6 | Parábola de los labradores malvados | mateo-21-parabola-de-los-labradores-malvados | 33–46 | Parábola | parabola-labradores-malvados |
| 7 | Parábola de las bodas reales | mateo-21-parabola-de-las-bodas-reales | 45–46 | Parábola | parabola-bodas-reales |

### Mateo 22
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola de las bodas (continuación) | mateo-22-parabola-de-las-bodas-continuacion | 1–14 | Parábola | parabola-bodas-reales |
| 2 | Tributo al César | mateo-22-tributo-al-cesar | 15–22 | Controversia | tributo-cesar |
| 3 | Pregunta sobre la resurrección | mateo-22-pregunta-sobre-la-resurreccion | 23–33 | Controversia | pregunta-resurreccion |
| 4 | El gran mandamiento | mateo-22-el-gran-mandamiento | 34–40 | Controversia | gran-mandamiento |
| 5 | ¿De quién es hijo el Cristo? | mateo-22-de-quien-es-hijo-el-cristo | 41–46 | Controversia | hijo-david |

### Mateo 23
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Ay de los escribas y fariseos | mateo-23-ay-de-los-escribas-y-fariseos | 1–36 | Discurso | ay-fariseos |
| 2 | Lamento sobre Jerusalén | mateo-23-lamento-sobre-jerusalen | 37–39 | Discurso | lamento-jerusalen |

### Mateo 24
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Profecía de la destrucción del templo | mateo-24-profecia-de-la-destruccion-del-templo | 1–2 | Profecía | destruccion-templo |
| 2 | Señales del fin de los tiempos | mateo-24-senales-del-fin-de-los-tiempos | 3–14 | Profecía | discurso-olio |
| 3 | La gran tribulación | mateo-24-la-gran-tribulacion | 15–28 | Profecía | discurso-olio |
| 4 | La venida del Hijo del Hombre | mateo-24-la-venida-del-hijo-del-hombre | 29–35 | Profecía | discurso-olio |
| 5 | Parábola del ladrón nocturno | mateo-24-parabola-del-ladron-nocturno | 36–44 | Parábola | parabola-ladron-nocturno |
| 6 | El siervo fiel y el siervo malo | mateo-24-el-siervo-fiel-y-el-siervo-malo | 45–51 | Parábola | siervo-fiel-malo |

### Mateo 25
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola de las diez vírgenes | mateo-25-parabola-de-las-diez-virgenes | 1–13 | Parábola | parabola-diez-virgenes |
| 2 | Parábola de los talentos | mateo-25-parabola-de-los-talentos | 14–30 | Parábola | parabola-talentos |
| 3 | El juicio de las naciones | mateo-25-el-juicio-de-las-naciones | 31–46 | Profecía | juicio-naciones |

### Mateo 26
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La conspiración para matar a Jesús | mateo-26-la-conspiracion-para-matar-a-jesus | 1–5 | Narrativa | conspiracion-pascua |
| 2 | La unción en Betania | mateo-26-la-uncion-en-betania | 6–13 | Narrativa | uncion-betania |
| 3 | La traición de Judas | mateo-26-la-traicion-de-judas | 14–16 | Narrativa | traicion-judas |
| 4 | La Última Cena | mateo-26-la-ultima-cena | 17–30 | Narrativa | pascua-redencion |
| 5 | Jesús predice la negación de Pedro | mateo-26-jesus-predice-la-negacion-de-pedro | 31–35 | Narrativa | predice-negacion-pedro |
| 6 | Getsemaní | mateo-26-getsemani | 36–46 | Narrativa | Getsemaní |
| 7 | El arresto de Jesús | mateo-26-el-arresto-de-jesus | 47–56 | Narrativa | arresto-jesus |
| 8 | Jesús ante el concilio | mateo-26-jesus-ante-el-concilio | 57–68 | Narrativa | juicio-sanedrin |
| 9 | La negación de Pedro | mateo-26-la-negacion-de-pedro | 69–75 | Narrativa | negacion-pedro |

### Mateo 27
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Judas se arrepiente y se ahorca | mateo-27-judas-se-arrepiente-y-se-ahorca | 1–10 | Narrativa | muerte-judas |
| 2 | Jesús ante Pilato | mateo-27-jesus-ante-pilato | 11–26 | Narrativa | juicio-pilato |
| 3 | Jesús azotado y entregado | mateo-27-jesus-azotado-y-entregado | 27–32 | Narrativa | jesus-azotado |
| 4 | La crucifixión | mateo-27-la-crucifixion | 33–44 | Narrativa | crucifixion-jesus |
| 5 | La muerte de Jesús | mateo-27-la-muerte-de-jesus | 45–56 | Narrativa | muerte-jesus |
| 6 | La sepultura de Jesús | mateo-27-la-sepultura-de-jesus | 57–61 | Narrativa | sepultura-jesus |
| 7 | El sello y la guardia | mateo-27-el-sello-y-la-guardia | 62–66 | Narrativa | sello-tumba |

### Mateo 28
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La resurrección de Jesús | mateo-28-la-resurreccion-de-jesus | 1–10 | Narrativa | resurreccion-jesus |
| 2 | El soborno de los guardias | mateo-28-el-soborno-de-los-guardias | 11–15 | Narrativa | soborno-guardias |
| 3 | La gran comisión | mateo-28-la-gran-comision | 16–20 | Narrativa | gran-comision |

---

## 2. Evangelio según San Marcos (16 capítulos)


## 2. Evangelio según San Marcos (16 capítulos)

### Marcos 1
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Predicación de Juan el Bautista | marcos-1-predicacion-de-juan-el-bautista | 1–8 | Discurso | juan-bautista-predica |
| 2 | Bautismo de Jesús | marcos-1-bautismo-de-jesus | 9–11 | Narrativa | bautismo-jesus |
| 3 | Tentación de Jesús | marcos-1-tentacion-de-jesus | 12–13 | Narrativa | tentacion-jesus |
| 4 | Jesús comienza su ministerio en Galilea | marcos-1-jesus-comienza-su-ministerio-en-galilea | 14–15 | Narrativa | inicio-ministerio-galilea |
| 5 | Llamamiento de los primeros discípulos | marcos-1-llamamiento-de-los-primeros-discipulos | 16–20 | Narrativa | llamado-primeros-discípulos |
| 6 | Jesús enseña y expulsa demonio en Capernaúm | marcos-1-jesus-ensena-y-expulsa-demonio-en-capernaum | 21–28 | Milagro | expulsion-demonio-capernaum |
| 7 | Jesús sana a la suegra de Pedro | marcos-1-jesus-sana-a-la-suegra-de-pedro | 29–31 | Milagro | jesus-sana-suegra-pedro |
| 8 | Jesús sana a muchos al atardecer | marcos-1-jesus-sana-a-muchos-al-atardecer | 32–34 | Milagro | curaciones-multitud-anochecer |
| 9 | Jesús predica en Galilea | marcos-1-jesus-predica-en-galilea | 35–39 | Narrativa | predicacion-galilea |
| 10 | Jesús sana al leproso | marcos-1-jesus-sana-al-leproso | 40–45 | Milagro | jesus-sana-leproso |

### Marcos 2
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al paralítico | marcos-2-jesus-sana-al-paralitico | 1–12 | Milagro | jesus-sana-paralitico |
| 2 | Llamamiento de Leví (Mateo) | marcos-2-llamamiento-de-levi-mateo | 13–17 | Narrativa | llamado-mateo |
| 3 | La pregunta sobre el ayuno | marcos-2-la-pregunta-sobre-el-ayuno | 18–22 | Discurso | pregunta-ayuno |
| 4 | Controversia sobre el sábado: las espigas | marcos-2-controversia-sobre-el-sabado-las-espigas | 23–28 | Controversia | controversia-sabado-espigas |

### Marcos 3
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Controversia: el hombre de la mano seca | marcos-3-controversia-el-hombre-de-la-mano-seca | 1–6 | Controversia | controversia-mano-seca |
| 2 | Las multitudes siguen a Jesús | marcos-3-las-multitudes-siguen-a-jesus | 7–12 | Narrativa | seguidores-multitud |
| 3 | Elección de los doce apóstoles | marcos-3-eleccion-de-los-doce-apostoles | 13–19 | Narrativa | eleccion-apostoles |
| 4 | Jesús y Beelzebú (familia interrumpiendo) | marcos-3-jesus-y-beelzebu-familia-interrumpiendo | 20–35 | Controversia | jesus-beelzebu |

### Marcos 4
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola del sembrador | marcos-4-parabola-del-sembrador | 1–9 | Parábola | parabola-sembrador |
| 2 | Propósito de las parábolas | marcos-4-proposito-de-las-parabolas | 10–12 | Discurso | proposito-parabolas |
| 3 | Parábola del sembrador explicada | marcos-4-parabola-del-sembrador-explicada | 13–20 | Parábola | parabola-sembrador |
| 4 | Parábola del candelabro | marcos-4-parabola-del-candelabro | 21–25 | Parábola | parabola-candelabro |
| 5 | Parábola del grano que crece | marcos-4-parabola-del-grano-que-crece | 26–29 | Parábola | parabola-grano-crece |
| 6 | Parábola del grano de mostaza | marcos-4-parabola-del-grano-de-mostaza | 30–34 | Parábola | parabola-grano-mostaza |
| 7 | Jesús calma la tempestad | marcos-4-jesus-calma-la-tempestad | 35–41 | Milagro | jesus-calma-tempestad |

### Marcos 5
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al endemoniado gadareno | marcos-5-jesus-sana-al-endemoniado-gadareno | 1–20 | Milagro | endemoniado-gadareno |
| 2 | Jairo busca a Jesús (inicio sandwich) | marcos-5-jairo-busca-a-jesus-inicio-sandwich | 21–24 | Narrativa | sanidad-hija-de-jairo |
| 3 | La mujer con flujo de sangre (intercalación) | marcos-5-la-mujer-con-flujo-de-sangre-intercalacion | 25–34 | Milagro | mujer-flujo-sangre |
| 4 | Jesús resucita a la hija de Jairo (cierre) | marcos-5-jesus-resucita-a-la-hija-de-jairo-cierre | 35–43 | Milagro | sanidad-hija-de-jairo |

### Marcos 6
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús en Nazaret de nuevo | marcos-6-jesus-en-nazaret-de-nuevo | 1–6 | Narrativa | jesus-nazaret-segunda |
| 2 | Jesús envía a los doce | marcos-6-jesus-envia-a-los-doce | 7–13 | Discurso | envio-apostoles |
| 3 | Muerte de Juan el Bautista (Herodes) | marcos-6-muerte-de-juan-el-bautista-herodes | 14–29 | Narrativa | muerte-juan-bautista |
| 4 | Alimentación de los cinco mil | marcos-6-alimentacion-de-los-cinco-mil | 30–44 | Milagro | alimentacion-cinco-mil |
| 5 | Jesús anda sobre el agua | marcos-6-jesus-anda-sobre-el-agua | 45–56 | Milagro | jesus-anda-sobre-agua |

### Marcos 7
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Lo que contamina al hombre | marcos-7-lo-que-contamina-al-hombre | 1–23 | Discurso | contaminacion-interior |
| 2 | La mujer sirofenicia | marcos-7-la-mujer-sirofenicia | 24–30 | Milagro | jesus-mujer-cananea |
| 3 | Jesús sana al sordomudo | marcos-7-jesus-sana-al-sordomudo | 31–37 | Milagro | jesus-sana-sordomudo |

### Marcos 8
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Alimentación de los cuatro mil | marcos-8-alimentacion-de-los-cuatro-mil | 1–10 | Milagro | alimentacion-cuatro-mil |
| 2 | La levadura de los fariseos | marcos-8-la-levadura-de-los-fariseos | 11–21 | Discurso | levaduras-fariseos |
| 3 | Jesús sana al ciego de Betsaida | marcos-8-jesus-sana-al-ciego-de-betsaida | 22–26 | Milagro | jesus-sana-ciego-betsaida |
| 4 | Confesión de Pedro | marcos-8-confesion-de-pedro | 27–30 | Narrativa | confesion-pedro |
| 5 | Jesús anuncia su muerte | marcos-8-jesus-anuncia-su-muerte | 31–33 | Discurso | anuncio-muerte-primera |
| 6 | El discipulado y la cruz | marcos-8-el-discipulado-y-la-cruz | 34–38 | Discurso | discipulado-costo |

### Marcos 9
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La transfiguración | marcos-9-la-transfiguracion | 1–13 | Narrativa | transfiguracion |
| 2 | Jesús sana al muchacho epiléptico | marcos-9-jesus-sana-al-muchacho-epileptico | 14–29 | Milagro | jesus-sana-epileptico |
| 3 | Jesús anuncia de nuevo su muerte | marcos-9-jesus-anuncia-de-nuevo-su-muerte | 30–32 | Discurso | anuncio-muerte-segunda |
| 4 | El mayor en el reino | marcos-9-el-mayor-en-el-reino | 33–37 | Discurso | mayor-reino |
| 5 | El que no es contra nosotros | marcos-9-el-que-no-es-contra-nosotros | 38–50 | Discurso | no-contra-nosotros |

### Marcos 10
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El matrimonio y el divorcio | marcos-10-el-matrimonio-y-el-divorcio | 1–12 | Discurso | ensenanza-divorcio |
| 2 | Jesús y los niños | marcos-10-jesus-y-los-ninos | 13–16 | Narrativa | jesus-bendice-ninos |
| 3 | El joven rico | marcos-10-el-joven-rico | 17–22 | Narrativa | joven-rico |
| 4 | El peligro de las riquezas | marcos-10-el-peligro-de-las-riquezas | 23–31 | Discurso | peligro-riquezas |
| 5 | Jesús anuncia su muerte por tercera vez | marcos-10-jesus-anuncia-su-muerte-por-tercera-vez | 32–34 | Discurso | anuncio-muerte-tercera |
| 6 | Los hijos de Zebedeo | marcos-10-los-hijos-de-zebedeo | 35–45 | Narrativa | madre-hijos-zebedeo |
| 7 | Jesús sana al ciego Bartimeo | marcos-10-jesus-sana-al-ciego-bartimeo | 46–52 | Milagro | jesus-sana-ciegos-jerico |

### Marcos 11
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La entrada triunfal en Jerusalén | marcos-11-la-entrada-triunfal-en-jerusalen | 1–11 | Narrativa | entrada-jerusalen |
| 2 | La higuera estéril (inicio sandwich) | marcos-11-la-higuera-esteril-inicio-sandwich | 12–14 | Milagro | higuera-esteril |
| 3 | Jesús purifica el templo (intercalación) | marcos-11-jesus-purifica-el-templo-intercalacion | 15–18 | Narrativa | purificacion-templo |
| 4 | La higuera seca y la oración (cierre) | marcos-11-la-higuera-seca-y-la-oracion-cierre | 19–26 | Narrativa | higuera-esteril |
| 5 | La autoridad de Jesús cuestionada | marcos-11-la-autoridad-de-jesus-cuestionada | 27–33 | Controversia | autoridad-jesus-cuestionada |

### Marcos 12
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola de los labradores malvados | marcos-12-parabola-de-los-labradores-malvados | 1–12 | Parábola | parabola-labradores-malvados |
| 2 | Tributo al César | marcos-12-tributo-al-cesar | 13–17 | Controversia | tributo-cesar |
| 3 | Pregunta sobre la resurrección | marcos-12-pregunta-sobre-la-resurreccion | 18–27 | Controversia | pregunta-resurreccion |
| 4 | El gran mandamiento | marcos-12-el-gran-mandamiento | 28–34 | Controversia | gran-mandamiento |
| 5 | ¿De quién es hijo el Cristo? | marcos-12-de-quien-es-hijo-el-cristo | 35–37 | Controversia | hijo-david |
| 6 | Denuncia a los escribas | marcos-12-denuncia-a-los-escribas | 38–40 | Discurso | ay-fariseos |
| 7 | La ofrenda de la viuda | marcos-12-la-ofrenda-de-la-viuda | 41–44 | Narrativa | ofrenda-viuda |

### Marcos 13
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Profecía de la destrucción del templo | marcos-13-profecia-de-la-destruccion-del-templo | 1–2 | Profecía | destruccion-templo |
| 2 | Señales del fin de los tiempos | marcos-13-senales-del-fin-de-los-tiempos | 3–13 | Profecía | discurso-olio |
| 3 | La gran tribulación | marcos-13-la-gran-tribulacion | 14–23 | Profecía | discurso-olio |
| 4 | La venida del Hijo del Hombre | marcos-13-la-venida-del-hijo-del-hombre | 24–27 | Profecía | discurso-olio |
| 5 | Parábola de la higuera | marcos-13-parabola-de-la-higuera | 28–37 | Parábola | parabola-higuera-olio |

### Marcos 14
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La conspiración para matar a Jesús | marcos-14-la-conspiracion-para-matar-a-jesus | 1–2 | Narrativa | conspiracion-pascua |
| 2 | La unción en Betania | marcos-14-la-uncion-en-betania | 3–11 | Narrativa | uncion-betania |
| 2 | La Última Cena | marcos-14-la-ultima-cena | 12–26 | Narrativa | pascua-redencion |
| 3 | Jesús predice la negación de Pedro | marcos-14-jesus-predice-la-negacion-de-pedro | 27–31 | Narrativa | predice-negacion-pedro |
| 4 | Getsemaní | marcos-14-getsemani | 32–42 | Narrativa | Getsemaní |
| 5 | El arresto de Jesús | marcos-14-el-arresto-de-jesus | 43–52 | Narrativa | arresto-jesus |
| 6 | Jesús ante el concilio | marcos-14-jesus-ante-el-concilio | 53–65 | Narrativa | juicio-sanedrin |
| 7 | La negación de Pedro | marcos-14-la-negacion-de-pedro | 66–72 | Narrativa | negacion-pedro |

### Marcos 15
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús ante Pilato | marcos-15-jesus-ante-pilato | 1–15 | Narrativa | juicio-pilato |
| 2 | Jesús azotado y burlado | marcos-15-jesus-azotado-y-burlado | 16–20 | Narrativa | jesus-azotado |
| 3 | La crucifixión | marcos-15-la-crucifixion | 21–32 | Narrativa | crucifixion-jesus |
| 4 | La muerte de Jesús | marcos-15-la-muerte-de-jesus | 33–41 | Narrativa | muerte-jesus |
| 5 | La sepultura de Jesús | marcos-15-la-sepultura-de-jesus | 42–47 | Narrativa | sepultura-jesus |

### Marcos 16
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La resurrección de Jesús | marcos-16-la-resurreccion-de-jesus | 1–8 | Narrativa | resurreccion-jesus |
| 2 | Jesús se aparece a María Magdalena | marcos-16-jesus-se-aparece-a-maria-magdalena | 9–11 | Narrativa | aparicion-maria-magdalena |
| 3 | Jesús se aparece a los dos discípulos | marcos-16-jesus-se-aparece-a-los-dos-discipulos | 12–13 | Narrativa | camino-emaus |
| 4 | La gran comisión | marcos-16-la-gran-comision | 14–18 | Narrativa | gran-comision |
| 5 | La ascensión | marcos-16-la-ascension | 19–20 | Narrativa | ascension-jesus |

---

## 3. Evangelio según San Lucas (24 capítulos)

### Lucas 1
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Prólogo del evangelio | lucas-1-prologo-del-evangelio | 1–4 | Prólogo | prologo-lucas |
| 2 | Anunciación a Zacarías | lucas-1-anunciacion-a-zacarias | 5–25 | Narrativa | anunciacion-zacarias |
| 3 | Anunciación a María | lucas-1-anunciacion-a-maria | 26–38 | Narrativa | anunciacion-maria |
| 4 | María visita a Elisabet | lucas-1-maria-visita-a-elisabet | 39–56 | Narrativa | visita-maria-elisabet |
| 5 | Nacimiento de Juan el Bautista | lucas-1-nacimiento-de-juan-el-bautista | 57–80 | Narrativa | nacimiento-juan-bautista |

### Lucas 2
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Nacimiento de Jesús | lucas-2-nacimiento-de-jesus | 1–7 | Narrativa | nacimiento-jesus |
| 2 | Los pastores y los ángeles | lucas-2-los-pastores-y-los-angeles | 8–20 | Narrativa | pastores-angeles |
| 3 | La presentación en el templo | lucas-2-la-presentacion-en-el-templo | 21–38 | Narrativa | presentacion-templo |
| 4 | El niño Jesús en el templo | lucas-2-el-nino-jesus-en-el-templo | 41–52 | Narrativa | nino-jesus-templo |

### Lucas 3
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Predicación de Juan el Bautista | lucas-3-predicacion-de-juan-el-bautista | 1–18 | Discurso | juan-bautista-predica |
| 2 | Juan bautiza a Jesús | lucas-3-juan-bautiza-a-jesus | 21–22 | Narrativa | bautismo-jesus |
| 3 | Genealogía de Jesús | lucas-3-genealogia-de-jesus | 23–38 | Genealogía | genealogia-jesus |

### Lucas 4
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Tentación de Jesús | lucas-4-tentacion-de-jesus | 1–13 | Narrativa | tentacion-jesus |
| 2 | Jesús en Nazaret (rechazo) | lucas-4-jesus-en-nazaret-rechazo | 14–30 | Narrativa | jesus-nazaret-original |
| 3 | Jesús en Capernaúm | lucas-4-jesus-en-capernaum | 31–37 | Narrativa | ministerio-capernaum-inicial |
| 4 | Jesús sana a la suegra de Pedro | lucas-4-jesus-sana-a-la-suegra-de-pedro | 38–39 | Milagro | jesus-sana-suegra-pedro |
| 5 | Jesús sana a muchos | lucas-4-jesus-sana-a-muchos | 40–44 | Milagro | curaciones-multitud-anochecer |

### Lucas 5
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La pesca milagrosa | lucas-5-la-pesca-milagrosa | 1–11 | Milagro | pesca-milagrosa |
| 2 | Jesús sana al leproso | lucas-5-jesus-sana-al-leproso | 12–16 | Milagro | jesus-sana-leproso |
| 3 | Jesús sana al paralítico | lucas-5-jesus-sana-al-paralitico | 17–26 | Milagro | jesus-sana-paralitico |
| 4 | Llamamiento de Leví (Mateo) | lucas-5-llamamiento-de-levi-mateo | 27–32 | Narrativa | llamado-mateo |
| 5 | La pregunta sobre el ayuno | lucas-5-la-pregunta-sobre-el-ayuno | 33–39 | Discurso | pregunta-ayuno |

### Lucas 6
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Controversia sobre el sábado: las espigas | lucas-6-controversia-sobre-el-sabado-las-espigas | 1–5 | Controversia | controversia-sabado-espigas |
| 2 | Controversia: el hombre de la mano seca | lucas-6-controversia-el-hombre-de-la-mano-seca | 6–11 | Controversia | controversia-mano-seca |
| 3 | Elección de los doce apóstoles | lucas-6-eleccion-de-los-doce-apostoles | 12–16 | Narrativa | eleccion-apostoles |
| 4 | El Sermón del Plano (Bienaventuranzas) | lucas-6-el-sermon-del-plano-bienaventuranzas | 17–26 | Discurso | sermon-del-plano |
| 5 | Amar a los enemigos (Sermón del Plano) | lucas-6-amar-a-los-enemigos-sermon-del-plano | 27–38 | Discurso | sermon-del-plano |
| 6 | Juzgar y dar fruto (Sermón del Plano) | lucas-6-juzgar-y-dar-fruto-sermon-del-plano | 39–49 | Discurso | sermon-del-plano |

### Lucas 7
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El siervo del centurión | lucas-7-el-siervo-del-centurion | 1–10 | Milagro | siervo-centurion |
| 2 | Jesús resucita al hijo de la viuda de Naín | lucas-7-jesus-resucita-al-hijo-de-la-viuda-de-nain | 11–17 | Milagro | resureccion-hijo-viuda-nain |
| 3 | Pregunta de Juan el Bautista | lucas-7-pregunta-de-juan-el-bautista | 18–35 | Narrativa | pregunta-juan-bautista |
| 4 | La mujer pecadora en casa de Simón | lucas-7-la-mujer-pecadora-en-casa-de-simon | 36–50 | Narrativa | mujer-pecadora-uncion |

### Lucas 8
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Las mujeres que servían a Jesús | lucas-8-las-mujeres-que-servian-a-jesus | 1–3 | Narrativa | mujeres-servian |
| 2 | Parábola del sembrador | lucas-8-parabola-del-sembrador | 4–8 | Parábola | parabola-sembrador |
| 3 | Propósito y explicación de las parábolas | lucas-8-proposito-y-explicacion-de-las-parabolas | 9–18 | Discurso | proposito-parabolas |
| 4 | La familia espiritual de Jesús | lucas-8-la-familia-espiritual-de-jesus | 19–21 | Narrativa | familia-espiritual |
| 5 | Jesús calma la tempestad | lucas-8-jesus-calma-la-tempestad | 22–25 | Milagro | jesus-calma-tempestad |
| 6 | Jesús sana al endemoniado gadareno | lucas-8-jesus-sana-al-endemoniado-gadareno | 26–39 | Milagro | endemoniado-gadareno |
| 7 | Jesús sana a la mujer con flujo (inicio sandwich) | lucas-8-jesus-sana-a-la-mujer-con-flujo-inicio-sandwich | 40–48 | Milagro | mujer-flujo-sangre |
| 8 | Jesús resucita a la hija de Jairo (cierre) | lucas-8-jesus-resucita-a-la-hija-de-jairo-cierre | 49–56 | Milagro | sanidad-hija-de-jairo |

### Lucas 9
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús envía a los doce | lucas-9-jesus-envia-a-los-doce | 1–6 | Discurso | envio-apostoles |
| 2 | Muerte de Juan el Bautista (Herodes) | lucas-9-muerte-de-juan-el-bautista-herodes | 7–9 | Narrativa | muerte-juan-bautista |
| 3 | Alimentación de los cinco mil | lucas-9-alimentacion-de-los-cinco-mil | 10–17 | Milagro | alimentacion-cinco-mil |
| 4 | Confesión de Pedro | lucas-9-confesion-de-pedro | 18–22 | Narrativa | confesion-pedro |
| 5 | Jesús anuncia su muerte | lucas-9-jesus-anuncia-su-muerte | 22–27 | Discurso | anuncio-muerte-primera |
| 6 | La transfiguración | lucas-9-la-transfiguracion | 28–36 | Narrativa | transfiguracion |
| 7 | Jesús sana al muchacho epiléptico | lucas-9-jesus-sana-al-muchacho-epileptico | 37–43 | Milagro | jesus-sana-epileptico |
| 8 | Jesús anuncia de nuevo su muerte | lucas-9-jesus-anuncia-de-nuevo-su-muerte | 44–45 | Discurso | anuncio-muerte-segunda |
| 9 | El mayor en el reino | lucas-9-el-mayor-en-el-reino | 46–48 | Discurso | mayor-reino |
| 10 | El que no es contra nosotros | lucas-9-el-que-no-es-contra-nosotros | 49–50 | Discurso | no-contra-nosotros |

### Lucas 10
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús envía a los setenta | lucas-10-jesus-envia-a-los-setenta | 1–16 | Discurso | envio-setenta |
| 2 | El regreso de los setenta | lucas-10-el-regreso-de-los-setenta | 17–20 | Narrativa | regreso-setenta |
| 3 | Jesús se regocija | lucas-10-jesus-se-regocija | 21–24 | Discurso | regocijo-jesus |
| 4 | Parábola del buen samaritano | lucas-10-parabola-del-buen-samaritano | 25–37 | Parábola | parabola-buen-samaritano |
| 5 | Marta y María | lucas-10-marta-y-maria | 38–42 | Narrativa | maria-marta |

### Lucas 11
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La enseñanza del Padrenuestro | lucas-11-la-ensenanza-del-padrenuestro | 1–4 | Discurso | ensenanza-padrenuestro |
| 2 | Parábola del amigo importuno | lucas-11-parabola-del-amigo-importuno | 5–13 | Parábola | parabola-amigo-importuno |
| 3 | Jesús y Beelzebú | lucas-11-jesus-y-beelzebu | 14–26 | Controversia | jesus-beelzebu |
| 4 | La señal de Jonás | lucas-11-la-senal-de-jonas | 27–36 | Discurso | senal-jonas |
| 5 | Ay de los fariseos y escribas | lucas-11-ay-de-los-fariseos-y-escribas | 37–54 | Discurso | ay-fariseos |

### Lucas 12
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La levadura de los fariseos | lucas-12-la-levadura-de-los-fariseos | 1–12 | Discurso | levaduras-fariseos |
| 2 | El rico insensato | lucas-12-el-rico-insensato | 13–21 | Parábola | parabola-rico-insensato |
| 3 | La providencia de Dios | lucas-12-la-providencia-de-dios | 22–34 | Discurso | providencia-dios |
| 4 | Siervos vigilantes | lucas-12-siervos-vigilantes | 35–48 | Discurso | siervos-vigilantes |
| 5 | Jesús causa división | lucas-12-jesus-causa-division | 49–53 | Discurso | jesus-causa-division |
| 6 | Discernir los tiempos | lucas-12-discernir-los-tiempos | 54–59 | Discurso | discernir-tiempos |

### Lucas 13
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Arrepentimiento o muerte | lucas-13-arrepentimiento-o-muerte | 1–5 | Discurso | arrepentimiento-muerte |
| 2 | Parábola de la higuera estéril | lucas-13-parabola-de-la-higuera-esteril | 6–9 | Parábola | parabola-higuera-esteril |
| 3 | Jesús sana a la mujer encorvada en sábado | lucas-13-jesus-sana-a-la-mujer-encorvada-en-sabado | 10–17 | Milagro | jesus-sana-mujer-encorvada |
| 4 | Parábola del grano de mostaza y la levadura | lucas-13-parabola-del-grano-de-mostaza-y-la-levadura | 18–21 | Parábola | parabola-grano-mostaza |
| 5 | La puerta estrecha | lucas-13-la-puerta-estrecha | 22–30 | Discurso | puerta-estrecha |
| 6 | Jesús llora sobre Jerusalén | lucas-13-jesus-llora-sobre-jerusalen | 31–35 | Discurso | lamento-jerusalen |

### Lucas 14
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al hidrópico en sábado | lucas-14-jesus-sana-al-hidropico-en-sabado | 1–6 | Milagro | jesus-sana-hidropico |
| 2 | Humildad en el banquete | lucas-14-humildad-en-el-banquete | 7–14 | Discurso | humildad-banquete |
| 3 | Parábola de la gran cena | lucas-14-parabola-de-la-gran-cena | 15–24 | Parábola | parabola-gran-cena |
| 4 | El costo del discipulado | lucas-14-el-costo-del-discipulado | 25–35 | Discurso | costo-discipulado |

### Lucas 15
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola de la oveja perdida | lucas-15-parabola-de-la-oveja-perdida | 1–7 | Parábola | parabola-oveja-perdida |
| 2 | Parábola de la moneda perdida | lucas-15-parabola-de-la-moneda-perdida | 8–10 | Parábola | parabola-moneda-perdida |
| 3 | Parábola del hijo pródigo | lucas-15-parabola-del-hijo-prodigo | 11–32 | Parábola | parabola-hijo-prodigo |

### Lucas 16
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola del mayordomo infiel | lucas-16-parabola-del-mayordomo-infiel | 1–13 | Parábola | parabola-mayordomo-infiel |
| 2 | La ley y el reino | lucas-16-la-ley-y-el-reino | 14–17 | Discurso | ley-reino |
| 3 | El rico y Lázaro | lucas-16-el-rico-y-lazaro | 19–31 | Parábola | parabola-rico-lazaro |

### Lucas 17
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El escándalo y el perdón | lucas-17-el-escandalo-y-el-perdon | 1–4 | Discurso | escandalo-perdon |
| 2 | El siervo inútil | lucas-17-el-siervo-inutil | 5–10 | Parábola | parabola-siervo-inutil |
| 3 | Los diez leprosos | lucas-17-los-diez-leprosos | 11–19 | Milagro | jesus-sana-diez-leprosos |
| 4 | La venida del reino | lucas-17-la-venida-del-reino | 20–37 | Profecía | venida-reino |

### Lucas 18
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Parábola de la viuda y el juez | lucas-18-parabola-de-la-viuda-y-el-juez | 1–8 | Parábola | parabola-viuda-juez |
| 2 | Parábola del fariseo y el publicano | lucas-18-parabola-del-fariseo-y-el-publicano | 9–14 | Parábola | parabola-fariseo-publicano |
| 3 | Jesús y los niños | lucas-18-jesus-y-los-ninos | 15–17 | Narrativa | jesus-bendice-ninos |
| 4 | El joven rico | lucas-18-el-joven-rico | 18–23 | Narrativa | joven-rico |
| 5 | El peligro de las riquezas | lucas-18-el-peligro-de-las-riquezas | 24–30 | Discurso | peligro-riquezas |
| 6 | Jesús anuncia su muerte por tercera vez | lucas-18-jesus-anuncia-su-muerte-por-tercera-vez | 31–34 | Discurso | anuncio-muerte-tercera |
| 7 | Jesús sana al ciego de Jericó | lucas-18-jesus-sana-al-ciego-de-jerico | 35–43 | Milagro | jesus-sana-ciegos-jerico |

### Lucas 19
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Zaqueo el publicano | lucas-19-zaqueo-el-publicano | 1–10 | Narrativa | zaqueo-publicano |
| 2 | Parábola de las diez minas | lucas-19-parabola-de-las-diez-minas | 11–27 | Parábola | parabola-diez-minas |
| 3 | La entrada triunfal en Jerusalén | lucas-19-la-entrada-triunfal-en-jerusalen | 28–44 | Narrativa | entrada-jerusalen |
| 4 | Jesús purifica el templo | lucas-19-jesus-purifica-el-templo | 45–48 | Narrativa | purificacion-templo |

### Lucas 20
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La autoridad de Jesús cuestionada | lucas-20-la-autoridad-de-jesus-cuestionada | 1–8 | Controversia | autoridad-jesus-cuestionada |
| 2 | Parábola de los labradores malvados | lucas-20-parabola-de-los-labradores-malvados | 9–19 | Parábola | parabola-labradores-malvados |
| 3 | Tributo al César | lucas-20-tributo-al-cesar | 20–26 | Controversia | tributo-cesar |
| 4 | Pregunta sobre la resurrección | lucas-20-pregunta-sobre-la-resurreccion | 27–40 | Controversia | pregunta-resurreccion |
| 5 | ¿De quién es hijo el Cristo? | lucas-20-de-quien-es-hijo-el-cristo | 41–44 | Controversia | hijo-david |
| 6 | Denuncia a los escribas | lucas-20-denuncia-a-los-escribas | 45–47 | Discurso | ay-fariseos |
| 7 | La ofrenda de la viuda | lucas-20-la-ofrenda-de-la-viuda | 45–47 | Narrativa | ofrenda-viuda |

### Lucas 21
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Profecía de la destrucción del templo | lucas-21-profecia-de-la-destruccion-del-templo | 5–9 | Profecía | destruccion-templo |
| 2 | Señales del fin de los tiempos | lucas-21-senales-del-fin-de-los-tiempos | 10–19 | Profecía | discurso-olio |
| 3 | La destrucción de Jerusalén | lucas-21-la-destruccion-de-jerusalen | 20–24 | Profecía | discurso-olio |
| 4 | La venida del Hijo del Hombre | lucas-21-la-venida-del-hijo-del-hombre | 25–28 | Profecía | discurso-olio |
| 5 | Parábola de la higuera | lucas-21-parabola-de-la-higuera | 29–33 | Parábola | parabola-higuera-olio |
| 6 | Velar y orar | lucas-21-velar-y-orar | 34–38 | Discurso | velar-orar |

### Lucas 22
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La conspiración para matar a Jesús | lucas-22-la-conspiracion-para-matar-a-jesus | 1–6 | Narrativa | conspiracion-pascua |
| 2 | La Última Cena | lucas-22-la-ultima-cena | 7–23 | Narrativa | pascua-redencion |
| 3 | La disputa por la grandeza | lucas-22-la-disputa-por-la-grandeza | 24–30 | Narrativa | siervo-mayor |
| 4 | Jesús predice la negación de Pedro | lucas-22-jesus-predice-la-negacion-de-pedro | 31–38 | Narrativa | predice-negacion-pedro |
| 5 | Getsemaní | lucas-22-getsemani | 39–46 | Narrativa | Getsemaní |
| 6 | El arresto de Jesús | lucas-22-el-arresto-de-jesus | 47–53 | Narrativa | arresto-jesus |
| 7 | Pedro niega a Jesús | lucas-22-pedro-niega-a-jesus | 54–62 | Narrativa | negacion-pedro |
| 8 | Jesús es burlado | lucas-22-jesus-es-burlado | 63–65 | Narrativa | burlas-jesus |
| 9 | Jesús ante el concilio | lucas-22-jesus-ante-el-concilio | 66–71 | Narrativa | juicio-sanedrin |

### Lucas 23
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús ante Pilato | lucas-23-jesus-ante-pilato | 1–5 | Narrativa | juicio-pilato |
| 2 | Jesús ante Herodes | lucas-23-jesus-ante-herodes | 6–12 | Narrativa | jesus-ante-herodes |
| 3 | Jesús ante Pilato de nuevo | lucas-23-jesus-ante-pilato-de-nuevo | 13–25 | Narrativa | juicio-pilato |
| 4 | La crucifixión | lucas-23-la-crucifixion | 26–43 | Narrativa | crucifixion-jesus |
| 5 | La muerte de Jesús | lucas-23-la-muerte-de-jesus | 44–49 | Narrativa | muerte-jesus |
| 6 | La sepultura de Jesús | lucas-23-la-sepultura-de-jesus | 50–56 | Narrativa | sepultura-jesus |

### Lucas 24
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La resurrección de Jesús | lucas-24-la-resurreccion-de-jesus | 1–12 | Narrativa | resurreccion-jesus |
| 2 | Camino a Emaús | lucas-24-camino-a-emaus | 13–35 | Narrativa | camino-emaus |
| 3 | Jesús se aparece a los discípulos | lucas-24-jesus-se-aparece-a-los-discipulos | 36–49 | Narrativa | aparicion-apostoles |
| 4 | La ascensión | lucas-24-la-ascension | 50–53 | Narrativa | ascension-jesus |

---

## 4. Evangelio según San Juan (21 capítulos)

### Juan 1
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Prólogo: el Verbo hecho carne | juan-1-prologo-el-verbo-hecho-carne | 1–18 | Prólogo | prologo-juan |
| 2 | Testimonio de Juan el Bautista | juan-1-testimonio-de-juan-el-bautista | 19–28 | Narrativa | juan-bautista-predica |
| 3 | El Cordero de Dios | juan-1-el-cordero-de-dios | 29–34 | Narrativa | bautismo-jesus |
| 4 | Los primeros discípulos | juan-1-los-primeros-discipulos | 35–42 | Narrativa | llamado-primeros-discípulos |
| 5 | Llamamiento de Felipe y Natanael | juan-1-llamamiento-de-felipe-y-natanael | 43–51 | Narrativa | llamado-felipe-natanael |

### Juan 2
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Las bodas de Caná | juan-2-las-bodas-de-cana | 1–12 | Milagro | bodas-cana |
| 2 | La purificación del templo | juan-2-la-purificacion-del-templo | 13–25 | Narrativa | purificacion-templo |

### Juan 3
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús y Nicodemo | juan-3-jesus-y-nicodemo | 1–21 | Discurso | jesus-nicodemo |
| 2 | Juan el Bautista da testimonio | juan-3-juan-el-bautista-da-testimonio | 22–36 | Discurso | juan-bautista-testimonio-final |

### Juan 4
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La mujer samaritana | juan-4-la-mujer-samaritana | 1–26 | Narrativa | mujer-samaritana |
| 2 | Los samaritanos creen | juan-4-los-samaritanos-creen | 27–42 | Narrativa | samaritanos-creen |
| 3 | Jesús sana al hijo del oficial | juan-4-jesus-sana-al-hijo-del-oficial | 43–54 | Milagro | jesus-sana-hijo-oficial |

### Juan 5
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al paralítico de Betesda | juan-5-jesus-sana-al-paralitico-de-betesda | 1–18 | Milagro | jesus-sana-paralitico-betesda |
| 2 | La autoridad del Hijo | juan-5-la-autoridad-del-hijo | 19–30 | Discurso | autoridad-hijo |
| 3 | Los cuatro testigos | juan-5-los-cuatro-testigos | 31–47 | Discurso | cuatro-testigos |

### Juan 6
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Alimentación de los cinco mil | juan-6-alimentacion-de-los-cinco-mil | 1–15 | Milagro | alimentacion-cinco-mil |
| 2 | Jesús anda sobre el agua | juan-6-jesus-anda-sobre-el-agua | 16–24 | Milagro | jesus-anda-sobre-agua |
| 3 | El pan de vida | juan-6-el-pan-de-vida | 25–59 | Discurso | pan-de-vida |
| 4 | Las palabras de vida eterna | juan-6-las-palabras-de-vida-eterna | 60–71 | Discurso | palabras-vida-eterna |

### Juan 7
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La incredulidad de los hermanos de Jesús | juan-7-la-incredulidad-de-los-hermanos-de-jesus | 1–9 | Narrativa | incredulidad-hermanos |
| 2 | Jesús enseña en la fiesta | juan-7-jesus-ensena-en-la-fiesta | 10–24 | Discurso | ensenanza-fiesta |
| 3 | ¿Es éste el Cristo? | juan-7-es-este-el-cristo | 25–36 | Discurso | es-este-el-cristo |
| 4 | Ríos de agua viva | juan-7-rios-de-agua-viva | 37–52 | Discurso | rios-agua-viva |

### Juan 8
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La mujer adúltera | juan-8-la-mujer-adultera | 1–11 | Narrativa | mujer-adultera |
| 2 | La luz del mundo | juan-8-la-luz-del-mundo | 12–30 | Discurso | luz-del-mundo |
| 3 | La verdad os hará libres | juan-8-la-verdad-os-hara-libres | 31–38 | Discurso | verdad-libera |
| 4 | Abraham y Jesús | juan-8-abraham-y-jesus | 39–59 | Discurso | abraham-jesus |

### Juan 9
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús sana al ciego de nacimiento | juan-9-jesus-sana-al-ciego-de-nacimiento | 1–7 | Milagro | jesus-sana-ciego-nacimiento |
| 2 | La investigación de los fariseos | juan-9-la-investigacion-de-los-fariseos | 8–23 | Narrativa | investigacion-fariseos-ciego |
| 3 | El ciego sanado y los fariseos | juan-9-el-ciego-sanado-y-los-fariseos | 24–34 | Discurso | ciego-fariseos |
| 4 | Ceguera espiritual | juan-9-ceguera-espiritual | 35–41 | Discurso | ceguera-espiritual |

### Juan 10
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El buen pastor | juan-10-el-buen-pastor | 1–18 | Discurso | buen-pastor |
| 2 | Jesús rechazado en la fiesta de la dedicación | juan-10-jesus-rechazado-en-la-fiesta-de-la-dedicacion | 19–39 | Discurso | jesus-rechazado-dedicacion |
| 3 | Más allá del Jordán | juan-10-mas-alla-del-jordan | 40–42 | Narrativa | mas-alla-jordan |

### Juan 11
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La muerte de Lázaro | juan-11-la-muerte-de-lazaro | 1–16 | Narrativa | muerte-lazaro |
| 2 | Jesús resucita a Lázaro | juan-11-jesus-resucita-a-lazaro | 17–44 | Milagro | resureccion-lazaro |
| 3 | La decisión de matar a Jesús | juan-11-la-decision-de-matar-a-jesus | 45–57 | Narrativa | decision-matar-jesus |

### Juan 12
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La unción en Betania | juan-12-la-uncion-en-betania | 1–11 | Narrativa | uncion-betania |
| 2 | La entrada triunfal en Jerusalén | juan-12-la-entrada-triunfal-en-jerusalen | 12–19 | Narrativa | entrada-jerusalen |
| 3 | El grano de trigo que muere | juan-12-el-grano-de-trigo-que-muere | 20–36 | Discurso | grano-trigo-muere |
| 4 | La incredulidad de los judíos | juan-12-la-incredulidad-de-los-judios | 37–50 | Discurso | incredulidad-judios-final |

### Juan 13
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús lava los pies a los discípulos | juan-13-jesus-lava-los-pies-a-los-discipulos | 1–20 | Narrativa | lavamiento-pies |
| 2 | Jesús anuncia la traición de Judas | juan-13-jesus-anuncia-la-traicion-de-judas | 21–30 | Narrativa | anuncio-traicion-judas |
| 3 | El mandamiento nuevo | juan-13-el-mandamiento-nuevo | 31–38 | Discurso | mandamiento-nuevo |

### Juan 14
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús consuela a sus discípulos | juan-14-jesus-consuela-a-sus-discipulos | 1–14 | Discurso | discurso-del-aposento-alto |
| 2 | La promesa del Consolador | juan-14-la-promesa-del-consolador | 15–31 | Discurso | discurso-del-aposento-alto |

### Juan 15
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La vid verdadera | juan-15-la-vid-verdadera | 1–17 | Discurso | discurso-del-aposento-alto |
| 2 | El odio del mundo | juan-15-el-odio-del-mundo | 18–25 | Discurso | discurso-del-aposento-alto |
| 3 | El Consolador y los testigos | juan-15-el-consolador-y-los-testigos | 26–27 | Discurso | discurso-del-aposento-alto |

### Juan 16
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La obra del Consolador | juan-16-la-obra-del-consolador | 1–15 | Discurso | discurso-del-aposento-alto |
| 2 | La tristeza se convertirá en gozo | juan-16-la-tristeza-se-convertira-en-gozo | 16–33 | Discurso | discurso-del-aposento-alto |

### Juan 17
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La oración intercesora | juan-17-la-oracion-intercesora | 1–26 | Discurso | discurso-del-aposento-alto |

### Juan 18
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | El arresto de Jesús | juan-18-el-arresto-de-jesus | 1–14 | Narrativa | arresto-jesus |
| 2 | Pedro y el siervo del sumo sacerdote | juan-18-pedro-y-el-siervo-del-sumo-sacerdote | 15–18 | Narrativa | pedro-siervo-sumo-sacerdote |
| 3 | Jesús ante el sumo sacerdote (Anás) | juan-18-jesus-ante-el-sumo-sacerdote-anas | 19–24 | Narrativa | juicio-anas |
| 4 | Pedro niega a Jesús | juan-18-pedro-niega-a-jesus | 25–27 | Narrativa | negacion-pedro |
| 5 | Jesús ante Pilato | juan-18-jesus-ante-pilato | 28–40 | Narrativa | juicio-pilato |

### Juan 19
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La flagelación y la sentencia | juan-19-la-flagelacion-y-la-sentencia | 1–16 | Narrativa | jesus-azotado |
| 2 | La crucifixión | juan-19-la-crucifixion | 17–27 | Narrativa | crucifixion-jesus |
| 3 | La muerte de Jesús | juan-19-la-muerte-de-jesus | 28–37 | Narrativa | muerte-jesus |
| 4 | La sepultura de Jesús | juan-19-la-sepultura-de-jesus | 38–42 | Narrativa | sepultura-jesus |

### Juan 20
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | La resurrección | juan-20-la-resurreccion | 1–10 | Narrativa | resurreccion-jesus |
| 2 | María Magdalena ve a Jesús | juan-20-maria-magdalena-ve-a-jesus | 11–18 | Narrativa | aparicion-maria-magdalena |
| 3 | Jesús se aparece a los discípulos | juan-20-jesus-se-aparece-a-los-discipulos | 19–23 | Narrativa | aparicion-apostoles |
| 4 | Tomás ve al Señor resucitado | juan-20-tomas-ve-al-senor-resucitado | 24–29 | Narrativa | tomas-increduo |
| 5 | El propósito del evangelio | juan-20-el-proposito-del-evangelio | 30–31 | Narrativa | proposito-evangelio-juan |

### Juan 21
| # | Título | Slug | v | Estilo | _evento_canonico |
|:-:|:-------|:-----|:-:|:-------|:------------------|
| 1 | Jesús se aparece junto al lago | juan-21-jesus-se-aparece-junto-al-lago | 1–14 | Narrativa | aparicion-lago-tiberiades |
| 2 | Jesús restaura a Pedro | juan-21-jesus-restaura-a-pedro | 15–19 | Narrativa | restauracion-pedro |
| 3 | La profecía sobre el futuro de Pedro y Juan | juan-21-la-profecia-sobre-el-futuro-de-pedro-y-juan | 20–23 | Narrativa | profecia-pedro-juan |
| 4 | Conclusión del evangelio | juan-21-conclusion-del-evangelio | 24–25 | Narrativa | conclusion-juan |

---

## Resumen de Fase D

| Libro | Capítulos | Perícopas |
|:------|:---------:|:---------:|
| Mateo | 28 | 121 |
| Marcos | 16 | 87 |
| Lucas | 24 | 101 |
| Juan | 21 | 78 |
| **Total** | **89** | **387** |

**Discurso del Aposento Alto (Juan 13–17)**: 10 perícopas con granularidad fina, `_evento_canonico = discurso-del-aposento-alto`.
**Sermón del Monte (Mateo 5–7)**: 20 perícopas con granularidad fina, `_evento_canonico = sermon-del-monte`.
**Sermón del Plano (Lucas 6)**: 3 perícopas, `_evento_canonico = sermon-del-plano`.
**Discurso del Monte de los Olivos (Mateo 24–25, Marcos 13, Lucas 21)**: compartido entre los 3 sinópticos, `_evento_canonico = discurso-olio`.

## Notas de concordancia

- **Tipo A (testimonio múltiple sinóptico)**: para eventos compartidos por Mt-Mc-Lc (y a veces Jn), el `_evento_canonico` es el mismo. Ejemplo: `bautismo-jesus`, `tentacion-jesus`, `transfiguracion`, `gran-comision`.
- **Eventos propios de Juan** (sin sinóptico): bodas de Caná, Nicodemo, mujer samaritana, Lázaro, Discursos del Aposento Alto, "Yo soy" declaraciones.
- **Eventos propios de Mateo**: predicciones del fin, parábolas escatológicas (10 vírgenes, talentos).
- **Eventos propios de Lucas**: parábolas únicas (hijo pródigo, buen samaritano, rico y Lázaro), visitas a Betania y Marta/María.
