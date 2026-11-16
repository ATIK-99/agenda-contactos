<?php
require_once __DIR__ . '/includes/autenticacion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$contactos = [];
$error = '';

$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['mensaje']);

try {
    // Consultamos exclusivamente los contactos del usuario autenticado.
    $consulta = $conexion->prepare(
        'SELECT id, nombre, telefono, correo, ciudad
         FROM contactos
         WHERE id_usuario = :id_usuario
         ORDER BY nombre, id'
    );

    $consulta->execute([
        'id_usuario' => $_SESSION['id_usuario']
    ]);

    $contactos = $consulta->fetchAll();
} catch (PDOException $e) {
    $error = 'No se pudieron cargar los contactos. Inténtalo más tarde.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Agenda personal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="cabecera"><a href="index.php">Mi agenda</a><span>Desarrollo Web · PHP y MySQL</span></header>
<main>
    <h1>Mi agenda personal</h1>

    <p>
        <strong>Nombre:</strong>
        <?= escapar($_SESSION['nombre']) ?>
    </p>

    <p>
        <strong>Correo:</strong>
        <?= escapar($_SESSION['correo']) ?>
    </p>

    <form action="logout.php" method="POST">
        <input
            type="hidden"
            name="csrf"
            value="<?= escapar($_SESSION['csrf']) ?>"
        >
        <button class="secundario" type="submit">Cerrar sesión</button>
    </form>

    <h2>Mis contactos</h2>

    <p>
        <a class="boton" href="crear_contacto.php">Registrar un contacto</a>
    </p>

    <?php if ($mensaje !== ''): ?>
        <p role="status"><?= escapar($mensaje) ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p role="alert"><?= escapar($error) ?></p>

    <?php elseif (empty($contactos)): ?>
        <p>Todavía no tienes contactos registrados.</p>

    <?php else: ?>
        <div class="tabla-contenedor"><table>
            <caption>Contactos de mi agenda</caption>
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Correo electrónico</th>
                    <th scope="col">Ciudad</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>

            <tbody>
    <?php foreach ($contactos as $contacto): ?>
        <tr>
            <td><?= escapar($contacto['nombre']) ?></td>
            <td><?= escapar($contacto['telefono']) ?></td>
            <td><?= escapar($contacto['correo']) ?></td>
            <td><?= escapar($contacto['ciudad']) ?></td>
            <td>
                <a href="editar_contacto.php?id=<?= escapar($contacto['id']) ?>">
                    Editar
                </a>

                <a href="eliminar_contacto.php?id=<?= escapar($contacto['id']) ?>">
                    Eliminar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table></div>
    <?php endif; ?>
</main>
<footer>Agenda personal de contactos · Proyecto académico</footer>
</body>
</html>