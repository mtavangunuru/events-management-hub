<?php
$currentPage = 'dashboard';
$pageTitle = 'Dashboard Overview';
require_once 'includes/header.php';

// Handle Vendor Approval / Rejection Actions directly from overview
$actionNotice = '';
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $targetId = (int)$_GET['id'];
    
    if ($action === 'approve') {
        $vData = $pdo->prepare("SELECT user_id, business_name FROM vendor_profiles WHERE id = :id");
        $vData->execute(['id' => $targetId]);
        $vRow = $vData->fetch();

        $upStmt = $pdo->prepare("UPDATE vendor_profiles SET verification_status = 'verified' WHERE id = :id");
        $upStmt->execute(['id' => $targetId]);

        if ($vRow) {
            $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :uid")->execute(['uid' => $vRow['user_id']]);
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Vendor Profile Approved!', 'Your vendor profile has been verified and published!', 'account')");
            $notif->execute(['uid' => $vRow['user_id']]);
        }

        $actionNotice = "Vendor ID #{$targetId} has been verified and published to the public directory!";
    } elseif ($action === 'reject') {
        $upStmt = $pdo->prepare("UPDATE vendor_profiles SET verification_status = 'rejected' WHERE id = :id");
        $upStmt->execute(['id' => $targetId]);
        $actionNotice = "Vendor ID #{$targetId} has been rejected.";
    }
}

// 1. Fetch Real Platform Counts
$verifiedCount = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles WHERE verification_status = 'verified'")->fetchColumn();
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles WHERE verification_status = 'pending'")->fetchColumn();
$pendingUsersCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$usersCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$requestsCount = (int)$pdo->query("SELECT COUNT(*) FROM event_requests")->fetchColumn();
$inquiriesCount = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$totGrossRev = (float)$pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();
$totCommRev = (float)$pdo->query("SELECT COALESCE(SUM(commission_amount), 0) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();

// 2. Fetch Pending KYC Applications
$pendingStmt = $pdo->query("
    SELECT vp.*, u.name AS owner_name, u.phone AS owner_phone, u.email AS owner_email, c.name AS category_name 
    FROM vendor_profiles vp 
    JOIN users u ON vp.user_id = u.id 
    JOIN categories c ON vp.category_id = c.id 
    WHERE vp.verification_status = 'pending' 
    ORDER BY vp.created_at DESC
");
$pendingVendors = $pendingStmt->fetchAll();

// 3. Fetch Recent Customer Event Requests
$recentReqStmt = $pdo->query("
    SELECT * FROM event_requests 
    ORDER BY created_at DESC 
    LIMIT 5
");
$recentRequests = $recentReqStmt->fetchAll();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);">Nuustar Events - Operations & Trust Center</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Monitor platform activity, review KYC vendor registrations, and manage service listings across Tanzania.</p>
</div>

<?php if (!empty($actionNotice)): ?>
    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($actionNotice); ?>
    </div>
<?php endif; ?>

<!-- Real-time Metric Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div>
            <div class="metric-label">Gross Payments Volume</div>
            <div class="metric-number" style="font-size: 1.25rem; color: #0284c7;"><?php echo number_format($totGrossRev); ?> TZS</div>
            <small style="color: var(--text-muted); font-size: 0.75rem;"><a href="payments.php" style="color: var(--primary);">View Payments &rarr;</a></small>
        </div>
        <div class="metric-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-receipt"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Platform Commission</div>
            <div class="metric-number" style="font-size: 1.25rem; color: #059669;"><?php echo number_format($totCommRev); ?> TZS</div>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Approved Deductions</small>
        </div>
        <div class="metric-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-percent"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Pending User Approvals</div>
            <div class="metric-number" style="color: <?php echo $pendingUsersCount > 0 ? '#d97706' : 'inherit'; ?>;">
                <?php echo $pendingUsersCount; ?>
            </div>
            <small style="color: var(--text-muted); font-size: 0.75rem;"><a href="users.php?status=pending" style="color: #b45309;">Review Users &rarr;</a></small>
        </div>
        <div class="metric-icon" style="background: #fffbeb; color: #d97706;"><i class="fa-solid fa-user-clock"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Approved Vendors</div>
            <div class="metric-number"><?php echo $verifiedCount; ?></div>
        </div>
        <div class="metric-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-store"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Customer Event Requests</div>
            <div class="metric-number"><?php echo $requestsCount; ?></div>
        </div>
        <div class="metric-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-bullhorn"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Registered Accounts</div>
            <div class="metric-number"><?php echo $usersCount; ?></div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-users"></i></div>
    </div>
</div>

<!-- Pending Verifications Queue (Trust & Safety) -->
<div class="content-box" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-shield-halved" style="color: var(--accent); margin-right: 8px;"></i> Vendor Verification Queue (KYC)</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">Unverified vendors remain hidden from public search until you review their portfolio and approve them.</p>
        </div>
        <span class="badge-status <?php echo count($pendingVendors) > 0 ? 'badge-rejected-status' : 'badge-verified-status'; ?>">
            <?php echo count($pendingVendors); ?> Awaiting Review
        </span>
    </div>

    <?php if (empty($pendingVendors)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-circle-check" style="font-size: 2.5rem; color: #10b981; margin-bottom: 10px;"></i>
            <p style="font-weight: 600; margin-bottom: 4px;">Queue is Clear!</p>
            <p style="font-size: 0.85rem;">All vendor registrations have been reviewed. No pending approvals at this moment.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Brand / Business</th>
                        <th>Owner & Contacts</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Portfolio / Proof</th>
                        <th>KYC Decision</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingVendors as $pv): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($pv['business_name']); ?></strong><br>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Reg: <?php echo date('M d, Y', strtotime($pv['created_at'])); ?></span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($pv['owner_name']); ?><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($pv['owner_phone']); ?></small><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($pv['owner_email']); ?></small>
                        </td>
                        <td><span class="logo-badge" style="background: var(--primary);"><?php echo htmlspecialchars($pv['category_name']); ?></span></td>
                        <td><?php echo htmlspecialchars($pv['district'] . ', ' . $pv['region']); ?></td>
                        <td>
                            <?php if (!empty($pv['instagram_link'])): ?>
                                <a href="<?php echo htmlspecialchars($pv['instagram_link']); ?>" target="_blank" style="color: var(--primary); font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fa-brands fa-instagram" style="color: #e1306c;"></i> View Portfolio
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.85rem;">No link provided</span>
                            <?php endif; ?>
                            <br>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $pv['whatsapp_number']); ?>?text=Hello%20<?php echo urlencode($pv['business_name']); ?>%2C%20this%20is%20Nuustar%20Events%20Admin%20regarding%20your%20vendor%20verification." target="_blank" style="color: #059669; font-size: 0.8rem; font-weight: 600;">
                                <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                            </a>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="dashboard.php?action=approve&id=<?php echo $pv['id']; ?>" class="btn btn-primary btn-sm" onclick="return confirm('Approve this vendor and publish them to public search?')">
                                    <i class="fa-solid fa-check"></i> Approve
                                </a>
                                <a href="dashboard.php?action=reject&id=<?php echo $pv['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;" onclick="return confirm('Reject this application?')">
                                    <i class="fa-solid fa-xmark"></i> Reject
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

<!-- Recent Event Requests (Customer RFP Board) -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-clipboard-list" style="color: var(--primary); margin-right: 8px;"></i> Recent Event Requests Posted by Customers</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">Inquiries submitted on the public request board.</p>
        </div>
        <a href="requests.php" class="btn btn-outline btn-sm">View All Requests</a>
    </div>

    <?php if (empty($recentRequests)): ?>
        <p style="color: var(--text-muted); padding: 20px; text-align: center;">No event requests submitted yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Event & Services</th>
                        <th>Date & Location</th>
                        <th>Guests & Budget</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentRequests as $req): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($req['customer_name']); ?></strong><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($req['phone']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($req['event_type']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Needed: <?php echo htmlspecialchars($req['services_needed']); ?></span>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($req['event_date'])); ?><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($req['location']); ?></small>
                        </td>
                        <td>
                            <?php echo (int)$req['guest_count']; ?> Guests<br>
                            <strong style="color: #059669; font-size: 0.85rem;"><?php echo htmlspecialchars($req['budget_tzs']); ?></strong>
                        </td>
                        <td>
                            <span class="badge-status <?php echo $req['status'] === 'open' ? 'badge-verified-status' : 'badge-pending-status'; ?>">
                                <?php echo htmlspecialchars($req['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $req['phone']); ?>?text=Hello%20<?php echo urlencode($req['customer_name']); ?>%2C%20this%20is%20Nuustar%20Events%20Admin%20regarding%20your%20request%20for%20<?php echo urlencode($req['event_type']); ?>." target="_blank" class="btn btn-whatsapp btn-sm">
                                <i class="fa-brands fa-whatsapp"></i> Chat Client
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
