<?php
session_start();
include 'linkDB.php'; // o la ruta donde está
$conn = getConnection();
//require_once 'conexion.php'; // asegúrate que esto define $conn (mysqli)




// Si ya está logueado, redirigir a panel
if (isset($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit;
}

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $err = "Completa email y contraseña.";
    } else {
        // Prepared statement para evitar SQL injection
        $sql = "SELECT ID_Usuario, Nombre, Email, Password FROM usuarios WHERE Email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $nombre, $email_db, $password_hash);
                $stmt->fetch();

                // Verificar contraseña (la BD debe guardar el hash con password_hash)
                if (password_verify($password, $password_hash)) {
                    // Login OK: crear sesión y redirigir
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $nombre;
                    header('Location: home_usuario.php');
                    exit;
                } else {
                    $err = "los datos ingresados son incorrectos";
                }
            } else {
                $err = "los datos ingresados son incorrectos";
            }

            $stmt->close();
        } else {
            $err = "Error del servidor. Intenta más tarde.";
        }
    }
}
?>