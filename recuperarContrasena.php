<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recuperar contraseña</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #ff6f00; }
    .form-container {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        max-width: 400px;
        margin: 80px auto;
    }
    .btn-orange { background-color: #ff6f00; color: black; }
    .btn-orange:hover { background-color: #e65100; }
</style>
</head>
<body>

<div class="form-container text-center">
    <h3>Recuperar contraseña</h3>
    <p>Ingresa tu correo registrado y recibirás un enlace para restablecer tu contraseña.</p>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="text-danger mb-3"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="recuperarContrasenaAccion.php" method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Correo electrónico" required>
        <button type="submit" class="btn btn-orange w-100">Recuperar</button>
    </form>

    <a href="index.php" class="d-block mt-3">Volver al login</a>
</div>

<?php if(isset($_SESSION['success'])): ?>
<!-- Modal de éxito -->
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

<!-- Bootstrap JS: necesario para el modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();

    document.getElementById('aceptarBtn').addEventListener('click', function() {
        window.location.href = 'index.php';
    });
</script>

<?php unset($_SESSION['success']); ?>
<?php endif; ?>

</body>
</html>
