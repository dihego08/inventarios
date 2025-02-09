let current = null;
$(document).ready(function () {
    lista_gastos();
    lista_sucursales();
});
function lista_sucursales() {
    $.post("ws/service.php?parAccion=lista_sucursales", function (response) {
        var obj = JSON.parse(response);
        $("#id_sucursal").append(`<option value="0">--SELECCIONE--</option>`);
        $.each(obj, function (index, val) {
            $("#id_sucursal").append(`<option value="${val.id}">${val.sucursal}</option>`);
        });
    });
}
function lista_gastos() {
    $.post("ws/service.php?parAccion=lista_gastos", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-gastos").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-gastos").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.concepto}</td>
                <td>${val.monto}</td>
                <td>${val.sucursal}</td>
                <td>${val.fecha_creacion}</td>
                <td>
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_gasto(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_usuario(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
    });
}
function actualizar_gasto(id) {
    $.post("ws/service.php?parAccion=actualizar_gasto", {
        id_sucursal: $("#id_sucursal").val(),
        concepto: $("#concepto").val(),
        monto: $("#monto").val(),
        id: id,
        fecha_creacion: current.fecha_creacion,
        id_usuario_creacion: current.id_usuario_creacion
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_gastos();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_gasto() {
    $.post("ws/service.php?parAccion=insertar_gasto", {
        id_sucursal: $("#id_sucursal").val(),
        concepto: $("#concepto").val(),
        monto: $("#monto").val()
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_gastos();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_gasto() {
    $("#gastoModalLabel").text("Nuevo Gasto");
    $("#btn-accion-gasto").text("Guardar");
    $("#btn-accion-gasto").attr("onclick", "insertar_gasto();");
}
function editar_gasto(id) {
    $("#gastoModalLabel").text("Editar Gasto");
    $("#btn-accion-gasto").text("Actualizar");
    $("#btn-accion-gasto").attr("onclick", "actualizar_gasto(" + id + ");");
    $.post("ws/service.php?parAccion=editar_gasto", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;
        $("#id_sucursal").val(obj.id_sucursal);
        $("#concepto").val(obj.concepto);
        $("#monto").val(obj.monto);
    });
}
function limpiar_formulario() {
    $(".form-control").val('');
    $("select").val(0);
}
function eliminar_usuario(id) {
    alertify.confirm('¿Eliminar este Usuario?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_usuario", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se eliminó correctamente.");
                lista_gastos();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}