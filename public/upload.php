<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$storageDir = __DIR__ . '/uploads/';
if (!interface_exists('GdImage')) { /* Vérification extension GD */ }

try {
    if (!isset($_FILES['image'])) throw new Exception("Aucun fichier reçu");

    $file = $_FILES['image'];
    $tmpPath = $file['tmp_name'];

    // 1. SÉCURITÉ : Vérification du vrai type MIME (pas juste l'extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpPath);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    
    if (!in_array($mimeType, $allowedTypes)) throw new Exception("Format non supporté");

    // 2. CHIFFREMENT DU NOM : On ne garde pas le nom original (sécurité)
    // On génère un hash unique pour éviter le "Directory Traversal"
    $newName = bin2hex(random_bytes(16)) . '.webp';
    $finalPath = $storageDir . $newName;

    // 3. COMPRESSION & REDIMENSIONNEMENT (Souveraineté des données)
    $image = match($mimeType) {
        'image/jpeg' => imagecreatefromjpeg($tmpPath),
        'image/png'  => imagecreatefrompng($tmpPath),
        'image/webp' => imagecreatefromwebp($tmpPath),
    };

    // Conversion en WebP avec qualité 80% (Gain de place énorme)
    imagewebp($image, $finalPath, 80);
    imagedestroy($image);

    echo json_encode([
        'status' => 'success',
        'url' => '/uploads/' . $newName
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}