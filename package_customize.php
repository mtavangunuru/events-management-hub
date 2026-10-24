<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customize Package | Nuustar Events</title>

    <link rel="stylesheet" href="style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <style>

        /* ==========================================
           CUSTOMIZE PAGE
        ========================================== */

        .customize-page {
            padding: 70px 0;
            background: var(--gray-light);
        }

        .customize-container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }


        /* ==========================================
           HEADER
        ========================================== */

        .customize-header {
            text-align: center;
            margin-bottom: 45px;
        }

        .customize-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: var(--blue);
            margin-bottom: 12px;
        }

        .customize-header p {
            max-width: 700px;
            margin: auto;
            color: var(--gray);
            line-height: 1.7;
        }


        /* ==========================================
           GRID
        ========================================== */

        .customize-grid {
            display: grid;
            grid-template-columns: 1.7fr 1fr;
            gap: 30px;
            align-items: start;
        }


        /* ==========================================
           CARDS
        ========================================== */

        .customize-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 35px;
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
        }

        .customize-card h2 {
            color: var(--blue);
            font-size: 23px;
            margin-bottom: 8px;
        }

        .section-description {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.6;
        }


        /* ==========================================
           SELECTED PACKAGE
        ========================================== */

        .selected-package {
            background: var(--blue);
            color: var(--white);
            padding: 28px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .selected-package h2 {
            color: var(--white);
            margin-bottom: 7px;
        }

        .selected-package p {
            margin: 0;
            opacity: .85;
        }

        .selected-package-price {
            color: var(--yellow);
            font-size: 23px;
            font-weight: 800;
            white-space: nowrap;
        }


        /* ==========================================
           INCLUDED SERVICES
        ========================================== */

        .included-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .included-item {
            padding: 13px 15px;
            background: var(--blue-light);
            border-radius: 8px;
            color: var(--blue);
            font-size: 14px;
            font-weight: 600;
        }

        .included-item i {
            color: var(--yellow-dark);
            margin-right: 8px;
        }


        /* ==========================================
           FORM
        ========================================== */

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--blue);
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(11,45,92,.08);
        }


        /* ==========================================
           ADD-ON SERVICES
        ========================================== */

        .service-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .service-option {
            position: relative;
        }

        .service-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .service-label {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: var(--transition);
            background: var(--white);
        }

        .service-label:hover {
            border-color: var(--yellow);
            transform: translateY(-2px);
        }

        .service-option input:checked + .service-label {
            border-color: var(--yellow);
            background: var(--yellow-light);
        }

        .service-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 50%;
            background: var(--blue-light);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-info {
            flex: 1;
        }

        .service-info strong {
            display: block;
            color: var(--blue);
            font-size: 13px;
            margin-bottom: 4px;
        }

        .service-info small {
            color: var(--gray);
            font-size: 11px;
        }

        .service-price {
            font-weight: 800;
            color: var(--blue);
            font-size: 12px;
            white-space: nowrap;
        }


        /* ==========================================
           SUMMARY
        ========================================== */

        .summary-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 30px;
        }

        .summary-card h2 {
            font-family: 'Playfair Display', serif;
            color: var(--blue);
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 13px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .summary-row span:first-child {
            color: var(--gray);
        }

        .summary-row span:last-child {
            color: var(--blue);
            font-weight: 700;
            text-align: right;
        }

        .summary-services {
            margin-top: 20px;
        }

        .summary-services h3 {
            color: var(--blue);
            font-size: 16px;
            margin-bottom: 12px;
        }

        .summary-services ul {
            padding-left: 18px;
            margin: 0;
        }

        .summary-services li {
            margin-bottom: 8px;
            color: var(--gray-dark);
            font-size: 13px;
        }

        .summary-total {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid var(--blue);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .summary-total span:first-child {
            font-weight: 700;
            color: var(--blue);
        }

        .summary-total span:last-child {
            font-size: 23px;
            font-weight: 800;
            color: var(--yellow-dark);
            text-align: right;
        }

        .summary-note {
            margin-top: 15px;
            font-size: 12px;
            color: var(--gray);
            line-height: 1.6;
        }


        /* ==========================================
           BUTTONS
        ========================================== */

        .customize-actions {
            margin-top: 30px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-secondary-custom {
            display: inline-block;
            padding: 13px 24px;
            border: 2px solid var(--blue);
            border-radius: 8px;
            text-decoration: none;
            color: var(--blue);
            font-weight: 700;
            transition: var(--transition);
        }

        .btn-secondary-custom:hover {
            background: var(--blue);
            color: var(--white);
        }


        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 900px) {

            .customize-grid {
                grid-template-columns: 1fr;
            }

            .summary-card {
                position: static;
            }

        }


        @media (max-width: 650px) {

            .customize-page {
                padding: 45px 0;
            }

            .customize-header h1 {
                font-size: 32px;
            }

            .customize-card {
                padding: 22px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: auto;
            }

            .service-options,
            .included-list {
                grid-template-columns: 1fr;
            }

            .selected-package {
                flex-direction: column;
                align-items: flex-start;
            }

        }

    </style>

</head>


<body>


<!-- ==================================================
     NAVBAR
================================================== -->

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
        </a>

    </nav>

</header>



<!-- ==================================================
     CUSTOMIZE PAGE
================================================== -->

<section class="customize-page">

    <div class="customize-container">


        <!-- HEADER -->

        <div class="customize-header">

            <h1>Customize Your Package</h1>

            <p>
                Personalize your event package by selecting
                the services you need. Your estimated total
                will update automatically.
            </p>

        </div>



        <div class="customize-grid">


            <!-- ==================================================
                 LEFT SIDE
            ================================================== -->

            <div>


                <!-- SELECTED PACKAGE -->

                <div class="customize-card">

                    <div class="selected-package">

                        <div>

                            <h2 id="packageName">
                                Gold Package
                            </h2>

                            <p id="packageDescription">
                                A complete package for memorable events.
                            </p>

                        </div>

                        <div class="selected-package-price"
                             id="packagePrice">

                            TZS 2,500,000

                        </div>

                    </div>

                </div>



                <!-- INCLUDED SERVICES -->

                <div class="customize-card">

                    <h2>
                        <i class="fa-solid fa-circle-check"></i>
                        Included in Your Package
                    </h2>

                    <p class="section-description">
                        These services are already included in your
                        selected package.
                    </p>


                    <div class="included-list"
                         id="includedServices">

                    </div>

                </div>



                <!-- EVENT DETAILS -->

                <div class="customize-card">

                    <h2>
                        <i class="fa-solid fa-calendar-days"></i>
                        Event Details
                    </h2>

                    <p class="section-description">
                        Tell us about the event you are planning.
                    </p>


                    <div class="form-grid">


                        <div class="form-group">

                            <label for="eventType">
                                Event Type *
                            </label>

                            <select id="eventType">

                                <option value="">
                                    Select event type
                                </option>

                                <option>Wedding</option>

                                <option>Birthday</option>

                                <option>Anniversary</option>

                                <option>Corporate Event</option>

                                <option>Conference</option>

                                <option>Meeting</option>

                                <option>Party</option>

                                <option>Other</option>

                            </select>

                        </div>



                        <div class="form-group">

                            <label for="eventDate">
                                Event Date *
                            </label>

                            <input type="date"
                                   id="eventDate">

                        </div>



                        <div class="form-group">

                            <label for="guests">
                                Number of Guests *
                            </label>

                            <input type="number"
                                   id="guests"
                                   min="1"
                                   placeholder="e.g. 100">

                        </div>



                        <div class="form-group">

                            <label for="location">
                                Preferred Location
                            </label>

                            <input type="text"
                                   id="location"
                                   placeholder="e.g. Mbezi Beach">

                        </div>



                        <div class="form-group full-width">

                            <label for="venueType">
                                Preferred Venue Type
                            </label>

                            <select id="venueType">

                                <option value="">
                                    Select venue type
                                </option>

                                <option>Indoor</option>

                                <option>Outdoor</option>

                                <option>Garden</option>

                                <option>Beach</option>

                                <option>Hotel</option>

                                <option>Conference Venue</option>

                                <option>Any</option>

                            </select>

                        </div>


                    </div>

                </div>



                <!-- ADDITIONAL SERVICES -->

                <div class="customize-card">

                    <h2>
                        <i class="fa-solid fa-puzzle-piece"></i>
                        Add Extra Services
                    </h2>

                    <p class="section-description">
                        These are optional services that are not
                        already included in your selected package.
                    </p>


                    <div class="service-options">


                        <!-- DECORATION -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="decoration"
                                   value="Premium Decoration"
                                   data-price="300000">

                            <label for="decoration"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Premium Decoration</strong>

                                    <small>
                                        Upgrade your decoration
                                    </small>

                                </div>

                                <div class="service-price">
                                    +300K
                                </div>

                            </label>

                        </div>



                        <!-- PHOTOGRAPHY -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="photography"
                                   value="Professional Photography"
                                   data-price="250000">

                            <label for="photography"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-camera"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Photography</strong>

                                    <small>
                                        Professional event photography
                                    </small>

                                </div>

                                <div class="service-price">
                                    +250K
                                </div>

                            </label>

                        </div>



                        <!-- VIDEOGRAPHY -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="videography"
                                   value="Professional Videography"
                                   data-price="350000">

                            <label for="videography"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-video"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Videography</strong>

                                    <small>
                                        Professional event video
                                    </small>

                                </div>

                                <div class="service-price">
                                    +350K
                                </div>

                            </label>

                        </div>



                        <!-- CATERING -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="catering"
                                   value="Premium Catering"
                                   data-price="500000">

                            <label for="catering"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-utensils"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Premium Catering</strong>

                                    <small>
                                        Upgrade your catering
                                    </small>

                                </div>

                                <div class="service-price">
                                    +500K
                                </div>

                            </label>

                        </div>



                        <!-- CAKE -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="cake"
                                   value="Event Cake"
                                   data-price="150000">

                            <label for="cake"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-cake-candles"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Event Cake</strong>

                                    <small>
                                        Customized celebration cake
                                    </small>

                                </div>

                                <div class="service-price">
                                    +150K
                                </div>

                            </label>

                        </div>



                        <!-- MC -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="mc"
                                   value="Professional MC / Hosting"
                                   data-price="200000">

                            <label for="mc"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-microphone"></i>
                                </div>

                                <div class="service-info">

                                    <strong>MC / Hosting</strong>

                                    <small>
                                        Professional event hosting
                                    </small>

                                </div>

                                <div class="service-price">
                                    +200K
                                </div>

                            </label>

                        </div>



                        <!-- VENUE -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="venue"
                                   value="Venue Selection"
                                   data-price="200000">

                            <label for="venue"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Venue Selection</strong>

                                    <small>
                                        Help finding the right venue
                                    </small>

                                </div>

                                <div class="service-price">
                                    +200K
                                </div>

                            </label>

                        </div>



                        <!-- SOUND -->

                        <div class="service-option">

                            <input type="checkbox"
                                   id="sound"
                                   value="Professional Sound System"
                                   data-price="300000">

                            <label for="sound"
                                   class="service-label">

                                <div class="service-icon">
                                    <i class="fa-solid fa-volume-high"></i>
                                </div>

                                <div class="service-info">

                                    <strong>Sound System</strong>

                                    <small>
                                        Professional event sound
                                    </small>

                                </div>

                                <div class="service-price">
                                    +300K
                                </div>

                            </label>

                        </div>


                    </div>

                </div>



                <!-- ADDITIONAL NOTES -->

                <div class="customize-card">

                    <h2>
                        <i class="fa-solid fa-message"></i>
                        Additional Requirements
                    </h2>

                    <p class="section-description">
                        Tell us about any special requirements,
                        theme, colors or ideas you have.
                    </p>


                    <div class="form-group">

                        <textarea id="notes"
                                  placeholder="Example: I would like blue and white decoration, a garden venue and a small birthday cake..."></textarea>

                    </div>


                    <div class="customize-actions">

                        <a href="index.php#packages"
                           class="btn-secondary-custom">

                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Packages

                        </a>


                        <button type="button"
                                class="btn btn-primary"
                                id="continueBtn">

                            Continue to Booking

                            <i class="fa-solid fa-arrow-right"></i>

                        </button>

                    </div>

                </div>


            </div>



            <!-- ==================================================
                 RIGHT SUMMARY
            ================================================== -->

            <aside class="summary-card">

                <h2>
                    Package Summary
                </h2>


                <div class="summary-row">

                    <span>Package</span>

                    <span id="summaryPackage">
                        Gold Package
                    </span>

                </div>


                <div class="summary-row">

                    <span>Base Price</span>

                    <span id="summaryBase">
                        TZS 2,500,000
                    </span>

                </div>


                <div class="summary-row">

                    <span>Event Type</span>

                    <span id="summaryEvent">
                        Not selected
                    </span>

                </div>


                <div class="summary-row">

                    <span>Guests</span>

                    <span id="summaryGuests">
                        Not specified
                    </span>

                </div>


                <div class="summary-services">

                    <h3>
                        Included Services
                    </h3>

                    <ul id="summaryIncluded">
                    </ul>


                    <h3 style="margin-top:25px;">
                        Added Services
                    </h3>

                    <ul id="summaryAdded">

                        <li>
                            No additional services
                        </li>

                    </ul>

                </div>


                <div class="summary-total">

                    <span>
                        Estimated Total
                    </span>

                    <span id="summaryTotal">
                        TZS 2,500,000
                    </span>

                </div>


                <p class="summary-note">

                    * The displayed amount is an estimated price.
                    The final quotation may depend on your event
                    requirements, guest number, venue and location.

                </p>

            </aside>


        </div>

    </div>

</section>



<!-- ==================================================
     FOOTER
================================================== -->

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

            <a href="index.php#packages">
                Packages
            </a>

            <a href="index.php#services">
                Services
            </a>

            <a href="venues.php">
                Venues
            </a>

            <a href="venue-booking.php">
                Book Your Event
            </a>

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



<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script>


/* ==================================================
   GET PACKAGE FROM URL
================================================== */

const urlParams =
    new URLSearchParams(window.location.search);

const selectedPackage =
    urlParams.get("package") || "gold";



/* ==================================================
   PACKAGE INFORMATION
================================================== */

const packages = {

    silver: {

        name: "Silver Package",

        price: 1500000,

        description:
            "A simple and affordable package for intimate and memorable events.",

        included: [

            "Basic Event Coordination",

            "Basic Event Decoration",

            "Catering Services",

            "Event Setup Support"

        ]

    },


    gold: {

        name: "Gold Package",

        price: 2500000,

        description:
            "A balanced package for beautiful and professionally organized events.",

        included: [

            "Professional Event Coordination",

            "Elegant Event Decoration",

            "Professional Photography",

            "Catering Services",

            "Event Hosting Support"

        ]

    },


    premium: {

        name: "Premium Package",

        price: 4000000,

        description:
            "A premium event experience with enhanced services and professional support.",

        included: [

            "Full Event Planning",

            "Premium Event Decoration",

            "Professional Photography",

            "Professional Videography",

            "Premium Catering",

            "Professional Event Hosting",

            "Guest Management Support"

        ]

    },


    luxury: {

        name: "Luxury Package",

        price: 7000000,

        description:
            "Our complete luxury event experience with dedicated planning and premium services.",

        included: [

            "Complete Event Planning",

            "Luxury Event Decoration",

            "Premium Photography",

            "Premium Videography",

            "Premium Catering",

            "Professional Event Hosting",

            "Guest Management",

            "Dedicated Event Coordinator"

        ]

    }

};



/* ==================================================
   GET SELECTED PACKAGE
================================================== */

const packageData =
    packages[selectedPackage] || packages.gold;



/* ==================================================
   ELEMENTS
================================================== */

const packageName =
    document.getElementById("packageName");

const packageDescription =
    document.getElementById("packageDescription");

const packagePrice =
    document.getElementById("packagePrice");

const includedServices =
    document.getElementById("includedServices");

const summaryPackage =
    document.getElementById("summaryPackage");

const summaryBase =
    document.getElementById("summaryBase");

const summaryTotal =
    document.getElementById("summaryTotal");

const summaryEvent =
    document.getElementById("summaryEvent");

const summaryGuests =
    document.getElementById("summaryGuests");

const summaryIncluded =
    document.getElementById("summaryIncluded");

const summaryAdded =
    document.getElementById("summaryAdded");



/* ==================================================
   FORMAT MONEY
================================================== */

function formatMoney(amount) {

    return "TZS " + amount.toLocaleString("en-US");

}



/* ==================================================
   DISPLAY PACKAGE INFORMATION
================================================== */

packageName.textContent =
    packageData.name;

packageDescription.textContent =
    packageData.description;

packagePrice.textContent =
    formatMoney(packageData.price);

summaryPackage.textContent =
    packageData.name;

summaryBase.textContent =
    formatMoney(packageData.price);

summaryTotal.textContent =
    formatMoney(packageData.price);



/* ==================================================
   DISPLAY INCLUDED SERVICES
================================================== */

includedServices.innerHTML = "";

summaryIncluded.innerHTML = "";


packageData.included.forEach(function(service) {


    /* LEFT SIDE */

    const item =
        document.createElement("div");

    item.className =
        "included-item";

    item.innerHTML =
        '<i class="fa-solid fa-check"></i>' +
        service;

    includedServices.appendChild(item);



    /* SUMMARY */

    const summaryItem =
        document.createElement("li");

    summaryItem.textContent =
        service;

    summaryIncluded.appendChild(summaryItem);

});



/* ==================================================
   SERVICE CHECKBOXES
================================================== */

const serviceCheckboxes =
    document.querySelectorAll(
        '.service-option input[type="checkbox"]'
    );



/* ==================================================
   UPDATE SUMMARY
================================================== */

function updateSummary() {

    let total =
        packageData.price;

    let selectedServices = [];


    serviceCheckboxes.forEach(function(checkbox) {

        if (checkbox.checked) {

            const price =
                Number(checkbox.dataset.price);

            total += price;

            selectedServices.push({

                name:
                    checkbox.value,

                price:
                    price

            });

        }

    });



    /* ------------------------------------------
       ADDED SERVICES
    ------------------------------------------ */

    summaryAdded.innerHTML = "";


    if (selectedServices.length === 0) {

        const li =
            document.createElement("li");

        li.textContent =
            "No additional services";

        summaryAdded.appendChild(li);

    } else {

        selectedServices.forEach(function(service) {

            const li =
                document.createElement("li");

            li.textContent =
                service.name +
                " - " +
                formatMoney(service.price);

            summaryAdded.appendChild(li);

        });

    }



    /* ------------------------------------------
       TOTAL
    ------------------------------------------ */

    summaryTotal.textContent =
        formatMoney(total);

}



/* ==================================================
   CHECKBOX EVENTS
================================================== */

serviceCheckboxes.forEach(function(checkbox) {

    checkbox.addEventListener(
        "change",
        updateSummary
    );

});



/* ==================================================
   EVENT TYPE
================================================== */

const eventType =
    document.getElementById("eventType");


eventType.addEventListener(
    "change",
    function() {

        summaryEvent.textContent =
            this.value || "Not selected";

    }
);



/* ==================================================
   GUESTS
================================================== */

const guests =
    document.getElementById("guests");


guests.addEventListener(
    "input",
    function() {

        summaryGuests.textContent =
            this.value
            ? Number(this.value).toLocaleString()
            : "Not specified";

    }
);



/* ==================================================
   CONTINUE BUTTON
================================================== */

const continueBtn =
    document.getElementById("continueBtn");


continueBtn.addEventListener(
    "click",
    function() {


        /* --------------------------------------
           VALIDATION
        -------------------------------------- */

        if (!eventType.value) {

            alert("Please select your event type.");

            eventType.focus();

            return;

        }


        const eventDate =
            document.getElementById("eventDate");


        if (!eventDate.value) {

            alert("Please select your event date.");

            eventDate.focus();

            return;

        }


        if (
            !guests.value ||
            Number(guests.value) < 1
        ) {

            alert("Please enter the number of guests.");

            guests.focus();

            return;

        }



        /* --------------------------------------
           COLLECT SERVICES
        -------------------------------------- */

        const selectedServices = [];


        serviceCheckboxes.forEach(function(checkbox) {

            if (checkbox.checked) {

                selectedServices.push({

                    name:
                        checkbox.value,

                    price:
                        Number(checkbox.dataset.price)

                });

            }

        });



        /* --------------------------------------
           CALCULATE TOTAL
        -------------------------------------- */

        let total =
            packageData.price;


        selectedServices.forEach(function(service) {

            total += service.price;

        });



        /* --------------------------------------
           CREATE CUSTOMIZATION DATA
        -------------------------------------- */

        const customization = {

            package:
                selectedPackage,

            packageName:
                packageData.name,

            basePrice:
                packageData.price,

            includedServices:
                packageData.included,

            eventType:
                eventType.value,

            eventDate:
                eventDate.value,

            guests:
                Number(guests.value),

            location:
                document.getElementById("location").value,

            venueType:
                document.getElementById("venueType").value,

            additionalServices:
                selectedServices,

            estimatedTotal:
                total,

            notes:
                document.getElementById("notes").value

        };



        /* --------------------------------------
           TEMPORARY STORAGE
        -------------------------------------- */

        localStorage.setItem(
            "nuustarCustomization",
            JSON.stringify(customization)
        );



        /* --------------------------------------
           NEXT PAGE
        -------------------------------------- */

            window.location.href =
        "package-booking.php?package=" + selectedPackage;

    }
);


</script>


</body>

</html>