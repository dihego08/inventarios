let current = null;
$(document).ready(function () {
    lista_usuarios();
    lista_colaboradores();
});
function lista_colaboradores() {
    $.post("ws/service.php?parAccion=lista_colaboradores", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_colaborador").append(`<option value="${val.id}">${val.nombres} ${val.apellido_paterno} ${val.apellido_materno}</option>`);
        });
    });
}
function lista_usuarios() {
    $.post("ws/service.php?parAccion=lista_usuarios", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-usuarios").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-usuarios").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.colaborador.nombres} ${val.colaborador.apellido_paterno} ${val.colaborador.apellido_materno}</td>
                <td>${val.usuario}</td>
                <td>
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_usuario(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_usuario(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
    });
}
function actualizar_usuario(id) {
    $.post("ws/service.php?parAccion=actualizar_usuario", {
        id_colaborador: $("#id_colaborador").val(),
        usuario: $("#usuario").val(),
        pass: $("#pass").val(),
        id: id,
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_usuarios();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_usuario() {
    $.post("ws/service.php?parAccion=insertar_usuario", {
        id_colaborador: $("#id_colaborador").val(),
        usuario: $("#usuario").val(),
        pass: $("#pass").val(),
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_usuarios();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_usuario() {
    $("#usuarioModalLabel").text("Nuevo Usuario");
    $("#btn-accion-usuario").text("Guardar");
    $("#btn-accion-usuario").attr("onclick", "insertar_usuario();");
}
function editar_usuario(id) {
    $("#usuarioModalLabel").text("Editar Usuario");
    $("#btn-accion-usuario").text("Actualizar");
    $("#btn-accion-usuario").attr("onclick", "actualizar_usuario(" + id + ");");
    $.post("ws/service.php?parAccion=editar_usuario", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;
        $("#id_colaborador").val(obj.id_colaborador);
        $("#usuario").val(obj.usuario);
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
                lista_usuarios();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}