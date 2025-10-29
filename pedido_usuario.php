<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
include "conexion.php";

$user_id = $_SESSION['user_id'];

// Consulta para obtener los pedidos del usuario logueado
$sql = "SELECT ID_Pedido, FechaCreacion, Receta 
        FROM PEDIDOS 
        WHERE ID_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RappiFarma - Mis Pedidos</title>
<style>
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to bottom, #ffffff, #eaf8ff);
        min-height: 100vh;
    }

    /* HEADER */
    header {
        background-color: #00a8e8;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 40px;
        position: relative;
    }

    header img {
        height: 60px;
        position: absolute;
        left: 40px;
    }

    header h2 {
        font-size: 24px;
        margin: 0;
    }

    /* CONTENIDO */
    .contenedor {
        padding: 30px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .pedido {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 20px;
        transition: transform 0.3s ease;
    }
    .pedido:hover {
        transform: scale(1.03);
    }

    .pedido h3 {
        margin: 0;
        color: #00a8e8;
        font-size: 20px;
    }

    .pedido p {
        color: #333;
        font-size: 16px;
        margin: 8px 0;
    }

    /* FOOTER Y MENÚ */
    footer {
        background-color: #e85d00;
        color: white;
        padding: 15px 40px;
        position: relative;
    }

    .footer-container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .footer-text {
        flex: 1;
        text-align: center;
        font-size: 16px;
    }

    .menu-footer-container {
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
    }

    .menu-icon-footer {
        font-size: 26px;
        cursor: pointer;
        user-select: none;
    }

    .menu-desplegable {
        display: none;
        position: absolute;
        bottom: 40px;
        right: 0;
        background-color: #003840;
        color: white;
        border-radius: 8px;
        width: 220px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        overflow: hidden;
        z-index: 999;
    }

    .menu-desplegable.active {
        display: block;
        animation: fadeUp 0.3s ease;
    }

    .menu-desplegable ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .menu-desplegable li {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        font-size: 17px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        cursor: pointer;
        transition: background 0.3s;
    }

    .menu-desplegable li:hover {
        background-color: #00555f;
    }

    .menu-desplegable li i {
        margin-right: 12px;
        font-size: 20px;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .contenedor { grid-template-columns: 1fr; }
        .menu-desplegable { right: 20px; width: 180px; }
    }
</style>

<!-- Íconos -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <img src="logo.png" alt="Logo RappiFarma">
    <h2>Mis Pedidos</h2>
</header>

<div class="contenedor">
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '
        <div class="pedido">
            <h3>Pedido N° '.$row['ID_Pedido'].'</h3>
            <p><strong>Fecha:</strong> '.$row['FechaCreacion'].'</p>
            <p><strong>Receta:</strong> '.(!empty($row['Receta']) ? '📎 Adjunta' : 'No subida').'</p>
        </div>';
    }
} else {
    echo "<p>No tenés pedidos realizados aún.</p>";
}
?>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-text">© 2025 RappiFarma - Todos los derechos reservados.</div>
        <div class="menu-footer-container">
            <div class="menu-icon-footer" onclick="toggleMenu()">&#9776;</div>
            <nav class="menu-desplegable" id="menuDesplegable">
                <ul>
                    <li onclick="window.location.href='home_usuario.php'"><i class="fas fa-home"></i>Inicio</li>
                    <li onclick="window.location.href='pedidos_usuario.php'"><i class="fas fa-box"></i>Mis Pedidos</li>
                    <li onclick="window.location.href='logout.php'"><i class="fas fa-power-off"></i>Cerrar Sesión</li>
                </ul>
            </nav>
        </div>
    </div>
</footer>

<script>
function toggleMenu() {
    const menu = document.getElementById('menuDesplegable');
    menu.classList.toggle('active');
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('menuDesplegable');
    const icon = document.querySelector('.menu-icon-footer');
    if (!menu.contains(e.target) && !icon.contains(e.target)) {
        menu.classList.remove('active');
    }
});
</script>

</body>
</html>
