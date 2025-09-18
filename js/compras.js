let current = null;
$(document).ready(function () {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;
    $("#fecha_hasta").val(today);
    today = new Date();
    today.setMonth(today.getMonth() - 1); // Restar 1 mes

    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
    var yyyy = today.getFullYear();
    let last_month = yyyy + '-' + mm + '-' + dd;

    $("#fecha_desde").val(last_month);

    lista_compras();

    $(".fecha").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });
    $.datetimepicker.setLocale('es');
});
function lista_compras() {
    $('#tabla-compras').DataTable().clear().destroy();
    $.post("ws/service.php?parAccion=lista_compras", {
        fecha_desde: $("#fecha_desde").val(),
        fecha_hasta: $("#fecha_hasta").val(),
    }, function (response) {
        var obj = JSON.parse(response);
        $("#tabla-compras").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-compras").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.fecha}</td>
                <td>${$.trim(val.razon_social)}</td>
                <td>S/ ${$.trim(val.subtotal)}</td>
                <td>S/ ${$.trim(val.igv)}</td>
                <td>S/ ${$.trim(val.total)}</td>
                <td>${$.trim(val.tipo_documento)}</td>
                <td>${$.trim(val.forma_pago)}</td>
                <td>
                    <span class="btn btn-outline-info btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="detalle_compra(${val.id});"><i class="fa fa-eye"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_compra(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
            </tr>`);
        });
        $("#tabla-compras").DataTable({
            scrollX: true,       // Habilita el desplazamiento horizontal
            autoWidth: false,    // Evita que DataTables ajuste el ancho automáticamente
            responsive: true,    // Permite que la tabla se adapte
            searching: true,     // Habilita el buscador
            paging: true,        // Habilita paginación
            ordering: true,      // Habilita ordenación
            info: true,
            dom: 'Brftip',
            "language": {
                "url": "./js/Spanish.json"
            },
            buttons: [
                'excel'
            ]
        });
    });
}
function detalle_compra(id) {
    $.post("ws/service.php?parAccion=detalle_compra", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        $("#tabla-detalle-venta").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-detalle-venta").find("tbody").append(`<tr>
                <td>${val.producto}</td>
                <td>${val.cantidad}</td>
                <td>S/ ${val.precio_unitario}</td>
                <td>S/ ${val.precio_unitario * val.cantidad}</td>
            </tr>`);
        });
    });
}
function eliminar_compra(id) {
    alertify.confirm('¿Eliminar Producto?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_compra", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se eliminó correctamente.");
                lista_compras();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}