<?php
$dsn = getenv('DATABASE_URL');
$pdo = new PDO($dsn);

// 1. Créer quelques profils
$usernames = ['leokd', 'agora_user', 'tech_expert', 'europe_dev', 'reddit_fan'];
$profileIds = [];

foreach ($usernames as $name) {
    $stmt = $pdo->prepare("INSERT INTO profiles (username) VALUES (?) ON CONFLICT (username) DO UPDATE SET username = EXCLUDED.username RETURNING id");
    $stmt->execute([$name]);
    $profileIds[] = $stmt->fetchColumn();
}

// 2. Générer des posts avec des votes variés pour tester les tris
echo "Génération de posts...\n";
for ($i = 0; $i < 50; $i++) {
    $authorId = $profileIds[array_rand($profileIds)];
    $up = rand(0, 500);
    $down = rand(0, 500);
    $isStory = (rand(0, 10) > 8) ? 'true' : 'false';
    
    $stmt = $pdo->prepare("INSERT INTO posts (author_id, content, upvotes, downvotes, is_story, created_at) VALUES (?, ?, ?, ?, $isStory, NOW() - INTERVAL '$i hours')");
    $stmt->execute([
        $authorId, 
        "Ceci est le message Agora numéro $i. Souveraineté numérique !",
        $up,
        $down
    ]);
}
echo "✅ Seed terminé !";