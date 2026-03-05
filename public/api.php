<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $dsn = getenv('DATABASE_URL');
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sort = $_GET['sort'] ?? 'recent';

    $orderBy = match($sort) {
        'top' => 'score DESC',
        'controversial' => 'total_votes DESC, ABS(COALESCE(SUM(v.vote_type), 0)) ASC',
        default => 'p.created_at DESC',
    };

    // LA REQUÊTE CORRIGÉE
    $stmt = $pdo->prepare("
        SELECT 
            p.*, 
            pr.username,
            COALESCE(SUM(v.vote_type), 0) as score,
            COUNT(DISTINCT v.id) as total_votes,
            COUNT(DISTINCT c.id) as comment_count
        FROM posts p
        JOIN profiles pr ON p.author_id = pr.id
        LEFT JOIN post_votes v ON p.id = v.post_id
        LEFT JOIN comments c ON p.id = c.post_id
        WHERE p.is_story = FALSE
        GROUP BY p.id, pr.username
        ORDER BY $orderBy
    ");
    
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}