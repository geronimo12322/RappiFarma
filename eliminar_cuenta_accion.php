<?php
session_start();
require_once "linkDB.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$conn = getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idUsuario = $_SESSION['user_id']; // tomo el usuario logueado
    $passwordIngresada = $_POST["passwordConfirm"] ?? '';

    // Traigo la contraseña real del usuario
    $query = "SELECT Password FROM USUARIOS WHERE ID_Usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $stmt->bind_result($passwordBD);
    $stmt->fetch();
    $stmt->close();

    if (!$passwordBD) {
        $_SESSION["error"] = "Usuario no encontrado.";
        header("Location: eliminarCuenta.php");
        exit();
    }

    // Verifico contraseña
    if (!password_verify($passwordIngresada, $passwordBD)) {
        $_SESSION["error"] = "Contraseña incorrecta.";
        header("Location: eliminarCuenta.php");
        exit();
    }

    // Elimino la cuenta
    $delete = "DELETE FROM USUARIOS WHERE ID_Usuario = ?";
    $stmt = $conn->prepare($delete);
    $stmt->bind_param("i", $idUsuario);

    if ($stmt->execute()) {
        session_destroy(); // cierro sesión
        header("Location: index.php"); // página final
        exit;
    } else {
        echo "Error al eliminar la cuenta.";
    }
}
?>
