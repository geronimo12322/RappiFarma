<?php
// Configuración de la cabecera para devolver una respuesta JSON
header('Content-Type: application/json');

session_start();

// Validar la sesión
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

// Incluye tu conexión a la base de datos
// Asegúrate de que este archivo y la función getConnection() funcionen correctamente
include "linkDB.php"; 
$conn = getConnection();

// Definimos el límite de 64 KB en bytes (constante)
$MAX_SIZE_BYTES = 64 * 1024; 

// Verificación inicial de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivoReceta'])) {
    
    $file = $_FILES['archivoReceta'];
    
    // --- 1. Validaciones de Seguridad en el Servidor ---
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error de subida de archivo: ' . $file['error']]);
        exit;
    }
    
    // Validar el tamaño del archivo (¡Doble chequeo de los 64 KB!)
    if ($file['size'] > $MAX_SIZE_BYTES) {
        // La validación de JavaScript también fallará aquí, pero el servidor es la última línea de defensa.
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Error: la imagen pesa más de los 64 kb.'
        ]);
        exit;
    }
    
    // Validar tipo de archivo (solo imágenes, aunque se guarda como binario)
    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_mime)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo imágenes JPG, PNG o GIF.']);
        exit;
    }

    // --- 2. Inserción BLOB en la tabla PEDIDOS ---

    // 1. Obtener el contenido binario del archivo
    $file_content = file_get_contents($file['tmp_name']);
    $user_id = $_SESSION['user_id'];
    
    // Preparamos el INSERT para la tabla PEDIDOS
    // ID_Usuario (i), Receta (b de BLOB, aunque mysqli lo trata como 's' para string en bind), FechaCreacion
    $sql = "INSERT INTO PEDIDOS (ID_Usuario, Receta, FechaCreacion) VALUES (?, ?, NOW())";
    
    if ($stmt = $conn->prepare($sql)) {
        
        // El tipo de dato para el BLOB se usa 's' (string) en bind_param
        // y se pasa una referencia a NULL.
        $null = NULL;
        $stmt->bind_param("sb", $user_id, $null); 
        
        // Usamos send_long_data para enviar el BLOB de manera eficiente.
        // El '1' es el índice del parámetro 'Receta' (el segundo '?' después de ID_Usuario, que es índice 0).
        $stmt->send_long_data(1, $file_content); 

        if ($stmt->execute()) {
            // Éxito: Registro creado en la DB.
            echo json_encode([
                'success' => true, 
                'message' => 'Carga exitosa. Pedido creado con receta BLOB. ID: ' . $stmt->insert_id
            ]);
        } else {
            // Error en la ejecución SQL
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Error al registrar la receta en la base de datos: ' . $stmt->error
            ]);
        }
        $stmt->close();
        
    } else {
        // Error en la preparación de la consulta
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta SQL.']);
    }

} else {
    // Si no es un método POST o falta el archivo
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida o archivo faltante.']);
}

// Cerramos la conexión
if (isset($conn)) {
    $conn->close();
}
?>