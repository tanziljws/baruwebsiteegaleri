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
    $type = $_POST['type'] ?? ''; // 'avatar' or 'cover'
    $file = $_FILES['image'] ?? null;
    
    if (!$file) {
        http_response_code(400);
        echo json_encode(['error' => 'No file uploaded']);
        exit;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }
    
    try {
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $uploadDir = '../uploads/profile/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $column = $type === 'avatar' ? 'avatar' : 'cover_image';
            
            $stmt = $pdo->prepare("UPDATE users SET $column = ? WHERE id = ?");
            $stmt->execute(['/uploads/profile/' . $filename, $_SESSION['user_id']]);
            
            echo json_encode([
                'success' => true,
                'url' => '/uploads/profile/' . $filename
            ]);
        } else {
            throw new Exception('Failed to upload file');
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Upload failed']);
    }
}