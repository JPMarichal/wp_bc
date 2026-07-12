# Ejemplo: Jerusalén

Demostración completa del pipeline para la ubicación **Jerusalén**.
(ID ficticio 12345 — usar el ID real al ejecutar.)

## Fase 0: Obtener datos

```bash
docker exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Jerusalén" \
  --fields=ID,post_title,post_name,post_status --allow-root

# Suponiendo ID=12345
docker exec wp_bc_cli wp post meta list 12345 --allow-root
```

Metadatos obtenidos:

| Meta key | Valor |
|----------|-------|
| `_bc_loc_name_en` | Jerusalem |
| `_bc_loc_type` | city |
| `_bc_loc_scriptures` | `[{"ref":"Josh 10:1"},{"ref":"Judg 1:7"},{"ref":"2 Sam 5:6"},{"ref":"1 Kgs 8:1"},{"ref":"Neh 2:17"},{"ref":"Isa 40:2"},{"ref":"Jer 3:17"},{"ref":"Zech 8:3"},{"ref":"Matt 4:25"},{"ref":"Luke 9:51"},{"ref":"John 12:12"},{"ref":"Acts 1:4"},{"ref":"Gal 4:25"},{"ref":"Heb 12:22"},{"ref":"Rev 3:12"},{"ref":"Rev 21:2"}]` |
| `_bc_loc_description` | Ciudad santa, capital de Israel |
| `_bc_loc_lat` | 31.7683 |
| `_bc_loc_lng` | 35.2137 |
| `_bc_loc_source` | openbible |
| `_bc_loc_confidence` | high |
| `_bc_loc_date_from` | -1000 |
| `_bc_loc_date_to` | 70 |

## Fase 1: Investigar con Alejandría

```bash
alejandria_kg_find(query: "Jerusalem")
alejandria_kg_profile(entity_name: "Jerusalem")
alejandria_search_text(query: "Jerusalén", source_filter: "es/scriptures", limit: 5)
alejandria_search_text(query: "Jerusalem", source_filter: "en/scriptures", limit: 5)
alejandria_search_text(query: "Jerusalén", source_filter: "es/manuals", limit: 5)
alejandria_chat_ask(question: "¿Cuál es la importancia de Jerusalén en la Biblia y en la historia de la Restauración?")
```

## Fase 5: Contenido redactado

```html
<h2>Una ciudad santa</h2>

<p>Jerusalén es una de las ciudades más antiguas y significativas del mundo bíblico. Ubicada en las colinas de Judá, entre el mar Mediterráneo y el mar Muerto, ha sido centro de la historia del convenio desde los días de Abraham. Su nombre evoca paz, aunque pocas ciudades han conocido tanta guerra.</p>

<h2>Historia bíblica</h2>

<p>En el Antiguo Testamento, Jerusalén fue conquistada por David, quien la estableció como capital de Israel (2 Samuel 5:6–9). Allí, Salomón edificó el templo, convirtiéndola en el centro espiritual del pueblo del convenio (1 Reyes 8:1). Fue destruida por los babilonios en 586 a.C. y reconstruida bajo Nehemías y Esdras (Nehemías 2:17). Los profetas Isaías, Jeremías y Zacarías anunciaron tanto su destrucción como su futura gloria (Isaías 40:2, Jeremías 3:17, Zacarías 8:3).</p>

<p>En el Nuevo Testamento, Jerusalén fue el escenario de los momentos culminantes del ministerio del Salvador. Allí fue aclamado como Rey, allí lloró sobre la ciudad, allí fue crucificado y, al tercer día, resucitó (Lucas 9:51, Juan 12:12). También en Jerusalén nació la Iglesia en el día de Pentecostés (Hechos 2).</p>

<h2>Significado en la teología de la Restauración</h2>

<p>Los Santos de los Últimos Días veneran Jerusalén como el lugar del sacrificio expiatorio de Jesucristo y como la ciudad donde el Evangelio fue predicado por primera vez después de la Resurrección. El canon de Escrituras de la Restauración enseña que Jerusalén será restaurada como ciudad santa en el Milenio (Apocalipsis 21:2) y que en ella se edificará un templo para el Señor. El Libro de Mormón también profetiza que Jerusalén será reconstruida y llegará a ser una ciudad santa (Eter 13:5).</p>

<h2>Referencias de las Escrituras</h2>

<table class="bc-forma-t">
  <thead>
    <tr>
      <th>Concepto</th>
      <th>Referencia</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>David conquistó Jerusalén y la estableció como capital</td>
      <td>2 Samuel 5:6–9</td>
    </tr>
    <tr>
      <td>Salomón edificó el templo como centro de adoración</td>
      <td>1 Reyes 8:1</td>
    </tr>
    <tr>
      <td>Jesús fue crucificado y resucitó en Jerusalén</td>
      <td>Lucas 23:33–24:6</td>
    </tr>
    <tr>
      <td>La Iglesia nació en Jerusalén en Pentecostés</td>
      <td>Hechos 2:1–4</td>
    </tr>
    <tr>
      <td>Jerusalén será restaurada como ciudad santa en el Milenio</td>
      <td>Apocalipsis 21:2</td>
    </tr>
  </tbody>
</table>
```

## Fase 6: Publicar

Incluir el contenido completo de Fase 5 (incluyendo la Forma T de
referencias). Usar un archivo temporal para evitar problemas de escaping:

```bash
# Escribir el contenido completo (con Forma T) a un archivo
# y leerlo desde el contenedor:
type C:\ruta\al\contenido-completo.html | docker exec -i wp_bc_cli wp post update 12345 --post_content="$(cat /dev/stdin)" --allow-root
```
```

## Fase 7: Verificar

```bash
docker exec wp_bc_cli wp post get 12345 --field=post_content --allow-root | head -c 200
```
