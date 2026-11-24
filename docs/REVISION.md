# Verificación de la versión 2

Fecha de revisión: 22 de noviembre de 2026.

Código base: rama master del repositorio ATIK-99/agenda-contactos, commit ef232fb. La actualización incorpora búsqueda por nombre, correo y ciudad, y ordenamiento por nombre o ciudad.

## Entorno y alcance

Pruebas ejecutadas con PHP 8.4.27 (servidor de desarrollo), PDO MySQL, mbstring, MariaDB 11.8.9 y Chromium Headless, mediante automatización del navegador. Se usó una base aislada llamada agenda_qa, dos cuentas ficticias y contactos de demostración. No se utilizó la base de XAMPP del estudiante ni una cuenta de hosting.

- Sintaxis: los 14 archivos PHP distribuibles pasaron la comprobación php -l.
- Importación: el SQL creó las tablas en una base seleccionada con un nombre diferente de agenda_personal.
- Reimportación: se conservaron exactamente los 2 usuarios y 5 contactos de la base de prueba.
- Contraseñas: se comprobó que el valor almacenado era un hash.
- Capturas: cuatro capturas reales de la ejecución local, revisadas visualmente; también se revisó la pantalla móvil de 390 px.
- Cambios: git diff --check sin errores de formato.

## Pruebas funcionales aprobadas

1. Páginas privadas redirigen al login sin sesión.
2. Registro, autenticación y agenda vacía con dos usuarios.
3. Creación de contactos y listado exclusivo del propietario.
4. Búsqueda parcial por nombre, correo, ciudad y todos los campos.
5. La búsqueda con OR conserva el aislamiento entre usuarios.
6. Orden por nombre y ciudad en ambas direcciones, con desempates.
7. Combinación, persistencia al recargar y limpieza de filtros.
8. Sin resultados, inyección SQL como texto y salida HTML escapada.
9. Parámetros desconocidos, arrays y longitud de búsqueda controlados.
10. Caracteres %, _ y ! se buscan literalmente.
11. Confirmación de eliminación y rechazo de token CSRF inválido.
12. Acceso y modificación de contactos ajenos rechazados.
13. Edición de contacto reflejada en los resultados de búsqueda.
14. Diseño móvil sin desbordamiento de página y navegación por teclado.
15. Cierre de sesión e inaccesibilidad posterior del panel.

## Estado del despliegue

- Cuenta de hosting creada en InfinityFree.
- Archivos de la aplicación cargados en el hosting.
- Configuración de conexión MySQL corregida mediante config/local.php.
- Error de conexión resuelto durante la configuración.
- Código de la versión 2 actualizado en la rama master de GitHub.
- URL pública incorporada al README.

URL del sistema: https://agenda-contactos-atik99.infinityfreeapp.com/

## Verificación en el hosting

Las pruebas anteriores corresponden al entorno local. La conexión con la base de datos del hosting fue corregida. Queda por registrar la comprobación completa de registro, inicio de sesión, creación, edición, eliminación, búsqueda, ordenamiento y cierre de sesión en la aplicación publicada.
