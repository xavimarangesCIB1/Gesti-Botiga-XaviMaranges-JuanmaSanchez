<?php
require_once '../includes/auth.php';
require_once '../conexio.php';
redirigirSiNoAdmin();

$id = $_GET['id'] ?? 0;

$conn->query("DELETE FROM usuarios WHERE id = $id AND rol != 'admin'");

header('Location: panel.php');
exit();
?>

