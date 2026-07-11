---
name: traducir-ubicaciones
description: |
  Traducir nombres de ubicaciones (bc_location) del inglés al español
  usando el corpus de Alejandría (RV60 LDS) como referencia bíblica.
---

# Skill: traducir-ubicaciones

Traduce nombres de ubicaciones del CPT `bc_location` cotejando el
pasaje bíblico inglés vs. su correspondiente en la RV60 española
del corpus de Alejandría.

## Campos del CPT

| Meta key | Descripción | Origen |
|----------|-------------|--------|
| `_bc_loc_name_en` | Nombre en inglés (KJV) | Ya existe |
| `_bc_loc_name_es` | Nombre traducido al español | Se escribe en paso 8 |
| `_bc_loc_scripture` | Referencia escritural española usada para la traducción (ej. "Jueces 12:13-15") | Se escribe en paso 8 |
| `_bc_loc_scriptures` | Array JSON de referencias inglesas (ej. `[{"ref":"Josh 21:30"}]`) | Ya existe |
| `_bc_loc_lat` | Latitud | Ya existe |
| `_bc_loc_lng` | Longitud | Ya existe |
| `_bc_loc_type` | Tipo (city, region, etc.) | Ya existe |
| `_bc_loc_source` | Fuente de datos (openbible, gnosis, church-history) | Ya existe |
| `_bc_loc_confidence` | Confianza (high, medium, low) | Ya existe |

## Procedimiento (8 pasos)

1. **Tomar nombre + pasaje** del documento `docs/pendientes-traduccion-locations.md`
2. **Ubicar el archivo español** en `C:\own\alejandria\corpus\es\scriptures\{testamento}\{libro}\{cap}.txt`
   - AT (`ot/`): genesis, exodo, levitico, numeros, deuteronomio, josue, jueces, rut,
     1-samuel, 2-samuel, 1-reyes, 2-reyes, 1-cronicas, 2-cronicas, esdras, nehemias,
     ester, job, salmos, proverbios, eclesiastes, cantares, isaias, jeremias,
     lamentaciones, ezequiel, daniel, oseas, joel, amos, abdias, jonas, miqueas,
     nahum, habacuc, sofonias, hageo, zacarias, malaquias
   - NT (`nt/`): mateo, marcos, lucas, juan, hechos, romanos, 1-corintios,
     2-corintios, galatas, efesios, filipenses, colosenses, 1-tesalonicenses,
     2-tesalonicenses, 1-timoteo, 2-timoteo, tito, filemon, hebreos, Santiago,
     1-pedro, 2-pedro, 1-juan, 2-juan, 3-juan, judas, apocalipsis
   - DyC (`dc/secciones/`): {num}.txt
   - Perla de Gran Precio (`pgp/`): moses, abraham, joseph-smith, faith
   - Libro de Mormón (`bom/`): 1-nefi, 2-nefi, jacob, enos, jaron, omni,
     palabras-de-mormon, mosiah, alma, helaman, 3-nefi, 4-nefi, mormon, ether, moroni
3. **Leer el texto español** del capítulo completo
4. **Cotejar** con el nombre inglés para identificar la forma española
5. **Buscar el post** `bc_location` por slug y obtener su ID
6. **Formatear la referencia española** según el mapa Libro Inglés → Español
7. **Obtener coordenadas** del post (`_bc_loc_lat`, `_bc_loc_lng` ya existen)
8. **Actualizar** vía WP-CLI:
   - `wp post update {ID} --post_title="{nombre español}" --post_name="{slug español}" --allow-root`
   - `wp post meta update {ID} _bc_loc_name_es "{nombre español}" --allow-root`
   - `wp post meta update {ID} _bc_loc_scripture "{referencia española}" --allow-root`

## Formato de referencia española

```
{Libro español abreviado} {cap}:{vers-inicio}[-{vers-fin}]
```

Ejemplos: `Jueces 12:13-15`, `Números 33:49`, `Lucas 3:1`

## Mapa de libros inglés → español

| Inglés | Español | Directorio |
|--------|---------|------------|
| Genesis | Génesis | `ot/genesis/` |
| Exodus | Éxodo | `ot/exodo/` |
| Leviticus | Levítico | `ot/levitico/` |
| Numbers | Números | `ot/numeros/` |
| Deuteronomy | Deuteronomio | `ot/deuteronomio/` |
| Joshua | Josué | `ot/josue/` |
| Judges | Jueces | `ot/jueces/` |
| Ruth | Rut | `ot/rut/` |
| 1 Samuel | 1 Samuel | `ot/1-samuel/` |
| 2 Samuel | 2 Samuel | `ot/2-samuel/` |
| 1 Kings | 1 Reyes | `ot/1-reyes/` |
| 2 Kings | 2 Reyes | `ot/2-reyes/` |
| 1 Chronicles | 1 Crónicas | `ot/1-cronicas/` |
| 2 Chronicles | 2 Crónicas | `ot/2-cronicas/` |
| Ezra | Esdras | `ot/esdras/` |
| Nehemiah | Nehemías | `ot/nehemias/` |
| Esther | Ester | `ot/ester/` |
| Job | Job | `ot/job/` |
| Psalms | Salmos | `ot/salmos/` |
| Proverbs | Proverbios | `ot/proverbios/` |
| Ecclesiastes | Eclesiastés | `ot/eclesiastes/` |
| Song of Solomon | Cantares | `ot/cantares/` |
| Isaiah | Isaías | `ot/isaias/` |
| Jeremiah | Jeremías | `ot/jeremias/` |
| Lamentations | Lamentaciones | `ot/lamentaciones/` |
| Ezekiel | Ezequiel | `ot/ezequiel/` |
| Daniel | Daniel | `ot/daniel/` |
| Hosea | Oseas | `ot/oseas/` |
| Joel | Joel | `ot/joel/` |
| Amos | Amós | `ot/amos/` |
| Obadiah | Abdías | `ot/abdias/` |
| Jonah | Jonás | `ot/jonas/` |
| Micah | Miqueas | `ot/miqueas/` |
| Nahum | Nahum | `ot/nahum/` |
| Habakkuk | Habacuc | `ot/habacuc/` |
| Zephaniah | Sofonías | `ot/sofonias/` |
| Haggai | Hageo | `ot/hageo/` |
| Zechariah | Zacarías | `ot/zacarias/` |
| Malachi | Malaquías | `ot/malaquias/` |
| Luke | Lucas | `nt/lucas/` |
| Acts | Hechos | `nt/hechos/` |
| D&C | DyC | `dc/` |
| Abraham | Abraham | `pgp/abraham/` |

## Mapa de nombres comunes

| Inglés (KJV) | Español (RV60) | Referencia |
|--------------|----------------|------------|
| Abdon | Abdón | Jueces 12:13-15 |
| Abel-shittim | Abel-sitim | Números 33:49 |
| Abilene | Abilinia | Lucas 3:1 |
| Accad | Acad | Génesis 10:10 |
| Achshaph | Acsaf | Josué 11:1 |
| Achzib | Aczib | Josué 19:29 |
| Adam | Adán | DyC 107:44-49 |
| Adullam | Adulam | Nehemías 11:30 |
| Adummim | Adumín | Josué 18:17 |
| Aijalon | Ajalón | 1 Samuel 14:31 |
| Ain | Aín | 1 Crónicas 4:32 |
| Akrabbim | Acrabim | Números 34:4 |
| Almon | Almón | Números 33:46 |
| Almon-diblathaim | Almón-diblataim | Números 33:46 |
| Alush | Alús | Números 33:13 |
| Amalek | Amalec | Éxodo 17:8 |
| Aphek | Afec | 1 Samuel 29:1 |
| Aphekah | Afeca | Josué 15:53 |
| Areopagus | Areópago | Hechos 17:19 |
| Arnon | Arnón | Jueces 11:13 |
