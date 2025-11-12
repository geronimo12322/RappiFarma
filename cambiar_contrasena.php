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

    .exito {
      background-color: #f8d7da;
      color: #23721cff;
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
    gap: 5px;
    justify-content: space-between;
    margin-top: 0px;
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
.btn-container {
        flex-direction: column;
    }
    .botones-container {
        flex-direction: column;
    }
    .botones-container .btn {
        flex: 1 1 100%;
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
                <div style="color: red; text-align: center; margin-bottom: 10px;">
                    <?= nl2br(htmlspecialchars($_GET['error'])); ?>
                </div>
            <?php elseif (isset($_GET['exito'])): ?>
                <div style="color: green; text-align: center; margin-bottom: 10px;">
                    <?= nl2br(htmlspecialchars($_GET['exito'])); ?>
                </div>
            <?php endif; ?> 

        <form action="procesar_cambio_contrasena.php" method="POST">
          <input type="password" name="contrasena_actual" placeholder="Contraseña actual" required>
          <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
          <input type="password" name="repetir_contrasena" placeholder="Repetir contraseña" required>
          

          <!-- 🔹 Botones -->
          <div class="botones-container">
              <button type="submit" name="accion" value="cambiar" class="btn btn-principal">Cambiar</button>
              <button type="submit" name="accion" value="cancelar" class="btn btn-principal" formnovalidate>Cancelar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</body>
</html>