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

if (empty($id_pedido) || empty($id_presupuesto)) {
    header('Location: index.php');
}

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
    header("Location: pago.php?id_pres=$id_presupuesto&id_pedido=$id_pedido");
exit;

}
echo'<script type="text/javascript">
alert("Ocurrio un error.");
window.location.href="index.php";
</script>';
$stmt->close();
$conn->close();
?>