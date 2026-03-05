<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

try {
    $pdo = new PDO(getenv('DATABASE_URL'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? null;
    $commentId = $data['comment_id'] ?? null; // Nouveau : support des commentaires
    $voteType = $data['vote_type'] ?? 0;

    $authorId = $pdo->query("SELECT id FROM profiles LIMIT 1")->fetchColumn();

    if ($commentId) {
        // Logique Vote Commentaire
        $stmt = $pdo->prepare("
            INSERT INTO comment_votes (comment_id, author_id, vote_type) 
            VALUES (?, ?, ?)
            ON CONFLICT (comment_id, author_id) 
            DO UPDATE SET vote_type = EXCLUDED.vote_type
        ");
        $stmt->execute([$commentId, $authorId, $voteType]);
    } else {
        // Logique Vote Post
        $stmt = $pdo->prepare("
            INSERT INTO post_votes (post_id, author_id, vote_type) 
            VALUES (?, ?, ?)
            ON CONFLICT (post_id, author_id) 
            DO UPDATE SET vote_type = EXCLUDED.vote_type
        ");
        $stmt->execute([$postId, $authorId, $voteType]);
    }

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}