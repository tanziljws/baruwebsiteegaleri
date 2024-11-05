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
} catch(PDOException $e) {
    $error = "Error fetching user data";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Pixify</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/settings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Include your navigation here -->

    <div class="settings-container">
        <div class="settings-sidebar">
            <h2>Settings</h2>
            <nav class="settings-nav">
                <a href="#profile" class="active" data-tab="profile">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="#account" data-tab="account">
                    <i class="fas fa-cog"></i> Account
                </a>
                <a href="#notifications" data-tab="notifications">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <a href="#privacy" data-tab="privacy">
                    <i class="fas fa-lock"></i> Privacy
                </a>
                <a href="#subscription" data-tab="subscription">
                    <i class="fas fa-crown"></i> Subscription
                </a>
                <?php if($user['subscription_status'] === 'free'): ?>
                    <a href="pricing.php" class="upgrade-btn">
                        <i class="fas fa-star"></i> Upgrade to Pro
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="settings-content">
            <!-- Profile Settings -->
            <div class="settings-section active" id="profile">
                <h2>Profile Settings</h2>
                <form id="profileForm" class="settings-form">
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <div class="avatar-upload">
                            <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'assets/default-avatar.jpg'); ?>" 
                                 alt="Profile Picture" id="avatarPreview">
                            <input type="file" id="avatarInput" accept="image/*">
                            <button type="button" class="upload-btn">Change Photo</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" 
                               value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" 
                               value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </div>

            <!-- Account Settings -->
            <div class="settings-section" id="account">
                <h2>Account Settings</h2>
                <form id="accountForm" class="settings-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword">
                    </div>

                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword">
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword">
                    </div>

                    <button type="submit" class="save-btn">Update Account</button>
                </form>

                <div class="danger-zone">
                    <h3>Danger Zone</h3>
                    <button type="button" class="delete-account-btn" onclick="confirmDeleteAccount()">
                        Delete Account
                    </button>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="settings-section" id="notifications">
                <h2>Notification Settings</h2>
                <form id="notificationForm" class="settings-form">
                    <div class="notification-group">
                        <h3>Email Notifications</h3>
                        <label class="toggle-switch">
                            <input type="checkbox" name="email_likes" checked>
                            <span class="slider"></span>
                            Likes on your posts
                        </label>

                        <label class="toggle-switch">
                            <input type="checkbox" name="email_comments" checked>
                            <span class="slider"></span>
                            Comments on your posts
                        </label>

                        <label class="toggle-switch">
                            <input type="checkbox" name="email_followers">
                            <span class="slider"></span>
                            New followers
                        </label>

                        <label class="toggle-switch">
                            <input type="checkbox" name="email_newsletter" checked>
                            <span class="slider"></span>
                            Newsletter and updates
                        </label>
                    </div>

                    <button type="submit" class="save-btn">Save Preferences</button>
                </form>
            </div>

            <!-- Privacy Settings -->
            <div class="settings-section" id="privacy">
                <h2>Privacy Settings</h2>
                <form id="privacyForm" class="settings-form">
                    <div class="privacy-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="private_account">
                            <span class="slider"></span>
                            Private Account
                        </label>
                        <p class="setting-description">Only approved followers can see your posts</p>

                        <label class="toggle-switch">
                            <input type="checkbox" name="show_activity" checked>
                            <span class="slider"></span>
                            Show Activity Status
                        </label>
                        <p class="setting-description">Let others see when you're active</p>

                        <label class="toggle-switch">
                            <input type="checkbox" name="show_location" checked>
                            <span class="slider"></span>
                            Show Location
                        </label>
                        <p class="setting-description">Display your location on your profile</p>
                    </div>

                    <button type="submit" class="save-btn">Save Privacy Settings</button>
                </form>
            </div>

            <!-- Subscription Settings -->
            <div class="settings-section" id="subscription">
                <h2>Subscription Details</h2>
                <div class="subscription-info">
                    <div class="current-plan">
                        <h3>Current Plan</h3>
                        <div class="plan-details">
                            <span class="plan-name">
                                <?php echo ucfirst($user['subscription_status']); ?> Plan
                            </span>
                            <?php if($user['subscription_status'] !== 'free'): ?>
                                <span class="plan-price">$9.99/month</span>
                                <span class="renewal-date">Renews on: Jan 1, 2024</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($user['subscription_status'] === 'free'): ?>
                        <a href="pricing.php" class="upgrade-btn">Upgrade to Pro</a>
                    <?php else: ?>
                        <button class="cancel-subscription-btn">Cancel Subscription</button>
                    <?php endif; ?>

                    <div class="billing-history">
                        <h3>Billing History</h3>
                        <table class="billing-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Add your billing history rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.settings-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links and sections
                document.querySelectorAll('.settings-nav a').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
                
                // Add active class to clicked link and corresponding section
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        // Profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('api/update_profile.php', {
                    method: 'POST',
                    body: new FormData(this)
                });
                
                if (response.ok) {
                    showNotification('Profile updated successfully');
                } else {
                    throw new Error('Failed to update profile');
                }
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });

        // Avatar upload preview
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Delete account confirmation
        function confirmDeleteAccount() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                // Add your delete account logic here
            }
        }

        // Notification function
        function showNotification(message, type = 'success') {
            // Add your notification logic here
        }
    </script>
</body>
</html>