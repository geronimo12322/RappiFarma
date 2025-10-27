<?php
session_start();
require_once 'conexion.php'; // asegúrate que esto define $conn (mysqli)



// Si ya está logueado, redirigir a panel
if (isset($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit;
}

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $err = "Completa email y contraseña.";
    } else {
        // Prepared statement para evitar SQL injection
        $sql = "SELECT ID_Usuario, Nombre, Email, Password FROM usuarios WHERE Email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $nombre, $email_db, $password_hash);
                $stmt->fetch();

                // Verificar contraseña (la BD debe guardar el hash con password_hash)
                if (password_verify($password, $password_hash)) {
                    // Login OK: crear sesión y redirigir
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $nombre;
                    header('Location: panel.php');
                    exit;
                } else {
                    $err = "Contraseña incorrecta.";
                }
            } else {
                $err = "No existe un usuario con ese email.";
            }

            $stmt->close();
        } else {
            $err = "Error del servidor. Intenta más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* estilo general */
body {
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
  background: #f2f2f2;
}

/* contenedor principal */
.form {
  background: #fff;
  padding: 28px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,.1);
  width: 100%;
  max-width: 340px; /* 🔹 un poco más angosto */
  min-height: 540px; /* 🔹 un poco más largo */
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* campos e inputs */
.input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  box-sizing: border-box;
}

/* botón */
.btn {
  width: 100%;
  padding: 12px;
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  margin-top: 8px;
}

.btn:hover {
  background: #0069d9;
}

/* mensaje de error */
.error {
  color: #b00020;
  margin: 8px 0;
  text-align: center;
}

/* texto debajo del botón */
p {
  text-align: center;
  font-size: 14px;
  margin-top: 16px;
}

/* enlaces */
a {
  color: #007bff;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}

/* vista para pantallas pequeñas */
@media (max-width: 480px) {
  .form {
    padding: 24px;
    margin: 0 10px;
    max-width: 90%;
    min-height: 520px;
  }

  h2 {
    font-size: 20px;
    text-align: center;
  }

  .btn, .input {
    font-size: 15px;
  }
}
</style>
</head>
<body>
  <div class="form">
    <h2>Iniciar sesión</h2>
    <?php if($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="post" action="login.php">
      <input class="input" type="email" name="email" placeholder="Email" required>
      <input class="input" type="password" name="password" placeholder="Contraseña" required>
      <button class="btn" type="submit">Entrar</button>
    </form>
    <p>¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
  </div>
</body>
</html>