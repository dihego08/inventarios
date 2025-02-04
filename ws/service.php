<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Lima');
include('../env/Monodon.php');
$mono = new Monodon();
$con = $mono->getConnection();
$accion = $_GET['parAccion'];

switch ($accion) {
    case 'lista_categorias':
        echo $mono->select_all("categorias", true);
        break;
    case 'insertar_categoria':
        $_POST['imagen'] = null;
        $_POST['id_usuario_creacion'] = null;
        $_POST['id_usuario_modificacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        echo $mono->insert_data("categorias", $_POST, false);
        break;
    case 'editar_categoria':
        echo $mono->select_one("categorias", array("id" => $_POST['id']));
        break;
    case 'actualizar_categoria':
        $_POST['imagen'] = null;
        $_POST['fecha_creacion'] = $_POST['fecha_creacion'];
        $_POST['id_usuario_creacion'] = $_POST['id_usuario_creacion'];
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = null;
        echo $mono->update_data("categorias", $_POST);
        break;
    case 'eliminar_categoria':
        echo $mono->delete_data("categorias", array("id" => $_POST['id']));
        break;


    case 'lista_sucursales':
        echo $mono->select_all("sucursales", true);
        break;
    case 'insertar_sucursal':
        $_POST['id_usuario_creacion'] = null;
        $_POST['id_usuario_modificacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        echo $mono->insert_data("sucursales", $_POST, false);
        break;
    case 'editar_sucursal':
        echo $mono->select_one("sucursales", array("id" => $_POST['id']));
        break;
    case 'actualizar_sucursal':
        $_POST['fecha_creacion'] = $_POST['fecha_creacion'];
        $_POST['id_usuario_creacion'] = $_POST['id_usuario_creacion'];
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = null;
        echo $mono->update_data("sucursales", $_POST);
        break;
    case 'eliminar_sucursal':
        echo $mono->delete_data("sucursales", array("id" => $_POST['id']));
        break;


    case 'lista_usuarios':
        $data = json_decode($mono->select_all("usuarios", true));
        $values = array();
        foreach ($data as $key) {
            $sucursal = json_decode($mono->select_one("sucursales", array("id" => $key->id_sucursal)));
            $key->sucursal = $sucursal->sucursal;
            unset($key->pass);
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_usuario':
        $_POST['id_usuario_creacion'] = null;
        $_POST['id_usuario_modificacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        if (empty($_POST['pass']) || is_null($_POST['pass'])) {
        } else {
            $_POST['pass'] = md5($_POST['pass']);
        }
        echo $mono->insert_data("usuarios", $_POST, false);
        break;
    case 'editar_usuario':
        $data = json_decode($mono->select_one("usuarios", array("id" => $_POST['id'])));
        unset($data->pass);
        echo json_encode($data);
        break;
    case 'actualizar_usuario':
        $_POST['fecha_creacion'] = $_POST['fecha_creacion'];
        $_POST['id_usuario_creacion'] = $_POST['id_usuario_creacion'];
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = null;
        if (empty($_POST['pass']) || is_null($_POST['pass'])) {
            $data = json_decode($mono->select_one("usuarios", array("id" => $_POST['id'])));
            $_POST['pass'] = $data->pass;
        } else {
            $_POST['pass'] = md5($_POST['pass']);
        }
        echo $mono->update_data("usuarios", $_POST);
        break;
    case 'eliminar_usuario':
        echo $mono->delete_data("usuarios", array("id" => $_POST['id']));
        break;


    case 'lista_clientes':
        $data = json_decode($mono->select_all("clientes", true));
        $values = array();
        foreach ($data as $key) {
            $sucursal = json_decode($mono->select_one("sucursales", array("id" => $key->id_sucursal)));
            $key->sucursal = $sucursal->sucursal;

            $tipo_cliente = json_decode($mono->select_one("tipos_clientes", array("id" => $key->id_tipo_cliente)));
            $key->tipo_cliente = $tipo_cliente->tipo_cliente;

            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'lista_clientes_sucursal':
        echo $mono->select_all_where("clientes", array("id_sucursal" => $_POST['id_sucursal']), true);
        break;
    case 'insertar_cliente':
        $_POST['id_usuario_creacion'] = null;
        $_POST['id_usuario_modificacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        echo $mono->insert_data("clientes", $_POST, false);
        break;
    case 'editar_cliente':
        echo $mono->select_one("clientes", array("id" => $_POST['id']));
        break;
    case 'actualizar_cliente':
        $_POST['fecha_creacion'] = $_POST['fecha_creacion'];
        $_POST['id_usuario_creacion'] = $_POST['id_usuario_creacion'];
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = null;
        echo $mono->update_data("clientes", $_POST);
        break;
    case 'eliminar_cliente':
        echo $mono->delete_data("clientes", array("id" => $_POST['id']));
        break;


    case 'lista_tipos_clientes':
        echo $mono->select_all("tipos_clientes", true);
        break;


    case 'lista_productos':
        $data = json_decode($mono->select_all("productos", true));
        $values = array();
        foreach ($data as $key) {
            $categoria = json_decode($mono->select_one("categorias", array("id" => $key->id_categoria)));
            if (!empty($categoria)) {
                $key->categoria = $categoria->categoria;
            } else {
                $key->categoria = '';
            }


            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_producto':
        $_POST['imagen'] = null;
        $_POST['id_usuario_creacion'] = null;
        $_POST['id_usuario_modificacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        echo $mono->insert_data("productos", $_POST, false);
        break;
    case 'editar_producto':
        echo $mono->select_one("productos", array("id" => $_POST['id']));
        break;
    case 'actualizar_producto':
        $_POST['imagen'] = null;
        $_POST['fecha_creacion'] = $_POST['fecha_creacion'];
        $_POST['id_usuario_creacion'] = $_POST['id_usuario_creacion'];
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = null;
        echo $mono->update_data("productos", $_POST);
        break;
    case 'eliminar_producto':
        echo $mono->delete_data("productos", array("id" => $_POST['id']));
        break;
    case 'lista_productos_categoria':
        $sql = 'SELECT p.*, ps.precio_unitario, ps.stock FROM productos p JOIN producto_sucursal ps ON p.id = ps.id_producto AND ps.id_sucursal = 2 WHERE p.id_categoria = ' . $_POST['id_categoria'];
        echo $mono->run_query($sql);
        break;


    case 'lista_formas_pagos':
        echo $mono->select_all("formas_pagos", true);
        break;

    case 'guardar_venta':
        $_POST['id_usuario_creacion'] = null;
        $_POST['id_usuario_modificacion'] = 1;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        $_POST['estado'] = 0;

        $res = json_decode($mono->insert_data("ventas", $_POST, false));
        $LID = $res->LID;
        $codigos_carro = $_POST['codigos_carro'];
        $cantidades = $_POST['cantidades'];
        $precios = $_POST['precios'];

        foreach ($codigos_carro as $i => $codigo) {
            $data = [
                'id_venta' => $LID,
                'id_producto' => $codigo,
                'precio_unitario' => $precios[$i],
                'cantidad' => $cantidades[$i],
                'total' => $precios[$i] * $cantidades[$i],
                'id_usuario_creacion' => 1,
                'id_usuario_modificacion' => null,
                'fecha_modificacion' => null,
                'fecha_creacion' => null,
            ];

            $r1 = $mono->insert_data("venta_detalle", $data, false);

            $data_movimientos = $data;
            $data_movimientos['tipo'] = 1;
            $data_movimientos['id_sucursal'] = $_POST['id_sucursal'];
            $data_movimientos['id_cliente'] = $_POST['id_cliente'];

            $r2 = $mono->insert_data("movimientos", $data_movimientos, false);

            $sql = "UPDATE producto_sucursal SET stock = stock - 1, id_usuario_modificacion = " . $_POST['id_usuario_modificacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "' WHERE id_producto = " . $codigo . " AND id_sucursal = " . $_POST['id_sucursal'];
            $mono->executor($sql, "update");
        }

        echo json_encode(array("Result" => "OK"));
        break;
    case 'lista_ventas':
        $sql = "SELECT v.*, c.nombres, s.sucursal, DATE(v.fecha_creacion) as fecha FROM ventas AS v LEFT JOIN clientes AS c ON c.id = v.id_cliente LEFT JOIN sucursales AS s ON s.id = v.id_sucursal WHERE v.estado = 0";
        echo $mono->run_query($sql);
        break;
    case 'eliminar_venta':
        $sql = "UPDATE ventas SET estado = 1 WHERE id = " . $_POST['id'];
        echo $mono->executor($sql, "update");
        break;
    case 'detalle_venta':
        $sql = "SELECT p.producto, v.* FROM venta_detalle AS v JOIN productos AS p ON p.id = v.id_producto AND v.id_venta = " . $_POST['id'];
        echo $mono->run_query($sql);
        break;
    default:
        # code...
        break;
}
