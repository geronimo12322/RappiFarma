<?php
session_start();
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

    if (!preg_match('/^[0-9]+$/', $telefono)) {
        header("Location: registro.php?error=El+telefono+debe+contener+solo+numeros,+sin+espacios+ni+simbolos");
        exit;
    }

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
    //$regex = '/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[\W_])(?!.*012|.*123|.*234|.*345|.*456|.*567|.*678|.*789).{8,}$/';
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])(?!.*012|.*123|.*234|.*345|.*456|.*567|.*678|.*789).{8,}$/';
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
                unset($_SESSION['form_data']);
                header("Location: index.php");
                exit;
            } else {
                header("Location: registro.php?error=Error+al+registrar+usuario");
                $stmt->error;
            }
            $stmt->close();
    } else {
        header("Location: registro.php?error=Error+al+preparar+la+consulta");
        $conn->error;
    }
       
    $conn->close();
}
?>