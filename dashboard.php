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
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
        
        <div class="info-usuario">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
            <p><strong>Rol:</strong> Usuario normal</p>
        </div>
        
        <ul>
            <li><a href="#">Ver catálogo de productos</a></li>
            <li><a href="#">Mi perfil</a></li>
            <li><a href="#">Mis pedidos</a></li>
        </ul>
        
        <a href="/logout.php" class="logout">Cerrar Sesión</a>
    </div>
</body>
</html>

