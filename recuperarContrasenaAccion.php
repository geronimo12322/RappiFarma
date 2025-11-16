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
$stmt = $conn->prepare("SELECT ID_Usuario AS ID, Email, CambiarContrasena FROM USUARIOS WHERE Email=? AND Estado='Activo'");
$stmt->bind_param("s", $emailIngresado);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: recuperarContrasena.php?error=correoNoExiste");
    exit;
}
$row = $result->fetch_assoc();
$stmt->close();
if ($row['CambiarContrasena'] === 1) {
    header("Location: recuperarContrasena.php?error=solicitado");
    exit;
}

//  Generar token y enlace 
$secret = "fS8#k2!9zR7bLx@qP4vT";        // clave para HMAC
$expiracion = time() + 900;          // 15 minutos
$data = $emailIngresado . '|' . $expiracion;
$token = hash_hmac('sha256', $data, $secret);

$enlace = "http://localhost/RappiFarma/restablecerContrasena.php?email=" 
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

    echo $emailIngresado;
    $mail->setFrom('rappifarm4@gmail.com', 'RappiFarma2');
    $mail->addAddress($emailIngresado);

    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña RappiFarma';
    $mail->Body = "
        <p>Hola,</p>
        <p>Haz click en el siguiente enlace para restablecer tu contraseña (válido por 15 minutos):</p>
        <p><a href='$enlace'>$enlace</a></p>
        <p>Si no solicitaste este cambio, ignora este correo.</p>
    ";
    $conn->query("CREATE EVENT actualizar_columna_15_min_".$row["ID"]." ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 15 MINUTE DO UPDATE USUARIOS SET CambiarContrasena=0 WHERE ID_Usuario=".$row["ID"]);
    $stmt = $conn->prepare("UPDATE USUARIOS SET CambiarContrasena=1 WHERE Email=?");
    $stmt->bind_param("s", $emailIngresado);
    $stmt->execute();

    $mail->send();

    header("Location: recuperarContrasena.php?success=1");
    exit;
} catch (Exception $e) {
    header("Location: recuperarContrasena.php?error=emailNoEnviado");
    exit;
}


