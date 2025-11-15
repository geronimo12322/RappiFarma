<?php
include "linkDB.php";
$conn = getConnection();

session_start();
// Si viene por POST = confirmar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_presupuesto = (int)($_POST['id_pres'] ?? 0);

    if ($id_presupuesto > 0) {

        $sql = "UPDATE presupuestos 
                SET Aceptado = 1, FechaAceptacion = NOW() 
                WHERE ID_Presupuesto = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_presupuesto);

        $stmt->execute();
        header("Location: pedido_usuario.php");    
        }
}



// Si no está logueado, volver al inicio
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Recibe parámetros
$id_presupuesto = (int)($_GET['id_pres'] ?? 0);
$id_pedido = (int)($_GET['id_pedido'] ?? 0);
$total_final = $_GET['monto'] ?? "0.00"; // Este lo mandás al redirigir



//Total de productos
$sql_total_prod = "
    SELECT IFNULL(SUM(Precio * Cantidad), 0) AS total_prod
    FROM PRODUCTOS
    WHERE ID_Presupuesto = ?
";

$stmt = $conn->prepare($sql_total_prod);
$stmt->bind_param("i", $id_presupuesto);
$stmt->execute();
$res_prod = $stmt->get_result()->fetch_assoc();
$stmt->close();

$total_productos = (float)$res_prod['total_prod'];


// extras del presupuesto
$sql_extras = "
    SELECT IFNULL(Extras, 0) AS extras
    FROM PRESUPUESTOS
    WHERE ID_Presupuesto = ?
";
$stmt = $conn->prepare($sql_extras);
$stmt->bind_param("i", $id_presupuesto);
$stmt->execute();
$res_extras = $stmt->get_result()->fetch_assoc();
$stmt->close();

$extras = (float)$res_extras['extras'];


$total_final = $total_productos + $extras;


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <style>
        .pago-container {
            max-width: 450px;
            margin: 60px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            background: white;
        }
        .titulo {
            font-weight: bold;
            font-size: 23px;
        }
        .metodo-box {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            margin-bottom: 12px;
            transition: 0.2s;
        }
        .metodo-box:hover {
            background: #f3f3f3;
        }
        .metodo-box input {
            transform: scale(1.3);
            margin-right: 10px;
        }
        .monto-final {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">

<div class="pago-container">

    <div class="titulo mb-3">Finalizar compra</div>

    <div class="mb-3 text-center">
        <h5>Método de pago</h5>
    </div>

    <label class="metodo-box d-flex align-items-center">
        <input type="radio" name="metodo" value="mercadopago">
        <span>Mercado Pago</span>
    </label>

    <label class="metodo-box d-flex align-items-center">
        <input type="radio" name="metodo" value="modo">
        <span>Modo</span>
    </label>

    <div class="monto-final">
        Total: $<?php echo htmlspecialchars($total_final); ?>
    </div>
    <form method="POST" id="pagoForm">
    <input type="hidden" name="id_pres" value="<?php echo $id_presupuesto; ?>">
    <button type="submit" id="confirmarBtn" class="btn btn-primary w-100 mt-3">
        Confirmar pago
    </button>
</form>


</div>

<script>

document.getElementById("confirmarBtn").addEventListener("click", function() {
    let metodo = document.querySelector('input[name="metodo"]:checked');
    if (!metodo) {
        alert("Seleccioná un método de pago");
        return;
    }

    alert("Pago con " + metodo.value + " confirmado (demo)");
});
</script>

</body>
</html>
























