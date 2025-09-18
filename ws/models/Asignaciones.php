<?php
class Asignaciones
{
    public $id;
    public $id_usuario;
    public $fecha_asignacion;
    public $fecha_devolucion;
    public $fecha_creacion;
    public $id_usuario_creacion;
    public $fecha_modificacion;
    public $id_usuario_modificacion;
}
class Asignacion_Detalle
{
    public $id;
    public $id_asignacion;
    public $id_producto;
    public $cantidad;
    public $id_motivo;
    public $observaciones;
    public $fecha;
    public $id_usuario_creacion;
    public $fecha_creacion;
    public $id_usuario_modificacion;
    public $fecha_modificacion;
    public $id_estado_asignacion;
    public $id_estado_devolucion;
    public $id_almacen;
    public $id_producto_serie;
}
