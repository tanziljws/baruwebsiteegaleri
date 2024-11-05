<?php
session_start();
require_once 'config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixify - Creative Image Platform</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo !isset($_SESSION['user_id']) ? 'landing-page' : ''; ?>">

    <!-- Premium Banner for Non-logged Users -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="premium-banner">
            <span>✨ Join Pixify Pro for unlimited uploads and exclusive features!</span>
            <a href="pricing.php" class="premium-link">Learn More <i class="fas fa-arrow-right"></i></a>
        </div>
    <?php endif; ?>

    <!-- Enhanced Navigation -->
    <nav>
        <div class="nav-left">
            <div class="logo">
                <i class="fas fa-camera-retro"></i>
                <span>Pixify</span>
            </div>
        </div>
        <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="upload.php" class="upload-btn"><i class="fas fa-cloud-upload-alt"></i> Upload</a>
                <a href="my_images.php"><i class="fas fa-images"></i> My Gallery</a>
                
                <span class="username">
    <i class="fas fa-user-circle"></i>
    <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="username-link">
        <?php echo htmlspecialchars($_SESSION['username']); ?>
    </a>
</span>

                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">Log in</a>
                <a href="register.php" class="signup-btn">Sign up free</a>
            <?php endif; ?>
        </div>
    </nav>

    

    <!-- Hero Section for Non-logged Users -->
<!-- index.php -->
<?php if (!isset($_SESSION['user_id'])): ?>
    <!-- Premium Banner -->
    <div class="premium-banner">
        <span>✨ Join Pixify Pro for unlimited uploads and exclusive features!</span>
        <a href="pricing.php" class="learn-more">Learn More →</a>
    </div>

    <!-- Main Landing Content -->
    <div class="landing-hero">
        <div class="hero-content">
            <h1>Get your next</h1>
            <div class="dynamic-text">
                <span class="active">inspiration</span>
                <span>creative idea</span>
                <span>perfect shot</span>
            </div>
            <div class="hero-buttons">
                <a href="register.php" class="signup-btn">Sign up</a>
                <a href="login.php" class="login-btn">Log in</a>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <form action="" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search amazing images...">
            </div>
            <button type="submit" class="search-btn">Search</button>
        </form>

        <!-- Category Pills -->
        <div class="category-pills">
            <button class="pill active">All</button>
            <button class="pill">Photography</button>
            <button class="pill">Digital Art</button>
            <button class="pill">Illustrations</button>
        </div>
    </div>
<?php endif; ?>

    <!-- Search Bar -->
    <div class="search-container">
        <form action="" method="GET">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search amazing images..."
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </div>
        </form>
    </div>

    <!-- Category Filters -->
    <div class="category-filters">
        <button class="filter-btn active">All</button>
        <button class="filter-btn">Photography</button>
        <button class="filter-btn">Digital Art</button>
        <button class="filter-btn">Illustrations</button>
    </div>

    <!-- Image Gallery -->
    <div class="gallery <?php echo !isset($_SESSION['user_id']) ? 'landing-gallery' : ''; ?>">
        <?php
        try {
            // Search query if search is active
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = '%' . $_GET['search'] . '%';
                $stmt = $pdo->prepare("SELECT images.*, users.username 
                                       FROM images 
                                       JOIN users ON images.user_id = users.id 
                                       WHERE title LIKE ? OR description LIKE ? 
                                       ORDER BY images.created_at DESC");
                $stmt->execute([$search, $search]);
            } else {
                // Default query to fetch all images
                $stmt = $pdo->prepare("SELECT images.*, users.username 
                                       FROM images 
                                       JOIN users ON images.user_id = users.id 
                                       ORDER BY images.created_at DESC");
                $stmt->execute();
            }

            $hasImages = false;
            while ($row = $stmt->fetch()) {
                $hasImages = true;
                ?>
                <div class="pin">
                    <div class="pin-image" 
                         onclick="openModal('<?php echo htmlspecialchars($row['image_url']); ?>', 
                                             '<?php echo htmlspecialchars(addslashes($row['title'])); ?>', 
                                             '<?php echo htmlspecialchars(addslashes($row['description'])); ?>', 
                                             '<?php echo htmlspecialchars($row['username']); ?>', 
                                             <?php echo $row['id']; ?>, 
                                             <?php echo $row['user_id']; ?>, 
                                             <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>)">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>">
                    </div>
                    <div class="pin-info">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                        <p class="posted-by">Posted by: <?php echo htmlspecialchars($row['username']); ?></p>
                        <?php if (isset($_SESSION['user_id']) && $row['user_id'] == $_SESSION['user_id']): ?>
                            <div class="pin-actions">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }

            if (!$hasImages) {
                echo '<div class="no-images">No images found. Be the first to upload!</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="error">Error loading images.</div>';
        }
        ?>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-flex">
                <div class="modal-image-container">
                    <img id="modalImage" src="" alt="">
                </div>
                <div class="modal-info">
                    <h2 id="modalTitle"></h2>
                    <p id="modalDescription" class="description"></p>
                    <div class="modal-stats">
                        <div class="likes">
                            <button class="like-btn" id="likeButton">
                                <i class="far fa-heart"></i>
                                <span id="likeCount">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="comments-section">
                        <h3>Comments</h3>
                        <div class="comments-container" id="commentsContainer"></div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form id="commentForm" class="comment-form">
                                <input type="hidden" id="imageId" value="">
                                <textarea placeholder="Add a comment..." required></textarea>
                                <button type="submit">Post</button>
                            </form>
                        <?php else: ?>
                            <p class="login-to-comment">Please <a href="login.php">login</a> to comment</p>
                        <?php endif; ?>
                    </div>
                    <p id="modalPostedBy" class="posted-by"></p>
                    <div id="modalActions" class="pin-actions"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Your existing styles here */
        /* Add the new modal and comments styles I provided earlier */
    </style>

    <script src="js/main.js"></script>
</body>
</html>