<?php
session_start();
require_once 'conexio.php';
require_once 'includes/auth.php';

if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: /tienda-ropa/admin/panel.php');
    } else {
        header('Location: /tienda-ropa/dashboard.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = md5($_POST['password'] ?? '');
    
    $stmt = $conn->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        
        if ($usuario['rol'] === 'admin') {
            header('Location: /tienda-ropa/admin/panel.php');
        } else {
            header('Location: /tienda-ropa/dashboard.php');
        }
        exit();
    } else {
        $error = 'Email o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Tienda Ropa</title>
    <link rel="stylesheet" href="/tienda-ropa/css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
