let current = null;
let id_selected = null;
let tipos_documentos = null;
$(document).ready(function () {
    lista_tipo_documentos().then(() => lista_colaboradores());
    lista_roles();
});
function lista_roles(){
    $.post("ws/service.php?parAccion=lista_roles", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_rol").append(`<option value="${val.id}">${val.rol}</option>`);
        });
    });
}
function lista_tipo_documentos() {
    return new Promise((resolve, reject) => {
        $.post("ws/service.php?parAccion=lista_tipos_documentos_identificacion", function (response) {
            var obj = JSON.parse(response);
            tipos_documentos = obj;
            $.each(obj, function (index, val) {
                $("#id_tipo_documento").append(`<option value="${val.id}">${val.tipo_documento}</option>`);
            });
            resolve(); // ✅ listo, ya terminó
        }).fail(reject);
    });
}
function lista_colaboradores() {
    $('#tabla-colaboradores').DataTable().clear().destroy();
    $.post("ws/service.php?parAccion=lista_colaboradores", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-colaboradores").find("tbody").empty();
        $.each(obj, function (index, val) {
            let tipo_documento = $.grep(tipos_documentos, function (value) {
                return value.id == val.id_tipo_documento;
            });
            $("#tabla-colaboradores").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.nombres}</td>
                <td>${val.apellido_paterno}</td>
                <td>${val.apellido_materno}</td>
                <td>${tipo_documento[0].tipo_documento}</td>
                <td>${val.n_documento}</td>
                <td>${val.email}</td>
                <td>${val.celular}</td>
                <td>${val.fecha_nacimiento}</td>
                <td>${val.rol}</td>
                <td>  
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_colaborador(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_colaborador(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
        $("#tabla-colaboradores").DataTable({
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
function actualizar_colaborador(id) {
    $.post("ws/service.php?parAccion=actualizar_colaborador", {
        id: id,
        nombres: $("#nombres").val(),
        apellido_paterno: $("#apellido_paterno").val(),
        apellido_materno: $("#apellido_materno").val(),
        id_tipo_documento: $("#id_tipo_documento").val(),
        n_documento: $("#n_documento").val(),
        celular: $("#celular").val(),
        email: $("#email").val(),
        fecha_nacimiento: $("#fecha_nacimiento").val(),
        id_rol: $("#id_rol").val(),
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_colaboradores();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_colaborador() {
    $.post("ws/service.php?parAccion=insertar_colaborador", {
        nombres: $("#nombres").val(),
        apellido_paterno: $("#apellido_paterno").val(),
        apellido_materno: $("#apellido_materno").val(),
        id_tipo_documento: $("#id_tipo_documento").val(),
        n_documento: $("#n_documento").val(),
        celular: $("#celular").val(),
        email: $("#email").val(),
        fecha_nacimiento: $("#fecha_nacimiento").val(),
        id_rol: $("#id_rol").val(),
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_colaboradores();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_colaborador() {
    $("#colaboradorModalLabel").text("Nuevo Colaborador");
    $("#btn-accion-colaborador").text("Guardar");
    $("#btn-accion-colaborador").attr("onclick", "insertar_colaborador();");
}
function editar_colaborador(id) {
    $("#colaboradorModalLabel").text("Editar Colaborador");
    $("#btn-accion-colaborador").text("Actualizar");
    $("#btn-accion-colaborador").attr("onclick", "actualizar_colaborador(" + id + ");");
    $.post("ws/service.php?parAccion=editar_colaborador", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;

        $("#nombres").val(obj.nombres);
        $("#apellido_paterno").val(obj.apellido_paterno);
        $("#apellido_materno").val(obj.apellido_materno);
        $("#id_tipo_documento").val(obj.id_tipo_documento);
        $("#n_documento").val(obj.n_documento);
        $("#celular").val(obj.celular);
        $("#email").val(obj.email);
        $("#fecha_nacimiento").val(obj.fecha_nacimiento);
        $("#id_rol").val(obj.id_rol);
    });
}
function limpiar_formulario() {
    $(".form-control").val('');
    $("select").val(0);
}
function eliminar_colaborador(id) {
    alertify.confirm('¿Eliminar este Colaborador?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_colaborador", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se agregó correctamente.");
                lista_colaboradores();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}