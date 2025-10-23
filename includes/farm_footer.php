<?php
if ($_SERVER['REQUEST_URI'] != "/rappifarma/presupuestos.php" && $_SERVER['REQUEST_URI'] != "/rappifarma/pedidos.php") {
    header("Location: ../");
    exit;
}
?>

</body>
</html>