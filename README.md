# A library with all that you need
This is my collection of php functions/classes that helped me in my early php days. They were useful for making web apps with languages, plugins and template systems.
I may maintain it but not so often as I don't use php that much anymore. But anyway, I leave it here so may it help another one too.

# Language system
## Intro
A simple way to translate a full website.

## Translating
To create a language, just create a `.json` file with the language code ([ISO 639-1](https://en.wikipedia.org/wiki/ISO_639-1)) as its name.
If your project is too big, you can create multiple files which name begins with `_` and the system will compile it automatically.

## Using translations
To show a language var, you must write `[[_var_name]]`. That is, a `_` at the begining, and the name of the variable. 

* If the var is inside another one, you must write the whole route of variable names using a dot `.` to join them.
* The first character to identify a variable as a language variable is a `_` but you can change it in the `global.php` file, modifying the `idioma_var` constant.

# Template system
## Intro
Every template is saved by default in the `/templates` folder.
This is not mandatory as you may save it somewhere else. But if you do so, you need to change every file and define where the folder is.

## Variables
* You can use variables that are sent from the main code, do it by adding two brackets around its name. For example, `[[var_name]]`. When the page loads, the template system will compile it and change this with the variable content.
* It is also posible to use native PHP vars. *But pay atention*, it can produce some unexpected results if it is not defined. To do this, you use the same as before, but prepending a `$` before the name. For example, `[[$lastname]]`.

## Includes
The template system also supports includes to load static files and separate the different site parts in several files. For example, instead of having only a `index.html` file, you may have something like:

* templates/
  * common/
    * --- header.html
    * --- footer.html
    * --- navigation.html
  * index.html
  * contact.html

Then, in the `index.html`:
 ```
<!DOCTYPE HTML>
<head>
    <title>Template Features</title>
</head>
<body>
    <!-- [include common/header.html] -->
    <h1>Some content</h1>
    <!-- [include common/footer.html] -->
</body>
</html>
 ```
This way, the system will load all files `<!-- [include url/to/file] -->`.

## Conditionals
The template system also supports simple conditions. The syntax is like this:

```
<!-- if condition then -->
...
<!-- else -->
...
<!-- end if -->
```

Of course, the `<!-- else -->` is optional.

# Plugin system
## Intro
This is a simple plugin system, based on events.
In every file that is considered necesary, we call events in key positions, for example, when the template begins, we use `empieza_template`. And when it ends, we call `termina_template`.
Each plugin must be designed in hooks that are called in those events. They will be run automatically.

## Installer and packages
The plugins must be compressed in `.zip` files. The installer will decompile it and search for the `instalar.json` file.

## Post-installer file: *instalar.json*
Is a json file that should have something like this:
```
{
    "extension": "Extension name",
    "version": "Version",
    "autor": "Autor name",
    "url": "Autor or plugin URL",
    "copiar": {
        "filename": "/copy/destination"
    },
    "sql": [
        "A list will SQL Querys to be executed",
        "You can use it to add plugin-specific tables",
        "Or update some data"
    ]
}
```