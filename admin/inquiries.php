<?php
$currentPage = 'inquiries';
$pageTitle = 'Direct Client Inquiries';
require_once 'includes/header.php';

$notice = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $inqId = (int)$_GET['id'];
    $del = $pdo->prepare("DELETE FROM inquiries WHERE id = :id");
    $del->execute(['id' => $inqId]);
    $notice = "Inquiry #{$inqId} deleted.";
}

// Fetch all inquiries with vendor profile and user info
$stmt = $pdo->query("
    SELECT i.*, vp.business_name, vp.whatsapp_number as vendor_phone, c.name as category_name 
    FROM inquiries i 
    JOIN vendor_profiles vp ON i.vendor_id = vp.id 
    JOIN categories c ON vp.category_id = c.id 
    ORDER BY i.created_at DESC
");
$inquiries = $stmt->fetchAll();
?>

<div style="margin-bottom: 25px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark);"><i class="fa-solid fa-comments" style="color: var(--primary); margin-right: 8px;"></i> Direct Vendor Quote Inquiries</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Monitor direct quote requests sent by clients to specific vendors on their profile pages.</p>
</div>

<?php if (!empty($notice)): ?>
    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($notice); ?>
    </div>
<?php endif; ?>

<div class="content-box">
    <?php if (empty($inquiries)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-regular fa-envelope-open" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
            <p>No customer quote inquiries submitted to vendors yet.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Target Vendor</th>
                        <th>Customer Contact</th>
                        <th>Event Type & Date</th>
                        <th>Selected Package</th>
                        <th>Customer Notes</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inq): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($inq['business_name']); ?></strong><br>
                            <span class="logo-badge" style="background: var(--primary); font-size: 0.7rem;"><?php echo htmlspecialchars($inq['category_name']); ?></span><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($inq['vendor_phone']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($inq['customer_name']); ?></strong><br>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($inq['customer_phone']); ?></small>
                        </td>
                        <td>
                            <strong style="color: var(--dark);"><?php echo htmlspecialchars($inq['event_type']); ?></strong><br>
                            <span><?php echo date('M d, Y', strtotime($inq['event_date'])); ?></span><br>
                            <small style="color: var(--text-muted);"><?php echo (int)$inq['guest_count']; ?> Guests</small>
                        </td>
                        <td>
                            <?php
                            $pkgName = 'Custom Quote';
                            $displayNotes = $inq['notes'] ?? '';
                            if (preg_match('/Selected Package:\s*([^|]+)/i', $displayNotes, $m)) {
                                $pkgName = trim($m[1]);
                                $displayNotes = trim(preg_replace('/Selected Package:\s*[^|]+(\| Notes:\s*)?/i', '', $displayNotes));
                            }
                            ?>
                            <strong style="color: var(--accent); font-size: 0.9rem;">
                                <?php echo htmlspecialchars($pkgName); ?>
                            </strong>
                        </td>
                        <td style="max-width: 250px; font-size: 0.85rem; color: var(--text-main);">
                            <?php echo !empty($displayNotes) ? nl2br(htmlspecialchars($displayNotes)) : '<span style="color: var(--text-muted); font-style: italic;">No additional notes</span>'; ?>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">
                            <?php echo date('M d, Y H:i', strtotime($inq['created_at'])); ?>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $inq['customer_phone']); ?>" target="_blank" class="btn btn-whatsapp btn-sm" title="Chat Customer on WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i> Customer
                                </a>
                                <a href="inquiries.php?action=delete&id=<?php echo $inq['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;" title="Delete" onclick="return confirm('Delete this inquiry record?')">
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
