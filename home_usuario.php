<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
include "linkDB.php";
$conn = getConnection();

// Consulta para obtener productos
$sql = "SELECT p.NombreComercial, p.NombreGenerico, p.Formato, p.Precio, p.Cantidad, p.BajoReceta 
        FROM PRODUCTOS p
        JOIN PRESUPUESTOS pr ON p.ID_Presupuesto = pr.ID_Presupuesto
        JOIN FARMACIAS f ON pr.ID_Farmacia = f.ID_Farmacia
        ORDER BY p.NombreComercial ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RappiFarma</title>
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
        justify-content: space-between;
        padding: 15px 30px;
        position: relative;
    }

    header img {
        height: 60px;
    }

    .bienvenida {
        flex: 1;
        text-align: center;
        font-size: 22px;
        color: #fcf5f5ff;
        margin: 0;
    }

   .contenedor { 
        padding: 30px; 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); /* 2 columnas fijas */
        gap: 25px; 
    }

    .producto { 
        background: white; 
        border-radius: 12px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        overflow: hidden; 
        transition: transform 0.3s ease; 
    }
    .producto:hover { 
        transform: scale(1.03); 
    }
    .producto img { 
        width: 100%; 
        height: 200px; 
        object-fit: cover; 
    }
    .info { 
        padding: 15px;
    }
    .info h3 { 
        margin: 0; 
        font-size: 20px; 
        color: #333; 
    }
    .info p { 
        font-size: 15px; 
        color: #666; 
    }
    .precio { 
        font-weight: bold; 
        color: #00a8e8; 
        margin-top: 10px; 
        font-size: 18px; 
    } 
   
    /* Menú hamburguesa en footer */
    footer {
        background-color: #e85d00; /* naranja */
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
        .contenedor { grid-template-columns: 1fr; } /* 1 por fila en móvil */
        .menu-desplegable { right: 20px; width: 180px; }
    }
</style>

<!-- Íconos (Font Awesome) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <img src="logo.png" alt="Logo RappiFarma">
    <h2 class="bienvenida">Bienvenido, <?=htmlspecialchars($_SESSION['user_name'])?></h2>
</header>

<div class="contenedor">
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bajoReceta = $row['BajoReceta'] ? "Sí" : "No";
        echo '
        <div class="producto">
            <div class="info">
                <h3>'.$row['NombreComercial'].'</h3>
                <p>Genérico: '.$row['NombreGenerico'].'<br>
                Formato: '.$row['Formato'].'<br>
                Cantidad: '.$row['Cantidad'].'<br>
                Bajo receta: '.$bajoReceta.'</p>
                <div class="precio">$ '.$row['Precio'].'</div>
            </div>
        </div>';
    }
} else {
    echo "<p>No hay productos disponibles.</p>";
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
                    <li><i class="fas fa-user"></i>Mi Cuenta</li>
                    <li><i class="fas fa-shopping-cart"></i>Carrito</li>
                    <li onclick="window.location.href='pedido_usuario.php'"><i class="fas fa-box"></i>Mis Pedidos</li>
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
