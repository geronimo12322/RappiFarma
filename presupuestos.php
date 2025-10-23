<?php
$title = "Cargar Presupuestos";
include "includes/farm_header.php";
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-5 outline m-3 p-2">
            <div class="m-1">
                <h3>Pedido</h3>
                <hr class="text-secondary">
                imagen etc
            </div>
        </div>
        <div class="col-lg-12 col-xl-5 outline m-3 p-2">
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
                            <tbody id="pres_tbody">
                                <tr>
                                    <th scope="row">Test</th>
                                    <td>TestTest</td>
                                    <td>format</td>
                                    <td>10</td>
                                    <td>100</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <button type="button" class="btn btn-outline-success"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="btn btn-outline-danger"><i class="bi bi-eraser"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
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
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="formModalLabel">Producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form">
                    <div class="form-floating mb-3">
                        <input class="form-control" placeholder="Nombre Generico" type="text" id="nombre_gen">
                        <label for="nombre_gen">Nombre Generico</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" placeholder="Nombre Comercial" type="text" id="nombre">
                        <label for="nombre">Nombre Comercial</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" id="formato">
                            <option selected hidden>Formato</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                        <label for="formato">Formato</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" placeholder="Cantidad" type="number" id="cant"  min="1" step="1">
                        <label for="cant" class="form-label">Cantidad</label>
                    </div>
                    <div class="form-floating">
                        <input class="form-control" placeholder="Precio" type="text" id="precio">
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

<script src="scripts/presupuestos.js"></script>

<?php
include "includes/farm_footer.php";
?>