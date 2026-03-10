<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$storageDir = __DIR__ . '/uploads/';
if (!is_dir($storageDir)) mkdir($storageDir, 0777, true);

try {
    if (!isset($_FILES['file'])) throw new Exception("Aucun fichier reçu"); // Svelte envoie 'file' dans FormData

    $file = $_FILES['file'];
    $tmpPath = $file['tmp_name'];

    // 1. SÉCURITÉ : Vérification MIME
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpPath);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedTypes)) throw new Exception("Format non supporté");

    // 2. NOM CHIFFRÉ (Anonymisation)
    $newName = bin2hex(random_bytes(16)) . '.webp';
    $finalPath = $storageDir . $newName;

    // 3. CHARGEMENT DE L'IMAGE
    $src = match($mimeType) {
        'image/jpeg' => imagecreatefromjpeg($tmpPath),
        'image/png'  => imagecreatefrompng($tmpPath),
        'image/webp' => imagecreatefromwebp($tmpPath),
    };

    // 4. REDIMENSIONNEMENT INTELLIGENT (Crop carré 400x400)
    $width = imagesx($src);
    $height = imagesy($src);
    $size = min($width, $height);
    $dest = imagecreatetruecolor(400, 400);

    // On active la transparence pour WebP
    imagealphablending($dest, false);
    imagesavealpha($dest, true);

    // Recadrage au centre
    imagecopyresampled(
        $dest, $src, 
        0, 0, ($width - $size) / 2, ($height - $size) / 2, 
        400, 400, $size, $size
    );

    // 5. SAUVEGARDE WEBP (Compression 80%)
    imagewebp($dest, $finalPath, 80);
    
    imagedestroy($src);
    imagedestroy($dest);

    echo json_encode([
        'status' => 'success',
        'url' => '/uploads/' . $newName
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}