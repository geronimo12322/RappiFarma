<?php
require_once "linkDB.php";
$conn = getConnection();

// Obtenemos los datos del formulario
$email    = $_POST['email'] ?? '';
$exp      = $_POST['exp'] ?? '';
$token    = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$password2= $_POST['password2'] ?? '';
$secret   = "fS8#k2!9zR7bLx@qP4vT";

// Validaciones básicas
if (!$email || !$exp || !$token) {
    die("Datos del enlace inválidos.");
}

// Verificamos que las contraseñas coincidan
if ($password !== $password2) {
    $error = urlencode("Las contraseñas no coinciden.");
    header("Location: restablecerContrasena.php?email=" . urlencode($email) . "&exp=$exp&token=$token&error=$error");
    exit;
}


// Validación avanzada de contraseña
$errores = [];

if (strlen($password) < 8) {
    $errores[] = "Mínimo 8 caracteres";
}

if (!preg_match('/[A-Z]/', $password)) {
    $errores[] = "Al menos 1 letra mayúscula";
}

if (!preg_match('/[a-z]/', $password)) {
    $errores[] = "Al menos 1 letra minúscula";
}

if (!preg_match('/[0-9]/', $password)) {
    $errores[] = "Al menos 1 número";
}

if (!preg_match('/[\W_]/', $password)) {
    $errores[] = "Al menos 1 carácter especial";
}

// Verificar secuencias numéricas 
for ($i = 0; $i <= 7; $i++) {
    if (strpos($password, (string)$i . ($i+1) . ($i+2)) !== false) {
        $errores[] = "No debe contener secuencias numéricas consecutivas";
        break;
    }
}

// Si hay errores, volvemos al formulario, informando que la contraseña no cumple requisitos
if (!empty($errores)) {
    $error = "La contraseña no cumple con los requisitos:\n- " . implode("\n- ", $errores);
    $error = urlencode($error); // codificar para URL
    header("Location: restablecerContrasena.php?email=" . urlencode($email) . "&exp=$exp&token=$token&error=$error");
    exit;
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

// actualizamos la contraseña en la BD
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT ID_Usuario AS ID FROM USUARIOS WHERE Email=? AND Estado='Activo'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$conn->query("DROP EVENT IF EXISTS actualizar_columna_15_min_".$row["ID"]);
$stmt = $conn->prepare("UPDATE USUARIOS SET Password=?, CambiarContrasena=0 WHERE ID_Usuario=?");
$stmt->bind_param("ss", $password_hash, $row["ID"]);

// en caso de el cambio de contraseña sea correcto redirige a index
if ($stmt->execute()) {
    header("Location: index.php?exito_pass");
    exit;
} else {
    die("Error al actualizar la contraseña.");
}
