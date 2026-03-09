<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$basedatos = "tienda_ropa_xavimaranges_juanmasanchez";

$conn = new mysqli($servidor, $usuario, $password, $basedatos);

if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
