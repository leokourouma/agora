<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

try {
    // 1. Connexion (on crée l'objet PDO d'abord !)
    $dsn = getenv('DATABASE_URL');
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    // 2. Récupération d'un ID valide
    $profileStmt = $pdo->query("SELECT id FROM profiles LIMIT 1");
    $validAuthorId = $profileStmt->fetchColumn();

    if (!$validAuthorId) {
        throw new Exception("La table 'profiles' est vide. Lance le seed.php !");
    }

    // 3. Traitement du POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $pdo->prepare("
            INSERT INTO posts (author_id, content, media_url, is_story) 
            VALUES (:author, :content, :media, :story)
        ");
        
        // On force les types pour éviter l'erreur Postgres 22P02
        $isStory = (isset($data['is_story']) && $data['is_story'] === true);

        $stmt->bindValue(':author', $validAuthorId);
        $stmt->bindValue(':content', $data['content'] ?? '');
        $stmt->bindValue(':media', $data['media_url'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':story', $isStory, PDO::PARAM_BOOL);

        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Post créé avec succès']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}