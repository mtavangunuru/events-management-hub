
<?php

session_start();

require_once "../connection.php";

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid venue booking ID.");
}


/*
|--------------------------------------------------------------------------
| GET BOOKING
|--------------------------------------------------------------------------
*/

$sql = "SELECT * FROM venue_bookings WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Venue booking not found.");
}

$booking = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| UPDATE BOOKING
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customer_name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $guests = intval($_POST['guests'] ?? 0);
    $venue_type = trim($_POST['venue_type'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $budget = trim($_POST['budget'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($customer_name === '' || $phone === '') {
        die("Customer name and phone are required.");
    }

    $sql = "
        UPDATE venue_bookings
        SET
            customer_name = ?,
            phone = ?,
            email = ?,
            event_type = ?,
            event_date = ?,
            guests = ?,
            venue_type = ?,
            location = ?,
            budget = ?,
            message = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssissssi",
        $customer_name,
        $phone,
        $email,
        $event_type,
        $event_date,
        $guests,
        $venue_type,
        $location,
        $budget,
        $message,
        $id
    );

    if ($stmt->execute()) {

        header("Location: venue-bookings.php");
        exit;

    } else {

        die("Venue booking could not be updated: " . $stmt->error);
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Venue Booking | Nuustar Events</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 230px;
            height: 100vh;
            background: #111827;
            color: white;
            padding: 25px 15px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #f4c430;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background: #f4c430;
            color: #111827;
        }

        .main {
            margin-left: 230px;
            padding: 30px;
        }

        .topbar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .topbar h1 {
            font-size: 26px;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            max-width: 900px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .save-button {
            background: #f4c430;
            color: #111827;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .save-button:hover {
            background: #d9ad00;
        }

        .cancel-button {
            background: #111827;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>


<!-- SIDEBAR -->

<div class="sidebar">

    <h2>NUUSTAR</h2>

    <a href="index.php">🏠 Dashboard</a>

    <a href="venue-bookings.php">📍 Venue Bookings</a>

    <a href="package-bookings.php">📦 Package Bookings</a>

    <a href="cake-orders.php">🎂 Cake Orders</a>

    <a href="ticket-orders.php">🎟️ Ticket Bookings</a>

    <a href="catering-orders.php">🍽️ Catering Bookings</a>

    <a href="../index.php">🌐 View Website</a>

    <a href="logout.php">🚪 Logout</a>

</div>


<!-- MAIN -->

<div class="main">

    <div class="topbar">

        <h1>Edit Venue Booking</h1>

    </div>


    <div class="form-card">

        <form method="POST">


            <div class="form-group">

                <label>Booking ID</label>

                <input
                    type="text"
                    value="<?php echo htmlspecialchars($booking['booking_id']); ?>"
                    readonly
                >

            </div>


            <div class="form-group">

                <label>Customer Name *</label>

                <input
                    type="text"
                    name="customer_name"
                    value="<?php echo htmlspecialchars($booking['customer_name']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Phone *</label>

                <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($booking['phone']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($booking['email'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Event Type</label>

                <input
                    type="text"
                    name="event_type"
                    value="<?php echo htmlspecialchars($booking['event_type'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Event Date</label>

                <input
                    type="date"
                    name="event_date"
                    value="<?php echo htmlspecialchars($booking['event_date'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Number of Guests</label>

                <input
                    type="number"
                    name="guests"
                    min="0"
                    value="<?php echo htmlspecialchars($booking['guests'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Venue Type</label>

                <input
                    type="text"
                    name="venue_type"
                    value="<?php echo htmlspecialchars($booking['venue_type'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Location</label>

                <input
                    type="text"
                    name="location"
                    value="<?php echo htmlspecialchars($booking['location'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Budget</label>

                <input
                    type="text"
                    name="budget"
                    value="<?php echo htmlspecialchars($booking['budget'] ?? ''); ?>"
                >

            </div>


            <div class="form-group">

                <label>Message</label>

                <textarea
                    name="message"
                ><?php echo htmlspecialchars($booking['message'] ?? ''); ?></textarea>

            </div>


            <div class="button-group">

                <button
                    type="submit"
                    class="save-button"
                >
                    💾 Save Changes
                </button>

                <a
                    href="venue-bookings.php"
                    class="cancel-button"
                >
                    Cancel
                </a>

            </div>


        </form>

    </div>

</div>

</body>

</html>