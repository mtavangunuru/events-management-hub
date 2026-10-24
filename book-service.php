<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

$vendorId = (int)($_GET['vendor_id'] ?? 0);
$packageId = (int)($_GET['package_id'] ?? 0);

// Fetch vendor details
$vStmt = $pdo->prepare("
    SELECT vp.*, c.name AS category_name, u.email, u.name AS contact_person 
    FROM vendor_profiles vp 
    JOIN categories c ON vp.category_id = c.id 
    JOIN users u ON vp.user_id = u.id 
    WHERE vp.id = :id
");
$vStmt->execute(['id' => $vendorId]);
$vendor = $vStmt->fetch();

if (!$vendor) {
    header("Location: vendors.php");
    exit;
}

// Fetch package details
$pStmt = $pdo->prepare("SELECT * FROM packages WHERE id = :pid AND vendor_id = :vid");
$pStmt->execute(['pid' => $packageId, 'vid' => $vendorId]);
$package = $pStmt->fetch();

if (!$package) {
    // If no package selected, get the first package of this vendor or redirect
    $pStmt = $pdo->prepare("SELECT * FROM packages WHERE vendor_id = :vid ORDER BY price_tzs ASC LIMIT 1");
    $pStmt->execute(['vid' => $vendorId]);
    $package = $pStmt->fetch();
    if (!$package) {
        die("<div style='text-align:center; padding:50px;'><h2>Vendor has no active service packages yet.</h2><a href='vendor-details.php?id={$vendorId}'>Back to Vendor</a></div>");
    }
}

// Check logged in user status
$isLoggedIn = isset($_SESSION['user_id']);
$userStatus = 'guest';
$customerUser = null;

if ($isLoggedIn) {
    $uStmt = $pdo->prepare("SELECT * FROM users WHERE id = :uid LIMIT 1");
    $uStmt->execute(['uid' => $_SESSION['user_id']]);
    $customerUser = $uStmt->fetch();
    if ($customerUser) {
        $userStatus = $customerUser['status']; // 'active', 'pending', 'suspended'
    }
}

$errorMsg = '';

// Handle Payment & Booking Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    if (!$isLoggedIn) {
        $errorMsg = "Please log in or register before completing your payment.";
    } elseif ($userStatus === 'pending') {
        $errorMsg = "Your customer account is currently awaiting administrator approval. You cannot make payments until approved.";
    } elseif ($userStatus !== 'active') {
        $errorMsg = "Your account is not active. Please contact Nuustar Events support.";
    } else {
        $eventDate = trim($_POST['event_date'] ?? '');
        $eventType = trim($_POST['event_type'] ?? 'Wedding');
        $guestCount = (int)($_POST['guest_count'] ?? 100);
        $notes = trim($_POST['notes'] ?? '');
        $custName = trim($_POST['customer_name'] ?? $customerUser['name']);
        $custPhone = trim($_POST['customer_phone'] ?? $customerUser['phone']);
        $payMethod = trim($_POST['payment_method'] ?? 'Vodacom M-Pesa');
        $payerNumber = trim($_POST['payer_number'] ?? $custPhone);

        if (empty($eventDate) || empty($custPhone) || empty($custName)) {
            $errorMsg = "Please provide your event date, name, and contact phone number.";
        } else {
            // Calculate Financials:
            // "payment will be deducted according to percentage approved and remain send to vendor"
            $totalAmount = (float)$package['price_tzs'];
            $commissionRate = (float)($vendor['commission_rate'] ?? 10.00);
            $commissionAmount = round($totalAmount * ($commissionRate / 100), 2);
            $vendorPayout = round($totalAmount - $commissionAmount, 2);

            // Generate Booking Ref & Payment Ref
            $bookingRef = 'NUE-PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $paymentRef = strtoupper(explode(' ', $payMethod)[0]) . '-' . date('His') . '-' . rand(1000, 9999);

            // Insert into bookings table
            $insBooking = $pdo->prepare("
                INSERT INTO bookings (
                    booking_ref, customer_id, customer_name, customer_phone,
                    vendor_id, package_id, package_title, event_date, event_type,
                    guest_count, notes, total_amount, commission_rate, commission_amount,
                    vendor_payout, payment_method, payment_reference, payment_status,
                    payout_status, booking_status
                ) VALUES (
                    :bref, :cid, :cname, :cphone,
                    :vid, :pid, :ptitle, :edate, :etype,
                    :guests, :notes, :total, :crate, :camount,
                    :vpayout, :pmethod, :pref, 'paid',
                    'pending', 'confirmed'
                )
            ");
            $insBooking->execute([
                'bref' => $bookingRef,
                'cid' => $customerUser['id'],
                'cname' => $custName,
                'cphone' => $custPhone,
                'vid' => $vendor['id'],
                'pid' => $package['id'],
                'ptitle' => $package['title'],
                'edate' => $eventDate,
                'etype' => $eventType,
                'guests' => $guestCount,
                'notes' => $notes,
                'total' => $totalAmount,
                'crate' => $commissionRate,
                'camount' => $commissionAmount,
                'vpayout' => $vendorPayout,
                'pmethod' => $payMethod . ' (' . $payerNumber . ')',
                'pref' => $paymentRef
            ]);

            // Create notification for Customer
            $notifCust = $pdo->prepare("
                INSERT INTO notifications (user_id, title, message, type) 
                VALUES (:uid, 'Payment Confirmed & Secured!', :msg, 'payment')
            ");
            $notifCust->execute([
                'uid' => $customerUser['id'],
                'msg' => "Payment of TZS " . number_format($totalAmount) . " for '" . $package['title'] . "' with " . $vendor['business_name'] . " was successful! Booking Reference: " . $bookingRef
            ]);

            // Create notification for Vendor
            if (!empty($vendor['user_id'])) {
                $notifVend = $pdo->prepare("
                    INSERT INTO notifications (user_id, title, message, type) 
                    VALUES (:uid, 'New Paid Booking Received!', :msg, 'booking')
                ");
                $notifVend->execute([
                    'uid' => $vendor['user_id'],
                    'msg' => "New client booking! " . $custName . " paid TZS " . number_format($totalAmount) . " for '" . $package['title'] . "'. Platform fee (" . $commissionRate . "%): TZS " . number_format($commissionAmount) . ". Net remainder due to you: TZS " . number_format($vendorPayout) . "."
                ]);
            }

            // Redirect to receipt confirmation page
            header("Location: booking-confirmation.php?ref=" . urlencode($bookingRef));
            exit;
        }
    }
}

$pageTitle = "Checkout & Payment - " . htmlspecialchars($package['title']);
require_once 'includes/header.php';
?>

<div class="container" style="max-width: 1000px; padding: 40px 20px 80px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
        <a href="vendors.php" style="color: var(--primary);">Vendors</a> &rarr; 
        <a href="vendor-details.php?id=<?php echo $vendor['id']; ?>" style="color: var(--primary);"><?php echo htmlspecialchars($vendor['business_name']); ?></a> &rarr; 
        <span>Checkout & Payment</span>
    </div>

    <!-- Auth / Approval State Banners -->
    <?php if (!$isLoggedIn): ?>
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 5px solid var(--primary); padding: 18px 22px; border-radius: var(--radius-sm); margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--primary); margin-bottom: 4px;">
                        <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Sign in to complete your booking
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">
                        To secure your celebration date and receive your official payment receipt voucher, please log in with your customer account or register.
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="login.php" class="btn btn-primary btn-sm">Log In</a>
                    <a href="register.php" class="btn btn-outline btn-sm">Register as Host</a>
                </div>
            </div>
        </div>
    <?php elseif ($userStatus === 'pending'): ?>
        <div style="background: #fffbeb; border: 1px solid #f59e0b; border-left: 5px solid #d97706; padding: 20px 24px; border-radius: var(--radius-sm); margin-bottom: 25px;">
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 1.6rem; color: #d97706; margin-top: 3px;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #92400e; margin-bottom: 5px;">Your Customer Account is Awaiting Admin Approval</h3>
                    <p style="font-size: 0.9rem; color: #b45309; line-height: 1.5; margin-bottom: 8px;">
                        Thank you for registering on Nuustar Events! As a security precaution, customer accounts are verified and approved by the administrator before bookings and payments are activated.
                    </p>
                    <p style="font-size: 0.85rem; color: #78350f;">
                        Our administrative team will review your registration shortly. Once approved, you can return to this page and complete your payment instantly.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($errorMsg); ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1.6fr 1.1fr; gap: 30px; align-items: start;">
        <!-- Left Column: Booking Form -->
        <div class="content-box">
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
                <i class="fa-solid fa-calendar-check" style="color: var(--primary); margin-right: 8px;"></i> Reservation & Event Details
            </h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 22px;">
                Please provide the schedule and requirements for your upcoming event.
            </p>

            <form action="book-service.php?vendor_id=<?php echo $vendorId; ?>&package_id=<?php echo $package['id']; ?>" method="POST" id="checkoutForm">
                <input type="hidden" name="process_payment" value="1">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Event Date <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="event_date" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Occasion / Event Type <span style="color: #dc2626;">*</span></label>
                        <select name="event_type" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                            <option value="Wedding (Harusi)">Wedding (Harusi)</option>
                            <option value="Send-off Party">Send-off Party</option>
                            <option value="Kitchen Party">Kitchen Party</option>
                            <option value="Birthday Celebration">Birthday Celebration</option>
                            <option value="Corporate Gala / Conference">Corporate Gala / Conference</option>
                            <option value="Private Dinner / Anniversary">Private Dinner / Anniversary</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Estimated Guest Count</label>
                        <input type="number" name="guest_count" placeholder="e.g. 250" value="150" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Venue / Hall Location</label>
                        <input type="text" name="notes" placeholder="e.g. Mlimani City, Masaki, etc." style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Your Full Name <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="customer_name" value="<?php echo htmlspecialchars($customerUser['name'] ?? ''); ?>" placeholder="e.g. Amani Mrosso" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">WhatsApp / Phone Number <span style="color: #dc2626;">*</span></label>
                        <input type="tel" name="customer_phone" value="<?php echo htmlspecialchars($customerUser['phone'] ?? ''); ?>" placeholder="+255 7XX XXX XXX" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark); margin-bottom: 12px;">
                    <i class="fa-solid fa-credit-card" style="color: var(--primary); margin-right: 6px;"></i> Select Payment Channel
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <label style="border: 2px solid var(--primary); border-radius: var(--radius-sm); padding: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
                        <input type="radio" name="payment_method" value="Vodacom M-Pesa" checked style="accent-color: var(--primary);">
                        <div>
                            <strong style="display: block; font-size: 0.95rem;">Vodacom M-Pesa</strong>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Instant USSD / Push Prompt</small>
                        </div>
                    </label>

                    <label style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #fff;">
                        <input type="radio" name="payment_method" value="Tigo Pesa" style="accent-color: var(--primary);">
                        <div>
                            <strong style="display: block; font-size: 0.95rem;">Tigo Pesa / Mixx</strong>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Instant Mobile Checkout</small>
                        </div>
                    </label>

                    <label style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #fff;">
                        <input type="radio" name="payment_method" value="Airtel Money" style="accent-color: var(--primary);">
                        <div>
                            <strong style="display: block; font-size: 0.95rem;">Airtel Money</strong>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Airtel Push / Till</small>
                        </div>
                    </label>

                    <label style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #fff;">
                        <input type="radio" name="payment_method" value="Bank Card (Visa/Mastercard)" style="accent-color: var(--primary);">
                        <div>
                            <strong style="display: block; font-size: 0.95rem;">Bank Card / Transfer</strong>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">CRDB / NMB / Visa</small>
                        </div>
                    </label>
                </div>

                <div class="form-group" style="margin-bottom: 22px;">
                    <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Payer Mobile Number / Account (For USSD Prompt)</label>
                    <input type="tel" name="payer_number" value="<?php echo htmlspecialchars($customerUser['phone'] ?? '+255 '); ?>" placeholder="e.g. 0754123456" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;">
                </div>

                <?php if ($isLoggedIn && $userStatus === 'active'): ?>
                    <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1.05rem; font-weight: 700;">
                        <i class="fa-solid fa-lock"></i> Authorize & Pay <?php echo number_format($package['price_tzs']); ?> TZS
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1.05rem; opacity: 0.6; cursor: not-allowed;" title="Approval or Login Required">
                        <i class="fa-solid fa-lock"></i> <?php echo !$isLoggedIn ? 'Sign In Required to Pay' : 'Account Awaiting Approval'; ?>
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Right Column: Order Summary & Commission Logic -->
        <aside>
            <div class="sticky-booking-card" style="box-shadow: var(--card-shadow-hover);">
                <!-- Vendor mini-card -->
                <div style="display: flex; gap: 14px; align-items: center; padding-bottom: 18px; border-bottom: 1px solid var(--border); margin-bottom: 18px;">
                    <img src="<?php echo (strpos($vendor['cover_image'], 'http') === 0 ? '' : '') . htmlspecialchars($vendor['cover_image'] ?? 'uploads/logo.png'); ?>" alt="Cover" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                    <div>
                        <span class="logo-badge" style="background: var(--primary); font-size: 0.7rem;"><?php echo htmlspecialchars($vendor['category_name']); ?></span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--dark); margin: 3px 0 2px;">
                            <?php echo htmlspecialchars($vendor['business_name']); ?>
                            <?php if ($vendor['verification_status'] === 'verified'): ?>
                                <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 0.9rem;"></i>
                            <?php endif; ?>
                        </h4>
                        <small style="color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($vendor['region']); ?></small>
                    </div>
                </div>

                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark); margin-bottom: 8px;">
                    <?php echo htmlspecialchars($package['title']); ?>
                </h3>
                <span style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 15px;">
                    <i class="fa-solid fa-users"></i> Capacity: <?php echo htmlspecialchars($package['guest_capacity'] ?? 'Full Venue'); ?>
                </span>

                <!-- Features Preview -->
                <ul style="list-style: none; padding: 0; margin: 0 0 18px; font-size: 0.85rem; color: var(--text-main); border-bottom: 1px solid var(--border); padding-bottom: 15px;">
                    <?php 
                    $feats = explode("\n", trim($package['features']));
                    foreach (array_slice($feats, 0, 4) as $ft):
                        if (empty(trim($ft))) continue;
                    ?>
                        <li style="padding: 3px 0; display: flex; gap: 8px; align-items: baseline;">
                            <i class="fa-solid fa-check" style="color: #10b981;"></i> <?php echo htmlspecialchars(trim($ft)); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Pricing Calculation Breakdown -->
                <div style="font-size: 0.9rem; margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: var(--text-muted);">Package Price:</span>
                        <strong><?php echo number_format($package['price_tzs']); ?> TZS</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: var(--text-muted);">Platform Escrow Guarantee:</span>
                        <span style="color: #059669; font-weight: 700;">FREE (Included)</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px solid var(--border); font-size: 1.15rem;">
                        <strong>Total Due Now:</strong>
                        <strong style="color: var(--primary);"><?php echo number_format($package['price_tzs']); ?> TZS</strong>
                    </div>
                </div>

                <!-- Transparent Commission & Escrow Security Notice -->
                <div style="background: #f8fafc; border: 1px dashed var(--border); border-radius: var(--radius-sm); padding: 14px; font-size: 0.8rem; color: var(--text-muted); line-height: 1.5;">
                    <strong style="color: var(--dark); display: block; margin-bottom: 4px;">
                        <i class="fa-solid fa-shield-halved" style="color: #059669;"></i> Buyer Protection & Split Escrow
                    </strong>
                    Your funds are held securely. The approved platform fee (<?php echo number_format($vendor['commission_rate'], 1); ?>%) is retained, and the remaining <?php echo 100 - number_format($vendor['commission_rate'], 1); ?>% is disbursed to the vendor upon booking execution.
                </div>
            </div>
        </aside>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
