<?php
require_once 'includes/auth.php';
redirigirSiNoLogueado();

if (esAdmin()) {
    header('Location: /admin/panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta - Tienda Ropa</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
    
    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
    <p><strong>Rol:</strong> Usuario normal</p>
    
    <ul>
        <li><a href="#">Ver catálogo de productos</a></li>
        <li><a href="#">Mi perfil</a></li>
        <li><a href="#">Mis pedidos</a></li>
    </ul>
    
    <a href="/logout.php">Cerrar Sesión</a>
</body>
</html>
