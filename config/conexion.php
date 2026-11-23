<?php
// Valores de desarrollo local. Las credenciales privadas van en local.php.
$config = [
    'servidor' => '127.0.0.1',
    'puerto' => '3307',
    'base_datos' => 'agenda_personal',
    'usuario' => 'root',
    'contrasena' => ''
];
if (is_file(__DIR__ . '/local.php')) {
    $config = array_replace($config, require __DIR__ . '/local.php');
}
try {
    $conexion = new PDO(
        "mysql:host={$config['servidor']};port={$config['puerto']};dbname={$config['base_datos']};charset=utf8mb4",
        $config['usuario'], $config['contrasena'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log('Agenda: fallo de conexión con la base de datos.');
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa los datos de config/local.php.');
}
