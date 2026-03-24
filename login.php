<?php
session_start();
require_once 'conexio.php';
require_once 'includes/auth.php';

if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: /admin/panel.php');
    } else {
        header('Location: /dashboard.php');
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
            header('Location: /admin/panel.php');
        } else {
            header('Location: /dashboard.php');
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
</head>
<body>
    <h1>Iniciar Sesión</h1>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
