<?php
require_once __DIR__ . '/includes/autenticacion.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$contactos = [];
$error = '';
$totalContactos = 0;

// Admitimos únicamente textos y opciones conocidas de los controles del panel.
$busqueda = is_string($_GET['buscar'] ?? null)
    ? mb_substr(trim($_GET['buscar']), 0, 254) : '';
$camposBusqueda = [
    'todos' => 'Todos los campos',
    'nombre' => 'Nombre',
    'correo' => 'Correo',
    'ciudad' => 'Ciudad'
];
$campo = is_string($_GET['campo'] ?? null)
    && isset($camposBusqueda[$_GET['campo']]) ? $_GET['campo'] : 'todos';
$orden = is_string($_GET['orden'] ?? null)
    && in_array($_GET['orden'], ['nombre', 'ciudad'], true)
    ? $_GET['orden'] : 'nombre';
$direccion = is_string($_GET['direccion'] ?? null)
    && $_GET['direccion'] === 'DESC' ? 'DESC' : 'ASC';

$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['mensaje']);

try {
    $parametros = ['id_usuario' => $_SESSION['id_usuario']];
    $conteo = $conexion->prepare('SELECT COUNT(*) FROM contactos WHERE id_usuario = :id_usuario');
    $conteo->execute($parametros);
    $totalContactos = (int) $conteo->fetchColumn();

    // El filtro de propietario se mantiene también al buscar.
    $sql = 'SELECT id, nombre, telefono, correo, ciudad
            FROM contactos WHERE id_usuario = :id_usuario';

    if ($busqueda !== '') {
        // Buscar % o _ debe encontrar esos caracteres, no actuar como comodín.
        $patron = '%' . strtr($busqueda, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $columnas = $campo === 'todos' ? ['nombre', 'correo', 'ciudad'] : [$campo];
        $condiciones = [];
        foreach ($columnas as $columna) {
            $condiciones[] = "$columna LIKE :buscar_$columna ESCAPE '!'";
            $parametros['buscar_' . $columna] = $patron;
        }
        $sql .= ' AND (' . implode(' OR ', $condiciones) . ')';
    }

    // Los identificadores SQL no se parametrizan: proceden de la lista permitida.
    // El nombre y el id resuelven empates para mantener un orden estable.
    $sql .= $orden === 'ciudad'
        ? " ORDER BY ciudad $direccion, nombre ASC, id ASC"
        : " ORDER BY nombre $direccion, id ASC";
    $consulta = $conexion->prepare($sql);
    $consulta->execute($parametros);

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

    <form class="filtros" action="panel.php" method="GET" role="search" aria-label="Buscar y ordenar contactos">
        <div class="filtros-campos">
            <div>
                <label for="buscar">Buscar contacto</label>
                <input type="search" id="buscar" name="buscar" maxlength="254"
                    placeholder="Nombre, correo o ciudad"
                    value="<?= escapar($busqueda) ?>" aria-describedby="ayuda-busqueda">
            </div>
            <div>
                <label for="campo">Buscar en</label>
                <select id="campo" name="campo">
                    <?php foreach ($camposBusqueda as $valor => $etiqueta): ?>
                        <option value="<?= escapar($valor) ?>"<?= $campo === $valor ? ' selected' : '' ?>><?= escapar($etiqueta) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="orden">Ordenar por</label>
                <select id="orden" name="orden">
                    <option value="nombre"<?= $orden === 'nombre' ? ' selected' : '' ?>>Nombre</option>
                    <option value="ciudad"<?= $orden === 'ciudad' ? ' selected' : '' ?>>Ciudad</option>
                </select>
            </div>
            <div>
                <label for="direccion">Dirección</label>
                <select id="direccion" name="direccion">
                    <option value="ASC"<?= $direccion === 'ASC' ? ' selected' : '' ?>>A → Z</option>
                    <option value="DESC"<?= $direccion === 'DESC' ? ' selected' : '' ?>>Z → A</option>
                </select>
            </div>
        </div>
        <p id="ayuda-busqueda" class="ayuda">Escribe una parte del nombre, correo o ciudad. Puedes combinar la búsqueda y el ordenamiento.</p>
        <div class="filtros-acciones">
            <button type="submit">Aplicar filtros</button>
            <a class="boton secundario" href="panel.php">Limpiar filtros</a>
        </div>
    </form>

    <?php if ($error !== ''): ?>
        <p role="alert"><?= escapar($error) ?></p>

    <?php elseif ($totalContactos === 0): ?>
        <p>Todavía no tienes contactos registrados.</p>

    <?php elseif (empty($contactos)): ?>
        <p role="status">No se encontraron contactos para «<?= escapar($busqueda) ?>». Prueba otro texto o limpia los filtros.</p>

    <?php else: ?>
        <p class="resumen-resultados">
            Mostrando <?= count($contactos) ?> de <?= $totalContactos ?> contacto<?= $totalContactos === 1 ? '' : 's' ?>.
            Orden: <?= $orden === 'ciudad' ? 'ciudad' : 'nombre' ?> (<?= $direccion === 'ASC' ? 'A → Z' : 'Z → A' ?>).
        </p>
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
