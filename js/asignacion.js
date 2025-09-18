var codigos_carro = [];
var cantidades = [];
var motivos = [];
let id_usuario_general = 0;
$(document).ready(function () {
  lista_colaboradores();

  lista_motivos();

  $("#guardar_asignacion").on("click", function () {
    if ($("#id_usuario").val() == 0 || $("#id_usuario").val() == "") {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Se debe de seleccionar un cliente!",
      });
    } else if ($("#id_motivo").val() == 0 || $("#id_motivo").val() == "") {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Se debe de seleccionar un Motivo!",
      });
    } else if (codigos_carro.length == 0) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Se debe de añadir al menos un producto!",
      });
    } else {
      let data = [];
      const items = document.querySelectorAll("#div_lista_ventas .item-carrito");

      items.forEach(item => {
        let cantidad = item.querySelector("strong").innerText.trim();
        let producto = item.querySelector("p").innerText.trim();
        let id_producto = item.querySelector(".id_oculto").value;
        let id_motivo = item.querySelector(".id_motivo_oculto").value; // ej: Cambio / Reposición
        let estado = item.querySelector("select").value;
        let observaciones = item.querySelector(".observaciones").value;
        let id_producto_serie = item.querySelector(".id_producto_serie").value;
        let id_almacen = item.querySelector(".id_almacen").value;

        data.push({
          cantidad: parseInt(cantidad),
          producto: producto,
          id_producto: id_producto,
          id_motivo: id_motivo,
          estado: parseInt(estado),
          observaciones: observaciones,
          id_producto_serie: id_producto_serie,
          id_almacen: id_almacen,
        });
      });

      console.log(data);
      //return;
      $.post(
        "ws/service.php?parAccion=guardar_asignacion",
        {
          id_usuario: $("#id_usuario").val(),
          data: data
        },
        function (response) {
          var obj = JSON.parse(response);

          if (obj.Result == "OK") {
            alertify.success("Venta Registrada Correctamente.");
            limpiar_formulario();
          } else {
            if (obj.Message !== undefined) {
              bootbox.alert({
                message: "ERROR: " + obj.Message,
                size: "small",
              });
            } else {
              alertify.error("Algo ha salido terriblemente mal.");
            }
          }
        }
      );
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
          search: request.term,
        },
        success: function (data) {
          response(
            $.map(data, function (item) {
              return {
                label: item.producto + " " + item.marca + " " + item.serie,
                value: item.producto + " " + item.marca + " " + item.serie,
                id: item.id,
                producto: item.producto + " " + item.marca + " " + item.serie,
                id_producto_serie: item.id_producto_serie,
                id_almacen: item.id_almacen,
              };
            })
          );
        },
      });
    },
    select: function (event, ui) {
      $("#add-item").attr(
        "onclick",
        `anadirItem(${ui.item.id}, '${ui.item.producto}', '${ui.item.id_producto_serie}', '${ui.item.id_almacen}')`
      );
      $("#cantidad").trigger("focus");
    },
  });
  $("#cantidad").on("keydown", function () {
    if (event.key === "Enter" || event.keyCode === 13) {
      $("#add-item").click();
    }
  });
});
function limpiar_campos() {
  $("#producto").trigger("focus");
  $("#producto").val("");
  $("#cantidad").val("");
}
function limpiar_formulario() {
  $(".form-control").val("");
  $("select").val(0);
  $(".item-carrito").remove();
  $(".order-empty").show();
  codigos_carro = [];
  cantidades = [];
  motivos = [];
  calcularTodo();
  $("#producto").trigger("focus");
}
function lista_motivos() {
  $.post(
    "ws/service.php?parAccion=lista_motivos",
    {
      id_sucursal: 2,
    },
    function (response) {
      var obj = JSON.parse(response);
      $.each(obj, function (index, val) {
        $("#id_motivo").append(
          `<option value="${val.id}">${val.motivo}</option>`
        );
      });
    }
  );
}
function lista_colaboradores() {
  $.post("ws/service.php?parAccion=lista_colaboradores", function (response) {
    var obj = JSON.parse(response);
    $("#id_usuario").empty();
    $("#id_usuario").append(`<option value="0">--SELECCIONE--</option>`);
    $.each(obj, function (index, val) {
      if (val.dni == "00000000") {
        id_usuario_general = val.id;
      }
      $("#id_usuario").append(
        `<option value="${val.id}">${val.nombres} ${val.apellido_paterno} ${val.apellido_materno}</option>`
      );
    });

    $("#id_usuario").select2();
    console.log(id_usuario_general);
    $("#id_usuario").val(id_usuario_general).trigger("change");
  });
}

function anadirItem(id, nombre, id_producto_serie, id_almacen) {
  console.log($("#producto"));
  console.log(nombre);
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
  if (flag == 0) {
    codigos_carro.push(id);
    cantidades.push($("#cantidad").val());
    motivos.push($("#id_motivo").val());

    $(".order-empty").hide();
    $("#div_lista_ventas").append(`
            <div id="item${id}" class="row item-carrito" style="color: #555555; background: rgba(140,143,183,0.2); border-bottom: solid 1px #313131;">
                <table class="table mb-0">
                    <tr>
                        <td width="5%" class="text-center">
                            <strong id="cant${id}">${$("#cantidad").val()}</strong>
                            <input type="hidden" class="id_oculto" value="${id}"/>
                            <input type="hidden" class="id_producto_serie" value="${id_producto_serie}"/>
                            <input type="hidden" class="id_almacen" value="${id_almacen}"/>
                        </td>
                        <td width="30%">
                            <p style="font-weight: bold; display: block; font-size: 13px; text-align: left;" class="w-100 mb-0">${nombre}</p>
                        </td>
                        <td width="10%">
                            ${$("#id_motivo option:selected").text()}
                            <input type="hidden" class="id_motivo_oculto" value="${$("#id_motivo").val()}"/>
                        </td>
                        <td>
                            <select class="form-control" id="id_estado_${id}">
                                <option value="0">--ESTADO--</option>
                                <option value="1">Nuevo</option>
                                <option value="2">Usado</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control observaciones" id="observacion_${id}" placeholder="Observaciones..." />
                        </td>
                        <td width="3%">
                            <span class="btn btn-danger btn-sm d-block" onclick="quitar_carro(${id})"><i class="fa fa-trash"></i></span>
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
  } else {
    var cant_actual =
      parseInt(cantidades[item_encontrado]) + parseInt($("#cantidad").val());
    //var precio_nuevo = precio * (cant_actual);
    $("#cant" + id).text(cant_actual);
    //$("#precio" + id).text(redondear(precio_nuevo, 2));

    cantidades[item_encontrado] = cant_actual;
    //precios[item_encontrado] = precio;
  }
  calcularTodo();

  limpiar_campos();

  console.log(cantidades);
  //console.log(precios);
  console.log(codigos_carro);
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

  console.log("El total: " + total);
}
function redondear(num, decimales = 2) {
  var signo = num >= 0 ? 1 : -1;
  num = num * signo;
  if (decimales === 0) return signo * Math.round(num);
  num = num.toString().split("e");
  num = Math.round(
    +(num[0] + "e" + (num[1] ? +num[1] + decimales : decimales))
  );
  num = num.toString().split("e");
  return signo * (num[0] + "e" + (num[1] ? +num[1] - decimales : -decimales));
}
function quitar_carro(id, precio) {
  var iterador = 0; //para saber en que posicion esta el articulo y su cantidad
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
    codigos_carro.splice(iterador, 1); //el 1 es cuantas veces hara la funcion
    cantidades.splice(iterador, 1);
  }
  calcularTodo();
}
