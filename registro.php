<?php
require_once __DIR__ . '/includes/sesion.php';

require_once __DIR__ . '/config/conexion.php';

if (!empty($_SESSION['id_usuario'])) {
    header('Location: panel.php');
    exit;
}

$errores = [];
$mensaje = $_SESSION['registro_mensaje'] ?? '';
unset($_SESSION['registro_mensaje']);
$nombre = '';
$correo = '';

// Token para comprobar que el formulario procede de esta sesión.
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Solo procesamos los datos cuando se envía el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = is_string($_POST['nombre'] ?? null)
        ? trim($_POST['nombre']) : '';

    $correo = is_string($_POST['correo'] ?? null)
        ? trim($_POST['correo']) : '';

    $password = is_string($_POST['password'] ?? null)
        ? $_POST['password'] : '';

    $confirmacion = is_string($_POST['confirmacion'] ?? null)
        ? $_POST['confirmacion'] : '';

    $token = is_string($_POST['csrf'] ?? null)
        ? $_POST['csrf'] : '';

    // Validaciones en el servidor.
    if (!hash_equals($_SESSION['csrf'], $token)) {
        $errores[] = 'La solicitud no es válida. Recarga la página.';
    }

    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'Ingresa un nombre de hasta 100 caracteres.';
    }

    if (
        !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
        strlen($correo) > 254
    ) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }

    if (mb_strlen($password) < 8 || strlen($password) > 72 || strpos($password, "\0") !== false) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres y no superar 72 bytes. '
            . 'Las tildes y otros caracteres especiales pueden ocupar más de un byte.';
    }

    if ($password !== $confirmacion) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errores)) {
        try {
            // Comprobamos si el correo ya está registrado.
            $consulta = $conexion->prepare(
                'SELECT id FROM usuarios WHERE correo = :correo'
            );

            $consulta->execute(['correo' => $correo]);

            if ($consulta->fetch()) {
                $errores[] = 'El correo electrónico ya está registrado.';
            } else {
                // Guardamos el hash, nunca la contraseña original.
                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $consulta = $conexion->prepare(
                    'INSERT INTO usuarios (nombre, correo, password)
                     VALUES (:nombre, :correo, :password)'
                );

                $consulta->execute([
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'password' => $passwordHash
                ]);

                $_SESSION['registro_mensaje'] = 'Usuario registrado correctamente. Ya puedes iniciar sesión.';
                header('Location: registro.php', true, 303);
                exit;
            }
        } catch (PDOException $e) {
            // También controlamos duplicados simultáneos.
            if (($e->errorInfo[1] ?? 0) == 1062) {
                $errores[] = 'El correo electrónico ya está registrado.';
            } else {
                $errores[] = 'No se pudo completar el registro. '
                    . 'Inténtalo más tarde.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Agenda personal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="cabecera"><a href="index.php">Mi agenda</a><span>Desarrollo Web · PHP y MySQL</span></header>
<main>
    <h1>Crear una cuenta</h1>

    <?php if (!empty($errores)): ?>
        <ul role="alert">
            <?php foreach ($errores as $error): ?>
                <li>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje !== ''): ?>
        <p role="status">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form action="registro.php" method="POST">
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
            <label for="nombre">Nombre completo:</label><br>
            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                autocomplete="name"
                value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </p>

        <p>
            <label for="correo">Correo electrónico:</label><br>
            <input
                type="email"
                id="correo"
                name="correo"
                maxlength="254"
                autocomplete="email"
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
                minlength="8"
                autocomplete="new-password"
                required
            >
        </p>

        <p>
            <label for="confirmacion">Confirmar contraseña:</label><br>
            <input
                type="password"
                id="confirmacion"
                name="confirmacion"
                minlength="8"
                autocomplete="new-password"
                required
            >
        </p>

        <button type="submit">Registrarme</button>
    </form>
    <p>
    ¿Ya tienes una cuenta?
    <a href="login.php">Iniciar sesión</a>
</p>
</main>
<footer>Agenda personal de contactos · Proyecto académico</footer>
</body>
</html>