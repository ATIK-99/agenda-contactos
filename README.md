# Agenda personal de contactos

Proyecto académico de Desarrollo Web de quinto ciclo. Aplicación en PHP y MySQL que permite a cada usuario registrar una cuenta, iniciar sesión y administrar exclusivamente sus propios contactos.

## Funcionalidades

- Registro con nombre, correo único y contraseña protegida con `password_hash()`.
- Inicio de sesión con `password_verify()` y renovación del identificador de sesión.
- Panel privado con nombre, correo y contactos del usuario.
- Registro, edición y eliminación de contactos con confirmación previa.
- Validación en el servidor, consultas preparadas PDO, escape de salida HTML y tokens CSRF.
- Control de propiedad mediante `id_usuario` en consultas, actualizaciones y eliminaciones.
- Cierre de sesión con eliminación de datos y cookie.
- CSS propio adaptable a pantallas pequeñas, sin JavaScript ni frameworks.

## Tecnologías

PHP 8.x, MySQL o MariaDB compatible, PDO MySQL, HTML5 y CSS3. Apache y phpMyAdmin mediante XAMPP para ejecución local.

## Requisitos

- PHP 8.0 o superior con extensiones `pdo_mysql` y `mbstring` activadas.
- MySQL 5.7+ o MariaDB 10.4+ con InnoDB y utf8mb4.
- Apache 2.4 (XAMPP) y navegador moderno.
- No requiere Composer, Node.js ni conexión a Internet para funcionar.

## Instalación en XAMPP

1. Copiar la carpeta `agenda-contactos` dentro de `C:\xampp\htdocs`.
2. Iniciar Apache y MySQL en el panel de XAMPP.
3. Abrir `http://localhost/phpmyadmin/`.
4. Para una instalación nueva, importar `sql/agenda.sql`. Crea `agenda_personal` y las tablas `usuarios` y `contactos` sin cargar usuarios ni datos personales.
5. Si ya existen las tablas creadas durante la actividad, no es necesario importar de nuevo. El script usa `IF NOT EXISTS`: no borra registros, pero tampoco corrige una estructura diferente.
6. Revisar la configuración de `config/conexion.php`: servidor `127.0.0.1`, puerto `3307`, base `agenda_personal`, usuario local `root`, contraseña vacía.
7. Si MySQL utiliza otro puerto o credenciales, copiar `config/local.ejemplo.php` como `config/local.php` y modificar allí los valores. Este archivo privado está excluido de Git.
8. Abrir `http://localhost/agenda-contactos/`. Si Apache usa otro puerto, agregarlo a la URL; el puerto MySQL no se escribe en el navegador.
9. Seleccionar Registrarte, crear una cuenta e iniciar sesión. No existen cuentas ni contraseñas predeterminadas.

## Estructura

| Ruta | Propósito |
| --- | --- |
| `index.php` | Entrada y redirección según sesión |
| `registro.php`, `login.php`, `logout.php` | Registro y autenticación |
| `panel.php` | Datos del usuario y lista de contactos |
| `crear_contacto.php`, `editar_contacto.php`, `eliminar_contacto.php` | Gestión de contactos |
| `includes/sesion.php` | Configuración común de sesiones y cabeceras |
| `includes/autenticacion.php` | Protección de páginas privadas |
| `includes/funciones.php` | Validación y escape de datos |
| `config/conexion.php` | Conexión PDO |
| `config/local.ejemplo.php` | Plantilla de configuración privada |
| `assets/css/estilos.css` | Diseño visual |
| `sql/agenda.sql` | Script de instalación |
| `docs/PRUEBAS.md` | Casos de aceptación y seguridad |
| `docs/ENTREGA.md` | Pasos de entrega y publicación |

## Modelo de datos

Un usuario puede tener varios contactos. Cada contacto pertenece a un único usuario mediante `contactos.id_usuario`, clave foránea hacia `usuarios.id`. El correo de usuario tiene restricción `UNIQUE`. La contraseña se guarda como hash en un campo de 255 caracteres. El teléfono se guarda como texto para conservar ceros iniciales y prefijos.

## Verificación

Consultar `docs/PRUEBAS.md` y ejecutar los casos con dos cuentas de prueba. No se deben utilizar datos personales reales al publicar capturas o archivos SQL. El script incluido contiene solo la estructura.

## Publicación del repositorio

Consultar `docs/ENTREGA.md`. El repositorio debe ser público. GitHub almacena el código; GitHub Pages no ejecuta PHP ni MySQL. La demostración se realiza en XAMPP.

## Alcance

Aplicación académica local. Incluye las medidas mínimas solicitadas y protecciones CSRF, cookies HttpOnly/SameSite y consultas preparadas. Un despliegue público de producción requiere configuración HTTPS, usuario de base de datos con permisos limitados y medidas operativas adicionales como límites de intentos de acceso.
