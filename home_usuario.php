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
        padding: 15px 30px; /* Ajustado el padding */
        position: fixed; /* Fijo en la parte inferior */
        bottom: 0;
        width: 100%;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 990; /* Asegura que el footer esté sobre el contenido, pero debajo del modal */
    }

    .footer-container {
        display: flex;
        justify-content: space-between; /* Distribuye el espacio */
        align-items: center;
        position: relative;
        padding: 0 10px; 
    }

    .footer-text {
        flex: 1;
        text-align: center;
        font-size: 16px;
    }

    .menu-footer-container {
        display: flex; /* Alinea los botones uno al lado del otro */
        align-items: center;
    }

    /*boton de la camara */
    .btn-camera {
        background: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        margin-right: 15px; /* Separación con el ícono de menú */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: transform .2s ease;
    }

    .btn-camera:hover {
        transform: scale(1.1);
    }

    .btn-camera img {
        width: 26px;
        height: 26px;
    }
    .menu-icon-footer {
        font-size: 26px;
        cursor: pointer;
        user-select: none;
    }

    .menu-desplegable {
        display: none;
        position: absolute;
        bottom: 55px; /* Subir un poco más */
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

    /* --- ESTILOS DEL MODAL DE RECETA --- */
    .modal {
        display: none; /* Oculto por defecto */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4); /* Fondo oscuro */
        animation: fadeIn 0.3s;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; 
        padding: 20px;
        border-radius: 10px;
        width: 80%; 
        max-width: 400px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        text-align: center;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #333;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-content h2 {
        color: #e85d00;
        margin-top: 0;
    }

    .modal-content input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .modal-content button {
        background-color: #00a8e8;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .modal-content button:hover:not(:disabled) {
        background-color: #0088b9;
    }
    
    .modal-content button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
    
    #mensajeCarga {
        margin-top: 15px;
        font-weight: bold;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 768px) {
        .contenedor { grid-template-columns: 1fr; }
        .footer-text { display: none; }

        .footer-container {
            justify-content: center;
            gap: 20px;
        }

        .btn-camera { margin-right: 10px; }

        .menu-desplegable {
            right: 10px;
            bottom: 70px; /* ✅ Menú más arriba para que no tape */
            width: 180px;
        }
    }
</style>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <img src="icon.png" alt="Logo RappiFarma">
    <h2 class="bienvenida">Bienvenido, <?=htmlspecialchars($_SESSION['user_name'])?></h2>
</header>

<div id="recetaModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Subir Receta Médica</h2>
        <form id="uploadForm" onsubmit="return validarImagen(event);" enctype="multipart/form-data">
            <input type="file" id="archivoReceta" name="archivoReceta" accept="image/*" required> 
            
            <button type="submit" id="btnConfirmar" disabled>Confirmar Carga</button>
        </form>
        <p id="mensajeCarga"></p>
        <p style="font-size: 0.8em; color: #666;">Máximo 16 MB.</p>
    </div>
</div>
<div class="contenedor">
<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bajoReceta = $row['BajoReceta'] ? "Sí" : "No";
        // Simulamos una imagen de producto
        $imagenProducto = 'medicina.png'; 
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
<div style="height: 80px;"></div> 

<footer>
    <div class="footer-container">
        <div class="footer-text">© 2025 RappiFarma - Todos los derechos reservados.</div>
        
        <div class="menu-footer-container">
            
            <button class="btn-camera" onclick="abrirModal()">
                <img src="camara.png" alt="Subir Receta">
            </button>

            <div class="menu-icon-footer" onclick="toggleMenu()">&#9776;</div>
            
            <nav class="menu-desplegable" id="menuDesplegable">
                <ul>
                    <li onclick="window.location.href='mi_cuenta.php'"><i class="fas fa-user"></i>Mi Cuenta</li>
                    
                    <li onclick="window.location.href='pedido_usuario.php'"><i class="fas fa-box"></i>Mis Pedidos</li>
                    <li onclick="window.location.href='logout.php'"><i class="fas fa-power-off"></i>Cerrar Sesión</li>
                </ul>
            </nav>
        </div>
    </div>
</footer>

<script>
    const MAX_SIZE_MB = 16;
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024 - 1; // 16777215 bytes
    const modal = document.getElementById('recetaModal');
    const fileInput = document.getElementById('archivoReceta');
    const btnConfirmar = document.getElementById('btnConfirmar');
    const mensajeCarga = document.getElementById('mensajeCarga');
    
    // Función para abrir el modal
    function abrirModal() {
        modal.style.display = "block";
        // Limpiar el estado anterior
        fileInput.value = '';
        btnConfirmar.disabled = true;
        mensajeCarga.textContent = '';
    }
    
    // Función para cerrar el modal
    function cerrarModal() {
        modal.style.display = "none";
    }

    // Cerrar si se hace clic fuera del modal (o para cerrar el menú)
    window.onclick = function(event) {
        // Cierra el modal si se hace clic fuera de su contenido
        if (event.target == modal) {
            cerrarModal();
        }
        
        // Mantener la funcionalidad del menú hamburguesa
        const menu = document.getElementById('menuDesplegable');
        const icon = document.querySelector('.menu-icon-footer');
        const cameraButton = document.querySelector('.btn-camera'); 

        if (menu.classList.contains('active') && !menu.contains(event.target) && !icon.contains(event.target) && !cameraButton.contains(event.target)) {
            menu.classList.remove('active');
        }
    }

    // Listener para habilitar/deshabilitar el botón de confirmar al seleccionar un archivo
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            btnConfirmar.disabled = false;
            mensajeCarga.textContent = ''; 
        } else {
            btnConfirmar.disabled = true;
        }
    });

    // Función principal para validar el tamaño y enviar la imagen al servidor
    async function validarImagen(event) {
        event.preventDefault(); // Evita el envío del formulario por defecto
        
        const file = fileInput.files[0];
        
        if (!file) {
            mensajeCarga.style.color = 'red';
            mensajeCarga.textContent = "Selecciona una imagen.";
            return false;
        }

        // Validación de tamaño del lado del cliente (rápida)
        if (file.size > MAX_SIZE_BYTES) {
            mensajeCarga.style.color = 'red';
            mensajeCarga.textContent = `Error: la imagen pesa más de los ${MAX_SIZE_KB} kb.`;
            setTimeout(cerrarModal, 3000); 
            return false;
        }
        
        // Si el tamaño es correcto, preparamos el envío al servidor
        mensajeCarga.style.color = 'orange';
        mensajeCarga.textContent = "Subiendo archivo, por favor espera...";
        btnConfirmar.disabled = true;

        // Usamos FormData para empaquetar el archivo para el envío
        const formData = new FormData();
        formData.append('archivoReceta', file);

        try {
            // Realizamos la petición AJAX al script PHP (¡asegúrate de que existe este archivo!)
            const response = await fetch('subir_receta.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            // Procesamos la respuesta del servidor
            if (response.ok && result.success) {
                mensajeCarga.style.color = 'green';
                mensajeCarga.textContent = result.message; // Muestra "Carga exitosa..."
            } else {
                // Manejamos errores devueltos por PHP
                mensajeCarga.style.color = 'red';
                mensajeCarga.textContent = result.message || 'Error desconocido en el servidor.'; 
            }

        } catch (error) {
            console.error('Error al enviar la receta:', error);
            mensajeCarga.style.color = 'red';
            mensajeCarga.textContent = 'Error de conexión. Intenta de nuevo.';
        } finally {
            // Restablece el estado y cierra el modal después de la notificación
            btnConfirmar.disabled = true;
            setTimeout(() => {
                cerrarModal();
                mensajeCarga.textContent = '';
                btnConfirmar.disabled = false;
            }, 3000); 
        }

        return false;
    }

    function toggleMenu() {
        const menu = document.getElementById('menuDesplegable');
        menu.classList.toggle('active');
    }
</script>

</body>
</html>

