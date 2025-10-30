<?php
session_start();
include 'linkDB.php';
$conn = getConnection();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"] ?? '';

    // 🔹 Escenario 1: Cancelar cambio
    if ($accion === "cancelar") {
        header("Location: home_usuario.php");
        exit;
    }

    $actual = trim($_POST["contrasena_actual"]);
    $nueva = trim($_POST["nueva_contrasena"]);
    $repetir = trim($_POST["repetir_contrasena"]);
    $id_usuario = $_SESSION["user_id"];

    // 🔹 Verificar contraseña actual
    $sql = "SELECT Password FROM usuarios WHERE ID_Usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows !== 1) {
        header("Location: cambiar_contrasena.php?error=Error al verificar el usuario");
        exit;
    }

    $row = $result->fetch_assoc();
    $password_actual_bd = $row['Password'];

    if (!password_verify($actual, $password_actual_bd)) {
        header("Location: cambiar_contrasena.php?error=La contraseña actual no es correcta");
        exit;
    }

    // 🔹 Validaciones
    if (strlen($nueva) < 8) {
        header("Location: cambiar_contrasena.php?error=La nueva contraseña debe tener al menos 8 caracteres");
        exit;
    }

    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/';
    if (!preg_match($regex, $nueva)) {
        header("Location: cambiar_contrasena.php?error=Formato de nueva contraseña incorrecto");
        exit;
    }

    if ($nueva !== $repetir) {
        header("Location: cambiar_contrasena.php?error=Las nuevas contraseñas no coinciden");
        exit;
    }

    if (password_verify($nueva, $password_actual_bd)) {
        header("Location: cambiar_contrasena.php?error=La nueva contraseña no puede ser igual a la actual");
        exit;
    }

    // 🔹 Encriptar y actualizar
    $password_hashed = password_hash($nueva, PASSWORD_DEFAULT);
    $sql_update = "UPDATE usuarios SET Password = ? WHERE ID_Usuario = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $password_hashed, $id_usuario);

    if ($stmt_update->execute()) {
        header("Location: home_usuario.php");
        exit;
    } else {
        header("Location: cambiar_contrasena.php?error=Error al cambiar la contraseña");
        exit;
    }

    $stmt_update->close();
    $conn->close();
}
?>