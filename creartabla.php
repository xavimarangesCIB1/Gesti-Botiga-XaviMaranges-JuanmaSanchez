<?php
require_once 'conexio.php';

$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'usuarios' creada correctamente<br>";
    
    $adminEmail = "admin@tienda.com";
    $adminPass = md5("admin123");
    $conn->query("INSERT INTO usuarios (nombre, email, password, rol) 
                  SELECT 'Administrador', '$adminEmail', '$adminPass', 'admin'
                  WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = '$adminEmail')");
    
    $userEmail = "user@tienda.com";
    $userPass = md5("user123");
    $conn->query("INSERT INTO usuarios (nombre, email, password, rol) 
                  SELECT 'Usuario Normal', '$userEmail', '$userPass', 'user'
                  WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = '$userEmail')");
    
    echo "Usuarios de prueba creados<br>";
    echo "Admin: admin@tienda.com / admin123<br>";
    echo "Usuario: user@tienda.com / user123";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
