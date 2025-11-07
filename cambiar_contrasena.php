<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar Contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
  font-family: "Segoe UI", Arial, sans-serif;
  background-color: #f2f2f2;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: flex-start; /* 👈 antes era center */
  min-height: 100vh;
}

    .container {
  background: rgba(255, 255, 255, 1);
  width: 70%;
  margin: 0px 20px 20px 20px;
  border-radius: 18px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.25);
  box-sizing: border-box;
  padding: 50px 70px;
  text-align: center;
  width: 90%;
  max-width: 320px;
}

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    .mensaje {
      text-align: center;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      box-sizing: border-box;
    }

    .botones {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-top: 10px;
    }

    button {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 6px;
      color: #fff;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.2s;
    }

    .btn-cambiar {
      background-color: #ff6f00;
    }
    .btn-cambiar:hover {
      background-color: #0069d9;
    }

    .btn-cancelar {
      background-color: #000000;
    }
    .btn-cancelar:hover {
      background-color: #c82333;
    }

    .top {
  flex: 20%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 30px;
}

.bottom {
  flex: 65%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 30px;
  padding-bottom: 60px;
}


.logo { 
  width: 200px;
  height: auto;
  display: block;
  margin: 0 auto; /* centra horizontalmente */
}

.main {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 100%;
  min-height: 100vh;
  padding: 30px 0;
  box-sizing: border-box;
}

    @media (max-width: 480px) {
  body {
    align-items: flex-start;
    min-height: auto;
  }

  .main {
    padding: 0;
  }

  .container {
    width: 100%;
    border-radius: 18px;
    margin: 0;
    box-shadow: none;
    padding: 25px;
  }

  h2 {
    font-size: 20px;
  }

  input, select, button {
    font-size: 14px;
    padding: 10px;
    margin: 6px 0;
  }

  label {
    margin-top: 6px;
  }
  .bottom {
  padding-bottom: 400px;
}
}
  </style>
</head>
<body>
  <div class="main">
    <div class="top">
      <img src="icon.png" alt="Logo RappiFarma" class="logo">
    </div>
    
    <div class="bottom">
      <div class="container">
        <h2>Cambiar Contraseña</h2>

        <!-- 🔹 Mostrar mensaje si existe -->
        <?php if (isset($_GET['error'])): ?>
          <div class="mensaje error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="procesar_cambio_contrasena.php" method="POST">
          <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
          <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
          <input type="password" name="repetir_contrasena" placeholder="Repetir contraseña" required>

          <div class="botones">
            <button type="submit" name="accion" value="cambiar" class="btn-cambiar">Cambiar</button>
            <button type="submit" name="accion" value="cancelar" class="btn-cancelar" formnovalidate>Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>