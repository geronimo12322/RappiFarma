<?php
session_start();
include 'linkDB.php'; // tu conexión
$conn = getConnection();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['user_id'];
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $telefono = trim($_POST["telefono"]);
    $dni = trim($_POST["dni"]);
    $provincia = trim($_POST["provincia"]);
    $localidad = trim($_POST["localidad"]);
    $CP = trim($_POST["CP"]);
    $direccion = trim($_POST["direccion"]);
    $tiene_obra_social = trim($_POST["tiene_obra_social"]);
    $obra_social = trim($_POST["obra_social"]);
    $nro_carnet = trim($_POST["nro_carnet"]);

    if ($tiene_obra_social === '0') {
        $obra_social = null;
        $nro_carnet = null;
    }

    $sql_update = "UPDATE usuarios 
        SET Nombre = ?, Apellido = ?, Telefono = ?, DNI = ?, Provincia = ?, Localidad = ?, CP = ?, Direccion = ?, 
            TieneObraSocial = ?, ObraSocial = ?, NroCarnet = ?
        WHERE ID_Usuario = ?";

    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param(
            'sssssssssissi',
            $nombre,
            $apellido,
            $telefono,
            $dni,
            $provincia,
            $localidad,
            $CP,
            $direccion,
            $tiene_obra_social,
            $obra_social,
            $nro_carnet,
            $id_usuario
        );

        if ($stmt->execute()) {
            header("Location: mi_cuenta.php?actualizado=1");
            exit;
        } else {
            $mensaje = "❌ Error al actualizar los datos: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
    }
}

// Obtener datos actuales del usuario (para precargar el formulario)
$sql_select = "SELECT * FROM usuarios WHERE ID_Usuario = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Error al preparar SELECT: " . $conn->error);
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Cuenta</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f5f9fc;
    font-family: Arial, sans-serif;
}
.container {
    max-width: 800px;
    margin: 0px auto 40px auto; /* arriba - derecha - abajo - izquierda */
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}
label { 
    font-weight:bold; 
    display:block; 
    margin-top:8px; 
}
input, select, textarea { 
    width:100%; 
    padding:8px; 
    margin-top:6px; 
    margin-bottom:12px; 
    border:1px solid #ccc; 
    border-radius:5px; 
}
input[readonly] { 
    background-color:white; 
    color:#333; 
    cursor:not-allowed; 
}
.row { 
    display:flex; 
    gap:10px; 
    flex-wrap: wrap;
}
.col { 
    flex:1; 
    min-width: 200px;
}
.btn-container {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
}
.btn {
    flex: 1;
    min-width: 150px;
    text-align: center;
    background-color:#007b8f;
    color:white;
    padding:10px 20px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    text-decoration: none;
    font-weight: bold;
}

.botones-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: space-between;
    margin-top: 20px;
}

.botones-container .btn {
    flex: 1 1 calc(25% - 10px);
    text-align: center;
    text-decoration: none;
    padding: 10px 0;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 15px;
    transition: background-color 0.2s ease;
}

/* Estilos por tipo */
.btn-principal {
    background-color: #ff6f00;
    color: white;
}
.btn-principal:hover {
    background-color: #e65100;
}

.btn-cambio {
    background-color: #ff6f00;
    color: white;
}
.btn-cambio:hover {
    background-color: #e65100;
}

.btn-secundario {
    background-color: #ff6f00;
    color: white;
}
.btn-secundario:hover {
    background-color: #e65100;
}

.btn-peligro {
    background-color: #ff6f00;
    color: white;
}
.btn-peligro:hover {
    background-color: #e65100;
}

/* 🔹 Vista móvil: los botones se apilan */
@media (max-width: 600px) {
    .botones-container {
        flex-direction: column;
    }
    .botones-container .btn {
        flex: 1 1 100%;
    }
    .container {
    width: 75%;
    border-radius: 18px;
    margin: 0;
    box-shadow: none;
    padding: 25px;
  }
}

.success { 
    background-color:#d4edda; 
    color:#155724; 
    padding:10px; 
    border-radius:5px; 
    margin-bottom:15px; 
}
.oculto { display:none; }

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

/* 🔹 Responsividad para pantallas chicas */
@media (max-width: 600px) {
    .btn-container {
        flex-direction: column;
    }
    h2 {
        font-size: 20px;
    }
    .btn {
        width: 100%;
    }
}
</style>
</head>
<body>

    <div class="top">
      <img src="icon.png" alt="Logo RappiFarma" class="logo">
    </div>
    <div class="bottom">
        <div class="container">
            <h2>Mi Cuenta</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="success"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['actualizado']) && $_GET['actualizado'] == 1): ?>
                <div class="success">Datos actualizados correctamente.</div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['Nombre'] ?? '') ?>" required>
                    </div>
                    <div class="col">
                        <label>Apellido</label>
                        <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['Apellido'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($usuario['Email'] ?? '') ?>" readonly>
                    </div>
                    <div class="col">
                        <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input 
            type="text" 
            name="telefono" 
            id="telefono"
            placeholder="Teléfono (solo números)"
            required 
            pattern="[0-9]+"
            title="El teléfono debe contener solo números, sin espacios ni símbolos."
            class="form-control"
            value="<?= htmlspecialchars($usuario['Telefono'] ?? '') ?>"
        >
        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>DNI</label>
                        <input type="text" name="dni" value="<?= htmlspecialchars($usuario['DNI'] ?? '') ?>">
                    </div>
                    <div class="col">
                        <label>Código Postal</label>
                        <input type="text" name="CP" value="<?= htmlspecialchars($usuario['CP'] ?? '') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Provincia</label>
                        <input type="text" name="provincia" value="<?= htmlspecialchars($usuario['Provincia'] ?? '') ?>">
                    </div>
                    <div class="col">
                        <label>Localidad</label>
                        <input type="text" name="localidad" value="<?= htmlspecialchars($usuario['Localidad'] ?? '') ?>">
                    </div>
                </div>

                <label>Dirección</label>
                <textarea name="direccion"><?= htmlspecialchars($usuario['Direccion'] ?? '') ?></textarea>

                <!-- Tiene obra social -->
                <label for="tiene_obra_social">¿Tenés obra social?</label>
                <select name="tiene_obra_social" id="tiene_obra_social" required>
                    <option value="0" <?= (isset($usuario['TieneObraSocial']) && $usuario['TieneObraSocial'] == 0) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= (isset($usuario['TieneObraSocial']) && $usuario['TieneObraSocial'] == 1) ? 'selected' : '' ?>>Sí</option>
                </select>

                <div id="datos_obra_social" class="<?= (isset($usuario['TieneObraSocial']) && $usuario['TieneObraSocial'] == 1) ? '' : 'oculto' ?>">
                    <div class="row">
                        <div class="col">
                            <label for="obra_social">Obra Social</label>
                            <input type="text" name="obra_social" id="obra_social" value="<?= htmlspecialchars($usuario['ObraSocial'] ?? '') ?>">
                        </div>
                        <div class="col">
                            <label for="nro_carnet">Número de Carnet</label>
                            <input type="text" name="nro_carnet" id="nro_carnet" value="<?= htmlspecialchars($usuario['NroCarnet'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                
                <!-- 🔹 Botones -->
                <div class="botones-container">
                    <button type="submit" class="btn btn-principal">Guardar cambios</button>
                    <a href="cambiar_contrasena.php" class="btn btn-cambio">Cambiar Contraseña</a>
                    <a href="eliminarCuenta.php" class="btn btn-peligro">Eliminar Cuenta</a>
                    <a href="home_usuario.php" class="btn btn-secundario">Volver</a>
                </div>
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

<script>
const selectObraSocial = document.getElementById('tiene_obra_social');
const datosObraSocial = document.getElementById('datos_obra_social');
const inputObraSocial = document.getElementById('obra_social');
const inputNroCarnet = document.getElementById('nro_carnet');

function actualizarCamposObraSocial() {
    if (selectObraSocial.value === '1') {
        datosObraSocial.classList.remove('oculto');
        inputObraSocial.required = true;
        inputNroCarnet.required = true;
    } else {
        datosObraSocial.classList.add('oculto');
        inputObraSocial.required = false;
        inputNroCarnet.required = false;
        inputObraSocial.value = '';
        inputNroCarnet.value = '';
    }
}

actualizarCamposObraSocial();
selectObraSocial.addEventListener('change', actualizarCamposObraSocial);
</script>
</body>
</html>