<?php
function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function campoPost($campo) {
    return is_string($_POST[$campo] ?? null) ? trim($_POST[$campo]) : '';
}
function validarContacto($datos) {
    $errores = [];
    if ($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 100) {
        $errores[] = 'Ingresa un nombre de hasta 100 caracteres.';
    }
    if (!preg_match('/\A[0-9+() .-]{7,25}\z/', $datos['telefono']) || preg_match_all('/[0-9]/', $datos['telefono']) < 7) {
        $errores[] = 'Ingresa un teléfono de 7 a 25 caracteres, con al menos 7 dígitos.';
    }
    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) || strlen($datos['correo']) > 254) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }
    if ($datos['ciudad'] === '' || mb_strlen($datos['ciudad']) > 100) {
        $errores[] = 'Ingresa una ciudad de hasta 100 caracteres.';
    }
    return $errores;
}
