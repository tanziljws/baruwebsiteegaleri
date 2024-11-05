<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Update basic info
        $stmt = $pdo->prepare("
            UPDATE users 
            SET bio = ?, 
                website = ?, 
                location = ?,
                social_links = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['bio'] ?? null,
            $data['website'] ?? null,
            $data['location'] ?? null,
            json_encode($data['social_links'] ?? []),
            $_SESSION['user_id']
        ]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                users.*,
                COUNT(DISTINCT followers.id) as followers_count,
                COUNT(DISTINCT following.id) as following_count,
                COUNT(DISTINCT images.id) as images_count,
                COUNT(DISTINCT likes.id) as total_likes
            FROM users
            LEFT JOIN followers ON users.id = followers.followed_id
            LEFT JOIN followers following ON users.id = following.follower_id
            LEFT JOIN images ON users.id = images.user_id
            LEFT JOIN likes ON images.id = likes.image_id
            WHERE users.id = ?
            GROUP BY users.id
        ");
        
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Remove sensitive information
        unset($userData['password']);
        
        echo json_encode($userData);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}