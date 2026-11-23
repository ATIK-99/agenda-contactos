# Actualización y entrega · Versión 2

## Sustituir los archivos locales

1. Guardar una copia de la carpeta actual y exportar la base como respaldo.
2. Extraer el ZIP y copiar el contenido de agenda-contactos sobre C:\xampp\htdocs\agenda-contactos.
3. Reemplazar archivos coincidentes. **Conservar .git y config/local.php**; no vienen en el paquete.
4. Verificar que la ruta sea agenda-contactos/index.php, sin otra carpeta agenda-contactos anidada.
5. No hay migración SQL para A y B: la base existente permanece igual.
6. Iniciar Apache y MySQL, ingresar a la agenda y recargar con Ctrl + F5.
7. Probar búsqueda por los tres campos, orden por nombre y ciudad en ambas direcciones, combinación y limpieza.

## Publicar la aplicación

Seguir [DESPLIEGUE.md](DESPLIEGUE.md). Antes de entregar, colocar la URL HTTPS real en el apartado **Enlaces y estado de publicación** del README y probarla desde una ventana privada.

## Actualizar el repositorio existente

Repositorio: https://github.com/ATIK-99/agenda-contactos.git

El código anterior está en **master**. No es necesario crear otro repositorio ni repetir git init o git remote add origin.

En CMD, dentro de C:\xampp\htdocs\agenda-contactos:

~~~bat
git status
git branch --show-current
~~~

La rama debe ser master, como en la entrega anterior. Si aparece otra, revisarlo antes de subir. Si es master:

~~~bat
git add .
git diff --cached --stat
git commit -m "Agregar busqueda, ordenamiento y preparacion para hosting"
git push origin master
~~~

El listado debe incluir código, SQL, README, guías y capturas. **config/local.php no debe aparecer**. Subir los archivos directamente, no solo un ZIP.

Si la URL pública todavía no estaba disponible al subir el código, completarla en README.md y realizar otro commit:

~~~bat
git add README.md
git commit -m "Documentar URL publica verificada"
git push origin master
~~~

Abrir https://github.com/ATIK-99/agenda-contactos/tree/master y comprobar que el README muestra las capturas y la URL correcta.

## Entrega final

| Entregable | Comprobación |
| --- | --- |
| URL del sistema | Funciona desde Internet: registro, acceso, CRUD, búsqueda y ordenamiento |
| GitHub | master contiene los cambios y el README muestra URL y capturas |
| SQL actualizado | sql/agenda.sql crea ambas tablas en la base seleccionada |

Las capturas incluidas son locales y usan datos ficticios. Si se añaden capturas del hosting, utilizar datos de demostración e indicar su entorno real.
