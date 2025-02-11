<?php

use Shuchkin\SimpleXLSX;

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Lima');
include('../env/Monodon.php');
include('models/Sucursal.php');
include('models/Ventas.php');
require __DIR__ . '/simplexlsx/src/SimpleXLSX.php';
$mono = new Monodon();
$con = $mono->getConnection();
$accion = $_GET['parAccion'];

switch ($accion) {
    case 'lista_categorias':
        echo $mono->select_all("categorias", true);
        break;
    case 'insertar_categoria':
        $_POST['imagen'] = null;
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
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
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
        echo $mono->update_data("categorias", $_POST);
        break;
    case 'eliminar_categoria':
        echo $mono->delete_data("categorias", array("id" => $_POST['id']));
        break;


    case 'lista_sucursales':
        echo $mono->select_all("sucursales", true);
        break;
    case 'insertar_sucursal':
        $sucursal = new Sucursal;
        $sucursal->sucursal = $_POST['sucursal'];
        $sucursal->direccion = $_POST['direccion'];
        $sucursal->id_usuario_creacion = $_SESSION['id'];
        echo $mono->insert_data_v2("sucursales", $sucursal);
        break;
    case 'editar_sucursal':
        echo $mono->select_one("sucursales", array("id" => $_POST['id']));
        break;
    case 'actualizar_sucursal':
        $sucursal = new Sucursal;
        $sucursal->sucursal = $_POST['sucursal'];
        $sucursal->direccion = $_POST['direccion'];
        $sucursal->id_usuario_modificacion = $_SESSION['id'];
        $sucursal->fecha_modificacion = date("Y-m-d H:i:s");
        $sucursal->id = $_POST['id'];
        echo $mono->update_data_v2("sucursales", $sucursal);
        break;
    case 'eliminar_sucursal':
        echo $mono->delete_data("sucursales", array("id" => $_POST['id']));
        break;


    case 'lista_usuarios':
        $data = json_decode($mono->select_all("usuarios", true));
        $values = array();
        foreach ($data as $key) {
            $sucursal = json_decode($mono->select_one("sucursales", array("id" => $key->id_sucursal)));
            $rol = json_decode($mono->select_one("roles", array("id" => $key->id_rol)));
            if (empty($sucursal) || is_null($sucursal)) {
                $key->sucursal = "";
            } else {
                $key->sucursal = $sucursal->sucursal;
            }
            if (empty($rol) || is_null($rol)) {
                $key->rol = "";
            } else {
                $key->rol = $rol->rol;
            }
            unset($key->pass);
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_usuario':
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
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
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
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


            if (empty($sucursal) || is_null($sucursal)) {
                $key->sucursal = '';
            } else {
                $key->sucursal = $sucursal->sucursal;
            }

            $tipo_cliente = json_decode($mono->select_one("tipos_clientes", array("id" => $key->id_tipo_cliente)));
            $key->tipo_cliente = $tipo_cliente->tipo_cliente;

            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'lista_clientes_sucursal':
        echo $mono->select_all_where("clientes", array("id_sucursal" => $_SESSION['id_sucursal']), true);
        break;
    case 'insertar_cliente':
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
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
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
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
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
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
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
        echo $mono->update_data("productos", $_POST);
        break;
    case 'eliminar_producto':
        echo $mono->delete_data("productos", array("id" => $_POST['id']));
        break;
    case 'lista_productos_categoria':
        $sql = 'SELECT p.*, ps.precio_unitario, ps.stock FROM productos p JOIN producto_sucursal ps ON p.id = ps.id_producto AND ps.id_sucursal = ' . $_SESSION['id_sucursal'] . ' WHERE p.id_categoria = ' . $_POST['id_categoria'];
        echo $mono->run_query($sql);
        break;


    case 'lista_formas_pagos':
        echo $mono->select_all("formas_pagos", true);
        break;

    case 'guardar_venta':
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        $_POST['estado'] = 0;

        if ($_POST['id_forma_pago'] == 2) {
            $cliente = json_decode($mono->select_one("clientes", array("id" => $_POST['id_cliente'])));
            if ($cliente->saldo < $_POST['monto']) {
                echo json_encode(array(
                    "Result" => "ERROR",
                    "Message" => "El cliente no tiene saldo suficiente"
                ));
                return;
            } else {
                $sql = "UPDATE clientes SET saldo = saldo - " . $_POST['monto'] . ", id_usuario_modificacion = " . $_POST['id_usuario_modificacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "' WHERE id = " . $_POST['id_cliente'];
                $mono->executor($sql, "update");
            }
        }
        $venta = new Ventas();
        $venta->id_cliente = $_POST['id_cliente'];
        $venta->id_sucursal = $_SESSION['id_sucursal'];
        $venta->monto = $_POST['monto'];
        $venta->id_usuario_creacion = $_SESSION['id'];
        $venta->id_forma_pago = $_POST['id_forma_pago'];
        $venta->estado = 0;

        $res = json_decode($mono->insert_data_v2("ventas", $venta));

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
                'id_usuario_creacion' => $_SESSION['id'],
                'id_usuario_modificacion' => null,
                'fecha_modificacion' => null,
                'fecha_creacion' => null,
            ];

            $r1 = $mono->insert_data("venta_detalle", $data, false);

            $data_movimientos = $data;
            $data_movimientos['tipo'] = 1;
            $data_movimientos['id_sucursal'] = $_SESSION['id_sucursal'];
            $data_movimientos['id_cliente'] = $_POST['id_cliente'];

            $r2 = $mono->insert_data("movimientos", $data_movimientos, false);

            $sql = "UPDATE producto_sucursal SET stock = stock - " . $cantidades[$i] . ", id_usuario_modificacion = " . $_POST['id_usuario_modificacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "' WHERE id_producto = " . $codigo . " AND id_sucursal = " . $_SESSION['id_sucursal'];
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


    case 'cargar_archivo':
        $fileName = $_FILES["archivo"]["name"];
        $fileTmpLoc = $_FILES["archivo"]["tmp_name"];
        $fileType = $_FILES["archivo"]["type"];
        $fileSize = $_FILES["archivo"]["size"];
        $fileErrorMsg = $_FILES["archivo"]["error"];

        if (!$fileTmpLoc) {
        }
        if (move_uploaded_file($fileTmpLoc, "temp/$fileName")) {
            $excel = $fileName;
        } else {
            echo json_encode(array("Result" => "ERROR", "ERROR" => "No se pudo cargar el archivo"));
            return;
        }

        if ($xlsx = SimpleXLSX::parse('temp/' . $excel)) {
            echo json_encode(array("Result" => "OK", "data" => $xlsx->rows()));
        } else {
            echo json_encode(array("Result" => "ERROR", "ERROR" => SimpleXLSX::parseError()));
        }
        break;
    case "guardar_abastecimiento":
        $fileName = $_FILES["archivo"]["name"];
        $fileTmpLoc = $_FILES["archivo"]["tmp_name"];
        $fileType = $_FILES["archivo"]["type"];
        $fileSize = $_FILES["archivo"]["size"];
        $fileErrorMsg = $_FILES["archivo"]["error"];

        if (!$fileTmpLoc) {
        }
        if (move_uploaded_file($fileTmpLoc, "temp/$fileName")) {
            $excel = $fileName;
        } else {
            echo json_encode(array("Result" => "ERROR", "ERROR" => "No se pudo cargar el archivo"));
            return;
        }

        if ($xlsx = SimpleXLSX::parse('temp/' . $excel)) {
            $data = $xlsx->rows();
            $aux = 0;
            foreach ($data as $i) {
                if ($aux > 0) {
                    $data_movimientos['tipo'] = 0;
                    $data_movimientos['id_producto'] = $i[0];
                    $data_movimientos['cantidad'] = $i[2];
                    $data_movimientos['precio_unitario'] = $i[3];
                    $data_movimientos['id_sucursal'] = $_POST['id_sucursal'];
                    $data_movimientos['id_cliente'] = null;
                    $data_movimientos['id_usuario_creacion'] = $_SESSION['id'];
                    $data_movimientos['fecha_creacion'] = null;
                    $data_movimientos['id_usuario_modificacion'] = null;
                    $data_movimientos['fecha_modificacion'] = null;

                    $r2 = $mono->insert_data("movimientos", $data_movimientos, false);

                    $existe = json_decode($mono->select_one("producto_sucursal", array("id_producto" => $i[0], "id_sucursal" => $_POST['id_sucursal'])));
                    if (empty($existe) || is_null($existe)) {

                        $dd['id_producto'] = $i[0];
                        $dd['stock'] = $i[2];
                        $dd['id_sucursal'] = $_POST['id_sucursal'];
                        $dd['precio_unitario'] = $i[3];
                        $dd['id_usuario_creacion'] = $data_movimientos['id_usuario_creacion'];
                        $dd['fecha_creacion'] = $data_movimientos['fecha_creacion'];
                        $dd['id_usuario_modificacion'] = $_SESSION['id'];
                        $dd['fecha_modificacion'] = $data_movimientos['fecha_modificacion'];
                        $r3 = $mono->insert_data("producto_sucursal", $dd, false);
                    } else {
                        $sql = "UPDATE producto_sucursal SET stock = stock + " . $i[2] . ", id_usuario_modificacion = " . $data_movimientos['id_usuario_creacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "' WHERE id_producto = " . $i[0] . " AND id_sucursal = " . $_POST['id_sucursal'];
                        $mono->executor($sql, "update");
                    }
                }
                $aux++;
            }
            echo json_encode(array("Result" => "OK", "data" => []));
        } else {
            echo json_encode(array("Result" => "ERROR", "ERROR" => SimpleXLSX::parseError()));
        }
        break;


    case 'buscar_saldos':
        $sql = "SELECT c.nombres, r.id, r.monto, date(r.fecha_creacion) fecha FROM clientes AS c JOIN recargas AS r ON r.id_cliente = c.id AND c.id = " . $_POST['id_cliente'];
        echo $mono->run_query($sql);
        break;
    case "insertar_saldo":
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = null;
        $r1 = $mono->insert_data("recargas", $_POST, false);

        $sql = "UPDATE clientes SET saldo = saldo + " . $_POST['monto'] . ", id_usuario_modificacion = " . $_POST['id_usuario_modificacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "' WHERE id = " . $_POST['id_cliente'];
        echo $mono->executor($sql, "update");

        break;

    case 'iniciar_sesion':
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $user = json_decode($mono->select_one("usuarios", array("usuario" => $user, "pass" => md5($pass))));
        if (empty($user) || is_null($user)) {
            echo json_encode(array(
                "Result" => "ERROR"
            ));
        } else {
            $_SESSION['id'] = $user->id;
            $_SESSION['nombres'] = $user->nombres;
            $_SESSION['id_sucursal'] = $user->id_sucursal;
            echo json_encode(array(
                "Result" => "OK"
            ));
        }
        break;
    case 'get_data_session':
        if (empty($_SESSION) || is_null($_SESSION)) {
            echo json_encode(array(
                "Result" => "ERROR"
            ));
        } else {
            echo json_encode($_SESSION);
        }
        break;
    case 'cerrar_session':
        session_destroy();
        unset($_SESSION);
        break;

    case 'ver_stock':
        $sql = "SELECT p.producto, ps.* FROM producto_sucursal AS ps JOIN productos AS p on p.id = ps.id_producto AND ps.id_sucursal = " . $_POST['id_sucursal'];
        echo $mono->run_query($sql);
        break;


    case 'lista_gastos':
        $data = json_decode($mono->select_all("gastos", true));
        $values = array();
        foreach ($data as $key) {
            $sucursal = json_decode($mono->select_one("sucursales", array("id" => $key->id_sucursal)));
            if (empty($sucursal) || is_null($sucursal)) {
                $key->sucursal = "";
            } else {
                $key->sucursal = $sucursal->sucursal;
            }
            unset($key->pass);
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_gasto':
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
        $_POST['fecha_creacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['id_usuario_modificacion'] = null;
        echo $mono->insert_data("gastos", $_POST, false);
        break;
    case 'editar_gasto':
        echo $mono->select_one("gastos", array("id" => $_POST['id']));
        break;
    case 'actualizar_gasto':
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
        echo $mono->update_data("gastos", $_POST);
        break;
    case 'lista_roles':
        echo $mono->select_all("roles", true);
        break;
    default:
        # code...
        break;
}
