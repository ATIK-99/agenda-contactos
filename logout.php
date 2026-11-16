<?php
require_once __DIR__ . '/includes/autenticacion.php';

// El cierre de sesión únicamente se permite mediante POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

// Comprobamos el token del formulario.
$token = is_string($_POST['csrf'] ?? null)
    ? $_POST['csrf'] : '';

if (
    empty($_SESSION['csrf']) ||
    !hash_equals($_SESSION['csrf'], $token)
) {
    http_response_code(403);
    exit('Solicitud no válida. Regresa al panel e inténtalo nuevamente.');
}

// Eliminamos los datos de la sesión.
$_SESSION = [];

// Eliminamos también la cookie que identifica la sesión.
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// Destruimos la sesión y regresamos al inicio de sesión.
session_destroy();

header('Location: login.php');
exit;