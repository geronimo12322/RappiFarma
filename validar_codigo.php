<?php
// validar_codigo.php - Implementación con Transacciones de Base de Datos (CORRECCIÓN FINAL)

header('Content-Type: application/json');
session_start();

$conn = null;

// Función auxiliar para enviar una respuesta JSON y terminar la ejecución
function sendResponse($conn, $success, $message) {
    if (!$success && $conn) {
        // Si hay un error, intentamos hacer un ROLLBACK y cerrar la conexión
        @$conn->rollback(); 
        @$conn->close();
    } elseif ($conn) {
        // Si hay éxito, cerramos la conexión 
        @$conn->close();
    }
    
    // Limpiamos el buffer de salida para asegurar que solo se envíe el JSON
    if (ob_get_level() > 0) {
        ob_clean();
    }
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// 1. Validaciones Iniciales y Lectura de JSON
if (!isset($_SESSION['user_id'])) {
    sendResponse($conn, false, 'Usuario no autenticado.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse($conn, false, 'Método de solicitud no válido.');
}

// Lectura de los datos JSON enviados por JavaScript
$input_json = file_get_contents('php://input');
$data = json_decode($input_json, true);

if ($data === null || !isset($data['codigo'])) {
    sendResponse($conn, false, 'Formato de datos JSON incorrecto o código faltante.');
}

// 2. Conexión a la DB
include "linkDB.php"; 
$conn = getConnection();

if (!$conn) {
    sendResponse(null, false, 'Error de conexión a la base de datos.');
}

// ** INICIO DE LA TRANSACCIÓN **
$conn->autocommit(FALSE);
$user_id = $_SESSION['user_id'];
$codigo_ingresado = strtoupper(trim($data['codigo'])); 

try {
    // 3. Consulta y Verificación del Código
    $sql_check = "
        SELECT 
            PR.ID_Presupuesto 
        FROM 
            PRESUPUESTOS PR
        JOIN 
            PEDIDOS P ON PR.ID_Pedido = PR.ID_Pedido
        WHERE 
            PR.Codigo = ?  /* <--- ¡CORRECCIÓN! Usar PR.Codigo (en tabla PRESUPUESTOS) */
            AND P.ID_Usuario = ? 
            AND PR.Aceptado = 1
            AND PR.Entregado = 0
        FOR UPDATE"; 

    $stmt_check = $conn->prepare($sql_check);
    if (!$stmt_check) throw new Exception("Error al preparar la verificación: " . $conn->error);
    
    $stmt_check->bind_param("si", $codigo_ingresado, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        $stmt_check->close();
        throw new Exception('Código no válido, no corresponde a un pedido activo o ya fue validado.', 404); 
    }

    $presupuesto_data = $result_check->fetch_assoc();
    $id_presupuesto_a_actualizar = $presupuesto_data['ID_Presupuesto'];
    $stmt_check->close();

    // 4. ACTUALIZACIÓN DEL ESTADO DE ENTREGA
    $sql_update = "
        UPDATE 
            PRESUPUESTOS 
        SET 
            Entregado = 1, 
            FechaEntrega = NOW()
        WHERE 
            ID_Presupuesto = ?";

    $stmt_update = $conn->prepare($sql_update);
    if (!$stmt_update) throw new Exception("Error al preparar la actualización: " . $conn->error);
    
    $stmt_update->bind_param("i", $id_presupuesto_a_actualizar);
    
    if (!$stmt_update->execute()) {
        throw new Exception("Error al ejecutar la actualización: " . $stmt_update->error);
    }
    
    $stmt_update->close();

    // ** COMMIT **
    $conn->commit();
    sendResponse($conn, true, 'Entrega confirmada con éxito.');

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    
    if ($e->getCode() === 404) {
        sendResponse($conn, false, $errorMessage);
    } else {
        sendResponse($conn, false, 'Error interno al procesar la solicitud: ' . $errorMessage);
    }
}

if ($conn && $conn->autocommit(TRUE) === FALSE) {
    error_log("No se pudo restablecer autocommit a TRUE.");
}