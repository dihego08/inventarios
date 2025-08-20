let current = null;
$(document).ready(function () {
    lista_almacenes();
    lista_sucursales();
});
function lista_sucursales() {
    $.post("ws/service.php?parAccion=lista_sucursales", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_sucursal").append(`<option value="${val.id}">${val.sucursal}</option>`);
        });
    });
}
function lista_almacenes() {
    $.post("ws/service.php?parAccion=lista_almacenes", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-almacenes").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-almacenes").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.almacen}</td>
                <td>${val.sucursal}</td>
                <td>
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_almacen(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_almacen(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
    });
}
function actualizar_almacen(id) {
    $.post("ws/service.php?parAccion=actualizar_almacen", {
        almacen: $("#almacen").val(),
        id: id, 
        id_sucursal: $("#id_sucursal").val()
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_almacenes();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_almacen() {
    $.post("ws/service.php?parAccion=insertar_almacen", {
        almacen: $("#almacen").val(),
        id_sucursal: $("#id_sucursal").val()
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_almacenes();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_almacen() {
    $("#almacenModalLabel").text("Nuevo Almacén");
    $("#btn-accion-almacen").text("Guardar");
    $("#btn-accion-almacen").attr("onclick", "insertar_almacen();");
    limpiar_formulario();
}
function limpiar_formulario() {
    $(".form-control").val('');
    $("select").val(0);
}
function editar_almacen(id) {
    $("#almacenModalLabel").text("Editar Almacén");
    $("#btn-accion-almacen").text("Actualizar");
    $("#btn-accion-almacen").attr("onclick", "actualizar_almacen(" + id + ");");
    $.post("ws/service.php?parAccion=editar_almacen", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;
        $("#almacen").val(obj.almacen);
        $("#id_sucursal").val(obj.id_sucursal);
    });
}
function eliminar_almacen(id) {
    alertify.confirm('¿Eliminar Almacén?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_almacen", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se eliminó correctamente.");
                lista_almacenes();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}