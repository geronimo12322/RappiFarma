<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluimos PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once "linkDB.php";

$conn = getConnection();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


$idUsuario = $_SESSION['user_id'];
$emailIngresado = $_POST['email'];      

// Obtener el correo del usuario desde la base de datos
$stmt = $conn->prepare("SELECT Email FROM USUARIOS WHERE ID_Usuario=?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($emailUsuario);
$stmt->fetch();
$stmt->close();

// si el email que se esperaba y el que ingreso el usuario no son iguales devuelve error
if ($emailIngresado != $emailUsuario) {
    $_SESSION['error'] = "El correo no coincide con tu cuenta.";
    header("Location: recuperarContrasena.php");
    exit;
}


//  Generar token y enlace 
$secret = "fS8#k2!9zR7bLx@qP4vT";        // clave para HMAC
$expiracion = time() + 900;          // 15 minutos
$data = $emailIngresado . '|' . $expiracion;
$token = hash_hmac('sha256', $data, $secret);

$enlace = "http://localhost/RappiFarma-main/restablecerContrasena.php?email=" 
          . urlencode($emailIngresado) . "&exp=" . $expiracion . "&token=" . $token;

// --- Enviar correo ---
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'RappiFarm4@gmail.com';      // Gmail de rappifarm
    $mail->Password   = 'lwkd dxqn zsbc fwgn';       // contraseña de gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('RappiFarm4@gmail.com', 'RappiFarma');
    $mail->addAddress($emailIngresado); 

    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña RappiFarma';
    $mail->Body = "
        <p>Hola,</p>
        <p>Haz click en el siguiente enlace para restablecer tu contraseña (válido por 15 minutos):</p>
        <p><a href='$enlace'>$enlace</a></p>
        <p>Si no solicitaste este cambio, ignora este correo.</p>
    ";

    $mail->send();

    $_SESSION['success'] = "Correo enviado con éxito. Revisa tu bandeja de entrada.";
    header("Location: recuperarContrasena.php");
    exit;


} catch (Exception $e) {
    $_SESSION['error'] = "El mail no está registrado.";
    header("Location: recuperarContrasena.php");
    exit;
}
