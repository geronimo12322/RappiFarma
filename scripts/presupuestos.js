$(document).ready(function() {
    $(".send-btn").click(function() {
        if (confirm("¿Esta seguro que desea marcar como enviado este producto? Esto generara un codigo de entrega de manera automatica.")) {
            var ID_Presupuesto = $(this).attr('id'), HTML_elem = this;
            $.ajax({
                async: false,
                url: "pres_cont.php",
                type: "POST",
                datatype:"json",
                data:  {opcion:"ENVIAR_PRES", ID_Presupuesto:ID_Presupuesto, ID_Farmacia:ID_Farmacia},
                success: function(data) {
                    var ret = JSON.parse(data);
                    if (ret["stat"]) {
                        window.alert("Codigo generado exitosamente: " + ret["cod"] + ".");
                        $(HTML_elem).prop("disabled", true);
                        fila = $(HTML_elem).closest("tr");
                        fila.find('td:eq(4)').text(ext_data["fecha"]);
                        fila.find('td:eq(5)').text(ext_data["cod"]);
                    } else {
                        if (ret["msg"] == "ERROR") {
                            window.alert("Ocurrio un error.");
                        }
                    }
                }
            });
        }
    });
});