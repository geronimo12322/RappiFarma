<!-- pagina de recuperar contrasena -->
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recuperar contraseña</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body { background-color: #D3D3D3; }

    /* ✅ Barra azul con el logo */
    .barra-logo {
        width: 100%;
        background-color: #0277bd; /* azul */
        height: 80px;
        display: flex;
        align-items: center;
        padding-left: 15px;
    }

    .logo {
        width: 55px;
    }

    .form-container {
        background: #fff;
        padding: 55px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        max-width: 400px;
        margin: 20px auto;      /* 🔹 bajamos un poco el contenedor para que no se pegue al borde */
    }

    .btn-orange { background-color: #ff6f00; color: black; }
    .btn-orange:hover { background-color: #D3D3D3; }

    .msg-error {
        color: red;
        font-size: 0.9rem;
        text-align: center;
    }
</style>
</head>
<body>

<!-- ✅ Barra azul con logo -->
<div class="barra-logo">
    <img src="icon.png" alt="Logo" class="logo">
</div>

<div class="form-container text-center">
    <h3>Recuperar contraseña</h3>
    <p>Ingresa tu correo registrado y recibirás un enlace para restablecer tu contraseña.</p>

    <?php if(isset($_GET['error']) && $_GET['error'] === "correoNoExiste"): ?>
        <p class="msg-error">El correo ingresado no está registrado.</p>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'emailNoEnviado'): ?>
        <p class="msg-error">No se pudo enviar el correo. Intentalo nuevamente.</p>
    <?php endif; ?>

    <form action="recuperarContrasenaAccion.php" method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Correo electrónico" required>
        <button type="submit" class="btn btn-orange w-100">Recuperar</button>
    </form>

    <a href="index.php" class="d-block mt-3">Volver al login</a>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <h5 class="modal-title text-success fw-bold">Correo enviado con éxito</h5>
      </div>
      <div class="modal-body">
        <p>Revisa tu correo para restablecer la contraseña.</p>
        <button id="aceptarBtn" class="btn btn-success">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();

    document.getElementById('aceptarBtn').addEventListener('click', function() {
        window.location.href = 'index.php';
    });
</script>
<?php endif; ?>

</body>
</html>
