<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$dsn = getenv('DATABASE_URL');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($data['email'])) {
    try {
        $pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data['password'], $user['password_hash'])) {
            echo json_encode(["success" => true, "token" => $user['id']]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Identifiants invalides."]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur serveur."]);
    }
}