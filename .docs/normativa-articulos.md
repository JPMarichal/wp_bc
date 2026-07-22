# Normativa y Estándares de Redacción de Artículos (wp_bc)

> Este documento persiste las normas editoriales, metodológicas y de calidad para la creación de artículos en el proyecto `wp_bc`, asegurando un comportamiento estándar e inalterable entre sesiones.

## 1. Rigor y Calidad en la Investigación
- **Prohibido apresurarse**: La velocidad de ejecución (mediante scripts o generadores) es una herramienta de eficiencia operativa, pero **nunca** debe comprometer la profundidad temática, el rigor histórico ni la precisión doctrinal.
- **Fuentes obligatorias**: Cada artículo debe fundamentarse en fuentes autorizadas (textos canónicos, manuales de la Iglesia, Encyclopedia of Mormonism, BYU RSC, Wikipedia, Encyclopaedia Britannica).
- **Enfoque de la Restauración**: Integrar de manera coherente y natural las perspectivas doctrinales de La Iglesia de Jesucristo de los Santos de los Últimos Días (Traducción de José Smith, revelación moderna, profetas y convenios) sin descuidar el contexto histórico-crítico general.

## 2. Estructura y Componentes de Contenido
- **Bloques Gutenberg**: Usar bloques dinámicos self-closing para pasajes de Escrituras:
  ```html
  <!-- wp:lds-passage-block/passage {"volume":"...","book":"...","chapter":N,"startVerse":N,"endVerse":N} /-->
  ```
  *Nunca* usar `core/quote` para Escrituras canónicas.
- **Tabla de Referencias (`bc-forma-t`)**:
  - Encabezados obligatorios: `Concepto | Referencia`.
  - **No limitativa**: El número de pasajes debe ser exhaustivo y responder al contenido real del artículo (habitualmente entre 10 y 15 pasajes significativos).
- **Fuentes Consultadas**: Listado con enlaces externos estructurados (`target="_blank" rel="noopener noreferrer"` con icono externo).

## 3. Automatización Eficiente
- Utilizar el generador universal (`bin/generate-article.php`) y archivos de configuración JSON estructurados para asegurar la consistencia de taxonomías, tags, series y metadatos sin errores manuales.
