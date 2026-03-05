<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

try {
    $pdo = new PDO(getenv('DATABASE_URL'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $authorId = $pdo->query("SELECT id FROM profiles LIMIT 1")->fetchColumn();
        
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, parent_id, author_id, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['post_id'], $data['parent_id'] ?? null, $authorId, $data['content']]);
        echo json_encode(['status' => 'success']);

    } else if ($method === 'GET') {
        $postId = $_GET['post_id'];
        $stmt = $pdo->prepare("
            SELECT c.*, pr.username, 
                   COALESCE(SUM(v.vote_type), 0) as score
            FROM comments c
            JOIN profiles pr ON c.author_id = pr.id
            LEFT JOIN comment_votes v ON c.id = v.comment_id
            WHERE c.post_id = ?
            GROUP BY c.id, pr.username
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$postId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}