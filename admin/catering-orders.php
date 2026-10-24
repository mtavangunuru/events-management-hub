
<?php
session_start();

require_once "../connection.php";

/*
|--------------------------------------------------------------------------
| GET CATERING BOOKINGS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT *
    FROM catering_bookings
    ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nuustar Events | Catering Bookings</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f3f4f6;
            color: #111827;
        }


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #111827;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 15px;
            overflow-y: auto;
        }

        .logo {
            color: #f5c400;
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 40px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 15px;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #f5c400;
            color: #111827;
        }


        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }


        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar h1 {
            font-size: 28px;
            color: #111827;
            margin-bottom: 7px;
        }

        .topbar p {
            color: #6b7280;
            font-size: 14px;
        }

        .admin-badge {
            background: #111827;
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 14px;
        }


        /* =========================
           CONTENT CARD
        ========================= */

        .content-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            margin-bottom: 20px;
        }

        .section-title h2 {
            font-size: 20px;
            color: #111827;
        }

        .section-title p {
            color: #6b7280;
            font-size: 13px;
            margin-top: 5px;
        }


        /* =========================
           TABLE
        ========================= */

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1500px;
        }

        th {
            background: #111827;
            color: #ffffff;
            padding: 14px 12px;
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #eeeeee;
            font-size: 13px;
            vertical-align: top;
        }

        tr:hover {
            background: #fafafa;
        }


        /* =========================
           STATUS
        ========================= */

        .status-form {
            margin: 0;
        }

        .status-select {
            padding: 7px 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #ffffff;
            font-size: 12px;
            cursor: pointer;
        }

        .status-select:focus {
            outline: none;
            border-color: #f5c400;
        }


        /* =========================
           MONEY
        ========================= */

        .amount {
            font-weight: bold;
            color: #111827;
            white-space: nowrap;
        }


        /* =========================
           EMPTY
        ========================= */

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }

        .empty h3 {
            margin-bottom: 8px;
            color: #111827;
        }


        /* =========================
           BACK BUTTON
        ========================= */

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            background: #111827;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
        }

        .back-button:hover {
            background: #f5c400;
            color: #111827;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
                padding: 20px;
            }

            .topbar {
                padding: 20px;
            }

            .topbar h1 {
                font-size: 22px;
            }

            .admin-badge {
                display: none;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="logo">
        NUU STAR
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="index.php">
                🏠
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="venue-bookings.php">
                🏛️
                <span>Venue Bookings</span>
            </a>
        </li>

        <li>
            <a href="package-bookings.php">
                📦
                <span>Package Bookings</span>
            </a>
        </li>

        <li>
            <a href="cake-orders.php">
                🎂
                <span>Cake Orders</span>
            </a>
        </li>

        <li>
            <a href="ticket-bookings.php">
                🎟️
                <span>Ticket Bookings</span>
            </a>
        </li>

        <li>
            <a href="catering-orders.php" class="active">
                🍽️
                <span>Catering Orders</span>
            </a>
        </li>

        <li>
            <a href="logout.php">
                🚪
                <span>Logout</span>
            </a>
        </li>

    </ul>

</div>



<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <h1>Catering Bookings</h1>

            <p>
                Manage catering requests received through Nuustar Events.
            </p>

        </div>

        <div class="admin-badge">
            Administrator
        </div>

    </div>



    <!-- BACK BUTTON -->

    <a href="index.php" class="back-button">
        ← Back to Dashboard
    </a>



    <!-- BOOKINGS -->

    <div class="content-card">

        <div class="section-title">

            <h2>All Catering Bookings</h2>

            <p>
                Customer catering requests and event details.
            </p>

        </div>


        <div class="table-container">

            <?php if ($result && $result->num_rows > 0): ?>

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Request ID</th>

                            <th>Customer</th>

                            <th>Phone</th>

                            <th>Package</th>

                            <th>Event Type</th>

                            <th>Guests</th>

                            <th>Event Date</th>

                            <th>Time</th>

                            <th>Location</th>

                            <th>Service Style</th>

                            <th>Food Preference</th>

                            <th>Budget</th>

                            <th>Additional Services</th>

                            <th>Food Cost</th>

                            <th>Add-ons Cost</th>

                            <th>Estimated Total</th>

                            <th>Status</th>

                            <th>Created</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo (int)$row['id']; ?>
                            </td>


                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($row['request_id']); ?>
                                </strong>
                            </td>


                            <td>

                                <strong>
                                    <?php echo htmlspecialchars($row['full_name']); ?>
                                </strong>

                                <?php if (!empty($row['email'])): ?>

                                    <br>

                                    <small>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </small>

                                <?php endif; ?>

                            </td>


                            <td>

                                <?php echo htmlspecialchars($row['phone']); ?>

                                <?php if (!empty($row['whatsapp'])): ?>

                                    <br>

                                    <small>
                                        WhatsApp:
                                        <?php echo htmlspecialchars($row['whatsapp']); ?>
                                    </small>

                                <?php endif; ?>

                            </td>


                            <td>

                                <?php echo htmlspecialchars($row['package_name']); ?>

                                <br>

                                <small>
                                    <?php
                                    echo number_format(
                                        (float)$row['price_per_person']
                                    );
                                    ?>
                                    TZS/person
                                </small>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($row['event_type']); ?>
                            </td>


                            <td>
                                <?php echo (int)$row['guests']; ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($row['event_date']); ?>
                            </td>


                            <td>
                                <?php
                                echo !empty($row['event_time'])
                                    ? htmlspecialchars($row['event_time'])
                                    : "-";
                                ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($row['location']); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($row['service_style']); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($row['food_preference']); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($row['budget']); ?>
                            </td>


                            <td>

                                <?php

                                if (!empty($row['additional_services'])) {

                                    echo htmlspecialchars(
                                        $row['additional_services']
                                    );

                                } else {

                                    echo "None";

                                }

                                ?>

                            </td>


                            <td class="amount">

                                <?php

                                echo number_format(
                                    (float)$row['food_cost']
                                );

                                ?>

                                TZS

                            </td>


                            <td class="amount">

                                <?php

                                echo number_format(
                                    (float)$row['addons_cost']
                                );

                                ?>

                                TZS

                            </td>


                            <td class="amount">

                                <?php

                                echo number_format(
                                    (float)$row['estimated_total']
                                );

                                ?>

                                TZS

                            </td>


                            <!-- STATUS -->

                            <td>

                                <form
                                    action="update-catering-status.php"
                                    method="POST"
                                    class="status-form"
                                >

                                    <input
                                        type="hidden"
                                        name="request_id"
                                        value="<?php echo htmlspecialchars($row['request_id']); ?>"
                                    >

                                    <select
                                        name="status"
                                        class="status-select"
                                        onchange="this.form.submit()"
                                    >

                                        <option
                                            value="Pending"
                                            <?php
                                            echo $row['status'] === 'Pending'
                                                ? 'selected'
                                                : '';
                                            ?>
                                        >
                                            Pending
                                        </option>

                                        <option
                                            value="Confirmed"
                                            <?php
                                            echo $row['status'] === 'Confirmed'
                                                ? 'selected'
                                                : '';
                                            ?>
                                        >
                                            Confirmed
                                        </option>

                                        <option
                                            value="Cancelled"
                                            <?php
                                            echo $row['status'] === 'Cancelled'
                                                ? 'selected'
                                                : '';
                                            ?>
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

                    </tbody>

                </table>


            <?php else: ?>

                <div class="empty">

                    <h3>No Catering Bookings Yet</h3>

                    <p>
                        Catering bookings submitted by customers will appear here.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>


</div>


</body>

</html>

<?php
$conn->close();
?>