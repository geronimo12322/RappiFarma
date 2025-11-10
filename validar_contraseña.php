<?php
session_start();
require_once "linkDB.php";  

$conn = getConnection(); 
$idUsuario = $_SESSION['user_id'];
$passwordIngresada = $_POST["passwordConfirm"] ?? "";

// Traer la contraseña de la BD
$query = "SELECT Password FROM usuarios WHERE ID_Usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($passwordBD);
$stmt->fetch();
$stmt->close();

// Usuario no encontrado
if (!$passwordBD) {
    $_SESSION["error"] = "Usuario no encontrado.";
    header("Location: eliminarcuenta.php");
    exit;
}

// Contraseña incorrecta
if (!password_verify($passwordIngresada, $passwordBD)) {
    $_SESSION["error"] = "Contraseña incorrecta.";
    header("Location: eliminarcuenta.php");
    exit;
}

// Contraseña correcta → activar modal
$_SESSION['password_valid'] = true;
header("Location: eliminarcuenta.php");
exit;
?>
