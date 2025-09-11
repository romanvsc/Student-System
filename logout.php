<?php
require_once 'auth.php';

cerrarSesion();
header('Location: login.php?mensaje=logout');
exit;
?>
