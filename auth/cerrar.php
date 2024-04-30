<?php
session_start(); // Iniciar sesión para acceder a la sesión actual
session_destroy(); // Destruir la sesión actual
header("Location: ../login.php"); // Redirigir al usuario a la página de inicio de sesión
exit();
?>