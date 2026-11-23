# Publicación en InfinityFree

La publicación se considera terminada después de probar el sistema desde su URL pública.

## 1. Crear cuenta y sitio

Entrar en https://www.infinityfree.com/, crear una cuenta gratuita y completar la verificación solicitada. Crear una cuenta de hosting y elegir un subdominio disponible. Guardar la URL exacta asignada para incorporarla al README tras verificarla.

## 2. Crear e importar la base

En el panel de la cuenta, abrir **MySQL Databases** y crear una base para la agenda. Anotar hostname, nombre completo de base y usuario, incluidos los prefijos del proveedor.

Abrir phpMyAdmin desde ese panel, **seleccionar la base**, entrar en **Importar** y cargar **sql/agenda.sql**. No contiene CREATE DATABASE ni USE: utiliza la base seleccionada. Deben aparecer usuarios y contactos.

La base queda sin registros. Crear una cuenta desde la aplicación y añadir contactos ficticios para evaluar. Los registros de XAMPP no se trasladan automáticamente: necesitan una exportación privada adicional si se desea conservarlos en el hosting.

## 3. Configurar la conexión

En una copia destinada al hosting, copiar config/hosting.ejemplo.php como **config/local.php** y completar:

| Clave | Valor del panel |
| --- | --- |
| servidor | MySQL Host Name |
| puerto | Puerto del proveedor; habitualmente 3306 |
| base_datos | MySQL DB Name completo, incluido el prefijo |
| usuario | MySQL User Name |
| contrasena | Contraseña de la cuenta de hosting/MySQL indicada en el panel |

En InfinityFree la contraseña de la cuenta de hosting puede diferir de la del área de cliente. El hostname tampoco es localhost ni el 127.0.0.1 de XAMPP.

Conservar configuraciones distintas en XAMPP y hosting. No probar la base remota desde XAMPP: InfinityFree restringe el acceso externo a sus bases gratuitas.

## 4. Subir archivos

En el administrador de archivos o mediante FTP, entrar en **htdocs** del subdominio y subir:

- Todos los archivos PHP de la raíz, incluido index.php.
- Las carpetas assets, config e includes con todo su contenido.
- El .htaccess de la raíz y los de config e includes.
- El config/local.php con los datos del hosting.

index.php debe quedar en **htdocs/index.php**. Si aparece una página de bienvenida del proveedor, comprobar si un index.html o index2.html la está mostrando y retirar solo ese archivo de bienvenida.

docs, sql, README.md, LinkVideo.docx y .git no son necesarios en el hosting. El SQL se importa en phpMyAdmin. Documentación, SQL y capturas sí permanecen en GitHub.

## 5. Configurar HTTPS

Configurar e instalar el certificado gratuito desde las opciones SSL de la cuenta, siguiendo las indicaciones vigentes del proveedor. Esperar su activación si corresponde. Comprobar que https:// abre el sitio sin advertencias antes de usar esa URL como enlace final.

La aplicación utiliza rutas relativas y activa Secure en la cookie cuando PHP identifica la petición como HTTPS.

## 6. Verificar desde Internet

En una ventana privada:

1. Abrir la URL pública: debe aparecer el inicio de sesión.
2. Registrar una cuenta e iniciar sesión.
3. Crear tres contactos ficticios en ciudades diferentes.
4. Buscar por nombre, correo y ciudad, seleccionando cada campo.
5. Ordenar por nombre y ciudad en ambas direcciones.
6. Combinar búsqueda y ordenamiento; limpiar los filtros.
7. Editar y eliminar un contacto con confirmación.
8. Crear otra cuenta: no debe ver ni modificar los contactos de la primera.
9. Cerrar sesión y abrir panel.php: debe pedir autenticación.
10. Probar también desde otro navegador o dispositivo con Internet.

Registrar los resultados reales en docs/REVISION.md, completar la URL en README.md y actualizar GitHub según [ENTREGA.md](ENTREGA.md).

## Problemas habituales

| Síntoma | Qué revisar |
| --- | --- |
| Error de conexión | Valores exactos del panel en config/local.php |
| Error al importar SQL | Haber creado y seleccionado la base antes de importar |
| Página de bienvenida | Ubicación de index.php y archivo de bienvenida del proveedor |
| Error 404 | Subdominio, directorio htdocs y nombres sin sufijos (1) |
| Faltan registros locales | El SQL incluido contiene solo la estructura |
| Estilos anteriores | Subir estilos.css y recargar con Ctrl + F5 |
| Faltan cambios en GitHub | Consultar master y revisar el resultado del push |

## Documentación oficial

- [Características del hosting](https://www.infinityfree.com/).
- [Crear una base MySQL](https://forum.infinityfree.com/t/how-to-setup-a-new-mysql-database/49342/1).
- [Migrar un sitio](https://forum.infinityfree.com/t/how-to-migrate-your-website-to-infinityfree/49315/1).
- [Conectar con MySQL](https://forum.infinityfree.com/t/how-to-connect-with-mysql/49340).
