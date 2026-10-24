<?php
require_once 'config/db.php';
$pageTitle = "Post an Event Request - Nuustar Events";

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $eventType = trim($_POST['event_type'] ?? '');
    $servicesNeeded = isset($_POST['services']) ? implode(', ', $_POST['services']) : 'All Services';
    $eventDate = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $guestCount = (int)($_POST['guest_count'] ?? 100);
    $budget = trim($_POST['budget'] ?? '0 TZS');
    $details = trim($_POST['details'] ?? '');

    if (!empty($customerName) && !empty($phone) && !empty($eventDate)) {
        $insertStmt = $pdo->prepare("
            INSERT INTO event_requests (customer_name, phone, event_type, services_needed, event_date, location, guest_count, budget_tzs, details, status)
            VALUES (:name, :phone, :event_type, :services, :date, :loc, :guests, :budget, :details, 'open')
        ");
        $insertStmt->execute([
            'name' => $customerName,
            'phone' => $phone,
            'event_type' => $eventType,
            'services' => $servicesNeeded,
            'date' => $eventDate,
            'loc' => $location,
            'guests' => $guestCount,
            'budget' => $budget,
            'details' => $details
        ]);
        $successMsg = "Your event request has been successfully published! Matching verified vendors can now submit price quotes to your WhatsApp/Phone ($phone).";
    } else {
        $errorMsg = "Please fill in all required fields (Name, Phone number, and Event Date).";
    }
}

require_once 'includes/header.php';
?>

<div style="background-color: #eef2ff; padding: 40px 0; border-bottom: 1px solid var(--border); text-align: center;">
    <div class="container" style="max-width: 800px;">
        <span class="hero-tagline" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">Save Time & Money</span>
        <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--dark); margin: 10px 0;">
            Post Your Event Requirements & Receive Quotes
        </h1>
        <p style="color: var(--text-muted); font-size: 1.05rem;">
            Instead of calling 20 different vendors, fill out this simple request and verified vendors available on your event date will send their best price offers directly to you.
        </p>
    </div>
</div>

<div class="container" style="max-width: 800px; padding: 50px 20px 80px;">
    <div class="content-box" style="padding: 40px;">

        <?php if (!empty($successMsg)): ?>
            <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 25px; font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <form action="post-request.php" method="POST">
            
            <!-- Step 1: Event Type -->
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="font-size: 0.85rem;"><i class="fa-solid fa-cake-candles"></i> 1. What type of event are you hosting?</label>
                <select name="event_type" required>
                    <option value="">Select Event Type...</option>
                    <option value="Wedding (Harusi)">Wedding (Harusi)</option>
                    <option value="Send-off Celebration">Send-off Celebration</option>
                    <option value="Kitchen Party / Bridal Shower">Kitchen Party / Bridal Shower</option>
                    <option value="Communion / Baptism">Communion / Baptism / Kipaimara</option>
                    <option value="Birthday / Anniversary">Birthday / Anniversary</option>
                    <option value="Corporate Conference">Corporate Conference / Gala Dinner</option>
                </select>
            </div>

            <!-- Step 2: Services Needed (Checkboxes) -->
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="font-size: 0.85rem;"><i class="fa-solid fa-layer-group"></i> 2. Which services do you need? (Select all that apply)</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 8px;">
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="venues"> Event Venues</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="decor" checked> Decor & Tents</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="photo" checked> Photo & Video</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="mc"> Master of Ceremony (MC)</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="catering"> Food & Beverage Catering</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="cake"> Wedding Cake</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="music"> DJs & Sound System</label>
                    <label class="filter-checkbox"><input type="checkbox" name="services[]" value="transport"> Bridal Cars & Transport</label>
                </div>
            </div>

            <!-- Step 3: Date & Location -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label style="font-size: 0.85rem;"><i class="fa-regular fa-calendar"></i> Event Date</label>
                    <input type="date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label style="font-size: 0.85rem;"><i class="fa-solid fa-location-dot"></i> Region & District / Venue</label>
                    <input type="text" name="location" placeholder="e.g. Kinondoni, Dar es Salaam" required>
                </div>
            </div>

            <!-- Step 4: Guests & Budget -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group">
                    <label style="font-size: 0.85rem;"><i class="fa-solid fa-users"></i> Estimated Number of Guests</label>
                    <input type="number" name="guest_count" placeholder="e.g. 250" required>
                </div>
                <div class="form-group">
                    <label style="font-size: 0.85rem;"><i class="fa-solid fa-money-bill-wave"></i> Total Estimated Budget (TZS)</label>
                    <input type="text" name="budget" placeholder="e.g. 3,500,000 TZS" required>
                </div>
            </div>

            <!-- Step 5: Extra Details -->
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="font-size: 0.85rem;"><i class="fa-solid fa-comment-dots"></i> Additional Requirements & Color Theme</label>
                <textarea rows="4" name="details" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; outline: none; background: var(--bg-light);" placeholder="Describe your preferred theme, colors, stage layout, special dietary needs, or hall specifications..."></textarea>
            </div>

            <!-- Step 6: Contact Info -->
            <div style="background-color: #f8fafc; border: 1px dashed var(--border); padding: 20px; border-radius: var(--radius-sm); margin-bottom: 30px;">
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--dark); margin-bottom: 15px;">Your Contact Details:</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label style="font-size: 0.8rem;">Your Full Name</label>
                        <input type="text" name="customer_name" placeholder="e.g. Sarah Mwangi" required>
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.8rem;">Phone / WhatsApp Number</label>
                        <input type="tel" name="phone" placeholder="+255 7XX XXX XXX" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block" style="padding: 15px; font-size: 1.1rem;">
                <i class="fa-solid fa-paper-plane"></i> Publish Event Request (Get Proposals)
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
