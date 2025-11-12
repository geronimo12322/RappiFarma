<?php
session_start();

// Si no hay usuario logueado, redirigimos
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Verifica si la contraseña fue validada
$showConfirmModal = false;
if (isset($_SESSION['password_valid']) && $_SESSION['password_valid'] === true) {
    $showConfirmModal = true;
    unset($_SESSION['password_valid']); // solo se usa una vez
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
        /* HEADER */
        header {
            background-color: #00a8e8;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            position: relative;
        }

        header img {
            height: 60px;
        }
        body { background-color: #FFFFFF; }
        .logo { position: absolute; top: 15px; left: 15px; width: 55px; }
        .mensaje-eliminar { font-size: 0.85rem; color: #000; font-weight: 500; }
        .barra-logo { width: 100%; background-color: #0277bd; height: 80px; display: flex; align-items: center; padding-left: 15px; }
    </style>
</head>

<body>

<header>
    <img src="icon.png" alt="Logo RappiFarma">
</header>

<div class="container d-flex flex-column justify-content-center align-items-center"
     style="min-height: calc(100vh - 100px);">

    <div class="card shadow pt-4 pb-4 px-1 text-center" style="width: min(90%, 420px);">
        <h4 class="fw-bold text-danger">¿Estás seguro de que querés eliminar tu cuenta?</h4>
        <p class="mt-2 mb-4">No podrás recuperarla después 😥</p>

        <form action="validar_contraseña.php" method="POST" class="w-100">
            <input type="hidden" name="id_usuario" value="<?= $_SESSION['user_id']; ?>">

            <div class="mb-3 text-start">
                <label class="form-label">Ingresá tu contraseña para continuar</label>
                <input type="password" class="form-control" name="passwordConfirm" required placeholder="Contraseña">

                <?php if (isset($_SESSION['error'])): ?>
                    <small class="text-danger"><?= $_SESSION['error']; ?></small>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>

            <p class="mensaje-eliminar mb-3">
                Esta acción es <b>irreversible</b>. Se eliminarán todos tus datos.
            </p>

            <button type="submit" class="btn w-100" style="background-color:#ff6f00; color:black; border:none;">
                Continuar
            </button>
        </form>

        <a href="mi_cuenta.php" class="btn btn-outline-secondary w-100 mt-3">Cancelar</a>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel" style="color: #000000; font-size: 1.5rem; ">
            Confirmar eliminación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body text-center">
        <p style="color: #dc3545; font-size: 1.2rem; font-weight: 500;">
          ¿Estás seguro? Esta acción borrará todos tus datos de forma permanente.
        </p>
      </div>

      <div class="modal-footer d-flex justify-content-between">
        <!-- Botón cancelar igual que en la tarjeta -->
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>

        <form action="eliminar_cuenta_accion.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= $_SESSION['user_id']; ?>">
            <button type="submit" class="btn" style="background-color: #ff6f00; color: #000000; ">
                Sí, estoy seguro
            </button>
        </form>
      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if($showConfirmModal): ?>
<script>
    // Si la contraseña fue validada, mostrar modal al cargar la página
    var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
</script>
<?php endif; ?>

</body>
</html>
