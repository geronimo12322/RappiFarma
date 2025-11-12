<?php
require_once "linkDB.php";
$conn = getConnection();

$email = $_GET['email'] ?? '';
$exp = $_GET['exp'] ?? '';
$token = $_GET['token'] ?? '';
$secret = "fS8#k2!9zR7bLx@qP4vT";

if (!$email || !$exp || !$token) {
    die("Link inválido.");
}

if (time() > (int)$exp) {
    die("El enlace ha expirado.");
}

$data = $email . '|' . $exp;
$token_valido = hash_hmac('sha256', $data, $secret);

if (!hash_equals($token_valido, $token)) {
    die("Link inválido o manipulado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cambiar contraseña</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


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

        /* Filtro translúcido que cubre todo */
body::before {
  content: "";
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(3px);
  pointer-events: none;
}

        .main {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .top {
            flex: 20%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 0px; /* Espacio desde arriba */
        }

        .top img {
            margin-top: 30px;
            width: 20%;
            max-width: 200px;
            height: auto;
        }

        .bottom {
            flex: 65%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 30px; /* separa el formulario del logo */
            padding-bottom: 60px; /* 👈 deja espacio al fondo */
        }

        .form-container {
            background: rgba(255, 255, 255, 0.92);
            padding: 50px 70px;
            border-radius: 18px;
            box-shadow: 0 6px 28px rgba(0,0,0,0.25);
            text-align: center;
            width: 90%;
            max-width: 320px;
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
    <link rel="icon" type="image/x-icon" href="icon.png">
</head>
<body>
    <div class="main">
        <div class="top">
            <img src="icon.png" alt="Logo RappiFarma">
        </div>

        <div class="bottom">
            <div class="form-container">
                <h1>Recuperar Contraseña</h1>
<!-- Formulario -->

                <form action="restablecerContrasenaAccion.php" method="POST">
                    <input class="input" type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <input class="input" type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <input class="input" type="hidden" name="exp" value="<?= htmlspecialchars($exp) ?>">

                    <input class="input" type="password" placeholder="Contraseña" name="password" required>
                    <input class="input" type="password" placeholder="Recuperar Contraseña" name="password2" required>

                    <?php if (isset($_GET['error'])): ?>
                        <p style="color:red; margin-top:10px; font-size:0.9rem; white-space: pre-line;">
                            <?= htmlspecialchars(urldecode($_GET['error'])) ?>
                        </p>
                    <?php endif; ?>

                    <button type="submit" class="btn-orange w-100">Cambiar contraseña</button>
                </form>

            </div>
        </div>
    </div>


   

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
