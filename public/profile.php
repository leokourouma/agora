<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$dsn = getenv('DATABASE_URL');
$pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_GET['id'] ?? null; 

if ($method === 'GET' && $userId) {
    // On cherche par USER_ID (celui du token)
    $stmt = $pdo->prepare("
        SELECT p.*, 
               (SELECT json_agg(a.*) FROM albums a WHERE a.profile_id = p.id) as albums
        FROM profiles p WHERE p.user_id = ?
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $result['visibility_settings'] = json_decode($result['visibility_settings'] ?? '{}');
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Profil non trouvé']);
    }
} 

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("
        UPDATE profiles 
        SET first_name = ?, last_name = ?, bio = ?, visibility_settings = ? 
        WHERE user_id = ?
    ");
    $stmt->execute([
        $data['first_name'] ?? null, 
        $data['last_name'] ?? null, 
        $data['bio'] ?? null, 
        json_encode($data['visibility_settings'] ?? []), 
        $data['user_id']
    ]);
    echo json_encode(['success' => true]);
}