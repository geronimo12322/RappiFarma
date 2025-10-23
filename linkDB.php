<?php
function getConnection(): mysqli {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "databaserappifarma";
    
    $conn = new mysqli($servername, $username, $password, $database);
    
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>