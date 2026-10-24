<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Nuustar Events' : 'Nuustar Events - Central Hub for All Event Services in Tanzania'; ?></title>
    <!-- Main Stylesheet (with cache-buster) -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : time(); ?>">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- Navigation Header -->
<header class="navbar">
    <div class="container nav-container">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <img src="uploads/logo.png" alt="Nuu Events" style="height: 48px; width: 48px; object-fit: contain; border-radius: 50%;">
            <span>Nuustar<span style="color: var(--accent);">Events</span></span>
            <span class="logo-badge">Tanzania</span>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="nav-links" id="navLinks">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <a href="vendors.php"><i class="fa-solid fa-magnifying-glass"></i> Explore Services</a>
            <a href="post-request.php"><i class="fa-solid fa-clipboard-list"></i> Post an Event</a>
            <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'customer'): ?>
                <a href="customer-dashboard.php"><i class="fa-solid fa-receipt"></i> My Bookings</a>
            <?php endif; ?>
        </nav>

        <!-- Right Action Buttons -->
        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                    <a href="admin/dashboard.php" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-user-shield"></i> Admin Control
                    </a>
                <?php elseif (($_SESSION['user_role'] ?? '') === 'vendor'): ?>
                    <a href="vendor/dashboard.php" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-store"></i> Vendor Portal
                    </a>
                <?php elseif (($_SESSION['user_role'] ?? '') === 'customer'): ?>
                    <a href="customer-dashboard.php" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-user-check"></i> My Dashboard
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </a>
            <?php else: ?>
                <a href="register.php?type=vendor" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-store"></i> Join as a Vendor
                </a>
                <a href="login.php" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                </a>
            <?php endif; ?>
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>
