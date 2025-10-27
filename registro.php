<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Estilo general */
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

    /* Contenedor principal */
    .container {
      background: #fff;
      width: 100%;
      max-width: 360px; /* 🔹 más angosto */
      min-height: 580px; /* 🔹 un poco más largo */
      padding: 28px;
      margin: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
    }

    /* Título */
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    /* Campos de entrada */
    input, select {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
      box-sizing: border-box;
    }

    /* Etiquetas */
    label {
      display: block;
      margin-top: 10px;
      font-size: 14px;
      color: #333;
    }

    /* Botón */
    button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      margin-top: 14px;
    }

    button:hover {
      background-color: #0069d9;
    }

    /* Responsivo para celulares */
    @media (max-width: 480px) {
      body {
        align-items: flex-start;
        padding-top: 30px;
      }

      .container {
        max-width: 90%;
        padding: 22px;
        margin: 0 10px;
        min-height: auto;
      }

      h2 {
        font-size: 20px;
      }

      input, select, button {
        font-size: 14px;
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registro de Usuario</h2>
    <form action="registrar_usuario.php" method="POST">
      <input type="text" name="nombre" placeholder="Nombre" required>
      <input type="text" name="apellido" placeholder="Apellido" required>
      <input type="email" name="email" placeholder="Correo electrónico" required>
      <input type="text" name="telefono" placeholder="Teléfono" required>
      <input type="text" name="dni" placeholder="DNI" required>
      <input type="text" name="direccion" placeholder="Dirección" required>

      <label>¿Tenés obra social?</label>
      <select name="tiene_obra_social" required>
        <option value="0">No</option>
        <option value="1">Sí</option>
      </select>

      <input type="text" name="obra_social" placeholder="Nombre de la obra social">
      <input type="text" name="nro_carnet" placeholder="Número de carnet">

      <input type="password" name="password" placeholder="Contraseña" required>

      <button type="submit">Registrarme</button>
    </form>
  </div>
</body>
</html>