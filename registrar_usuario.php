<?php
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


    // --- Validar formato de email ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ El email ingresado no es válido.";
        exit;
    }

    // --- Verificar si el email ya está registrado ---
    $sql_check = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql_check);

    if ($result && $result->num_rows > 0) {
        echo "⚠️ El email ingresado ya está en uso. Intenta con otro.";
        exit;
    }

    // --- Validar fortaleza de la contraseña ---
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])(?!.*012|.*123|.*234|.*345|.*456|.*567|.*678|.*789).{8,}$/';

    if (!preg_match($regex, $password)) {
        echo "❌ La contraseña no cumple con los requisitos:<br>
        - Mínimo 8 caracteres<br>
        - Al menos 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial<br>
        - No debe contener secuencias numéricas (como 123, 456, etc.)";
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