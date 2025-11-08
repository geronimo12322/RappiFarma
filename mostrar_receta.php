<?php
// mostrar_receta.php - Script para servir la imagen BLOB de la Receta.

session_start();
// Validación de seguridad: Asegura que el usuario esté logueado y el ID de pedido sea válido
if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(403);
    exit;
}

// Asegúrate de que 'linkDB.php' contiene la función getConnection()
include "linkDB.php"; 
$conn = getConnection();
$id_pedido = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Consultar la receta y verificar la propiedad del pedido.
$sql = "
    SELECT 
        P.Receta 
    FROM 
        PEDIDOS P
    WHERE 
        P.ID_Pedido = ? AND P.ID_Usuario = ?"; 

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_pedido, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$data || empty($data['Receta'])) {
    // Si no se encuentra la receta o no pertenece al usuario, muestra un error.
    http_response_code(404);
    echo "Receta no encontrada o no autorizada.";
    exit;
}

// ------------------------------------------
// Servir el Contenido Binario (La Imagen)
// ------------------------------------------

$receta_blob = $data['Receta'];

// Asumimos tipo MIME para la imagen. Ajusta si tu campo almacena otro formato (PNG, PDF).
$mime_type = 'image/jpeg'; 

// 1. Limpiar cualquier output previo y establecer los headers
ob_clean(); // Limpia el buffer de salida
header('Content-Type: ' . $mime_type);
header('Content-Length: ' . strlen($receta_blob));

// 2. Imprimir el contenido binario
echo $receta_blob;

exit;
?>