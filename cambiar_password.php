<?php
require_once 'includes/auth.php';
require_once 'conexio.php';
redirigirSiNoLogueado();

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    
    if (!password_verify($password_actual, $usuario['password'])) {
        $error = 'La contraseña actual es incorrecta';
    } elseif (strlen($password_nueva) < 4) {
        $error = 'La nueva contraseña debe tener al menos 4 caracteres';
    } elseif ($password_nueva !== $confirmar_password) {
        $error = 'Las contraseñas nuevas no coinciden';
    } else {
        $nueva_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $nueva_hash, $_SESSION['usuario_id']);
        
        if ($stmt->execute()) {
            $mensaje = 'Contraseña actualizada correctamente';
        } else {
            $error = 'Error al actualizar la contraseña';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña - Tienda Ropa</title>
    <link rel="stylesheet" href="/tienda-ropa/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Cambiar Contraseña</h1>
        <p>Usuario: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
        
        <?php if ($mensaje): ?>
            <div class="success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div>
                <label>Contraseña actual:</label>
                <input type="password" name="password_actual" required>
            </div>
            <div>
                <label>Nueva contraseña:</label>
                <input type="password" name="password_nueva" required>
            </div>
            <div>
                <label>Confirmar nueva contraseña:</label>
                <input type="password" name="confirmar_password" required>
            </div>
            <button type="submit">Cambiar contraseña</button>
        </form>
        
        <br>
        <a href="/tienda-ropa/dashboard.php" class="back">⬅ Volver al dashboard</a>
        <a href="/tienda-ropa/logout.php" class="logout">Cerrar Sesión</a>
    </div>
</body>
</html>

