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

// Sanitizar entradas de usuario
function limpiarEntrada($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
    return $dato;
}

// Validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generar token CSRF
function generarToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verificarToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
