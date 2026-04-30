<?php
require_once '../includes/auth.php';
require_once '../conexio.php';
redirigirSiNoAdmin();

$mensaje = '';
$error = '';

// Procesar formulario de crear producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $talla = $_POST['talla'] ?? '';
    $color = $_POST['color'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    
    if (empty($nombre) || $precio <= 0) {
        $error = 'Nombre y precio son obligatorios';
    } else {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, talla, color, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssss", $nombre, $descripcion, $precio, $stock, $talla, $color, $categoria);
        
        if ($stmt->execute()) {
            $mensaje = 'Producto creado correctamente';
        } else {
            $error = 'Error al crear el producto';
        }
    }
}

// Procesar eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM productos WHERE id = $id");
    $mensaje = 'Producto eliminado';
}

// Obtener todos los productos
$productos = $conn->query("SELECT * FROM productos ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Productos - Panel Admin</title>
    <link rel="stylesheet" href="/tienda-ropa/css/style.css">
    <style>
        .form-producto {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .form-producto input, .form-producto textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .form-row {
            display: flex;
            gap: 1rem;
        }
        .form-row > div {
            flex: 1;
        }
        .producto-card {
            border: 1px solid #ddd;
            padding: 1rem;
            margin: 0.5rem;
            border-radius: 8px;
            display: inline-block;
            width: 200px;
        }
        .admin-container {
            max-width: 1200px;
        }
        .boton-eliminar {
            color: red;
            text-decoration: none;
            margin-left: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Gestión de Productos</h1>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
        
        <?php if ($mensaje): ?>
            <div class="success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="form-producto">
            <h2>Crear Nuevo Producto</h2>
            <form method="POST" action="">
                <div class="form-row">
                    <div>
                        <input type="text" name="nombre" placeholder="Nombre del producto" required>
                        <input type="number" step="0.01" name="precio" placeholder="Precio (€)" required>
                        <input type="number" name="stock" placeholder="Stock" value="0">
                    </div>
                    <div>
                        <input type="text" name="talla" placeholder="Talla (opcional)">
                        <input type="text" name="color" placeholder="Color (opcional)">
                        <input type="text" name="categoria" placeholder="Categoría (opcional)">
                    </div>
                </div>
                <textarea name="descripcion" rows="3" placeholder="Descripción del producto"></textarea>
                <button type="submit" name="crear">Crear Producto</button>
            </form>
        </div>
        
        <h2>Productos Existentes</h2>
        <div style="display: flex; flex-wrap: wrap;">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p><strong>Precio:</strong> <?php echo $producto['precio']; ?> €</p>
                    <p><strong>Stock:</strong> <?php echo $producto['stock']; ?></p>
                    <p><strong>Talla:</strong> <?php echo $producto['talla'] ?: '-'; ?></p>
                    <p><strong>Color:</strong> <?php echo $producto['color'] ?: '-'; ?></p>
                    <a href="?eliminar=<?php echo $producto['id']; ?>" class="boton-eliminar" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <br>
        <a href="/tienda-ropa/logout.php" class="logout">Cerrar Sesión</a>
        <a href="/tienda-ropa/admin/panel.php" class="back">Volver al panel</a>
    </div>
</body>
</html>
