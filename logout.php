<?php
session_start();
session_destroy();
header('Location: /tienda-ropa/login.php');
exit();
?>
