<?php
require_once 'includes/auth.php';
require_once 'conexio.php';
redirigirSiNoLogueado();

if (esAdmin()) {
    header('Location: /tienda-ropa/admin/panel.php');
    exit();
}

$mensaje = '';
$error = '';

// Inicializar carrito en sesión
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Añadir producto al carrito
if (isset($_GET['añadir'])) {
    $producto_id = $_GET['añadir'];
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        $_SESSION['carrito'][$producto_id] = 1;
    }
    header('Location: /tienda-ropa/carrito.php');
    exit();
}

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $producto_id = $_GET['eliminar'];
    unset($_SESSION['carrito'][$producto_id]);
    header('Location: /tienda-ropa/carrito.php');
    exit();
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    $_SESSION['carrito'] = [];
    header('Location: /tienda-ropa/carrito.php');
    exit();
}

// Procesar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['realizar_pedido'])) {
    $direccion = trim($_POST['direccion'] ?? '');
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    
    if (empty($direccion)) {
        $error = 'La dirección de envío es obligatoria';
    } elseif (empty($_SESSION['carrito'])) {
        $error = 'El carrito está vacío';
    } else {
        $total = 0;
        $items_carrito = [];
        foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
            $stmt = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
            $stmt->bind_param("i", $producto_id);
            $stmt->execute();
            $producto = $stmt->get_result()->fetch_assoc();
            $items_carrito[] = [
                'id' => $producto_id,
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad
            ];
            $total += $producto['precio'] * $cantidad;
        }
        
        $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, direccion_envio, metodo_pago, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->bind_param("idss", $_SESSION['usuario_id'], $total, $direccion, $metodo_pago);
        $stmt->execute();
        $pedido_id = $conn->insert_id;
        
        foreach ($items_carrito as $item) {
            $stmt = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $pedido_id, $item['id'], $item['cantidad'], $item['precio']);
            $stmt->execute();
        }
        
        $_SESSION['carrito'] = [];
        $mensaje = "Pedido realizado correctamente. Número de pedido: $pedido_id";
    }
}

// Obtener productos del carrito
$carrito_productos = [];
$carrito_total = 0;
foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
    $stmt = $conn->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    if ($producto) {
        $producto['cantidad'] = $cantidad;
        $producto['subtotal'] = $producto['precio'] * $cantidad;
        $carrito_productos[] = $producto;
        $carrito_total += $producto['subtotal'];
    }
}

// Obtener productos para mostrar en tienda
$productos = $conn->query("SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda - Carrito de Compra</title>
    <link rel="stylesheet" href="/tienda-ropa/css/style.css">
    <style>
        .productos-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .producto-card {
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 8px;
            width: 200px;
            text-align: center;
        }
        .producto-card h3 {
            margin: 0 0 0.5rem 0;
        }
        .precio {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        input, select {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .carrito-vacio {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
        
        <?php if ($mensaje): ?>
            <div class="success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <h2>Productos</h2>
        <div class="productos-container">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p class="precio"><?php echo $producto['precio']; ?> €</p>
                    <a href="?añadir=<?php echo $producto['id']; ?>" class="btn">Añadir al carrito</a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h2>Mi Carrito</h2>
        <?php if (empty($carrito_productos)): ?>
            <div class="carrito-vacio">El carrito está vacío</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrito_productos as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td><?php echo $item['precio']; ?> €</td>
                            <td><?php echo $item['cantidad']; ?></td>
                            <td><?php echo $item['subtotal']; ?> €</td>
                            <td><a href="?eliminar=<?php echo $item['id']; ?>" class="btn-danger" style="color: white; padding: 0.25rem 0.5rem; text-decoration: none; border-radius: 3px;">Eliminar</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th><?php echo $carrito_total; ?> €</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            
            <p><a href="?vaciar" class="btn-danger" style="color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 5px;">Vaciar carrito</a></p>
            
            <h3>Realizar Pedido</h3>
            <form method="POST" style="max-width: 400px;">
                <textarea name="direccion" rows="3" placeholder="Dirección de envío" required></textarea>
                <select name="metodo_pago" required>
                    <option value="">Seleccionar método de pago</option>
                    <option value="Tarjeta">Tarjeta de crédito</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Transferencia">Transferencia bancaria</option>
                    <option value="Contrareembolso">Contrareembolso</option>
                </select>
                <button type="submit" name="realizar_pedido" class="btn-success">Confirmar Pedido</button>
            </form>
        <?php endif; ?>
        
        <br>
        <a href="/tienda-ropa/dashboard.php" class="back">⬅ Volver al dashboard</a>
        <a href="/tienda-ropa/logout.php" class="logout">Cerrar Sesión</a>
    </div>
</body>
</html>
