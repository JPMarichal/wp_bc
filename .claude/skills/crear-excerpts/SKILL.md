# Skill: Crear Excerpts SEO para Artículos

## Cuándo usar
Cuando se necesite crear o revisar excerpts (meta descriptions) de artículos WordPress para mejorar CTR en resultados de búsqueda (SERP). Aplica a posts nuevos, revisiones de posts existentes, o batches completos de una serie/colección. También cuando se analice el rendimiento SEO de los excerpts actuales del sitio.

## Principios SEO para excerpts (investigación 2026)

### Anatomía de un excerpt efectivo

```
[Keyword front-loaded] + [Value proposition] + [Specific detail] → [Soft CTA]
  primeros 70-80 chars       80-120 chars        120-155 chars      (opcional)
  ←——————————— mobile safe (120 chars) —————————→
  ←———————————————— desktop safe (155-160 chars) —————————————————→
```

### Reglas fundamentales

1. **Front-loading**: keyword principal en los primeros 70 caracteres. Google bolds query matches en el snippet, lo que atrae la mirada.
2. **Longitud**: 120-155 caracteres. Google mide píxeles (~920px desktop, ~680px mobile). La información crítica debe caber en los primeros 120 caracteres (mobile); el resto es bonus para desktop.
3. **Específico > genérico**: incluir números (fechas, cantidades, años), nombres propios (traductores, versiones, lugares), y detalles concretos que diferencian el artículo.
4. **Sin boilerplate**: prohibido "Este artículo analiza...", "En este artículo exploraremos...", "Descubre cómo...", "Conoce...". La meta description no se refiere a sí misma.
5. **Voz activa**: directa, informativa. Responde "¿Qué voy a encontrar aquí?".
6. **Único por página**: cada artículo necesita su propio excerpt. No usar plantillas repetidas entre artículos de una serie.
7. **Natural, no keyword-stuffed**: la keyword aparece una vez, de forma orgánica.
8. **Sin superlativos publicitarios**: evitar "mejor", "increíble", "fascinante", "extraordinario", "!!!", "&", "..."
9. **Coincidir con la intención de búsqueda**: el excerpt debe reflejar lo que el usuario busca y lo que la página realmente entrega.

### Tips específicos para series

- Cada artículo de una serie debe destacar su **ángulo único** dentro del tema general. No usar el mismo gancho para todos.
- Incluir el **subtema específico** del artículo (no solo el nombre de la serie).
- Usar **números ordinales o años** cuando ayuden a situar cronológicamente ("primera traducción", "revisión de 1602", "proceso 2004-2009").
- Diferenciar con **nombres propios** específicos del artículo (traductores, versiones, eventos) que no aparecen en los excerpts de los artículos vecinos.
- Evitar "final de la serie", "culminación", "este artículo de la serie" — el lector SERP no sabe que es una serie.

## Pipeline

```
1.  Leer artículo completo (título + contenido)
2.  Extraer: tema central, keyword principal, gancho único
3.  Redactar excerpt en < 155 chars
4.  Verificar front-loading (primeros 70 chars)
5.  Verificar mobile-safe (primeros 120 chars autónomos)
6.  Aplicar via WP-CLI
7.  Verificar en aplicación Evangelio (si aplica)
```

## Paso 1: Leer el artículo

```bash
podman exec wp_bc_cli wp post get <ID> --field=post_title --allow-root
podman exec wp_bc_cli wp post get <ID> --field=post_excerpt --allow-root
podman exec wp_bc_cli wp post get <ID> --field=post_content --allow-root | head -c 3000
```

## Paso 2: Extraer los elementos clave

| Elemento | Pregunta guía |
|----------|--------------|
| Tema central | ¿De qué trata el artículo en 5 palabras? |
| Keyword principal | ¿Qué frase buscaría un lector interesado? |
| Gancho único | ¿Qué diferencia este artículo de otros similares? |
| Datos concretos | Fechas, nombres, cifras, versiones bíblicas |

## Paso 3: Redactar

### Patrones que funcionan

| Patrón | Ejemplo |
|--------|---------|
| **[Keyword]: [especificación]** | "Casiodoro de Reina y la primera traducción completa de la Biblia al castellano desde las lenguas originales (1569). Doce años, exilio, Inquisición y la portada del oso." |
| **[Contexto] + [detalle específico]** | "Traducciones bíblicas españolas anteriores a la Reforma: Biblia prealfonsí, General Estoria de Alfonso X y la Biblia de Alba de 1433, del latín y el hebreo al romance." |
| **[Afirmación directa] + [2-3 detalles de apoyo]** | "La Reina-Valera 1960: el comité de Eugene Nida, cuatro sesiones, 1.700 páginas de sugerencias y el equilibrio entre la tradición textual y la erudición moderna." |

### Anti-patrones (NO usar)

| Malo | Por qué |
|------|---------|
| "Este artículo analiza la historia de la Vulgata..." | Boilerplate autoreferencial |
| "Descubre cómo la Reforma cambió..." | Imperativo genérico |
| "Conoce la fascinante historia de..." | Superlativo vacío |
| "Un viaje a través de la transmisión..." | Metáfora sin información |
| "En este artículo exploraremos..." | Filler autoreferencial |

### Long-tail keyword tips

- Usar frases de 3-5 palabras que reflejen búsquedas reales
- Preferir combinaciones: `[personaje] + [obra] + [año]` o `[versión bíblica] + [característica]`
- Incluir el año o siglo cuando sea relevante para la búsqueda
- Las frases específicas tienen menor competencia y mayor intención que términos genéricos

## Paso 4: Verificar

```python
# Rápida verificación de caracteres
excerpt = "texto del excerpt"
print(f"Total: {len(excerpt)} chars")
print(f"Mobile-safe (primeros 120): {excerpt[:120]}")
```

Requisitos:
- [ ] ≤ 155 caracteres total
- [ ] Primeros 120 chars comunican la idea principal
- [ ] Primeros 70 chars contienen keyword principal
- [ ] No contiene frases boilerplate
- [ ] Es único vs. otros artículos de la misma serie
- [ ] Menciona al menos un dato concreto (nombre, año, cifra)

## Paso 5: Aplicar via WP-CLI

```bash
# Post individual
podman exec wp_bc_cli wp post update <ID> --post_excerpt='Excerpt optimizado aquí.' --allow-root

# Batch de una serie
# Leer IDs de la serie primero
podman exec wp_bc_cli wp eval '
$posts = [ID1, ID2, ID3, ...];
$excerpts = [
    ID1 => "excerpt para ID1",
    ID2 => "excerpt para ID2",
];
foreach ($posts as $pid) {
    wp_update_post(["ID" => $pid, "post_excerpt" => $excerpts[$pid]]);
    echo "Post $pid actualizado.\n";
}
' --allow-root
```

## Paso 6: Verificar post-aplicación

```bash
podman exec wp_bc_cli wp eval '
$posts = [ID1, ID2, ...];
foreach ($posts as $pid) {
    $p = get_post($pid);
    echo str_pad($pid, 5) . " " . str_pad(strlen($p->post_excerpt), 4) . " " . $p->post_excerpt . "\n";
}
' --allow-root
```

## Referencia rápida de caracteres

```
         1         2         3         4         5         6         7         8         9         10        11        12        13        14        15
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
↑--- 70 chars: front-load keyword ---↑
↑------------------ 120 chars: mobile safe ------------------↑
↑---------------------------------- 155 chars: desktop limit ----------------------------------↑
```

## Notas

- WordPress usa `post_excerpt` como meta description en la mayoría de temas. Verificar que el theme active soporte para excerpt en `post` (soportado por defecto).
- Google rewritea ~60% de las meta descriptions. Un excerpt bien escrito, específico y query-matched reduce la probabilidad de rewrite.
- Para artículos que no tienen excerpt, Google genera snippet automático del contenido — casi siempre peor que uno escrito a mano.
- Los cambios son inmediatos, pero Google puede tardar días/semanas en reflejarlos en el SERP.
- Para páginas estáticas (`page`), el excerpt también se usa como meta description si el theme lo soporta.
