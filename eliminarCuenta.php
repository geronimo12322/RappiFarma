<?php
session_start();

// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar mi cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #ff6f00; }
    </style>
</head>
<body>

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4 text-center" style="max-width: 420px; width: 100%;">
        <h4 class="fw-bold">Eliminar mi cuenta</h4>
        <p class="text-muted mt-2" style="font-size: 1.1rem;">
            Esta acción es <b>irreversible</b>. Se eliminarán todos tus datos.
        </p>

        <!-- Botón principal -->
        <button class="btn btn-danger btn-lg mt-3 w-100" data-bs-toggle="modal" data-bs-target="#confirmarModal">
            Eliminar cuenta
        </button>

        <a href="home_usuario.php" class="btn btn-outline-secondary btn-sm mt-3 w-100">
            Cancelar y volver
        </a>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-danger fw-bold">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <p>¿Estás seguro de que querés eliminar tu cuenta?</p>
        <p class="fw-semibold text-danger">No podrás recuperarla después 😥</p>

        <!-- Formulario -->
        <form id="eliminarForm" action="eliminar_cuenta_accion.php" method="POST" class="w-100 mt-3">
            <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['user_id']; ?>">

            <div class="mb-3 text-start">
                <label for="passwordConfirm" class="form-label">Ingresá tu contraseña para confirmar</label>
                <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm" placeholder="Contraseña">
                <small id="passwordError" class="text-danger d-none">Debes completar este campo</small>
            </div>

            <button type="submit" class="btn btn-danger w-100">Sí, eliminar definitivamente</button>
        </form>

        <button class="btn btn-secondary w-100 mt-2" data-bs-dismiss="modal">Cancelar</button>
      </div>

    </div>
  </div>
</div>

<script>
  // Validación del campo contraseña
  const form = document.getElementById('eliminarForm');
  const passwordInput = document.getElementById('passwordConfirm');
  const passwordError = document.getElementById('passwordError');

  form.addEventListener('submit', function(e) {
    if(passwordInput.value.trim() === "") {
      e.preventDefault();
      passwordError.classList.remove('d-none');
      passwordInput.focus();
    } else {
      passwordError.classList.add('d-none');
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
