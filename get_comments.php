<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dsn = getenv('DATABASE_URL');
$pdo = new PDO($dsn);

$postId = $_GET['post_id'] ?? null;

if (!$postId) {
    echo json_encode([]);
    exit;
}

// On utilise LTREE pour récupérer l'arbre complet trié par chemin
$stmt = $pdo->prepare("
    SELECT c.*, p.username 
    FROM comments c 
    JOIN profiles p ON c.author_id = p.id 
    WHERE c.post_id = ? 
    ORDER BY c.path ASC
");
$stmt->execute([$postId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));