
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ticket Confirmation | Nuustar Events</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Main CSS -->
    <link rel="stylesheet" href="style.css">

    <style>
        /* =====================================
           TICKET CONFIRMATION
        ===================================== */

        .ticket-confirmation-page {
            min-height: 100vh;
            background: var(--gray-light);
            padding: 120px 20px 70px;
        }

        .ticket-wrapper {
            max-width: 850px;
            margin: 0 auto;
        }

        /* Success heading */
        .ticket-success {
            text-align: center;
            margin-bottom: 30px;
        }

        .success-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: var(--yellow);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .ticket-success h1 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            font-size: 38px;
            margin-bottom: 8px;
        }

        .ticket-success p {
            color: var(--gray);
            line-height: 1.6;
        }

        /* Digital Ticket */
        .ticket-card {
            background: var(--white);
            border-radius: 18px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            position: relative;
        }

        .ticket-top {
            background: var(--blue);
            color: var(--white);
            padding: 28px 35px;
            position: relative;
        }

        .ticket-brand {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .ticket-brand-logo {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .ticket-brand-logo span {
            color: var(--yellow);
        }

        .ticket-label {
            background: var(--yellow);
            color: var(--blue);
            padding: 7px 13px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .event-title-area {
            margin-top: 28px;
        }

        .event-title-area h2 {
            font-family: "Playfair Display", serif;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .event-title-area p {
            color: rgba(255,255,255,0.85);
            font-size: 14px;
        }

        /* Decorative ticket line */
        .ticket-divider {
            position: relative;
            height: 25px;
            background: var(--white);
        }

        .ticket-divider::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: var(--gray-light);
            border-radius: 50%;
        }

        .ticket-divider::after {
            content: "";
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: var(--gray-light);
            border-radius: 50%;
        }

        /* Main ticket body */
        .ticket-body {
            padding: 35px;
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 35px;
        }

        .ticket-information h3 {
            color: var(--blue);
            font-size: 18px;
            margin-bottom: 18px;
        }

        .ticket-detail {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 15px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
        }

        .ticket-detail:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--gray);
            font-size: 13px;
        }

        .detail-value {
            color: var(--black);
            font-size: 14px;
            font-weight: 700;
        }

        /* QR */
        .qr-area {
            text-align: center;
            border-left: 1px solid var(--border);
            padding-left: 30px;
        }

        .qr-area h3 {
            color: var(--blue);
            font-size: 16px;
            margin-bottom: 12px;
        }

        #qrcode {
            width: 180px;
            height: 180px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #qrcode img {
            width: 180px !important;
            height: 180px !important;
        }

        #qrcode canvas {
            width: 180px !important;
            height: 180px !important;
        }

        .qr-ticket-id {
            font-size: 12px;
            color: var(--gray);
            word-break: break-word;
        }

        /* Price section */
        .ticket-price-section {
            margin: 0 35px 30px;
            padding: 20px;
            background: var(--yellow-light);
            border-radius: 10px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }

        .price-row span:first-child {
            color: var(--gray-dark);
        }

        .price-row span:last-child {
            color: var(--blue);
            font-weight: 700;
        }

        .price-total {
            border-top: 1px solid rgba(11,45,92,0.15);
            margin-top: 10px;
            padding-top: 14px;
            display: flex;
            justify-content: space-between;
            color: var(--blue);
            font-size: 21px;
            font-weight: 800;
        }

        /* Footer inside ticket */
        .ticket-footer {
            background: var(--gray-light);
            padding: 20px 35px;
            text-align: center;
        }

        .ticket-footer p {
            margin: 4px 0;
            font-size: 12px;
            color: var(--gray);
        }

        .ticket-footer strong {
            color: var(--blue);
        }

        /* Actions */
        .ticket-actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .ticket-action-btn {
            border: none;
            text-decoration: none;
            cursor: pointer;
            padding: 13px 20px;
            border-radius: 8px;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-primary-ticket {
            background: var(--blue);
            color: var(--white);
        }

        .btn-primary-ticket:hover {
            background: var(--blue-dark);
            transform: translateY(-2px);
        }

        .btn-yellow-ticket {
            background: var(--yellow);
            color: var(--blue);
        }

        .btn-yellow-ticket:hover {
            background: var(--yellow-dark);
            transform: translateY(-2px);
        }

        .btn-outline-ticket {
            background: var(--white);
            color: var(--blue);
            border: 1px solid var(--blue);
        }

        .btn-outline-ticket:hover {
            background: var(--blue-light);
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
        }

        .btn-whatsapp:hover {
            background: #1da851;
            transform: translateY(-2px);
        }

        /* Notice */
        .ticket-notice {
            background: var(--blue-light);
            margin-top: 25px;
            padding: 18px 20px;
            border-radius: 10px;
            text-align: center;
            color: var(--gray-dark);
            font-size: 13px;
            line-height: 1.6;
        }

        /* No ticket */
        .no-ticket {
            display: none;
            background: var(--white);
            text-align: center;
            padding: 55px 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
        }

        .no-ticket i {
            font-size: 50px;
            color: var(--yellow-dark);
            margin-bottom: 20px;
        }

        .no-ticket h2 {
            color: var(--blue);
            margin-bottom: 10px;
        }

        .no-ticket p {
            color: var(--gray);
            margin-bottom: 25px;
        }

        /* Responsive */
        @media (max-width: 700px) {

            .ticket-confirmation-page {
                padding: 100px 15px 50px;
            }

            .ticket-success h1 {
                font-size: 30px;
            }

            .ticket-top {
                padding: 25px 20px;
            }

            .ticket-body {
                grid-template-columns: 1fr;
                padding: 25px 20px;
            }

            .qr-area {
                border-left: none;
                border-top: 1px solid var(--border);
                padding-left: 0;
                padding-top: 25px;
            }

            .ticket-price-section {
                margin: 0 20px 25px;
            }

            .ticket-footer {
                padding: 18px 20px;
            }

            .ticket-detail {
                grid-template-columns: 120px 1fr;
            }

            .ticket-brand {
                align-items: flex-start;
            }

            .event-title-area h2 {
                font-size: 25px;
            }
        }

        /* =====================================
           PRINT
        ===================================== */

        @media print {

            body {
                background: white;
            }

            .navbar,
            .footer,
            .ticket-success,
            .ticket-actions,
            .ticket-notice {
                display: none !important;
            }

            .ticket-confirmation-page {
                padding: 10px;
                background: white;
                min-height: auto;
            }

            .ticket-wrapper {
                max-width: 100%;
            }

            .ticket-card {
                box-shadow: none;
                border: 1px solid #ddd;
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
         PAGE
    ========================== -->

    <main class="ticket-confirmation-page">

        <div class="ticket-wrapper">

            <!-- Success -->
            <div class="ticket-success">

                <div class="success-icon">
                    <i class="fa-solid fa-check"></i>
                </div>

                <h1>Ticket Created Successfully</h1>

                <p>
                    Your Nuustar Events digital ticket is ready.
                    Keep the QR code available when attending the event.
                </p>

            </div>


            <!-- =========================
                 DIGITAL TICKET
            ========================== -->

            <div class="ticket-card" id="printableTicket">

                <!-- Ticket Top -->
                <div class="ticket-top">

                    <div class="ticket-brand">

                        <div class="ticket-brand-logo">
                            <span>NUU</span>STAR
                            EVENTS
                        </div>

                        <div class="ticket-label">
                            DIGITAL TICKET
                        </div>

                    </div>


                    <div class="event-title-area">

                        <h2 id="eventName">
                            Event Name
                        </h2>

                        <p>
                            We look forward to celebrating with you.
                        </p>

                    </div>

                </div>


                <!-- Divider -->
                <div class="ticket-divider"></div>


                <!-- Ticket Body -->
                <div class="ticket-body">

                    <!-- Information -->
                    <div class="ticket-information">

                        <h3>
                            <i class="fa-solid fa-circle-info"></i>
                            Ticket Information
                        </h3>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Ticket ID
                            </span>

                            <span class="detail-value"
                                  id="ticketId">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Customer
                            </span>

                            <span class="detail-value"
                                  id="customerName">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Event Date
                            </span>

                            <span class="detail-value"
                                  id="eventDate">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Event Time
                            </span>

                            <span class="detail-value"
                                  id="eventTime">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Location
                            </span>

                            <span class="detail-value"
                                  id="eventLocation">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Ticket Type
                            </span>

                            <span class="detail-value"
                                  id="ticketType">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Quantity
                            </span>

                            <span class="detail-value"
                                  id="ticketQuantity">
                                ---
                            </span>

                        </div>


                        <div class="ticket-detail">

                            <span class="detail-label">
                                Status
                            </span>

                            <span class="detail-value"
                                  id="ticketStatus">
                                PENDING
                            </span>

                        </div>

                    </div>


                    <!-- QR Code -->
                    <div class="qr-area">

                        <h3>Scan to Verify</h3>

                        <div id="qrcode"></div>

                        <div class="qr-ticket-id">
                            <strong id="qrTicketId">
                                ---
                            </strong>
                        </div>

                    </div>

                </div>


                <!-- Price -->
                <div class="ticket-price-section">

                    <div class="price-row">

                        <span>Price per Ticket</span>

                        <span id="pricePerTicket">
                            TZS 0
                        </span>

                    </div>


                    <div class="price-row">

                        <span>Quantity</span>

                        <span id="priceQuantity">
                            1
                        </span>

                    </div>


                    <div class="price-row">

                        <span>Service Fee</span>

                        <span id="serviceFee">
                            TZS 0
                        </span>

                    </div>


                    <div class="price-total">

                        <span>Total</span>

                        <span id="totalPrice">
                            TZS 0
                        </span>

                    </div>

                </div>


                <!-- Ticket Footer -->
                <div class="ticket-footer">

                    <p>
                        Present this ticket and its QR code at the event entrance.
                    </p>

                    <p>
                        <strong>Nuustar Events</strong>
                        | 0624 947 668
                        | www.nuustar.com
                    </p>

                </div>

            </div>


            <!-- =========================
                 ACTION BUTTONS
            ========================== -->

            <div class="ticket-actions">

                <button
                    type="button"
                    onclick="window.print()"
                    class="ticket-action-btn btn-primary-ticket">

                    <i class="fa-solid fa-print"></i>

                    Print Ticket

                </button>


                <button
                    type="button"
                    onclick="downloadTicket()"
                    class="ticket-action-btn btn-yellow-ticket">

                    <i class="fa-solid fa-download"></i>

                    Download Ticket

                </button>


                <button
                    type="button"
                    onclick="sendWhatsApp()"
                    class="ticket-action-btn btn-whatsapp">

                    <i class="fa-brands fa-whatsapp"></i>

                    Send via WhatsApp

                </button>


                <a
                    href="tickets.php"
                    class="ticket-action-btn btn-outline-ticket">

                    <i class="fa-solid fa-ticket"></i>

                    More Events

                </a>

            </div>


            <!-- Notice -->
            <div class="ticket-notice">

                <strong>
                    Important:
                </strong>

                This is currently a frontend demonstration.
                In the PHP + MySQL version, the ticket ID will be stored
                permanently, the QR code will be verified against the
                database, and ticket status will be changed from
                <strong>Pending</strong> to <strong>Used</strong> after
                successful entrance scanning.

            </div>

        </div>


        <!-- No Ticket -->
        <div class="ticket-wrapper no-ticket" id="noTicket">

            <i class="fa-solid fa-ticket"></i>

            <h2>
                No Ticket Found
            </h2>

            <p>
                We could not find ticket information on this device.
                Please return to the tickets page and make a booking.
            </p>

            <a
                href="tickets.php"
                class="ticket-action-btn btn-primary-ticket">

                <i class="fa-solid fa-arrow-left"></i>

                Browse Tickets

            </a>

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

                <a href="index.php#services">
                    Venue Selection
                </a>

                <a href="index.php#services">
                    Decoration
                </a>

                <a href="index.php#services">
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


    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>

        // ==========================================
        // GET SAVED TICKET
        // ==========================================

        const savedTicket =
            localStorage.getItem("nuustarTicket");


        const ticketWrapper =
            document.querySelector(".ticket-wrapper");


        const noTicket =
            document.getElementById("noTicket");


        // ==========================================
        // FORMAT MONEY
        // ==========================================

        function formatCurrency(amount) {

            return "TZS " +
                Number(amount).toLocaleString("en-TZ");

        }


        // ==========================================
        // LOAD TICKET
        // ==========================================

        if (!savedTicket) {

            ticketWrapper.style.display = "none";

            noTicket.style.display = "block";

        } else {

            let ticketData;

            try {

                ticketData =
                    JSON.parse(savedTicket);

            } catch (error) {

                console.error(
                    "Could not read ticket:",
                    error
                );

                ticketWrapper.style.display = "none";

                noTicket.style.display = "block";

                ticketData = null;

            }


            if (ticketData) {

                const event =
                    ticketData.event || {};

                const customer =
                    ticketData.customer || {};

                const ticket =
                    ticketData.ticket || {};


                // ==========================================
                // EVENT INFORMATION
                // ==========================================

                document.getElementById(
                    "eventName"
                ).textContent =
                    event.name || "Nuustar Event";


                document.getElementById(
                    "eventDate"
                ).textContent =
                    event.date || "---";


                document.getElementById(
                    "eventTime"
                ).textContent =
                    event.time || "---";


                document.getElementById(
                    "eventLocation"
                ).textContent =
                    event.location || "---";


                // ==========================================
                // CUSTOMER INFORMATION
                // ==========================================

                document.getElementById(
                    "customerName"
                ).textContent =
                    customer.fullName || "---";


                // ==========================================
                // TICKET INFORMATION
                // ==========================================

                document.getElementById(
                    "ticketId"
                ).textContent =
                    ticketData.ticketId || "---";


                document.getElementById(
                    "qrTicketId"
                ).textContent =
                    ticketData.ticketId || "---";


                document.getElementById(
                    "ticketType"
                ).textContent =
                    ticket.type || "---";


                document.getElementById(
                    "ticketQuantity"
                ).textContent =
                    ticket.quantity || 1;


                document.getElementById(
                    "ticketStatus"
                ).textContent =
                    ticketData.status || "Pending";


                // ==========================================
                // PRICE
                // ==========================================

                document.getElementById(
                    "pricePerTicket"
                ).textContent =
                    formatCurrency(ticket.price || 0);


                document.getElementById(
                    "priceQuantity"
                ).textContent =
                    ticket.quantity || 1;


                document.getElementById(
                    "serviceFee"
                ).textContent =
                    formatCurrency(ticket.serviceFee || 0);


                document.getElementById(
                    "totalPrice"
                ).textContent =
                    formatCurrency(ticket.total || 0);


                // ==========================================
                // GENERATE QR CODE
                // ==========================================

                const qrContainer =
                    document.getElementById("qrcode");

                qrContainer.innerHTML = "";


                const qrData = JSON.stringify({

                    ticketId:
                        ticketData.ticketId,

                    event:
                        event.code,

                    type:
                        ticket.type

                });


                new QRCode(qrContainer, {

                    text: qrData,

                    width: 180,

                    height: 180,

                    correctLevel:
                        QRCode.CorrectLevel.H

                });

            }

        }


        // ==========================================
        // WHATSAPP
        // ==========================================

        function sendWhatsApp() {

            if (!savedTicket) {

                alert("Ticket information was not found.");

                return;

            }


            const ticketData =
                JSON.parse(savedTicket);


            const event =
                ticketData.event || {};

            const customer =
                ticketData.customer || {};

            const ticket =
                ticketData.ticket || {};


            const message =
`Hello Nuustar Events,

My event ticket has been created successfully.

Ticket ID: ${ticketData.ticketId}

Event: ${event.name}
Date: ${event.date}
Time: ${event.time}
Location: ${event.location}

Customer: ${customer.fullName}
Ticket Type: ${ticket.type}
Quantity: ${ticket.quantity}

Total: ${formatCurrency(ticket.total)}

Please keep this ticket reference for verification.

Thank you,
${customer.fullName}`;


            /*
                WhatsApp number supplied by the customer.

                If no WhatsApp number was entered,
                we open WhatsApp without a specific recipient.
            */

            let whatsappNumber =
                customer.whatsapp ||
                customer.phone ||
                "";


            // Remove spaces, + and other characters
            whatsappNumber =
                whatsappNumber.replace(/\D/g, "");


            /*
                Tanzania numbers often begin with 0.
                WhatsApp international format requires 255.
            */

            if (
                whatsappNumber.startsWith("0") &&
                whatsappNumber.length === 10
            ) {

                whatsappNumber =
                    "255" +
                    whatsappNumber.substring(1);

            }


            let whatsappURL;


            if (whatsappNumber) {

                whatsappURL =
                    "https://wa.me/" +
                    whatsappNumber +
                    "?text=" +
                    encodeURIComponent(message);

            } else {

                whatsappURL =
                    "https://wa.me/?text=" +
                    encodeURIComponent(message);

            }


            window.open(
                whatsappURL,
                "_blank"
            );

        }


        // ==========================================
        // DOWNLOAD
        // ==========================================

        function downloadTicket() {

            alert(
                "For now, use 'Print Ticket' and choose 'Save as PDF'. " +
                "We will add direct PDF ticket generation with PHP in the next stage."
            );

            window.print();

        }

    </script>

</body>
</html>