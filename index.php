<?php
session_start();
include 'linkDB.php'; // o la ruta donde está
$conn = getConnection();
//require_once 'conexion.php'; // asegúrate que esto define $conn (mysqli)




// Si ya está logueado, redirigir a home-usuario
if (isset($_SESSION['user_id'])) {
        header('Location: home_usuario.php');
    exit;
}
if (isset($_SESSION['farmacia_id'])) {
        header('Location: pedidos.php');
    exit;
}


$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $err = "Completa email y contraseña.";
    } else {
        // 🔹 Primero buscar en tabla de usuarios
        $sql_usuario = "SELECT ID_Usuario AS id, Nombre, Email, Password FROM usuarios WHERE Email = ? AND Estado = 'Activo'";
        if ($stmt = $conn->prepare($sql_usuario)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $nombre, $email_db, $password_hash);
                $stmt->fetch();
                
                if (password_verify($password, $password_hash)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $nombre;
                    $_SESSION['tipo'] = 'usuario';
                    header('Location: home_usuario.php');
                    exit;
                } else {
                    $err = "Los datos ingresados son incorrectos.";
                }
            } else {
                // 🔹 Si no es usuario, probar si es farmacia
                
                $sql_farmacia = "SELECT ID_Farmacia AS id, Direccion, Email, Password FROM farmacias WHERE Email = ?";
                if ($stmt2 = $conn->prepare($sql_farmacia)) {
                    $stmt2->bind_param('s', $email);
                    $stmt2->execute();
                    $stmt2->store_result();

                    if ($stmt2->num_rows === 1) {
                        $stmt2->bind_result($id, $direccion, $email_db, $password_hash);
                        $stmt2->fetch();
                        var_dump($email_db, $password_hash, $password);
                        if (password_verify($password, $password_hash)) {
                            $_SESSION['farmacia_id'] = $id;
                            $_SESSION['farmacia_direccion'] = $direccion;
                            $_SESSION['tipo'] = 'farmacia';
                            header('Location: pedidos.php');
                            exit;
                        } else {
                            $err = "Los datos ingresados son incorrectos.";
                        }
                    } else {
                        $err = "Los datos ingresados son incorrectos.";
                    }

                    $stmt2->close();
                }
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
                <h1>Ingreso a RappiFarma</h1>
                <?php if($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
                <?php
                if (isset($_GET['exito'])) {
                    $msg = "✅ Registro exitoso";
                    $title = "Usuario registrado";
                    $body = "Su usuario se ha registrado con exito. Recibira un correo en la direccion especificada para verificar la cuenta.";
                }
                if (isset($_GET['exito_ver'])) {
                    $msg = "✅ Verificacion exitosa";
                    $title = "Correo verificado";
                    $body = "Su correo ha sido verificado correctamente. Ingrese sus datos para iniciar sesion.";
                }
                if (isset($_GET['exito_pass'])) {
                    $msg = "✅ Cambio de contraseña exitoso";
                    $title = "Contraseña actualizada";
                    $body = "Su contraseña se ha cambiado con exito. Ingrese las nuevas credenciales para iniciar sesion.";
                }
                ?>
                <?php if (isset($_GET['exito']) || isset($_GET['exito_ver']) || isset($_GET['exito_pass'])): ?>
                    <div style="color: green; text-align: center; margin-bottom: 10px;">
                        <?php echo $msg; ?>
                    </div><!-- Modal de confirmación -->
                        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel" style="color: #000000; font-size: 1.5rem; ">
                                    <?php echo $title; ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>

                            <div class="modal-body text-center">
                                <?php echo $body; ?>
                            </div>

                            <div class="modal-footer d-flex justify-content-center">
                                <!-- Botón cancelar igual que en la tarjeta -->
                                <button type="button" class="" data-bs-dismiss="modal">Cerrar</button>
                            </div>

                            </div>
                        </div>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                        <script>
                            // Si la contraseña fue validada, mostrar modal al cargar la página
                            var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                            confirmModal.show();
                        </script>
                        <script>
                            var modal = new bootstrap.Modal(document.getElementById('successModal'));
                            modal.show();

                            document.getElementById('aceptarBtn').addEventListener('click', function() {
                                window.location.href = 'index.php';
                            });
                        </script>
                <?php endif; ?>
                <form action="index.php" method="POST">
                    <input class="input" type="email" name="email" placeholder="Email" required>
                    <input class="input" type="password" name="password" placeholder="Contraseña" required>
                    <button type="submit">Ingresar</button>
                    <a href="registro.php" class="register">Registrarse</a>
                    <a href="recuperarcontrasena.php" class="register">Olvide mi contraseña</a>
                </form>
            </div>
        </div>
    </div>


   

    
</body>
</html>
