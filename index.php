<?php

session_start(); 
require_once 'linkDB.php'; 
$conn = getConnection();

// Si ya está logueado, redirigir a panel
if (isset($_SESSION['user_id'])) {
    header('Location: home_usuario.php');
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    
<title>RappiFarma - Ingreso</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: url('farmacia.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Filtro translúcido */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(3px);
            z-index: 0;
        }

        .main {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .top {
            flex: 35%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 30px; /* Espacio desde arriba */
        }

        .top img {
            width: 28%;
            max-width: 280px;
            height: auto;
        }

        .bottom {
            flex: 65%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 40px; /* separa el formulario del logo */
            padding-bottom: 60px; /* 👈 deja espacio al fondo */
        }

        .form-container {
            background: rgba(255, 255, 255, 0.92);
            padding: 50px 70px;
            border-radius: 18px;
            box-shadow: 0 6px 28px rgba(0,0,0,0.25);
            text-align: center;
            min-width: 400px;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 35px;
        }

        input {
            display: block;
            width: 100%;
            max-width: 320px;
            margin: 15px auto;
            padding: 14px;
            border: 1px solid #bbb;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            background-color: #ff6f00;
            color: black;
            border: none;
            padding: 12px 50px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 25px;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #e65100;
        }

        .register {
            display: block;
            margin-top: 22px;
            font-size: 15px;
            color: #000;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .top img {
                width: 50%;
            }
            .form-container {
                width: 90%;
                padding: 35px;
            }
            input {
                width: 90%;
            }
        }
    </style>
</head>
</head>
<body>
    <div class="main">
        <div class="top">
            <img src="logo.png" alt="Logo RappiFarma">
        </div>

        <div class="bottom">
            <div class="form-container">
                <h1>Ingreso a RappiFarma</h1>
                <?php if($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
                <form action="index.php" method="POST">
                    <input class="input" type="email" name="email" placeholder="Email" required>
                    <input class="input" type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit">Ingresar</button>
                    <a href="registro.php" class="register">Registrarse</a>
                </form>
            </div>
        </div>
    </div>
 
</body>
</html>
