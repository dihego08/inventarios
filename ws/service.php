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
include('models/Movimientos.php');
include('models/Productos.php');
include('models/Proveedor.php');
include('models/Compra.php');
include('models/Colaborador.php');
include('models/Usuario.php');
include('models/Asignaciones.php');
require __DIR__ . '/simplexlsx/src/SimpleXLSX.php';
require 'jwt.php';

//include('secure.php'); // Archivo para validar el token

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
        $_POST['fecha_creacion'] = date("Y-m-d H:i:s");
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
    case 'lista_marcas':
        echo $mono->select_all("marcas", true);
        break;
    case 'insertar_marca':
        $_POST['imagen'] = null;
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
        $_POST['id_usuario_modificacion'] = null;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = date("Y-m-d H:i:s");
        echo $mono->insert_data("marcas", $_POST, false);
        break;
    case 'insertar_rol':
        echo $mono->insert_data("roles", $_POST, false);
        break;
    case 'editar_marca':
        echo $mono->select_one("marcas", array("id" => $_POST['id']));
        break;
    case 'editar_rol':
        echo $mono->select_one("roles", array("id" => $_POST['id']));
        break;
    case 'actualizar_rol':
        echo $mono->update_data("roles", $_POST);
        break;
    case 'eliminar_rol':
        echo $mono->delete_data("roles", array("id" => $_POST['id']));
        break;
    case 'actualizar_marca':
        $_POST['imagen'] = null;
        $_POST['fecha_creacion'] = $_POST['fecha_creacion'];
        $_POST['id_usuario_creacion'] = $_POST['id_usuario_creacion'];
        $_POST['fecha_modificacion'] = date("Y-m-d H:i:s");
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
        echo $mono->update_data("marcas", $_POST);
        break;
    case 'eliminar_marca':
        echo $mono->delete_data("marcas", array("id" => $_POST['id']));
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
            $colaborador = json_decode($mono->select_one("colaboradores", array("id" => $key->id_colaborador)));
            if (empty($colaborador) || is_null($colaborador)) {
                $key->colaborador = "";
            } else {
                $key->colaborador = $colaborador;
            }
            unset($key->pass);
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_usuario':
        $usuario = new Usuario;
        $usuario->usuario = $_POST['usuario'];

        $usuario->id_colaborador = $_POST['id_colaborador'];
        $usuario->id_usuario_creacion = $_SESSION['id'];
        if (empty($_POST['pass']) || is_null($_POST['pass'])) {
        } else {
            $_POST['pass'] = md5($_POST['pass']);
        }
        $usuario->pass = $_POST['pass'];
        echo $mono->insert_data_v2("usuarios", $usuario);
        break;
    case 'editar_usuario':
        $data = json_decode($mono->select_one("usuarios", array("id" => $_POST['id'])));
        unset($data->pass);
        echo json_encode($data);
        break;
    case 'actualizar_usuario':
        $usuario = new Usuario;
        $usuario->usuario = $_POST['usuario'];
        $usuario->id = $_POST['id'];
        $usuario->id_colaborador = $_POST['id_colaborador'];
        $usuario->id_usuario_modificacion = $_SESSION['id'];
        $usuario->fecha_modificacion = date("Y-m-d H:i:s");
        if (!empty($_POST['pass']) && !is_null($_POST['pass'])) {
            $usuario->pass = md5($_POST['pass']);
        }
        echo $mono->update_data_v2("usuarios", $usuario);
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
        $_POST['fecha_creacion'] = date("Y-m-d H:i:s");
        $_POST['saldo'] = 0;
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
        $sql = "SELECT * FROM productos WHERE estado is null or estado = 0";
        $data = json_decode($mono->run_query($sql));
        $values = array();
        foreach ($data as $key) {
            if ($key->id_marca > 0 && !empty($key->id_marca)) {
                $marca = json_decode($mono->select_one("marcas", array("id" => $key->id_marca)));
                $key->marca = $marca->marca;
            } else {
                $key->marca = null;
            }

            if ($key->id_categoria > 0 && !empty($key->id_categoria)) {
                $categoria = json_decode($mono->select_one("categorias", array("id" => $key->id_categoria)));
                $key->categoria = $categoria->categoria;
            } else {
                $key->categoria = null;
            }
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_producto':
        $producto = new Productos();
        $producto->producto = $_POST['producto'];
        $producto->id_categoria = $_POST['id_categoria'];
        $producto->descripcion = $_POST['descripcion'];;
        $producto->id_marca = $_POST['id_marca'];
        $producto->codigo = $_POST['codigo'];
        $producto->estado = 0;

        echo $mono->insert_data_v2("productos", $producto);
        break;
    case 'editar_producto':
        echo $mono->select_one("productos", array("id" => $_POST['id']));
        break;
    case 'actualizar_producto':
        $producto = new Productos();
        $producto->producto = $_POST['producto'];
        $producto->id_categoria = $_POST['id_categoria'];
        $producto->descripcion = $_POST['descripcion'];;
        $producto->id_marca = $_POST['id_marca'];
        $producto->codigo = $_POST['codigo'];
        $producto->id = $_POST['id'];
        echo $mono->update_data_v2("productos", $producto);
        break;
    case 'eliminar_producto':
        $sql = "UPDATE productos SET estado = 1/*, id_usuario_modificacion = " . $_SESSION['id'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "'*/ WHERE id = " . $_POST['id'];
        echo $mono->executor($sql, "update");
        break;
    case 'lista_productos_categoria':
        $sql = 'SELECT p.*, ps.precio_unitario, ps.stock FROM productos p JOIN producto_sucursal ps ON p.id = ps.id_producto AND ps.id_sucursal = ' . $_SESSION['id_sucursal'] . ' WHERE p.id_categoria = ' . $_POST['id_categoria'] . " AND p.estado = 0";
        echo $mono->run_query($sql);
        break;

    case 'lista_formas_pagos':
        echo $mono->select_all("formas_pagos", true);
        break;
    case 'lista_motivos':
        echo $mono->select_all("motivos", true);
        break;
    case 'lista_tipos_documentos':
        echo $mono->select_all("tipos_documentos", true);
        break;
    case 'lista_tipos_documentos_identificacion':
        echo $mono->select_all("tipos_documentos_identificacion", true);
        break;
    case 'guardar_asignacion':
        $asignacion = new Asignaciones();
        $asignacion->id_usuario = $_POST['id_usuario'];
        $asignacion->fecha_asignacion = date("Y-m-d H:i:s");
        //$asignacion->monto = $_POST['monto'];
        $asignacion->id_usuario_creacion = $_SESSION['id'];
        //$asignacion->id_forma_pago = $_POST['id_forma_pago'];
        $asignacion->fecha_creacion = date("Y-m-d H:i:s");
        //$asignacion->estado = 0;

        $res = json_decode($mono->insert_data_v2("asignaciones", $asignacion));

        $LID = $res->LID;
        /*$codigos_carro = $_POST['codigos_carro'];
        $cantidades = $_POST['cantidades'];
        $precios = $_POST['precios'];*/
        $detalle = $_POST['data'];

        $asignacion_detalle = new Asignacion_Detalle;
        $movimientos = new Movimientos;
        foreach ($detalle as $i) {
            $asignacion_detalle->id_asignacion = $LID;
            $asignacion_detalle->id_producto = $i['id_producto'];
            $asignacion_detalle->id_motivo = $i['id_motivo'];
            $asignacion_detalle->cantidad = $i['cantidad'];
            $asignacion_detalle->observaciones = $i['observaciones'];
            $asignacion_detalle->id_usuario_creacion = $_SESSION['id'];
            $asignacion_detalle->fecha_creacion = $asignacion->fecha_creacion;

            $r1 = $mono->insert_data_v2("detalle_asignaciones ", $asignacion_detalle);

            /*$movimientos->tipo = 1;
            $movimientos->id_producto = $codigo;
            $movimientos->cantidad = $cantidades[$i];
            $movimientos->precio_unitario = $precios[$i];
            $movimientos->id_sucursal = $_SESSION['id_sucursal'];
            $movimientos->id_cliente = $_POST['id_cliente'];
            $movimientos->id_usuario_creacion = $_SESSION['id'];
            $movimientos->fecha_creacion = date("Y-m-d H:i:s");

            $r2 = $mono->insert_data_v2("movimientos", $movimientos);

            $sql = "UPDATE producto_sucursal SET stock = stock - " . $cantidades[$i] . ", id_usuario_modificacion = " . $_POST['id_usuario_modificacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "' WHERE id_producto = " . $codigo . " AND id_sucursal = " . $_SESSION['id_sucursal'];
            $mono->executor($sql, "update");*/
        }

        echo json_encode(array("Result" => "OK"));
        break;
    case 'lista_ventas':
        $sql = "SELECT v.*, c.nombres, s.sucursal, v.fecha_creacion as fecha, f.forma_pago FROM ventas AS v LEFT JOIN clientes AS c ON c.id = v.id_cliente LEFT JOIN sucursales AS s ON s.id = v.id_sucursal LEFT JOIN formas_pagos AS f ON f.id = v.id_forma_pago WHERE v.estado = 0 AND date(v.fecha_creacion) BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "'";
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
    case 'guardar_abastecimiento_manual':
        $data_movimientos['tipo'] = 0;
        $data_movimientos['id_producto'] = $_POST['id_producto'];
        $data_movimientos['cantidad'] = $_POST['cantidad'];
        $data_movimientos['precio_unitario'] = $_POST['precio_unitario'];
        $data_movimientos['id_sucursal'] = $_POST['id_sucursal'];
        $data_movimientos['id_cliente'] = null;
        $data_movimientos['id_usuario_creacion'] = $_SESSION['id'];
        $data_movimientos['fecha_creacion'] = date("Y-m-d H:i:s");
        $data_movimientos['id_usuario_modificacion'] = null;
        $data_movimientos['fecha_modificacion'] = null;

        $r2 = $mono->insert_data("movimientos", $data_movimientos, false);

        $existe = json_decode($mono->select_one("producto_sucursal", array("id_producto" => $_POST['id_producto'], "id_sucursal" => $_POST['id_sucursal'])));
        if (empty($existe) || is_null($existe)) {

            $dd['id_producto'] = $_POST['id_producto'];
            $dd['stock'] = $_POST['cantidad'];
            $dd['id_sucursal'] = $_POST['id_sucursal'];
            $dd['precio_unitario'] = $_POST['precio_unitario'];
            $dd['id_usuario_creacion'] = $data_movimientos['id_usuario_creacion'];
            $dd['fecha_creacion'] = $data_movimientos['fecha_creacion'];
            $dd['id_usuario_modificacion'] = $_SESSION['id'];
            $dd['fecha_modificacion'] = $data_movimientos['fecha_modificacion'];
            $r3 = $mono->insert_data("producto_sucursal", $dd, false);
        } else {
            $sql = "UPDATE producto_sucursal SET stock = stock + " . $_POST['cantidad'] . ", id_usuario_modificacion = " . $data_movimientos['id_usuario_creacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "', precio_unitario=" . $_POST['precio_unitario'] . " WHERE id_producto = " . $_POST['id_producto'] . " AND id_sucursal = " . $_POST['id_sucursal'];
            $mono->executor($sql, "update");
        }
        echo json_encode(array("Result" => "OK", "data" => []));
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
                    $data_movimientos['fecha_creacion'] = date("Y-m-d H:i:s");
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
                        $sql = "UPDATE producto_sucursal SET stock = stock + " . $i[2] . ", id_usuario_modificacion = " . $data_movimientos['id_usuario_creacion'] . ", fecha_modificacion = '" . date("Y-m-d H:i:s") . "', precio_unitario=" . $_POST['precio_unitario'] . " WHERE id_producto = " . $i[0] . " AND id_sucursal = " . $_POST['id_sucursal'];
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
    case 'buscar_saldos_cliente':
        $sql = "SELECT r.id, r.monto, r.fecha_creacion as fecha, 'R' AS tipo FROM recargas AS r WHERE r.id_cliente = " . $_POST['id_cliente'] . " AND date(r.fecha_creacion) BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "' UNION SELECT v.id, v.monto, v.fecha_creacion as fecha, 'V' AS tipo FROM ventas AS v WHERE v.estado = 0 AND date(v.fecha_creacion) BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "' AND v.id_forma_pago = 2 AND v.id_cliente = " . $_POST['id_cliente'];
        echo $mono->run_query($sql);
        break;
    case 'buscar_saldos':
        $sql = "SELECT c.nombres, r.id, r.monto, r.fecha_creacion as fecha FROM clientes AS c JOIN recargas AS r ON r.id_cliente = c.id AND c.id = " . $_POST['id_cliente'] . " WHERE fecha BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "'";
        echo $mono->run_query($sql);
        break;
    case "insertar_saldo":
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
        $_POST['id_usuario_modificacion'] = 0;
        $_POST['fecha_modificacion'] = null;
        $_POST['fecha_creacion'] = date("Y-m-d H:i:s");
        $_POST['id_forma_pago'] = $_POST['id_forma_pago'];
        $_POST['fecha'] = $_POST['fecha'];
        $r1 = $mono->insert_data("recargas", $_POST, false);
        $_POST['id_usuario_modificacion'] = $_SESSION['id'];
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
            $payload = [
                "user_id" => $user->id,
                "exp" => time() + (60 * 60) // Expira en 1 hora
            ];
            $token = generateJWT($payload, $secret_key);

            $colaborador = json_decode($mono->select_one("colaboradores", array("id" => $user->id_colaborador)));

            $_SESSION['id'] = $user->id;
            $_SESSION['nombres'] = $colaborador->nombres;
            $_SESSION['id_sucursal'] = 1; //$colaborador->id_sucursal;
            echo json_encode(array(
                "Result" => "OK",
                "Token" => $token
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
        $sql = "SELECT p.producto, p.id, ps.id_producto, ps.stock, ps.precio_unitario FROM producto_sucursal AS ps RIGHT JOIN productos AS p on p.id = ps.id_producto AND ps.id_sucursal = " . $_POST['id_sucursal'];
        echo $mono->run_query($sql);
        break;


    case 'lista_gastos':
        $sql = "SELECT g.*, s.sucursal, g.fecha as fecha FROM gastos AS g LEFT JOIN sucursales AS s ON s.id = g.id_sucursal WHERE date(g.fecha_creacion) BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "'";
        echo $mono->run_query($sql);
        break;
    case 'insertar_gasto':
        $_POST['id_usuario_creacion'] = $_SESSION['id'];
        $_POST['fecha_creacion'] = date("Y-m-d H:i:s");
        $_POST['fecha'] = $_POST['fecha'];
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

    case 'ver_movimientos':
        $sql = "SELECT p.producto, m.id, m.tipo, m.id_producto, m.cantidad, p.precio_unitario, c.nombres, m.fecha_creacion as fecha FROM movimientos AS m left join productos AS p on p.id = m.id_producto left join clientes as c on c.id = m.id_cliente WHERE m.id_sucursal = " . $_POST['id_sucursal'] . " AND date(m.fecha_creacion) BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "'";
        echo $mono->run_query($sql);
        break;
    case 'autocomplete':
        $sql = 'SELECT p.* FROM productos p WHERE p.producto LIKE "%' . $_GET['search'] . '%" AND (p.estado is null or estado = 0)';
        echo $mono->run_query($sql);
        break;
    case 'reporte_fecha':

        if ($_POST['id_sucursal'] > 0) {
            $sql_ventas = "SELECT SUM(cant) AS cant, id FROM (SELECT COALESCE(SUM(v.monto), 0) AS cant, fp.id FROM ventas v RIGHT JOIN formas_pagos fp ON v.id_forma_pago = fp.id AND date(v.fecha_creacion) = '" . $_POST['fecha'] . "' AND v.id_sucursal = " . $_POST['id_sucursal'] . " GROUP BY fp.id UNION ALL SELECT COALESCE(sum(r.monto), 0) AS cant, fp.id FROM recargas r RIGHT JOIN formas_pagos fp ON r.id_forma_pago = fp.id AND r.fecha = '" . $_POST['fecha'] . "' JOIN clientes c ON c.id = r.id_cliente AND c.id_sucursal = " . $_POST['id_sucursal'] . " GROUP BY fp.id) AS tb1 GROUP BY id;";
            $sql_gastos = "SELECT COALESCE(SUM(monto), 0) AS cant FROM gastos WHERE id_sucursal = " . $_POST['id_sucursal'] . " AND fecha = '" . $_POST['fecha'] . "'";
        } else {
            $sql_ventas = "SELECT SUM(cant) AS cant, id FROM (SELECT COALESCE(SUM(v.monto), 0) AS cant, fp.id FROM ventas v RIGHT JOIN formas_pagos fp ON v.id_forma_pago = fp.id AND date(v.fecha_creacion) = '" . $_POST['fecha'] . "' GROUP BY fp.id UNION ALL SELECT COALESCE(sum(r.monto), 0) AS cant, fp.id FROM recargas r RIGHT JOIN formas_pagos fp ON r.id_forma_pago = fp.id AND r.fecha = '" . $_POST['fecha'] . "' GROUP BY fp.id) AS tb1 GROUP BY id;";
            $sql_gastos = "SELECT COALESCE(SUM(monto), 0) AS cant FROM gastos WHERE fecha = '" . $_POST['fecha'] . "'";
        }
        //echo $sql;
        $ventas = json_decode($mono->run_query($sql_ventas));
        $gastos = json_decode($mono->run_query($sql_gastos));
        $acumulado = json_decode($mono->run_query("select sum(cant) as cant, fecha, 'V' tipo FROM (SELECT COALESCE(sum(v.monto), 0) cant, DATE(v.fecha_creacion) fecha, 'V' tipo FROM ventas AS v GROUP BY fecha UNION ALL SELECT COALESCE(SUM(r.monto), 0) cant, DATE(r.fecha) fecha, 'R' tipo FROM recargas AS r GROUP BY fecha ORDER BY fecha DESC) as G GROUP by fecha union all SELECT COALESCE(SUM(g.monto), 0) cant, DATE(g.fecha) fecha, 'G' tipo FROM gastos AS g GROUP BY fecha ORDER BY fecha DESC;"));
        $values = array();
        $values['ventas'] = $ventas;
        $values['gastos'] = $gastos;
        $values['acumulado'] = $acumulado;
        echo json_encode($values);
        break;

    case 'lista_proveedores':
        echo $mono->select_all("proveedores", true);
        break;
    case 'insertar_proveedor':
        $proveedor = new Proveedor();
        $proveedor->razon_social = $_POST['razon_social'];
        $proveedor->n_documento = $_POST['n_documento'];
        $proveedor->direccion = $_POST['direccion'];;
        $proveedor->telefono = $_POST['telefono'];
        $proveedor->email = $_POST['email'];
        $proveedor->id_tipo_documento = $_POST['id_tipo_documento'];
        echo $mono->insert_data_v2("proveedores", $proveedor);
        break;
    case 'editar_proveedor':
        echo $mono->select_one("proveedores", array("id" => $_POST['id']));
        break;
    case 'actualizar_proveedor':
        $proveedor = new Proveedor();
        $proveedor->razon_social = $_POST['razon_social'];
        $proveedor->n_documento = $_POST['n_documento'];
        $proveedor->direccion = $_POST['direccion'];;
        $proveedor->telefono = $_POST['telefono'];
        $proveedor->email = $_POST['email'];
        $proveedor->id_tipo_documento = $_POST['id_tipo_documento'];
        $proveedor->id = $_POST['id'];
        echo $mono->update_data_v2("proveedores", $proveedor);
        break;
    case 'eliminar_proveedor':
        echo $mono->delete_data("proveedores", array("id" => $_POST['id']));
        break;
    case 'guardar_compra':
        $compra = new Compra();
        $compra->id_proveedor = $_POST['id_proveedor'];
        $compra->fecha = $_POST['fecha'];
        $compra->total = $_POST['total'];
        $compra->subtotal = $_POST['subtotal'];
        $compra->igv = $_POST['igv'];
        $compra->id_forma_pago = $_POST['id_forma_pago'];
        $compra->id_tipo_documento = $_POST['id_tipo_documento'];

        $res = json_decode($mono->insert_data_v2("compras", $compra));

        $LID = $res->LID;
        $codigos_carro = $_POST['codigos_carro'];
        $cantidades = $_POST['cantidades'];
        $precios = $_POST['precios'];

        $compraDetalle = new DetalleCompra;
        $movimientos = new Movimientos;
        foreach ($codigos_carro as $i => $codigo) {
            $compraDetalle->id_compra = $LID;
            $compraDetalle->id_producto = $codigo;
            $compraDetalle->precio_unitario = $precios[$i];
            $compraDetalle->cantidad = $cantidades[$i];

            $r1 = $mono->insert_data_v2("detalle_compras", $compraDetalle);
        }

        echo json_encode(array("Result" => "OK"));
        break;
    case 'lista_compras':
        $sql = "SELECT v.*, c.nombres, s.sucursal, v.fecha as fecha, f.forma_pago, td.tipo_documento FROM compras AS v LEFT JOIN proveedores AS c ON c.id = v.id_proveedor LEFT JOIN sucursales AS s ON s.id = v.id_sucursal LEFT JOIN formas_pagos AS f ON f.id = v.id_forma_pago JOIN tipos_documentos AS td ON td.id = v.id_tipo_documento WHERE date(v.fecha) BETWEEN '" . $_POST['fecha_desde'] . "' AND '" . $_POST['fecha_hasta'] . "'";
        echo $mono->run_query($sql);
        break;

    case 'lista_almacenes':
        $data = json_decode($mono->select_all("almacenes", true));
        $values = array();
        foreach ($data as $key) {
            $sucursal = json_decode($mono->select_one("sucursales", array("id" => $key->id_sucursal)));

            if (empty($sucursal) || is_null($sucursal)) {
                $key->sucursal = '';
            } else {
                $key->sucursal = $sucursal->sucursal;
            }
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'editar_almacen':
        echo $mono->select_one("almacenes", array("id" => $_POST['id']));
        break;
    case 'actualizar_almacen':
        echo $mono->update_data("almacenes", $_POST);
        break;
    case 'eliminar_almacen':
        echo $mono->delete_data("almacenes", array("id" => $_POST['id']));
        break;
    case 'insertar_almacen':
        echo $mono->insert_data("almacenes", $_POST, false);
        break;

    case 'lista_colaboradores':
        $data = json_decode($mono->select_all("colaboradores", true));
        $values = array();
        foreach ($data as $key) {
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
            $values[] = $key;
        }
        echo json_encode($values);
        break;
    case 'insertar_colaborador':
        $colaborador = new Colaborador;
        $colaborador->nombres = $_POST['nombres'];
        $colaborador->email = $_POST['email'];
        $colaborador->id_rol = $_POST['id_rol'];
        $colaborador->n_documento = $_POST['n_documento'];
        $colaborador->apellido_paterno = $_POST['apellido_paterno'];
        $colaborador->apellido_materno = $_POST['apellido_materno'];
        $colaborador->id_tipo_documento = $_POST['id_tipo_documento'];
        $colaborador->fecha_nacimiento = $_POST['fecha_nacimiento'];
        $colaborador->celular = $_POST['celular'];
        echo $mono->insert_data_v2("colaboradores", $colaborador);
        break;
    case 'editar_colaborador':
        echo $mono->select_one("colaboradores", array("id" => $_POST['id']));
        break;
    case 'actualizar_colaborador':
        $colaborador = new Colaborador;
        $colaborador->nombres = $_POST['nombres'];
        $colaborador->email = $_POST['email'];
        $colaborador->id_rol = $_POST['id_rol'];
        $colaborador->n_documento = $_POST['n_documento'];
        $colaborador->apellido_paterno = $_POST['apellido_paterno'];
        $colaborador->apellido_materno = $_POST['apellido_materno'];
        $colaborador->id_tipo_documento = $_POST['id_tipo_documento'];
        $colaborador->fecha_nacimiento = $_POST['fecha_nacimiento'];
        $colaborador->celular = $_POST['celular'];
        $colaborador->id = $_POST['id'];
        echo $mono->update_data_v2("colaboradores", $colaborador);
        break;
    case 'eliminar_colaborador':
        echo $mono->delete_data("colaboradores", array("id" => $_POST['id']));
        break;
    default:
        # code...
        break;
}
