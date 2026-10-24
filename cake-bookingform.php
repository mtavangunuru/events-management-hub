
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cake Booking Form | Nuustar Events</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Main CSS -->
    <link rel="stylesheet" href="style.css">


    <style>

        /* =========================================
           CAKE BOOKING FORM
        ========================================= */

        .cake-booking-page {
            min-height: 100vh;
            background: var(--gray-light);
            padding: 120px 20px 70px;
        }


        .cake-form-container {
            max-width: 1100px;
            margin: 0 auto;
        }


        /* HEADER */

        .cake-form-header {
            text-align: center;
            margin-bottom: 35px;
        }


        .cake-form-header h1 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            font-size: 42px;
            margin-bottom: 10px;
        }


        .cake-form-header p {
            max-width: 700px;
            margin: 0 auto;
            color: var(--gray);
            line-height: 1.7;
        }


        /* PROGRESS */

        .progress {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 35px;
        }


        .progress-step {
            display: flex;
            align-items: center;
            gap: 8px;
        }


        .step-number {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 13px;
            font-weight: 800;
        }


        .step-number.active {
            background: var(--yellow);
        }


        .step-number.done {
            background: var(--blue);
            color: var(--white);
        }


        .step-label {
            color: var(--gray);
            font-size: 12px;
            font-weight: 700;
        }


        .step-label.active {
            color: var(--blue);
        }


        .progress-line {
            width: 65px;
            height: 2px;
            background: var(--border);
            margin: 0 10px;
        }


        /* GRID */

        .cake-booking-grid {
            display: grid;
            grid-template-columns: 1.3fr 0.75fr;
            gap: 25px;
            align-items: start;
        }


        /* CARD */

        .cake-form-card {
            background: var(--white);
            padding: 30px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
        }


        .cake-form-card h2 {
            color: var(--blue);
            font-size: 21px;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--yellow);
        }


        .cake-form-card h2 i {
            color: var(--yellow-dark);
            margin-right: 6px;
        }


        /* FORM */

        .form-group {
            margin-bottom: 18px;
        }


        .form-group label {
            display: block;
            color: var(--blue);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 7px;
        }


        .required {
            color: #dc2626;
        }


        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            background: var(--white);
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
        }


        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--yellow-dark);
        }


        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }


        /* CAKE INFO */

        .cake-note {
            background: var(--blue-light);
            color: var(--gray-dark);
            padding: 15px;
            border-radius: 9px;
            font-size: 13px;
            line-height: 1.7;
            margin-bottom: 20px;
        }


        .cake-note i {
            color: var(--yellow-dark);
            margin-right: 5px;
        }


        /* DESIGN OPTIONS */

        .design-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }


        .design-option {
            position: relative;
        }


        .design-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }


        .design-label {
            display: block;
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 16px 12px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }


        .design-label i {
            display: block;
            color: var(--yellow-dark);
            font-size: 22px;
            margin-bottom: 8px;
        }


        .design-label strong {
            display: block;
            color: var(--blue);
            font-size: 13px;
            margin-bottom: 4px;
        }


        .design-label span {
            color: var(--gray);
            font-size: 11px;
        }


        .design-option input:checked + .design-label {
            border-color: var(--blue);
            background: var(--blue-light);
        }


        /* AGREEMENT */

        .agreement {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin-top: 20px;
            color: var(--gray-dark);
            font-size: 13px;
            line-height: 1.6;
        }


        .agreement input {
            width: auto;
            margin-top: 3px;
            accent-color: var(--blue);
        }


        /* BUTTON */

        .submit-btn {
            width: 100%;
            margin-top: 20px;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: var(--yellow);
            color: var(--blue);
            font-family: "Montserrat", sans-serif;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
        }


        .submit-btn:hover {
            background: var(--yellow-dark);
            transform: translateY(-2px);
        }


        /* SUMMARY */

        .cake-summary-card {
            position: sticky;
            top: 100px;
        }


        .summary-cake {
            background: var(--blue);
            color: var(--white);
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 20px;
        }


        .summary-cake small {
            display: block;
            color: rgba(255,255,255,0.7);
            margin-bottom: 5px;
        }


        .summary-cake h3 {
            font-family: "Playfair Display", serif;
            font-size: 25px;
            margin-bottom: 5px;
        }


        .summary-cake p {
            font-size: 13px;
            line-height: 1.6;
            color: rgba(255,255,255,0.85);
        }


        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }


        .summary-row span:first-child {
            color: var(--gray);
        }


        .summary-row span:last-child {
            color: var(--blue);
            font-weight: 700;
            text-align: right;
        }


        .price-box {
            background: var(--yellow-light);
            border-radius: 10px;
            padding: 18px;
            margin-top: 20px;
        }


        .price-line {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 5px 0;
            font-size: 13px;
        }


        .price-line span:first-child {
            color: var(--gray-dark);
        }


        .price-line span:last-child {
            color: var(--blue);
            font-weight: 700;
        }


        .total-line {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid rgba(11,45,92,0.15);
            padding-top: 13px;
            margin-top: 10px;
            color: var(--blue);
            font-size: 20px;
            font-weight: 800;
        }


        .summary-note {
            background: var(--blue-light);
            color: var(--gray-dark);
            border-radius: 9px;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
            line-height: 1.7;
        }


        .summary-note i {
            color: var(--yellow-dark);
            margin-right: 5px;
        }


        /* RESPONSIVE */

        @media (max-width: 850px) {

            .cake-booking-grid {
                grid-template-columns: 1fr;
            }

            .cake-summary-card {
                position: static;
            }

        }


        @media (max-width: 600px) {

            .cake-booking-page {
                padding: 100px 15px 50px;
            }


            .cake-form-header h1 {
                font-size: 32px;
            }


            .cake-form-card {
                padding: 22px;
            }


            .form-row {
                grid-template-columns: 1fr;
            }


            .design-grid {
                grid-template-columns: 1fr;
            }


            .progress-line {
                width: 25px;
            }


            .step-label {
                display: none;
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

                <a href="index.php">
                    Home
                </a>

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

                <a
                    href="index.php#packages"
                    class="nav-book-btn"
                >
                    Book Your Event
                </a>

            </div>

        </div>

    </nav>


    <!-- =========================
         MAIN
    ========================== -->

    <main class="cake-booking-page">


        <div class="cake-form-container">


            <!-- HEADER -->

            <div class="cake-form-header">

                <h1>
                    Order Your Cake
                </h1>

                <p>
                    Choose your cake size, flavour and design,
                    then provide your event details. The price shown
                    is an estimated price and may change depending
                    on the final design.
                </p>

            </div>


            <!-- PROGRESS -->

            <div class="progress">

                <div class="progress-step">

                    <div class="step-number done">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <span class="step-label">
                        Cake Samples
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-step">

                    <div class="step-number active">
                        2
                    </div>

                    <span class="step-label active">
                        Cake Order
                    </span>

                </div>


                <div class="progress-line"></div>


                <div class="progress-step">

                    <div class="step-number">
                        3
                    </div>

                    <span class="step-label">
                        Confirmation
                    </span>

                </div>

            </div>


            <!-- GRID -->

            <div class="cake-booking-grid">


                <!-- ====================================
                     LEFT
                ===================================== -->

                <div>


                    <!-- CAKE DETAILS -->

                    <div class="cake-form-card">

                        <h2>
                            <i class="fa-solid fa-cake-candles"></i>
                            Cake Details
                        </h2>


                        <div class="cake-note">

                            <i class="fa-solid fa-circle-info"></i>

                            The prices below are estimates.
                            Larger sizes, premium flavours and
                            more detailed decorations may increase
                            the final price.

                        </div>


                        <!-- SIZE -->

                        <div class="form-row">

                            <div class="form-group">

                                <label for="cakeSize">
                                    Cake Size
                                    <span class="required">*</span>
                                </label>

                                <select id="cakeSize">

                                    <option value="1">
                                        1 kg
                                    </option>

                                    <option value="1.5">
                                        1.5 kg
                                    </option>

                                    <option value="2">
                                        2 kg
                                    </option>

                                    <option value="3">
                                        3 kg
                                    </option>

                                    <option value="4">
                                        4 kg
                                    </option>

                                    <option value="5">
                                        5 kg
                                    </option>

                                </select>

                            </div>


                            <!-- FLAVOUR -->

                            <div class="form-group">

                                <label for="flavour">
                                    Flavour
                                    <span class="required">*</span>
                                </label>

                                <select id="flavour">

                                    <option value="Vanilla">
                                        Vanilla
                                    </option>

                                    <option value="Chocolate">
                                        Chocolate
                                    </option>

                                    <option value="Lemon">
                                        Lemon
                                    </option>

                                    <option value="Strawberry">
                                        Strawberry
                                    </option>

                                    <option value="Carrot">
                                        Carrot
                                    </option>

                                    <option value="Coconut">
                                        Coconut
                                    </option>

                                    <option value="Red Velvet">
                                        Red Velvet
                                    </option>

                                    <option value="Mixed Flavour">
                                        Mixed Flavour
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- DESIGN -->

                        <div class="form-group">

                            <label>
                                Cake Design
                                <span class="required">*</span>
                            </label>


                            <div class="design-grid">


                                <div class="design-option">

                                    <input
                                        type="radio"
                                        name="design"
                                        id="simpleDesign"
                                        value="Simple"
                                        data-price="0"
                                        checked
                                    >

                                    <label
                                        for="simpleDesign"
                                        class="design-label"
                                    >

                                        <i class="fa-solid fa-cake-candles"></i>

                                        <strong>
                                            Simple
                                        </strong>

                                        <span>
                                            Standard decoration
                                        </span>

                                    </label>

                                </div>


                                <div class="design-option">

                                    <input
                                        type="radio"
                                        name="design"
                                        id="elegantDesign"
                                        value="Elegant"
                                        data-price="15000"
                                    >

                                    <label
                                        for="elegantDesign"
                                        class="design-label"
                                    >

                                        <i class="fa-solid fa-star"></i>

                                        <strong>
                                            Elegant
                                        </strong>

                                        <span>
                                            Enhanced decoration
                                        </span>

                                    </label>

                                </div>


                                <div class="design-option">

                                    <input
                                        type="radio"
                                        name="design"
                                        id="customDesign"
                                        value="Custom"
                                        data-price="30000"
                                    >

                                    <label
                                        for="customDesign"
                                        class="design-label"
                                    >

                                        <i class="fa-solid fa-wand-magic-sparkles"></i>

                                        <strong>
                                            Custom
                                        </strong>

                                        <span>
                                            Detailed custom design
                                        </span>

                                    </label>

                                </div>

                            </div>

                        </div>


                        <!-- QUANTITY -->

                        <div class="form-group">

                            <label for="quantity">
                                Number of Cakes
                                <span class="required">*</span>
                            </label>

                            <input
                                type="number"
                                id="quantity"
                                min="1"
                                max="20"
                                value="1"
                            >

                        </div>

                    </div>


                    <!-- EVENT INFORMATION -->

                    <div class="cake-form-card">

                        <h2>
                            <i class="fa-solid fa-calendar-days"></i>
                            Event Information
                        </h2>


                        <div class="form-row">

                            <div class="form-group">

                                <label for="eventType">
                                    Event Type
                                    <span class="required">*</span>
                                </label>

                                <select id="eventType">

                                    <option value="">
                                        Select Event Type
                                    </option>

                                    <option>
                                        Wedding
                                    </option>

                                    <option>
                                        Birthday
                                    </option>

                                    <option>
                                        Anniversary
                                    </option>

                                    <option>
                                        Baby Shower
                                    </option>

                                    <option>
                                        Graduation
                                    </option>

                                    <option>
                                        Corporate Event
                                    </option>

                                    <option>
                                        Other
                                    </option>

                                </select>

                            </div>


                            <div class="form-group">

                                <label for="eventDate">
                                    Event Date
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="eventDate"
                                >

                            </div>

                        </div>


                        <div class="form-row">

                            <div class="form-group">

                                <label for="eventTime">
                                    Event Time
                                </label>

                                <input
                                    type="time"
                                    id="eventTime"
                                >

                            </div>


                            <div class="form-group">

                                <label for="delivery">
                                    Delivery / Collection
                                </label>

                                <select id="delivery">

                                    <option value="Customer Collection">
                                        Customer Collection
                                    </option>

                                    <option value="Nuustar Delivery">
                                        Nuustar Delivery
                                    </option>

                                </select>

                            </div>

                        </div>


                        <div class="form-group">

                            <label for="location">
                                Event / Delivery Location
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="location"
                                placeholder="e.g. Mbezi Beach, Dar es Salaam"
                            >

                        </div>

                    </div>


                    <!-- CUSTOMER -->

                    <div class="cake-form-card">

                        <h2>
                            <i class="fa-solid fa-user"></i>
                            Customer Information
                        </h2>


                        <div class="form-row">

                            <div class="form-group">

                                <label for="fullName">
                                    Full Name
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="fullName"
                                    placeholder="Enter your full name"
                                >

                            </div>


                            <div class="form-group">

                                <label for="phone">
                                    Phone Number
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="tel"
                                    id="phone"
                                    placeholder="e.g. 0624947668"
                                >

                            </div>

                        </div>


                        <div class="form-row">

                            <div class="form-group">

                                <label for="whatsapp">
                                    WhatsApp Number
                                </label>

                                <input
                                    type="tel"
                                    id="whatsapp"
                                    placeholder="e.g. 0624947668"
                                >

                            </div>


                            <div class="form-group">

                                <label for="email">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    placeholder="example@email.com"
                                >

                            </div>

                        </div>


                        <!-- CUSTOM DESCRIPTION -->

                        <div class="form-group">

                            <label for="cakeMessage">
                                Cake Design / Special Requirements
                            </label>

                            <textarea
                                id="cakeMessage"
                                placeholder="Describe the colour, theme, writing, character, topper, filling or design you would like..."
                            ></textarea>

                        </div>


                        <!-- AGREEMENT -->

                        <label class="agreement">

                            <input
                                type="checkbox"
                                id="agreement"
                            >

                            <span>
                                I understand that the displayed amount
                                is an estimate and that Nuustar Events
                                will confirm the final cake price before
                                the order is finalized.
                            </span>

                        </label>


                        <button
                            type="button"
                            class="submit-btn"
                            onclick="submitCakeOrder()"
                        >

                            Submit Cake Request

                            <i class="fa-solid fa-arrow-right"></i>

                        </button>

                    </div>

                </div>


                <!-- ====================================
                     RIGHT SUMMARY
                ===================================== -->

                <div>

                    <div class="cake-form-card cake-summary-card">

                        <h2>
                            <i class="fa-solid fa-receipt"></i>
                            Cake Summary
                        </h2>


                        <div class="summary-cake">

                            <small>
                                Your Selection
                            </small>

                            <h3 id="summaryFlavour">
                                Vanilla Cake
                            </h3>

                            <p>
                                Customized for your special occasion.
                            </p>

                        </div>


                        <div class="summary-row">

                            <span>
                                Size
                            </span>

                            <span id="summarySize">
                                1 kg
                            </span>

                        </div>


                        <div class="summary-row">

                            <span>
                                Flavour
                            </span>

                            <span id="summaryFlavourRow">
                                Vanilla
                            </span>

                        </div>


                        <div class="summary-row">

                            <span>
                                Design
                            </span>

                            <span id="summaryDesign">
                                Simple
                            </span>

                        </div>


                        <div class="summary-row">

                            <span>
                                Quantity
                            </span>

                            <span id="summaryQuantity">
                                1
                            </span>

                        </div>


                        <div class="summary-row">

                            <span>
                                Event
                            </span>

                            <span id="summaryEvent">
                                Not selected
                            </span>

                        </div>


                        <div class="summary-row">

                            <span>
                                Delivery
                            </span>

                            <span id="summaryDelivery">
                                Customer Collection
                            </span>

                        </div>


                        <div class="price-box">

                            <div class="price-line">

                                <span>
                                    Cake Price
                                </span>

                                <span id="cakePrice">
                                    TZS 50,000
                                </span>

                            </div>


                            <div class="price-line">

                                <span>
                                    Design Fee
                                </span>

                                <span id="designFee">
                                    TZS 0
                                </span>

                            </div>


                            <div class="price-line">

                                <span>
                                    Delivery
                                </span>

                                <span id="deliveryFee">
                                    TZS 0
                                </span>

                            </div>


                            <div class="total-line">

                                <span>
                                    Estimated Total
                                </span>

                                <span id="estimatedTotal">
                                    TZS 50,000
                                </span>

                            </div>

                        </div>


                        <div class="summary-note">

                            <i class="fa-solid fa-circle-info"></i>

                            Cake prices are examples for the website.
                            Final pricing may change depending on flavour,
                            size, filling, decoration, toppers and custom
                            design requirements.

                        </div>

                    </div>

                </div>

            </div>

        </div>

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

                <h4>
                    Quick Links
                </h4>

                <a href="index.php">
                    Home
                </a>

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

                <h4>
                    Our Services
                </h4>

                <a href="venues.php">
                    Venue Selection
                </a>

                <a href="catering.php">
                    Catering
                </a>

                <a href="cakes.php">
                    Cakes
                </a>

                <a href="index.php#services">
                    Decoration
                </a>

                <a href="index.php#services">
                    Photography
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Contact Us
                </h4>

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
                    &copy; 2026 Nuustar Events.
                    All Rights Reserved.
                </p>

            </div>

        </div>

    </footer>


    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>

        /* =========================================
           CAKE PRICING
        ========================================= */

        /*
           Base prices are example prices for development.

           1 kg:
           Vanilla       50,000
           Chocolate     60,000
           Lemon         55,000
           Strawberry    65,000
           Carrot        60,000
           Coconut       55,000
           Red Velvet    70,000
           Mixed         75,000

           Larger sizes are calculated proportionally.
        */

        const flavours = {

            "Vanilla": 50000,

            "Chocolate": 60000,

            "Lemon": 55000,

            "Strawberry": 65000,

            "Carrot": 60000,

            "Coconut": 55000,

            "Red Velvet": 70000,

            "Mixed Flavour": 75000

        };


        /* =========================================
           ELEMENTS
        ========================================= */

        const cakeSize =
            document.getElementById("cakeSize");

        const flavour =
            document.getElementById("flavour");

        const quantity =
            document.getElementById("quantity");

        const delivery =
            document.getElementById("delivery");


        /* =========================================
           CURRENCY
        ========================================= */

        function formatCurrency(amount) {

            return "TZS " +
                Number(amount).toLocaleString("en-TZ");

        }


        /* =========================================
           GET DESIGN
        ========================================= */

        function getSelectedDesign() {

            return document.querySelector(
                'input[name="design"]:checked'
            );

        }


        /* =========================================
           UPDATE SUMMARY
        ========================================= */

        function updateSummary() {

            const size =
                Number(cakeSize.value);

            const selectedFlavour =
                flavour.value;

            const numberOfCakes =
                Number(quantity.value) || 1;

            const selectedDesign =
                getSelectedDesign();


            /* Base flavour price */

            const oneKgPrice =
                flavours[selectedFlavour];


            /*
                Size multiplier.

                This gives an estimated price that
                increases with cake size.
            */

            let sizeMultiplier = 1;


            if (size === 1) {

                sizeMultiplier = 1;

            } else if (size === 1.5) {

                sizeMultiplier = 1.45;

            } else if (size === 2) {

                sizeMultiplier = 1.8;

            } else if (size === 3) {

                sizeMultiplier = 2.5;

            } else if (size === 4) {

                sizeMultiplier = 3.3;

            } else if (size === 5) {

                sizeMultiplier = 4.1;

            }


            const pricePerCake =
                Math.round(
                    oneKgPrice * sizeMultiplier
                );


            const cakeCost =
                pricePerCake * numberOfCakes;


            /* Design */

            const designFeePerCake =
                selectedDesign
                    ? Number(
                        selectedDesign.dataset.price
                    )
                    : 0;


            const designFee =
                designFeePerCake *
                numberOfCakes;


            /* Delivery */

            let deliveryFee = 0;


            if (
                delivery.value ===
                "Nuustar Delivery"
            ) {

                deliveryFee = 10000;

            }


            /* Total */

            const total =
                cakeCost +
                designFee +
                deliveryFee;


            /* =================================
               DISPLAY
            ================================= */

            document.getElementById(
                "summaryFlavour"
            ).textContent =
                selectedFlavour + " Cake";


            document.getElementById(
                "summarySize"
            ).textContent =
                size + " kg";


            document.getElementById(
                "summaryFlavourRow"
            ).textContent =
                selectedFlavour;


            document.getElementById(
                "summaryDesign"
            ).textContent =
                selectedDesign
                    ? selectedDesign.value
                    : "Simple";


            document.getElementById(
                "summaryQuantity"
            ).textContent =
                numberOfCakes;


            document.getElementById(
                "summaryDelivery"
            ).textContent =
                delivery.value;


            document.getElementById(
                "cakePrice"
            ).textContent =
                formatCurrency(cakeCost);


            document.getElementById(
                "designFee"
            ).textContent =
                formatCurrency(designFee);


            document.getElementById(
                "deliveryFee"
            ).textContent =
                formatCurrency(deliveryFee);


            document.getElementById(
                "estimatedTotal"
            ).textContent =
                formatCurrency(total);

        }


        /* =========================================
           EVENTS
        ========================================= */

        cakeSize.addEventListener(
            "change",
            updateSummary
        );


        flavour.addEventListener(
            "change",
            updateSummary
        );


        quantity.addEventListener(
            "input",
            updateSummary
        );


        delivery.addEventListener(
            "change",
            updateSummary
        );


        document
            .querySelectorAll(
                'input[name="design"]'
            )
            .forEach(function(radio) {

                radio.addEventListener(
                    "change",
                    updateSummary
                );

            });


        document.getElementById("eventType")
            .addEventListener(
                "change",
                function() {

                    document.getElementById(
                        "summaryEvent"
                    ).textContent =
                        this.value ||
                        "Not selected";

                }
            );


        /* =========================================
           SUBMIT
        ========================================= */

        function submitCakeOrder() {


            const size =
                Number(cakeSize.value);


            const selectedFlavour =
                flavour.value;


            const numberOfCakes =
                Number(quantity.value);


            const selectedDesign =
                getSelectedDesign();


            const eventType =
                document.getElementById(
                    "eventType"
                ).value;


            const eventDate =
                document.getElementById(
                    "eventDate"
                ).value;


            const eventTime =
                document.getElementById(
                    "eventTime"
                ).value;


            const deliveryMethod =
                delivery.value;


            const location =
                document.getElementById(
                    "location"
                ).value.trim();


            const fullName =
                document.getElementById(
                    "fullName"
                ).value.trim();


            const phone =
                document.getElementById(
                    "phone"
                ).value.trim();


            const whatsapp =
                document.getElementById(
                    "whatsapp"
                ).value.trim();


            const email =
                document.getElementById(
                    "email"
                ).value.trim();


            const cakeMessage =
                document.getElementById(
                    "cakeMessage"
                ).value.trim();


            const agreement =
                document.getElementById(
                    "agreement"
                ).checked;


            /* ==================================
               VALIDATION
            ================================== */

            if (!numberOfCakes ||
                numberOfCakes < 1 ||
                numberOfCakes > 20) {

                alert(
                    "Please enter a valid number of cakes."
                );

                return;
            }


            if (!eventType) {

                alert(
                    "Please select your event type."
                );

                return;
            }


            if (!eventDate) {

                alert(
                    "Please select the event date."
                );

                return;
            }


            if (!location) {

                alert(
                    "Please enter the event or delivery location."
                );

                return;
            }


            if (!fullName) {

                alert(
                    "Please enter your full name."
                );

                return;
            }


            if (!phone) {

                alert(
                    "Please enter your phone number."
                );

                return;
            }


            if (!agreement) {

                alert(
                    "Please accept the agreement before submitting."
                );

                return;
            }


            /* ==================================
               PRICE
            ================================== */

            const oneKgPrice =
                flavours[selectedFlavour];


            let sizeMultiplier = 1;


            if (size === 1) {

                sizeMultiplier = 1;

            } else if (size === 1.5) {

                sizeMultiplier = 1.45;

            } else if (size === 2) {

                sizeMultiplier = 1.8;

            } else if (size === 3) {

                sizeMultiplier = 2.5;

            } else if (size === 4) {

                sizeMultiplier = 3.3;

            } else if (size === 5) {

                sizeMultiplier = 4.1;

            }


            const pricePerCake =
                Math.round(
                    oneKgPrice *
                    sizeMultiplier
                );


            const cakeCost =
                pricePerCake *
                numberOfCakes;


            const designFeePerCake =
                selectedDesign
                    ? Number(
                        selectedDesign.dataset.price
                    )
                    : 0;


            const designFee =
                designFeePerCake *
                numberOfCakes;


            const deliveryFee =
                deliveryMethod ===
                "Nuustar Delivery"
                    ? 10000
                    : 0;


            const estimatedTotal =
                cakeCost +
                designFee +
                deliveryFee;


            /* ==================================
               REQUEST ID
            ================================== */

            const orderId =
                "CAKE-" + Date.now();


            /* ==================================
               SAVE ORDER
            ================================== */

            const cakeOrder = {

                orderId: orderId,

                cake: {

                    size: size,

                    flavour:
                        selectedFlavour,

                    design:
                        selectedDesign
                            ? selectedDesign.value
                            : "Simple",

                    numberOfCakes:
                        numberOfCakes

                },


                event: {

                    type:
                        eventType,

                    date:
                        eventDate,

                    time:
                        eventTime,

                    location:
                        location

                },


                delivery: {

                    method:
                        deliveryMethod,

                    fee:
                        deliveryFee

                },


                pricing: {

                    pricePerCake:
                        pricePerCake,

                    cakeCost:
                        cakeCost,

                    designFee:
                        designFee,

                    deliveryFee:
                        deliveryFee,

                    estimatedTotal:
                        estimatedTotal

                },


                customer: {

                    fullName:
                        fullName,

                    phone:
                        phone,

                    whatsapp:
                        whatsapp,

                    email:
                        email

                },


                specialRequirements:
                    cakeMessage,


                status:
                    "Pending",


                createdAt:
                    new Date().toISOString()

            };


            /* ==================================
               LOCAL STORAGE
            ================================== */

            localStorage.setItem(
                "nuustarCakeOrder",
                JSON.stringify(cakeOrder)
            );


            /* ==================================
               CONFIRMATION
            ================================== */
/* ==================================
   SEND TO PHP
================================== */

const form = document.createElement("form");

form.method = "POST";
form.action = "save-cake-order.php";


function addField(name, value) {

    const input =
        document.createElement("input");

    input.type = "hidden";
    input.name = name;
    input.value = value;

    form.appendChild(input);
}


addField("customer_name", fullName);
addField("phone", phone);
addField("whatsapp", whatsapp);
addField("email", email);

addField("cake_size", size);
addField(
    "flavour",
    selectedFlavour
);

addField(
    "design",
    selectedDesign
        ? selectedDesign.value
        : "Simple"
);

addField(
    "quantity",
    numberOfCakes
);

addField(
    "event_type",
    eventType
);

addField(
    "event_date",
    eventDate
);

addField(
    "event_time",
    eventTime
);

addField(
    "location",
    location
);

addField(
    "delivery_method",
    deliveryMethod
);

addField(
    "delivery_fee",
    deliveryFee
);

addField(
    "price_per_cake",
    pricePerCake
);

addField(
    "cake_cost",
    cakeCost
);

addField(
    "design_fee",
    designFee
);

addField(
    "estimated_total",
    estimatedTotal
);

addField(
    "special_requirements",
    cakeMessage
);


document.body.appendChild(form);

form.submit();

        }


        /* =========================================
           INITIAL DISPLAY
        ========================================= */

        updateSummary();

    </script>

</body>
</html>