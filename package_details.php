
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Package Details | Nuustar Events</title>

    <link rel="stylesheet" href="style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        /* ================================
           PACKAGE DETAILS PAGE
        ================================= */

        .package-details-page {
            padding: 80px 0;
            background: var(--gray-light);
        }

        .package-container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
        }

        .package-header {
            text-align: center;
            margin-bottom: 45px;
        }

        .package-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: var(--blue);
            margin-bottom: 12px;
        }

        .package-header p {
            color: var(--gray);
            max-width: 650px;
            margin: auto;
            line-height: 1.7;
        }

        .package-main {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 45px;
        }

        .package-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .package-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: var(--blue);
            margin-bottom: 10px;
        }

        .package-price {
            font-size: 25px;
            font-weight: 800;
            color: var(--yellow-dark);
        }

        .package-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .package-section h3 {
            color: var(--blue);
            font-size: 20px;
            margin-bottom: 20px;
        }

        .package-section ul {
            list-style: none;
            padding: 0;
        }

        .package-section ul li {
            margin-bottom: 14px;
            color: var(--gray-dark);
            line-height: 1.6;
        }

        .package-section ul li i {
            color: var(--yellow-dark);
            margin-right: 10px;
        }

        .package-info-box {
            background: var(--blue-light);
            border-left: 5px solid var(--yellow);
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .package-info-box strong {
            color: var(--blue);
        }

        .package-actions {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
        }

        .package-actions p {
            color: var(--gray);
            margin-bottom: 20px;
        }

        .btn-back {
            display: inline-block;
            margin-left: 12px;
            padding: 13px 25px;
            border: 2px solid var(--blue);
            border-radius: 8px;
            color: var(--blue);
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: var(--blue);
            color: var(--white);
        }

        @media (max-width: 768px) {

            .package-details-page {
                padding: 50px 0;
            }

            .package-main {
                padding: 25px;
            }

            .package-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .package-header h1 {
                font-size: 32px;
            }

            .package-title h2 {
                font-size: 30px;
            }

            .btn-back {
                margin-left: 0;
                margin-top: 12px;
            }
        }
    </style>
</head>

<body>

<!-- ================================
     NAVBAR
================================= -->

<header class="navbar">

    <div class="logo">
        <span>NUU</span>STAR
        <small>EVENTS</small>
    </div>

    <nav>
        <a href="index.php">Home</a>

        <a href="index.php#packages">Packages</a>

        <a href="index.php#services">Services</a>

        <a href="venues.php">Venues</a>

        <a href="tickets.php#tickets">Tickets</a>

        <a href="index.php#contact">Contact</a>

        <a href="venue-booking.php" class="nav-btn">
            Book Your Event
    </nav>

</header>


<!-- ================================
     PACKAGE DETAILS
================================= -->

<section class="package-details-page">

    <div class="package-container">

        <div class="package-header">
            <h1>Package Details</h1>

            <p>
                Choose a package that matches your event needs.
                You can customize your package before submitting your booking request.
            </p>
        </div>


        <div class="package-main">

            <!-- Package title will be changed by JavaScript -->

            <div class="package-title">

                <h2 id="packageName">
                    Silver Package
                </h2>

                <div class="package-price" id="packagePrice">
                    Starting from TZS 2,500,000
                </div>
                <div class="package-title">

                <h2 id="packageName">
                    Gold Package
                </h2>

                <div class="package-price" id="packagePrice">
                    Starting from TZS 4,000,000
                </div>
                <div class="package-title">

                <h2 id="packageName">
                    Premium Package
                </h2>

                <div class="package-price" id="packagePrice">
                    Starting from TZS 7,000,000
                </div>
                <div class="package-title">

                <h2 id="packageName">
                    Luxury Package
                </h2>

                <div class="package-price" id="packagePrice">
                    Starting from TZS 10,000,000
                </div>



            </div>


            <div class="package-grid">

                <!-- WHAT IS INCLUDED -->

                <div class="package-section">

                    <h3>
                        <i class="fa-solid fa-circle-check"></i>
                        What's Included
                    </h3>

                    <ul id="packageFeatures">

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Professional event coordination
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Elegant event decoration
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Professional photography
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Professional videography
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Catering services
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Event hosting support
                        </li>

                    </ul>

                </div>


                <!-- SUITABLE FOR -->

                <div class="package-section">

                    <h3>
                        <i class="fa-solid fa-calendar-check"></i>
                        Suitable For
                    </h3>

                    <ul id="packageEvents">

                        <li>
                            <i class="fa-solid fa-star"></i>
                            Weddings
                        </li>

                        <li>
                            <i class="fa-solid fa-star"></i>
                            Birthday celebrations
                        </li>

                        <li>
                            <i class="fa-solid fa-star"></i>
                            Anniversaries
                        </li>

                        <li>
                            <i class="fa-solid fa-star"></i>
                            Corporate events
                        </li>

                        <li>
                            <i class="fa-solid fa-star"></i>
                            Private celebrations
                        </li>

                    </ul>

                </div>

            </div>


            <!-- PACKAGE INFORMATION -->

            <div class="package-info-box">

                <strong>Important:</strong>

                Package prices are starting prices and may change depending
                on the number of guests, venue, location, selected services,
                decoration requirements and other event preferences.

            </div>


            <!-- ACTIONS -->

            <div class="package-actions">

                <p>
                    Ready to plan your event?
                    Customize this package and send us your booking request.
                </p>

                <a href="package_customize.php"
                   class="btn btn-primary"
                   id="customizePackageBtn">

                    Customize This Package

                </a>

                <a href="index.php#packages"
                   class="btn-back">

                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Packages

                </a>

            </div>

        </div>

    </div>

</section>


<!-- ================================
     FOOTER
================================= -->

<footer class="footer">

    <div class="footer-container">

        <div class="footer-about">

            <div class="logo">
                <span>NUU</span>STAR
                <small>EVENTS</small>
            </div>

            <p>
                Your event. Our passion.
                We help you create memorable events
                with professional planning and coordination.
            </p>

        </div>


        <div class="footer-links">

            <h3>Quick Links</h3>

            <a href="index.php">Home</a>
            <a href="index.php#packages">Packages</a>
            <a href="index.php#services">Services</a>
            <a href="venues.php">Venues</a>
            <a href="venue-booking.php">Book Your Event</a>

        </div>


        <div class="footer-services">

            <h3>Our Services</h3>

            <a href="#">Venue Selection</a>
            <a href="#">Event Decoration</a>
            <a href="#">Catering</a>
            <a href="#">Photography</a>
            <a href="#">Videography</a>

        </div>


        <div class="footer-contact">

            <h3>Contact Us</h3>

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

        <p>
            © 2026 Nuustar Events. All Rights Reserved.
        </p>

    </div>

</footer>


<!-- ================================
     JAVASCRIPT
================================= -->

<script>

    /*
       Read the package from the URL.

       Example:

       package-details.php?package=silver

       package-details.php?package=gold

       package-details.php?package=premium

       package-details.php?package=luxury
    */

    const urlParams = new URLSearchParams(window.location.search);

    const selectedPackage = urlParams.get("package");


    const packages = {

        silver: {

            name: "Silver Package",

            price: "Starting from TZS 1,500,000",

            features: [
                "Basic event coordination",
                "Elegant event decoration",
                "Basic photography",
                "Catering services",
                "Event setup support"
            ],

            events: [
                "Birthday celebrations",
                "Small weddings",
                "Anniversaries",
                "Private parties"
            ]

        },


        gold: {

            name: "Gold Package",

            price: "Starting from TZS 2,500,000",

            features: [
                "Professional event coordination",
                "Elegant event decoration",
                "Professional photography",
                "Professional videography",
                "Catering services",
                "Event hosting support"
            ],

            events: [
                "Weddings",
                "Birthday celebrations",
                "Anniversaries",
                "Corporate events",
                "Private celebrations"
            ]

        },


        premium: {

            name: "Premium Package",

            price: "Starting from TZS 4,000,000",

            features: [
                "Full event planning",
                "Premium event decoration",
                "Professional photography",
                "Professional videography",
                "Premium catering",
                "Professional event hosting",
                "Guest management support"
            ],

            events: [
                "Large weddings",
                "Corporate events",
                "Conferences",
                "Luxury celebrations",
                "Major private events"
            ]

        },


        luxury: {

            name: "Luxury Package",

            price: "Starting from TZS 7,000,000",

            features: [
                "Complete event planning",
                "Luxury event decoration",
                "Premium photography",
                "Premium videography",
                "Premium catering",
                "Professional hosting",
                "Guest management",
                "Dedicated event coordinator"
            ],

            events: [
                "Luxury weddings",
                "High-end corporate events",
                "VIP celebrations",
                "Large conferences",
                "Exclusive private events"
            ]

        }

    };


    /*
       Find the selected package
    */

    const packageData = packages[selectedPackage] || packages.gold;


    /*
       Display package name
    */

    document.getElementById("packageName").textContent =
        packageData.name;


    /*
       Display package price
    */

    document.getElementById("packagePrice").textContent =
        packageData.price;


    /*
       Display features
    */

    const featuresList =
        document.getElementById("packageFeatures");

    featuresList.innerHTML = "";

    packageData.features.forEach(function(feature) {

        const li = document.createElement("li");

        li.innerHTML =
            '<i class="fa-solid fa-check"></i>' + feature;

        featuresList.appendChild(li);

    });


    /*
       Display suitable events
    */

    const eventsList =
        document.getElementById("packageEvents");

    eventsList.innerHTML = "";

    packageData.events.forEach(function(event) {

        const li = document.createElement("li");

        li.innerHTML =
            '<i class="fa-solid fa-star"></i>' + event;

        eventsList.appendChild(li);

    });


    /*
       Send selected package to the next page
    */
document.getElementById("bookPackageBtn").href =
    "package-customize.php?package=" + selectedPackage;

</script>

</body>
</html>