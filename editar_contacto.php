<?php
require_once __DIR__ . '/includes/autenticacion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$errores = [];

// Validamos el identificador recibido en la URL.
$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$id) {
    http_response_code(400);
    exit('Identificador de contacto no válido.');
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

try {
    // Solo recuperamos el contacto si pertenece al usuario.
    $consulta = $conexion->prepare(
        'SELECT nombre, telefono, correo, ciudad
         FROM contactos
         WHERE id = :id AND id_usuario = :id_usuario'
    );

    $consulta->execute([
        'id' => $id,
        'id_usuario' => $_SESSION['id_usuario']
    ]);

    $datos = $consulta->fetch();

    if (!$datos) {
        http_response_code(404);
        exit('Contacto no disponible.');
    }
} catch (PDOException $e) {
    http_response_code(500);
    exit('No se pudo cargar el contacto. Inténtalo más tarde.');
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
            // También comprobamos la propiedad al actualizar.
            $consulta = $conexion->prepare(
                'UPDATE contactos
                 SET nombre = :nombre,
                     telefono = :telefono,
                     correo = :correo,
                     ciudad = :ciudad
                 WHERE id = :id AND id_usuario = :id_usuario'
            );

            $consulta->execute([
                'nombre' => $datos['nombre'],
                'telefono' => $datos['telefono'],
                'correo' => $datos['correo'],
                'ciudad' => $datos['ciudad'],
                'id' => $id,
                'id_usuario' => $_SESSION['id_usuario']
            ]);

            $_SESSION['mensaje'] = 'Cambios guardados correctamente.';

            header('Location: panel.php', true, 303);
            exit;
        } catch (PDOException $e) {
            $errores[] = 'No se pudieron guardar los cambios. '
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
    <title>Editar contacto | Agenda personal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="cabecera"><a href="index.php">Mi agenda</a><span>Desarrollo Web · PHP y MySQL</span></header>
<main>
    <h1>Editar contacto</h1>

    <?php if (!empty($errores)): ?>
        <ul role="alert">
            <?php foreach ($errores as $error): ?>
                <li><?= escapar($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="editar_contacto.php?id=<?= escapar($id) ?>" method="POST">
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

        <button type="submit">Guardar cambios</button>
        <a href="panel.php">Cancelar</a>
    </form>
</main>
<footer>Agenda personal de contactos · Proyecto académico</footer>
</body>
</html>