<?php
require_once 'config/db.php';
$pageTitle = "Home - Central Marketplace for Event Services in Tanzania";

// Fetch all categories from database
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll();

// Fetch verified featured vendors
$stmt = $pdo->query("
    SELECT vp.*, c.name AS category_name, c.slug AS category_slug 
    FROM vendor_profiles vp 
    JOIN categories c ON vp.category_id = c.id 
    WHERE vp.verification_status = 'verified' 
    ORDER BY vp.rating DESC 
    LIMIT 6
");
$featuredVendors = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- High-Priority Inline Styles for Hero Moving Pictures (Guarantees Instant Load without Cache Delays) -->
<style>
.hero-section {
    position: relative !important;
    background: #0a1128 !important;
    color: #ffffff !important;
    padding: 85px 0 120px !important;
    text-align: center !important;
    overflow: visible !important;
}

/* Background Moving Slides Container */
.hero-bg-slider {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 1 !important;
    overflow: hidden !important;
    pointer-events: none !important;
}

.hero-bg-slide {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transition: opacity 1.8s ease-in-out, visibility 1.8s ease-in-out !important;
    overflow: hidden !important;
}

.hero-bg-slide.active {
    opacity: 1 !important;
    visibility: visible !important;
}

.hero-bg-slide img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center center !important;
    display: block !important;
    transform: scale(1.0) !important;
    transition: transform 7.5s cubic-bezier(0.25, 1, 0.5, 1) !important;
    will-change: transform !important;
}

.hero-bg-slide.active img {
    transform: scale(1.12) !important;
}

/* Vivid Event Overlay: Balances Photo Clarity with Text Readability */
.hero-bg-overlay {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: linear-gradient(
        135deg,
        rgba(10, 17, 40, 0.62) 0%,
        rgba(26, 20, 64, 0.52) 50%,
        rgba(10, 17, 40, 0.65) 100%
    ) !important;
    z-index: 2 !important;
    pointer-events: none !important;
}

.hero-content {
    position: relative !important;
    z-index: 5 !important;
}

/* Slide Indicator Dots */
.hero-slide-indicators {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-top: 22px !important;
    background: rgba(0, 0, 0, 0.4) !important;
    padding: 6px 14px !important;
    border-radius: 30px !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
}

.hero-indicator-dot {
    width: 9px !important;
    height: 9px !important;
    border-radius: 50% !important;
    background: rgba(255, 255, 255, 0.45) !important;
    border: none !important;
    padding: 0 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
}

.hero-indicator-dot.active {
    width: 24px !important;
    border-radius: 20px !important;
    background: var(--accent, #f59e0b) !important;
}
</style>

<!-- Hero Section with Moving Pictures Background -->
<section class="hero-section">
    <!-- Animated Moving Background Pictures Slider -->
    <div class="hero-bg-slider" id="heroBgSlider">
        <!-- Slide 1: Luxury Wedding Stage & Hall Setup -->
        <div class="hero-bg-slide active">
            <img src="images/wedding1.jpeg" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1920&q=80';" alt="Luxury Wedding Stage & Hall">
        </div>
        <!-- Slide 2: Grand Gala Ballroom & Dynamic Ambient Lighting -->
        <div class="hero-bg-slide">
            <img src="images/venue 1.webp" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=1920&q=80';" alt="Grand Ballroom Lighting">
        </div>
        <!-- Slide 3: Floral Styling, Luxury Draping & Table Decor -->
        <div class="hero-bg-slide">
            <img src="images/wedding2.jpeg" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1920&q=80';" alt="Floral Styling & Decor">
        </div>
        <!-- Slide 4: Gourmet Event Catering & Banquet Dining -->
        <div class="hero-bg-slide">
            <img src="images/banner.jpeg" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555244162-803834f70033?auto=format&fit=crop&w=1920&q=80';" alt="Gourmet Catering & Dining">
        </div>
        <!-- Slide 5: Cinema 4K Cinematography & Modern Celebration -->
        <div class="hero-bg-slide">
            <img src="images/event1.webp" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=1920&q=80';" alt="Live Event Celebration">
        </div>
    </div>

    <!-- Dark Luxury Contrast Overlay -->
    <div class="hero-bg-overlay"></div>

    <div class="container hero-content">
        <span class="hero-tagline"><i class="fa-solid fa-crown" style="color: var(--accent); margin-right: 5px;"></i> Your Moments, Our Focus</span>
        <h1 class="hero-title">Plan Your Dream Event with <span>Top Verified Vendors</span></h1>
        <p class="hero-subtitle">From Weddings, Send-offs, and Kitchen Parties to Corporate Conferences—discover, compare, and book verified venues, decorators, caterers, and photographers across Tanzania.</p>
        
        <!-- Smart Search Box -->
        <div class="search-box-card">
            <form action="vendors.php" method="GET" class="search-form">
                <!-- Category Select (Dynamic from DB) -->
                <div class="form-group">
                    <label><i class="fa-solid fa-layer-group"></i> Service Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['slug']); ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Region Select (Clean Tanzanian Cities & Districts) -->
                <div class="form-group">
                    <label><i class="fa-solid fa-location-dot"></i> Region / Location</label>
                    <select name="region">
                        <option value="">All Regions</option>
                        <option value="Kinondoni">Dar es Salaam - Kinondoni</option>
                        <option value="Ilala">Dar es Salaam - Ilala</option>
                        <option value="Ubungo">Dar es Salaam - Ubungo</option>
                        <option value="Masaki">Dar es Salaam - Masaki / Oysterbay</option>
                        <option value="Dar es Salaam">Dar es Salaam (All)</option>
                        <option value="Arusha">Arusha City</option>
                        <option value="Dodoma">Dodoma City</option>
                        <option value="Mwanza">Mwanza City</option>
                        <option value="Zanzibar">Zanzibar Island</option>
                    </select>
                </div>

                <!-- Event Date -->
                <div class="form-group">
                    <label><i class="fa-regular fa-calendar"></i> Event Date</label>
                    <input type="date" name="event_date">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
            </form>
        </div>

        <!-- Slide Indicator Dots -->
        <div class="hero-slide-indicators" aria-label="Moving Pictures Controls">
            <button type="button" class="hero-indicator-dot active" onclick="jumpToHeroSlide(0)" title="Slide 1"></button>
            <button type="button" class="hero-indicator-dot" onclick="jumpToHeroSlide(1)" title="Slide 2"></button>
            <button type="button" class="hero-indicator-dot" onclick="jumpToHeroSlide(2)" title="Slide 3"></button>
            <button type="button" class="hero-indicator-dot" onclick="jumpToHeroSlide(3)" title="Slide 4"></button>
            <button type="button" class="hero-indicator-dot" onclick="jumpToHeroSlide(4)" title="Slide 5"></button>
        </div>
    </div>
</section>

<!-- Popular Categories Grid -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Explore Services by Category</h2>
            <p>Browse experienced professionals specialized in every aspect of your celebration</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="vendors.php?category=<?php echo urlencode($cat['slug']); ?>" class="category-card">
                <div class="category-icon"><i class="fa-solid <?php echo htmlspecialchars($cat['icon']); ?>"></i></div>
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                <span><?php echo (int)$cat['vendor_count']; ?>+ Providers</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured & Verified Vendors -->
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <h2>Featured & Verified Vendors</h2>
            <p>Vetted businesses trusted for exceptional reliability and service quality</p>
        </div>

        <div class="vendors-grid">
            <?php foreach ($featuredVendors as $v): ?>
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
                            <span>Starting at</span>
                            <strong><?php echo number_format($v['starting_price']); ?> TZS</strong>
                        </div>
                        <div class="vendor-actions">
                            <button type="button" class="btn btn-whatsapp btn-sm" onclick="openAdminWhatsApp('<?php echo htmlspecialchars($v['business_name'], ENT_QUOTES); ?>')">
                                <i class="fa-brands fa-whatsapp"></i> Chat Admin
                            </button>
                            <a href="vendor-details.php?id=<?php echo $v['id']; ?>" class="btn btn-outline btn-sm">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="vendors.php" class="btn btn-primary" style="padding: 12px 30px;">
                Browse All Vendors <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section">
    <div class="container">
        <div class="section-header">
            <h2>How Nuustar Events Works</h2>
            <p>Three effortless steps to organize an unforgettable event without the usual stress</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Search & Compare</h3>
                <p>Filter by category, city, and budget. View transparent starting prices in TZS and authentic portfolio photos.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Request Quotes</h3>
                <p>Inquire directly on the platform or chat instantly with vendors via verified WhatsApp links with pre-filled event details.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Book with Confidence</h3>
                <p>Collaborate with verified providers and enjoy peace of mind knowing your celebration is handled by verified pros.</p>
            </div>
        </div>
    </div>
</section>

<!-- Vendor Call to Action Banner -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <div class="cta-content">
                <h2>Are you an Event Service Provider in Tanzania?</h2>
                <p>
                    Grow your bookings every week. List your business on Nuustar Events, showcase your portfolio, and receive inquiries for weddings, send-offs, and corporate gigs nationwide.
                </p>
            </div>
            <div>
                <a href="register.php?type=vendor" class="btn btn-accent" style="padding: 14px 28px; font-size: 1.05rem;">
                    <i class="fa-solid fa-store"></i> Register as a Vendor Today
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Moving Background Pictures Script -->
<script>
(function() {
    const slides = document.querySelectorAll('.hero-bg-slide');
    const dots = document.querySelectorAll('.hero-indicator-dot');
    let currentSlide = 0;
    let slideTimer = null;

    function activateSlide(index) {
        if (!slides.length) return;
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (dots[i]) dots[i].classList.remove('active');
        });
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    function advanceSlide() {
        activateSlide(currentSlide + 1);
    }

    function startAutoSlide() {
        if (slideTimer) clearInterval(slideTimer);
        slideTimer = setInterval(advanceSlide, 5000);
    }

    window.jumpToHeroSlide = function(idx) {
        activateSlide(idx);
        startAutoSlide();
    };

    startAutoSlide();
})();
</script>

<?php require_once 'includes/footer.php'; ?>