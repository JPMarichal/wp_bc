# Ejemplo: Ai (Hay) — nivel C

Demostración del formato nuevo para una ubicación nivel C (150–300 palabras).
(ID ficticio 12346 — usar el ID real al ejecutar.)

## Fase 0: Obtener datos

```bash
docker exec wp_bc_cli wp post list \
  --post_type=bc_location \
  --s="Ai" \
  --fields=ID,post_title,post_name,post_status --allow-root
```

Metadatos:
- `_bc_loc_name_en`: Ai
- `_bc_loc_type`: city
- `_bc_loc_scriptures`: [{"ref":"Josh 7:2"},{"ref":"Josh 8:1"},{"ref":"Josh 10:1"},{"ref":"Neh 11:31"},{"ref":"Jer 49:3"}]
- `_bc_loc_lat`: 31.9167, `_bc_loc_lng`: 35.2833
- `_bc_loc_confidence`: medium

## Fase 1: Investigar

```bash
alejandria_kg_find(query: "Ai")
alejandria_search_text(query: "Ai", source_filter: "es/scriptures")
alejandria_chat_ask(question: "¿Qué importancia tiene Ai en la Biblia?")

# Fallback
webfetch(url: "https://biblehub.com/topical/a/ai.htm")
```

## Fase 2: Contenido redactado

Nivel C: ~250 palabras, intro + 2 módulos + conclusión + fuentes + Forma T 3 filas.

```html
<p>Hay, conocida en español como Ai —que significa "la ruina"—, fue una ciudad cananea ubicada al este de Betel, en la región montañosa de Benjamín (Josué 7:2). Ocupa un lugar significativo en la narrativa de la conquista de Canaán bajo el liderazgo de Josué.</p>

<h2>Historia en las Escrituras</h2>

<p>Ai aparece principalmente en el libro de Josué. La primera vez que Israel intentó tomarla, fueron derrotados estrepitosamente debido al pecado de Acán, quien había tomado del anatema de Jericó (Josué 7:2–5). Después de que Acán fue juzgado y ejecutado, Josué dirigió un segundo ataque empleando una estrategia de emboscada que resultó en una victoria completa. La ciudad fue destruida y quedó en ruinas para siempre (Josué 8:1–29).</p>

<p>Siglos después, en el período postexílico, Ai fue reconstruida y aparece mencionada entre las ciudades habitadas por los benjamitas que regresaron del cautiverio (Nehemías 11:31). El profeta Jeremías también la menciona en un oráculo contra los filisteos (Jeremías 49:3).</p>

<p>En la actualidad, la mayoría de los arqueólogos identifican Ai con el sitio de et-Tell, aunque persiste un debate sobre si la destrucción de la ciudad del Bronce Antiguo coincide cronológicamente con la conquista israelita.</p>

<h2>Lecciones y simbolismo</h2>

<p>La historia de Ai enseña que la desobediencia individual puede afectar a toda la comunidad del convenio. La derrota inicial de Israel no fue por inferioridad militar sino por la presencia del pecado en el campamento. Una vez que el obstáculo fue removido, el Señor peleó por Su pueblo y les dio la victoria. Es un poderoso recordatorio de que la santidad personal tiene consecuencias colectivas.</p>

<h2>Conclusión</h2>

<p>Ai, cuyo nombre proféticamente significa "la ruina", experimentó la ruina literal dos veces: una por el juicio divino y otra por el abandono humano. Su historia nos recuerda que la obediencia colectiva es necesaria para que el poder de Dios se manifieste plenamente entre Su pueblo.</p>

<h2>Fuentes consultadas</h2>

<ul>
  <li><a href="https://www.churchofjesuschrist.org/study/scriptures/gs/hay?lang=spa">Guía para el Estudio de las Escrituras — Hay</a></li>
  <li><a href="https://biblehub.com/topical/a/ai.htm">BibleHub — Ai</a></li>
</ul>

<h2>Referencias de las Escrituras</h2>

<table class="bc-forma-t">
  <thead>
    <tr><th>Concepto</th><th>Referencia</th></tr>
  </thead>
  <tbody>
    <tr><td>Israel fue derrotado por el pecado de Acán</td><td>Josué 7:2–5</td></tr>
    <tr><td>Josué tomó Ai con una estrategia de emboscada</td><td>Josué 8:1–29</td></tr>
    <tr><td>Ai fue reconstruida en el período postexílico</td><td>Nehemías 11:31</td></tr>
  </tbody>
</table>
```

Notas sobre este ejemplo:
- ~250 palabras (nivel C)
- 3 módulos: Historia + Lecciones + Conclusión + Fuentes + Forma T
- Intro 1 párrafo: responde "¿Qué es Ai?"
- Arqueología mencionada brevemente sin polemizar
- Forma T: 3 filas
- Sin sección de Restauración forzada (no hay contenido SUD significativo)
