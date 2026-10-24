<?php
require_once "connection.php";

$venues = [];

$sql = "SELECT * FROM venues WHERE status = 'Available' ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $venues[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Venues | Nuustar Events</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="style.css">


    <style>

        /* ================================
           VENUES PAGE
        ================================= */

        .venues-page {
            background: #ffffff;
        }


        /* ================================
           PAGE HERO
        ================================= */

        .venues-hero {
            min-height: 430px;

            background:
                linear-gradient(
                    rgba(7, 31, 64, 0.78),
                    rgba(7, 31, 64, 0.78)
                ),
                url("images/venue.jpg") center/cover no-repeat;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 120px 20px 80px;

            color: white;
        }

        .venues-hero-content {
            max-width: 850px;
        }

        .venues-hero-content span {
            display: inline-block;

            color: #f4c430;

            font-size: 14px;
            font-weight: 700;

            letter-spacing: 2px;

            text-transform: uppercase;

            margin-bottom: 15px;
        }

        .venues-hero-content h1 {
            font-family: "Playfair Display", serif;

            font-size: 58px;
            line-height: 1.15;

            margin-bottom: 20px;
        }

        .venues-hero-content p {
            max-width: 700px;

            margin: auto;

            font-size: 17px;
            line-height: 1.8;

            color: #f3f4f6;
        }


        /* ================================
           INTRO
        ================================= */

        .venues-intro {
            padding: 80px 20px 40px;

            text-align: center;
        }

        .venues-intro-container {
            max-width: 900px;

            margin: auto;
        }

        .section-label {
            color: #f4c430;

            font-size: 13px;
            font-weight: 800;

            letter-spacing: 2px;

            text-transform: uppercase;

            margin-bottom: 10px;
        }

        .venues-intro h2 {
            font-family: "Playfair Display", serif;

            color: #0b2d5c;

            font-size: 40px;

            margin-bottom: 18px;
        }

        .venues-intro p {
            color: #6b7280;

            font-size: 16px;
            line-height: 1.8;
        }


        /* ================================
           FILTERS
        ================================= */

        .venue-filters {
            padding: 25px 20px 55px;
        }

        .venue-filter-container {
            max-width: 1150px;

            margin: auto;

            display: flex;

            justify-content: center;
            align-items: center;

            flex-wrap: wrap;

            gap: 12px;
        }

        .venue-filter {
            border: 1px solid #e5e7eb;

            background: white;

            color: #374151;

            padding: 11px 22px;

            border-radius: 30px;

            font-family: "Montserrat", sans-serif;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.3s ease;
        }

        .venue-filter:hover,
        .venue-filter.active {
            background: #0b2d5c;

            color: white;

            border-color: #0b2d5c;
        }


        /* ================================
           VENUE GRID
        ================================= */

        .venues-section {
            padding: 20px 20px 90px;
        }

        .venues-container {
            max-width: 1150px;

            margin: auto;
        }

        .venues-grid {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 28px;
        }


        /* ================================
           VENUE CARD
        ================================= */

        .venue-card {
            background: white;

            border-radius: 15px;

            overflow: hidden;

            border: 1px solid #e5e7eb;

            box-shadow:
                0 8px 25px rgba(11, 45, 92, 0.08);

            transition: all 0.3s ease;
        }

        .venue-card:hover {
            transform: translateY(-8px);

            box-shadow:
                0 18px 40px rgba(11, 45, 92, 0.15);
        }

        .venue-image {
            height: 230px;

            position: relative;

            overflow: hidden;
        }

        .venue-image img {
            width: 100%;
            height: 100%;

            object-fit: cover;

            transition: transform 0.5s ease;
        }

        .venue-card:hover .venue-image img {
            transform: scale(1.06);
        }

        .venue-badge {
            position: absolute;

            top: 15px;
            left: 15px;

            background: #f4c430;

            color: #071f40;

            padding: 7px 13px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 800;

            text-transform: uppercase;
        }


        /* ================================
           FAVORITE
        ================================= */

        .venue-favorite {
            position: absolute;

            top: 15px;
            right: 15px;

            width: 38px;
            height: 38px;

            border-radius: 50%;

            border: none;

            background: rgba(255, 255, 255, 0.95);

            color: #0b2d5c;

            cursor: pointer;

            display: flex;

            align-items: center;
            justify-content: center;

            transition: 0.3s ease;
        }

        .venue-favorite:hover {
            background: #f4c430;
        }


        /* ================================
           CARD CONTENT
        ================================= */

        .venue-content {
            padding: 22px;
        }

        .venue-content h3 {
            color: #0b2d5c;

            font-size: 21px;

            margin-bottom: 10px;
        }

        .venue-location {
            display: flex;

            align-items: center;

            gap: 8px;

            color: #6b7280;

            font-size: 13px;

            margin-bottom: 15px;
        }

        .venue-location i {
            color: #f4c430;
        }

        .venue-description {
            color: #6b7280;

            font-size: 14px;

            line-height: 1.7;

            margin-bottom: 18px;
        }


        /* ================================
           VENUE INFORMATION
        ================================= */

        .venue-info {
            display: flex;

            gap: 18px;

            padding: 15px 0;

            border-top: 1px solid #e5e7eb;

            border-bottom: 1px solid #e5e7eb;

            margin-bottom: 18px;
        }

        .venue-info-item {
            display: flex;

            align-items: center;

            gap: 7px;

            color: #374151;

            font-size: 12px;

            font-weight: 600;
        }

        .venue-info-item i {
            color: #0b2d5c;
        }


        /* ================================
           PRICE
        ================================= */

        .venue-bottom {
            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;
        }

        .venue-price small {
            display: block;

            color: #6b7280;

            font-size: 11px;

            margin-bottom: 3px;
        }

        .venue-price strong {
            color: #0b2d5c;

            font-size: 18px;
        }

        .venue-button {
            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 10px 16px;

            background: #0b2d5c;

            color: white;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 700;

            text-decoration: none;

            transition: 0.3s ease;
        }

        .venue-button:hover {
            background: #f4c430;

            color: #071f40;
        }


        /* ================================
           WHY CHOOSE US
        ================================= */

        .why-venues {
            background: #eaf2fb;

            padding: 80px 20px;
        }

        .why-venues-container {
            max-width: 1100px;

            margin: auto;
        }

        .why-venues-heading {
            text-align: center;

            margin-bottom: 50px;
        }

        .why-venues-heading h2 {
            font-family: "Playfair Display", serif;

            font-size: 38px;

            color: #0b2d5c;

            margin-bottom: 12px;
        }

        .why-venues-heading p {
            color: #6b7280;
        }

        .venue-benefits {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 25px;
        }

        .venue-benefit {
            background: white;

            padding: 30px 20px;

            border-radius: 14px;

            text-align: center;

            box-shadow:
                0 8px 25px rgba(11, 45, 92, 0.06);
        }

        .venue-benefit-icon {
            width: 58px;
            height: 58px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #fff7d6;

            color: #0b2d5c;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 22px;
        }

        .venue-benefit h3 {
            color: #0b2d5c;

            font-size: 17px;

            margin-bottom: 10px;
        }

        .venue-benefit p {
            color: #6b7280;

            font-size: 13px;

            line-height: 1.7;
        }


        /* ================================
           VENUE CTA
        ================================= */

        .venue-cta {
            padding: 90px 20px;

            background:
                linear-gradient(
                    135deg,
                    #0b2d5c,
                    #071f40
                );

            color: white;

            text-align: center;
        }

        .venue-cta-container {
            max-width: 750px;

            margin: auto;
        }

        .venue-cta h2 {
            font-family: "Playfair Display", serif;

            font-size: 42px;

            margin-bottom: 18px;
        }

        .venue-cta p {
            color: #e5e7eb;

            line-height: 1.8;

            margin-bottom: 30px;
        }

        .venue-cta-buttons {
            display: flex;

            justify-content: center;

            gap: 15px;

            flex-wrap: wrap;
        }

        .venue-cta-primary {
            background: #f4c430;

            color: #071f40;

            padding: 14px 25px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: 800;

            font-size: 14px;

            transition: 0.3s ease;
        }

        .venue-cta-primary:hover {
            background: white;
        }

        .venue-cta-secondary {
            background: transparent;

            color: white;

            padding: 14px 25px;

            border: 1px solid rgba(255,255,255,0.5);

            border-radius: 8px;

            text-decoration: none;

            font-weight: 700;

            font-size: 14px;

            transition: 0.3s ease;
        }

        .venue-cta-secondary:hover {
            background: white;

            color: #0b2d5c;
        }


        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 1000px) {

            .venues-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .venue-benefits {
                grid-template-columns: repeat(2, 1fr);
            }

            .venues-hero-content h1 {
                font-size: 48px;
            }

        }


        @media (max-width: 700px) {

            .venues-hero {
                min-height: 380px;

                padding-top: 110px;
            }

            .venues-hero-content h1 {
                font-size: 38px;
            }

            .venues-hero-content p {
                font-size: 14px;
            }

            .venues-intro {
                padding-top: 60px;
            }

            .venues-intro h2 {
                font-size: 32px;
            }

            .venues-grid {
                grid-template-columns: 1fr;
            }

            .venue-benefits {
                grid-template-columns: 1fr;
            }

            .venue-cta h2 {
                font-size: 34px;
            }

        }


        @media (max-width: 480px) {

            .venues-hero-content h1 {
                font-size: 32px;
            }

            .venue-filter-container {
                justify-content: flex-start;
            }

            .venue-filter {
                padding: 9px 15px;

                font-size: 12px;
            }

            .venue-image {
                height: 210px;
            }

            .venue-bottom {
                align-items: flex-start;

                flex-direction: column;
            }

            .venue-button {
                width: 100%;

                justify-content: center;
            }

        }

    </style>

</head>


<body class="venues-page">


<!-- ================================
     NAVIGATION
================================= -->

<header class="navbar">

    <div class="container nav-container">

        <!-- Logo -->

        <a href="index.php" class="logo">

            <span>NUU</span>STAR

            <small>EVENTS</small>

        </a>


        <!-- Navigation -->

        <nav class="nav-menu">

            <a href="index.php">
                Home
            </a>

            <a href="index.php#packages">
                Packages
            </a>

            <a href="index.php#services">
                Services
            </a>

            <a href="venues.php" class="active">
                Venues
            </a>

            <a href="index.php#tickets">
                Tickets
            </a>

            <a href="index.php#contact">
                Contact
            </a>

            <a href="index.php#book" class="nav-button">
                Book Your Event
            </a>

        </nav>

    </div>

</header>



<!-- ================================
     HERO
================================= -->

<section class="venues-hero">

    <div class="venues-hero-content">

        <span>
            NUUSTAR EVENTS
        </span>

        <h1>
            Find The Perfect Venue
            For Your Special Event
        </h1>

        <p>
            Discover beautiful and carefully selected venues
            for weddings, birthdays, corporate events,
            meetings and other memorable occasions.
        </p>

    </div>

</section>



<!-- ================================
     INTRO
================================= -->

<section class="venues-intro">

    <div class="venues-intro-container">

        <div class="section-label">
            Our Venues
        </div>

        <h2>
            A Venue For Every Occasion
        </h2>

        <p>
            At Nuustar Events, we help you find the right venue
            based on your event type, number of guests, location
            and budget. Explore our available venue options
            and let us help you create an unforgettable event.
        </p>

    </div>

</section>



<!-- ================================
     FILTERS
================================= -->

<section class="venue-filters">

    <div class="venue-filter-container">

        <button class="venue-filter active">
            All Venues
        </button>

        <button class="venue-filter">
            Weddings
        </button>

        <button class="venue-filter">
            Birthdays
        </button>

        <button class="venue-filter">
            Corporate
        </button>

        <button class="venue-filter">
            Outdoor
        </button>

        <button class="venue-filter">
            Meetings
        </button>

    </div>

</section>



<!-- ================================
     VENUES
================================= -->

<section class="venues-section">

    <div class="venues-container">

        <div class="venues-grid">


            <?php if (!empty($venues)): ?>


                <?php foreach ($venues as $venue): ?>


                    <!-- VENUE FROM DATABASE -->

                    <article class="venue-card">


                        <div class="venue-image">


                            <?php if (!empty($venue['image'])): ?>

                                <img
                                    src="<?php echo htmlspecialchars($venue['image']); ?>"
                                    alt="<?php echo htmlspecialchars($venue['venue_name']); ?>">

                            <?php else: ?>

                                <img
                                    src="images/venue.jpg"
                                    alt="Nuustar Events venue">

                            <?php endif; ?>


                            <span class="venue-badge">

                                <?php echo htmlspecialchars($venue['venue_type']); ?>

                            </span>


                            <button
                                class="venue-favorite"
                                aria-label="Add venue to favorites">

                                <i class="fa-regular fa-heart"></i>

                            </button>


                        </div>



                        <div class="venue-content">


                            <h3>

                                <?php echo htmlspecialchars($venue['venue_name']); ?>

                            </h3>


                            <div class="venue-location">

                                <i class="fa-solid fa-location-dot"></i>

                                <span>

                                    <?php echo htmlspecialchars($venue['location']); ?>

                                </span>

                            </div>


                            <p class="venue-description">

                                <?php

                                echo htmlspecialchars(
                                    $venue['description']
                                );

                                ?>

                            </p>



                            <div class="venue-info">


                                <div class="venue-info-item">

                                    <i class="fa-solid fa-users"></i>

                                    Up to

                                    <?php

                                    echo number_format(
                                        $venue['capacity']
                                    );

                                    ?>

                                </div>


                                <div class="venue-info-item">

                                    <i class="fa-solid fa-building"></i>

                                    <?php

                                    echo htmlspecialchars(
                                        $venue['venue_type']
                                    );

                                    ?>

                                </div>


                            </div>



                            <div class="venue-bottom">


                                <div class="venue-price">

                                    <small>
                                        Starting from
                                    </small>

                                    <strong>

                                        TSh

                                        <?php

                                        echo number_format(
                                            $venue['price']
                                        );

                                        ?>

                                    </strong>

                                </div>

<a
    href="venue_booking.php?id=<?php echo $venue['id']; ?>"
    class="venue-button">

    Book Your Venue

    <i class="fa-solid fa-arrow-right"></i>

</a>

                               


                            </div>


                        </div>


                    </article>


                <?php endforeach; ?>


            <?php else: ?>


                <!-- NO VENUES -->

                <div
                    style="
                        grid-column: 1 / -1;
                        text-align: center;
                        padding: 60px 20px;
                    ">

                    <h3 style="color:#0b2d5c;">

                        No venues are currently available.

                    </h3>

                    <p style="color:#6b7280;">

                        Please check again later or contact
                        Nuustar Events.

                    </p>

                </div>


            <?php endif; ?>


        </div>

    </div>

</section>



<!-- ================================
     WHY CHOOSE NUU STAR
================================= -->

<section class="why-venues">

    <div class="why-venues-container">


        <div class="why-venues-heading">

            <div class="section-label">
                Why Nuustar Events?
            </div>

            <h2>
                We Make Venue Finding Easy
            </h2>

            <p>
                You don't have to spend days searching for the
                right place. We help you find a venue that fits
                your event.
            </p>

        </div>



        <div class="venue-benefits">


            <div class="venue-benefit">

                <div class="venue-benefit-icon">

                    <i class="fa-solid fa-location-dot"></i>

                </div>

                <h3>
                    Great Locations
                </h3>

                <p>
                    Access venues in convenient and attractive
                    locations around Dar es Salaam.
                </p>

            </div>



            <div class="venue-benefit">

                <div class="venue-benefit-icon">

                    <i class="fa-solid fa-wallet"></i>

                </div>

                <h3>
                    Different Budgets
                </h3>

                <p>
                    We help you find venue options that match
                    your event budget.
                </p>

            </div>



            <div class="venue-benefit">

                <div class="venue-benefit-icon">

                    <i class="fa-solid fa-users"></i>

                </div>

                <h3>
                    Any Event Size
                </h3>

                <p>
                    From small private gatherings to large
                    celebrations and corporate events.
                </p>

            </div>



            <div class="venue-benefit">

                <div class="venue-benefit-icon">

                    <i class="fa-solid fa-headset"></i>

                </div>

                <h3>
                    Personal Support
                </h3>

                <p>
                    Our team can guide you through the venue
                    selection and booking process.
                </p>

            </div>


        </div>

    </div>

</section>



<!-- ================================
     CTA
================================= -->

<section class="venue-cta">

    <div class="venue-cta-container">


        <h2>
            Can't Find What You're Looking For?
        </h2>


        <p>
            Tell us about your event, your preferred location,
            number of guests and budget. Our team can help
            you find a suitable venue.
        </p>


        <div class="venue-cta-buttons">


            <a
                href="index.php#book"
                class="venue-cta-primary">

                <i class="fa-solid fa-calendar-check"></i>

                Book Your Event

            </a>


            <a
                href="index.php#contact"
                class="venue-cta-secondary">

                <i class="fa-solid fa-phone"></i>

                Contact Us

            </a>


        </div>


    </div>

</section>



<!-- ================================
     FOOTER
================================= -->

<footer class="footer">

    <div class="container footer-container">


        <!-- About -->

        <div class="footer-column">


            <a
                href="index.php"
                class="logo footer-logo">

                <span>NUU</span>STAR

                <small>EVENTS</small>

            </a>


            <p>
                Creating memorable events with beautiful
                venues, elegant decoration, delicious catering
                and professional event services.
            </p>


            <div class="social-links">


                <a
                    href="#"
                    aria-label="Instagram">

                    <i class="fab fa-instagram"></i>

                </a>


                <a
                    href="#"
                    aria-label="Facebook">

                    <i class="fab fa-facebook-f"></i>

                </a>


                <a
                    href="#"
                    aria-label="TikTok">

                    <i class="fab fa-tiktok"></i>

                </a>


                <a
                    href="#"
                    aria-label="WhatsApp">

                    <i class="fab fa-whatsapp"></i>

                </a>


            </div>

        </div>



        <!-- Quick Links -->

        <div class="footer-column">


            <h3>
                Quick Links
            </h3>


            <ul>


                <li>

                    <a href="index.php">
                        Home
                    </a>

                </li>


                <li>

                    <a href="index.php#packages">
                        Packages
                    </a>

                </li>


                <li>

                    <a href="index.php#services">
                        Services
                    </a>

                </li>


                <li>

                    <a href="venues.php">
                        Venues
                    </a>

                </li>


                <li>

                    <a href="index.php#tickets">
                        Tickets
                    </a>

                </li>


            </ul>

        </div>



        <!-- Services -->

        <div class="footer-column">


            <h3>
                Our Services
            </h3>


            <ul>


                <li>

                    <a href="index.php#services">
                        Venue Finding
                    </a>

                </li>


                <li>

                    <a href="index.php#services">
                        Event Decoration
                    </a>

                </li>


                <li>

                    <a href="index.php#services">
                        Catering
                    </a>

                </li>


                <li>

                    <a href="index.php#services">
                        Photography
                    </a>

                </li>


                <li>

                    <a href="index.php#services">
                        Videography
                    </a>

                </li>


            </ul>

        </div>



        <!-- Contact -->

        <div class="footer-column">


            <h3>
                Contact Us
            </h3>


            <ul class="footer-contact">


                <li>

                    <i class="fa-solid fa-phone"></i>

                    <span>
                        0624 947 668
                    </span>

                </li>


                <li>

                    <i class="fa-solid fa-location-dot"></i>

                    <span>
                        Dar es Salaam, Tanzania
                    </span>

                </li>


                <li>

                    <i class="fa-solid fa-globe"></i>

                    <span>
                        www.nuustar.com
                    </span>

                </li>


            </ul>

        </div>


    </div>



    <div class="footer-bottom">

        <p>

            &copy; 2026 Nuustar Events.
            All Rights Reserved.

        </p>

    </div>

</footer>


</body>

</html>