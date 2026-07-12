# Ejemplo: Ai (Hay)

Demostración para una ubicación menos conocida con información limitada.
(ID ficticio 12346 — usar el ID real al ejecutar.)

## Fase 0: Obtener datos

```bash
docker exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Hay" \
  --fields=ID,post_title,post_name,post_status --allow-root

docker exec wp_bc_cli wp post meta list 12346 --allow-root
```

Metadatos obtenidos:

| Meta key | Valor |
|----------|-------|
| `_bc_loc_name_en` | Ai |
| `_bc_loc_type` | city |
| `_bc_loc_scriptures` | `[{"ref":"Josh 7:2"},{"ref":"Josh 8:1"},{"ref":"Josh 10:1"},{"ref":"Neh 11:31"},{"ref":"Jer 49:3"}]` |
| `_bc_loc_lat` | 31.9167 |
| `_bc_loc_lng` | 35.2833 |
| `_bc_loc_source` | openbible |
| `_bc_loc_confidence` | medium |
| `_bc_loc_date_from` | -1400 |
| `_bc_loc_date_to` | -500 |

## Fase 1: Investigar

Alejandría:
```bash
alejandria_kg_find(query: "Ai")
alejandria_search_text(query: "Ai", source_filter: "es/scriptures")
alejandria_search_semantic(query: "Ai biblical city conquest")
```

Fallo de Alejandría → Guía para el Estudio de las Escrituras:
```bash
webfetch(url: "https://www.churchofjesuschrist.org/study/scriptures/gs/hay?lang=spa")
```

## Fase 5: Contenido redactado

```html
<h2>Una ciudad de la conquista</h2>

<p>Hay (Ai en hebreo, que significa "la ruina") era una ciudad cananea ubicada al este de Betel, en la región montañosa de Benjamín. Aparece prominentemente en el relato de la conquista de Canaán bajo el liderazgo de Josué.</p>

<h2>Relato bíblico</h2>

<p>La primera vez que Israel intentó tomar Hay, fueron derrotados debido al pecado de Acán, quien había tomado del anatema de Jericó (Josué 7:2–5). Después de que Acán fue juzgado, Josué dirigió un segundo ataque con una estrategia de emboscada, logrando la victoria (Josué 8:1–29). Hay fue destruida y quedó en ruinas. Más tarde, en el período postexílico, la ciudad fue reconstruida y mencionada entre las ciudades habitadas por los benjamitas (Nehemías 11:31).</p>

<h2>Significado en la teología de la Restauración</h2>

<p>La historia de Hay enseña la importancia de la obediencia y la santidad en la obra del Señor. La derrota inicial de Israel muestra que la desobediencia individual puede afectar a toda la comunidad del convenio, mientras que la victoria final demuestra que el arrepentimiento y la obediencia restauran el favor divino. Es un poderoso recordatorio de que el Señor pelea por Su pueblo cuando este es fiel.</p>

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
      <td>Israel fue derrotado por el pecado de Acán</td>
      <td>Josué 7:2–5</td>
    </tr>
    <tr>
      <td>Josué tomó la ciudad con una emboscada estratégica</td>
      <td>Josué 8:1–29</td>
    </tr>
    <tr>
      <td>Hay fue reconstruida en el período postexílico</td>
      <td>Nehemías 11:31</td>
    </tr>
  </tbody>
</table>
```

## Fase 6: Publicar

Incluir el contenido completo de Fase 5 (incluyendo la Forma T de
referencias). Usar un archivo temporal para evitar problemas de escaping:

```bash
type C:\ruta\al\contenido-completo.html | docker exec -i wp_bc_cli wp post update 12346 --post_content="$(cat /dev/stdin)" --allow-root
```
```

## Fase 7: Verificar

```bash
docker exec wp_bc_cli wp post get 12346 --field=post_content --allow-root | head -c 200
```
