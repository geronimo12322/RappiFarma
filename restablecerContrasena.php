<?php
session_start();
require_once "linkDB.php";
$conn = getConnection();

$email = $_GET['email'] ?? '';
$exp = $_GET['exp'] ?? '';
$token = $_GET['token'] ?? '';
$secret = "fS8#k2!9zR7bLx@qP4vT";

if (!$email || !$exp || !$token) {
    die("Link inválido.");
}

// Verificamos expiración del link del correo
if (time() > (int)$exp) {
    die("El enlace ha expirado.");
}

// Verificamos la firma HMAC
$data = $email . '|' . $exp;
$token_valido = hash_hmac('sha256', $data, $secret);

if (!hash_equals($token_valido, $token)) {
    die("Link inválido o manipulado.");
}

// Si pasa todo, mostrar formulario para nueva contraseña

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cambiar contraseña</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .container { max-width: 400px; }
    .btn-lg-custom {
        padding: 15px;
        font-size: 1.2rem;
    }
</style>
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4 text-center">Cambiar contraseña</h3>
    <form action="restablecerContrasenaAccion.php" method="POST">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="exp" value="<?= htmlspecialchars($exp) ?>">

        <div class="mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password" class="form-control form-control-lg" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password2" class="form-control form-control-lg" required>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="text-danger mb-3">
                <?= $_SESSION['error']; 
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>


        <button type="submit" class="btn btn-primary w-100 btn-lg-custom">Cambiar contraseña</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

