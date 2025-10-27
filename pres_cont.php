<?php
    include "linkDB.php";
    $ID_Farmacia = intval(isset($_POST['ID_Farmacia']) ? $_POST['ID_Farmacia'] : "");
    $ID_Pedido = intval(isset($_POST['ID_Pedido']) ? $_POST['ID_Pedido'] : "");
    $opcion = isset($_POST['opcion']) ? $_POST['opcion'] : "";
    $info = isset($_POST['info']) ? $_POST['info'] : "";

    $ret = array();

    switch ($opcion) {
        case "CARGAR_PRES": {
            if ($info != "" && count($info) > 0) {
                $conn = getConnection(); //Se obtiene una conexion
                $query = 'SELECT COUNT(ID_Presupuesto) AS Cant FROM presupuestos WHERE ID_Pedido = ? AND Aceptado = 1;'; //Se verifica que el usuario no haya aceptado otro presupuesto asociado a ese pedido

                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $ID_Pedido);
                $ret["stat"] = $stmt->execute();

                if ($ret["stat"]) {
                    $res = $stmt->get_result();
                    $fetch_res = $res->fetch_assoc();
                    if ($fetch_res["Cant"] == 0) {
                        $query = 'INSERT INTO presupuestos(`ID_Pedido`, `ID_Farmacia`) VALUES (?, ?)'; //Se inserta primero un presupuesto

                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ii', $ID_Pedido, $ID_Farmacia);
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
                        }
                    } else {
                        $ret["stat"] = false; //El pedido ya se encuentra aceptado
                        $ret["msg"] = "OCCUPIED";
                    }
                }
                $stmt->close();
            }
            break;
        }
    }
    print json_encode($ret, JSON_UNESCAPED_UNICODE);
?>