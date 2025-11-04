<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include 'linkDB.php'; // o la ruta donde está
$conn = getConnection();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
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
    // --- Validar formato de email ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?error=El+email+ingresado+no+es+valido");
        exit;
    }

    // --- Verificar si el email ya está registrado ---
    $sql_check = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql_check);

    if ($result && $result->num_rows > 0) {
        header("Location: registro.php?error=El+email+ingresado+ya+esta+en+uso.+Intenta+con+otro.");
        exit;
    }

    // --- Escenario 3: contraseña con menos de 8 caracteres ---
    if (strlen($password) < 8) {
        header("Location: registro.php?error=La+contraseña+contiene+menos+de+8+caracteres");
        exit;
    }

    // --- Escenario 4: formato incorrecto ---
    $regex = '/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_])(?!.*012|.*123|.*234|.*345|.*456|.*567|.*678|.*789).{8,}$/';
    if (!preg_match($regex, $password)) {
        header("Location: registro.php?error=" . urlencode("Formato incorrecto, la contraseña debe incluir:\n- Al menos 1 letra mayúscula\n- Al menos 1 letra minúscula\n- Al menos 1 número\n- Al menos 1 carácter especial"));
        exit;
    }


    // --- Encriptar contraseña ---
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // --- Registrar usuario ---
    
   
    if ($tiene_obra_social == '0') {
        $obra_social = null;
        $nro_carnet = null;
    }
    $sql_insert = "INSERT INTO usuarios (nombre, apellido, email, telefono, dni, provincia, localidad, CP, direccion, tieneObraSocial, obraSocial, nroCarnet, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param('sssssssssisss', $nombre, $apellido, $email, $telefono, $dni, $provincia, $localidad, $CP, $direccion, $tiene_obra_social, $obra_social, $nro_carnet, $password_hashed);
            if ($stmt->execute()) {
                // ✅ Registro exitoso → redirigir

                
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

                unset($_SESSION['form_data']);
                header("Location: index.php");
                exit;
            } else {
                echo "❌ Error al registrar usuario: " . $stmt->error;
            }
            $stmt->close();
    } else {
        echo "❌ Error al preparar la consulta: " . $conn->error;
    }
       
    $conn->close();
}
?>