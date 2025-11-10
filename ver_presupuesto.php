<?php
// ver_presupuesto.php

session_start();
// Validación de seguridad y existencia de ID
if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(403);
    echo "Acceso denegado o ID de presupuesto no válido.";
    exit;
}

// Asegúrate de que 'linkDB.php' contiene la función getConnection()
include "linkDB.php"; 
$conn = getConnection();
$id_presupuesto = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// 1. Obtener detalles del Presupuesto, Farmacia y la Receta del Pedido
$sql_presupuesto = "
    SELECT 
        PR.ID_Pedido,
        PR.ID_Presupuesto, 
        PR.FechaCreacion, 
        PR.Extras, 
        PR.Aceptado, 
        PR.Entregado,
        F.RazonSocial AS Farmacia,
        P.ID_Pedido,
        P.Receta     /* Campo binario de la Receta */
    FROM 
        PRESUPUESTOS PR
    JOIN 
        FARMACIAS F ON PR.ID_Farmacia = F.ID_Farmacia
    JOIN 
        PEDIDOS P ON PR.ID_Pedido = P.ID_Pedido
    WHERE 
        PR.ID_Presupuesto = ?"; 

$stmt_p = $conn->prepare($sql_presupuesto);
$stmt_p->bind_param("i", $id_presupuesto);
$stmt_p->execute();
$presupuesto = $stmt_p->get_result()->fetch_assoc();
$stmt_p->close();

if (!$presupuesto) {
    http_response_code(404);
    echo "Presupuesto no encontrado o no está asociado a tu cuenta.";
    exit;
}

// Determinamos si la receta está adjunta (el BLOB no es vacío)
$receta_adjunta = !empty($presupuesto['Receta']);

// 2. Obtener los Productos del Presupuesto
$sql_productos = "
    SELECT 
        NombreComercial, 
        NombreGenerico, 
        Formato, 
        Precio, 
        Cantidad
    FROM 
        PRODUCTOS
    WHERE 
        ID_Presupuesto = ?";

$stmt_prod = $conn->prepare($sql_productos);
$stmt_prod->bind_param("i", $id_presupuesto);
$stmt_prod->execute();
$productos_result = $stmt_prod->get_result();
$stmt_prod->close();
$conn->close();

// ------------------------------------------
// Generación del HTML para el Modal
// ------------------------------------------

$html = '
    <h3 style="color: #e85d00; border-bottom: 2px solid #eee; padding-bottom: 10px;">Detalle del Presupuesto N° ' . $presupuesto['ID_Presupuesto'] . '</h3>
    <p><strong>Farmacia Emisora:</strong> ' . htmlspecialchars($presupuesto['Farmacia']) . '</p>
    <p><strong>Pedido Relacionado:</strong> #' . $presupuesto['ID_Pedido'] . '</p>
    <p><strong>Fecha de Emisión:</strong> ' . $presupuesto['FechaCreacion'] . '</p>';

if ($presupuesto['Aceptado']) {
    $html .= '
        <div style="margin-top: 15px; padding: 10px; background-color: #f7f7f7; border-left: 5px solid #00a8e8; display: flex; align-items: center; justify-content: space-between;">
            <strong>Receta:</strong> ';
            
    // Lógica para mostrar el botón "Ver Receta"
    if ($receta_adjunta) {
        $html .= '
            <span>✅ Receta Adjunta.</span>
            <a href="mostrar_receta.php?id=' . $presupuesto['ID_Pedido'] . '" target="_blank" style="background-color: #4caf50; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background 0.3s;">
                <i class="fas fa-image"></i> Ver Receta
            </a>';
    } else {
        $html .= '<span style="color: #e85d00; font-weight: bold;">❌ Receta No Adjunta</span>';
    }
    $html .= '</div>';
}
// FIN DEL BLOQUE DE RECETA

$html .= '
    <h4 style="color: #00a8e8; margin-top: 20px;">Productos Incluidos:</h4>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9em;">
        <thead>
            <tr style="background-color: #f1f1f1;">
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Nombre Comercial</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Formato / Genérico</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Cant.</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">P. Unitario</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Subtotal</th>
            </tr>
        </thead>
        <tbody>';

$total_productos = 0;
while($prod = $productos_result->fetch_assoc()) {
    $subtotal = $prod['Precio'] * $prod['Cantidad'];
    $total_productos += $subtotal;
    
    $html .= '
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($prod['NombreComercial']) . '</td>
                <td style="padding: 10px; border: 1px solid #ddd; font-size: 0.9em;">' . htmlspecialchars($prod['Formato'] . ' / ' . $prod['NombreGenerico']) . '</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">' . $prod['Cantidad'] . '</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">$' . number_format($prod['Precio'], 2, ',', '.') . '</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">$' . number_format($subtotal, 2, ',', '.') . '</td>
            </tr>';
}

$total_final = $total_productos + $presupuesto['Extras'];

$html .= '
        </tbody>
    </table>
    
    <div style="text-align: right; font-size: 1.1em; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 15px;">
        <p>Subtotal Productos: <strong>$' . number_format($total_productos, 2, ',', '.') . '</strong></p>
        <p style="color: #555;">Cargos Extras (Envío, etc.): <strong>$' . number_format($presupuesto['Extras'], 2, ',', '.') . '</strong></p>
        <p style="font-size: 1.3em; font-weight: bold; color: #000;">TOTAL FINAL: <span style="color: #e85d00;">$' . number_format($total_final, 2, ',', '.') . '</span></p>
    </div>
    
    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 25px; padding: 10px; background-color: '.($presupuesto['Aceptado'] ? '#f0fff0' : '#fff0f0').'; border: 1px solid '.($presupuesto['Aceptado'] ? '#4caf50' : '#af4c50').'">
        <strong>Estado del Presupuesto:</strong> ' . ($presupuesto['Aceptado'] ? '✅ Aceptado con éxito.' : '❌ Sin aceptar') .  
        (!$presupuesto['Aceptado'] ? '<a href="aceptar_pres.php?id_pres=' . $presupuesto['ID_Presupuesto'] . '&id_pedido=' . $presupuesto['ID_Pedido'] . '" style="background-color: #4caf50; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background 0.3s;">
            <i class="fas fa-image"></i> Aceptar Presupuesto
        </a>' : '') .
        '</div>';

echo $html;

?>