<?php
$secret_key = "IBEEcywkw8eLoLw"; // Usa una clave segura y guárdala bien

// Función para generar un JWT manualmente
function generateJWT($payload, $secret_key) {
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload = json_encode($payload);

    // Codificar en Base64URL
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    // Crear la firma
    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret_key, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    // Construir el token
    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
}

// Función para verificar un JWT
function verifyJWT($token, $secret_key) {
    $parts = explode('.', $token);

    if (count($parts) !== 3) {
        return null; // Token inválido
    }

    [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = $parts;

    // Verificar firma
    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret_key, true);
    $validSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    if ($base64UrlSignature !== $validSignature) {
        return null; // Firma inválida
    }

    // Decodificar payload
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);

    // Verificar expiración
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return null; // Token expirado
    }

    return $payload; // Token válido
}
?>
