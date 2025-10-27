<?php
$title = "Cargar Presupuestos";
include "includes/farm_header.php";
include "linkDB.php";

$conn = getConnection(); //Se obtiene una conexion
$query = 'SELECT DISTINCT PE.ID_Pedido, PE.ID_Usuario, PRES.Aceptado, USU.Nombre, USU.Apellido, USU.Provincia, USU.Localidad, USU.CP, USU.Direccion FROM pedidos PE INNER JOIN usuarios USU LEFT JOIN presupuestos PRES ON PE.ID_Pedido = PRES.ID_Pedido AND PRES.Aceptado = 1 WHERE PRES.Aceptado IS NULL;'; //Se buscan todos los pedidos que no tengan un presupuesto aceptado

$stmt = $conn->prepare($query);
$ret["stat"] = $stmt->execute();

        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
if ($ret["stat"]) {
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        var_dump($row);
        echo "<br>";
    }
}
?>

<main role="main" style="margin-top:65px;margin-bottom:70px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="sm-outline m-3 p-2">
                <div class="m-1">
                    <h3>Recetas</h3>
                    <hr class="text-secondary">
                    <div class="container-fluid">
                        asdasd
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include "includes/farm_footer.php";
?>