<?php
if (strtolower($_SERVER['REQUEST_URI']) != "/rappifarma/presupuestos.php" && strtolower($_SERVER['REQUEST_URI']) != "/rappifarma/pedidos.php") {
    header("Location: ../");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.19.3/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="styles/main_styles.css">
    <title><?php echo isset($title) ? $title : ""; ?></title>
</head>
<body>
    <nav class="fixed-top navbar navbar-expand-md bg-light border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="icon.png" alt="Icono" width="40" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="pedidos.php"><i class="bi bi-bag-check"></i> Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="presupuestos.php"><i class="bi bi-receipt"></i> Presupuestos</a>
                    </li>
                </ul>
                <div class="d-flex nav-item" role="search">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Cerrar Sesion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>