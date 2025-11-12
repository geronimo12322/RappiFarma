<?php
session_start();
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: url('farmacia.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Filtro translúcido que cubre todo */
body::before {
  content: "";
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(3px);
  z-index: 0;
  pointer-events: none;
}

        .main {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .top {
            flex: 20%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 0px; /* Espacio desde arriba */
        }

        .top img {
            margin-top: 30px;
            width: 20%;
            max-width: 200px;
            height: auto;
        }

        .bottom {
            flex: 65%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 30px; /* separa el formulario del logo */
            padding-bottom: 60px; /* 👈 deja espacio al fondo */
        }

        .form-container {
            background: rgba(255, 255, 255, 0.92);
            padding: 50px 70px;
            border-radius: 18px;
            box-shadow: 0 6px 28px rgba(0,0,0,0.25);
            text-align: center;
            width: 90%;
            max-width: 320px;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 35px;
        }

        input {
            display: block;
            width: 100%;
            max-width: 320px;
            margin: 15px auto;
            padding: 14px;
            border: 1px solid #bbb;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            background-color: #ff6f00;
            color: black;
            border: none;
            padding: 12px 50px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 25px;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #e65100;
        }

        .register {
            display: block;
            margin-top: 22px;
            font-size: 15px;
            color: #000;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .top img {
                width: 50%;
            }
            .form-container {
                width: 90%;
                padding: 35px;
            }
            input {
                width: 90%;
            }
        }
        
      /* clase para ocultar */
      .oculto { display: none; }
    </style>
    <link rel="icon" type="image/x-icon" href="icon.png">
</head>



<body>
  <div class="main">
    <div class="top">
      <img src="icon.png" alt="Logo RappiFarma" class="logo">
    </div>

    <div class="bottom">
        <div class="form-container">
        <h2>Registro de Usuario</h2>
        <?php if (isset($_GET['error'])): ?>
                <div style="color: red; text-align: center; margin-bottom: 10px;">
                    <?= nl2br(htmlspecialchars($_GET['error'])); ?>
                </div>
            <?php elseif (isset($_GET['exito'])): ?>
                <div style="color: green; text-align: center; margin-bottom: 10px;">
                    ✅ Registro exitoso
                </div>
            <?php endif; ?>
        <form action="registrar_usuario.php" method="POST">
          
          <input class="input" type="text" name="nombre" placeholder="Nombre" required value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>">
          <input class="input" type="text" name="apellido" placeholder="Apellido" required value="<?= htmlspecialchars($form_data['apellido'] ?? '') ?>">
          <input class="input" type="email" name="email" placeholder="Correo electrónico" required value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
          <div class="form-group">
            <input class="input" 
              type="text" 
              name="telefono" 
              id="telefono"
              placeholder="Teléfono (solo números)"
              required 
              pattern="[0-9]+"
              title="El teléfono debe contener solo números, sin espacios ni símbolos."
              class="form-control"
              value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>"
            >
          </div>
          <input class="input" type="text" name="dni" placeholder="DNI" required value="<?= htmlspecialchars($form_data['dni'] ?? '') ?>">
          <input class="input" type="text" name="provincia" placeholder="Provincia" required value="<?= htmlspecialchars($form_data['provincia'] ?? '') ?>">
          <input class="input" type="text" name="localidad" placeholder="Localidad" required value="<?= htmlspecialchars($form_data['localidad'] ?? '') ?>">
          <input class="input" type="text" name="CP" placeholder="Codigo Postal" required value="<?= htmlspecialchars($form_data['CP'] ?? '') ?>">
          <input class="input" type="text" name="direccion" placeholder="Dirección" required value="<?= htmlspecialchars($form_data['direccion'] ?? '') ?>">

          <label>¿Tenés obra social?</label>
          <select name="tiene_obra_social" id="tiene_obra_social" required>
            <option value="0" <?= (isset($form_data['tiene_obra_social']) && $form_data['tiene_obra_social'] == '0') ? 'selected' : '' ?>>No</option>
            <option value="1" <?= (isset($form_data['tiene_obra_social']) && $form_data['tiene_obra_social'] == '1') ? 'selected' : '' ?>>Sí</option>
          </select>


          <div id="datos_obra_social" class="oculto">
            <input class="input" type="text" name="obra_social" id="obra_social" placeholder="Nombre de la obra social" value="<?= htmlspecialchars($form_data['obra_social'] ?? '') ?>">
            <input class="input" type="text" name="nro_carnet" id="nro_carnet" placeholder="Número de carnet" value="<?= htmlspecialchars($form_data['nro_carnet'] ?? '') ?>">
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

          <input class="input" type="password" name="password" placeholder="Contraseña" required>
          <input class="input" type="password" name="confirm_password" placeholder="Contraseña" required>
          
          <button type="submit">Registrarme</button>
          <a href="index.php" class="register">¿Ya tenes una cuenta?</a>
        </form>
        <script>
          const telefonoInput = document.getElementById('telefono');
          const errorTelefono = document.getElementById('error-telefono');

          telefonoInput.addEventListener('input', function() {
            // Rechaza todo lo que no sea número
            if (/[^0-9]/.test(this.value)) {
              errorTelefono.style.display = 'block';
              this.setCustomValidity("Solo se permiten números, sin espacios ni símbolos");
            } else {
              errorTelefono.style.display = 'none';
              this.setCustomValidity("");
            }
          });
        </script>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['form_data']); ?>
</body>
</html>