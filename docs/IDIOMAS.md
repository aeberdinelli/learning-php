# Sistema de idiomas
## Introducción
El sistema de idiomas, es una forma muy simple de traducir toda la web.

## Traduciendo contenido
Para crear un idioma, simplemente se debe crear un archivo `.json` cuyo nombre sea el código de idioma según la [ISO 639-1](https://en.wikipedia.org/wiki/ISO_639-1).

Si el proyecto es demasiado grande, se pueden crear múltiples archivos anteponiendo un guíon bajo `_`. El sistema compilará automáticamente los cambios nuevos para generar un único JSON para cargarlo cuando sea necesario.

## Mostrando contenido
Para mostrar una determinada variable de idioma, simplemente se debe escribir `[[_nombre_variable]]`. Es decir, un `_` y después el nombre de la variable.

* Si la variable está dentro de otra, se debe escribir toda la ruta hacia la misma utilizando un punto `.` para navegar por las diferentes variables.
* El primer caracter que por defecto es un `_` puede modificarse en el archivo `global.php`, cambiando la constante `idioma_var`.
