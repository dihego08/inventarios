let current = null;
$(document).ready(function () {
    lista_productos();
    lista_categorias();
});
function lista_categorias() {
    $.post("ws/service.php?parAccion=lista_categorias", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_categoria").append(`<option value="${val.id}">${val.categoria}</option>`);
        });
    });
}
function lista_productos() {
    $('#tabla-productos').DataTable().clear().destroy();
    $.post("ws/service.php?parAccion=lista_productos", function (response) {
        var obj = JSON.parse(response);
        $("#tabla-productos").find("tbody").empty();
        $.each(obj, function (index, val) {
            $("#tabla-productos").find("tbody").append(`<tr>
                <td>${val.id}</td>
                <td>${val.producto}</td>
                <td>${val.categoria}</td>
                <td>
                    <span class="btn btn-outline-warning btn-sm d-block mb-1" data-toggle="modal" data-target="#formulario" onclick="editar_producto(${val.id});"><i class="fa fa-edit"></i></span>
                    <span  class="btn btn-outline-danger btn-sm d-block" onclick="eliminar_producto(${val.id});"><i class="fa fa-trash"></i></span>
                </td>
                </tr>`);
        });
        $("#tabla-productos").DataTable({
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
function actualizar_producto(id) {
    $.post("ws/service.php?parAccion=actualizar_producto", {
        producto: $("#producto").val(),
        id_categoria: $("#id_categoria").val(),
        id: id,
        fecha_creacion: current.fecha_creacion,
        id_usuario_creacion: current.id_usuario_creacion
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se modificó correctamente.");
            lista_productos();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function insertar_producto() {
    $.post("ws/service.php?parAccion=insertar_producto", {
        producto: $("#producto").val(),
        id_categoria: $("#id_categoria").val()
    }, function (response) {
        var obj = JSON.parse(response);
        if (obj.Result == "OK") {
            alertify.success("Se agregó correctamente.");
            lista_productos();
            limpiar_formulario();
        } else {
            alertify.error("Algo ha salido terriblemente mal.");
        }
    });
}
function nuevo_producto() {
    $("#productoModalLabel").text("Nuevo Producto");
    $("#btn-accion-producto").text("Guardar");
    $("#btn-accion-producto").attr("onclick", "insertar_producto();");
}
function limpiar_formulario() {
    $(".form-control").val('');
    $("select").val(0);
}
function editar_producto(id) {
    $("#productoModalLabel").text("Editar Producto");
    $("#btn-accion-producto").text("Actualizar");
    $("#btn-accion-producto").attr("onclick", "actualizar_producto(" + id + ");");
    $.post("ws/service.php?parAccion=editar_producto", {
        id: id
    }, function (response) {
        var obj = JSON.parse(response);
        current = obj;
        $("#producto").val(obj.producto);
        $("#id_categoria").val(obj.id_categoria);
    });
}
function eliminar_producto(id) {
    alertify.confirm('¿Eliminar Producto?', 'Esta acción no se puede deshacer', function () {
        $.post("ws/service.php?parAccion=eliminar_producto", {
            id: id
        }, function (response) {
            var obj = JSON.parse(response);
            if (obj.Result == "OK") {
                alertify.success("Se eliminó correctamente.");
                lista_productos();
            } else {
                alertify.error("Algo ha salido terriblemente mal.");
            }
        });
    }, function () {
        alertify.error('Cancel')
    });
}