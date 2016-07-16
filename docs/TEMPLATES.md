# Sistema de Templates
## Introducción
Todos los templates son guardados por defecto en la carpeta `/templates`.
Esto no es obligatorio ya que pueden ser guardados en otro lugar. Sin embargo, hay que modificar todos los archivos y definir manualmente dónde está la carpeta con los templates.

## Variables
* Se pueden utilizar variables que sean enviadas desde el código principal usando dos corchetes alrededor del nombre de la misma. Por ejemplo, `[[nombre]]`. Al cargar la página, el sistema compilará el template y cambiará ese texto por el contenido de la variable correspondiente.
* También es posible utilizar variables nativas de PHP. *Pero esto es peligroso*, ya que puede generar errores de compilación si la misma no está definida. El método para incrustar este tipo de dato, es el mismo que el anterior, pero incluyendo el símbolo `$` antes del nombre. Por ejemplo, `[[$apellido]]`.

## Includes
El sistema de templates también soporta llamar otros archivos estáticos, para separar las diferentes partes de un sitio en varios archivos, por ejemplo, en vez de tener un único archivo `index.html`, podríamos tener algo como lo siguiente:

* templates/
  * comun/
    * header.html
    * footer.html
    * navegacion.html
  * index.html
  * contacto.html

Después, en `index.html`, podríamos tener algo como:
 ```
<!DOCTYPE HTML>
<head>
    <title>Probando Templates</title>
</head>
<body>
    <!-- [include comun/header.html] -->
    <h1>Contenido de index.html</h1>
    <!-- [include comun/footer.html] -->
</body>
</html>
 ```
Y de esta forma, el sistema de templates cargaría los archivos que se pidan utilizando `<!-- [include url/al/archivo] -->`.

## Condiciones
El sistema de templates también soporta condiciones simples. La metodología es la siguiente:

```
<!-- if condicion then -->
...
<!-- else -->
...
<!-- end if -->
```

Notar que el bloque `<!-- else -->` en realidad es opcional.
