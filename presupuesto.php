<?php
session_start();
if (!isset($_SESSION['farmacia_id'])) {
    header('Location: index.php');
    exit;
}

$title = "Cargar Presupuesto";
$redirect_url = "index.php";
include "includes/farm_header.php";
include "linkDB.php";

$db_error_msg = "Ocurrio un error inesperado.";
$tomado_error_msg = "El pedido ya ha sido tomado.";
$ret["msg"] = "";

$ID_Farmacia = intval($_SESSION['farmacia_id']);

if (isset($_GET["ID"])) {
    $ID = $_GET["ID"];
    echo '<script>var ID_Farmacia = ' . $ID_Farmacia . '; var ID_Pedido = ' . $ID . ';</script>';
    $conn = getConnection(); //Se obtiene una conexion
    $query = 'SELECT COUNT(presupuestos.Aceptado) AS Cant FROM `presupuestos` WHERE presupuestos.Aceptado = 1 AND ID_Pedido = ?'; //Se trae el pedido correspondiente al ID

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $ID);
    $ret["stat"] = $stmt->execute();
    $pedido = array();

    if ($ret["stat"]) {
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($row["Cant"] == 0) { //Se comprueba que el pedido no haya sido ya aceptado por otra farmacia
            $query = 'SELECT PE.ID_Pedido AS ID, CONCAT(USU.Nombre, " ", USU.Apellido) AS Nombre, USU.Provincia AS Prov, CONCAT(USU.Localidad, " (", USU.CP, ")") AS Loc, USU.Direccion AS Dir, PE.Receta AS Receta FROM pedidos PE INNER JOIN usuarios USU ON PE.ID_Usuario = USU.ID_Usuario WHERE PE.ID_Pedido = ?'; //Se buscan todos los datos asociados al pedido

            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $ID);
            $ret["stat"] = $stmt->execute();

            if ($ret["stat"]) {
                $res = $stmt->get_result();
                $pedido = $res->fetch_assoc();
            } else { //Error en stmt
                $ret["msg"] = $db_error_msg;
            }

            if ($pedido == null) { //Error en consulta (ID inexistente)
                $ret["stat"] = false;
                $ret["msg"] = $db_error_msg;
            }
        } else {//Pedido tomado
            $ret["stat"] = false;
            $ret["msg"] = $tomado_error_msg;
        }
    }
} else { //Error en stmt
    $ret["stat"] = false;
    $ret["msg"] = $db_error_msg;
}

if (!$ret["stat"]) {
    echo'<script type="text/javascript">alert("' . $ret["msg"] . '");window.location.href="' . $redirect_url . '";</script>'; //REDIRIGIR A ARCHIVO LOGIN
}
?>

<main role="main" style="margin-top:65px;margin-bottom:70px;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-xl-5 md-outline m-3 p-2">
                <div class="m-1">
                    <h3>Receta</h3>
                    <hr class="text-secondary">
                    <div class="container-fluid">
                        <?php
                            echo "<b>Cliente:</b> " . $pedido["Nombre"];
                            echo "<br><b>Localidad:</b> " . $pedido["Prov"] . ", " . $pedido["Loc"];
                            echo "<br><b>Direccion:</b> " . $pedido["Dir"];
                            $image = imagecreatefromstring($pedido["Receta"]);
                            ob_start();
                            imagepng($image);
                            imagedestroy($image);
                            $image_data = ob_get_contents();
                            ob_end_clean();
                            $image_data_base64 = base64_encode($image_data);
                            echo '<img class="mb-2" style="width:100%;" src="data:image/png;base64,' . $image_data_base64 . '" />';
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-5 md-outline m-3 p-2">
                <div class="m-1">
                    <h3>Presupuesto</h3>
                    <hr class="text-secondary">
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
                                        <th scope="col">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="pres_tbody"></tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-5">
                                <button id="nuevo-btn" type="button" data-bs-toggle="modal" data-bs-target="#formModal" class="btn btn-outline-secondary"><i class="bi bi-cart-plus"></i> Agregar Producto</button>
                            </div>
                            <div class="col-5 offset-2 d-flex justify-content-end align-items-center">
                                <span>Total: <span id="total"></span>$</span>
                            </div>
                        </div>
                        <form id="form_extra">
                            <div class="row">
                                <div class="col">
                                    <label id="extra-error" class="error" for="extra"></label>
                                </div>
                                <div class="col mb-2"><br></div>
                            </div>
                            <div class="form-floating mb-2">
                                <input class="form-control" placeholder="Costos Extra" type="text" id="extra" name="extra">
                                <label for="extra">Costos Extra</label>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end align-items-center">
                                <button id="conf-btn" type="button" class="btn btn-outline-success"><i class="bi bi-check-square"></i> Confirmar Presupuesto</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="formModalLabel">Producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form">
                    <div class="row">
                        <div class="col-11">
                            <label id="nombre_gen-error" class="error" for="nombre_gen"></label>
                        </div>
                        <div class="col-1"><br></div>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Nombre Generico" type="text" id="nombre_gen" name="nombre_gen">
                        <label for="nombre_gen">Nombre Generico</label>
                    </div>
                    <div class="row">
                        <div class="col-11">
                            <label id="nombre-error" class="error" for="nombre"></label>
                        </div>
                        <div class="col-1 mb-2"><br></div>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Nombre Comercial" type="text" id="nombre" name="nombre">
                        <label for="nombre">Nombre Comercial</label>
                    </div>
                    <div class="row">
                        <div class="col-11">
                            <label id="formato-error" class="error" for="formato"></label>
                        </div>
                        <div class="col-1 mb-2"><br></div>
                    </div>
                    <div class="form-floating">
                        <select class="form-select" id="formato" name="formato">
                            <option value="" selected hidden>Formato</option>
                            <option value="1">comprimidos</option>
                            <option value="2">cápsulas</option>
                            <option value="3">polvo</option>
                            <option value="4">jarabe</option>
                            <option value="5">suspensión</option>
                            <option value="6">solución</option>
                            <option value="7">pomada</option>
                            <option value="8">crema</option>
                            <option value="9">gel</option>
                            <option value="10">inyectable</option>
                            <option value="11">inhalable</option>
                            <option value="12">transdérmica</option>
                        </select>
                        <label for="formato">Formato</label>
                    </div>
                    <div class="row">
                        <div class="col-11">
                            <label id="cant-error" class="error" for="cant"></label>
                        </div>
                        <div class="col-1 mb-2"><br></div>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Cantidad" type="number" id="cant" name="cant" min="1" step="1">
                        <label for="cant" class="form-label">Cantidad</label>
                    </div>
                    <div class="row">
                        <div class="col-11">
                            <label id="precio-error" class="error" for="precio"></label>
                        </div>
                        <div class="col-1"><br></div>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Precio" type="text" id="precio" name="precio">
                        <label for="precio">Precio</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="guardar-btn" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/presupuesto.js"></script>

<?php
include "includes/farm_footer.php";

?>
