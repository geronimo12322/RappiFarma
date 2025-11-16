<?php
session_start();
// Si ya está logueado, redirigir a home-usuario
if (isset($_SESSION['user_id'])) {
        header('Location: home_usuario.php');
    exit;
}
if (isset($_SESSION['farmacia_id'])) {
        header('Location: pedidos.php');
    exit;
}
?>
<!-- pagina de recuperar contrasena -->
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<title>Recuperar contraseña</title>

<style>
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
  z-index: 0;
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
</style>
<link rel="icon" type="image/x-icon" href="icon.png">
</head>
<body>
<body>
    <div class="main">
        <div class="top">
            <img src="icon.png" alt="Logo RappiFarma">
        </div>

        <div class="bottom">
            <div class="form-container">

    <h3>Recuperar contraseña</h3>
    <p>Ingresa tu correo registrado y recibirás un enlace para restablecer tu contraseña.</p>

    <?php if(isset($_GET['error']) && $_GET['error'] === "correoNoExiste"): ?>
        <p class="msg-error">El correo ingresado no está registrado.</p>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'emailNoEnviado'): ?>
        <p class="msg-error">No se pudo enviar el correo. Intentalo nuevamente.</p>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'solicitado'): ?>
        <p class="msg-error">Ya ha solicitado un cambio de contraseña.</p>
    <?php endif; ?>

    <form action="recuperarContrasenaAccion.php" method="POST">
        <input type="email" name="email" class="input" placeholder="Correo electrónico" required>
        <button type="submit">Recuperar</button>
    </form>

    <a href="index.php" class="register">Volver al login</a>
</div>

<?php if (isset($_GET['success'])): ?>
    

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel" style="color: #000000; font-size: 1.5rem; ">
            Correo enviado correctamente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body text-center">
          Se ha enviado un correo a la direccion especificada. Dirijase al link para recuperar la contraseña.
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
        </div>
    </div>

</body>
</html>
