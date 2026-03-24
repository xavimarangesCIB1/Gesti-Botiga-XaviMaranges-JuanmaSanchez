<?php
session_start();

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

function redirigirSiNoLogueado() {
    if (!estaLogueado()) {
        header('Location: /tienda-ropa/login.php');
        exit();
    }
}

function redirigirSiNoAdmin() {
    redirigirSiNoLogueado();
    if (!esAdmin()) {
        die('Acceso denegado. No tienes permisos de administrador.');
    }
}
?>
