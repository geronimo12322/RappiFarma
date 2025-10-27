<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Panel</title></head>
<body>
  <h2>Bienvenido, <?=htmlspecialchars($_SESSION['user_name'])?></h2>
  <p>Área privada.</p>
  <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>