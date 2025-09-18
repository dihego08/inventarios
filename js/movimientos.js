$(document).ready(function () {
  lista_sucursales();
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

  $(".fecha").datetimepicker({
    format: "Y-m-d",
    timepicker: false
  });
  $.datetimepicker.setLocale('es');
});
function lista_sucursales() {
  $.post("ws/service.php?parAccion=lista_sucursales", function (response) {
    var obj = JSON.parse(response);
    $.each(obj, function (index, val) {
      $("#id_sucursal").append(`<option value="${val.id}">${val.sucursal}</option>`);
    });
  });
}
var colores = ['badge-success', 'badge-danger', 'badge-primary', 'badge-warning'];
function ver_movimientos() {
  $('#tabla-movimientos').DataTable().clear().destroy();
  $.post("ws/service.php?parAccion=ver_movimientos", {
    id_sucursal: $("#id_sucursal").val(),
    fecha_desde: $("#fecha_desde").val(),
    fecha_hasta: $("#fecha_hasta").val(),
  }, function (response) {
    var obj = JSON.parse(response);
    $("#tabla-movimientos").find("tbody").empty();
    $.each(obj, function (index, val) {
      let destino = '';
      if (val.nombres == "" || val.nombres == null) {
        destino = val.razon_social;
      } else if (val.razon_social == "" || val.razon_social == null) {
        destino = val.nombres + " " + val.apellido_paterno + " " + val.apellido_materno;
      }
      $("#tabla-movimientos").find("tbody").append(`<tr>
              <td>${val.id}</td>
              <td>${$.trim(val.producto)}</td>
              <td><span class='badge ${colores[(val.id_tipo_movimiento - 1)]}'>${val.tipo_movimiento}</span></td>
              <td>${val.cantidad}</td>
              <td>${$.trim(destino)}</td>
              <td>${val.precio_unitario || 0}</td>
              <td>${val.fecha}</td>
          </tr>`);
    });
    $("#tabla-movimientos").DataTable({
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
