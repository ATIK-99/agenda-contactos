<?php
require_once __DIR__ . '/sesion.php';
if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
