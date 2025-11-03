<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Estilo general */
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: url('farmacia.png') no-repeat center center fixed;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-size: cover;
      position: relative;
    }

    /* Filtro translúcido */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(3px);
            z-index: 0;
        }
      
    .main {
            position: relative;
            justify-content: center; /* centra horizontal */
            align-items: center;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100vh; 
        }

    /* Contenedor principal */
    .container {
      background: #fff;
      width: 70%;
      max-height: 100%;

      margin: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
      padding: 20px;
      border-radius: 10px;
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
      background-color: #ff6f00;
      color: black;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      margin-top: 14px;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #0069d9;
    }

    /* clase para ocultar */
    .oculto { display: none; }

    .register {
      display: block;
      margin-top: 22px;
      font-size: 15px;
      color: #000;
      text-decoration: underline;
    }

    /* Responsivo para celulares */
@media (max-width: 480px) {
  body {
    align-items: flex-start;
    padding-top: 0;
    height: auto;
    overflow-y: auto;
  }

  /* Ocultar fondo translúcido detrás */
  body::before {
    display: none;
  }

  .main {
    height: auto;
    justify-content: flex-start;
    align-items: center;
    padding: 0;
  }

  .container {
    width: 100%;
    height: 100vh;          /* ocupa toda la altura visible */
    background: white;       /* fondo blanco opaco */
    overflow: visible;
    border-radius: 0;
    margin: 0;
    box-shadow: none;
    padding: 25px;
    box-sizing: border-box;
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
}
  </style>
</head>



<body>
  <div class="main">
    <div class="container">
      <h2>Registro de Usuario</h2>
      <?php if (isset($_GET['error'])): ?>
              <div style="color: red; text-align: center; margin-bottom: 10px;">
                  <?= htmlspecialchars($_GET['error']); ?>
              </div>
          <?php elseif (isset($_GET['exito'])): ?>
              <div style="color: green; text-align: center; margin-bottom: 10px;">
                  ✅ Registro exitoso
              </div>
          <?php endif; ?>
      <form action="registrar_usuario.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="text" name="telefono" placeholder="Teléfono" required>
        <input type="text" name="dni" placeholder="DNI" required>
        <input type="text" name="provincia" placeholder="Provincia" required>
        <input type="text" name="localidad" placeholder="Localidad" required>
        <input type="text" name="CP" placeholder="Codigo Postal" required>
        <input type="text" name="direccion" placeholder="Dirección" required>

        <label>¿Tenés obra social?</label>
        <select name="tiene_obra_social" id="tiene_obra_social" required>
          <option value="0">No</option>
          <option value="1">Sí</option>
        </select>


        <div id="datos_obra_social" class="oculto">
          <input type="text" name="obra_social" id="obra_social" placeholder="Nombre de la obra social">
          <input type="text" name="nro_carnet" id="nro_carnet" placeholder="Número de carnet">
        </div>

        <script>
          const selectObraSocial = document.getElementById('tiene_obra_social');
          const datosObraSocial = document.getElementById('datos_obra_social');
          const inputObraSocial = document.getElementById('obra_social');
          const inputNroCarnet = document.getElementById('nro_carnet');

          selectObraSocial.addEventListener('change', function() {
            if (this.value === '1') {
              datosObraSocial.classList.remove('oculto'); // muestra los campos
              inputObraSocial.required = true;
              inputNroCarnet.required = true;
            } else {
              datosObraSocial.classList.add('oculto'); // los oculta
              inputObraSocial.required = false;
              inputNroCarnet.required = false;
              inputObraSocial.value = '';
              inputNroCarnet.value = '';
            }
          });
        </script>

        <input type="password" name="password" placeholder="Contraseña" required>
        
        <button type="submit">Registrarme</button>
      </form>
      <a href="index.php" class="register">¿Ya tenes una cuenta?</a>
    </div>
  </div>
</body>
</html>