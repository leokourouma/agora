<?php
// On récupère l'URL configurée dans le docker-compose
$dsn = getenv('DATABASE_URL'); 
try {
    $pdo = new PDO($dsn, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Insertion d'un profil de test
    $stmt = $pdo->prepare("
        INSERT INTO profiles (username, first_name, last_name, bio) 
        VALUES (?, ?, ?, ?) 
        ON CONFLICT (username) DO UPDATE SET bio = EXCLUDED.bio
        RETURNING id
    ");
    $stmt->execute(['leokd', 'Leo', 'Kourouma', 'Bâtisseur de l\'Agora']);
    
    echo "✅ Base de données initialisée et profil créé !";
} catch (Exception $e) {
    echo "❌ Erreur de seed : " . $e->getMessage();
}