<?php
if ($_SERVER['REQUEST_URI'] != "/rappifarma/presupuestos.php" && $_SERVER['REQUEST_URI'] != "/rappifarma/pedidos.php") {
    header("Location: ../");
    exit;
}
?>

    <footer class="fixed-bottom d-flex flex-wrap bg-light justify-content-between align-items-center py-3 border-top">
        <div class="col-md-4 px-3 pe-4 d-flex align-items-center">
            <a href="#" class="me-2 mb-sm-0 text-body-secondary text-decoration-none lh-1" aria-label="RappiFarma">
                <img src="icon.png" alt="Icono" width="40" height="40">
            </a>
            <span class="mb-sm-0 text-body-secondary">© RappiFarma</span>
        </div>
        <ul class="fs-3 nav col-md-4 px-3 justify-content-end list-unstyled d-flex">
            <li class="ms-3">
                <a class="text-body-secondary" href="#" aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>
            </li>
            <li class="ms-3">
                <a class="text-body-secondary" href="#" aria-label="Facebook">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </li>
        </ul>
    </footer>
</body>
</html>