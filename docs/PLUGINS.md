# Sistema de plugins
## Introducción
Este es un sistema de plugins simple, basado en eventos.
En cada archivo que se considere necesario, se ejecutan eventos en posiciones claves, por ejemplo `empieza_template` o `termina_template`.
Cada plugin debe ser pensado en funciones que se ejecutan en dichos eventos, las mismas serán llamadas automáticamente por el sistema de gestión.

## Instaladores y paquetes
Los plugins deben ser comprimidos en archivos `.zip`. El instalador lo descomprime automáticamente, y luego busca el archivo `instalar.json`.

## Archivo post-instalación *instalar.json*
Es un archivo json que debería contener algo como lo siguiente:
```
{
    "extension": "Nombre de la extension",
    "version": "Version de la extension",
    "autor": "Nombre del autor",
    "url": "Url de la web del autor o del plugin",
    "copiar": {
        "nombre_de_archivo": "/direccion/a/copiar"
    },
    "sql": [
        "lista de consultas SQL para ejecutar en la base de datos"
    ]
}
```
