<?php
session_start();
if (!isset($_SESSION['farmacia_id'])) {
    header('Location: index.php');
    exit;
}

include "linkDB.php";
$ID_Farmacia = intval(isset($_POST['ID_Farmacia']) ? $_POST['ID_Farmacia'] : "");
$ID_Pedido = intval(isset($_POST['ID_Pedido']) ? $_POST['ID_Pedido'] : "");
$ID_Presupuesto = intval(isset($_POST['ID_Presupuesto']) ? $_POST['ID_Presupuesto'] : "");
$opcion = isset($_POST['opcion']) ? $_POST['opcion'] : "";
$info = isset($_POST['info']) ? $_POST['info'] : "";
$extra = floatval(isset($_POST['extra']) ? $_POST['extra'] : "");

$ret = array();

switch ($opcion) {
    case "CARGAR_PRES": {
        if (!empty($info) && count($info) > 0 && !empty($ID_Pedido) && !empty($ID_Farmacia)) {
            $conn = getConnection(); //Se obtiene una conexion
            $query = 'SELECT COUNT(ID_Presupuesto) AS Cant FROM presupuestos WHERE ID_Pedido = ? AND Aceptado = 1;'; //Se verifica que el usuario no haya aceptado otro presupuesto asociado a ese pedido

            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $ID_Pedido);
            $ret["stat"] = $stmt->execute();

            if ($ret["stat"]) {
                $res = $stmt->get_result();
                $fetch_res = $res->fetch_assoc();
                if ($fetch_res["Cant"] == 0) {
                    $query = 'INSERT INTO presupuestos(`ID_Pedido`, `ID_Farmacia`, `Extras`) VALUES (?, ?, ?);'; //Se inserta primero un presupuesto

                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iid', $ID_Pedido, $ID_Farmacia, $extra);
                    $ret["stat"] = $stmt->execute();

                    if ($ret["stat"]) {
                        $last_insert = $conn->insert_id;
                        $query_params = substr(str_repeat("(" . substr(str_repeat("?, ", 6), 0, -2) . "), ", count($info)), 0, -2); //Se arma un string de la forma (?, ?, ...), (?, ?, ...), ... para todos los campos a insertar en productos, y se multiplica la cantidad de veces que sea necesario para todos los productos
                        $query = 'INSERT INTO productos(`ID_Presupuesto`, `NombreGenerico`, `NombreComercial`, `Formato`, `Precio`, `Cantidad`) VALUES ' . $query_params . ';';

                        $stmt = $conn->prepare($query);

                        $params_values = array();
                        for ($i = 0; $i < count($info); $i++) { //Se crea un arreglo con todos los valores a cargar
                            $params_values[$i * (count($info[0]) + 1)] = $last_insert; //Se carga antes que el resto de valores el ultimo ID insertado, que corresponde al presupuesto que se acaba de crear
                            $info[$i]["cantidad"] = intval($info[$i]["cantidad"]); //Se hacen las conversiones necesarias
                            $info[$i]["precio"] = floatval($info[$i]["precio"]);
                            $params_values = array_merge($params_values, array_values($info[$i])); //Se concatenan todos los valores a cargar
                        }
                        
                        $stmt->bind_param(str_repeat('isssid', count($info)), ...$params_values); //Se bindea de acuerdo a la cantidad de parametros a insertar
                        $ret["stat"] = $stmt->execute();

                        if (!$ret["stat"]) { //Error de stmt
                            $ret["msg"] = "ERROR";
                        }
                    } else { //Error de stmt
                        $ret["msg"] = "ERROR";
                    }
                } else {
                    $ret["stat"] = false; //El pedido ya se encuentra aceptado
                    $ret["msg"] = "OCCUPIED";
                }
            } else { //Error de stmt
                $ret["msg"] = "ERROR";
            }
            $stmt->close();
        } else { //Error en los datos enviados
            $ret["stat"] = false;
            $ret["msg"] = "ERROR";
        }
        break;
    }
    case "ENVIAR_PRES": {
        if (!empty($ID_Presupuesto)) {
            $conn = getConnection(); //Se obtiene una conexion
            $query = 'SELECT (CASE WHEN Codigo IS NULL THEN 0 ELSE 1 END) AS Ent FROM presupuestos WHERE ID_Presupuesto = ?;'; //Se verifica que el pedido no este ya marcado como entregado

            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $ID_Presupuesto);
            $ret["stat"] = $stmt->execute();

            if ($ret["stat"]) {
                $res = $stmt->get_result();
                $fetch_res = $res->fetch_assoc();
                if (!$fetch_res["Ent"]) {
                    $query = 'UPDATE presupuestos SET FechaEnvio = ?, Codigo = ? WHERE ID_Presupuesto = ?;'; //Se actualiza la fecha de envio y el codigo

                    $fecha_act = new DateTime();
                    $fecha_str = $fecha_act->format("Y-m-d H:i:s");
                    $cod = "";
                    for ($i = 0; $i < 6; $i++) {
                        $cod .= rand(0, 9);
                    }

                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('ssi', $fecha_str, $cod, $ID_Presupuesto);
                    $ret["stat"] = $stmt->execute();

                    if ($ret["stat"]) { //Se devuelve la fecha y el codigo para que se carguen en la tabla
                        $ret["fecha"] = $fecha_str;
                        $ret["cod"] = $cod;
                    } else { //Error de stmt
                        $ret["msg"] = "ERROR";
                    }
                } else {
                    $ret["stat"] = false; //El pedido ya se encuentra aceptado
                    $ret["msg"] = "ERROR";
                }
            } else { //Error de stmt
                $ret["msg"] = "ERROR";
            }
            $stmt->close();
        } else { //Error en los datos enviados
            $ret["stat"] = false;
            $ret["msg"] = "ERROR";
        }
        break;
    }
    case "BUSCAR_PRES": {
        if (!empty($ID_Presupuesto)) {
            $conn = getConnection(); //Se obtiene una conexion
            $query = 'SELECT NombreGenerico, NombreComercial, Formato, Cantidad, Precio FROM productos WHERE ID_Presupuesto = ?;'; //Se verifica que el pedido no este ya marcado como entregado

            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $ID_Presupuesto);
            $ret["stat"] = $stmt->execute();

            if ($ret["stat"]) {
                $res = $stmt->get_result();
                $ret["data"] = $res->fetch_all(MYSQLI_ASSOC);
            } else {
                $ret["msg"] = "ERROR";
            }
        } else { //Error en los datos enviados
            $ret["stat"] = false;
            $ret["msg"] = "ERROR";
        }
        break;
    }
}
print json_encode($ret, JSON_UNESCAPED_UNICODE);
?>