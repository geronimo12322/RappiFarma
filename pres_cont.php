<?php
    include "linkDB.php";
    $ID_Farmacia = intval(isset($_POST['ID_Farmacia']) ? $_POST['ID_Farmacia'] : "");
    $ID_Pedido = intval(isset($_POST['ID_Pedido']) ? $_POST['ID_Pedido'] : "");
    $opcion = isset($_POST['opcion']) ? $_POST['opcion'] : "";
    $info = isset($_POST['info']) ? $_POST['info'] : "";

    switch ($opcion) {
        case "CARGAR_PRES": {
            if (count($info) > 0) {
                $conn = getConnection(); //Se obtiene una conexion

                $query = 'INSERT INTO presupuestos(`ID_Pedido`, `ID_Farmacia`) VALUES (?, ?)'; //Se inserta primero un presupuesto
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $ID_Pedido, $ID_Farmacia);
                $stmt->execute();

                $query_params = substr(str_repeat("(" . substr(str_repeat("?, ", 6), 0, -2) . "), ", count($info)), 0, -2); //Se arma un sring de la forma (?, ?, ...) para todos los campos del 
                $query = 'INSERT INTO productos(`ID_Presupuesto`, `NombreGenerico`, `NombreComercial`, `Formato`, `Precio`, `Cantidad`) VALUES ' . $query_params . ';';
                $stmt = $conn->prepare($query);
                $params_values = array();

                for ($i = 0; $i < count($info); $i++) {
                    $params_values[$i * (count($info[0]) + 1)] = $conn->insert_id;
                    $info[$i]["cantidad"] = intval($info[$i]["cantidad"]);
                    $info[$i]["precio"] = floatval($info[$i]["precio"]);
                    $params_values = array_merge($params_values, array_values($info[$i]));
                }
                
                $stmt->bind_param(str_repeat('isssid', count($info)), ...$params_values);
                $stmt->execute();
                $stmt->close();
            }
            break;
        }
    }
?>