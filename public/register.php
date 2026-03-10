<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$dsn = getenv('DATABASE_URL');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($data['email']) && !empty($data['password'])) {
    try {
        $pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?) RETURNING id");
        $stmt->execute([$data['email'], $hash]);
        $userId = $stmt->fetchColumn();

        echo json_encode(["success" => true, "token" => $userId]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["error" => "Email déjà utilisé ou erreur SQL."]);
    }
}