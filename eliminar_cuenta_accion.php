<?php
session_start();
require_once "linkDB.php";  


$conn = getConnection(); 
$idUsuario = $_SESSION['user_id'];
$passwordIngresada = $_POST["passwordConfirm"] ?? "";

//Traer la contraseña desde la BD
$query = "SELECT Password FROM usuarios WHERE ID_Usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($passwordBD);
$stmt->fetch();
$stmt->close();

// Validar si el usuario no existe
if (!$passwordBD) {
    $_SESSION["error"] = "Usuario no encontrado.";
    header("Location: eliminarcuenta.php");
    exit;
}

//Verifico contraseña ingresada
if (!password_verify($passwordIngresada, $passwordBD)) {
    $_SESSION["error"] = "Contraseña incorrecta.";
    header("Location: eliminarcuenta.php");
    exit;
}

//Si la contraseña es correcta entonces elimino el usuario
$delete = "DELETE FROM usuarios WHERE ID_Usuario = ?";
$stmt = $conn->prepare($delete);
$stmt->bind_param("i", $idUsuario);

if ($stmt->execute()) {
    session_destroy(); 
    header("Location: index.php"); 
    exit;
} else {
    $_SESSION["error"] = "Error al eliminar la cuenta.";
    header("Location: eliminar_cuenta.php");
    exit;
}
?>
