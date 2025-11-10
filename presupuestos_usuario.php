<?php
session_start();
// Asegura que solo usuarios logueados accedan
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
// Asegúrate de que 'linkDB.php' contiene la función getConnection()
include "linkDB.php"; 
$conn = getConnection();
$user_id = $_SESSION['user_id'];
$url_gestion_pres = "test.php";
$id = 0;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    header('Location: index.php');
}

// Consulta para obtener datos del pedido
$sql = "
    SELECT
		COUNT(P.ID_Pedido) AS Cant_PresAceptado,
        P.ID_Pedido AS ID_Pedido,
        P.FechaCreacion AS FechaCreacion
    FROM 
        PEDIDOS P
    JOIN
        PRESUPUESTOS PR
    ON PR.ID_Pedido = P.ID_Pedido
    WHERE 
        P.ID_Pedido = ? AND PR.Aceptado = 1"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$pedido = Array();

if ($result->num_rows > 0) {
    $pedido = $result->fetch_assoc();
    if ($pedido['Cant_PresAceptado'] != 0) {
        header('Location: index.php');
    }
} else {
    header('Location: index.php');
}

// Consulta para obtener todos los presupuestos asociados al pedido del usuario
$sql = "
    SELECT 
        PR.ID_Presupuesto,
        PR.FechaCreacion AS FechaPresupuesto,
        PR.Extras,
        F.RazonSocial AS Farmacia,
        (
            SELECT IFNULL(SUM(T3.Precio * T3.Cantidad), 0) 
            FROM PRODUCTOS T3 
            WHERE T3.ID_Presupuesto = PR.ID_Presupuesto
        ) AS TotalProductos,
        (
            (
                SELECT IFNULL(SUM(T4.Precio * T4.Cantidad), 0) 
                FROM PRODUCTOS T4 
                WHERE T4.ID_Presupuesto = PR.ID_Presupuesto
            ) + PR.Extras
        ) AS TotalPresupuesto
    FROM 
        PRESUPUESTOS PR
    JOIN 
        FARMACIAS F ON PR.ID_Farmacia = F.ID_Farmacia
    WHERE 
        PR.ID_Pedido = ?
    ORDER BY 
        PR.FechaCreacion DESC"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RappiFarma - Gestionar Presupuestos</title>
<style>
    /* Estilos CSS (Incluye el modal de validación y el botón QR) */
    * { box-sizing: border-box; }
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to bottom, #ffffff, #eaf8ff); min-height: 100vh; display: flex; flex-direction: column; }
    header { background-color: #00a8e8; color: white; display: flex; align-items: center; justify-content: center; padding: 15px 40px; position: relative; }
    header img { height: 60px; position: absolute; left: 40px; }
    header h2 { font-size: 24px; margin: 0; }
    .main-content { padding: 0 30px 30px 30px; flex-grow: 1; }
    .section-title { color: #00a8e8; margin: 30px 0 15px 0; font-size: 24px; border-bottom: 2px solid #00a8e8; padding-bottom: 5px; }
    .contenedor { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
    .presupuesto { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px; transition: transform 0.3s ease; }
    .presupuesto:hover { transform: scale(1.03); }
    .presupuesto h3 { margin: 0 0 5px 0; color: #e85d00; font-size: 20px; }
    .presupuesto p { color: #333; font-size: 16px; margin: 8px 0; }
    .btn-ver { display: inline-block; background-color: #007bff; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 10px; transition: background-color 0.3s; }
    .btn-ver:hover { background-color: #0056b3; }
    .status-badge { display: inline-block; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 14px; margin-top: 5px; }
    .status-esperando { background-color: #e85d00; display: inline-block; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 14px; margin-top: 5px; }
    .status-entregado { background-color: #4caf50; color: white; }
    .status-aceptado { background-color: #00a8e8; color: white; }
    .status-pendiente { background-color: #ffeb3b; color: #333; }
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
    .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border-radius: 15px; width: 90%; max-width: 800px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; animation-name: animatetop; animation-duration: 0.4s }
    @keyframes animatetop { from {top: -300px; opacity: 0} to {top: 0; opacity: 1} }
    .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1; }
    .close-btn:hover, .close-btn:focus { color: #333; text-decoration: none; }
    footer { background-color: #e85d00; color: white; padding: 15px 40px; position: relative; margin-top: auto; }
    .footer-container { display: flex; justify-content: flex-end; align-items: center; position: relative; }
    .footer-text { flex: 1; text-align: center; font-size: 16px; }
    .menu-footer-container { display: flex; align-items: center; }
    
    /* Estilo del botón QR/Validar */
    .btn-validar {
        background-color: white;
        color: #e85d00;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        cursor: pointer;
        margin-right: 15px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .icono-validar {
        width: 34px;
        height: 34px;
        object-fit: contain;
        filter: drop-shadow(0 0 2px rgba(0,0,0,0.2));
    }
    .btn-validar:hover { transform: scale(1.08); box-shadow: 0 6px 14px rgba(0,0,0,0.3); }

    .menu-icon-footer { font-size: 26px; cursor: pointer; user-select: none; }
    .menu-desplegable { display: none; position: absolute; bottom: 40px; right: 0; background-color: #003840; color: white; border-radius: 8px; width: 220px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); overflow: hidden; z-index: 999; }
    .menu-desplegable.active { display: block; animation: fadeUp 0.3s ease; }
    .menu-desplegable ul { list-style: none; margin: 0; padding: 0; }
    .menu-desplegable li { display: flex; align-items: center; padding: 15px 20px; font-size: 17px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); cursor: pointer; transition: background 0.3s; }
    .menu-desplegable li:hover { background-color: #00555f; }
    .menu-desplegable li i { margin-right: 12px; font-size: 20px; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Estilos del Modal de Validación */
    #validarModal .modal-content { text-align: center; max-width: 400px;}
    #validarModal input { width: 80%; padding: 10px; margin: 15px 0; font-size: 18px; border: 2px solid #00a8e8; border-radius: 8px; text-transform: uppercase; }
    #validarModal button { background-color: #00a8e8; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 16px; cursor: pointer; transition: background 0.3s ease; font-weight: bold; }
    #validarModal button:hover { background-color: #007bbd; }
    
    @media (max-width: 768px) {
        .contenedor { grid-template-columns: 1fr; }
        .footer-text { display: none; }
        .footer-container { justify-content: center; gap: 20px; }
        .menu-desplegable { right: 10px; bottom: 70px; width: 180px; }
    }
</style>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <img src="icon.png" alt="Logo RappiFarma">
    <h2>Pedido N°<?php echo $pedido['ID_Pedido']; ?> 💊</h2>
</header>

<div class="main-content">
    <h2>Datos de Pedido:</h2>
    <p>Pedido N°<?php echo $pedido['ID_Pedido']; ?></p>
    <p>Fecha de Creacion: <?php echo $pedido['FechaCreacion']; ?></p>
    <div>
        <span>Receta: </span>
        <a href="mostrar_receta.php?id= <?php echo $pedido['ID_Pedido']; ?> " target="_blank" style="background-color: #4caf50; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background 0.3s;">
            <i class="fas fa-image"></i> Ver Receta
        </a>
        </div>
    <h2 class="section-title">📋 Presupuestos (<?php echo count($budgets); ?>)</h2>
    <div class="contenedor">
    <?php 
    if (!empty($budgets)) {
        foreach($budgets as $row) {
            $total_formateado = number_format($row['TotalPresupuesto'], 2, ',', '.');
            echo '
            <div class="presupuesto">
                <h3>Presupuesto N° '.$row['ID_Presupuesto'].'</h3>
                <p><strong>Farmacia:</strong> '.$row['Farmacia'].'</p>
                <p><strong>Monto Total:</strong> $'.$total_formateado.'</p>
            
                <a href="#" class="btn-ver" onclick="openModal('.$row['ID_Presupuesto'].'); return false;">
                    <i class="fas fa-eye"></i> Ver Presupuesto
                </a>
            </div>';
        }
    } else {
        echo "<p>No tenés pedidos pendientes o aceptados en proceso.</p>";
    }
    ?>
    </div>
</div>  

<div id="detalleModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div id="modal-body-content">
            Cargando detalle...
        </div>
    </div>
</div>
<div id="validarModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <span class="close-btn" onclick="closeValidarModal()">&times;</span>
        <h3>Validar Código de Envío 📦</h3>
        <p>Ingresá el código recibido con tu pedido:</p>
        <input type="text" id="codigoEnvio" placeholder="Codigo de entrega" style="text-transform: uppercase;">
        <br>
        <button id="btnConfirmarValidacion" onclick="confirmarValidacion()">Confirmar Entrega</button>
        <div id="validacionResultado" style="margin-top: 15px; font-weight: bold;"></div>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-text">© 2025 RappiFarma - Todos los derechos reservados.</div>
        <div class="menu-footer-container">
            <button class="btn-validar" onclick="openValidarModal()">
                <img src="validar.png" alt="Validar codigo de envio" class="icono-validar">
            </button>

            <div class="menu-icon-footer" onclick="toggleMenu()">&#9776;</div>
            <nav class="menu-desplegable" id="menuDesplegable">
                <ul>
                    <li onclick="window.location.href='home_usuario.php'"><i class="fas fa-home"></i>Inicio</li>
                    <li onclick="window.location.href='mi_cuenta.php'"><i class="fas fa-box"></i>Mi Cuenta</li>
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

// Funciones de Detalle del Presupuesto (Fetch GET)
function openModal(idPresupuesto) {
    const modal = document.getElementById('detalleModal');
    const modalContent = document.getElementById('modal-body-content');
    modalContent.innerHTML = 'Cargando detalle del presupuesto...';
    modal.style.display = 'block';

    fetch('ver_presupuesto.php?id=' + idPresupuesto) 
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text || 'Error al conectar.'); });
            }
            return response.text();
        })
        .then(html => { modalContent.innerHTML = html; })
        .catch(error => {
            modalContent.innerHTML = '<p style="color: red;">Error al cargar los detalles: ' + error.message + '</p>';
            console.error('AJAX Error:', error);
        });
}

function closeModal() {
    document.getElementById('detalleModal').style.display = 'none';
    document.getElementById('modal-body-content').innerHTML = 'Cargando detalle...';
}

// Funciones del Modal de Validación (Fetch POST JSON)
function openValidarModal() {
    // Abre el modal y limpia campos
    document.getElementById('validarModal').style.display = 'block';
    document.getElementById('codigoEnvio').value = '';
    document.getElementById('validacionResultado').innerHTML = '';
}

function closeValidarModal() {
    document.getElementById('validarModal').style.display = 'none';
}

function confirmarValidacion() {
    const codigo = document.getElementById('codigoEnvio').value.trim();
    const resultadoDiv = document.getElementById('validacionResultado');
    const btn = document.getElementById('btnConfirmarValidacion');

    if (codigo.length === 0) {
        resultadoDiv.innerHTML = '<span style="color: red;">Ingresa un código.</span>';
        return;
    }

    // Deshabilitar botón
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
    resultadoDiv.innerHTML = 'Procesando...';

    // Envío JSON a validar_codigo.php
    fetch('validar_codigo.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ codigo: codigo }) 
    })
    .then(response => {
        // Manejar errores HTTP
        if (!response.ok) {
            return response.json().catch(() => {
                throw new Error(`Error HTTP: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        // Habilitar botón
        btn.disabled = false;
        btn.innerHTML = 'Confirmar Entrega';

        if (data.success) {
            resultadoDiv.innerHTML = '<span style="color: green;">✅ ¡Validación exitosa! Pedido Entregado.</span>';
            // Recargar la página para ver el estado actualizado (Entregado)
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            resultadoDiv.innerHTML = '<span style="color: red;">❌ Error: ' + data.message + '</span>';
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = 'Confirmar Entrega';
        resultadoDiv.innerHTML = '<span style="color: red;">Error de conexión/servidor. Inténtalo de nuevo.</span>';
        console.error('Error AJAX:', error);
    });
}

// Cierra el modal si el usuario hace clic fuera de él
window.onclick = function(event) {
    const modalDetalle = document.getElementById('detalleModal');
    const modalValidar = document.getElementById('validarModal');
    
    if (event.target == modalDetalle) closeModal();
    if (event.target == modalValidar) closeValidarModal();
}
</script>

</body>
</html>


