$(document).ready(function () {
    lista_sucursales();

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    $("#fecha").val(today);

    $("#fecha").datetimepicker({
        format: "Y-m-d",
        timepicker: false
    });
    $.datetimepicker.setLocale('es');
});
function lista_sucursales() {
    $.post("ws/service.php?parAccion=lista_sucursales", function (response) {
        var obj = JSON.parse(response);
        $("#id_sucursal").append(`<option value="0">--TODOS--</option>`);
        $.each(obj, function (index, val) {
            $("#id_sucursal").append(`<option value="${val.id}">${val.sucursal}</option>`);
        });
    });
}
function reporte_fecha() {
    $.post("ws/service.php?parAccion=reporte_fecha", {
        id_sucursal: $("#id_sucursal").val(),
        fecha: $("#fecha").val()
    }, function (response) {
        var obj = JSON.parse(response);
        $("#ventas_ahora").text("S/ " + obj[0].cant);
        $("#gastos_ahora").text("S/ " + $.trim(obj[1].cant));

        $("#yape_ahora").text("S/ " + $.trim(obj[2].cant));
        $("#plin_ahora").text("S/ " + $.trim(obj[3].cant));
        $("#efectivo_ahora").text("S/ " + $.trim(obj[4].cant));
        $("#saldo_ahora").text("S/ " + $.trim(obj[5].cant));
    });
}