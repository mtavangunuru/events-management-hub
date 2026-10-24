<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';

// Security check: Must be logged in as admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit;
}

// Fetch live counts for sidebar badges
$badgePendingCount = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles WHERE verification_status = 'pending'")->fetchColumn();
$badgePendingUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$badgePendingPayouts = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE payment_status = 'paid' AND payout_status = 'pending'")->fetchColumn();
$badgeOpenRequests = (int)$pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'open'")->fetchColumn();
$currentAdminName = $_SESSION['user_name'] ?? 'Super Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Nuustar Admin' : 'Admin Control - Nuustar Events'; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .admin-table-actions {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-tab-btn {
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            background: #fff;
            border: 1px solid var(--border);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }
        .filter-tab-btn:hover, .filter-tab-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .badge-status {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-verified-status {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .badge-pending-status {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-rejected-status {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

<!-- Admin Header -->
<header class="navbar" style="position: static; background: #0a2540; border-bottom: 2px solid var(--accent);">
    <div class="container nav-container" style="max-width: 100%; padding: 0 30px;">
        <a href="dashboard.php" class="logo" style="color: #fff; display: flex; align-items: center; gap: 10px;">
            <img src="../uploads/logo.png" alt="Nuustar Events" style="height: 42px; width: 42px; object-fit: contain; border-radius: 50%; border: 2px solid var(--accent);">
            <span>Nuustar<span style="color: var(--accent);">Admin</span></span>
            <span class="logo-badge" style="background: #dc2626;">Super Admin</span>
        </a>
        <div style="display: flex; align-items: center; gap: 20px; color: #fff;">
            <span><i class="fa-solid fa-user-shield" style="color: var(--accent);"></i> <?php echo htmlspecialchars($currentAdminName); ?></span>
            <a href="../index.php" target="_blank" class="btn btn-outline btn-sm" style="border-color: rgba(255,255,255,0.4); color: #fff;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Website
            </a>
            <a href="../logout.php" class="btn btn-outline btn-sm" style="border-color: var(--accent); color: var(--accent);">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
            </a>
        </div>
    </div>
</header>

<div class="dashboard-layout">
    <!-- Admin Sidebar -->
    <aside class="dashboard-sidebar">
        <nav class="dashboard-nav">
            <a href="dashboard.php" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard Overview
            </a>
            <a href="vendors.php?status=pending" class="<?php echo ($currentPage ?? '') === 'verification' ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-check"></i> Verification Queue 
                <?php if ($badgePendingCount > 0): ?>
                    <span style="background: #dc2626; color: #fff; font-size: 0.7rem; padding: 2px 7px; border-radius: 20px; margin-left: auto;">
                        <?php echo $badgePendingCount; ?> Pending
                    </span>
                <?php endif; ?>
            </a>
            <a href="vendors.php" class="<?php echo ($currentPage ?? '') === 'vendors' ? 'active' : ''; ?>">
                <i class="fa-solid fa-store"></i> All Vendors & KYC
            </a>
            <a href="categories.php" class="<?php echo ($currentPage ?? '') === 'categories' ? 'active' : ''; ?>">
                <i class="fa-solid fa-layer-group"></i> Service Categories
            </a>
            <a href="requests.php" class="<?php echo ($currentPage ?? '') === 'requests' ? 'active' : ''; ?>">
                <i class="fa-solid fa-clipboard-list"></i> Customer Event Requests
                <?php if ($badgeOpenRequests > 0): ?>
                    <span style="background: var(--accent); color: #fff; font-size: 0.7rem; padding: 2px 7px; border-radius: 20px; margin-left: auto;">
                        <?php echo $badgeOpenRequests; ?> Open
                    </span>
                <?php endif; ?>
            </a>
            <a href="payments.php" class="<?php echo ($currentPage ?? '') === 'payments' ? 'active' : ''; ?>">
                <i class="fa-solid fa-money-bill-transfer"></i> Track Payments & Escrow
                <?php if ($badgePendingPayouts > 0): ?>
                    <span style="background: #10b981; color: #fff; font-size: 0.7rem; padding: 2px 7px; border-radius: 20px; margin-left: auto;">
                        <?php echo $badgePendingPayouts; ?> Paid
                    </span>
                <?php endif; ?>
            </a>
            <a href="inquiries.php" class="<?php echo ($currentPage ?? '') === 'inquiries' ? 'active' : ''; ?>">
                <i class="fa-solid fa-comments"></i> Direct Inquiries
            </a>
            <a href="users.php" class="<?php echo ($currentPage ?? '') === 'users' ? 'active' : ''; ?>">
                <i class="fa-solid fa-users"></i> Users & Roles
                <?php if ($badgePendingUsers > 0): ?>
                    <span style="background: #f59e0b; color: #fff; font-size: 0.7rem; padding: 2px 7px; border-radius: 20px; margin-left: auto;">
                        <?php echo $badgePendingUsers; ?> New
                    </span>
                <?php endif; ?>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area Begins -->
    <main class="dashboard-content">
