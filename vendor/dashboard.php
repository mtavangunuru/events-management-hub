<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

// Auth Protection: Must be logged in as vendor or admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
$currentUserRole = $_SESSION['user_role'] ?? '';

if ($currentUserRole !== 'vendor' && $currentUserRole !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Check user account status
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = :uid LIMIT 1");
$userStmt->execute(['uid' => $currentUserId]);
$userAccount = $userStmt->fetch();

// Find current logged-in vendor's profile
$vpStmt = $pdo->prepare("SELECT vp.*, c.name as category_name FROM vendor_profiles vp JOIN categories c ON vp.category_id = c.id WHERE vp.user_id = :uid LIMIT 1");
$vpStmt->execute(['uid' => $currentUserId]);
$vendorProfile = $vpStmt->fetch();

// If admin is viewing or vendor profile not yet created, fallback to first vendor or create on the fly
if (!$vendorProfile) {
    $vpStmt = $pdo->query("SELECT vp.*, c.name as category_name FROM vendor_profiles vp JOIN categories c ON vp.category_id = c.id ORDER BY id ASC LIMIT 1");
    $vendorProfile = $vpStmt->fetch();
}

$successMsg = '';
$errorMsg = '';

// 1. Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $bName = trim($_POST['business_name'] ?? '');
    $catId = (int)($_POST['category_id'] ?? 1);
    $region = trim($_POST['region'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $whatsapp = trim($_POST['whatsapp_number'] ?? '');
    $instagram = trim($_POST['instagram_link'] ?? '');
    $startPrice = (float)($_POST['starting_price'] ?? 0);

    if (empty($bName) || empty($whatsapp)) {
        $errorMsg = "Business name and WhatsApp contact number are required.";
    } else {
        $up = $pdo->prepare("
            UPDATE vendor_profiles 
            SET business_name = :bname, category_id = :cid, region = :reg, district = :dist, 
                bio = :bio, whatsapp_number = :wa, instagram_link = :insta, starting_price = :sprice 
            WHERE id = :id
        ");
        $up->execute([
            'bname' => $bName,
            'cid' => $catId,
            'reg' => $region,
            'dist' => $district,
            'bio' => $bio,
            'wa' => $whatsapp,
            'insta' => $instagram,
            'sprice' => $startPrice,
            'id' => $vendorProfile['id']
        ]);
        $successMsg = "Your business profile has been updated successfully!";
        // Refresh profile data
        $vpStmt = $pdo->prepare("SELECT vp.*, c.name as category_name FROM vendor_profiles vp JOIN categories c ON vp.category_id = c.id WHERE vp.id = :id");
        $vpStmt->execute(['id' => $vendorProfile['id']]);
        $vendorProfile = $vpStmt->fetch();
    }
}

// 2. Handle Payment Method / Payout Details Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payout'])) {
    $pMethod = trim($_POST['payout_method'] ?? 'M-Pesa');
    $pName = trim($_POST['payout_account_name'] ?? '');
    $pNumber = trim($_POST['payout_account_number'] ?? '');
    $pBank = trim($_POST['payout_bank_name'] ?? '');

    if (empty($pNumber) || empty($pName)) {
        $errorMsg = "Please provide both the registered account name and mobile/account number.";
    } else {
        $upPay = $pdo->prepare("
            UPDATE vendor_profiles 
            SET payout_method = :pmethod, payout_account_name = :pname, payout_account_number = :pnumber, payout_bank_name = :pbank 
            WHERE id = :id
        ");
        $upPay->execute([
            'pmethod' => $pMethod,
            'pname' => $pName,
            'pnumber' => $pNumber,
            'pbank' => $pBank,
            'id' => $vendorProfile['id']
        ]);
        $successMsg = "Payment & payout method saved! Client payment remainders will be sent to this destination.";
        // Refresh
        $vendorProfile['payout_method'] = $pMethod;
        $vendorProfile['payout_account_name'] = $pName;
        $vendorProfile['payout_account_number'] = $pNumber;
        $vendorProfile['payout_bank_name'] = $pBank;
    }
}

// 3. Handle Add Service Package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $pkgTitle = trim($_POST['title'] ?? '');
    $pkgPrice = (float)($_POST['price_tzs'] ?? 0);
    $pkgCap = trim($_POST['guest_capacity'] ?? '');
    $pkgFeatures = trim($_POST['features'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($pkgTitle) || $pkgPrice <= 0 || empty($pkgFeatures)) {
        $errorMsg = "Please enter service title, valid price (TZS), and key features included.";
    } else {
        $insPkg = $pdo->prepare("
            INSERT INTO packages (vendor_id, title, price_tzs, guest_capacity, is_featured, features) 
            VALUES (:vid, :title, :price, :cap, :feat, :features)
        ");
        $insPkg->execute([
            'vid' => $vendorProfile['id'],
            'title' => $pkgTitle,
            'price' => $pkgPrice,
            'cap' => $pkgCap,
            'feat' => $isFeatured,
            'features' => $pkgFeatures
        ]);
        $successMsg = "New service package '{$pkgTitle}' has been added successfully!";
    }
}

// 4. Handle Edit Service Package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $editPkgId = (int)$_POST['package_id'];
    $pkgTitle = trim($_POST['title'] ?? '');
    $pkgPrice = (float)($_POST['price_tzs'] ?? 0);
    $pkgCap = trim($_POST['guest_capacity'] ?? '');
    $pkgFeatures = trim($_POST['features'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($pkgTitle) || $pkgPrice <= 0 || empty($pkgFeatures)) {
        $errorMsg = "All service package fields are required.";
    } else {
        $upPkg = $pdo->prepare("
            UPDATE packages 
            SET title = :title, price_tzs = :price, guest_capacity = :cap, is_featured = :feat, features = :features 
            WHERE id = :id AND vendor_id = :vid
        ");
        $upPkg->execute([
            'title' => $pkgTitle,
            'price' => $pkgPrice,
            'cap' => $pkgCap,
            'feat' => $isFeatured,
            'features' => $pkgFeatures,
            'id' => $editPkgId,
            'vid' => $vendorProfile['id']
        ]);
        $successMsg = "Service package updated successfully!";
    }
}

// 5. Handle Delete Service Package
if (isset($_GET['action']) && $_GET['action'] === 'delete_pkg' && isset($_GET['pkg_id'])) {
    $delPkgId = (int)$_GET['pkg_id'];
    $delPkg = $pdo->prepare("DELETE FROM packages WHERE id = :id AND vendor_id = :vid");
    $delPkg->execute(['id' => $delPkgId, 'vid' => $vendorProfile['id']]);
    $successMsg = "Service package has been deleted.";
}

// 6. Handle Cover Photo Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_cover'])) {
    if (isset($_FILES['new_cover']) && $_FILES['new_cover']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['new_cover']['tmp_name'];
        $fileName = $_FILES['new_cover']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $uploadsDir = '../uploads/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }
            $newFileName = 'vendor_' . $vendorProfile['id'] . '_' . time() . '.' . $ext;
            $dest = $uploadsDir . $newFileName;
            if (move_uploaded_file($fileTmp, $dest)) {
                $dbPath = 'uploads/' . $newFileName;
                $upStmt = $pdo->prepare("UPDATE vendor_profiles SET cover_image = :img WHERE id = :id");
                $upStmt->execute(['img' => $dbPath, 'id' => $vendorProfile['id']]);
                $vendorProfile['cover_image'] = $dbPath;
                $successMsg = "Your business cover photo has been updated successfully!";
            } else {
                $errorMsg = "Could not save uploaded photo. Please try again.";
            }
        } else {
            $errorMsg = "Only JPG, PNG, and WebP images are supported.";
        }
    } else {
        $errorMsg = "Please select a valid image file to upload.";
    }
}

// Fetch categories for profile edit
$allCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Fetch Packages for this vendor
$pkgStmt = $pdo->prepare("SELECT * FROM packages WHERE vendor_id = :vid ORDER BY price_tzs ASC");
$pkgStmt->execute(['vid' => $vendorProfile['id']]);
$vendorPackages = $pkgStmt->fetchAll();

// Fetch Bookings & Payments for this vendor
$bStmt = $pdo->prepare("SELECT * FROM bookings WHERE vendor_id = :vid ORDER BY created_at DESC");
$bStmt->execute(['vid' => $vendorProfile['id']]);
$vendorBookings = $bStmt->fetchAll();

// Aggregations for this vendor
$grossSales = 0;
$commDeducted = 0;
$netRemainder = 0;
$paidPayouts = 0;
$pendingPayouts = 0;

foreach ($vendorBookings as $vb) {
    if ($vb['payment_status'] === 'paid') {
        $grossSales += (float)$vb['total_amount'];
        $commDeducted += (float)$vb['commission_amount'];
        $netRemainder += (float)$vb['vendor_payout'];
        if ($vb['payout_status'] === 'paid') {
            $paidPayouts += (float)$vb['vendor_payout'];
        } else {
            $pendingPayouts += (float)$vb['vendor_payout'];
        }
    }
}

// Fetch Inquiries for this vendor
$inqStmt = $pdo->prepare("SELECT * FROM inquiries WHERE vendor_id = :vid ORDER BY created_at DESC LIMIT 10");
$inqStmt->execute(['vid' => $vendorProfile['id']]);
$vendorInquiries = $inqStmt->fetchAll();

// Fetch In-App Notifications
$notifStmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10");
$notifStmt->execute(['uid' => $currentUserId]);
$vendorNotifications = $notifStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Portal - Nuustar Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .badge-verified-status { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }
        .badge-pending-status { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }
        .service-card { border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 20px; background: #fff; display: flex; flex-direction: column; justify-content: space-between; position: relative; }
        .service-card.featured-card { border-color: var(--primary); box-shadow: 0 4px 12px rgba(10,37,64,0.08); }
        .service-features-list { list-style: none; padding: 0; margin: 12px 0 20px; font-size: 0.85rem; color: var(--text-main); }
        .service-features-list li { padding: 4px 0; display: flex; align-items: baseline; gap: 8px; }
        .service-features-list li i { color: #10b981; }
    </style>
</head>
<body>

<!-- Dashboard Top Header -->
<header class="navbar" style="position: static; background: #0a2540; border-bottom: 2px solid var(--accent);">
    <div class="container nav-container" style="max-width: 100%; padding: 0 30px;">
        <a href="../index.php" class="logo" style="color: #fff; display: flex; align-items: center; gap: 10px;">
            <img src="../uploads/logo.png" alt="Nuustar Events" style="height: 42px; width: 42px; object-fit: contain; border-radius: 50%; border: 2px solid var(--accent);">
            <span>Nuustar<span style="color: var(--accent);">VendorHub</span></span>
        </a>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <?php if ($vendorProfile['verification_status'] === 'verified'): ?>
                    <span class="badge-status badge-verified-status"><i class="fa-solid fa-circle-check"></i> Verified Partner</span>
                <?php else: ?>
                    <span class="badge-status badge-pending-status"><i class="fa-solid fa-clock"></i> Pending Admin Approval</span>
                <?php endif; ?>
                <strong style="color: #fff;"><?php echo htmlspecialchars($vendorProfile['business_name']); ?></strong>
            </div>
            <a href="../logout.php" class="btn btn-outline btn-sm" style="border-color: var(--accent); color: var(--accent);">Log Out</a>
        </div>
    </div>
</header>

<div class="dashboard-layout">
    <!-- Sidebar Navigation -->
    <aside class="dashboard-sidebar">
        <div style="text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
            <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; margin: 0 auto 10px; border: 2px solid var(--accent); background: #e0e7ff;">
                <img src="<?php echo (strpos($vendorProfile['cover_image'], 'http') === 0 ? '' : '../') . htmlspecialchars($vendorProfile['cover_image']); ?>" alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--dark);"><?php echo htmlspecialchars($vendorProfile['business_name']); ?></h4>
            <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($vendorProfile['district'] . ', ' . $vendorProfile['region']); ?></span>
            <div style="margin-top: 5px;">
                <span class="logo-badge" style="background: var(--primary);"><?php echo htmlspecialchars($vendorProfile['category_name']); ?></span>
            </div>
        </div>

        <nav class="dashboard-nav">
            <a href="#overviewSection" class="active"><i class="fa-solid fa-chart-pie"></i> Overview</a>
            <a href="#servicesSection"><i class="fa-solid fa-layer-group"></i> My Services & Packages <span style="background: var(--primary); color: #fff; font-size: 0.7rem; padding: 2px 7px; border-radius: 20px; margin-left: auto;"><?php echo count($vendorPackages); ?></span></a>
            <a href="#bookingsSection"><i class="fa-solid fa-money-check-dollar"></i> Bookings & Payments <span style="background: #10b981; color: #fff; font-size: 0.7rem; padding: 2px 7px; border-radius: 20px; margin-left: auto;"><?php echo count($vendorBookings); ?></span></a>
            <a href="#payoutSection"><i class="fa-solid fa-wallet"></i> Payment Method (Payouts)</a>
            <a href="#profileSection"><i class="fa-solid fa-id-card"></i> Edit Business Profile</a>
            <a href="#inquiriesSection"><i class="fa-solid fa-envelope-open-text"></i> Direct Inquiries <span style="background: var(--accent); color: #fff; font-size: 0.7rem; padding: 2px 6px; border-radius: 20px; margin-left: auto;"><?php echo count($vendorInquiries); ?></span></a>
            <a href="../vendor-details.php?id=<?php echo $vendorProfile['id']; ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Preview Live Storefront</a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="dashboard-content">
        <!-- Pending Approval Alert -->
        <?php if ($vendorProfile['verification_status'] === 'pending' || ($userAccount['status'] ?? '') === 'pending'): ?>
            <div style="background: #fffbeb; border: 1px solid #f59e0b; border-left: 5px solid #d97706; padding: 18px 22px; border-radius: var(--radius-sm); margin-bottom: 25px;">
                <div style="display: flex; align-items: flex-start; gap: 15px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.5rem; color: #d97706; margin-top: 3px;"></i>
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #92400e; margin-bottom: 4px;">Account Awaiting Administrator Approval</h3>
                        <p style="font-size: 0.9rem; color: #b45309; line-height: 1.5; margin-bottom: 8px;">
                            Your registration has been submitted and is currently being reviewed by Nuustar Events Administration for KYC and quality check.
                        </p>
                        <p style="font-size: 0.85rem; color: #78350f;">
                            <i class="fa-solid fa-circle-info"></i> In the meantime, you can <strong>add your services/packages</strong>, <strong>complete your profile</strong>, and <strong>configure your payout method</strong> below so you are completely ready once approved!
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><?php echo htmlspecialchars($vendorProfile['business_name']); ?></h1>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Manage your services, track customer payments, and receive platform payouts.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="#addServiceBox" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add New Service</a>
                <a href="../vendor-details.php?id=<?php echo $vendorProfile['id']; ?>" target="_blank" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View Storefront
                </a>
            </div>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <!-- Metric Cards Grid -->
        <div class="metrics-grid" id="overviewSection">
            <div class="metric-card">
                <div>
                    <div class="metric-label">Gross Customer Bookings</div>
                    <div class="metric-number" style="font-size: 1.35rem; color: #0f172a;"><?php echo number_format($grossSales); ?> TZS</div>
                    <small style="color: var(--text-muted); font-size: 0.75rem;"><?php echo count($vendorBookings); ?> Orders Received</small>
                </div>
                <div class="metric-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-receipt"></i></div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Platform Fee (<?php echo number_format($vendorProfile['commission_rate'], 1); ?>%)</div>
                    <div class="metric-number" style="font-size: 1.35rem; color: #059669;">- <?php echo number_format($commDeducted); ?> TZS</div>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Approved commission deducted</small>
                </div>
                <div class="metric-icon" style="background:#ecfdf5; color:#059669;"><i class="fa-solid fa-percent"></i></div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Your Net Earnings Remainder</div>
                    <div class="metric-number" style="font-size: 1.35rem; color: var(--primary);"><?php echo number_format($netRemainder); ?> TZS</div>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Total to be paid out to you</small>
                </div>
                <div class="metric-icon" style="background: #e0e7ff; color: #4338ca;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Pending Payout Balance</div>
                    <div class="metric-number" style="font-size: 1.35rem; color: <?php echo $pendingPayouts > 0 ? '#d97706' : '#059669'; ?>;">
                        <?php echo number_format($pendingPayouts); ?> TZS
                    </div>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Disbursed so far: <?php echo number_format($paidPayouts); ?> TZS</small>
                </div>
                <div class="metric-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-clock-rotate-left"></i></div>
            </div>
        </div>

        <!-- SECTION 1: SERVICES & PACKAGES MANAGEMENT -->
        <div class="content-box" id="servicesSection" style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--dark); margin: 0;">
                        <i class="fa-solid fa-layer-group" style="color: var(--primary); margin-right: 8px;"></i> My Services & Packages
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                        Add, edit, or configure the service tiers that customers can book and pay for online.
                    </p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="toggleAddServiceForm()">
                    <i class="fa-solid fa-plus"></i> Add New Package
                </button>
            </div>

            <!-- Add Service Form (Toggled) -->
            <div id="addServiceBox" style="display: none; background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 24px; margin-bottom: 25px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 15px;">Create a New Service Package</h3>
                <form action="dashboard.php#servicesSection" method="POST">
                    <input type="hidden" name="add_service" value="1">
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Service / Package Title</label>
                            <input type="text" name="title" placeholder="e.g. Bronze Elegance Package or 4K Cinematography Tier" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Price (TZS)</label>
                            <input type="number" step="1000" name="price_tzs" placeholder="e.g. 1500000" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Guest Capacity / Scope</label>
                            <input type="text" name="guest_capacity" placeholder="e.g. 150 - 250 Guests" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">
                            Package Features (Enter one feature per line)
                        </label>
                        <textarea name="features" rows="4" placeholder="Sweetheart stage setup with fresh florals&#10;Banquet chair covers and satin sashes&#10;Intelligent dynamic LED mood uplighting" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.85rem;"></textarea>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                        <input type="checkbox" name="is_featured" id="isFeaturedCheck" style="accent-color: var(--primary);">
                        <label for="isFeaturedCheck" style="font-size: 0.85rem; font-weight: 600;">Mark as "Most Popular" / Featured Package</label>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-save"></i> Save & Publish Service</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="toggleAddServiceForm()">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Existing Packages Grid -->
            <?php if (empty($vendorPackages)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fafafa; border-radius: var(--radius-sm);">
                    <i class="fa-solid fa-box-open" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p style="font-weight: 600;">No services added yet!</p>
                    <p style="font-size: 0.85rem;">Click "Add New Package" above to create your first package so customers can book and pay you.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($vendorPackages as $pkg): ?>
                    <div class="service-card <?php echo $pkg['is_featured'] ? 'featured-card' : ''; ?>">
                        <div>
                            <?php if ($pkg['is_featured']): ?>
                                <span style="background: var(--primary); color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 6px;">MOST POPULAR</span>
                            <?php endif; ?>
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark); margin-bottom: 4px;"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-users"></i> <?php echo htmlspecialchars($pkg['guest_capacity'] ?? 'Custom capacity'); ?></span>
                            
                            <div style="font-size: 1.3rem; font-weight: 800; color: var(--primary); margin: 12px 0 8px;">
                                <?php echo number_format($pkg['price_tzs']); ?> <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">TZS</span>
                            </div>

                            <ul class="service-features-list">
                                <?php 
                                $featLines = explode("\n", trim($pkg['features']));
                                foreach ($featLines as $fl):
                                    if (empty(trim($fl))) continue;
                                ?>
                                    <li><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars(trim($fl)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div style="display: flex; gap: 8px; border-top: 1px solid var(--border); padding-top: 12px; margin-top: auto;">
                            <button type="button" class="btn btn-outline btn-sm" style="flex: 1;" onclick='openEditServiceModal(<?php echo json_encode($pkg); ?>)'>
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <a href="dashboard.php?action=delete_pkg&pkg_id=<?php echo $pkg['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;" onclick="return confirm('Delete this package?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- SECTION 2: BOOKINGS & PAYMENTS (ESCROW & DEDUCTION TRACKER) -->
        <div class="content-box" id="bookingsSection" style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--dark); margin: 0;">
                        <i class="fa-solid fa-money-check-dollar" style="color: #059669; margin-right: 8px;"></i> Customer Bookings & Payment Deductions
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                        Customer payments are held securely in Escrow. Platform commission (<?php echo number_format($vendorProfile['commission_rate'], 1); ?>%) is deducted and remaining balance is sent to your payout account.
                    </p>
                </div>
                <span class="badge-status badge-verified-status"><?php echo count($vendorBookings); ?> Orders Total</span>
            </div>

            <?php if (empty($vendorBookings)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted); background: #fafafa; border-radius: var(--radius-sm);">
                    <i class="fa-regular fa-credit-card" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p style="font-weight: 600;">No customer bookings recorded yet.</p>
                    <p style="font-size: 0.85rem;">When clients choose your services and complete payment, each order and fee breakdown will appear here.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Receipt / Ref</th>
                                <th>Client Name</th>
                                <th>Service & Event Date</th>
                                <th>Total Paid</th>
                                <th>Platform Fee (<?php echo number_format($vendorProfile['commission_rate'], 1); ?>%)</th>
                                <th>Your Payout Remainder</th>
                                <th>Payout Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vendorBookings as $b): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary); font-family: monospace;"><?php echo htmlspecialchars($b['booking_ref']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($b['created_at'])); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($b['customer_name']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($b['customer_phone']); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($b['package_title']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($b['event_date'])); ?> (<?php echo htmlspecialchars($b['event_type']); ?>)</small>
                                </td>
                                <td>
                                    <strong><?php echo number_format($b['total_amount']); ?> TZS</strong><br>
                                    <span class="badge-status badge-verified-status" style="font-size: 0.65rem; padding: 2px 6px;">PAID</span>
                                </td>
                                <td style="color: #059669;">
                                    - <?php echo number_format($b['commission_amount']); ?> TZS
                                </td>
                                <td style="color: var(--primary);">
                                    <strong style="font-size: 1rem;"><?php echo number_format($b['vendor_payout']); ?> TZS</strong>
                                </td>
                                <td>
                                    <?php if ($b['payout_status'] === 'paid'): ?>
                                        <span class="badge-status badge-verified-status"><i class="fa-solid fa-circle-check"></i> Disbursed</span><br>
                                        <small style="color: var(--text-muted); font-size: 0.7rem;">Ref: <?php echo htmlspecialchars($b['payout_reference'] ?? 'Completed'); ?></small>
                                    <?php else: ?>
                                        <span class="badge-status badge-pending-status"><i class="fa-solid fa-clock"></i> In Escrow / Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="admin-table-actions">
                                        <a href="../booking-confirmation.php?ref=<?php echo urlencode($b['booking_ref']); ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 5px 8px;" title="View Voucher Receipt">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                        <?php 
                                        $cleanCustPhone = preg_replace('/[^0-9]/', '', $b['customer_phone']);
                                        $waClientMsg = rawurlencode("Hello " . $b['customer_name'] . ", this is " . $vendorProfile['business_name'] . " regarding your confirmed booking #" . $b['booking_ref'] . " for " . $b['event_date'] . ". Thank you for your payment!");
                                        ?>
                                        <a href="https://wa.me/<?php echo $cleanCustPhone; ?>?text=<?php echo $waClientMsg; ?>" target="_blank" class="btn btn-whatsapp btn-sm" style="padding: 5px 8px;" title="Chat Client on WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; align-items: start;">
            <!-- SECTION 3: PAYMENT METHOD / PAYOUT SETUP -->
            <div class="content-box" id="payoutSection">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
                    <i class="fa-solid fa-wallet" style="color: var(--primary);"></i> Payout Payment Method
                </h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 18px;">
                    Configure the bank account or mobile money line where your net booking earnings will be disbursed.
                </p>

                <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: var(--radius-sm); padding: 14px; margin-bottom: 20px; font-size: 0.85rem; color: #3730a3;">
                    <i class="fa-solid fa-circle-info"></i> <strong>Approved Platform Commission:</strong> <?php echo number_format($vendorProfile['commission_rate'], 1); ?>%<br>
                    You receive <strong><?php echo 100 - number_format($vendorProfile['commission_rate'], 1); ?>%</strong> of all customer order totals deposited into the account below.
                </div>

                <form action="dashboard.php#payoutSection" method="POST">
                    <input type="hidden" name="update_payout" value="1">

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Payout Channel</label>
                        <select name="payout_method" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                            <option value="M-Pesa" <?php echo ($vendorProfile['payout_method'] ?? '') === 'M-Pesa' ? 'selected' : ''; ?>>Vodacom M-Pesa</option>
                            <option value="Tigo Pesa" <?php echo ($vendorProfile['payout_method'] ?? '') === 'Tigo Pesa' ? 'selected' : ''; ?>>Tigo Pesa / Mixx</option>
                            <option value="Airtel Money" <?php echo ($vendorProfile['payout_method'] ?? '') === 'Airtel Money' ? 'selected' : ''; ?>>Airtel Money</option>
                            <option value="Halopesa" <?php echo ($vendorProfile['payout_method'] ?? '') === 'Halopesa' ? 'selected' : ''; ?>>Halopesa</option>
                            <option value="Bank Transfer" <?php echo ($vendorProfile['payout_method'] ?? '') === 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer (CRDB / NMB / Stanbic)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Registered Account Name</label>
                        <input type="text" name="payout_account_name" value="<?php echo htmlspecialchars($vendorProfile['payout_account_name'] ?? ''); ?>" placeholder="e.g. Elegance Decor Tanzania or Sarah Mwangi" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Account / Mobile Phone Number</label>
                        <input type="text" name="payout_account_number" value="<?php echo htmlspecialchars($vendorProfile['payout_account_number'] ?? ''); ?>" placeholder="e.g. 0712000111 or 0150XXXXXXXX" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 22px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Bank Name & Branch (If Bank Transfer)</label>
                        <input type="text" name="payout_bank_name" value="<?php echo htmlspecialchars($vendorProfile['payout_bank_name'] ?? ''); ?>" placeholder="e.g. CRDB Bank, Oysterbay Branch" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-check"></i> Save Payout Details
                    </button>
                </form>
            </div>

            <!-- SECTION 4: EDIT PROFILE -->
            <div class="content-box" id="profileSection">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
                    <i class="fa-solid fa-id-card" style="color: var(--primary);"></i> Edit Business Profile
                </h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 18px;">
                    Update your brand information, service category, and public portfolio.
                </p>

                <form action="dashboard.php#profileSection" method="POST">
                    <input type="hidden" name="update_profile" value="1">

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Business / Brand Name</label>
                        <input type="text" name="business_name" value="<?php echo htmlspecialchars($vendorProfile['business_name']); ?>" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Service Category</label>
                            <select name="category_id" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                                <?php foreach ($allCategories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $vendorProfile['category_id'] == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Starting Price (TZS)</label>
                            <input type="number" name="starting_price" value="<?php echo (int)$vendorProfile['starting_price']; ?>" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Region</label>
                            <input type="text" name="region" value="<?php echo htmlspecialchars($vendorProfile['region']); ?>" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">District / Area</label>
                            <input type="text" name="district" value="<?php echo htmlspecialchars($vendorProfile['district']); ?>" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">WhatsApp Number</label>
                            <input type="tel" name="whatsapp_number" value="<?php echo htmlspecialchars($vendorProfile['whatsapp_number']); ?>" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Instagram Page Link</label>
                            <input type="text" name="instagram_link" value="<?php echo htmlspecialchars($vendorProfile['instagram_link'] ?? ''); ?>" placeholder="instagram.com/yourhandle" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 22px;">
                        <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Business Bio & Experience Description</label>
                        <textarea name="bio" rows="3" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.85rem;"><?php echo htmlspecialchars($vendorProfile['bio'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-save"></i> Save Profile Details
                    </button>
                </form>
            </div>
        </div>

        <!-- Cover Photo Upload Section -->
        <div class="content-box" id="coverPhotoSection" style="margin-bottom: 30px;">
            <h2 style="font-size: 1.25rem;"><i class="fa-solid fa-camera"></i> Update Business Cover Photo / Picture</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">
                Upload a picture of your venue hall, catering buffet, wedding decor setup, or professional MC photo.
            </p>

            <form action="dashboard.php#coverPhotoSection" method="POST" enctype="multipart/form-data" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="upload_cover" value="1">
                <input type="file" name="new_cover" accept="image/*" required style="padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-light);">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-upload"></i> Upload & Save Picture
                </button>
            </form>
        </div>

        <!-- Recent Inquiries Table -->
        <div class="content-box" id="inquiriesSection">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2>Recent Customer Inquiries & Quote Requests</h2>
                <span class="badge-status badge-verified-status"><?php echo count($vendorInquiries); ?> Total</span>
            </div>

            <?php if (empty($vendorInquiries)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-regular fa-envelope-open" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                    <p>No customer quote requests received yet. Inquiries submitted via your profile will appear here!</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Event Details</th>
                            <th>Date</th>
                            <th>Guests</th>
                            <th>Notes / Package</th>
                            <th>Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendorInquiries as $inq): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($inq['customer_name']); ?></strong><br>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($inq['customer_phone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($inq['event_type']); ?></td>
                            <td><?php echo htmlspecialchars($inq['event_date']); ?></td>
                            <td><?php echo (int)$inq['guest_count']; ?></td>
                            <td style="max-width: 250px; font-size: 0.85rem; color: var(--text-muted);">
                                <?php echo htmlspecialchars($inq['notes']); ?>
                            </td>
                            <td>
                                <?php 
                                $clientCleanPhone = preg_replace('/[^0-9]/', '', $inq['customer_phone']);
                                $replyMsg = rawurlencode("Hello " . $inq['customer_name'] . ", this is " . $vendorProfile['business_name'] . " regarding your event inquiry for " . $inq['event_date'] . " on Nuustar Events. We are delighted to confirm our availability!");
                                ?>
                                <a href="https://wa.me/<?php echo $clientCleanPhone; ?>?text=<?php echo $replyMsg; ?>" target="_blank" class="btn btn-whatsapp btn-sm">
                                    <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Edit Service Modal -->
<div id="editServiceModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 520px; border-radius: var(--radius-sm); padding: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); position: relative; max-height: 90vh; overflow-y: auto;">
        <button type="button" onclick="closeEditServiceModal()" style="position: absolute; top: 18px; right: 20px; background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
            <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Edit Service Package
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">Update pricing, deliverables, or guest capacity.</p>

        <form action="dashboard.php#servicesSection" method="POST">
            <input type="hidden" name="edit_service" value="1">
            <input type="hidden" name="package_id" id="editPkgId">

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Package Title</label>
                <input type="text" name="title" id="editPkgTitle" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div class="form-group">
                    <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Price (TZS)</label>
                    <input type="number" name="price_tzs" id="editPkgPrice" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Guest Capacity / Scope</label>
                    <input type="text" name="guest_capacity" id="editPkgCapacity" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Package Features (One per line)</label>
                <textarea name="features" id="editPkgFeatures" rows="4" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.85rem;"></textarea>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <input type="checkbox" name="is_featured" id="editPkgFeatured" style="accent-color: var(--primary);">
                <label for="editPkgFeatured" style="font-size: 0.85rem; font-weight: 600;">Mark as "Most Popular" / Featured</label>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditServiceModal()" class="btn btn-outline btn-sm" style="padding: 10px 18px;">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 22px;">
                    <i class="fa-solid fa-save"></i> Save Package
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAddServiceForm() {
    const box = document.getElementById('addServiceBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display === 'block') {
        box.scrollIntoView({ behavior: 'smooth' });
    }
}

function openEditServiceModal(pkg) {
    document.getElementById('editPkgId').value = pkg.id;
    document.getElementById('editPkgTitle').value = pkg.title;
    document.getElementById('editPkgPrice').value = pkg.price_tzs;
    document.getElementById('editPkgCapacity').value = pkg.guest_capacity || '';
    document.getElementById('editPkgFeatures').value = pkg.features || '';
    document.getElementById('editPkgFeatured').checked = pkg.is_featured == 1;

    document.getElementById('editServiceModal').style.display = 'flex';
}

function closeEditServiceModal() {
    document.getElementById('editServiceModal').style.display = 'none';
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('editServiceModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

</body>
</html>
