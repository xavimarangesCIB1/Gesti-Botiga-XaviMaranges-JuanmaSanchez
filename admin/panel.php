<?php
require_once '../includes/auth.php';
require_once '../conexio.php';
redirigirSiNoAdmin();

$resultado = $conn->query("SELECT id, nombre, email, rol, created_at FROM usuarios ORDER BY id DESC");
$usuarios = $resultado->fetch_all(MYSQLI_ASSOC);

$totalUsuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$totalAdmins = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'user'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Tienda Ropa</title>
</head>
<body>
    <h1>Panel de Administración</h1>
    <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
    
    <h3>Estadísticas</h3>
    <p>Total Usuarios: <?php echo $totalUsuarios; ?></p>
    <p>Administradores: <?php echo $totalAdmins; ?></p>
    <p>Usuarios Normales: <?php echo $totalUsers; ?></p>
    
    <h2>Gestión de Usuarios</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo $usuario['rol']; ?></td>
                <td><?php echo $usuario['created_at']; ?></td>
                <td>
                    <?php if ($usuario['rol'] !== 'admin'): ?>
                        <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="/logout.php">Cerrar Sesión</a>
    <a href="/dashboard.php">Volver al inicio</a>
</body>
</html>
