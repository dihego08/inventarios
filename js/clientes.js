let current = null;
$(document).ready(function () {
    lista_clientes();
    lista_sucursales();
    lista_tipos_clientes();
    $("#id_tipo_cliente").on("change", function () {
        if ($(this).val() == 1) {
            $("#div-tipo-cliente").attr("hidden", false);
        } else {
            $("#div-tipo-cliente").attr("hidden", true);
        }
    });
});
function lista_tipos_clientes() {
    $.post("ws/service.php?parAccion=lista_tipos_clientes", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_tipo_cliente").append(`<option value="${val.id}">${val.tipo_cliente}</option>`);
        });
    });
}
function lista_sucursales() {
    $.post("ws/service.php?parAccion=lista_sucursales", function (response) {
        var obj = JSON.parse(response);
        $("#id_sucursal").append(`<option value="0">--SELECCIONE--</option>`);
        $.each(obj, function (index, val) {
            $("#id_sucursal").append(`<option value="${val.id}">${val.sucursal}</option>`);
        });
    });
}
function lista_clientes() {
    $.post("ws/service.php?parAccion=lista_clientes", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-clientes").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-clientes").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.nombres}</td>
                <td>${val.dni}</td>
                <td>${val.sucursal}</td>
                <td>${val.grado}</td>
                <td>${val.seccion}</td>
                <td>${val.tipo_cliente}</td>
                <td>${val.padre}</td>
                <td>${val.celular}</td>
                <td>${val.saldo}</td>
                <td>
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_cliente(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_cliente(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
    });
}
function actualizar_cliente(id) {
    $.post("ws/service.php?parAccion=actualizar_cliente", {
        nombres: $("#nombres").val(),
        dni: $("#dni").val(),
        id_sucursal: $("#id_sucursal").val(),
        grado: $("#grado").val(),
        seccion: $("#seccion").val(),
        id_tipo_cliente: $("#id_tipo_cliente").val(),
        padre: $("#padre").val(),
        celular: $("#celular").val(),
        saldo: $("#saldo").val(),
        correo: $("#correo").val(),
        id: id,
        fecha_creacion: current.fecha_creacion,
        id_usuario_creacion: current.id_usuario_creacion
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_clientes();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_cliente() {
    $.post("ws/service.php?parAccion=insertar_cliente", {
        nombres: $("#nombres").val(),
        dni: $("#dni").val(),
        id_sucursal: $("#id_sucursal").val(),
        grado: $("#grado").val(),
        seccion: $("#seccion").val(),
        id_tipo_cliente: $("#id_tipo_cliente").val(),
        padre: $("#padre").val(),
        celular: $("#celular").val(),
        saldo: $("#saldo").val(),
        correo: $("#correo").val(),
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_clientes();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_cliente() {
    $("#clienteModalLabel").text("Nuevo Cliente");
    $("#btn-accion-cliente").text("Guardar");
    $("#btn-accion-cliente").attr("onclick", "insertar_cliente();");
}
function editar_cliente(id) {
    $("#clienteModalLabel").text("Editar Cliente");
    $("#btn-accion-cliente").text("Actualizar");
    $("#btn-accion-cliente").attr("onclick", "actualizar_cliente(" + id + ");");
    $.post("ws/service.php?parAccion=editar_cliente", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;

        $("#nombres").val(obj.nombres)
        $("#dni").val(obj.dni)
        $("#id_sucursal").val(obj.id_sucursal)
        $("#grado").val(obj.grado)
        $("#seccion").val(obj.seccion)
        $("#id_tipo_cliente").val(obj.id_tipo_cliente)
        $("#padre").val(obj.padre)
        $("#celular").val(obj.celular)
        $("#saldo").val(obj.saldo)
        $("#correo").val(obj.correo)
    });
}
function limpiar_formulario() {
    $(".form-control").val('');
    $("select").val(0);
}
function eliminar_cliente(id) {
    alertify.confirm('¿Eliminar este Cliente?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_cliente", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se agregó correctamente.");
                lista_clientes();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}