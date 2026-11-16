# Entrega del proyecto

## Actualizar la copia existente sin perder datos

1. Guardar una copia de la carpeta actual fuera de `htdocs`.
2. Extraer el ZIP y copiar la carpeta `agenda-contactos` a `C:\xampp\htdocs`, reemplazando los archivos anteriores.
3. Verificar que no quedó una carpeta duplicada: la ruta debe terminar en `agenda-contactos/index.php`, no `agenda-contactos/agenda-contactos/index.php`.
4. Conservar los nombres originales sin sufijos `(1)`.
5. No borrar la base de datos. Si conserva las tablas de la actividad, no ejecutar cambios SQL.
6. Si había una contraseña propia para MySQL, establecerla en `config/local.php` usando la plantilla.
7. Cerrar la sesión anterior, volver a ingresar y actualizar el navegador con Ctrl+F5.
8. Ejecutar las pruebas de `PRUEBAS.md`.

## SQL

`sql/agenda.sql` instala desde cero y no incluye DROP ni datos personales. En una base existente con la estructura original no se requieren migraciones. Si la estructura es distinta, revisar primero en phpMyAdmin; IF NOT EXISTS no actualiza columnas existentes.

## Repositorio público

El paquete está listo para subir, pero el repositorio externo no viene creado.

Opción con Git desde la terminal de Visual Studio Code, abierta en la carpeta del proyecto:

```bash
git init
git add .
git commit -m "Completar agenda personal en PHP y MySQL"
git branch -M main
```

Crear en GitHub un repositorio público llamado `agenda-contactos`, vacío (sin README ni .gitignore adicionales). Copiar su URL y ejecutar, sustituyendo TU_USUARIO:

```bash
git remote add origin https://github.com/TU_USUARIO/agenda-contactos.git
git push -u origin main
```

Si Git solicita identidad, configurar nombre y correo propios antes del commit. Si ya existe un remoto origin, comprobarlo con `git remote -v` antes de agregar otro. Autenticarse con el mecanismo de GitHub; no guardar tokens ni contraseñas en el proyecto.

También es posible cargar los archivos desde la web del repositorio, conservando las subcarpetas. No cargar únicamente el ZIP: el README y código deben poder examinarse directamente. No subir `config/local.php`, registros ni exportaciones con usuarios reales. `.gitignore` actúa con Git, no al seleccionar archivos manualmente.

## Elementos que se entregan

- Carpeta completa del proyecto (se puede comprimir en ZIP).
- `sql/agenda.sql`.
- Enlace del repositorio público con README visible.

Comprobar el enlace en una ventana privada para verificar su acceso público. Las capturas son opcionales salvo que el docente las solicite.
