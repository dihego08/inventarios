var codigos_carro = [];
var cantidades = [];
var precios = [];
var almacenes = [];
var series_carro = [];
let id_cliente_general = 0;
$(document).ready(function () {
    $(".fecha").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });
    $.datetimepicker.setLocale('es');
    getTodayDate();
    lista_proveedores();
    lista_almacenes();
    lista_tipos_documentos();
    lista_formas_pagos();

    $("#guardar_compra").on("click", function () {
        if ($("#id_cliente").val() == 0 || $("#id_cliente").val() == "") {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Se debe de seleccionar un cliente!",
            });
        } else if ($("#id_forma_pago").val() == 0 || $("#id_forma_pago").val() == "") {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Se debe de seleccionar una Forma de Pago!",
            });
        } else if (codigos_carro.length == 0) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Se debe de añadir al menos un producto!",
            });
        } else {
            let carrito = generarCarritoJSON();
            $.post("ws/service.php?parAccion=guardar_compra", {
                /*codigos_carro: codigos_carro,
                cantidades: cantidades,
                precios: precios,
                almacenes: almacenes,
                series_carro: series_carro,*/
                carrito: carrito,
                id_proveedor: $("#id_proveedor").val(),
                id_forma_pago: $("#id_forma_pago").val(),
                id_tipo_documento: $("#id_tipo_documento").val(),
                total: $("#total").val(),
                subtotal: $("#subtotal").val(),
                igv: $("#igv").val(),
                fecha: $("#fecha").val(),
            }, function (response) {
                var obj = JSON.parse(response);

                if (obj.Result == "OK") {
                    alertify.success("Venta Registrada Correctamente.");
                    limpiar_formulario();
                } else {
                    if (obj.Message !== undefined) {
                        bootbox.alert({
                            message: "ERROR: " + obj.Message,
                            size: 'small'
                        });
                    } else {
                        alertify.error("Algo ha salido terriblemente mal.");
                    }
                }
            });
        }
    });
    limpiar_campos();
    $("#producto").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "ws/service.php?parAccion=autocomplete",
                type: "GET",
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.producto + " " + item.marca + " " + item.modelo,
                            value: item.producto + " " + item.marca + " " + item.modelo,
                            id: item.id,
                            producto: item.producto + " " + item.marca + " " + item.modelo,
                            precio_unitario: parseFloat(item.precio_unitario).toFixed(2)
                        }
                    }))
                }
            });
        },
        select: function (event, ui) {
            $("#add-item").attr("onclick", `anadirItem(${ui.item.id}, '${ui.item.producto}', '${ui.item.precio_unitario}')`);
            $("#cantidad").trigger("focus");
        }
    });
    $("#cantidad").on("keydown", function () {
        if (event.key === "Enter" || event.keyCode === 13) {
            $("#add-item").click();
        }
    });
    $("#id_almacen").on("change", function () {
        let tipo = $("#id_almacen option:selected").data("id");
        if (tipo == 1) {
            $("#div-compras").empty();
            for (let index = 0; index < $("#cantidad").val(); index++) {
                $("#div-compras").append(`<input type="text" class="form-control mb-1" placeholder="Serie..." />`);
            }
        } else {
            $("#div-compras").empty();
        }
    });
});
function limpiar_campos() {
    $("#producto").trigger("focus");
    $("#producto").val("");
    $("#cantidad").val("");
    $("#precio").val("");
    $("#id_almacen").val(0);
    $("#id_almacen").change();
}
function limpiar_formulario() {
    $(".form-control:not(#fecha)").val('');
    $("select:not(#id_almacen)").val(0);
    $('.item-carrito').remove();
    $(".order-empty").show();
    codigos_carro = [];
    cantidades = [];
    precios = [];
    almacenes = [];
    series_carro = [];
    calcularTodo();
    $("#producto").trigger("focus");
}
function lista_formas_pagos() {
    $.post("ws/service.php?parAccion=lista_formas_pagos", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_forma_pago").append(`<option value="${val.id}">${val.forma_pago}</option>`);
        });
    });
}
function lista_tipos_documentos() {
    $.post("ws/service.php?parAccion=lista_tipos_documentos", function (response) {
        var obj = JSON.parse(response);
        $.each(obj, function (index, val) {
            $("#id_tipo_documento").append(`<option value="${val.id}">${val.tipo_documento}</option>`);
        });
    });
}
function lista_proveedores() {
    $.post("ws/service.php?parAccion=lista_proveedores", function (response) {
        var obj = JSON.parse(response);
        $("#id_proveedor").empty();
        $("#id_proveedor").append(`<option value="0">--SELECCIONE--</option>`);
        $.each(obj, function (index, val) {
            $("#id_proveedor").append(`<option value="${val.id}">${val.n_documento} - ${val.razon_social}</option>`);
        });

        $("#id_proveedor").select2();
    });
}
function lista_almacenes() {
    $.post("ws/service.php?parAccion=lista_almacenes", function (response) {
        var obj = JSON.parse(response);
        $("#id_almacen").empty();
        $("#id_almacen").append(`<option value="0">--SELECCIONE--</option>`);
        $.each(obj, function (index, val) {
            $("#id_almacen").append(`<option value="${val.id}" data-id="${val.tipo}">${val.almacen}</option>`);
        });
    });
}
function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // meses van de 0 a 11
    const day = String(today.getDate()).padStart(2, '0');
    //return `${year}-${month}-${day}`;
    $("#fecha").val(`${year}-${month}-${day}`);
}
function generarCarritoJSON() {
    let carrito = [];

    $("#div_lista_ventas .item-carrito").each(function () {
        let item = {};
        item.id_producto = $(this).attr("id").replace("item", "");
        item.cantidad = $(this).find("strong[id^=cant]").text();
        item.precio_unitario = $(this).find("span.badge-primary").text().replace("S/ ", "");
        item.almacen = $(this).find("input.id_almacen_oculto").val();

        // 👇 aquí capturas las series si existen
        let seriesText = $(this).find("p.series").last().text();
        let series = seriesText ? seriesText.split(",").map(s => s.trim()) : [];
        item.series = series;

        carrito.push(item);
    });

    return carrito;
}
function anadirItem(id, nombre, precio) {
    precio = parseFloat($("#precio").val()).toFixed(2);
    var flag = 0;
    var iterador = 0;
    var item_encontrado = 0;
    for (var i = 0; i < codigos_carro.length; i++) {
        if (id == codigos_carro[i]) {
            flag = 1;
            iterador = i;
            item_encontrado = i;
        }
    }
    let total_item = precio * $("#cantidad").val();
    if (flag == 0) {
        codigos_carro.push(id);
        cantidades.push($("#cantidad").val());
        precios.push(precio);
        almacenes.push($("#id_almacen").val());
        let series = [];
        $("#div-compras input").each(function () {
            let serie = $(this).val().trim();
            if (serie) {
                series.push(serie);
            }
        });
        series_carro.push(series); // guardamos las series (array paralelo)
        $(".order-empty").hide();
        let series_html = "";
        if (series.length > 0) {
            series_html = `<p class="my-0 series" style="font-size: font-size: 13px;">${series.join(", ")}</p>`;
        }
        $("#div_lista_ventas").append(`
            <div id="item${id}" class="row item-carrito" style="color: #555555; background: rgba(140,143,183,0.2); border-bottom: solid 1px #313131;">
                <table class="table mb-0">
                    <tr>
                        <td width="10%" class="text-center">
                            <strong id="cant${id}">${$("#cantidad").val()}</strong>
                        </td>
                        <td width="60%">
                            <p style="font-weight: bold; display: block; font-size: 13px; text-align: left;" class="w-100 mb-0">${nombre}</p>
                            <p class="w-100 my-0" style="font-size: 16px; text-align: left;"> <span class="badge badge-primary">S/ ${precio}</span> - <span class="badge badge-dark">${$("#id_almacen option:selected").text()}</span></p>
                            <input type="hidden" class="id_almacen_oculto" value="${$("#id_almacen").val()}">
                            ${series_html}
                        </td>
                        <td width="20%">
                            <strong>S/ </strong><strong id="precio${id}">${total_item}</strong>
                        </td>
                        <td width="10%">
                            <span class="btn btn-danger btn-sm d-block" onclick="quitar_carro(${id},${total_item})"><i class="fa fa-trash"></i></span>
                        </td>
                    </tr>
                </table>
            </div>
        `);

        $("#div_engloba").empty();
        $("#div_engloba").append(`
            <div class="row">
                <div class="col-6">
                </div>
                <div class="col-6 text-center" style="border-top: solid 2px #313131;">
                    <p>Total: S/ <strong id="subtotal"></strong> </p>
                </div>
            </div>
        `);
    }
    else {
        var cant_actual = parseInt(cantidades[item_encontrado]) + parseInt($("#cantidad").val());
        var precio_nuevo = precio * (cant_actual);
        $("#cant" + id).text(cant_actual);
        $("#precio" + id).text(redondear(precio_nuevo, 2));

        cantidades[item_encontrado] = cant_actual;
        precios[item_encontrado] = precio;
        //almacenes[]
    }
    calcularTodo();

    limpiar_campos();
}
function calcularTodo() {
    var total = 0;
    for (var i = 0; i < codigos_carro.length; i++) {
        total += parseFloat($("#precio" + codigos_carro[i]).html() || 0);
    }
    total = redondear(total, 2);
    var sub_total = redondear(total / 1.18);
    var igv = redondear(total - sub_total, 2);
    $("#total").val(total);
    $("#total_detalle_span").text("S/ " + total);

    $("#subtotal").val(sub_total);
    $("#subtotal_detalle_span").text("S/ " + sub_total);

    $("#igv").val(igv);
    $("#igv_detalle_span").text("S/ " + igv);

}
function redondear(num, decimales = 2) {
    var signo = (num >= 0 ? 1 : -1);
    num = num * signo;
    if (decimales === 0)
        return signo * Math.round(num);
    num = num.toString().split("e");
    num = Math.round(+(num[0] + "e" + (num[1] ? (+num[1] + decimales) : decimales)));
    num = num.toString().split("e");
    return signo * (num[0] + "e" + (num[1] ? (+num[1] - decimales) : -decimales));
}
function quitar_carro(id, precio) {
    var iterador = 0;//para saber en que posicion esta el articulo y su cantidad
    for (var i = 0; i < codigos_carro.length; i++) {
        if (id == codigos_carro[i]) {
            iterador = i;
        }
    }
    var precio_a_dos = redondear(precio, 2);
    var cantidad_anterior = parseInt($("#cant" + id).val());
    var precio_anterior = parseFloat($("#precio" + id).html());
    if (cantidad_anterior > 1) {
        var nueva_cantidad = cantidad_anterior - 1;
        var nuevo_precio = precio_anterior - precio_a_dos;
        $("#cant" + id).val(nueva_cantidad);
        $("#precio" + id).html(redondear(nuevo_precio, 2));
        cantidades[iterador]--;
    } else {
        console.log("para borrar pues");
        //borrar tanto de tabla como de sus arreglos sabiendo su posicion
        $("#item" + id).remove();
        //codigos_carro.pop(id);
        codigos_carro.splice(iterador, 1);//el 1 es cuantas veces hara la funcion 
        cantidades.splice(iterador, 1);
    }
    calcularTodo();
}