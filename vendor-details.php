<?php
require_once 'config/db.php';

$vendorId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$prefillDate = isset($_GET['date']) ? trim($_GET['date']) : '';

// Fetch vendor details from database
$stmt = $pdo->prepare("
    SELECT vp.*, c.name AS category_name, u.email, u.name AS contact_person 
    FROM vendor_profiles vp 
    JOIN categories c ON vp.category_id = c.id 
    JOIN users u ON vp.user_id = u.id 
    WHERE vp.id = :id
");
$stmt->execute(['id' => $vendorId]);
$vendor = $stmt->fetch();

if (!$vendor) {
    die("<div style='text-align:center; padding:50px;'><h2>Vendor not found</h2><a href='vendors.php'>Return to Directory</a></div>");
}

// Fetch packages for this vendor
$pkgStmt = $pdo->prepare("SELECT * FROM packages WHERE vendor_id = :id ORDER BY price_tzs ASC");
$pkgStmt->execute(['id' => $vendorId]);
$packages = $pkgStmt->fetchAll();

// Handle Customer Quote Inquiry & Instant WhatsApp Notification
$inquirySuccess = false;
$whatsappNotificationUrl = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventType = trim($_POST['event_type'] ?? 'Wedding');
    $packageName = trim($_POST['package_name'] ?? 'Custom Quote');
    $guestCount = (int)($_POST['guest_count'] ?? 100);
    $customerName = trim($_POST['customer_name'] ?? 'A Client');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!empty($eventDate) && !empty($customerPhone) && !empty($customerName)) {
        // 1. Save inquiry into database
        $inqStmt = $pdo->prepare("
            INSERT INTO inquiries (vendor_id, customer_name, customer_phone, event_type, event_date, guest_count, notes, status)
            VALUES (:vid, :name, :phone, :etype, :edate, :guests, :notes, 'pending')
        ");
        $fullNotes = "Selected Package: " . $packageName . (!empty($notes) ? " | Notes: " . $notes : "");
        $inqStmt->execute([
            'vid' => $vendorId,
            'name' => $customerName,
            'phone' => $customerPhone,
            'etype' => $eventType,
            'edate' => $eventDate,
            'guests' => $guestCount,
            'notes' => $fullNotes
        ]);

        // 2. Generate WhatsApp Notification URL
        $cleanPhone = preg_replace('/[^0-9]/', '', $vendor['whatsapp_number']);
        $waMsg = "Hello " . $vendor['business_name'] . ",\n\nI have submitted a quotation inquiry on Nuustar Events!\n\n"
               . "📋 Celebration Details:\n"
               . "• Event: " . $eventType . "\n"
               . "• Date: " . $eventDate . "\n"
               . "• Selected Package: " . $packageName . "\n"
               . "• Estimated Guests: " . $guestCount . "\n"
               . "• Host Name: " . $customerName . "\n"
               . "• Host Phone: " . $customerPhone . "\n";
        if (!empty($notes)) {
            $waMsg .= "• Special Notes: " . $notes . "\n";
        }
        $waMsg .= "\nPlease check your availability and confirm. Thank you!";

        $whatsappNotificationUrl = "https://wa.me/" . $cleanPhone . "?text=" . rawurlencode($waMsg);
        $inquirySuccess = true;
    } else {
        $errorMsg = "Please fill in your name, phone number, and event date.";
    }
}

$pageTitle = htmlspecialchars($vendor['business_name']) . " - Nuustar Events";
require_once 'includes/header.php';
?>

<!-- Vendor Header / Cover -->
<section class="vendor-profile-header">
    <div class="container">
        <!-- Cover Photo -->
        <div class="vendor-cover-wrap">
            <img src="<?php echo htmlspecialchars($vendor['cover_image']); ?>" alt="<?php echo htmlspecialchars($vendor['business_name']); ?>">
        </div>

        <!-- Meta Bar -->
        <div class="profile-meta-bar">
            <div class="profile-info">
                <h1>
                    <?php echo htmlspecialchars($vendor['business_name']); ?> 
                    <?php if ($vendor['verification_status'] === 'verified'): ?>
                        <span class="badge-verified"><i class="fa-solid fa-circle-check"></i> Verified Provider</span>
                    <?php endif; ?>
                </h1>
                <div style="display: flex; gap: 20px; align-items: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 5px; flex-wrap: wrap;">
                    <span><i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> <?php echo htmlspecialchars($vendor['district'] . ', ' . $vendor['region']); ?></span>
                    <span><i class="fa-solid fa-star" style="color: #b45309;"></i> <strong><?php echo number_format($vendor['rating'], 1); ?></strong> (<?php echo (int)$vendor['reviews_count']; ?> Reviews)</span>
                    <span><i class="fa-solid fa-tags" style="color: var(--primary);"></i> <?php echo htmlspecialchars($vendor['category_name']); ?></span>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-whatsapp" onclick="openWhatsApp('<?php echo htmlspecialchars($vendor['whatsapp_number']); ?>', '<?php echo htmlspecialchars($vendor['business_name']); ?>')">
                    <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Content Layout -->
<div class="container">
    <div class="profile-details-layout">
        <!-- Left Main Area (Tabs & Content) -->
        <main>
            <!-- Navigation Tabs -->
            <div class="tabs-nav">
                <button class="tab-btn active" onclick="showTab('packages')"><i class="fa-solid fa-list-check"></i> Packages & Pricing</button>
                <button class="tab-btn" onclick="showTab('about')"><i class="fa-solid fa-circle-info"></i> About Us</button>
                <button class="tab-btn" onclick="showTab('gallery')"><i class="fa-solid fa-image"></i> Portfolio Gallery</button>
                <button class="tab-btn" onclick="showTab('reviews')"><i class="fa-solid fa-star"></i> Client Reviews</button>
            </div>

            <!-- Tab 1: Packages & Pricing -->
            <section id="tab-packages" class="tab-content">
                <div class="content-box">
                    <h2>Available Service Packages</h2>
                    <p style="color: var(--text-muted); margin-bottom: 25px;">
                        Choose the package that matches your guest count and vision. All packages can be customized for your venue dimensions.
                    </p>

                    <div class="packages-list">
                        <?php if (empty($packages)): ?>
                            <p style="color: var(--text-muted);">No pre-set packages listed yet. Inquire directly for a custom quote.</p>
                        <?php else: ?>
                            <?php foreach ($packages as $pkg): ?>
                            <div class="package-card <?php echo $pkg['is_featured'] ? 'highlight' : ''; ?>">
                                <div class="package-header">
                                    <div>
                                        <?php if ($pkg['is_featured']): ?>
                                            <div style="background: var(--primary); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 5px;">MOST POPULAR</div>
                                        <?php endif; ?>
                                        <h3 class="package-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                                        <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($pkg['guest_capacity']); ?></span>
                                    </div>
                                    <div class="package-price"><?php echo number_format($pkg['price_tzs']); ?> TZS</div>
                                </div>
                                <ul class="package-features">
                                    <?php 
                                    $features = explode("\n", trim($pkg['features']));
                                    foreach ($features as $f): 
                                        if (empty(trim($f))) continue;
                                    ?>
                                        <li><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars(trim($f)); ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                    <span style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-shield-halved" style="color: #059669;"></i> Escrow Protected</span>
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" class="btn btn-outline btn-sm btn-select-pkg" onclick="selectPackage('<?php echo htmlspecialchars($pkg['title'], ENT_QUOTES); ?>', <?php echo (int)$pkg['price_tzs']; ?>, this)">
                                            <i class="fa-solid fa-comment-dots"></i> Quote Inquiry
                                        </button>
                                        <a href="book-service.php?vendor_id=<?php echo $vendor['id']; ?>&package_id=<?php echo $pkg['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-lock"></i> Book & Pay Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Tab 2: About (Hidden by default, shown by JS) -->
            <section id="tab-about" class="tab-content" style="display: none;">
                <div class="content-box">
                    <h2>About Elegance Decor Tanzania</h2>
                    <p style="margin-bottom: 15px; color: var(--text-main); line-height: 1.8;">
                        Elegance Decor is a premier event styling and stage architecture firm based in Dar es Salaam. With over 6 years of expertise designing luxurious weddings, high-level diplomatic galas, and private celebrations across Tanzania, we transform ordinary spaces into unforgettable sensory experiences.
                    </p>
                    <h3 style="font-size: 1.1rem; margin-top: 25px; margin-bottom: 10px;">Regions Covered:</h3>
                    <p style="color: var(--text-muted);">Dar es Salaam (transport included), Bagamoyo, Morogoro, Zanzibar, and upcountry upon request.</p>
                </div>
            </section>

            <!-- Tab 3: Gallery (Hidden by default) -->
            <section id="tab-gallery" class="tab-content" style="display: none;">
                <div class="content-box">
                    <h2>Past Work & Portfolio Gallery</h2>
                    <div class="gallery-grid">
                        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=500&q=80" alt="Work 1"></div>
                        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?auto=format&fit=crop&w=500&q=80" alt="Work 2"></div>
                        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=500&q=80" alt="Work 3"></div>
                        <div class="gallery-item"><img src="https://images.unsplash.com/photo-1520854221256-17451cc331bf?auto=format&fit=crop&w=500&q=80" alt="Work 4"></div>
                    </div>
                </div>
            </section>

            <!-- Tab 4: Reviews (Hidden by default) -->
            <section id="tab-reviews" class="tab-content" style="display: none;">
                <div class="content-box">
                    <h2>Verified Client Reviews (48)</h2>
                    <div style="border-bottom: 1px solid var(--border); padding-bottom: 15px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong>Mariam & Salim (Send-off Celebration)</strong>
                            <span style="color: #b45309;"><i class="fa-solid fa-star"></i> 5.0</span>
                        </div>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">
                            "They transformed our hall into a true fairytale! Every guest commented on how gorgeous the sweetheart table and lighting were. Punctual, professional, and attentive."
                        </p>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong>Dr. Peter K. (Wedding Reception)</strong>
                            <span style="color: #b45309;"><i class="fa-solid fa-star"></i> 5.0</span>
                        </div>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">
                            "World-class execution. They remained on-site until the celebration wrapped up at midnight without any hassle. Highly recommended for couples looking for peace of mind."
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Right Sticky Booking / Inquiry Form -->
        <aside id="bookingBox">
            <div class="sticky-booking-card">
                <div class="booking-header-price" id="bookingDisplayPrice">
                    <span>Starting Price</span>
                    <?php echo number_format($vendor['starting_price']); ?> TZS
                </div>

                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin-bottom: 15px;">
                    Request a Custom Quote
                </h3>

                <?php if ($inquirySuccess): ?>
                    <div style="background-color: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                        <h4 style="margin-bottom: 6px; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Inquiry Recorded!</h4>
                        <p style="font-size: 0.85rem; line-height: 1.5; margin-bottom: 12px;">
                            Your inquiry has been stored. Click below to notify <strong><?php echo htmlspecialchars($vendor['business_name']); ?></strong> directly on WhatsApp!
                        </p>
                        <a href="<?php echo $whatsappNotificationUrl; ?>" target="_blank" class="btn btn-whatsapp btn-sm btn-block">
                            <i class="fa-brands fa-whatsapp"></i> Open WhatsApp Notification Now
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMsg)): ?>
                    <div style="background-color: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 15px; font-size: 0.85rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($errorMsg); ?>
                    </div>
                <?php endif; ?>

                <form action="vendor-details.php?id=<?php echo $vendorId; ?>" method="POST">
                    <input type="hidden" name="submit_inquiry" value="1">

                    <!-- Selected Package Dropdown -->
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label><i class="fa-solid fa-box-archive"></i> Selected Package / Service</label>
                        <select name="package_name" id="selectedPackageSelect" onchange="onPackageSelectChange(this)">
                            <option value="Custom / Let's Discuss" data-price="<?php echo (int)$vendor['starting_price']; ?>">Custom / Let's Discuss</option>
                            <?php foreach ($packages as $pkg): ?>
                                <option value="<?php echo htmlspecialchars($pkg['title']); ?>" data-price="<?php echo (int)$pkg['price_tzs']; ?>">
                                    <?php echo htmlspecialchars($pkg['title']); ?> (<?php echo number_format($pkg['price_tzs']); ?> TZS)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Event Date</label>
                        <input type="date" name="event_date" value="<?php echo htmlspecialchars($prefillDate); ?>" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Event Type</label>
                        <select name="event_type" required>
                            <option value="Wedding (Harusi)">Wedding (Harusi)</option>
                            <option value="Send-off Party">Send-off Party</option>
                            <option value="Kitchen Party">Kitchen Party</option>
                            <option value="Birthday / Anniversary">Birthday / Anniversary</option>
                            <option value="Corporate Conference">Corporate Conference</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Estimated Guest Count</label>
                        <input type="number" name="guest_count" placeholder="e.g. 250" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Your Full Name</label>
                        <input type="text" name="customer_name" placeholder="e.g. Sarah Mwangi" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Your WhatsApp / Phone Number</label>
                        <input type="tel" name="customer_phone" placeholder="+255 7XX XXX XXX" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Notes / Hall Location</label>
                        <textarea name="notes" rows="2" style="width: 100%; padding: 8px 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.85rem;" placeholder="e.g. Mlimani City Hall, Navy & Gold theme..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-paper-plane"></i> Send Inquiry & Notify WhatsApp
                    </button>
                </form>

                <div style="text-align: center; margin: 15px 0; color: var(--text-muted); font-size: 0.85rem;">- OR -</div>

                <button type="button" class="btn btn-whatsapp btn-block" onclick="openWhatsApp('<?php echo htmlspecialchars($vendor['whatsapp_number']); ?>', '<?php echo htmlspecialchars($vendor['business_name']); ?>')">
                    <i class="fa-brands fa-whatsapp"></i> Direct Chat on WhatsApp
                </button>
            </div>
        </aside>
    </div>
</div>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

    document.getElementById('tab-' + tabName).style.display = 'block';
    event.currentTarget.classList.add('active');
}

// Interactive Package Selection
function selectPackage(title, price, btn) {
    const pkgSelect = document.getElementById('selectedPackageSelect');
    if (pkgSelect) {
        for (let i = 0; i < pkgSelect.options.length; i++) {
            if (pkgSelect.options[i].value === title) {
                pkgSelect.selectedIndex = i;
                break;
            }
        }
    }
    
    // Update displayed price
    const priceDisplay = document.getElementById('bookingDisplayPrice');
    if (priceDisplay) {
        priceDisplay.innerHTML = '<span>Selected Tier</span>' + Number(price).toLocaleString() + ' TZS';
    }

    // Highlight selected card visually
    document.querySelectorAll('.package-card').forEach(c => {
        c.style.borderColor = 'var(--border)';
    });
    btn.closest('.package-card').style.borderColor = 'var(--accent)';
    
    // Switch button text temporarily
    btn.innerHTML = '<i class="fa-solid fa-check"></i> Selected!';
    setTimeout(() => {
        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Select this Package';
    }, 2000);

    // Scroll to booking form
    document.getElementById('bookingBox').scrollIntoView({ behavior: 'smooth' });
}

function onPackageSelectChange(selectElem) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    const priceDisplay = document.getElementById('bookingDisplayPrice');
    if (priceDisplay && price) {
        priceDisplay.innerHTML = '<span>Selected Tier</span>' + Number(price).toLocaleString() + ' TZS';
    }
}

<?php if ($inquirySuccess && !empty($whatsappNotificationUrl)): ?>
// Automatically open WhatsApp to notify the vendor immediately
window.open('<?php echo $whatsappNotificationUrl; ?>', '_blank');
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
