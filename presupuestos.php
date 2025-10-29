<?php
$title = "Presupuestos Aceptados";
include "includes/farm_header.php";
include "linkDB.php";

$ret["msg"] = "";

$ID_Farmacia = 1; //OBTENER DE PHP_SESSION

echo '<script>var ID_Farmacia = ' . $ID_Farmacia . ';</script>';

$conn = getConnection(); //Se obtiene una conexion
$query = 'SELECT DISTINCT PRES.ID_Presupuesto AS ID_Pres, PE.ID_Pedido AS ID, PE.ID_Usuario AS ID_USER, CONCAT(USU.Nombre, " ", USU.Apellido) AS Nombre, USU.Provincia AS Prov, CONCAT(USU.Localidad, " (", USU.CP, ")") AS Loc, USU.Direccion AS Dir, (CASE WHEN PRES.Aceptado = 1 THEN PRES.FechaCreacion ELSE "-" END) AS FechaA, (CASE WHEN PRES.Entregado = 1 THEN PRES.FechaEntrega ELSE "-" END) AS FechaE, (CASE WHEN PRES.Codigo IS NOT NULL THEN PRES.Codigo ELSE "-" END) AS Cod, (CASE WHEN PRES.Codigo IS NOT NULL THEN PRES.FechaEnvio ELSE "-" END) AS FechaEn FROM presupuestos PRES INNER JOIN usuarios USU INNER JOIN pedidos PE ON PE.ID_Usuario = USU.ID_Usuario ON PE.ID_Pedido = PRES.ID_Pedido WHERE PRES.ID_Farmacia = ? AND PRES.Aceptado = 1;'; //Se buscan todos los pedidos que tengan presupuesto aceptado

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $ID_Farmacia);
$ret["stat"] = $stmt->execute();
?>

<main role="main" style="margin-top:65px;margin-bottom:70px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="sm-outline m-3 p-2">
                <div class="m-1">
                    <h3>Presupuestos Aceptados</h3>
                    <hr class="text-secondary">
                    <div class="container-fluid">
                        <div style="overflow-y:auto;">
                            <table class="table" id="pres_table">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Provincia</th>
                                        <th scope="col">Localidad</th>
                                        <th scope="col">Direccion</th>
                                        <th scope="col">Fecha de Aceptacion</th>
                                        <th scope="col">Fecha de Envio</th>
                                        <th scope="col">Codigo</th>
                                        <th scope="col">Fecha de Entrega</th>
                                        <th scope="col">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="pres_tbody">
                                    <?php
                                    if ($ret["stat"]) {
                                        $res = $stmt->get_result();
                                        if ($res->num_rows != 0) {
                                            while ($row = $res->fetch_assoc()) {
                                                echo '<tr>';
                                                echo '<th scope="row">' . $row["Nombre"] . "</th>";
                                                echo '<td>' . $row["Prov"] . "</td>";
                                                echo '<td>' . $row["Loc"] . "</td>";
                                                echo '<td>' . $row["Dir"] . "</td>";
                                                echo '<td>' . $row["FechaA"] . "</td>";
                                                echo '<td>' . $row["FechaEn"] . "</td>";
                                                echo '<td>' . $row["Cod"] . "</td>";
                                                echo '<td>' . $row["FechaE"] . "</td>";
                                                echo '<td><div class="btn-group" role="group" id="' . $row["ID_Pres"] . '"><button ' . ($row["Cod"] != "-" ? "disabled" : "") . ' type="button" class="btn btn-outline-success send-btn"><i class="bi bi-send"></i></button><button type="button" class="btn btn-outline-primary look-btn" data-bs-toggle="modal" data-bs-target="#presModal"><i class="bi bi-search"></i></button></div></td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            $ret["stat"] = false;
                                            $ret["msg"] = "No hay presupuestos disponibles.";
                                        }
                                    } else {
                                        $ret["msg"] = "Ocurrio un error.";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php if (!$ret["stat"]) { echo $ret["msg"]; } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="presModal" tabindex="-1" aria-labelledby="presModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="presModalLabel">Presupuesto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div style="overflow-y:auto;">
                        <table class="table" id="pres_table">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre Generico</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Formato</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Precio</th>
                                </tr>
                            </thead>
                            <tbody id="pres_modal_tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/presupuestos.js"></script>

<?php
include "includes/farm_footer.php";
?>