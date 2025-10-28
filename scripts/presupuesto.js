$(document).ready(function() {
    var cant_elems = 0, opcion, fila, resta = 0, total = 0;
    $('#total').text(total);
    var validation = $("#form").validate({
        rules: {
            nombre_gen : {
                required: true,
                maxlength: 50
            },
            nombre: {
                required: true,
                maxlength: 50
            },
            formato: {
                required: true,
                maxlength: 50
            },
            cant: {
                required: true,
                number: true,
                min: 1
            },
            precio: {
                required: true,
                number: true,
                min: 0
            }
        }
    });

    $("#nuevo-btn").click(function() {
        opcion = 1;
        $('#form').trigger("reset");
        validation.resetForm();
    });

    $("#conf-btn").click(function() {
        var data_ajax = new Array();
        $("#pres_tbody tr").each(function(index, elem) {
            var row_data = new Object();
            row_data['nombre_gen'] = $(elem).find("th:eq(0)").text();
            row_data['nombre'] = $(elem).find("td:eq(0)").text();
            row_data['formato'] = $(elem).find("td:eq(1)").text();
            row_data['cantidad'] = $(elem).find("td:eq(2)").text();
            row_data['precio'] = $(elem).find("td:eq(3)").text();
            data_ajax.push(row_data);
        });

        $.ajax({
            async: false,
            url: "pres_cont.php",
            type: "POST",
            datatype:"json",
            data:  {info:data_ajax, opcion:"CARGAR_PRES", ID_Pedido:ID_Pedido, ID_Farmacia:ID_Farmacia},
            success: function(data) {
                var ret = JSON.parse(data);
                if (ret['stat']) {
                    window.alert("Presupuesto cargado correctamente.");
                } else {
                    if (ret['msg'] == "OCCUPIED") {
                        window.alert("El presupuesto ya se encuentra tomado.");
                    } else if (ret['msg'] == "ERROR") {
                        window.alert("Ocurrio un error al cargar el presupuesto.");
                    }
                }
            }
        });
    });

    $('#guardar-btn').click(function() {
        if ($('#form').valid()) {  //Se verifica que el formulario posea valores correctos
            //Se cargan los valores del formulario
            var nombre_gen = $('#nombre_gen').val();
            var nombre = $('#nombre').val();
            var formato = $('#formato').val();
            var cantidad = $('#cant').val();
            var precio = $('#precio').val();

            if (opcion == 1) { //Si es nuevo, se carga una nueva fila
                $('#pres_tbody').append('<tr id="elem-' + cant_elems + '"/>');
                $('#elem-' + cant_elems).append('<th scope="row">' + nombre_gen + '</th>');
                $('#elem-' + cant_elems).append('<td>' + nombre + '</td>');
                $('#elem-' + cant_elems).append('<td>' + formato + '</td>');
                $('#elem-' + cant_elems).append('<td>' + cantidad + '</td>');
                $('#elem-' + cant_elems).append('<td>' + precio + '</td>');
                $('#elem-' + cant_elems).append('<td><div class="btn-group" role="group"><button type="button" data-bs-toggle="modal" data-bs-target="#formModal" class="btn btn-outline-success edit-btn" id="edit-' + cant_elems + '"><i class="bi bi-pencil"></i></button><button type="button" class="btn btn-outline-danger elim-btn" id="del-' + cant_elems + '"><i class="bi bi-eraser"></i></button></div></td>');

                $(".edit-btn").click(function() {
                    opcion = 2;
                    fila = $(this).closest("tr");
                    $('#form').trigger("reset");
                    validation.resetForm();
                    $('#nombre_gen').val(fila.find('th:eq(0)').text());
                    $('#nombre').val(fila.find('td:eq(0)').text());
                    $('#formato').val(fila.find('td:eq(1)').text());
                    $('#cant').val(parseInt(fila.find('td:eq(2)').text()));
                    $('#precio').val(parseFloat(fila.find('td:eq(3)').text()));

                    resta = parseInt(fila.find('td:eq(2)').text()) * parseFloat(fila.find('td:eq(3)').text()); //Se almacena el valor que se debera restar al total
                });
                
                $('#del-' + cant_elems).click(function() {
                    if (confirm("¿Esta seguro que desea eliminar este elemento?")) {
                        fila = $(this).closest("tr");
                        total = total - parseInt(fila.find('td:eq(2)').text()) * parseFloat(fila.find('td:eq(3)').text()); //Se resta el valor del producto
                        $('#total').text(total);
                        fila.remove();
                    }
                });

                cant_elems += 1; //Se agrega un elemento a la suma
            } else if (opcion == 2) { //Si es una edicion, se cargan los nuevos valores a la fila ya existente
                fila.find('th:eq(0)').text(nombre_gen);
                fila.find('td:eq(0)').text(nombre);
                fila.find('td:eq(1)').text(formato);
                fila.find('td:eq(2)').text(cantidad);
                fila.find('td:eq(3)').text(precio);
                total = total - resta; //Se resta el valor almacenado
            }
            total = total + parseInt(cantidad) * parseFloat(precio); //Se suma al total el nuevo producto
            $('#total').text(total);
            $('#formModal').modal('hide');
        }
    });
});