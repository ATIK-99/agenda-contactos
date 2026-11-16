<?php
require_once __DIR__ . '/includes/autenticacion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$errores = [];

$datos = [
    'nombre' => '',
    'telefono' => '',
    'correo' => '',
    'ciudad' => ''
];

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = campoPost($campo);
    }

    $errores = validarContacto($datos);

    if (!hash_equals($_SESSION['csrf'], campoPost('csrf'))) {
        $errores[] = 'La solicitud no es válida. Recarga la página.';
    }

    if (empty($errores)) {
        try {
            $consulta = $conexion->prepare(
                'INSERT INTO contactos
                    (id_usuario, nombre, telefono, correo, ciudad)
                 VALUES
                    (:id_usuario, :nombre, :telefono, :correo, :ciudad)'
            );

            $consulta->execute([
                'id_usuario' => $_SESSION['id_usuario'],
                'nombre' => $datos['nombre'],
                'telefono' => $datos['telefono'],
                'correo' => $datos['correo'],
                'ciudad' => $datos['ciudad']
            ]);

            $_SESSION['mensaje'] = 'Contacto registrado correctamente.';

            header('Location: panel.php', true, 303);
            exit;
        } catch (PDOException $e) {
            $errores[] = 'No se pudo guardar el contacto. '
                . 'Inténtalo más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo contacto | Agenda personal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="cabecera"><a href="index.php">Mi agenda</a><span>Desarrollo Web · PHP y MySQL</span></header>
<main>
    <h1>Registrar un contacto</h1>

    <?php if (!empty($errores)): ?>
        <ul role="alert">
            <?php foreach ($errores as $error): ?>
                <li><?= escapar($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="crear_contacto.php" method="POST">
        <input
            type="hidden"
            name="csrf"
            value="<?= escapar($_SESSION['csrf']) ?>"
        >

        <p>
            <label for="nombre">Nombre:</label><br>
            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                value="<?= escapar($datos['nombre']) ?>"
                required
            >
        </p>

        <p>
            <label for="telefono">Teléfono:</label><br>
            <input
                type="tel"
                id="telefono"
                name="telefono"
                maxlength="25"
                value="<?= escapar($datos['telefono']) ?>"
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
                value="<?= escapar($datos['correo']) ?>"
                required
            >
        </p>

        <p>
            <label for="ciudad">Ciudad:</label><br>
            <input
                type="text"
                id="ciudad"
                name="ciudad"
                maxlength="100"
                value="<?= escapar($datos['ciudad']) ?>"
                required
            >
        </p>

        <button type="submit">Guardar contacto</button>
        <a href="panel.php">Cancelar</a>
    </form>
</main>
<footer>Agenda personal de contactos · Proyecto académico</footer>
</body>
</html>