<?php
require_once __DIR__ . '/includes/autenticacion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$error = '';

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('Método no permitido.');
}

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
    // Verificamos que el contacto pertenece al usuario.
    $consulta = $conexion->prepare(
        'SELECT nombre, telefono, correo, ciudad
         FROM contactos
         WHERE id = :id AND id_usuario = :id_usuario'
    );

    $consulta->execute([
        'id' => $id,
        'id_usuario' => $_SESSION['id_usuario']
    ]);

    $contacto = $consulta->fetch();

    if (!$contacto) {
        http_response_code(404);
        exit('Contacto no disponible.');
    }
} catch (PDOException $e) {
    http_response_code(500);
    exit('No se pudo cargar el contacto. Inténtalo más tarde.');
}

// Abrir la página no elimina el contacto.
// Solo se elimina al enviar el formulario de confirmación.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], campoPost('csrf'))) {
        http_response_code(403);
        $error = 'La solicitud no es válida. Recarga la página.';
    } else {
        try {
            $consulta = $conexion->prepare(
                'DELETE FROM contactos
                 WHERE id = :id AND id_usuario = :id_usuario'
            );

            $consulta->execute([
                'id' => $id,
                'id_usuario' => $_SESSION['id_usuario']
            ]);

            if ($consulta->rowCount() === 1) {
                $_SESSION['mensaje'] = 'Contacto eliminado correctamente.';
            } else {
                $_SESSION['mensaje'] = 'El contacto ya no está disponible.';
            }

            header('Location: panel.php', true, 303);
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo eliminar el contacto. Inténtalo más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar contacto | Agenda personal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="cabecera"><a href="index.php">Mi agenda</a><span>Desarrollo Web · PHP y MySQL</span></header>
<main>
    <h1>Eliminar contacto</h1>

    <?php if ($error !== ''): ?>
        <p role="alert"><?= escapar($error) ?></p>
    <?php endif; ?>

    <p>¿Confirmas que deseas eliminar este contacto?</p>

    <ul>
        <li>
            <strong>Nombre:</strong>
            <?= escapar($contacto['nombre']) ?>
        </li>
        <li>
            <strong>Teléfono:</strong>
            <?= escapar($contacto['telefono']) ?>
        </li>
        <li>
            <strong>Correo:</strong>
            <?= escapar($contacto['correo']) ?>
        </li>
        <li>
            <strong>Ciudad:</strong>
            <?= escapar($contacto['ciudad']) ?>
        </li>
    </ul>

    <p>Esta acción no se puede deshacer.</p>

    <form
        action="eliminar_contacto.php?id=<?= escapar($id) ?>"
        method="POST"
    >
        <input
            type="hidden"
            name="csrf"
            value="<?= escapar($_SESSION['csrf']) ?>"
        >

        <button class="peligro" type="submit">Sí, eliminar contacto</button>
        <a href="panel.php">Cancelar</a>
    </form>
</main>
<footer>Agenda personal de contactos · Proyecto académico</footer>
</body>
</html>