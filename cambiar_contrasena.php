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
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background: #fff;
      width: 100%;
      max-width: 340px;
      min-height: 480px;
      padding: 28px;
      margin: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      justify-content: center;
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
      background-color: #007bff;
    }
    .btn-cambiar:hover {
      background-color: #0069d9;
    }

    .btn-cancelar {
      background-color: #dc3545;
    }
    .btn-cancelar:hover {
      background-color: #c82333;
    }

    @media (max-width: 480px) {
      .container {
        max-width: 90%;
        padding: 24px;
        margin: 0 10px;
      }
      h2 { font-size: 20px; }
      input, button { font-size: 14px; padding: 10px; }
      .botones { flex-direction: column; }
      .botones button { width: 100%; }
    }
  </style>
</head>
<body>
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
</body>
</html>