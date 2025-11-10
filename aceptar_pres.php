<?php
session_start();
// Validación de seguridad: Asegura que el usuario esté logueado y el ID de pedido sea válido
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Asegúrate de que 'linkDB.php' contiene la función getConnection()
include "linkDB.php"; 
$conn = getConnection();
$id_presupuesto = (int)$_GET['id_pres'];
$id_pedido = (int)$_GET['id_pedido'];
$user_id = $_SESSION['user_id'];

// 1. Obtener cantidad de presupuestos aceptados con ese ID de pedido
$sql_presupuesto = "
    SELECT 
        COUNT(PR.Aceptado) AS Cant_PresAceptado
    FROM 
        PRESUPUESTOS PR
    WHERE 
        PR.ID_Pedido = ? AND PR.Aceptado = 1"; 

$stmt = $conn->prepare($sql_presupuesto);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$presupuesto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($presupuesto['Cant_PresAceptado'] == 0) {
    // 1. Update para aceptar el presupuesto
    $sql = "UPDATE presupuestos SET Aceptado = 1, FechaAceptacion = ? WHERE ID_Presupuesto = ?;"; 

    $fecha_act = new DateTime();
    $fecha_str = $fecha_act->format("Y-m-d H:i:s");

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $fecha_str, $id_presupuesto);
    if ($stmt->execute()) {
        echo'<script type="text/javascript">
        alert("Presupuesto aceptado con exito.");
        window.location.href="index.php";
        </script>';
    }
}
echo'<script type="text/javascript">
alert("Ocurrio un error.");
window.location.href="index.php";
</script>';
$stmt->close();
$conn->close();
?>