<?php
session_start();
include 'linkDB.php'; // o la ruta donde está
$conn = getConnection();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluimos PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';





if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $telefono = trim($_POST["telefono"]);
    $dni = trim($_POST["dni"]);
    $provincia = trim($_POST["provincia"]);
    $localidad = trim($_POST["localidad"]);
    $CP = trim($_POST["CP"]);
    $direccion = trim($_POST["direccion"]);
    $tiene_obra_social = trim($_POST["tiene_obra_social"]);
    $obra_social = trim($_POST["obra_social"]);
    $nro_carnet = trim($_POST["nro_carnet"]);







    $_SESSION['form_data'] = $_POST;

    if (!preg_match('/^[0-9]+$/', $telefono)) {
        header("Location: registro.php?error=El+telefono+debe+contener+solo+numeros,+sin+espacios+ni+simbolos");
        exit;
    }

    // --- Validar formato de email ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?error=El+email+ingresado+no+es+valido");
        exit;
    }

    // --- Verificar si el email ya está registrado en usuarios ---
    $sql_check_usuarios = "SELECT * FROM usuarios WHERE email = '$email' AND Estado<>'Expirado'";
    $result_usuarios = $conn->query($sql_check_usuarios);

    // --- Verificar si el email ya está registrado en farmacias ---
    $sql_check_farmacias = "SELECT * FROM farmacias WHERE email = '$email'";
    $result_farmacias = $conn->query($sql_check_farmacias);

    if (($result_usuarios && $result_usuarios->num_rows > 0) || ($result_farmacias && $result_farmacias->num_rows > 0)) {
        header("Location: registro.php?error=El+email+ingresado+ya+esta+en+uso.+Intenta+con+otro.");
        exit;
    }

    // --- Escenario 3: contraseña con menos de 8 caracteres ---
    if (strlen($password) < 8) {
        header("Location: registro.php?error=La+contraseña+contiene+menos+de+8+caracteres");
        exit;
    }

    // --- Escenario 4: formato incorrecto ---
    //$regex = '/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_])(?!.*012|.*123|.*234|.*345|.*456|.*567|.*678|.*789).{8,}$/';
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])(?!.*012|.*123|.*234|.*345|.*456|.*567|.*678|.*789).{8,}$/';
    if (!preg_match($regex, $password)) {
        header("Location: registro.php?error=" . urlencode("Formato incorrecto, la contraseña debe incluir:\n- Al menos 1 letra mayúscula\n- Al menos 1 letra minúscula\n- Al menos 1 número\n- Al menos 1 carácter especial"));
        exit;
    }




    // --- Encriptar contraseña ---
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // --- Verificar que las contraseñas coincidan ---
    if ($password !== $confirm_password) {
        header("Location: registro.php?error=Las+contraseñas+no+coinciden");
        exit;
    }

    // --- Registrar usuario ---


    if ($tiene_obra_social == '0') {
        $obra_social = null;
        $nro_carnet = null;
    }
    $sql_insert = "INSERT INTO usuarios (nombre, apellido, email, telefono, dni, provincia, localidad, CP, direccion, tieneObraSocial, obraSocial, nroCarnet, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param('sssssssssisss', $nombre, $apellido, $email, $telefono, $dni, $provincia, $localidad, $CP, $direccion, $tiene_obra_social, $obra_social, $nro_carnet, $password_hashed);


            //  Generar token y enlace
            $secret = "]WE7y3UfvViwjzA+RuAk";        // clave para HMAC
            $expiracion = time() + 900;          // 15 minutos
            $data = $email . '|' . $expiracion;
            $token = hash_hmac('sha256', $data, $secret);

            $enlace = "http://localhost/RappiFarma/ActivarEmail.php?email=" 
                    . urlencode($email) . "&exp=" . $expiracion . "&token=" . $token;

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
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña RappiFarma';
                $mail->Body = "
                    <p>Hola,</p>
                    <p>Haz click en el siguiente enlace para restablecer tu contraseña (válido por 15 minutos):</p>
                    <p><a href='$enlace'>$enlace</a></p>
                    <p>Si no solicitaste este cambio, ignora este correo.</p>
                ";

                $mail->send();


                if ($stmt->execute()) {
                    // ✅ Registro exitoso → redirigir
                    unset($_SESSION['form_data']);
                    header("Location: index.php?exito");
                    exit;
                } else {
                    header("Location: registro.php?error=Error+al+registrar+usuario");
                }
                exit;
            } catch (Exception $e) {
                header("Location: index.php?error=emailNoEnviado");
                exit;
            }
    } else {
        header("Location: registro.php?error=Error+al+preparar+la+consulta");
    }

    $conn->close();
}
?>