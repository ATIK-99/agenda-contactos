<?php
require_once __DIR__ . '/includes/sesion.php';

if (!empty($_SESSION['id_usuario'])) {
    header('Location: panel.php');
} else {
    header('Location: login.php');
}

exit;