
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catering Services | Nuustar Events</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="style.css">

    <style>
        /* ===================================
           CATERING PAGE
        =================================== */

        .catering-page {
            background: var(--gray-light);
            min-height: 100vh;
        }

        /* HERO */
        .catering-hero {
            min-height: 470px;
            padding: 120px 20px 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;

            background:
                linear-gradient(
                    rgba(7, 31, 64, 0.88),
                    rgba(11, 45, 92, 0.88)
                );
        }

        .catering-hero-content {
            max-width: 800px;
            color: var(--white);
        }

        .catering-hero-content .hero-icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: var(--yellow);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .catering-hero-content h1 {
            font-family: "Playfair Display", serif;
            font-size: 50px;
            margin-bottom: 15px;
        }

        .catering-hero-content p {
            font-size: 17px;
            line-height: 1.8;
            color: rgba(255,255,255,0.9);
        }

        /* INTRO */
        .catering-intro {
            max-width: 850px;
            margin: 0 auto;
            padding: 70px 20px 35px;
            text-align: center;
        }

        .catering-intro h2 {
            font-family: "Playfair Display", serif;
            font-size: 34px;
            color: var(--blue);
            margin-bottom: 12px;
        }

        .catering-intro p {
            color: var(--gray);
            line-height: 1.8;
        }

        /* CATERING PACKAGES */
        .catering-container {
            max-width: 1150px;
            margin: 0 auto;
            padding: 20px;
        }

        .catering-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .catering-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
        }

        .catering-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .catering-card-top {
            background: var(--blue);
            color: var(--white);
            padding: 28px 25px;
            text-align: center;
        }

        .catering-card-top.featured {
            background: var(--yellow);
            color: var(--blue);
        }

        .catering-icon {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .catering-card-top.featured .catering-icon {
            background: rgba(11,45,92,0.1);
        }

        .catering-card-top h3 {
            font-family: "Playfair Display", serif;
            font-size: 25px;
            margin-bottom: 7px;
        }

        .catering-card-top p {
            font-size: 13px;
            opacity: 0.88;
        }

        .catering-card-body {
            padding: 25px;
        }

        .catering-card-body h4 {
            color: var(--blue);
            font-size: 15px;
            margin-bottom: 15px;
        }

        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-list li {
            display: flex;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid var(--border);
            color: var(--gray-dark);
            font-size: 14px;
        }

        .menu-list li:last-child {
            border-bottom: none;
        }

        .menu-list i {
            color: var(--yellow-dark);
            margin-top: 3px;
        }

        .catering-price {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .catering-price span:first-child {
            color: var(--gray);
            font-size: 13px;
        }

        .catering-price strong {
            color: var(--blue);
            font-size: 18px;
        }

        .catering-btn {
            display: inline-flex;
            width: 100%;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            padding: 13px 18px;
            border-radius: 8px;
            background: var(--yellow);
            color: var(--blue);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: var(--transition);
        }

        .catering-btn:hover {
            background: var(--yellow-dark);
            transform: translateY(-2px);
        }

        /* MENU CATEGORIES */
        .menu-section {
            max-width: 1150px;
            margin: 65px auto 0;
            padding: 0 20px;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-heading h2 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            font-size: 33px;
            margin-bottom: 8px;
        }

        .section-heading p {
            color: var(--gray);
        }

        .menu-category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .menu-category {
            background: var(--white);
            padding: 25px 18px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .menu-category:hover {
            transform: translateY(-4px);
        }

        .menu-category i {
            font-size: 28px;
            color: var(--yellow-dark);
            margin-bottom: 13px;
        }

        .menu-category h3 {
            color: var(--blue);
            font-size: 17px;
            margin-bottom: 8px;
        }

        .menu-category p {
            color: var(--gray);
            font-size: 13px;
            line-height: 1.6;
        }

        /* ADD ONS */
        .addons-section {
            max-width: 1150px;
            margin: 65px auto 0;
            padding: 0 20px;
        }

        .addons-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 35px;
            box-shadow: var(--shadow-sm);
        }

        .addons-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .addon {
            background: var(--gray-light);
            border-radius: 10px;
            padding: 17px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray-dark);
            font-size: 13px;
            font-weight: 600;
        }

        .addon i {
            color: var(--yellow-dark);
        }

        /* WHY */
        .why-catering {
            max-width: 1150px;
            margin: 65px auto 0;
            padding: 0 20px;
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .why-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 28px;
            box-shadow: var(--shadow-sm);
        }

        .why-card i {
            font-size: 27px;
            color: var(--yellow-dark);
            margin-bottom: 15px;
        }

        .why-card h3 {
            color: var(--blue);
            margin-bottom: 8px;
            font-size: 17px;
        }

        .why-card p {
            color: var(--gray);
            line-height: 1.7;
            font-size: 13px;
        }

        /* CTA */
        .catering-cta {
            max-width: 1150px;
            margin: 70px auto 0;
            padding: 0 20px 70px;
        }

        .cta-box {
            background: var(--blue);
            color: var(--white);
            border-radius: var(--radius);
            padding: 45px 30px;
            text-align: center;
        }

        .cta-box h2 {
            font-family: "Playfair Display", serif;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .cta-box p {
            max-width: 700px;
            margin: 0 auto 22px;
            color: rgba(255,255,255,0.88);
            line-height: 1.7;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--yellow);
            color: var(--blue);
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 8px;
            font-weight: 800;
            transition: var(--transition);
        }

        .cta-btn:hover {
            background: var(--yellow-dark);
            transform: translateY(-2px);
        }

        /* RESPONSIVE */
        @media (max-width: 950px) {
            .catering-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .menu-category-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .addons-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .why-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .catering-hero-content h1 {
                font-size: 35px;
            }

            .catering-grid {
                grid-template-columns: 1fr;
            }

            .menu-category-grid {
                grid-template-columns: 1fr;
            }

            .addons-grid {
                grid-template-columns: 1fr;
            }

            .addons-box {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================== -->
    <nav class="navbar">

        <div class="container nav-container">

            <a href="index.php" class="logo">
                <span>NUU</span>STAR
                <small>EVENTS</small>
            </a>

            <div class="nav-links">

                <a href="index.php">Home</a>

                <a href="index.php#packages">
                    Packages
                </a>

                <a href="index.php#services">
                    Services
                </a>

                <a href="venues.php">
                    Venues
                </a>

                <a href="tickets.php">
                    Tickets
                </a>

                <a href="index.php#contact">
                    Contact
                </a>

                <a href="index.php#packages"
                   class="nav-book-btn">
                    Book Your Event
                </a>

            </div>

        </div>

    </nav>


    <!-- =========================
         MAIN
    ========================== -->

    <main class="catering-page">

        <!-- HERO -->
        <section class="catering-hero">

            <div class="catering-hero-content">

                <div class="hero-icon">
                    <i class="fa-solid fa-utensils"></i>
                </div>

                <h1>Exceptional Catering For Every Occasion</h1>

                <p>
                    From intimate celebrations to large corporate events,
                    Nuustar Events provides delicious food, professional
                    service and beautifully presented catering solutions.
                </p>

            </div>

        </section>


        <!-- INTRO -->
        <section class="catering-intro">

            <h2>Catering Packages</h2>

            <p>
                Choose a catering style that fits your event, guest count
                and budget. Our menus can also be customized according to
                your preferences.
            </p>

        </section>


        <!-- PACKAGES -->
        <section class="catering-container">

            <div class="catering-grid">

                <!-- PACKAGE 1 -->
                <div class="catering-card">

                    <div class="catering-card-top">

                        <div class="catering-icon">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>

                        <h3>Basic Catering</h3>

                        <p>
                            Suitable for small gatherings and meetings
                        </p>

                    </div>

                    <div class="catering-card-body">

                        <h4>Sample Menu</h4>

                        <ul class="menu-list">

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Tea & Coffee
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Soft Drinks
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Snacks
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Light Meals
                            </li>

                        </ul>

                        <div class="catering-price">

                            <span>Starting from</span>

                            <strong>
                                TZS 15,000 / person
                            </strong>

                        </div>

                        <a href="catering-bookingform.php?package=basic"
                           class="catering-btn">

                            Request This Package

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>


                <!-- PACKAGE 2 -->
                <div class="catering-card">

                    <div class="catering-card-top featured">

                        <div class="catering-icon">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>

                        <h3>Standard Catering</h3>

                        <p>
                            Great for birthdays and social events
                        </p>

                    </div>

                    <div class="catering-card-body">

                        <h4>Sample Menu</h4>

                        <ul class="menu-list">

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Welcome Drinks
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Rice & Pilau
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Chicken / Beef
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Vegetables & Salad
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Soft Drinks
                            </li>

                        </ul>

                        <div class="catering-price">

                            <span>Starting from</span>

                            <strong>
                                TZS 25,000 / person
                            </strong>

                        </div>

                        <a href="catering-bookingform.php?package=standard"
                           class="catering-btn">

                            Request This Package

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>


                <!-- PACKAGE 3 -->
                <div class="catering-card">

                    <div class="catering-card-top">

                        <div class="catering-icon">
                            <i class="fa-solid fa-champagne-glasses"></i>
                        </div>

                        <h3>Premium Catering</h3>

                        <p>
                            Elegant menus for weddings and special events
                        </p>

                    </div>

                    <div class="catering-card-body">

                        <h4>Sample Menu</h4>

                        <ul class="menu-list">

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Welcome Drinks
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Multiple Main Courses
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Premium Side Dishes
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Salads & Desserts
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Professional Service
                            </li>

                        </ul>

                        <div class="catering-price">

                            <span>Starting from</span>

                            <strong>
                                TZS 40,000 / person
                            </strong>

                        </div>

                        <a href="catering-bookingform.php?package=premium"
                           class="catering-btn">

                            Request This Package

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>

        </section>


        <!-- MENU CATEGORIES -->
        <section class="menu-section">

            <div class="section-heading">

                <h2>Our Catering Options</h2>

                <p>
                    Build a menu that matches the style of your event.
                </p>

            </div>


            <div class="menu-category-grid">

                <div class="menu-category">

                    <i class="fa-solid fa-utensils"></i>

                    <h3>Buffet</h3>

                    <p>
                        Flexible buffet menus for medium and large events.
                    </p>

                </div>


                <div class="menu-category">

                    <i class="fa-solid fa-plate-wheat"></i>

                    <h3>Plated Meals</h3>

                    <p>
                        Professionally served meals for elegant occasions.
                    </p>

                </div>


                <div class="menu-category">

                    <i class="fa-solid fa-mug-hot"></i>

                    <h3>Tea Break</h3>

                    <p>
                        Tea, coffee, snacks and refreshments for meetings.
                    </p>

                </div>


                <div class="menu-category">

                    <i class="fa-solid fa-cake-candles"></i>

                    <h3>Desserts & Cakes</h3>

                    <p>
                        Cakes, desserts and sweet treats for celebrations.
                    </p>

                </div>

            </div>

        </section>


        <!-- ADD ONS -->
        <section class="addons-section">

            <div class="section-heading">

                <h2>Catering Add-Ons</h2>

                <p>
                    Add extra services to make your catering experience complete.
                </p>

            </div>


            <div class="addons-box">

                <div class="addons-grid">

                    <div class="addon">
                        <i class="fa-solid fa-cake-candles"></i>
                        Event Cake
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-glass-water"></i>
                        Drinks Station
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-ice-cream"></i>
                        Dessert Station
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-mug-hot"></i>
                        Tea & Coffee Station
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-person-walking"></i>
                        Waiter Service
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-table"></i>
                        Table Setup
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-truck"></i>
                        Food Delivery
                    </div>

                    <div class="addon">
                        <i class="fa-solid fa-box"></i>
                        Takeaway Packaging
                    </div>

                </div>

            </div>

        </section>


        <!-- WHY CHOOSE US -->
        <section class="why-catering">

            <div class="section-heading">

                <h2>Why Choose Nuustar Catering?</h2>

                <p>
                    We focus on quality, presentation and a smooth guest experience.
                </p>

            </div>


            <div class="why-grid">

                <div class="why-card">

                    <i class="fa-solid fa-bowl-rice"></i>

                    <h3>Quality Food</h3>

                    <p>
                        Carefully planned menus designed for different
                        event styles and guest preferences.
                    </p>

                </div>


                <div class="why-card">

                    <i class="fa-solid fa-star"></i>

                    <h3>Beautiful Presentation</h3>

                    <p>
                        We pay attention to how food and serving areas
                        look as well as how they taste.
                    </p>

                </div>


                <div class="why-card">

                    <i class="fa-solid fa-users"></i>

                    <h3>Professional Service</h3>

                    <p>
                        Our catering service can include setup, serving
                        and guest support depending on your package.
                    </p>

                </div>

            </div>

        </section>


        <!-- CTA -->
        <section class="catering-cta">

            <div class="cta-box">

                <h2>Planning An Event?</h2>

                <p>
                    Tell us your event type, number of guests, preferred menu
                    and budget. We can prepare a catering proposal that fits
                    your occasion.
                </p>

                <a href="catering-bookingform.php"
                   class="cta-btn">

                    Request Catering

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>

        </section>

    </main>


    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="footer">

        <div class="container footer-grid">

            <div class="footer-brand">

                <div class="logo">
                    <span>NUU</span>STAR
                    <small>EVENTS</small>
                </div>

                <p>
                    Creating beautiful, memorable and professionally
                    organized events for every special occasion.
                </p>

                <div class="social-links">

                    <a href="#">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>

                </div>

            </div>


            <div class="footer-column">

                <h4>Quick Links</h4>

                <a href="index.php">Home</a>

                <a href="index.php#packages">
                    Packages
                </a>

                <a href="index.php#services">
                    Services
                </a>

                <a href="venues.php">
                    Venues
                </a>

                <a href="tickets.php">
                    Tickets
                </a>

            </div>


            <div class="footer-column">

                <h4>Our Services</h4>

                <a href="venues.php">
                    Venue Selection
                </a>

                <a href="index.php#services">
                    Decoration
                </a>

                <a href="catering.php">
                    Catering
                </a>

                <a href="index.php#services">
                    Photography
                </a>

                <a href="index.php#services">
                    Videography
                </a>

            </div>


            <div class="footer-column">

                <h4>Contact Us</h4>

                <p>
                    <i class="fa-solid fa-phone"></i>
                    0624 947 668
                </p>

                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    Dar es Salaam, Tanzania
                </p>

                <p>
                    <i class="fa-solid fa-globe"></i>
                    www.nuustar.com
                </p>

            </div>

        </div>


        <div class="footer-bottom">

            <div class="container">

                <p>
                    &copy; 2026 Nuustar Events. All Rights Reserved.
                </p>

            </div>

        </div>

    </footer>

</body>
</html>