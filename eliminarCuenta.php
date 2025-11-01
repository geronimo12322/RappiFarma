<?php
session_start();

// Si no hay usuario logueado, redirigimos
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
          .mensaje-eliminar {
            font-size: 0.85rem;
            color: #000;
            font-weight: 500;
}

    </style>
</head>

<body>

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4 text-center" style="max-width: 430px; width: 100%;">
        
        <h4 class="fw-bold text-danger">¿Estás seguro de que querés eliminar tu cuenta?</h4>
        <p class="mt-2 mb-4">
            No podrás recuperarla después 😥
        </p>

        <form action="eliminar_cuenta_accion.php" method="POST" class="w-100">
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
            <button type="submit" class="btn btn-danger w-100">Continuar</button>
        </form>

        <a href="home_usuario.php" class="btn btn-outline-secondary w-100 mt-3">
            Cancelar
        </a>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
