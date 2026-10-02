<?php
// mostrar_foto.php
// La carpeta uploaded_files/ no es accesible directamente desde el navegador (ver .htaccess).
// Este script sirve una foto puntual bajo demanda, validando primero el nombre de archivo.

$carpeta = __DIR__ . '/uploaded_files/';
$archivo = basename($_GET['f'] ?? ''); // basename() evita path traversal (ej: ../../wp-config.php)
$ruta = $carpeta . $archivo;

$tiposPermitidos = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
];
$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

if ($archivo === '' || !isset($tiposPermitidos[$extension]) || !is_file($ruta)) {
    http_response_code(404);
    exit('Imagen no encontrada.');
}

header('Content-Type: ' . $tiposPermitidos[$extension]);
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
exit;
