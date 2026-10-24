<?php
$currentPage = 'payments';
$pageTitle = 'Track Payments & Commissions';
require_once 'includes/header.php';

$notice = '';
$error = '';

// Handle Payout Status Update (Admin disbursing remainder to vendor)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disburse_payout'])) {
    $bookingId = (int)$_POST['booking_id'];
    $payoutRef = trim($_POST['payout_reference'] ?? '');
    
    if (empty($payoutRef)) {
        $payoutRef = 'DISB-' . date('Ymd') . '-' . rand(1000, 9999);
    }

    $up = $pdo->prepare("
        UPDATE bookings 
        SET payout_status = 'paid', payout_date = NOW(), payout_reference = :pref 
        WHERE id = :id
    ");
    $up->execute(['pref' => $payoutRef, 'id' => $bookingId]);

    // Fetch booking & vendor details to notify vendor
    $bStmt = $pdo->prepare("SELECT b.*, vp.user_id as vendor_user_id, vp.business_name FROM bookings b JOIN vendor_profiles vp ON b.vendor_id = vp.id WHERE b.id = :id");
    $bStmt->execute(['id' => $bookingId]);
    $bData = $bStmt->fetch();

    if ($bData && !empty($bData['vendor_user_id'])) {
        $msg = "Your vendor payout of TZS " . number_format($bData['vendor_payout']) . " for booking #" . $bData['booking_ref'] . " has been disbursed! (Reference: " . $payoutRef . ")";
        $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, 'Payout Disbursed!', :msg, 'payout')")->execute([
            'uid' => $bData['vendor_user_id'],
            'msg' => $msg
        ]);
    }

    $notice = "Payout for Booking #{$bData['booking_ref']} marked as DISBURSED successfully! (Reference: {$payoutRef})";
}

// Filter variables
$filterPayoutStatus = $_GET['payout_status'] ?? '';
$searchKeyword = trim($_GET['search'] ?? '');

$sql = "
    SELECT b.*, vp.business_name, vp.payout_method, vp.payout_account_name, vp.payout_account_number, vp.payout_bank_name, vp.whatsapp_number as vendor_phone, c.name as category_name
    FROM bookings b
    JOIN vendor_profiles vp ON b.vendor_id = vp.id
    JOIN categories c ON vp.category_id = c.id
    WHERE 1=1
";
$params = [];

if (!empty($filterPayoutStatus)) {
    $sql .= " AND b.payout_status = :pstatus";
    $params['pstatus'] = $filterPayoutStatus;
}

if (!empty($searchKeyword)) {
    $sql .= " AND (b.booking_ref LIKE :kw1 OR b.customer_name LIKE :kw2 OR b.customer_phone LIKE :kw3 OR vp.business_name LIKE :kw4)";
    $params['kw1'] = '%' . $searchKeyword . '%';
    $params['kw2'] = '%' . $searchKeyword . '%';
    $params['kw3'] = '%' . $searchKeyword . '%';
    $params['kw4'] = '%' . $searchKeyword . '%';
}

$sql .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// KPI Aggregations
$totGross = (float)$pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();
$totCommission = (float)$pdo->query("SELECT COALESCE(SUM(commission_amount), 0) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();
$totVendorPayouts = (float)$pdo->query("SELECT COALESCE(SUM(vendor_payout), 0) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();
$pendingPayoutsSum = (float)$pdo->query("SELECT COALESCE(SUM(vendor_payout), 0) FROM bookings WHERE payment_status = 'paid' AND payout_status = 'pending'")->fetchColumn();
$disbursedPayoutsSum = (float)$pdo->query("SELECT COALESCE(SUM(vendor_payout), 0) FROM bookings WHERE payment_status = 'paid' AND payout_status = 'paid'")->fetchColumn();
$countTransactions = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE payment_status = 'paid'")->fetchColumn();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-money-bill-transfer" style="color: var(--primary); margin-right: 8px;"></i> Track Payments & Commission Deductions</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Monitor customer payments, calculate platform percentage commissions, and manage vendor payout disbursements.</p>
</div>

<?php if (!empty($notice)): ?>
    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($notice); ?>
    </div>
<?php endif; ?>

<!-- Financial Overview KPI Grid -->
<div class="metrics-grid">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Customer Volume</div>
            <div class="metric-number" style="font-size: 1.35rem; color: #0f172a;"><?php echo number_format($totGross); ?> TZS</div>
            <small style="color: var(--text-muted); font-size: 0.75rem;"><?php echo $countTransactions; ?> Completed Transactions</small>
        </div>
        <div class="metric-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-receipt"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Platform Deducted Revenue</div>
            <div class="metric-number" style="font-size: 1.35rem; color: #059669;"><?php echo number_format($totCommission); ?> TZS</div>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Retained Commission Percentage</small>
        </div>
        <div class="metric-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-percent"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Vendor Remainder (Total)</div>
            <div class="metric-number" style="font-size: 1.35rem; color: #4338ca;"><?php echo number_format($totVendorPayouts); ?> TZS</div>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Allocated to service vendors</small>
        </div>
        <div class="metric-icon" style="background: #e0e7ff; color: #4338ca;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Pending Vendor Payouts</div>
            <div class="metric-number" style="font-size: 1.35rem; color: <?php echo $pendingPayoutsSum > 0 ? '#d97706' : '#059669'; ?>;">
                <?php echo number_format($pendingPayoutsSum); ?> TZS
            </div>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Awaiting disbursement</small>
        </div>
        <div class="metric-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-clock-rotate-left"></i></div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="payments.php" class="filter-tab-btn <?php echo empty($filterPayoutStatus) ? 'active' : ''; ?>">
        All Payments (<?php echo count($bookings); ?>)
    </a>
    <a href="payments.php?payout_status=pending" class="filter-tab-btn <?php echo $filterPayoutStatus === 'pending' ? 'active' : ''; ?>" style="<?php echo $pendingPayoutsSum > 0 ? 'background: #fffbeb; border-color: #f59e0b; color: #b45309; font-weight: 700;' : ''; ?>">
        <i class="fa-solid fa-hourglass-half"></i> Pending Payouts to Vendors
    </a>
    <a href="payments.php?payout_status=paid" class="filter-tab-btn <?php echo $filterPayoutStatus === 'paid' ? 'active' : ''; ?>">
        <i class="fa-solid fa-circle-check"></i> Disbursed Payouts
    </a>
</div>

<!-- Search Bar -->
<div class="content-box" style="padding: 16px 20px; margin-bottom: 20px;">
    <form action="payments.php" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <?php if (!empty($filterPayoutStatus)): ?><input type="hidden" name="payout_status" value="<?php echo htmlspecialchars($filterPayoutStatus); ?>"><?php endif; ?>
        <div style="flex: 1; min-width: 250px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Search by receipt #, customer, vendor, or phone..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="padding: 9px 18px;">
            <i class="fa-solid fa-magnifying-glass"></i> Filter
        </button>
        <a href="payments.php" class="btn btn-outline btn-sm" style="padding: 9px 15px;">Reset</a>
    </form>
</div>

<!-- Transactions Table -->
<div class="content-box">
    <?php if (empty($bookings)): ?>
        <div style="text-align: center; padding: 45px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-receipt" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px;"></i>
            <h3 style="color: var(--dark); margin-bottom: 6px;">No Payment Records Found</h3>
            <p style="font-size: 0.9rem;">Customer payments for service packages will be recorded and calculated here in real time.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Receipt & Date</th>
                        <th>Customer</th>
                        <th>Vendor & Service</th>
                        <th>Total Paid</th>
                        <th>Commission Deducted</th>
                        <th>Vendor Remainder</th>
                        <th>Vendor Payout Account</th>
                        <th>Payout Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary); font-family: monospace; font-size: 0.95rem;"><?php echo htmlspecialchars($b['booking_ref']); ?></strong><br>
                            <small style="color: var(--text-muted);"><i class="fa-regular fa-clock"></i> <?php echo date('M d, Y H:i', strtotime($b['created_at'])); ?></small><br>
                            <span class="badge-status badge-verified-status" style="font-size: 0.65rem; padding: 2px 6px;">
                                <i class="fa-solid fa-check"></i> <?php echo strtoupper($b['payment_status']); ?>
                            </span>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($b['customer_name']); ?></strong><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($b['customer_phone']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($b['business_name']); ?></strong><br>
                            <span style="font-size: 0.85rem; color: var(--dark); font-weight: 600;"><?php echo htmlspecialchars($b['package_title']); ?></span><br>
                            <small style="color: var(--text-muted);"><i class="fa-regular fa-calendar"></i> Event: <?php echo date('M d, Y', strtotime($b['event_date'])); ?></small>
                        </td>
                        <td>
                            <strong style="font-size: 1.05rem;"><?php echo number_format($b['total_amount']); ?> TZS</strong><br>
                            <small style="color: var(--text-muted); font-size: 0.75rem;"><?php echo htmlspecialchars($b['payment_method']); ?></small>
                        </td>
                        <td style="color: #059669;">
                            <strong>- <?php echo number_format($b['commission_amount']); ?> TZS</strong><br>
                            <span class="logo-badge" style="background: #059669; font-size: 0.7rem;"><?php echo number_format($b['commission_rate'], 1); ?>% Rate</span>
                        </td>
                        <td style="color: var(--primary);">
                            <strong style="font-size: 1.05rem;"><?php echo number_format($b['vendor_payout']); ?> TZS</strong><br>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Due to Vendor</small>
                        </td>
                        <td style="font-size: 0.85rem;">
                            <?php if (!empty($b['payout_account_number'])): ?>
                                <strong><?php echo htmlspecialchars($b['payout_method']); ?>:</strong> <?php echo htmlspecialchars($b['payout_account_number']); ?><br>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($b['payout_account_name'] ?? ''); ?></small>
                                <?php if (!empty($b['payout_bank_name'])): ?><br><small style="color: var(--text-muted);"><?php echo htmlspecialchars($b['payout_bank_name']); ?></small><?php endif; ?>
                            <?php else: ?>
                                <small style="color: #9ca3af; font-style: italic;">No account on file</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($b['payout_status'] === 'paid'): ?>
                                <span class="badge-status badge-verified-status"><i class="fa-solid fa-circle-check"></i> Disbursed</span><br>
                                <small style="color: var(--text-muted); font-size: 0.7rem;">Ref: <?php echo htmlspecialchars($b['payout_reference'] ?? 'Done'); ?></small>
                            <?php else: ?>
                                <span class="badge-status badge-pending-status" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;"><i class="fa-solid fa-clock"></i> Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <?php if ($b['payout_status'] !== 'paid'): ?>
                                    <button type="button" class="btn btn-primary btn-sm" style="background: #059669; border-color: #059669; padding: 5px 9px; font-weight: 700;" title="Disburse Vendor Remainder" onclick='openDisburseModal(<?php echo json_encode($b); ?>)'>
                                        <i class="fa-solid fa-paper-plane"></i> Send Payout
                                    </button>
                                <?php endif; ?>

                                <a href="../booking-confirmation.php?ref=<?php echo urlencode($b['booking_ref']); ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 5px 9px;" title="View Voucher Receipt">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>

                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $b['vendor_phone']); ?>?text=Hello%20<?php echo urlencode($b['business_name']); ?>%2C%20Nuustar%20Events%20has%20received%20a%20confirmed%20payment%20for%20Booking%20<?php echo urlencode($b['booking_ref']); ?>." target="_blank" class="btn btn-whatsapp btn-sm" style="padding: 5px 9px;" title="Chat Vendor">
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

<!-- Disburse Payout Modal -->
<div id="disburseModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 480px; border-radius: var(--radius-sm); padding: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); position: relative;">
        <button type="button" onclick="closeDisburseModal()" style="position: absolute; top: 18px; right: 20px; background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
            <i class="fa-solid fa-paper-plane" style="color: #059669;"></i> Confirm Vendor Payout Disbursement
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">Record transfer of the remaining balance to the vendor's payment account.</p>

        <form action="payments.php" method="POST">
            <input type="hidden" name="disburse_payout" value="1">
            <input type="hidden" name="booking_id" id="disburseBookingId">

            <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 15px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Booking Ref:</span>
                    <strong id="disburseBookingRef"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Vendor:</span>
                    <strong id="disburseVendorName"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Gross Paid:</span>
                    <span id="disburseGross"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #059669;">
                    <span style="font-size: 0.85rem;">Platform Fee Deducted:</span>
                    <strong id="disburseComm"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px solid var(--border); font-size: 1.05rem;">
                    <span>Net Remainder Due:</span>
                    <strong id="disburseNet" style="color: var(--primary);"></strong>
                </div>
            </div>

            <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: var(--radius-sm); padding: 12px; margin-bottom: 18px; font-size: 0.85rem;">
                <strong><i class="fa-solid fa-wallet"></i> Destination Payout Account:</strong><br>
                <span id="disburseAccount"></span>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 5px;">Disbursement Transaction Reference (Bank/M-Pesa Code)</label>
                <input type="text" name="payout_reference" placeholder="e.g. MPESA98765432 or CRDB-TR-1234" required style="width: 100%; padding: 9px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeDisburseModal()" class="btn btn-outline btn-sm" style="padding: 10px 18px;">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm" style="background: #059669; border-color: #059669; padding: 10px 22px;">
                    <i class="fa-solid fa-check"></i> Confirm Disbursement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDisburseModal(booking) {
    document.getElementById('disburseBookingId').value = booking.id;
    document.getElementById('disburseBookingRef').textContent = booking.booking_ref;
    document.getElementById('disburseVendorName').textContent = booking.business_name;
    document.getElementById('disburseGross').textContent = Number(booking.total_amount).toLocaleString() + ' TZS';
    document.getElementById('disburseComm').textContent = '-' + Number(booking.commission_amount).toLocaleString() + ' TZS (' + booking.commission_rate + '%)';
    document.getElementById('disburseNet').textContent = Number(booking.vendor_payout).toLocaleString() + ' TZS';

    let accText = (booking.payout_method || 'Mobile Money') + ' - ' + (booking.payout_account_number || 'No number on file') + ' (' + (booking.payout_account_name || 'N/A') + ')';
    if (booking.payout_bank_name) {
        accText += ' - ' + booking.payout_bank_name;
    }
    document.getElementById('disburseAccount').textContent = accText;

    document.getElementById('disburseModal').style.display = 'flex';
}

function closeDisburseModal() {
    document.getElementById('disburseModal').style.display = 'none';
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('disburseModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
