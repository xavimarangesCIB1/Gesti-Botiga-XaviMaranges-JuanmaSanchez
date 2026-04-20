<?php
session_start();
require_once 'conexio.php';
require_once 'includes/auth.php';

// Si ya está logueado, redirigir
if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: /tienda-ropa/admin/panel.php');
    } else {
        header('Location: /tienda-ropa/dashboard.php');
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido';
    } elseif (strlen($password) < 4) {
        $error = 'La contraseña debe tener al menos 4 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $error = 'Este email ya está registrado';
        } else {
            // Cifrar contraseña con password_hash
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario (por defecto rol 'user')
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $nombre, $email, $password_hash);
            
            if ($stmt->execute()) {
                $success = 'Cuenta creada correctamente. Ya puedes iniciar sesión.';
            } else {
                $error = 'Error al crear la cuenta';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Tienda Ropa</title>
    <link rel="stylesheet" href="/tienda-ropa/css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Crear Cuenta</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            ¿Ya tienes cuenta? <a href="/tienda-ropa/login.php">Inicia sesión</a>
        </p>
    </div>
</body>
</html>
