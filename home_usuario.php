<?php
include "linkDB.php";
$db = new database();
$conn = $db->conexion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RappiFarma - Catálogo</title>
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
        padding: 15px 40px;
        position: relative;
    }

    header img {
        height: 60px;
    }

    .menu-icon {
        font-size: 30px;
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    /* MENÚ DESPLEGABLE */
    .menu-desplegable {
        display: none;
        position: absolute;
        right: 40px;
        top: 70px;
        background-color: #003840;
        color: white;
        border-radius: 8px;
        width: 220px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 999;
        overflow: hidden;
    }

    .menu-desplegable.active {
        display: block;
        animation: fadeIn 0.3s ease;
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
        cursor: pointer;
        font-size: 17px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: background 0.3s;
    }

    .menu-desplegable li:hover {
        background-color: #00555f;
    }

    .menu-desplegable li i {
        margin-right: 12px;
        font-size: 20px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* BUSCADOR */
    .busqueda {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 15px;
        gap: 10px;
        background-color: white;
    }

    .busqueda input {
        width: 50%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    .busqueda button {
        background-color: #00a8e8;
        border: none;
        color: white;
        border-radius: 6px;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 16px;
    }

    /* PRODUCTOS */
    .contenedor {
        padding: 30px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .producto {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .producto:hover { transform: scale(1.03); }

    .producto img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .info { padding: 15px; }

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

    footer {
        background-color: #e85d00ff;
        color: white;
        text-align: center;
        padding: 10px 0;
        margin-top: auto;
    }

    @media (max-width: 768px) {
        .busqueda input { width: 70%; }
        .menu-desplegable { right: 20px; width: 180px; }
    }
</style>

<!-- Íconos (Font Awesome) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <img src="logo.png" alt="Logo RappiFarma">
    <div class="menu-icon" onclick="toggleMenu()">&#9776;</div>

    <!-- MENÚ DESPLEGABLE -->
    <nav class="menu-desplegable" id="menuDesplegable">
        <ul>
            <li><i class="fas fa-user"></i>Mi Cuenta</li>
            <li><i class="fas fa-shopping-cart"></i>Carrito</li>
            <li><i class="fas fa-box"></i>Mis Pedidos</li>
            <li><i class="fas fa-power-off"></i>Cerrar Sesión</li>
        </ul>
    </nav>
</header>

<div class="busqueda">
    <input type="text" placeholder="Buscar productos...">
    <button>🔍</button>
</div>



<footer>
    © 2025 RappiFarma - Todos los derechos reservados.
</footer>

<script>
function toggleMenu() {
    const menu = document.getElementById('menuDesplegable');
    menu.classList.toggle('active');
}

// Cerrar menú si se hace clic fuera
document.addEventListener('click', function(e) {
    const menu = document.getElementById('menuDesplegable');
    const icon = document.querySelector('.menu-icon');
    if (!menu.contains(e.target) && !icon.contains(e.target)) {
        menu.classList.remove('active');
    }
});
</script>

</body>
</html>
