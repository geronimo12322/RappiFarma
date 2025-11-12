<?php
require_once "linkDB.php";
$conn = getConnection();

// Obtenemos los datos del formulario
$email    = $_GET['email'] ?? '';
$exp      = $_GET['exp'] ?? '';
$token    = $_GET['token'] ?? '';
$secret   = "]WE7y3UfvViwjzA+RuAk";

// Validaciones básicas
if (!$email || !$exp || !$token) {
    die("Datos del enlace inválidos.");
}

// Verificar expiración del enlace recibido por mail
if (time() > (int)$exp) {
    die("El enlace ha expirado.");
}

// Verificar token
$data = $email . '|' . $exp;
$token_valido = hash_hmac('sha256', $data, $secret);

if (!hash_equals($token_valido, $token)) {
    die("Link inválido o manipulado.");
}

$stmt = $conn->prepare("UPDATE USUARIOS SET Estado='Activo' WHERE Email=? AND Estado='Desactivado'");
$email = htmlspecialchars($email);
$stmt->bind_param("s", $email);

// en caso de el cambio de contraseña sea correcto redirige a index
if ($stmt->execute()) {
    header("Location: index.php?exito_ver");
    exit;
} else {
    die("Error al actualizar la contraseña.");
}
