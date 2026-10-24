<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Auth Protection: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];

// Fetch user profile from DB
$uStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$uStmt->execute(['id' => $currentUserId]);
$currentUser = $uStmt->fetch();

if (!$currentUser) {
    header("Location: logout.php");
    exit;
}

// Handle Mark Notifications as Read
if (isset($_GET['action']) && $_GET['action'] === 'read_all') {
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid")->execute(['uid' => $currentUserId]);
    header("Location: customer-dashboard.php#notificationsTab");
    exit;
}

// Fetch Customer Bookings & Payments
$bStmt = $pdo->prepare("
    SELECT b.*, vp.business_name, vp.whatsapp_number as vendor_phone, vp.cover_image, c.name as category_name 
    FROM bookings b 
    JOIN vendor_profiles vp ON b.vendor_id = vp.id 
    JOIN categories c ON vp.category_id = c.id 
    WHERE b.customer_id = :uid OR b.customer_phone = :phone 
    ORDER BY b.created_at DESC
");
$bStmt->execute(['uid' => $currentUserId, 'phone' => $currentUser['phone']]);
$bookings = $bStmt->fetchAll();

// Aggregations
$totSpent = 0;
$upcomingCount = 0;
$today = date('Y-m-d');

foreach ($bookings as $b) {
    if ($b['payment_status'] === 'paid') {
        $totSpent += (float)$b['total_amount'];
        if ($b['event_date'] >= $today) {
            $upcomingCount++;
        }
    }
}

// Fetch Notifications
$notifStmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC");
$notifStmt->execute(['uid' => $currentUserId]);
$notifications = $notifStmt->fetchAll();

$pageTitle = "My Customer Dashboard - Nuustar Events";
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 1050px; padding: 40px 20px 80px;">
    <!-- Welcome Header & Status -->
    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 25px 30px; margin-bottom: 25px; box-shadow: var(--card-shadow); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800;">
                <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
            </div>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--dark); margin: 0 0 4px;">
                    Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>!
                </h1>
                <div style="display: flex; gap: 15px; font-size: 0.85rem; color: var(--text-muted); flex-wrap: wrap;">
                    <span><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($currentUser['email']); ?></span>
                    <span><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($currentUser['phone']); ?></span>
                    <span><i class="fa-solid fa-shield"></i> Role: Host / Customer</span>
                </div>
            </div>
        </div>

        <div>
            <?php if ($currentUser['status'] === 'active'): ?>
                <span class="badge-status badge-verified-status" style="padding: 6px 14px; font-size: 0.85rem;">
                    <i class="fa-solid fa-circle-check"></i> Account Approved & Active
                </span>
            <?php elseif ($currentUser['status'] === 'pending'): ?>
                <span class="badge-status badge-pending-status" style="padding: 6px 14px; font-size: 0.85rem; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                    <i class="fa-solid fa-clock"></i> Awaiting Administrator Approval
                </span>
            <?php else: ?>
                <span class="badge-status badge-rejected-status" style="padding: 6px 14px; font-size: 0.85rem;">
                    <?php echo htmlspecialchars($currentUser['status']); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Approval Pending Notification Banner -->
    <?php if ($currentUser['status'] === 'pending'): ?>
        <div style="background: #fffbeb; border: 1px solid #f59e0b; border-left: 5px solid #d97706; padding: 20px 24px; border-radius: var(--radius-sm); margin-bottom: 25px;">
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <i class="fa-solid fa-hourglass-start" style="font-size: 1.8rem; color: #d97706; margin-top: 3px;"></i>
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #92400e; margin-bottom: 5px;">Registration Awaiting Administrator Approval</h3>
                    <p style="font-size: 0.9rem; color: #b45309; line-height: 1.5; margin-bottom: 8px;">
                        Your registration has been received successfully and is queued for verification by the Nuustar Events admin team.
                    </p>
                    <p style="font-size: 0.85rem; color: #78350f;">
                        You will receive an in-app notification and approval as soon as our administrator reviews your profile. Once approved, you can freely choose services, pay online, and reserve your event dates.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- KPI Metric Cards -->
    <div class="metrics-grid" style="margin-bottom: 30px;">
        <div class="metric-card">
            <div>
                <div class="metric-label">Confirmed Bookings</div>
                <div class="metric-number"><?php echo count($bookings); ?></div>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Verified vendor orders</small>
            </div>
            <div class="metric-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-calendar-check"></i></div>
        </div>

        <div class="metric-card">
            <div>
                <div class="metric-label">Upcoming Celebrations</div>
                <div class="metric-number" style="color: #059669;"><?php echo $upcomingCount; ?></div>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Events scheduled</small>
            </div>
            <div class="metric-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-cake-candles"></i></div>
        </div>

        <div class="metric-card">
            <div>
                <div class="metric-label">Total Payments Made</div>
                <div class="metric-number" style="font-size: 1.35rem; color: var(--primary);"><?php echo number_format($totSpent); ?> TZS</div>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Held securely in escrow</small>
            </div>
            <div class="metric-icon" style="background: #e0e7ff; color: #4338ca;"><i class="fa-solid fa-receipt"></i></div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="filter-tabs" style="margin-bottom: 25px;">
        <button type="button" class="filter-tab-btn active" onclick="switchCustomerTab('bookings', this)">
            <i class="fa-solid fa-receipt"></i> My Bookings & Payment Receipts (<?php echo count($bookings); ?>)
        </button>
        <button type="button" class="filter-tab-btn" onclick="switchCustomerTab('notifications', this)">
            <i class="fa-solid fa-bell"></i> Payment Notifications & Alerts (<?php echo count($notifications); ?>)
        </button>
    </div>

    <!-- TAB 1: MY BOOKINGS & RECEIPTS -->
    <div id="tabContentBookings" class="content-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin: 0;">My Booked Services & Payment Receipts</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                    View payment status, print official confirmation vouchers, or coordinate with your booked vendors.
                </p>
            </div>
            <a href="vendors.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Explore More Services</a>
        </div>

        <?php if (empty($bookings)): ?>
            <div style="text-align: center; padding: 50px 20px; color: var(--text-muted); background: #fafafa; border-radius: var(--radius-sm);">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                <h3 style="color: var(--dark); margin-bottom: 6px;">No Bookings Yet</h3>
                <p style="font-size: 0.9rem; max-width: 450px; margin: 0 auto 20px;">
                    Discover verified event venues, caterers, photographers, and decorators across Tanzania and book your first service tier.
                </p>
                <a href="vendors.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Browse Vendors</a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Voucher Reference</th>
                            <th>Vendor & Category</th>
                            <th>Service Package</th>
                            <th>Event Date</th>
                            <th>Amount Paid</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <strong style="font-family: monospace; color: var(--primary); font-size: 0.95rem;"><?php echo htmlspecialchars($b['booking_ref']); ?></strong><br>
                                <small style="color: var(--text-muted);"><i class="fa-regular fa-clock"></i> <?php echo date('M d, Y', strtotime($b['created_at'])); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($b['business_name']); ?></strong><br>
                                <span class="logo-badge" style="background: var(--primary); font-size: 0.7rem;"><?php echo htmlspecialchars($b['category_name']); ?></span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($b['package_title']); ?></strong><br>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($b['event_type']); ?> (<?php echo (int)$b['guest_count']; ?> Guests)</small>
                            </td>
                            <td>
                                <strong style="color: var(--dark);"><?php echo date('M d, Y', strtotime($b['event_date'])); ?></strong>
                            </td>
                            <td>
                                <strong style="font-size: 1.05rem; color: #059669;"><?php echo number_format($b['total_amount']); ?> TZS</strong><br>
                                <small style="color: var(--text-muted); font-size: 0.75rem;"><?php echo htmlspecialchars($b['payment_method']); ?></small>
                            </td>
                            <td>
                                <span class="badge-status badge-verified-status" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-circle-check"></i> PAID & SECURED
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="booking-confirmation.php?ref=<?php echo urlencode($b['booking_ref']); ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-weight: 700;" title="View Official Receipt Voucher">
                                        <i class="fa-solid fa-receipt"></i> Receipt
                                    </a>
                                    <?php 
                                    $clnPhone = preg_replace('/[^0-9]/', '', $b['vendor_phone']);
                                    $waMsg = rawurlencode("Hello " . $b['business_name'] . ", this is " . $currentUser['name'] . " regarding my paid booking #" . $b['booking_ref'] . " for " . $b['event_date'] . ".");
                                    ?>
                                    <a href="https://wa.me/<?php echo $clnPhone; ?>?text=<?php echo $waMsg; ?>" target="_blank" class="btn btn-whatsapp btn-sm" style="padding: 6px 10px;" title="Chat with Vendor on WhatsApp">
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

    <!-- TAB 2: NOTIFICATIONS & ALERTS -->
    <div id="tabContentNotifications" class="content-box" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin: 0;">Payment Notifications & System Alerts</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                    Real-time status updates on your approvals, payments, and booking confirmations.
                </p>
            </div>
            <?php if (!empty($notifications)): ?>
                <a href="customer-dashboard.php?action=read_all" class="btn btn-outline btn-sm">Mark All as Read</a>
            <?php endif; ?>
        </div>

        <?php if (empty($notifications)): ?>
            <div style="text-align: center; padding: 45px 20px; color: var(--text-muted); background: #fafafa; border-radius: var(--radius-sm);">
                <i class="fa-regular fa-bell-slash" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                <p>No notifications at the moment.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($notifications as $n): ?>
                <div style="border: 1px solid var(--border); border-left: 4px solid <?php echo $n['type'] === 'payment' ? '#10b981' : 'var(--primary)'; ?>; border-radius: var(--radius-sm); padding: 16px 20px; background: <?php echo $n['is_read'] ? '#fff' : '#f0fdf4'; ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--dark); margin: 0;">
                            <?php if ($n['type'] === 'payment'): ?>
                                <i class="fa-solid fa-circle-check" style="color: #059669; margin-right: 6px;"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-bell" style="color: var(--primary); margin-right: 6px;"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($n['title']); ?>
                        </h4>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">
                            <i class="fa-regular fa-clock"></i> <?php echo date('M d, Y H:i', strtotime($n['created_at'])); ?>
                        </small>
                    </div>
                    <p style="font-size: 0.85rem; color: var(--text-main); margin: 0; line-height: 1.5;">
                        <?php echo htmlspecialchars($n['message']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function switchCustomerTab(tabName, btn) {
    document.getElementById('tabContentBookings').style.display = tabName === 'bookings' ? 'block' : 'none';
    document.getElementById('tabContentNotifications').style.display = tabName === 'notifications' ? 'block' : 'none';

    document.querySelectorAll('.filter-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// Auto open tab if anchored
if (window.location.hash === '#notificationsTab') {
    const notifBtn = document.querySelectorAll('.filter-tab-btn')[1];
    if (notifBtn) {
        switchCustomerTab('notifications', notifBtn);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
