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
    /* Estilo general */
body {
  font-family: "Segoe UI", Arial, sans-serif;
  background: url('farmacia.png') no-repeat center center fixed;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
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
  justify-content: center;
  align-items: center;
  width: 100%;
  min-height: 100vh;
  padding: 30px 0;
  box-sizing: border-box;
}

/* Contenedor principal */
.container {
  background: rgba(255, 255, 255, 0.92);
  width: 70%;
  margin: 20px;
  border-radius: 18px;
  box-shadow: 0 6px 28px rgba(0,0,0,0.25);
  box-sizing: border-box;
  padding: 50px 70px;
  text-align: center;
  width: 90%;
  max-width: 320px;
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
  text-align: center;
}

.top {
            flex: 20%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 30px; /* Espacio desde arriba */
        }



        .bottom {
            flex: 65%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 30px; /* separa el formulario del logo */
            padding-bottom: 60px; /* 👈 deja espacio al fondo */
        }


.logo { 
  width: 200px;
  height: auto;
  display: block;
  margin: 0 auto; /* centra horizontalmente */
}


/* Responsivo para celulares */
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
          
          <input type="text" name="nombre" placeholder="Nombre" required value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>">
          <input type="text" name="apellido" placeholder="Apellido" required value="<?= htmlspecialchars($form_data['apellido'] ?? '') ?>">
          <input type="email" name="email" placeholder="Correo electrónico" required value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
          <div class="form-group">
            <input 
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
          <input type="text" name="dni" placeholder="DNI" required value="<?= htmlspecialchars($form_data['dni'] ?? '') ?>">
          <input type="text" name="provincia" placeholder="Provincia" required value="<?= htmlspecialchars($form_data['provincia'] ?? '') ?>">
          <input type="text" name="localidad" placeholder="Localidad" required value="<?= htmlspecialchars($form_data['localidad'] ?? '') ?>">
          <input type="text" name="CP" placeholder="Codigo Postal" required value="<?= htmlspecialchars($form_data['CP'] ?? '') ?>">
          <input type="text" name="direccion" placeholder="Dirección" required value="<?= htmlspecialchars($form_data['direccion'] ?? '') ?>">

          <label>¿Tenés obra social?</label>
          <select name="tiene_obra_social" id="tiene_obra_social" required>
            <option value="0" <?= (isset($form_data['tiene_obra_social']) && $form_data['tiene_obra_social'] == '0') ? 'selected' : '' ?>>No</option>
            <option value="1" <?= (isset($form_data['tiene_obra_social']) && $form_data['tiene_obra_social'] == '1') ? 'selected' : '' ?>>Sí</option>
          </select>


          <div id="datos_obra_social" class="oculto">
            <input type="text" name="obra_social" id="obra_social" placeholder="Nombre de la obra social" value="<?= htmlspecialchars($form_data['obra_social'] ?? '') ?>">
            <input type="text" name="nro_carnet" id="nro_carnet" placeholder="Número de carnet" value="<?= htmlspecialchars($form_data['nro_carnet'] ?? '') ?>">
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