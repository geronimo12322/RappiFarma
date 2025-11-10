<?php
session_start();
require_once "linkDB.php";  

$conn = getConnection(); 
$idUsuario = $_POST['id_usuario'] ?? 0;

$update = "UPDATE usuarios SET Estado = 'Borrado' WHERE ID_Usuario = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("i", $idUsuario);

if ($stmt->execute()) {
    session_destroy(); 
    header("Location: index.php"); 
    exit;
} else {
    $_SESSION["error"] = "Error al modificar el estado de la cuenta.";
    header("Location: eliminarcuenta.php");
    exit;
}
?>
