<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - Pixify</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/pricing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-left">
            <div class="logo">
                <i class="fas fa-camera-retro"></i>
                <span>Pixify</span>
            </div>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="upload.php"><i class="fas fa-cloud-upload-alt"></i> Upload</a>
                <a href="my_images.php"><i class="fas fa-images"></i> My Gallery</a>
                <span class="username">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">Log in</a>
                <a href="register.php" class="signup-btn">Sign up free</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Pricing Header -->
    <div class="pricing-header">
        <h1>Choose Your Perfect Plan</h1>
        <p class="pricing-subtitle">Get access to advanced features and unlimited uploads with our premium plans</p>
        
        <div class="pricing-toggle">
            <button class="toggle-button active">Monthly</button>
            <button class="toggle-button">Yearly</button>
        </div>
    </div>

    <!-- Pricing Plans -->
    <div class="pricing-container">
        <!-- Free Plan -->
        <div class="pricing-plan">
            <h2 class="plan-name">Free</h2>
            <div class="plan-price">
                $0 <small>/month</small>
            </div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> 10 uploads per month</li>
                <li><i class="fas fa-check"></i> Basic editing tools</li>
                <li><i class="fas fa-check"></i> Community support</li>
                <li><i class="fas fa-check"></i> Ad-supported</li>
            </ul>
            <a href="register.php" class="plan-button outlined">Get Started</a>
        </div>

        <!-- Pro Plan -->
        <div class="pricing-plan">
            <div class="popular-badge">Most Popular</div>
            <h2 class="plan-name">Pro</h2>
            <div class="plan-price">
                $9.99 <small>/month</small>
            </div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Unlimited uploads</li>
                <li><i class="fas fa-check"></i> Advanced editing tools</li>
                <li><i class="fas fa-check"></i> Priority support</li>
                <li><i class="fas fa-check"></i> Ad-free experience</li>
                <li><i class="fas fa-check"></i> Analytics dashboard</li>
            </ul>
            <a href="checkout.php?plan=pro" class="plan-button">Upgrade Now</a>
        </div>

        <!-- Business Plan -->
        <div class="pricing-plan">
            <h2 class="plan-name">Business</h2>
            <div class="plan-price">
                $24.99 <small>/month</small>
            </div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Everything in Pro</li>
                <li><i class="fas fa-check"></i> Team collaboration</li>
                <li><i class="fas fa-check"></i> API access</li>
                <li><i class="fas fa-check"></i> Custom branding</li>
                <li><i class="fas fa-check"></i> 24/7 support</li>
            </ul>
            <a href="checkout.php?plan=business" class="plan-button">Choose Business</a>
        </div>
    </div>

    <!-- Enterprise Section -->
    <div class="enterprise-section">
        <div class="enterprise-content">
            <h2>Need a Custom Solution?</h2>
            <p>Contact us for enterprise-grade solutions tailored to your specific needs</p>
            <a href="contact.php" class="contact-button">Contact Sales</a>
        </div>
    </div>

    <script>
        // Toggle between monthly and yearly pricing
        const toggleButtons = document.querySelectorAll('.toggle-button');
        const prices = {
            pro: {
                monthly: '$9.99',
                yearly: '$99.99'
            },
            business: {
                monthly: '$24.99',
                yearly: '$249.99'
            }
        };

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                toggleButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const isYearly = this.textContent === 'Yearly';
                const priceElements = document.querySelectorAll('.plan-price');
                
                // Update Pro plan price
                priceElements[1].innerHTML = `${isYearly ? prices.pro.yearly : prices.pro.monthly} <small>/${isYearly ? 'year' : 'month'}</small>`;
                
                // Update Business plan price
                priceElements[2].innerHTML = `${isYearly ? prices.business.yearly : prices.business.monthly} <small>/${isYearly ? 'year' : 'month'}</small>`;
            });
        });
    </script>
</body>
</html>