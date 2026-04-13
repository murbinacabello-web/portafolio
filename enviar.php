<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if (empty($nombre) || empty($correo) || empty($mensaje)) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son requeridos']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Correo inválido']);
    exit;
}

if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/', $nombre)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre inválido']);
    exit;
}

if (strlen($nombre) > 100 || strlen($correo) > 50 || strlen($mensaje) > 300) {
    http_response_code(400);
    echo json_encode(['error' => 'Excede límites permitidos']);
    exit;
}

$destinatario = 'murbinacabello@gmail.com';
$asunto = 'Nuevo mensaje desde portafolio - ' . htmlspecialchars($nombre);
$cuerpo = "Nombre: " . htmlspecialchars($nombre) . "\n";
$cuerpo .= "Correo: " . htmlspecialchars($correo) . "\n";
$cuerpo .= "Mensaje: " . htmlspecialchars($mensaje) . "\n";

$headers = "From: no-reply@portafolio.local\r\n";
$headers .= "Reply-To: " . $correo . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($destinatario, $asunto, $cuerpo, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al enviar el mensaje']);
}