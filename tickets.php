
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tickets | Nuustar Events</title>

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
        /* =================================
           TICKETS PAGE
        ================================= */

        .tickets-page {
            background: var(--gray-light);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* Hero */
        .tickets-hero {
            min-height: 420px;
            background:
                linear-gradient(rgba(7, 31, 64, 0.72), rgba(7, 31, 64, 0.72)),
                url("images/tickets.webp") center/cover no-repeat;

            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 20px 70px;
        }

        .tickets-hero-content {
            max-width: 760px;
            color: var(--white);
        }

        .tickets-hero-content h1 {
            font-family: "Playfair Display", serif;
            font-size: 48px;
            margin-bottom: 15px;
        }

        .tickets-hero-content p {
            font-size: 17px;
            line-height: 1.8;
            color: rgba(255,255,255,0.9);
        }

        /* Intro */
        .tickets-intro {
            max-width: 850px;
            margin: 0 auto;
            text-align: center;
            padding: 70px 20px 40px;
        }

        .tickets-intro h2 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            font-size: 34px;
            margin-bottom: 12px;
        }

        .tickets-intro p {
            color: var(--gray);
            line-height: 1.7;
        }

        /* Events */
        .tickets-container {
            max-width: 1150px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .event-ticket-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .event-ticket-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .event-image {
            height: 220px;
            overflow: hidden;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .event-ticket-card:hover .event-image img {
            transform: scale(1.05);
        }

        .event-content {
            padding: 25px;
        }

        .event-content h3 {
            color: var(--blue);
            font-size: 21px;
            margin-bottom: 15px;
        }

        .event-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .event-detail i {
            color: var(--yellow-dark);
            width: 17px;
        }

        .event-description {
            color: var(--gray-dark);
            font-size: 14px;
            line-height: 1.6;
            margin: 15px 0 20px;
        }

        /* Ticket Types */
        .ticket-types {
            border-top: 1px solid var(--border);
            padding-top: 18px;
            margin-top: 10px;
        }

        .ticket-types h4 {
            color: var(--blue);
            font-size: 15px;
            margin-bottom: 12px;
        }

        .ticket-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .ticket-option:last-child {
            border-bottom: none;
        }

        .ticket-option span:first-child {
            font-size: 13px;
            color: var(--gray-dark);
        }

        .ticket-price {
            font-weight: 700;
            color: var(--blue);
            font-size: 14px;
        }

        .get-ticket-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 18px;
            background: var(--yellow);
            color: var(--blue);
            padding: 13px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
        }

        .get-ticket-btn:hover {
            background: var(--yellow-dark);
            transform: translateY(-2px);
        }

        /* Info Section */
        .ticket-info {
            max-width: 1150px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .ticket-info-box {
            background: var(--blue);
            border-radius: var(--radius);
            padding: 35px;
            color: var(--white);
            text-align: center;
        }

        .ticket-info-box h2 {
            font-family: "Playfair Display", serif;
            font-size: 30px;
            margin-bottom: 12px;
        }

        .ticket-info-box p {
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
            color: rgba(255,255,255,0.9);
        }

        /* Responsive */
        @media (max-width: 950px) {
            .events-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .tickets-hero-content h1 {
                font-size: 34px;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }

            .tickets-intro h2 {
                font-size: 28px;
            }

            .ticket-info-box {
                padding: 28px 20px;
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
                <a href="index.php#packages">Packages</a>
                <a href="index.php#services">Services</a>
                <a href="venues.php">Venues</a>
                <a href="tickets.php">Tickets</a>
                <a href="index.php#contact">Contact</a>
                <a href="index.php#packages" class="nav-book-btn">
                    Book Your Event
                </a>
            </div>

        </div>
    </nav>


    <!-- =========================
         HERO
    ========================== -->
    <section class="tickets-hero">

        <div class="tickets-hero-content">

            <h1>Get Your Event Tickets</h1>

            <p>
                Discover upcoming events hosted by Nuustar Events
                and secure your ticket before the event date.
            </p>

        </div>

    </section>


    <!-- =========================
         INTRO
    ========================== -->
    <section class="tickets-intro">

        <h2>Upcoming Events</h2>

        <p>
            Choose an event below to view available ticket types,
            prices and booking options.
        </p>

    </section>


    <!-- =========================
         EVENTS
    ========================== -->
    <section class="tickets-container">

        <div class="events-grid">

            <!-- Event 1 -->
            <div class="event-ticket-card">

                <div class="event-image">
                    <img src="images/event1.webp" alt="Annual Business Networking Event">
                </div>

                <div class="event-content">

                    <h3>Annual Business Networking Event</h3>

                    <div class="event-detail">
                        <i class="fa-solid fa-calendar"></i>
                        <span>20 December 2026</span>
                    </div>

                    <div class="event-detail">
                        <i class="fa-solid fa-clock"></i>
                        <span>5:00 PM</span>
                    </div>

                    <div class="event-detail">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Dar es Salaam</span>
                    </div>

                    <p class="event-description">
                        Connect with entrepreneurs, professionals and
                        business leaders in a memorable networking evening.
                    </p>

                    <div class="ticket-types">

                        <h4>Ticket Types</h4>

                        <div class="ticket-option">
                            <span>Regular</span>
                            <span class="ticket-price">TZS 30,000</span>
                        </div>

                        <div class="ticket-option">
                            <span>VIP</span>
                            <span class="ticket-price">TZS 70,000</span>
                        </div>

                    </div>

                    <a href="ticket-booking.php?event=business-networking"
                       class="get-ticket-btn">
                        Get Ticket
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </div>


            <!-- Event 2 -->
            <div class="event-ticket-card">

                <div class="event-image">
                    <img src="images/event2.webp" alt="Women's Economic Empowerment Event">
                </div>

                <div class="event-content">

                    <h3>Women's Economic Empowerment</h3>

                    <div class="event-detail">
                        <i class="fa-solid fa-calendar"></i>
                        <span>08 March 2027</span>
                    </div>

                    <div class="event-detail">
                        <i class="fa-solid fa-clock"></i>
                        <span>10:00 AM</span>
                    </div>

                    <div class="event-detail">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Dar es Salaam</span>
                    </div>

                    <p class="event-description">
                        An inspiring gathering focused on learning,
                        networking and creating opportunities for women.
                    </p>

                    <div class="ticket-types">

                        <h4>Ticket Types</h4>

                        <div class="ticket-option">
                            <span>Standard</span>
                            <span class="ticket-price">TZS 20,000</span>
                        </div>

                        <div class="ticket-option">
                            <span>VIP</span>
                            <span class="ticket-price">TZS 50,000</span>
                        </div>

                    </div>

                    <a href="ticket-booking.php?event=women-empowerment"
                       class="get-ticket-btn">
                        Get Ticket
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </div>


            <!-- Event 3 -->
            <div class="event-ticket-card">

                <div class="event-image">
                    <img src="images/event3.webp" alt="Corporate Gala Night">
                </div>

                <div class="event-content">

                    <h3>Corporate Gala Night</h3>

                    <div class="event-detail">
                        <i class="fa-solid fa-calendar"></i>
                        <span>15 April 2027</span>
                    </div>

                    <div class="event-detail">
                        <i class="fa-solid fa-clock"></i>
                        <span>7:00 PM</span>
                    </div>

                    <div class="event-detail">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Dar es Salaam</span>
                    </div>

                    <p class="event-description">
                        Enjoy a premium evening of entertainment,
                        networking and celebration.
                    </p>

                    <div class="ticket-types">

                        <h4>Ticket Types</h4>

                        <div class="ticket-option">
                            <span>Regular</span>
                            <span class="ticket-price">TZS 50,000</span>
                        </div>

                        <div class="ticket-option">
                            <span>VIP</span>
                            <span class="ticket-price">TZS 100,000</span>
                        </div>

                    </div>

                    <a href="ticket-booking.php?event=corporate-gala"
                       class="get-ticket-btn">
                        Get Ticket
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         INFO
    ========================== -->
    <section class="ticket-info">

        <div class="ticket-info-box">

            <h2>Simple Ticket Booking</h2>

            <p>
                Select your event, choose the ticket type and enter
                your details. After completing your booking, you will
                receive a ticket confirmation with your booking reference.
            </p>

        </div>

    </section>


    <!-- =========================
         FOOTER
    ========================== -->
    <footer id="contact">

    <div class="footer-container">

        <div class="footer-about">

            <div class="logo">

                <img src="images/logo.png" alt="Nuustar Events" style="width: 50px; height: 50px; border-radius: 80%;">

            </div>

            <p>
                Creating beautiful experiences and capturing
                unforgettable memories.
            </p>

            <div class="socials">

                <a href="#">
                    <i class="fab fa-instagram"></i>
                </a>

                <a href="#">
                    <i class="fab fa-facebook"></i>
                </a>

                <a href="#">
                    <i class="fab fa-tiktok"></i>
                </a>

                <a href="#">
                    <i class="fab fa-whatsapp"></i>
                </a>

            </div>

        </div>


        <div class="footer-links">

            <h3>Quick Links</h3>

            <a href="index.php">Home</a>
            <a href="#packages">Packages</a>
            <a href="#services">Services</a>
            <a href="venues.php">Venues</a>
            <a href="tickets.php">Tickets</a>

        </div>


        <div class="footer-links">

            <h3>Services</h3>

            <a href="venues.php">Venue</a>
            <a href="catering.php">Catering</a>
            <a href="#">Photography</a>
            <a href="#">Videography</a>
            <a href="#">Decoration</a>
            <a href="cakes.php">Cakes</a>

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


    <div class="copyright">

        <p>
            © 2026 Nuustar Events. All Rights Reserved.
        </p>

    </div>

</footer>


</body>
</html>