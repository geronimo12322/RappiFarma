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
    body {
        background-color: #FFFFFF;
        margin: 0;
    }

    .barra-logo {
        width: 100%;
        background-color: #0277bd;
        height: 80px;
        display: flex;
        align-items: center;
        padding-left: 15px;
    }

    .logo {
        width: 55px;
    }

    .btn-orange {
        background-color: #ff6f00;
        color: black;
        border: none;
        padding: 15px;
        font-size: 1.2rem;
    }
    .btn-orange:hover {
        background-color: #e56300;
    }
    .formulario-restablecer {
    padding-top: 80px; 
    max-width: 400px;
    margin: auto;
}


</style>
</head>
<body>

<div class="barra-logo">
    <img src="icon.png" alt="Logo" class="logo">
</div>

<!-- Formulario -->

    <form action="restablecerContrasenaAccion.php" method="POST" class="formulario-restablecer">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="exp" value="<?= htmlspecialchars($exp) ?>">

        <div class="mb-4 text-start">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password" class="form-control form-control-lg" required>
        </div>

        <div class="mb-4 text-start">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password2" class="form-control form-control-lg" required>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <p style="color:red; margin-top:10px; font-size:0.9rem; white-space: pre-line;">
                <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            </p>
        <?php endif; ?>

        <button type="submit" class="btn-orange w-100">Cambiar contraseña</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
