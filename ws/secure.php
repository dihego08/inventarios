<?php
//require 'jwt.php'; // Archivo con funciones JWT

// Obtener encabezado de autorización
$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Acceso denegado. Falta el token."]);
    http_response_code(401);
    exit;
}

// Extraer token del encabezado
$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);

// Validar token
try {
    $decoded = verifyJWT($token, $secret_key); // Usa la misma clave secreta del `jwt.php`
    //$_SESSION['id'] = $decoded->id_usuario; // Puedes almacenar el ID del usuario en la sesión
    //return true;
} catch (Exception $e) {
    echo json_encode(["error" => "Token inválido o expirado."]);
    http_response_code(401);
    exit;
}
?>
