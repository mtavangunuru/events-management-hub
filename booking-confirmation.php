<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

$bookingRef = trim($_GET['ref'] ?? '');
$booking = null;

if (!empty($bookingRef)) {
    $stmt = $pdo->prepare("
        SELECT b.*, vp.business_name, vp.whatsapp_number as vendor_phone, vp.cover_image, c.name as category_name
        FROM bookings b
        JOIN vendor_profiles vp ON b.vendor_id = vp.id
        JOIN categories c ON vp.category_id = c.id
        WHERE b.booking_ref = :bref
        LIMIT 1
    ");
    $stmt->execute(['bref' => $bookingRef]);
    $booking = $stmt->fetch();
}

$pageTitle = $booking ? "Payment Receipt #" . htmlspecialchars($booking['booking_ref']) : "Booking Confirmation";
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 850px; padding: 40px 20px 80px;">
    <?php if ($booking): ?>
        <!-- Success Alert Header -->
        <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: var(--radius-sm); padding: 30px; text-align: center; margin-bottom: 25px;">
            <div style="width: 70px; height: 70px; background: #059669; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 15px;">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 800; color: #065f46; margin-bottom: 6px;">Payment Received & Verified!</h1>
            <p style="color: #047857; font-size: 0.95rem; max-width: 550px; margin: 0 auto 15px;">
                Your payment has been secured in Escrow. Your booking for <strong><?php echo htmlspecialchars($booking['package_title']); ?></strong> with <strong><?php echo htmlspecialchars($booking['business_name']); ?></strong> is officially confirmed.
            </p>
            <div style="display: inline-block; background: #fff; border: 1px solid #a7f3d0; padding: 8px 18px; border-radius: 30px; font-size: 0.9rem; font-weight: 700; color: #065f46;">
                Booking Voucher Reference: <span style="font-family: monospace; font-size: 1.05rem;"><?php echo htmlspecialchars($booking['booking_ref']); ?></span>
            </div>
        </div>

        <!-- Official Printable Voucher / Receipt -->
        <div class="content-box" id="printableReceipt" style="padding: 35px; box-shadow: var(--card-shadow-hover); border: 1px solid var(--border); margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--primary); padding-bottom: 20px; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--dark); margin-bottom: 4px;">
                        <span style="color: var(--primary);">Nuustar</span> Events Tanzania
                    </h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">Official Payment Receipt & Confirmation Voucher</span>
                    <span style="font-size: 0.8rem; color: var(--text-muted);">Dar es Salaam, Tanzania • support@nuustar.com</span>
                </div>
                <div style="text-align: right;">
                    <div style="background: #ecfdf5; color: #059669; border: 1px solid #10b981; font-weight: 800; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px; display: inline-block; margin-bottom: 5px;">
                        <i class="fa-solid fa-shield-halved"></i> PAID & SECURED IN ESCROW
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                        Date: <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?>
                    </div>
                </div>
            </div>

            <!-- Client & Vendor Summary Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 18px;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Customer (Event Host)</span>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--dark); margin: 6px 0 4px;"><?php echo htmlspecialchars($booking['customer_name']); ?></h4>
                    <span style="font-size: 0.85rem; color: var(--text-muted); display: block;"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                    <span style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-top: 2px;"><i class="fa-solid fa-cake-candles"></i> <?php echo htmlspecialchars($booking['event_type']); ?></span>
                </div>

                <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 18px;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Service Provider (Vendor)</span>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--dark); margin: 6px 0 4px;"><?php echo htmlspecialchars($booking['business_name']); ?></h4>
                    <span style="font-size: 0.85rem; color: var(--text-muted); display: block;"><i class="fa-solid fa-tags"></i> <?php echo htmlspecialchars($booking['category_name']); ?></span>
                    <span style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-top: 2px;"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($booking['vendor_phone']); ?></span>
                </div>
            </div>

            <!-- Booking Service Details Table -->
            <table class="data-table" style="margin-bottom: 25px;">
                <thead>
                    <tr>
                        <th>Service / Package Description</th>
                        <th>Event Date</th>
                        <th>Scope</th>
                        <th style="text-align: right;">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($booking['package_title']); ?></strong><br>
                            <small style="color: var(--text-muted);"><?php echo htmlspecialchars($booking['notes'] ?? 'Standard package specifications'); ?></small>
                        </td>
                        <td>
                            <strong style="color: var(--primary);"><?php echo date('F d, Y', strtotime($booking['event_date'])); ?></strong>
                        </td>
                        <td><?php echo (int)$booking['guest_count']; ?> Guests</td>
                        <td style="text-align: right; font-weight: 800; font-size: 1.05rem; color: var(--dark);">
                            <?php echo number_format($booking['total_amount']); ?> TZS
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Financial Summary & Commission Transparency -->
            <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 25px; align-items: start;">
                <div style="background: #f0fdf4; border: 1px dashed #10b981; border-radius: var(--radius-sm); padding: 16px; font-size: 0.85rem; color: #166534; line-height: 1.5;">
                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 5px;"><i class="fa-solid fa-lock"></i> Payment Security & Notification</h4>
                    <p style="margin-bottom: 6px;">
                        This voucher serves as verified proof of payment. Payment reference: <code><?php echo htmlspecialchars($booking['payment_reference']); ?></code> via <?php echo htmlspecialchars($booking['payment_method']); ?>.
                    </p>
                    <p style="margin: 0; font-size: 0.8rem; color: #15803d;">
                        The approved platform commission (<?php echo number_format($booking['commission_rate'], 1); ?>%) has been accounted for, and remaining balance (TZS <?php echo number_format($booking['vendor_payout']); ?>) is allocated for vendor payout upon service execution.
                    </p>
                </div>

                <div style="background: #fafafa; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 18px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);">Gross Package Total:</span>
                        <strong><?php echo number_format($booking['total_amount']); ?> TZS</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);">Payment Processing:</span>
                        <span style="color: #059669; font-weight: 700;">0 TZS (Free)</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 2px solid var(--border); font-size: 1.15rem;">
                        <strong>Amount Paid (TZS):</strong>
                        <strong style="color: #059669;"><?php echo number_format($booking['total_amount']); ?> TZS</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="btn btn-outline" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print Payment Receipt
            </button>
            <?php 
            $cleanVenPhone = preg_replace('/[^0-9]/', '', $booking['vendor_phone']);
            $waVenMsg = rawurlencode("Hello " . $booking['business_name'] . ", I have just completed online payment for Booking #" . $booking['booking_ref'] . " (" . $booking['package_title'] . ") for " . $booking['event_date'] . " on Nuustar Events. Here is my booking confirmation!");
            ?>
            <a href="https://wa.me/<?php echo $cleanVenPhone; ?>?text=<?php echo $waVenMsg; ?>" target="_blank" class="btn btn-whatsapp">
                <i class="fa-brands fa-whatsapp"></i> Notify Vendor on WhatsApp
            </a>
            <a href="customer-dashboard.php" class="btn btn-primary">
                <i class="fa-solid fa-user-check"></i> Go to My Bookings
            </a>
        </div>
    <?php else: ?>
        <div class="content-box" style="text-align: center; padding: 60px 20px;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: #f59e0b; margin-bottom: 15px;"></i>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--dark); margin-bottom: 10px;">Booking Reference Not Found</h2>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 25px;">
                We could not locate this booking voucher in the database. Please check your reference code or visit your customer dashboard.
            </p>
            <a href="customer-dashboard.php" class="btn btn-primary">Go to Customer Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<style>
@media print {
    .navbar, footer, .btn, .filter-tabs, div[style*="justify-content: center"] {
        display: none !important;
    }
    #printableReceipt {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
