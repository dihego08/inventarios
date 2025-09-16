let current = null;
$(document).ready(function () {
    lista_roles();
});
function lista_roles() {
    $.post("ws/service.php?parAccion=lista_roles", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-roles").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-roles").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.rol}</td>
                <td>
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_rol(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_rol(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
    });
}
function actualizar_rol(id) {
    $.post("ws/service.php?parAccion=actualizar_rol", {
        rol: $("#rol").val(),
        id: id,
        fecha_creacion: current.fecha_creacion,
        id_usuario_creacion: current.id_usuario_creacion
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_roles();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_rol() {
    $.post("ws/service.php?parAccion=insertar_rol", {
        rol: $("#rol").val()
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_roles();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_rol() {
    $("#rolModalLabel").text("Nuevo Rol");
    $("#btn-accion-rol").text("Guardar");
    $("#btn-accion-rol").attr("onclick", "insertar_rol();");
    limpiar_formulario();
}
function limpiar_formulario(){
    $(".form-control").val('');
    $("select").val(0);
}
function editar_rol(id) {
    $("#rolModalLabel").text("Editar Rol");
    $("#btn-accion-rol").text("Actualizar");
    $("#btn-accion-rol").attr("onclick", "actualizar_rol(" + id + ");");
    $.post("ws/service.php?parAccion=editar_rol", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;
        $("#rol").val(obj.rol);
    });
}
function eliminar_rol(id) {
    alertify.confirm('¿Eliminar Rol?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_rol", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se eliminó correctamente.");
                lista_roles();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}