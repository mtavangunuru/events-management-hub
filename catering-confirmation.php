
<?php
/*
|--------------------------------------------------------------------------
| Nuustar Events - Catering Confirmation
|--------------------------------------------------------------------------
| This page reads the catering request saved by
| catering-bookingform.php in localStorage.
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catering Confirmation | Nuustar Events</title>

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
        .confirmation-page {
            min-height: 100vh;
            background: var(--gray-light);
            padding: 120px 20px 70px;
        }

        .confirmation-container {
            max-width: 1050px;
            margin: 0 auto;
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .success-icon {
            width: 82px;
            height: 82px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: var(--yellow);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: var(--shadow-sm);
        }

        .confirmation-header h1 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            font-size: 43px;
            margin-bottom: 10px;
        }

        .confirmation-header p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .reference-box {
            background: var(--blue);
            color: var(--white);
            border-radius: var(--radius);
            padding: 25px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .reference-box .label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-bottom: 6px;
        }

        .reference-box strong {
            font-size: 24px;
            letter-spacing: 1px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--yellow);
            color: var(--blue);
            padding: 9px 15px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .confirmation-grid {
            display: grid;
            grid-template-columns: 1.3fr 0.7fr;
            gap: 25px;
            align-items: start;
        }

        .confirmation-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
        }

        .confirmation-card h2 {
            color: var(--blue);
            font-size: 20px;
            margin: 0 0 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--yellow);
        }

        .confirmation-card h2 i {
            color: var(--yellow-dark);
            margin-right: 7px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-item {
            background: var(--gray-light);
            border-radius: 9px;
            padding: 14px;
        }

        .detail-item.full {
            grid-column: 1 / -1;
        }

        .detail-label {
            display: block;
            color: var(--gray);
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .detail-value {
            color: var(--blue);
            font-size: 14px;
            font-weight: 700;
            word-break: break-word;
        }

        .package-box {
            background: var(--blue);
            color: var(--white);
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 20px;
        }

        .package-box .small-label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            margin-bottom: 5px;
        }

        .package-box h3 {
            font-family: "Playfair Display", serif;
            font-size: 25px;
            margin-bottom: 6px;
        }

        .package-box p {
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        .price-box {
            background: var(--yellow-light);
            border-radius: 10px;
            padding: 18px;
        }

        .price-row,
        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 8px 0;
        }

        .price-row {
            color: var(--gray-dark);
            font-size: 13px;
        }

        .price-row span:last-child {
            color: var(--blue);
            font-weight: 700;
            text-align: right;
        }

        .total-row {
            border-top: 1px solid rgba(11,45,92,0.15);
            margin-top: 8px;
            padding-top: 14px;
            color: var(--blue);
            font-size: 19px;
            font-weight: 800;
        }

        .addon-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .addon-list li {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        .addon-list li:last-child {
            border-bottom: none;
        }

        .addon-list .addon-name {
            color: var(--gray-dark);
        }

        .addon-list .addon-price {
            color: var(--blue);
            font-weight: 700;
            white-space: nowrap;
        }

        .no-addons {
            color: var(--gray);
            font-size: 13px;
        }

        .notice {
            background: var(--blue-light);
            border-radius: 9px;
            padding: 16px;
            color: var(--gray-dark);
            font-size: 12px;
            line-height: 1.7;
        }

        .notice i {
            color: var(--yellow-dark);
            margin-right: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .action-btn {
            flex: 1;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 13px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-family: "Montserrat", sans-serif;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            border: none;
        }

        .primary-btn {
            background: var(--yellow);
            color: var(--blue);
        }

        .secondary-btn {
            background: var(--blue);
            color: var(--white);
        }

        .empty-state {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 45px 30px;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .empty-state i {
            color: var(--yellow-dark);
            font-size: 45px;
            margin-bottom: 18px;
        }

        .empty-state h2 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--gray);
            line-height: 1.7;
        }

        @media print {
            .navbar,
            .footer,
            .action-buttons {
                display: none !important;
            }

            .confirmation-page {
                padding: 20px;
                background: white;
            }

            .confirmation-card,
            .reference-box {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }

        @media (max-width: 800px) {
            .confirmation-grid {
                grid-template-columns: 1fr;
            }

            .reference-box {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 600px) {
            .confirmation-page {
                padding: 100px 15px 50px;
            }

            .confirmation-header h1 {
                font-size: 32px;
            }

            .confirmation-card {
                padding: 22px;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .detail-item.full {
                grid-column: auto;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container nav-container">

            <a href="index.php" class="logo">
                <span>NUU</span>STAR
                <small>EVENTS</small>
            </a>

            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="index.php#packages">Packages</a>
                <a href="index.php#services">Services</a>
                <a href="venues.php">Venues</a>
                <a href="tickets.php">Tickets</a>
                <a href="index.php#contact">Contact</a>
                <a href="index.php#packages" class="nav-book-btn">Book Your Event</a>
            </div>

        </div>
    </nav>

    <main class="confirmation-page">

        <div class="confirmation-container">

            <div id="confirmationContent">

                <div class="confirmation-header">
                    <div class="success-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <h1>Catering Request Received</h1>

                    <p>
                        Thank you for choosing Nuustar Events.
                        Your catering request has been received successfully.
                        We will review your requirements and contact you to confirm
                        availability, menu and final pricing.
                    </p>
                </div>

                <div class="reference-box">
                    <div>
                        <span class="label">CATERING REQUEST REFERENCE</span>
                        <strong id="requestId">—</strong>
                    </div>

                    <div class="status-badge">
                        <i class="fa-solid fa-clock"></i>
                        Pending Confirmation
                    </div>
                </div>

                <div class="confirmation-grid">

                    <!-- LEFT -->
                    <div>

                        <div class="confirmation-card">
                            <h2>
                                <i class="fa-solid fa-calendar-days"></i>
                                Event Details
                            </h2>

                            <div class="details-grid">

                                <div class="detail-item">
                                    <span class="detail-label">Event Type</span>
                                    <span class="detail-value" id="eventType">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Number of Guests</span>
                                    <span class="detail-value" id="guests">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Event Date</span>
                                    <span class="detail-value" id="eventDate">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Event Time</span>
                                    <span class="detail-value" id="eventTime">—</span>
                                </div>

                                <div class="detail-item full">
                                    <span class="detail-label">Event Location</span>
                                    <span class="detail-value" id="location">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Service Style</span>
                                    <span class="detail-value" id="serviceStyle">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Food Preference</span>
                                    <span class="detail-value" id="foodPreference">—</span>
                                </div>

                                <div class="detail-item full">
                                    <span class="detail-label">Estimated Budget</span>
                                    <span class="detail-value" id="budget">—</span>
                                </div>

                            </div>
                        </div>

                        <div class="confirmation-card">
                            <h2>
                                <i class="fa-solid fa-user"></i>
                                Customer Information
                            </h2>

                            <div class="details-grid">

                                <div class="detail-item">
                                    <span class="detail-label">Full Name</span>
                                    <span class="detail-value" id="fullName">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Phone Number</span>
                                    <span class="detail-value" id="phone">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">WhatsApp</span>
                                    <span class="detail-value" id="whatsapp">—</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-label">Email</span>
                                    <span class="detail-value" id="email">—</span>
                                </div>

                                <div class="detail-item full">
                                    <span class="detail-label">Special Requirements / Menu Requests</span>
                                    <span class="detail-value" id="notes">—</span>
                                </div>

                            </div>
                        </div>

                        <div class="confirmation-card">
                            <h2>
                                <i class="fa-solid fa-circle-info"></i>
                                What Happens Next?
                            </h2>

                            <div class="notice">
                                <i class="fa-solid fa-check-circle"></i>
                                Your request is currently <strong>Pending Confirmation</strong>.
                                Nuustar Events will review your guest count, event date,
                                menu preferences, selected services and other requirements.
                                We will then contact you using the phone, WhatsApp or email
                                provided to confirm availability and the final price.
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div>

                        <div class="confirmation-card">

                            <h2>
                                <i class="fa-solid fa-utensils"></i>
                                Catering Summary
                            </h2>

                            <div class="package-box">
                                <span class="small-label">SELECTED CATERING PACKAGE</span>
                                <h3 id="packageName">—</h3>
                                <p id="packageDescription">—</p>
                            </div>

                            <div class="price-box">

                                <div class="price-row">
                                    <span>Price / Person</span>
                                    <span id="pricePerPerson">—</span>
                                </div>

                                <div class="price-row">
                                    <span>Guests</span>
                                    <span id="summaryGuests">—</span>
                                </div>

                                <div class="price-row">
                                    <span>Catering</span>
                                    <span id="foodCost">—</span>
                                </div>

                                <div class="price-row">
                                    <span>Additional Services</span>
                                    <span id="addonsTotal">—</span>
                                </div>

                                <div class="total-row">
                                    <span>Estimated Total</span>
                                    <span id="estimatedTotal">—</span>
                                </div>

                            </div>

                        </div>

                        <div class="confirmation-card">

                            <h2>
                                <i class="fa-solid fa-plus"></i>
                                Additional Services
                            </h2>

                            <ul class="addon-list" id="addonList">
                                <li class="no-addons">
                                    No additional services selected.
                                </li>
                            </ul>

                        </div>

                        <div class="action-buttons">
                            <a href="catering.php" class="action-btn secondary-btn">
                                <i class="fa-solid fa-arrow-left"></i>
                                Catering Page
                            </a>

                            <button type="button"
                                    class="action-btn primary-btn"
                                    onclick="window.print()">
                                <i class="fa-solid fa-print"></i>
                                Print Confirmation
                            </button>
                        </div>

                    </div>

                </div>

            </div>

            <!-- EMPTY STATE -->
            <div id="emptyState" class="empty-state" style="display:none;">

                <i class="fa-solid fa-file-circle-question"></i>

                <h2>Booking Information Not Found</h2>

                <p>
                    We could not find a catering request in this browser.
                    Please return to the catering booking form and submit
                    your request again.
                </p>

                <div class="action-buttons">
                    <a href="catering-bookingform.php"
                       class="action-btn primary-btn">
                        <i class="fa-solid fa-utensils"></i>
                        Go to Catering Form
                    </a>

                    <a href="catering.php"
                       class="action-btn secondary-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to Catering
                    </a>
                </div>

            </div>

        </div>

    </main>

    <!-- FOOTER -->
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
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                </div>

            </div>

            <div class="footer-column">
                <h4>Quick Links</h4>
                <a href="index.php">Home</a>
                <a href="index.php#packages">Packages</a>
                <a href="index.php#services">Services</a>
                <a href="venues.php">Venues</a>
                <a href="tickets.php">Tickets</a>
            </div>

            <div class="footer-column">
                <h4>Our Services</h4>
                <a href="venues.php">Venue Selection</a>
                <a href="catering.php">Catering</a>
                <a href="index.php#services">Decoration</a>
                <a href="index.php#services">Photography</a>
                <a href="index.php#services">Videography</a>
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
                <p>&copy; 2026 Nuustar Events. All Rights Reserved.</p>
            </div>
        </div>

    </footer>

    <script>
        /*
        |--------------------------------------------------------------------------
        | LOAD CATERING REQUEST
        |--------------------------------------------------------------------------
        */

        const storedRequest =
            localStorage.getItem("nuustarCateringRequest");

        const confirmationContent =
            document.getElementById("confirmationContent");

        const emptyState =
            document.getElementById("emptyState");


        /*
        |--------------------------------------------------------------------------
        | HELPERS
        |--------------------------------------------------------------------------
        */

        function formatCurrency(amount) {

            return "TZS " +
                Number(amount || 0).toLocaleString("en-TZ");

        }


        function formatDate(dateValue) {

            if (!dateValue) {
                return "Not specified";
            }

            const date =
                new Date(dateValue + "T00:00:00");

            if (isNaN(date.getTime())) {
                return dateValue;
            }

            return date.toLocaleDateString(
                "en-GB",
                {
                    day: "2-digit",
                    month: "long",
                    year: "numeric"
                }
            );

        }


        function displayValue(value) {

            return value !== undefined &&
                   value !== null &&
                   String(value).trim() !== ""
                ? value
                : "Not specified";

        }


        /*
        |--------------------------------------------------------------------------
        | DISPLAY REQUEST
        |--------------------------------------------------------------------------
        */

        if (!storedRequest) {

            confirmationContent.style.display = "none";
            emptyState.style.display = "block";

        } else {

            try {

                const request =
                    JSON.parse(storedRequest);


                /* REQUEST */

                document.getElementById("requestId")
                    .textContent =
                    displayValue(request.requestId);


                /* PACKAGE */

                const packageData =
                    request.package || {};

                document.getElementById("packageName")
                    .textContent =
                    displayValue(packageData.name);

                document.getElementById("packageDescription")
                    .textContent =
                    displayValue(packageData.description);

                document.getElementById("pricePerPerson")
                    .textContent =
                    packageData.price > 0
                        ? formatCurrency(packageData.price)
                        : "Custom";


                /* EVENT */

                const event =
                    request.event || {};

                document.getElementById("eventType")
                    .textContent =
                    displayValue(event.type);

                document.getElementById("guests")
                    .textContent =
                    event.guests
                        ? Number(event.guests).toLocaleString("en-TZ")
                        : "Not specified";

                document.getElementById("summaryGuests")
                    .textContent =
                    event.guests
                        ? Number(event.guests).toLocaleString("en-TZ")
                        : "0";

                document.getElementById("eventDate")
                    .textContent =
                    formatDate(event.date);

                document.getElementById("eventTime")
                    .textContent =
                    displayValue(event.time);

                document.getElementById("location")
                    .textContent =
                    displayValue(event.location);

                document.getElementById("serviceStyle")
                    .textContent =
                    displayValue(event.serviceStyle);

                document.getElementById("foodPreference")
                    .textContent =
                    displayValue(event.foodPreference);

                document.getElementById("budget")
                    .textContent =
                    displayValue(event.budget);


                /* CUSTOMER */

                const customer =
                    request.customer || {};

                document.getElementById("fullName")
                    .textContent =
                    displayValue(customer.fullName);

                document.getElementById("phone")
                    .textContent =
                    displayValue(customer.phone);

                document.getElementById("whatsapp")
                    .textContent =
                    displayValue(customer.whatsapp);

                document.getElementById("email")
                    .textContent =
                    displayValue(customer.email);

                document.getElementById("notes")
                    .textContent =
                    displayValue(request.notes);


                /* PRICING */

                const pricing =
                    request.pricing || {};

                document.getElementById("foodCost")
                    .textContent =
                    formatCurrency(pricing.foodCost);

                document.getElementById("addonsTotal")
                    .textContent =
                    formatCurrency(pricing.addonsTotal);

                document.getElementById("estimatedTotal")
                    .textContent =
                    formatCurrency(pricing.estimatedTotal);


                /* ADDITIONAL SERVICES */

                const addonList =
                    document.getElementById("addonList");

                const addons =
                    Array.isArray(request.additionalServices)
                        ? request.additionalServices
                        : [];

                if (addons.length > 0) {

                    addonList.innerHTML = "";

                    addons.forEach(function(addon) {

                        const li =
                            document.createElement("li");

                        const name =
                            document.createElement("span");

                        name.className = "addon-name";
                        name.textContent =
                            displayValue(addon.name);

                        const price =
                            document.createElement("span");

                        price.className = "addon-price";
                        price.textContent =
                            formatCurrency(addon.price);

                        li.appendChild(name);
                        li.appendChild(price);

                        addonList.appendChild(li);

                    });

                }


            } catch (error) {

                console.error(
                    "Unable to read catering request:",
                    error
                );

                confirmationContent.style.display = "none";
                emptyState.style.display = "block";

            }

        }
    </script>

</body>
</html>
