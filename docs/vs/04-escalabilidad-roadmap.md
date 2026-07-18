# Escalabilidad y Plan de Implementación

## 1. Escalabilidad por Pilar

### Pilar 1: Enciclopedia de Personajes Bíblicos

| Aspecto | Detalle |
|---------|---------|
| **Esfuerzo unitario** | ~10-15 min por personaje (con pipeline IA existente) |
| **Volumen total** | 100+ personajes |
| **Tiempo estimado** | 2-3 semanas (batch) |
| **Recursos existentes** | Framework `bc_quote_author`, skill `biografia-persona`, Alejandría |
| **Qué falta** | Extensión del skill para personajes bíblicos (no SUD), CPT separado o extensión |
| **Ciclo** | Personajes principales primero (Abraham, Moisés, David), luego secundarios |
| **Reutilización** | Sirve para cualquier año que estudie AT (2022, 2026, 2030, etc.) y para estudio independiente |

### Pilar 2: Geografía Bíblica Interactiva

| Aspecto | Detalle |
|---------|---------|
| **Esfuerzo unitario** | ~5-10 min por ubicación (con pipeline existente) |
| **Volumen total** | 300+ ubicaciones (act: ~50) |
| **Tiempo estimado** | Continuo (se puede hacer en lotes de 20-30) |
| **Recursos existentes** | CPT `bc_location`, MapLibre GL, skill `glosario-ubicaciones-contenido` |
| **Qué falta** | Más datos de AT, mapas de rutas (nuevo tipo de contenido) |
| **Ciclo** | Priorizar ubicaciones que aparecen en las primeras semanas de CFM |
| **Reutilización** | Las ubicaciones son transversales a todos los años y libros canónicos |

### Pilar 3: Conexiones con la Restauración

| Aspecto | Detalle |
|---------|---------|
| **Esfuerzo unitario** | Automatizable vía Alejandría (búsqueda semántica) |
| **Volumen total** | 52 guías + 30 artículos |
| **Tiempo estimado** | 1 semana para batch de 52 (setup de skill) |
| **Recursos existentes** | MCP Alejandría (search_hybrid, kg_find) |
| **Qué falta** | Skill nuevo `conexiones-restauracion` que procese el bloque semanal y genere conexiones |
| **Reutilización** | Conexiones LdM-AT válidas permanentemente; artículos temáticos son contenido editorial permanente |

### Pilar 4: Herramientas de Estudio Visuales

| Aspecto | Detalle |
|---------|---------|
| **Esfuerzo unitario** | 1-2 horas por herramienta (setup inicial de datos + script) |
| **Volumen total** | 20+ herramientas |
| **Tiempo estimado** | 2-3 semanas (construcción de datos + generación) |
| **Recursos existentes** | Datos bíblicos en Alejandría, scripts de generación (a crear) |
| **Qué falta** | Base de datos estructurada de reyes, profetas, fechas; scripts de visualización |
| **Reutilización** | Una vez construidos los datos, se reutilizan cada ciclo del AT y se actualizan para NT/LdM |

### Pilar 5: Biblioteca de Recursos Descargables

| Aspecto | Detalle |
|---------|---------|
| **Esfuerzo unitario** | Automatizable (template + datos → PDF) |
| **Volumen total** | 200+ PDFs |
| **Tiempo estimado** | 1 semana (setup de templates + script de generación) |
| **Recursos existentes** | Ninguno (hay que crear templates) |
| **Qué falta** | Templates HTML+CSS para cada tipo de PDF, script de generación |
| **Reutilización** | Los templates se reutilizan cada año; solo cambian los datos |

### Pilar 6: Contexto Histórico y Cultural

| Aspecto | Detalle |
|---------|---------|
| **Esfuerzo unitario** | 5-10 min por ficha semanal (automatizado) |
| **Volumen total** | 52 fichas + 30 artículos |
| **Tiempo estimado** | 1-2 semanas |
| **Recursos existentes** | Alejandría, skills de investigación |
| **Qué falta** | Skill que extraiga contexto para cada bloque semanal |
| **Reutilización** | El contexto histórico del AT es permanente; los artículos profundos son contenido evergreen |

## 2. Plan de Implementación por Fases

### Fase 0 — Setup Técnico (1-2 semanas)

- [ ] Crear CPT `bc_bible_character` para personajes del AT
- [ ] Extender skill `biografia-persona` para personajes bíblicos
- [ ] Crear taxonomy `cfm_week` para asociar contenido a semanas específicas
- [ ] Template `single-cfm-week.php` para las páginas semanales
- [ ] Sistema de registro de usuarios (login, progreso, favoritos, notas)
- [ ] Plugin de newsletter (suscripción semanal)
- [ ] Template de PDF (HTML → CSS → PDF vía script)

### Fase 1 — Contenido Batch (3-4 semanas)

- [ ] Generar 20 biografías de personajes principales del AT (Abraham, Moisés, David, etc.)
- [ ] Generar 52 fichas de contexto histórico semanal (batch)
- [ ] Generar 52 hojas de conexiones con la Restauración (batch)
- [ ] Ampliar ubicaciones: agregar 100 ubicaciones del AT al CPT existente
- [ ] Crear 5 herramientas visuales iniciales (cronología, genealogía Abraham, tabla reyes)

### Fase 2 — Lanzamiento (1 semana)

- [ ] Landing page "Ven, Sígueme 2026 — Recursos de Apoyo"
- [ ] Publicar primeras 10 guías semanales completas
- [ ] Newsletter funcionando
- [ ] Descargables de las primeras 10 semanas
- [ ] Verificación con usuarios reales (feedback)

### Fase 3 — Contenido Semanal Continuo

- [ ] Publicar guía semanal cada lunes (52 semanas)
- [ ] Publicar personaje destacado cada miércoles
- [ ] Publicar geografía interactiva cada viernes
- [ ] Newsletter semanal automatizado
- [ ] Iterar según feedback

### Fase 4 — Expansión

- [ ] Completar 100+ biografías de personajes
- [ ] Completar 300+ ubicaciones
- [ ] 20+ herramientas visuales
- [ ] Podcast semanal en español (opcional)
- [ ] 200+ PDFs descargables
- [ ] Comunidad activa en comentarios
- [ ] Versión en inglés (opcional, futuro)

## 3. Proyección de Contenido Anual

| Tipo de contenido | Año 1 | Año 2 | Año 3 (madurez) |
|-------------------|-------|-------|------------------|
| Guías semanales | 52 | 52 | 52 (nuevo ciclo) |
| Biografías personajes | 40 | 80 | 100+ |
| Ubicaciones mapeadas | 150 | 250 | 300+ |
| Conexiones Restauración | 52 | 52 + 30 artículos | 52 + 60 artículos |
| Herramientas visuales | 10 | 20 | 30 |
| PDFs descargables | 100 | 200 | 300+ |
| Artículos de contexto | 30 | 60 | 90 |
| Usuarios registrados | (proyectado) | (crecimiento) | (madurez) |

## 4. Proyección Multi-Anual

El calendario de CFM sigue un ciclo de 4 años:

| Año | Bloque | Estrategia Verdades Eternas |
|-----|--------|-----------------------------|
| **2026** | AT | Lanzar con personajes + ubicaciones del AT |
| **2027** | NT | Personajes y ubicaciones del NT (mismos CPTs, nuevo contenido). Mapas de Tierra Santa en tiempos de Cristo |
| **2028** | LdM | Personajes y ubicaciones del LdM (Mesamérica, arabia). Mapas de rutas de Lehi, tierra de Nefi, etc. |
| **2029** | DyC | Personajes de la Restauración (bc_quote_author ya existe). Ubicaciones de la historia de la Iglesia (Kirtland, Nauvoo, Salt Lake) |
| **2030** | AT | Reutilizar contenido del AT + actualizar/mejorar |

**Ventaja**: Cada año se aprovecha la infraestructura existente. Los CPTs de personajes y
ubicaciones son transversales a todos los libros canónicos. El contenido se acumula y se
vuelve más valioso con cada ciclo.

## 5. Requisitos Técnicos Nuevos

### CPTs a crear
- `bc_bible_character` (personajes bíblicos): reutiliza lógica de `bc_quote_author`
  pero con campos específicos (escrituras asociadas, rol bíblico, época, etc.)

### Taxonomías a crear
- `cfm_week`: asocia cualquier contenido a una semana específica del calendario CFM.
  Términos: `2026-week-01`, `2026-week-02`, ... `2026-week-52`

### Templates a crear
- `single-cfm-week.php`: dashboard semanal que agrega mapas, personajes, conexiones
- `archive-cfm-week.php`: calendario de todas las semanas
- `single-bc_bible_character.php`: biografía de personaje bíblico
- `archive-bc_bible_character.php`: glosario de personajes

### Skills nuevos
- `apoyo-cfm-semanal`: genera la página semanal completa (mapa + personaje + conexiones + contexto)
- `conexiones-restauracion`: busca paralelos entre el bloque del AT y LdM/DyC/PGP
- `personaje-biblico`: adaptación de `biografia-persona` para personajes bíblicos

### Funcionalidades de usuario
- Sistema de registro/login
- Progreso de lectura (checklist de semanas)
- Notas personales por semana
- Favoritos (personajes, ubicaciones)
- Historial de descargas
- Newsletter (plugin de suscripción + envío automatizado)

## 6. Métricas de Éxito

| Métrica | Corto plazo (3 meses) | Medio plazo (1 año) |
|---------|-----------------------|---------------------|
| Personajes publicados | 40 | 100+ |
| Ubicaciones mapeadas | 150 | 300+ |
| Guías semanales completas | 52 | 52 (cada año) |
| Usuarios registrados | — | — |
| Descargas de PDFs | — | — |
| Suscriptores newsletter | — | — |
| Tiempo en sitio (promedio) | — | — |

> (Nota: las métricas cuantitativas se definirán al implementar analytics)
