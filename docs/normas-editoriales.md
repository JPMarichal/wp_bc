# Normas Editoriales — bc_quote_author

> Documento rector para la redacción de biografías y todo contenido del sitio.
> Estas normas son obligatorias. Ninguna excepción sin aprobación explícita.

---

## 1. Denominación de la Iglesia

Regla absoluta: **no usar «mormón» como gentilicio ni «iglesia mormona» como nombre.**

| Incorrecto | Correcto |
|------------|----------|
| la Iglesia Mormona | La Iglesia de Jesucristo de los Santos de los Últimos Días |
| los mormones | los Santos de los Últimos Días / los Santos |
| líder mormón | líder de la Iglesia |
| historia mormona | historia de la Iglesia / historia de los Santos |
| pioneros mormones | pioneros Santos de los Últimos Días |
| doctrina mormona | doctrina de la Iglesia |

- Usar el nombre completo **«La Iglesia de Jesucristo de los Santos de los Últimos Días»** al menos una vez al inicio del texto.
- Después de la primera mención, usar **«la Iglesia»** o **«la Iglesia de Jesucristo»** como apócope.
- «Santos» (con mayúscula) o «Santos de los Últimos Días» son los gentilicios correctos.
- «SUD» solo en contextos internos, nunca en textos públicos.

---

## 2. Estructura de la biografía

Toda biografía sigue esta plantilla de secciones, en este orden:

1. **Apertura** (1-2 párrafos, sin subtítulo)
2. **Primeros años y conversión**
3. **Familia**
4. **Misiones y servicio eclesiástico**
5. **Obra y contribuciones**
6. **Pruebas y desafíos**
7. **Sus últimos días**
8. **Fuentes consultadas**

Es la única sección que permite lista con viñetas.

---

## 3. Principios de redacción

### 3.1 Narrativo puro
Prosa continua, sin viñetas, sin tablas, sin datos sueltos en el cuerpo. Las listas solo se permiten en «Fuentes consultadas».

### 3.2 Edificante pero no grandilocuente
Tono espiritual, favorable a la Iglesia, sin ser pomposo. Evitar frases como «su nombre sigue resonando» o interpretaciones subjetivas. Preferir lenguaje directo y objetivo.

### 3.3 Sin fechas sueltas
Las fechas van integradas en la oración. Ejemplo:
- Correcto: «Nacido en 1805 en Vermont...»
- Incorrecto: «Nació el 23 de diciembre de 1805. Su lugar de nacimiento fue Vermont.»

### 3.4 Desafíos incluidos
Mostrar pruebas y persecuciones con objetividad, sin dramatizar. Una biografía real muestra las pruebas —eso es lo que la hace edificante.

### 3.5 Errores doctrinales tratados con honestidad
Si la persona enseñó doctrina falsa o fue disciplinada, narrar el hecho con objetividad, reconociendo el error y el proceso correctivo. No endulzar ni ocultar.

### 3.6 Equilibrado y respetuoso
Nunca peyorativo hacia otras creencias. Favorable a la Iglesia sin ser tendencioso.

### 3.7 Sin copy-paste
Ningún párrafo debe ser idéntico al de una fuente. Redacción original, voz propia.

### 3.8 Citas trazables
Toda cita textual debe:
1. Tener citación explícita verificable (ej: «texto» (History of the Church, 3:232).)
2. Corresponder a la fuente original
3. Ser leal al original (traducir con precisión: «mean» ≠ «vile»)
4. Ir en español
5. Si no se puede verificar, mejor parafrasear

### 3.9 Citas despectivas: omitir si no edifican
Si una cita es despectiva, denigrante o negativa sin aportar contexto edificante a la narrativa, omitirla. La cita debe tener citación verificable.

### 3.10 Prioridad de fuentes
Joseph Smith Papers → ldsorg → biographical-encyclopedia (Jenson) → BYU RSC → CHD → church-news → Dialogue/BYU Studies → Utah History Encyclopedia → Wikipedia/Wikidata (último recurso).

### 3.11 Wikipedia no es fuente primaria
Wikipedia y Wikidata solo se usan como complemento, no como fuente principal. Consultar fuentes estables (Joseph Smith Papers, CHD, Jenson, RSC/BYU). Si no existen en el corpus, buscarlas en línea.

### 3.12 No conformarse con fuentes defectuosas
Si una fuente del corpus está incompleta, es una página de desambiguación o contiene datos incorrectos, descargar la fuente correcta y actualizar el corpus. Esto aplica a TODAS las fuentes, no solo Wikipedia.

### 3.13 Búsqueda exhaustiva
Si una fuente prioritaria no existe en el corpus, descargarla en línea. No limitarse a lo que ya está descargado.

### 3.14 Todas las fuentes disponibles
Consultar tantas fuentes como existan en `corpus/personajes/<slug>/`. No limitarse a 2-3. Extraer el texto completo de cada una.

### 3.15 Síntesis, no collage
No concatenar fuentes. Fusionar en un solo relato cronológico.

### 3.16 Actualizar el corpus
Siempre que se descargue una fuente correcta (porque la del corpus era incorrecta o faltaba), actualizar el archivo en `corpus/personajes/<slug>/`.

### 3.17 Evitar reiteración en todo el texto
Prohibida la reiteración de estructuras, frases o giros en todo el texto, no solo en aperturas.

En particular:
- **Aperturas**: Cada biografía debe tener un inicio único. «Hubo un hombre que...» máximo UNA vez en todo el sitio.
- **Transiciones**: No repetir «Por otro lado», «Además», «Cabe destacar», etc. en párrafos consecutivos.
- **Vocabulario**: Si «sirvió» aparece en un párrafo, usar sinónimos en el siguiente (actuó, trabajó, fue llamado, participó, etc.).
- **Estructura oracional**: Alternar entre oraciones largas y cortas. No empezar tres oraciones seguidas con el mismo sujeto.
- **Revisión previa**: Antes de escribir una nueva biografía, revisar las ya publicadas para no repetir giros característicos.

---

## 4. Metadatos del infobox

Después de publicar la biografía, poblar TODOS los campos aplicables:

| Meta key | Descripción | Ejemplo |
|----------|-------------|---------|
| `_author_description` | Cargo o descripción breve | «Tercer presidente de la Iglesia» |
| `_author_is_ga` | 1 si fue Autoridad General | 1 |
| `_author_birth_date` | Fecha de nacimiento en español | «1 de noviembre de 1808» |
| `_author_birth_place` | Lugar de nacimiento | «Milnthorpe, Westmorland, Inglaterra» |
| `_author_death_date` | Fecha de muerte | «25 de julio de 1887» |
| `_author_death_place` | Lugar de muerte | «Kaysville, Utah, Estados Unidos» |
| `_author_nationality` | Nacionalidad | «Estadounidense» |
| `_author_father` | Nombre del padre | «James Taylor» |
| `_author_mother` | Nombre de la madre | «Agnes Taylor» |
| `_author_spouses` | JSON array de cónyuges | `[{"name":"Leonora Cannon","marriage_year":1833}]` |
| `_author_callings` | JSON array de llamamientos | `[{"calling":"apostol","org":"Cuórum de los Doce"}]` |
| `_author_witness_type` | Solo para Testigos del Libro de Mormón | `three-witnesses` o `eight-witnesses` |

### Reglas específicas

- **`_author_is_ga`**: Solo `1` si la persona tuvo un llamamiento formal como Autoridad General. Los Testigos del Libro de Mormón (Martin Harris, David Whitmer) NO son GA.
- **`_author_callings`**: Para Testigos no-GA usar slug `testigo`, no `apostol`.
- **`_author_father`** y **`_author_mother`**: Siempre verificar. Se despliegan en el infobox.
- **`_author_spouses`**: Incluir al menos el cónyuge principal. Los matrimonios plurales se listan en el array.

---

## 5. Foto (imagen destacada)

- Usar `scripts/import-photo.sh <ID> <QID> <nombre>` para descargar desde Wikidata.
- El script usa `Special:FilePath` de Wikimedia Commons (no requiere calcular hash MD5).
- Verificar con `wp post meta list <ID>` que `_thumbnail_id` esté poblado.

---

## 6. Comentarios

- `comment_status` debe ser `open` en todo post de tipo `bc_quote_author`.
- Verificar con `wp post get <ID> --field=comment_status`.
- Abrir con `wp post update <ID> --comment_status=open`.

---

## 7. Post-publicación

Siempre ejecutar después de publicar:

```bash
scripts/verify-bio.sh <ID>
```

El script verifica:
- `post_content` > 500 caracteres (no corrupto)
- Metadatos del infobox poblados
- `_thumbnail_id` existe
- `comment_status` es `open`

---

## 8. Título de página

- El título de la página debe ser «La biografía de [nombre]».
- Se aplica automáticamente via el filtro `document_title_parts` en `inc/persona.php`
  (función `bc_persona_biography_title()`).

---

## 9. Share bar

- En páginas de biografía, el texto de la share bar debe ser «Comparte esta biografía».
- La share bar superior va ANTES de la card (back nav → share bar → card → contenido).
- No poner share bar entre la card y la biografía (error revertido).

---

## 10. OG / Twitter tags

- En `bc_quote_author`, los meta tags OG y Twitter usan «La biografía de [nombre]» como título.
- Implementado en `inc/og-tags.php`.

---

## 11. Page title dinámico

- `bc_persona_biography_title( $post_id )` retorna «La biografía de X».
- Aplicado por el filtro `document_title_parts`.
- Verificar que el title tag en el navegador muestre el formato correcto.

---

## 12. Pipeline técnico

### 12.1 Redacción
1. Leer fuentes del corpus (`corpus/personajes/<slug>/`)
2. Sintetizar en un solo relato cronológico
3. Escribir HTML con bloques de WordPress (`<!-- wp:paragraph -->`, etc.)
4. Guardar en `wp-content/uploads/bio-<slug>.html`

### 12.2 Publicación
```bash
scripts/publish-bio.sh <ID> /var/www/html/wp-content/uploads/bio-<slug>.html "<excerpt>"
```

### 12.3 Foto
```bash
scripts/import-photo.sh <ID> <QID> "<Nombre>"
```

### 12.4 Metadatos
```bash
docker exec wp_bc wp post meta update <ID> _author_<campo> "<valor>" --allow-root
```

### 12.5 Verificación
```bash
scripts/verify-bio.sh <ID>
```

### 12.6 Nota sobre MSYS2 (Git Bash)
En Windows con Git Bash, anteponer `MSYS2_ARG_CONV_EXCL="*"` a los scripts
para evitar que Git Bash convierta rutas Unix (ej: `/var/www/html/...`):
```bash
MSYS2_ARG_CONV_EXCL="*" bash scripts/publish-bio.sh ...
```

---

## 13. Excepciones conocidas

- **`_author_witness_type`**: Solo aplica a Testigos del Libro de Mormón. Para presidentes de la Iglesia, apóstoles y demás personas, debe quedar vacío. El script `verify-bio.sh` lo marcará como error — es esperado.
- **Testigos del Libro de Mormón**: De los Tres Testigos, solo Oliver Cowdery fue Autoridad General. Martin Harris y David Whitmer NO.
