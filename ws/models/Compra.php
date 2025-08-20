<?php
class Compra
{
    public $id;
    public $id_proveedor;
    public $id_usuario;
    public $fecha;
    public $total;
    public $id_forma_pago;
    public $archivo;
    public $subtotal;
    public $igv;
    public $id_sucursal;
    public $id_tipo_documento;
}
class DetalleCompra
{
    public $id;
    public $id_compra;
    public $id_producto;
    public $cantidad;
    public $precio_unitario;
}
