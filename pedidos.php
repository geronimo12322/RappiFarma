<?php
session_start();
if (!isset($_SESSION['farmacia_id'])) {
    header('Location: index.php');
    exit;
}

$title = "Pedidos";
include "includes/farm_header.php";
include "linkDB.php";

$conn = getConnection(); //Se obtiene una conexion
$query = 'SELECT DISTINCT PE.ID_Pedido AS ID, PE.ID_Usuario AS ID_USER, CONCAT(USU.Nombre, " ", USU.Apellido) AS Nombre, USU.Provincia AS Prov, CONCAT(USU.Localidad, " (", USU.CP, ")") AS Loc, USU.Direccion AS Dir, PE.Receta AS Receta, PE.FechaCreacion AS Fecha FROM pedidos PE INNER JOIN usuarios USU ON PE.ID_Usuario = USU.ID_Usuario LEFT JOIN presupuestos PRES ON PE.ID_Pedido = PRES.ID_Pedido AND PRES.Aceptado = 1 WHERE PRES.Aceptado IS NULL;'; //Se buscan todos los pedidos que no tengan un presupuesto aceptado

$stmt = $conn->prepare($query);
$ret["stat"] = $stmt->execute();
?>

<main role="main" style="margin-top:65px;margin-bottom:70px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="sm-outline m-3 p-2">
                <div class="m-1">
                    <h3>Pedidos Pendientes</h3>
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
                                        <th scope="col">Fecha Realizacion</th>
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
                                                echo '<td>' . $row["Fecha"] . "</td>";
                                                $image = imagecreatefromstring($row["Receta"]);
                                                ob_start();
                                                imagepng($image);
                                                imagedestroy($image);
                                                $image_data = ob_get_contents();
                                                ob_end_clean();
                                                $image_data_base64 = base64_encode($image_data);
                                                echo '<td><div class="btn-group" role="group"><a class="btn btn-outline-success" download="' . $row["Nombre"] . ' (Pedido).png" href="data:image/png;base64, ' . $image_data_base64 . '" title="Descargar receta"><i class="bi bi-download"></i></a><a class="btn btn-outline-primary" href="presupuesto.php?ID=' . $row["ID"] . '" title="Aceptar pedido"><i class="bi bi-clipboard-check"></i></a></div></td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            $ret["stat"] = false;
                                            $ret["msg"] = "No hay pedidos disponibles.";
                                        }
                                    } else {
                                        $ret["msg"] = "Ocurrio un error.";
                                    }
                                    $stmt->close();
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

<script type="text/javascript">
$("#link-pedidos").addClass("disabled");
</script>

<?php
include "includes/farm_footer.php";
?>