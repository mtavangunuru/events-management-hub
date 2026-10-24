<?php
$currentPage = isset($_GET['status']) && $_GET['status'] === 'pending' ? 'verification' : 'vendors';
$pageTitle = 'Vendor Management & KYC';
require_once 'includes/header.php';

// Handle Action (Approve, Reject, Delete, Update Commission)
$notice = '';
$error = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $targetId = (int)$_GET['id'];
    
    if ($action === 'approve') {
        // Fetch user_id for this vendor
        $vData = $pdo->prepare("SELECT user_id, business_name FROM vendor_profiles WHERE id = :id");
        $vData->execute(['id' => $targetId]);
        $vRow = $vData->fetch();

        // Update vendor profile to verified
        $upStmt = $pdo->prepare("UPDATE vendor_profiles SET verification_status = 'verified' WHERE id = :id");
        $upStmt->execute(['id' => $targetId]);

        // Also activate the user login account
        if ($vRow) {
            $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :uid")->execute(['uid' => $vRow['user_id']]);
            // Add notification
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Vendor Profile Approved!', 'Your vendor profile has been verified and published! You can now add services and receive bookings.', 'account')");
            $notif->execute(['uid' => $vRow['user_id']]);
        }

        $notice = "Vendor #{$targetId} ({$vRow['business_name']}) has been successfully APPROVED and PUBLISHED to the directory!";
    } elseif ($action === 'reject') {
        $upStmt = $pdo->prepare("UPDATE vendor_profiles SET verification_status = 'rejected' WHERE id = :id");
        $upStmt->execute(['id' => $targetId]);
        $notice = "Vendor ID #{$targetId} status changed to rejected/suspended.";
    } elseif ($action === 'delete') {
        $delStmt = $pdo->prepare("DELETE FROM vendor_profiles WHERE id = :id");
        $delStmt->execute(['id' => $targetId]);
        $notice = "Vendor ID #{$targetId} has been permanently deleted.";
    }
}

// Handle Update Commission Rate & Payout Details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_commission'])) {
    $vId = (int)$_POST['vendor_id'];
    $newRate = (float)$_POST['commission_rate'];
    $pMethod = trim($_POST['payout_method'] ?? 'M-Pesa');
    $pAccName = trim($_POST['payout_account_name'] ?? '');
    $pAccNum = trim($_POST['payout_account_number'] ?? '');
    $pBank = trim($_POST['payout_bank_name'] ?? '');

    if ($newRate < 0 || $newRate > 100) {
        $error = "Commission rate must be between 0% and 100%.";
    } else {
        $upComm = $pdo->prepare("
            UPDATE vendor_profiles 
            SET commission_rate = :crate, 
                payout_method = :pmethod, 
                payout_account_name = :pname, 
                payout_account_number = :pnum, 
                payout_bank_name = :pbank 
            WHERE id = :id
        ");
        $upComm->execute([
            'crate' => $newRate,
            'pmethod' => $pMethod,
            'pname' => $pAccName,
            'pnum' => $pAccNum,
            'pbank' => $pBank,
            'id' => $vId
        ]);
        $notice = "Vendor ID #{$vId} commission rate updated to {$newRate}% and payout details saved.";
    }
}

// Fetch categories for filtering
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();

// Build Vendor Query with Filters
$filterStatus = $_GET['status'] ?? '';
$filterCat = $_GET['category'] ?? '';
$searchKeyword = trim($_GET['search'] ?? '');

$sql = "
    SELECT vp.*, u.name AS owner_name, u.email AS owner_email, u.phone AS owner_phone, u.status AS user_account_status, c.name AS category_name 
    FROM vendor_profiles vp 
    JOIN users u ON vp.user_id = u.id 
    JOIN categories c ON vp.category_id = c.id 
    WHERE 1=1
";
$params = [];

if (!empty($filterStatus)) {
    $sql .= " AND vp.verification_status = :status";
    $params['status'] = $filterStatus;
}

if (!empty($filterCat)) {
    $sql .= " AND vp.category_id = :cat_id";
    $params['cat_id'] = (int)$filterCat;
}

if (!empty($searchKeyword)) {
    $sql .= " AND (vp.business_name LIKE :kw1 OR u.name LIKE :kw2 OR vp.region LIKE :kw3 OR vp.district LIKE :kw4)";
    $params['kw1'] = '%' . $searchKeyword . '%';
    $params['kw2'] = '%' . $searchKeyword . '%';
    $params['kw3'] = '%' . $searchKeyword . '%';
    $params['kw4'] = '%' . $searchKeyword . '%';
}

$sql .= " ORDER BY vp.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vendors = $stmt->fetchAll();

// Counts for filter tabs
$totalAll = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles")->fetchColumn();
$totalPending = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles WHERE verification_status = 'pending'")->fetchColumn();
$totalVerified = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles WHERE verification_status = 'verified'")->fetchColumn();
$totalRejected = (int)$pdo->query("SELECT COUNT(*) FROM vendor_profiles WHERE verification_status = 'rejected'")->fetchColumn();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-store" style="color: var(--primary); margin-right: 8px;"></i> Vendor Directory & Approvals</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Review vendor KYC registrations, approve profiles, configure commission deduction percentages, and track payout methods.</p>
</div>

<?php if (!empty($notice)): ?>
    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($notice); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Status Filter Tabs -->
<div class="filter-tabs">
    <a href="vendors.php" class="filter-tab-btn <?php echo empty($filterStatus) ? 'active' : ''; ?>">
        All Vendors (<?php echo $totalAll; ?>)
    </a>
    <a href="vendors.php?status=pending" class="filter-tab-btn <?php echo $filterStatus === 'pending' ? 'active' : ''; ?>" style="<?php echo $totalPending > 0 ? 'background: #fffbeb; border-color: #f59e0b; color: #b45309; font-weight: 700;' : ''; ?>">
        <i class="fa-solid fa-clock"></i> Pending Approval (<?php echo $totalPending; ?>)
    </a>
    <a href="vendors.php?status=verified" class="filter-tab-btn <?php echo $filterStatus === 'verified' ? 'active' : ''; ?>">
        <i class="fa-solid fa-circle-check"></i> Approved & Verified (<?php echo $totalVerified; ?>)
    </a>
    <a href="vendors.php?status=rejected" class="filter-tab-btn <?php echo $filterStatus === 'rejected' ? 'active' : ''; ?>">
        <i class="fa-solid fa-ban"></i> Rejected / Suspended (<?php echo $totalRejected; ?>)
    </a>
</div>

<!-- Search & Category Filter Bar -->
<div class="content-box" style="padding: 18px 24px; margin-bottom: 25px;">
    <form action="vendors.php" method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <?php if (!empty($filterStatus)): ?>
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($filterStatus); ?>">
        <?php endif; ?>
        
        <div style="flex: 1; min-width: 220px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Search by business name, owner, city..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
        </div>

        <div style="min-width: 180px;">
            <select name="category" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $filterCat == $c['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 18px;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <a href="vendors.php" class="btn btn-outline btn-sm" style="padding: 10px 18px;">Reset</a>
    </form>
</div>

<!-- Vendors Table -->
<div class="content-box">
    <?php if (empty($vendors)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-magnifying-glass" style="font-size: 2rem; margin-bottom: 10px; color: #cbd5e1;"></i>
            <p>No vendors matched your query or selected filters.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Business & Category</th>
                        <th>Owner & Contacts</th>
                        <th>Approved Commission</th>
                        <th>Payout Method</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendors as $v): ?>
                    <tr style="<?php echo $v['verification_status'] === 'pending' ? 'background: #fffbeb;' : ''; ?>">
                        <td style="width: 55px;">
                            <img src="<?php echo (strpos($v['cover_image'], 'http') === 0 ? '' : '../') . htmlspecialchars($v['cover_image'] ?? 'uploads/logo.png'); ?>" alt="Cover" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($v['business_name']); ?></strong><br>
                            <span class="logo-badge" style="background: var(--primary); font-size: 0.7rem;"><?php echo htmlspecialchars($v['category_name']); ?></span>
                            <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 5px;"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($v['region']); ?></span><br>
                            <a href="../vendor-details.php?id=<?php echo $v['id']; ?>" target="_blank" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Preview Profile
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($v['owner_name']); ?><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($v['owner_phone']); ?></small><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($v['owner_email']); ?></small>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 1rem; color: var(--primary);"><?php echo number_format($v['commission_rate'], 1); ?>%</strong>
                                <button type="button" class="btn btn-outline btn-sm" style="padding: 2px 7px; font-size: 0.75rem;" title="Adjust Commission" onclick='openCommissionModal(<?php echo json_encode($v); ?>)'>
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                            </div>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Platform fee on sales</small>
                        </td>
                        <td style="font-size: 0.85rem;">
                            <?php if (!empty($v['payout_account_number'])): ?>
                                <strong><?php echo htmlspecialchars($v['payout_method']); ?></strong><br>
                                <code><?php echo htmlspecialchars($v['payout_account_number']); ?></code><br>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($v['payout_account_name'] ?? ''); ?></small>
                            <?php else: ?>
                                <span style="color: #9ca3af; font-style: italic;">Not set yet</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($v['verification_status'] === 'verified'): ?>
                                <span class="badge-status badge-verified-status"><i class="fa-solid fa-circle-check"></i> Verified</span>
                            <?php elseif ($v['verification_status'] === 'pending'): ?>
                                <span class="badge-status badge-pending-status" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;"><i class="fa-solid fa-clock"></i> Pending Approval</span>
                            <?php else: ?>
                                <span class="badge-status badge-rejected-status"><i class="fa-solid fa-ban"></i> Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <?php if ($v['verification_status'] !== 'verified'): ?>
                                    <a href="vendors.php?action=approve&id=<?php echo $v['id']; ?><?php echo !empty($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-primary btn-sm" style="background: #059669; border-color: #059669; padding: 6px 12px; font-weight: 700;" title="Approve & Publish" onclick="return confirm('Approve this vendor profile and activate their user account?')">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </a>
                                <?php endif; ?>

                                <?php if ($v['verification_status'] !== 'rejected'): ?>
                                    <a href="vendors.php?action=reject&id=<?php echo $v['id']; ?><?php echo !empty($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline btn-sm" style="color: #d97706; border-color: #d97706; padding: 6px 9px;" title="Reject or Suspend" onclick="return confirm('Suspend/Reject this vendor profile?')">
                                        <i class="fa-solid fa-pause"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $v['whatsapp_number']); ?>?text=Hello%20<?php echo urlencode($v['business_name']); ?>%2C%20this%20is%20Nuustar%20Events%20Admin." target="_blank" class="btn btn-whatsapp btn-sm" style="padding: 6px 9px;" title="Chat on WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>

                                <a href="vendors.php?action=delete&id=<?php echo $v['id']; ?><?php echo !empty($filterStatus) ? '&status=' . urlencode($filterStatus) : ''; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626; padding: 6px 9px;" title="Delete Vendor" onclick="return confirm('Permanently delete this vendor and all their packages? This action cannot be undone.')">
                                    <i class="fa-solid fa-trash"></i>
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

<!-- Commission & Payout Modal -->
<div id="commModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 480px; border-radius: var(--radius-sm); padding: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); position: relative;">
        <button type="button" onclick="closeCommissionModal()" style="position: absolute; top: 18px; right: 20px; background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
            <i class="fa-solid fa-percent" style="color: var(--primary);"></i> Commission & Payout Method
        </h2>
        <p id="commVendorTitle" style="font-size: 0.9rem; color: var(--primary); font-weight: 700; margin-bottom: 20px;"></p>

        <form action="vendors.php" method="POST">
            <input type="hidden" name="update_commission" value="1">
            <input type="hidden" name="vendor_id" id="commVendorId">

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">
                    Approved Platform Commission Percentage (%)
                </label>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="number" step="0.5" min="0" max="100" name="commission_rate" id="commRateInput" required style="width: 120px; padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 1rem; font-weight: 700;">
                    <span style="font-weight: 700; font-size: 1.1rem; color: var(--text-muted);">%</span>
                </div>
                <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                    This percentage is deducted from customer payments. The remaining balance is disbursed to the vendor.
                </small>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border); margin: 20px 0;">

            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--dark); margin-bottom: 12px;">Vendor Payout Account Details</h4>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 4px;">Payout Method</label>
                <select name="payout_method" id="commPayoutMethod" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    <option value="M-Pesa">Vodacom M-Pesa</option>
                    <option value="Tigo Pesa">Tigo Pesa</option>
                    <option value="Airtel Money">Airtel Money</option>
                    <option value="Bank Transfer">Bank Transfer (CRDB/NMB/Stanbic)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 4px;">Registered Account Name</label>
                <input type="text" name="payout_account_name" id="commPayoutName" placeholder="e.g. Elegance Decor Ltd" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 4px;">Account Number / Mobile Number</label>
                <input type="text" name="payout_account_number" id="commPayoutNumber" placeholder="e.g. 0712000111 or Bank Account #" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 4px;">Bank Name (If applicable)</label>
                <input type="text" name="payout_bank_name" id="commPayoutBank" placeholder="e.g. CRDB Bank Tanzania" style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeCommissionModal()" class="btn btn-outline btn-sm" style="padding: 10px 18px;">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 22px;">
                    <i class="fa-solid fa-check"></i> Save Payout Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCommissionModal(vendor) {
    document.getElementById('commVendorId').value = vendor.id;
    document.getElementById('commVendorTitle').textContent = vendor.business_name;
    document.getElementById('commRateInput').value = vendor.commission_rate || 10.00;
    document.getElementById('commPayoutMethod').value = vendor.payout_method || 'M-Pesa';
    document.getElementById('commPayoutName').value = vendor.payout_account_name || '';
    document.getElementById('commPayoutNumber').value = vendor.payout_account_number || '';
    document.getElementById('commPayoutBank').value = vendor.payout_bank_name || '';

    document.getElementById('commModal').style.display = 'flex';
}

function closeCommissionModal() {
    document.getElementById('commModal').style.display = 'none';
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('commModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
