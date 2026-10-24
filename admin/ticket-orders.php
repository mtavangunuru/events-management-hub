
<?php

session_start();

require_once "../connection.php";

$sql = "SELECT * FROM ticket_bookings ORDER BY created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ticket Bookings | Nuustar Events Admin</title>

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

        .content-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1900px;
            border-collapse: collapse;
        }

        th {
            background: #111827;
            color: white;
            padding: 12px;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        tr:hover {
            background: #f9fafb;
        }

        .status-form {
            margin: 0;
        }

        .status-select {
            padding: 7px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            background: white;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #111827;
            font-weight: bold;
        }

        .back-button:hover {
            color: #f4c430;
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


<!-- MAIN CONTENT -->

<div class="main">

    <div class="topbar">

        <h1>Ticket Bookings</h1>

    </div>


    <a href="index.php" class="back-button">
        ← Back to Dashboard
    </a>


    <div class="content-card">

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Ticket ID</th>
                    <th>Event Code</th>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Event Time</th>
                    <th>Event Location</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>WhatsApp</th>
                    <th>Email</th>
                    <th>Contact Method</th>
                    <th>Ticket Type</th>
                    <th>Price / Ticket</th>
                    <th>Quantity</th>
                    <th>Service Fee</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created</th>

                </tr>

            </thead>


            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row['id']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['ticket_id']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['event_code']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['event_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['event_date'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['event_time'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['event_location'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['customer_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['whatsapp'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['email'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['contact_method'] ?? ''); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['ticket_type']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['price_per_ticket']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['quantity']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['service_fee']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['total']); ?>
                        </td>

                        <td>

                            <form
                                action="update-ticket-status.php"
                                method="POST"
                                class="status-form"
                            >

                                <input
                                    type="hidden"
                                    name="ticket_id"
                                    value="<?php echo htmlspecialchars($row['ticket_id']); ?>"
                                >

                                <select
                                    name="status"
                                    class="status-select"
                                    onchange="this.form.submit()"
                                >

                                    <option
                                        value="Pending"
                                        <?php echo ($row['status'] === 'Pending') ? 'selected' : ''; ?>
                                    >
                                        Pending
                                    </option>

                                    <option
                                        value="Confirmed"
                                        <?php echo ($row['status'] === 'Confirmed') ? 'selected' : ''; ?>
                                    >
                                        Confirmed
                                    </option>

                                    <option
                                        value="Cancelled"
                                        <?php echo ($row['status'] === 'Cancelled') ? 'selected' : ''; ?>
                                    >
                                        Cancelled
                                    </option>

                                </select>

                            </form>

                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['created_at']); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td colspan="19" class="no-data">
                        No ticket bookings found.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>