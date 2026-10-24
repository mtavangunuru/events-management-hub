<?php
$currentPage = 'requests';
$pageTitle = 'Event Requests (RFP Board)';
require_once 'includes/header.php';

$notice = '';

// Handle Status Changes
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reqId = (int)$_GET['id'];
    $act = $_GET['action'];

    if ($act === 'close') {
        $up = $pdo->prepare("UPDATE event_requests SET status = 'closed' WHERE id = :id");
        $up->execute(['id' => $reqId]);
        $notice = "Request #{$reqId} marked as closed.";
    } elseif ($act === 'reopen') {
        $up = $pdo->prepare("UPDATE event_requests SET status = 'open' WHERE id = :id");
        $up->execute(['id' => $reqId]);
        $notice = "Request #{$reqId} reopened.";
    } elseif ($act === 'delete') {
        $del = $pdo->prepare("DELETE FROM event_requests WHERE id = :id");
        $del->execute(['id' => $reqId]);
        $notice = "Request #{$reqId} deleted.";
    }
}

// Filter by status
$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT * FROM event_requests WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND status = :status";
    $params['status'] = $statusFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

$countOpen = (int)$pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'open'")->fetchColumn();
$countClosed = (int)$pdo->query("SELECT COUNT(*) FROM event_requests WHERE status = 'closed'")->fetchColumn();
$countTotal = (int)$pdo->query("SELECT COUNT(*) FROM event_requests")->fetchColumn();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-clipboard-list" style="color: var(--primary); margin-right: 8px;"></i> Customer Event Requests (RFP Board)</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Leads and requests submitted by customers looking for vendors across Tanzania.</p>
</div>

<?php if (!empty($notice)): ?>
    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($notice); ?>
    </div>
<?php endif; ?>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="requests.php" class="filter-tab-btn <?php echo empty($statusFilter) ? 'active' : ''; ?>">
        All Requests (<?php echo $countTotal; ?>)
    </a>
    <a href="requests.php?status=open" class="filter-tab-btn <?php echo $statusFilter === 'open' ? 'active' : ''; ?>">
        Open Leads (<?php echo $countOpen; ?>)
    </a>
    <a href="requests.php?status=closed" class="filter-tab-btn <?php echo $statusFilter === 'closed' ? 'active' : ''; ?>">
        Closed / Fulfilled (<?php echo $countClosed; ?>)
    </a>
</div>

<div class="content-box">
    <?php if (empty($requests)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size: 2rem; margin-bottom: 10px; color: #cbd5e1;"></i>
            <p>No event requests found in this category.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Event Type & Services</th>
                        <th>Event Date & Hall</th>
                        <th>Guests & Budget</th>
                        <th>Specific Notes</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td>#<?php echo $r['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($r['customer_name']); ?></strong><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($r['phone']); ?></small>
                        </td>
                        <td>
                            <strong style="color: var(--primary);"><?php echo htmlspecialchars($r['event_type']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Required: <?php echo htmlspecialchars($r['services_needed']); ?></span>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($r['event_date'])); ?><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($r['location']); ?></small>
                        </td>
                        <td>
                            <?php echo (int)$r['guest_count']; ?> Guests<br>
                            <strong style="color: #059669; font-size: 0.85rem;"><?php echo htmlspecialchars($r['budget_tzs']); ?></strong>
                        </td>
                        <td style="max-width: 250px; font-size: 0.85rem; color: var(--text-main);">
                            <?php echo nl2br(htmlspecialchars($r['details'] ?? '')); ?>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'open'): ?>
                                <span class="badge-status badge-verified-status"><i class="fa-solid fa-circle-check"></i> Open</span>
                            <?php else: ?>
                                <span class="badge-status badge-rejected-status"><i class="fa-solid fa-check-double"></i> Closed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $r['phone']); ?>?text=Hello%20<?php echo urlencode($r['customer_name']); ?>%2C%20this%20is%20Nuustar%20Events%20Admin%20regarding%20your%20request%20for%20<?php echo urlencode($r['event_type']); ?>." target="_blank" class="btn btn-whatsapp btn-sm" title="WhatsApp Customer">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>

                                <?php if ($r['status'] === 'open'): ?>
                                    <a href="requests.php?action=close&id=<?php echo $r['id']; ?>" class="btn btn-outline btn-sm" title="Mark as Closed" onclick="return confirm('Mark this request as fulfilled/closed?')">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="requests.php?action=reopen&id=<?php echo $r['id']; ?>" class="btn btn-outline btn-sm" title="Reopen Request" onclick="return confirm('Reopen this request?')">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="requests.php?action=delete&id=<?php echo $r['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;" title="Delete" onclick="return confirm('Delete this request?')">
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

<?php require_once 'includes/footer.php'; ?>
