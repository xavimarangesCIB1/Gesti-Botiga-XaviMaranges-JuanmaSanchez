<?php
require_once '../includes/auth.php';
require_once '../conexio.php';
redirigirSiNoAdmin();

$mensaje = '';
$error = '';
$producto = null;

// Obtener producto a editar
$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $talla = $_POST['talla'] ?? '';
    $color = $_POST['color'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    if (empty($nombre) || $precio <= 0) {
        $error = 'Nombre y precio son obligatorios';
    } else {
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, talla = ?, color = ?, categoria = ?, activo = ? WHERE id = ?");
        $stmt->bind_param("ssdiss", $nombre, $descripcion, $precio, $stock, $talla, $color, $categoria, $activo, $id);
        
        if ($stmt->execute()) {
            $mensaje = 'Producto actualizado correctamente';
            // Recargar datos
            $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $producto = $stmt->get_result()->fetch_assoc();
        } else {
            $error = 'Error al actualizar el producto';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Panel Admin</title>
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
        .checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checkbox input {
            width: auto;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Editar Producto</h1>
        
        <?php if ($mensaje): ?>
            <div class="success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="form-producto">
            <form method="POST" action="">
                <div class="form-row">
                    <div>
                        <input type="text" name="nombre" placeholder="Nombre del producto" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                        <input type="number" step="0.01" name="precio" placeholder="Precio (€)" value="<?php echo $producto['precio']; ?>" required>
                        <input type="number" name="stock" placeholder="Stock" value="<?php echo $producto['stock']; ?>">
                    </div>
                    <div>
                        <input type="text" name="talla" placeholder="Talla" value="<?php echo htmlspecialchars($producto['talla']); ?>">
                        <input type="text" name="color" placeholder="Color" value="<?php echo htmlspecialchars($producto['color']); ?>">
                        <input type="text" name="categoria" placeholder="Categoría" value="<?php echo htmlspecialchars($producto['categoria']); ?>">
                    </div>
                </div>
                <textarea name="descripcion" rows="3" placeholder="Descripción"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                <div class="checkbox">
                    <input type="checkbox" name="activo" <?php echo $producto['activo'] ? 'checked' : ''; ?>>
                    <label>Producto activo (visible en la tienda)</label>
                </div>
                <button type="submit" name="actualizar">Actualizar Producto</button>
                <a href="/tienda-ropa/admin/productos.php" class="back">Cancelar</a>
            </form>
        </div>
        
        <br>
        <a href="/tienda-ropa/admin/productos.php" class="back">⬅ Volver a productos</a>
        <a href="/tienda-ropa/logout.php" class="logout">Cerrar Sesión</a>
    </div>
</body>
</html>
