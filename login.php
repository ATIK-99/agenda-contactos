<?php
require_once __DIR__ . '/includes/sesion.php';

require_once __DIR__ . '/config/conexion.php';

// Si ya inició sesión, lo enviamos al panel.
if (!empty($_SESSION['id_usuario'])) {
    header('Location: panel.php');
    exit;
}

$errores = [];
$correo = '';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = is_string($_POST['correo'] ?? null)
        ? trim($_POST['correo']) : '';

    $password = is_string($_POST['password'] ?? null)
        ? $_POST['password'] : '';

    $token = is_string($_POST['csrf'] ?? null)
        ? $_POST['csrf'] : '';

    if (!hash_equals($_SESSION['csrf'], $token)) {
        $errores[] = 'La solicitud no es válida. Recarga la página.';
    }

    if (
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($correo) > 254
    ) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }

    if ($password === '' || strlen($password) > 72 || strpos($password, "\0") !== false) {
        $errores[] = 'Ingresa una contraseña válida.';
    }

    if (empty($errores)) {
        try {
            $consulta = $conexion->prepare(
                'SELECT id, nombre, correo, password
                 FROM usuarios
                 WHERE correo = :correo'
            );

            $consulta->execute(['correo' => $correo]);

            $usuario = $consulta->fetch();

            if (
                $usuario &&
                password_verify($password, $usuario['password'])
            ) {
                // Cambiamos el identificador al autenticar al usuario.
                session_regenerate_id(true);

                $_SESSION['id_usuario'] = (int) $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];

                // Renovamos el token tras iniciar sesión.
                $_SESSION['csrf'] = bin2hex(random_bytes(32));

                header('Location: panel.php');
                exit;
            } else {
                $errores[] = 'Correo o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $errores[] = 'No se pudo iniciar sesión. Inténtalo más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Agenda personal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="cabecera"><a href="index.php">Mi agenda</a><span>Desarrollo Web · PHP y MySQL</span></header>
<main>
    <h1>Iniciar sesión</h1>

    <?php if (!empty($errores)): ?>
        <ul role="alert">
            <?php foreach ($errores as $error): ?>
                <li>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <input
            type="hidden"
            name="csrf"
            value="<?= htmlspecialchars(
                $_SESSION['csrf'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <p>
            <label for="correo">Correo electrónico:</label><br>
            <input
                type="email"
                id="correo"
                name="correo"
                maxlength="254"
                autocomplete="username"
                value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </p>

        <p>
            <label for="password">Contraseña:</label><br>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >
        </p>

        <button type="submit">Ingresar</button>
    </form>

    <p>¿No tienes una cuenta? <a href="registro.php">Registrarte</a></p>
</main>
<footer>Agenda personal de contactos · Proyecto académico</footer>
</body>
</html>