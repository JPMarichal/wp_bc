---
name: aplicar-citaciones
description: |
  Aplicar el Formato de Citas para Dilton (FCD) en artículos de wp_bc.
  Usar SIEMPRE que se necesite: (1) formatear la sección "Fuentes consultadas"
  de un post o CPT, (2) verificar que las citas bibliográficas cumplen el FCD,
  (3) corregir referencias mal formateadas, (4) normalizar citas de Escrituras,
  conferencias, libros, sitios web, manuales y otras fuentes.
  NO usar para el formato de pasajes de Escrituras en bloque (usar el skill
  crear-editar-posts para eso).
---

# Skill: Aplicar Citaciones (FCD)

## ¿Qué es FCD?

**FCD** = **Formato de Citas para Dilton**. Es el sistema de referencias bibliográficas que rige todas las "Fuentes consultadas" y citas parentéticas en wp_bc.

## Cuándo usar

- Al crear o editar la sección **"Fuentes consultadas"** de cualquier post o CPT
- Al verificar que una referencia bibliográfica cumple el formato correcto
- Al normalizar citas existentes que no sigan el FCD
- Al integrar este skill con `crear-editar-posts`, `biografia-persona` o `glosario-ubicaciones-contenido`

## Formato por tipo de fuente

### Tabla FCD

| Tipo | Formato | Ejemplo |
|:-----|:--------|:--------|
| **Escrituras** | `(Libro capítulo:versículo)` | `(Juan 3:16)` o `(DyC 76:22–24)` |
| **Conferencia General** | `(Autor, "Título del discurso", Conferencia general de mes de año)` | `(Jeffrey R. Holland, "El costoso —y sublime— don de la oración", Conferencia general de octubre de 2024)` |
| **Otros discursos** | `(Autor, "Título", ocasión, fecha)` | `(Spencer W. Kimball, "The False Gods We Worship", Discurso de devocional, Universidad Brigham Young, 4 de junio de 1974)` |
| **Libros** | `(Autor, "Título del libro", cap. X)` | `(Hugh Nibley, "Lehi in the Desert", cap. 3)` |
| **Cap. con autor distinto** | `(Autor del cap., "Título del cap.", en Autor del libro [ed.], "Título del libro")` | `(John W. Welch, "The Power of the Word", en Richard L. Anderson [ed.], "Book of Mormon Studies")` |
| **Artículos académicos** | `(Autor, "Artículo", Revista, vol. X, núm. Y, año)` | `(Matthew L. Bowen, "‘Most Desirable Above All Things’", Interpreter, vol. 44, núm. 2, 2021)` |
| **Sitios web oficiales** | `(ChurchofJesusChrist.org, "Título de la página")` | `(ChurchofJesusChrist.org, "Oración")` |
| **Otros sitios web** | `(Autor, Sitio, "Título del artículo")` | `(Book of Mormon Central, "¿Qué es el Libro de Mormón?")` |
| **Manuales de la Iglesia** | `(Nombre del manual, X.X.X, "Título de la sección")` | `(Predicad Mi Evangelio, 3.2, "La doctrina de Cristo")` |
| **Materiales propios** | `(apuntes personales sobre…, año)` | `(apuntes personales sobre el Evangelio de Juan, 2024)` |

### Reglas generales de la tabla

1. **Todo entre paréntesis** — el formato completo de la referencia va entre paréntesis `( )`
2. **Títulos en español entre comillas dobles** — `"Título del discurso"`, `"Título del libro"` (excepto Escrituras y manuales, que no llevan comillas alrededor del título)
3. **Separación por comas** — los elementos se separan con coma y espacio
4. **Guion para rangos** — usar `–` (en dash) para rangos de versículos, páginas, fechas
5. **Idioma** — priorizar español. Si la fuente original está en inglés, usar el título original en inglés entre comillas
6. **Autor** — nombre completo en el orden: Nombre Apellido (no invertir). Si no hay autor conocido, omitir
7. **URLs** — NO incluir URLs dentro del paréntesis FCD. Las URLs van en el `<a href="...">` de la lista "Fuentes consultadas"

### Alcance: solo obras NO escriturarias

Las "Fuentes consultadas" listan exclusivamente **obras externas a las Escrituras canónicas**: discursos de conferencia, libros, artículos académicos, manuales, sitios web, materiales propios, etc.

**No incluir** referencias a pasajes de las Escrituras (Éxodo, Hechos, Alma, DyC, Moisés, etc.). Esas referencias se gestionan mediante la taxonomía `bc_chapter` (skill `asignar-capitulos`). La tabla FCD incluye "Escrituras" como formato para citas inline dentro del texto, pero no para la sección "Fuentes consultadas".

## Aplicación en "Fuentes consultadas"

Cada entrada de la lista `<ul>` en "Fuentes consultadas" combina:

1. **El enlace** (con URL, target=_blank, icono external-link)
2. **La referencia FCD** entre paréntesis, como descripción

### Estructura HTML

```html
<ul class="wp-block-list">
<li><a href="URL" target="_blank" rel="noopener noreferrer">Título visible <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (Referencia FCD)</li>
</ul>
```

### Ejemplos por tipo

**Escritura:**
```html
<li><a href="https://www.churchofjesuschrist.org/study/scriptures/ot/gen/19" target="_blank" rel="noopener noreferrer">Génesis 19 <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (Génesis 19:1—11)</li>
```

**Conferencia General:**
```html
<li><a href="https://www.churchofjesuschrist.org/study/general-conference/2024/10/13holland" target="_blank" rel="noopener noreferrer">El costoso —y sublime— don de la oración <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (Jeffrey R. Holland, "El costoso —y sublime— don de la oración", Conferencia general de octubre de 2024)</li>
```

**Libro:**
```html
<li><a href="https://ejemplo.com" target="_blank" rel="noopener noreferrer">Lehi in the Desert <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (Hugh Nibley, "Lehi in the Desert", cap. 3)</li>
```

**Sitio web oficial:**
```html
<li><a href="https://www.churchofjesuschrist.org/study/manual/gospel-topics/prayer" target="_blank" rel="noopener noreferrer">Oración <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (ChurchofJesusChrist.org, "Oración")</li>
```

**Manual de la Iglesia:**
```html
<li><a href="https://www.churchofjesuschrist.org/study/manual/preach-my-gospel/3" target="_blank" rel="noopener noreferrer">Predicad Mi Evangelio <i class="fas fa-external-link-alt" aria-hidden="true"></i></a> — (Predicad Mi Evangelio, 3.2, "La doctrina de Cristo")</li>
```

**Material propio:**
```html
<li>— (apuntes personales sobre el Evangelio de Juan, 2024)</li>
```

## Integración con otros skills

| Skill | Cómo se integra |
|:------|:----------------|
| `crear-editar-posts` | Usa este skill para dar formato FCD a la sección "Fuentes consultadas" de cada post |
| `biografia-persona` | Usa este skill para formatear las fuentes al pie de cada biografía |
| `glosario-ubicaciones-contenido` | Usa este skill para formatear las fuentes de cada entrada de ubicación |
| `normativa` | Referencia este skill como la autoridad en formato de citas |

## Sobre fuentes de Alejandría

Alejandría es una herramienta de búsqueda interna, no una fuente. **No citar "Alejandría" ni "corpus" como fuente.** En lugar de eso, desglosar las obras específicas que se consultaron dentro del corpus:

| Lo que se consultó en Alejandría | Cómo se cita (FCD) |
|:---------------------------------|:--------------------|
| Escrituras canónicas | `(Génesis 19:1–11)`, `(1 Nefi 3:7)`, `(DyC 76:22–24)` |
| Discurso de conferencia | `(Jeffrey R. Holland, "Título", Conferencia general de mes de año)` |
| Manual de la Iglesia | `(Predicad Mi Evangelio, 3.2, "La doctrina de Cristo")` |
| Artículo de revista académica | `(Autor, "Artículo", Revista, vol. X, núm. Y, año)` |
| Discurso de devocional | `(Autor, "Título", ocasión, fecha)` |

**Regla**: Si se consultó un pasaje de Escrituras en Alejandría, se cita como Escritura. Si se consultó un discurso de conferencia, se cita como Conferencia General. La herramienta de búsqueda es transparente para el lector.

## Verificación rápida

Antes de dar por terminada una sección "Fuentes consultadas", verificar:

- [ ] Cada entrada sigue el formato FCD de su tipo
- [ ] Todas las URLs funcionan y apuntan a la fuente correcta
- [ ] Los títulos están en español (o en inglés original si no hay traducción)
- [ ] Las referencias de Escrituras usan guion `–` para rangos
- [ ] No hay URLs dentro del paréntesis FCD
- [ ] No hay menciones a "Alejandría", "corpus" ni otras herramientas internas como fuente
- [ ] Las obras consultadas están desglosadas por título específico
- [ ] No hay escrituras canónicas (la taxonomía `bc_chapter` cubre eso)
- [ ] No hay fuentes no consultadas realmente
