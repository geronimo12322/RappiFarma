<?php
session_start();
include('conexion.php');
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"];

    // Escenario 2: Cancelar cambio
    if ($accion == "cancelar") {
        echo "🔙 Se canceló el cambio de contraseña.";
        exit;
    }

    $nueva = trim($_POST["nueva_contrasena"]);
    $repetir = trim($_POST["repetir_contrasena"]);
    $id_usuario = $_SESSION["user_id"];

    // Escenario 3: Menos de 8 caracteres
    if (strlen($nueva) < 8) {
        echo "❌ La contraseña contiene menos de 8 dígitos.";
        exit;
    }

    // Escenario 4: Formato incorrecto
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/';
    if (!preg_match($regex, $nueva)) {
        echo "❌ Formato de contraseña incorrecto.";
        exit;
    }

    // Validar que ambas contraseñas coincidan
    if ($nueva !== $repetir) {
        echo "❌ Las contraseñas no coinciden.";
        exit;
    }

    // Encriptar y actualizar
    $password_hashed = password_hash($nueva, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET Password = '$password_hashed' WHERE ID_Usuario = '$id_usuario'";

    if ($conn->query($sql) === TRUE) {
        echo "✅ Se cambió la contraseña correctamente.";
    } else {
        echo "❌ Error al cambiar la contraseña: " . $conn->error;
    }

    $conn->close();
}
?>