<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluimos PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once "linkDB.php";

$conn = getConnection();



$emailIngresado = $_POST['email'];
      

// Verificar si el email existe en la BD
$stmt = $conn->prepare("SELECT Email FROM USUARIOS WHERE Email=?");
$stmt->bind_param("s", $emailIngresado);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header("Location: recuperarContrasena.php?error=correoNoExiste");
exit;
} 
$stmt->close();



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
    $mail->Username   = 'rappifarm4@gmail.com';    
    $mail->Password   = 'lgqu imbb scka owhh';     // contraseña de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('rappifarm4@gmail.com', 'RappiFarma2');
    $mail->addAddress($emailIngresado);

    $mail->isHTML(true);
    $mail->Subject = 'Recuperacion de contrasena RappiFarma';
    $mail->Body = "
        <p>Hola,</p>
        <p>Haz click en el siguiente enlace para restablecer tu contraseña (válido por 15 minutos):</p>
        <p><a href='$enlace'>$enlace</a></p>
        <p>Si no solicitaste este cambio, ignora este correo.</p>
    ";

    $mail->send();

    header("Location: recuperarContrasena.php?success=1");
    exit;
} catch (Exception $e) {
    header("Location: recuperarContrasena.php?error=emailNoEnviado");
    exit;
}


