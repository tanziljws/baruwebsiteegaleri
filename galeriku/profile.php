<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Fetch user statistics
    $stmtStats = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM images WHERE user_id = ?) as total_images,
            (SELECT COUNT(*) FROM likes WHERE image_id IN (SELECT id FROM images WHERE user_id = ?)) as total_likes,
            (SELECT COUNT(*) FROM followers WHERE followed_id = ?) as followers,
            (SELECT COUNT(*) FROM followers WHERE follower_id = ?) as following
    ");
    $stmtStats->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
    $stats = $stmtStats->fetch();

    // Fetch user's images
    $stmtImages = $pdo->prepare("
        SELECT images.*, 
               COUNT(DISTINCT likes.id) as like_count,
               COUNT(DISTINCT comments.id) as comment_count
        FROM images 
        LEFT JOIN likes ON images.id = likes.image_id
        LEFT JOIN comments ON images.id = comments.image_id
        WHERE images.user_id = ?
        GROUP BY images.id
        ORDER BY images.created_at DESC
    ");
    $stmtImages->execute([$_SESSION['user_id']]);
    $images = $stmtImages->fetchAll();

} catch(PDOException $e) {
    $error = "Error fetching user data";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user['username']); ?> - Pixify</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Include your navigation here -->

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-cover">
                <img src="<?php echo $user['cover_image'] ?? 'assets/default-cover.jpg'; ?>" alt="Cover">
                <button class="edit-cover"><i class="fas fa-camera"></i></button>
            </div>
            
            <div class="profile-info">
                <div class="profile-avatar">
                    <img src="<?php echo $user['avatar'] ?? 'assets/default-avatar.jpg'; ?>" alt="Avatar">
                    <button class="edit-avatar"><i class="fas fa-camera"></i></button>
                </div>
                
                <div class="profile-details">
                    <h1><?php echo htmlspecialchars($user['username']); ?></h1>
                    <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet'); ?></p>
                    <div class="profile-stats">
                        <div class="stat">
                            <span class="stat-value"><?php echo $stats['total_images']; ?></span>
                            <span class="stat-label">Posts</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $stats['total_likes']; ?></span>
                            <span class="stat-label">Likes</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $stats['followers']; ?></span>
                            <span class="stat-label">Followers</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $stats['following']; ?></span>
                            <span class="stat-label">Following</span>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <a href="settings.php" class="edit-profile-btn">
                            <i class="fas fa-cog"></i> Edit Profile
                        </a>
                        <?php if($user['subscription_status'] === 'free'): ?>
                        <a href="pricing.php" class="upgrade-btn">
                            <i class="fas fa-crown"></i> Upgrade to Pro
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Navigation -->
        <div class="profile-nav">
            <button class="active" data-tab="posts">
                <i class="fas fa-images"></i> Posts
            </button>
            <button data-tab="collections">
                <i class="fas fa-folder"></i> Collections
            </button>
            <button data-tab="liked">
                <i class="fas fa-heart"></i> Liked
            </button>
            <button data-tab="stats">
                <i class="fas fa-chart-bar"></i> Statistics
            </button>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Posts Tab -->
            <div class="tab-content active" id="posts">
                <div class="gallery-grid">
                    <?php foreach($images as $image): ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($image['title']); ?>">
                        <div class="gallery-item-info">
                            <div class="gallery-item-stats">
                                <span><i class="fas fa-heart"></i> <?php echo $image['like_count']; ?></span>
                                <span><i class="fas fa-comment"></i> <?php echo $image['comment_count']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Collections Tab -->
            <div class="tab-content" id="collections">
                <div class="collections-grid">
                    <!-- Add your collections content here -->
                </div>
            </div>

            <!-- Liked Tab -->
            <div class="tab-content" id="liked">
                <!-- Add your liked images content here -->
            </div>

            <!-- Statistics Tab -->
            <div class="tab-content" id="stats">
                <div class="stats-container">
                    <div class="stats-card">
                        <h3>Engagement Overview</h3>
                        <canvas id="engagementChart"></canvas>
                    </div>
                    <div class="stats-card">
                        <h3>Popular Posts</h3>
                        <!-- Add popular posts list -->
                    </div>
                    <div class="stats-card">
                        <h3>Growth Metrics</h3>
                        <!-- Add growth metrics -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Profile Image</h2>
            <form id="imageUploadForm">
                <input type="file" accept="image/*" required>
                <button type="submit">Upload</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>