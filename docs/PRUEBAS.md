# Pruebas de aceptación

Ejecutar en XAMPP con dos usuarios ficticios A y B. Registrar el resultado real antes de entregar. Esta lista no implica que se hayan ejecutado en el equipo del estudiante.

| Caso | Acción | Resultado esperado |
| --- | --- | --- |
| 1 | Abrir panel, crear, editar y eliminar sin sesión | Redirección a login; no expone contactos |
| 2 | Registrar A con datos válidos | Se crea una cuenta y el password almacenado es un hash |
| 3 | Repetir correo de A | Se rechaza sin duplicar registros |
| 4 | Probar correo inválido, campos vacíos o contraseñas distintas | Rechazo y mensaje claro |
| 5 | Ingresar contraseña incorrecta | Error de credenciales; no se crea sesión autenticada |
| 6 | Ingresar como A | Panel con su nombre y correo |
| 7 | Crear contacto de A | Aparece una vez en su panel, también tras recargar |
| 8 | Editar ciudad o teléfono | Cambios visibles en panel |
| 9 | Abrir eliminar y cancelar | El contacto permanece |
| 10 | Registrar B e iniciar como B | No aparecen contactos de A |
| 11 | Abrir URL de edición del contacto A desde B | 404 Contacto no disponible |
| 12 | Abrir URL de eliminación del contacto A desde B | 404 Contacto no disponible |
| 13 | Enviar POST con ID ajeno desde B | No modifica ni elimina el contacto A |
| 14 | Alterar token oculto csrf en el inspector y enviar | Rechazo sin cambiar datos |
| 15 | Enviar nombre con texto `<script>alert(1)</script>` | Se muestra como texto; no ejecuta JavaScript |
| 16 | Confirmar eliminación como propietario A | Desaparece y muestra confirmación |
| 17 | Cerrar sesión y solicitar nuevamente panel | Redirige a login |
| 18 | URL con id inexistente, negativo o texto | Mensaje controlado, sin datos internos |
| 19 | Pantalla estrecha / teclado Tab | Campos legibles, foco visible, tabla desplazable |
| 20 | Abrir raíz del proyecto | Dirige a login o panel según sesión |
| 21 | Buscar parte del nombre, correo y ciudad, seleccionando cada campo | Encuentra solo coincidencias del campo y del usuario actual |
| 22 | Buscar en Todos los campos | Consulta nombre, correo y ciudad, conservando el aislamiento por usuario |
| 23 | Buscar texto sin coincidencias | Muestra búsqueda sin resultados, no agenda vacía |
| 24 | Ordenar por nombre y ciudad en ambas direcciones | Orden A → Z / Z → A correcto; empates resueltos por nombre e id |
| 25 | Combinar búsqueda con ordenamiento y recargar | Conserva filtros y resultados |
| 26 | Pulsar Limpiar filtros | Recupera todos los contactos y el orden inicial |
| 27 | Desde B buscar valores exclusivos de A | No muestra contactos ajenos |
| 28 | Buscar los caracteres %, _ y ! | Los interpreta literalmente, no como comodines |
| 29 | Alterar campo, orden y dirección con valores no admitidos o arrays | Usa valores seguros por defecto y no ejecuta SQL recibido del usuario |
| 30 | Buscar una comilla, texto SQL o etiquetas HTML | Trata la entrada como texto, sin ejecutar SQL ni HTML |
| 31 | Importar SQL en una base con otro nombre | Crea las tablas dentro de la base seleccionada |
| 32 | Reimportar SQL con registros existentes | Conserva tablas y registros |
| 33 | Usar los filtros en pantalla estrecha y con teclado | Controles legibles, foco visible y sin desbordamiento de la página |
| 34 | Abrir URL HTTPS pública desde una ventana privada | Aplicación funcional y certificado válido |

Para el caso 13 se puede cambiar el atributo action de un formulario propio mediante las herramientas del navegador, usando el ID del contacto ajeno y un token válido de B. Comprobar después con A que no hubo cambios. Para validar en servidor, retirar temporalmente required o type=email con el inspector y enviar datos inválidos; no basta con el rechazo del navegador.
