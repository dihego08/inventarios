<?php
class Ventas
{
    public $id;
    public $id_cliente;
    public $id_sucursal;
    public $monto;
    public $id_usuario_creacion;
    public $fecha_creacion;
    public $id_usuario_modificacion;
    public $fecha_modificacion;
    public $id_forma_pago;
    public $estado;
}
class Venta_detalle
{
    public $id;
    public $id_venta;
    public $id_producto;
    public $precio_unitario;
    public $cantidad;
    public $total;
    public $id_usuario_creacion;
    public $fecha_creacion;
    public $id_usuario_modificacion;
    public $fecha_modificacion;
}
