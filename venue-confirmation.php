
<?php

require_once "connection.php";

$bookingId = $_GET["booking_id"] ?? "";

if ($bookingId === "") {
    die("Booking reference not found.");
}

$sql = "
    SELECT *
    FROM venue_bookings
    WHERE booking_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $bookingId);

$stmt->execute();

$result = $stmt->get_result();

$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking was not found.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Venue Booking Confirmation | Nuustar Events</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="style.css">

    <style>

        .confirmation-page {
            min-height: 100vh;
            background: var(--gray-light);
            padding: 120px 20px 70px;
        }

        .confirmation-container {
            max-width: 900px;
            margin: auto;
        }

        .success-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .success-icon {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: var(--yellow);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 30px;
        }

        .success-header h1 {
            font-family: "Playfair Display", serif;
            color: var(--blue);
            font-size: 40px;
            margin-bottom: 10px;
        }

        .success-header p {
            color: var(--gray);
            line-height: 1.7;
        }

        .reference {
            display: inline-block;
            margin-top: 18px;
            background: var(--blue-light);
            padding: 14px 22px;
            border-radius: 10px;
        }

        .reference span {
            display: block;
            font-size: 12px;
            color: var(--gray);
        }

        .reference strong {
            color: var(--blue);
            font-size: 20px;
        }

        .confirmation-card {
            background: var(--white);
            padding: 30px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 25px;
        }

        .confirmation-card h2 {
            color: var(--blue);
            font-size: 21px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--yellow);
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .detail-box {
            border: 1px solid var(--border);
            padding: 20px;
            border-radius: 10px;
        }

        .detail-box h3 {
            color: var(--blue);
            font-size: 17px;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 9px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row span:first-child {
            color: var(--gray);
        }

        .detail-row span:last-child {
            color: var(--blue);
            font-weight: 700;
            text-align: right;
        }

        .status {
            margin-top: 20px;
            padding: 16px;
            background: var(--blue-light);
            border-radius: 9px;
            color: var(--gray-dark);
            font-size: 13px;
            line-height: 1.7;
        }

        .status strong {
            color: var(--blue);
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .action-btn {
            text-decoration: none;
            padding: 13px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .home-btn {
            background: var(--blue);
            color: var(--white);
        }

        .venue-btn {
            background: var(--yellow);
            color: var(--blue);
        }

        @media (max-width: 700px) {

            .details-grid {
                grid-template-columns: 1fr;
            }

            .success-header h1 {
                font-size: 31px;
            }

            .detail-row {
                flex-direction: column;
                gap: 4px;
            }

            .detail-row span:last-child {
                text-align: left;
            }

        }

    </style>

</head>


<body>

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

            </div>

        </div>

    </nav>


    <main class="confirmation-page">

        <div class="confirmation-container">


            <div class="success-header">

                <div class="success-icon">
                    <i class="fa-solid fa-check"></i>
                </div>

                <h1>
                    Venue Request Received!
                </h1>

                <p>
                    Thank you for choosing Nuustar Events.
                    Your venue booking request has been received successfully.
                </p>

                <div class="reference">

                    <span>
                        Booking Reference
                    </span>

                    <strong>
                        <?php echo htmlspecialchars($booking["booking_id"]); ?>
                    </strong>

                </div>

            </div>


            <div class="confirmation-card">

                <h2>
                    <i class="fa-solid fa-calendar-check"></i>
                    Venue Booking Details
                </h2>


                <div class="details-grid">


                    <div class="detail-box">

                        <h3>
                            Customer Information
                        </h3>

                        <div class="detail-row">
                            <span>Full Name</span>
                            <span>
                                <?php echo htmlspecialchars($booking["customer_name"]); ?>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Phone</span>
                            <span>
                                <?php echo htmlspecialchars($booking["phone"]); ?>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Email</span>
                            <span>
                                <?php echo htmlspecialchars($booking["email"] ?: "Not provided"); ?>
                            </span>
                        </div>

                    </div>


                    <div class="detail-box">

                        <h3>
                            Event Information
                        </h3>

                        <div class="detail-row">
                            <span>Event Type</span>
                            <span>
                                <?php echo htmlspecialchars($booking["event_type"]); ?>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Event Date</span>
                            <span>
                                <?php echo htmlspecialchars($booking["event_date"] ?: "Not provided"); ?>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Guests</span>
                            <span>
                                <?php echo htmlspecialchars($booking["guests"]); ?>
                            </span>
                        </div>

                    </div>


                    <div class="detail-box">

                        <h3>
                            Venue Preferences
                        </h3>

                        <div class="detail-row">
                            <span>Venue Type</span>
                            <span>
                                <?php echo htmlspecialchars($booking["venue_type"]); ?>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Preferred Location</span>
                            <span>
                                <?php echo htmlspecialchars($booking["location"]); ?>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span>Budget</span>
                            <span>
                                TZS <?php echo number_format($booking["budget"]); ?>
                            </span>
                        </div>

                    </div>


                    <div class="detail-box">

                        <h3>
                            Additional Information
                        </h3>

                        <div class="detail-row">
                            <span>Request</span>
                            <span>
                                <?php echo htmlspecialchars($booking["message"] ?: "No additional information"); ?>
                            </span>
                        </div>

                    </div>

                </div>


                <div class="status">

                    <i class="fa-solid fa-circle-info"></i>

                    Your request is currently:

                    <strong>
                        <?php echo htmlspecialchars($booking["status"]); ?>
                    </strong>

                    <br>

                    This is a venue request and not yet a final confirmed
                    reservation. Nuustar Events will contact you to confirm
                    venue availability, pricing and the final booking.

                </div>

            </div>


            <div class="actions">

                <a
                    href="venues.php"
                    class="action-btn venue-btn"
                >
                    <i class="fa-solid fa-building"></i>
                    Browse Venues
                </a>


                <a
                    href="index.php"
                    class="action-btn home-btn"
                >
                    <i class="fa-solid fa-house"></i>
                    Back to Home
                </a>

            </div>

        </div>

    </main>


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

            </div>


            <div class="footer-column">

                <h4>Quick Links</h4>

                <a href="index.php">Home</a>
                <a href="index.php#packages">Packages</a>
                <a href="venues.php">Venues</a>
                <a href="tickets.php">Tickets</a>

            </div>


            <div class="footer-column">

                <h4>Services</h4>

                <a href="catering.php">Catering</a>
                <a href="cakes.php">Cakes</a>
                <a href="venues.php">Venue Selection</a>

            </div>


            <div class="footer-column">

                <h4>Contact</h4>

                <p>
                    <i class="fa-solid fa-phone"></i>
                    0624 947 668
                </p>

                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    Dar es Salaam, Tanzania
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

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>