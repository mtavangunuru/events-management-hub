<?php
require_once 'config/db.php';
$pageTitle = "Browse Vendors - Nuustar Events";

// Get filter query params
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$selectedRegion = isset($_GET['region']) ? trim($_GET['region']) : (isset($_GET['reg']) ? (is_array($_GET['reg']) ? reset($_GET['reg']) : trim($_GET['reg'])) : '');
$selectedDate = isset($_GET['event_date']) ? trim($_GET['event_date']) : '';

// 1. Fetch categories for sidebar filter
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$allCategories = $catStmt->fetchAll();

// 2. Build Dynamic Vendor Query
$sql = "
    SELECT vp.*, c.name AS category_name, c.slug AS category_slug 
    FROM vendor_profiles vp 
    JOIN categories c ON vp.category_id = c.id 
    WHERE vp.verification_status = 'verified'
";
$params = [];

if (!empty($selectedCategory)) {
    $sql .= " AND c.slug = :category";
    $params['category'] = $selectedCategory;
}

if (!empty($selectedRegion)) {
    $sql .= " AND (vp.region LIKE :reg1 OR vp.district LIKE :reg2)";
    $params['reg1'] = '%' . $selectedRegion . '%';
    $params['reg2'] = '%' . $selectedRegion . '%';
}

$sql .= " ORDER BY vp.rating DESC";

$vendorStmt = $pdo->prepare($sql);
$vendorStmt->execute($params);
$vendorsList = $vendorStmt->fetchAll();

require_once 'includes/header.php';
?>

<div style="background-color: #eef2ff; padding: 30px 0; border-bottom: 1px solid var(--border);">
    <div class="container">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark); margin-bottom: 5px;">
            Discover Event Service Providers in Tanzania
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Compare pricing packages, inspect verified past work, and contact vendors directly.
        </p>
    </div>
</div>

<div class="container">
    <div class="directory-layout">
        <!-- Filter Sidebar -->
        <aside class="filter-sidebar">
            <div class="filter-title">
                <span><i class="fa-solid fa-sliders"></i> Filter Results</span>
                <a href="vendors.php" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">Reset All</a>
            </div>

            <form action="vendors.php" method="GET">
                <!-- Category Filter -->
                <div class="filter-group">
                    <h4>Category</h4>
                    <?php foreach ($allCategories as $c): ?>
                    <label class="filter-checkbox">
                        <input type="radio" name="category" value="<?php echo htmlspecialchars($c['slug']); ?>" <?php if($selectedCategory == $c['slug']) echo 'checked'; ?> onchange="this.form.submit()"> 
                        <?php echo htmlspecialchars($c['name']); ?>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Region Filter -->
                <div class="filter-group">
                    <h4>Region & Location</h4>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="" <?php if(empty($selectedRegion)) echo 'checked'; ?> onchange="this.form.submit()"> All Locations
                    </label>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="Kinondoni" <?php if($selectedRegion == 'Kinondoni') echo 'checked'; ?> onchange="this.form.submit()"> Dar - Kinondoni
                    </label>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="Ilala" <?php if($selectedRegion == 'Ilala') echo 'checked'; ?> onchange="this.form.submit()"> Dar - Ilala
                    </label>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="Masaki" <?php if($selectedRegion == 'Masaki') echo 'checked'; ?> onchange="this.form.submit()"> Dar - Masaki / Oysterbay
                    </label>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="Arusha" <?php if($selectedRegion == 'Arusha') echo 'checked'; ?> onchange="this.form.submit()"> Arusha City
                    </label>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="Dodoma" <?php if($selectedRegion == 'Dodoma') echo 'checked'; ?> onchange="this.form.submit()"> Dodoma City
                    </label>
                    <label class="filter-checkbox">
                        <input type="radio" name="region" value="Zanzibar" <?php if($selectedRegion == 'Zanzibar') echo 'checked'; ?> onchange="this.form.submit()"> Zanzibar Island
                    </label>
                </div>

                <!-- Event Date Filter -->
                <div class="filter-group">
                    <h4>Event Date</h4>
                    <input type="date" name="event_date" value="<?php echo htmlspecialchars($selectedDate); ?>" style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.9rem;" onchange="this.form.submit()">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block btn-sm" style="margin-top: 15px;">
                    Apply Filters
                </button>
            </form>
        </aside>

        <!-- Main Catalog Results -->
        <main>
            <div class="catalog-header">
                <h2>Showing <?php echo count($vendorsList); ?> Available Vendors</h2>
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); margin-right: 6px;">Filter status:</label>
                    <span class="badge-verified"><i class="fa-solid fa-circle-check"></i> All Verified</span>
                </div>
            </div>

            <?php if (empty($vendorsList)): ?>
                <div class="content-box" style="text-align: center; padding: 50px;">
                    <i class="fa-solid fa-store-slash" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 15px;"></i>
                    <h3>No vendors found matching your filter</h3>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Try clearing some filters to see all available service providers.</p>
                    <a href="vendors.php" class="btn btn-primary btn-sm">Clear All Filters</a>
                </div>
            <?php else: ?>
                <div class="vendors-grid">
                    <?php foreach ($vendorsList as $v): ?>
                    <div class="vendor-card">
                        <div class="vendor-card-image">
                            <img src="<?php echo htmlspecialchars($v['cover_image']); ?>" alt="<?php echo htmlspecialchars($v['business_name']); ?>">
                            <span class="vendor-category-badge"><?php echo htmlspecialchars($v['category_name']); ?></span>
                        </div>
                        <div class="vendor-card-body">
                            <div class="vendor-card-header">
                                <h3 class="vendor-card-title"><?php echo htmlspecialchars($v['business_name']); ?></h3>
                                <?php if ($v['verification_status'] === 'verified'): ?>
                                    <span class="badge-verified"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                <?php endif; ?>
                            </div>
                            <div class="vendor-location">
                                <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($v['district'] . ', ' . $v['region']); ?>
                            </div>
                            <div class="vendor-rating">
                                <i class="fa-solid fa-star"></i> <?php echo number_format($v['rating'], 1); ?> <span>(<?php echo (int)$v['reviews_count']; ?> Reviews)</span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">
                                <?php echo htmlspecialchars($v['bio']); ?>
                            </p>
                            <div class="vendor-card-footer">
                                <div class="price-tag">
                                    <span>Starts from</span>
                                    <strong><?php echo number_format($v['starting_price']); ?> TZS</strong>
                                </div>
                                <div class="vendor-actions">
                                    <button type="button" class="btn btn-whatsapp btn-sm" onclick="openAdminWhatsApp('<?php echo htmlspecialchars($v['business_name'], ENT_QUOTES); ?>')">
                                        <i class="fa-brands fa-whatsapp"></i> Chat Admin
                                    </button>
                                    <a href="vendor-details.php?id=<?php echo $v['id']; ?><?php if(!empty($selectedDate)) echo '&date=' . urlencode($selectedDate); ?>" class="btn btn-outline btn-sm">Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
