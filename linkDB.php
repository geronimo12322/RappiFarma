<?php
class datos {
    var $host = "localhost";
    var $db = "databaserappifarma";
    var $usuario = "root";
    var $contraseña = "";
}

class database {
    function conexion(){
        $datos = new datos();
        $host = $datos->host;
        $db = $datos->db;
        $usuario = $datos->usuario;
        $contraseña = $datos->contraseña;
        try {
            $cnn = new PDO('mysql:host=' . $host . ';dbname=' . $db, $usuario, $contraseña);
            return $cnn;
        } catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}
?>