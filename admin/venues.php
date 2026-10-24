
<?php

session_start();

require_once "../connection.php";

$sql = "SELECT * FROM venues ORDER BY created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
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

    <title>Venue Management | Nuustar Events Admin</title>

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
            overflow-y: auto;
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

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar h1 {
            font-size: 26px;
        }

        .add-button {
            display: inline-block;
            background: #f4c430;
            color: #111827;
            padding: 11px 17px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .add-button:hover {
            background: #d9ad00;
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
            min-width: 1200px;
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
            vertical-align: middle;
        }

        tr:hover {
            background: #f9fafb;
        }

        .venue-image {
            width: 110px;
            height: 75px;
            object-fit: cover;
            border-radius: 7px;
            border: 1px solid #ddd;
        }

        .no-image {
            width: 110px;
            height: 75px;
            background: #f3f4f6;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 12px;
            text-align: center;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .available {
            background: #dcfce7;
            color: #166534;
        }

        .unavailable {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 7px;
        }

        .edit-button,
        .delete-button {
            padding: 7px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .edit-button {
            background: #2563eb;
            color: white;
        }

        .edit-button:hover {
            background: #1d4ed8;
        }

        .delete-button {
            background: #dc2626;
            color: white;
        }

        .delete-button:hover {
            background: #b91c1c;
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

        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #777;
        }

    </style>

</head>

<body>


<!-- SIDEBAR -->

<div class="sidebar">

    <h2>NUUSTAR</h2>

    <a href="index.php">
        🏠 Dashboard
    </a>

    <a href="venue-bookings.php">
        📍 Venue Bookings
    </a>

    <a href="package-bookings.php">
        📦 Package Bookings
    </a>

    <a href="cake-orders.php">
        🎂 Cake Orders
    </a>

    <a href="ticket-orders.php">
        🎟️ Ticket Bookings
    </a>

    <a href="catering-orders.php">
        🍽️ Catering Bookings
    </a>

    <a href="venues.php">
        🏛️ Venue Management
    </a>

    <a href="../index.php">
        🌐 View Website
    </a>

    <a href="logout.php">
        🚪 Logout
    </a>

</div>


<!-- MAIN -->

<div class="main">


    <div class="topbar">

        <h1>
            Venue Management
        </h1>

        <a
            href="add-venue.php"
            class="add-button"
        >
            ➕ Add Venue
        </a>

    </div>


    <?php if (isset($_GET['success'])): ?>

        <div class="success-message">

            ✅ Venue added successfully.

        </div>

    <?php endif; ?>


    <a
        href="index.php"
        class="back-button"
    >
        ← Back to Dashboard
    </a>


    <div class="content-card">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Photo</th>

                    <th>Venue Name</th>

                    <th>Location</th>

                    <th>Type</th>

                    <th>Capacity</th>

                    <th>Price</th>

                    <th>Description</th>

                    <th>Status</th>

                    <th>Created</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <!-- ID -->

                        <td>

                            <?php
                            echo htmlspecialchars($row['id']);
                            ?>

                        </td>


                        <!-- PHOTO -->

                        <td>

                            <?php if (!empty($row['image'])): ?>

                                <img
                                    src="../<?php echo htmlspecialchars($row['image']); ?>"
                                    alt="Venue Photo"
                                    class="venue-image"
                                >

                            <?php else: ?>

                                <div class="no-image">
                                    No Photo
                                </div>

                            <?php endif; ?>

                        </td>


                        <!-- NAME -->

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars($row['venue_name']);
                                ?>
                            </strong>

                        </td>


                        <!-- LOCATION -->

                        <td>

                            <?php
                            echo htmlspecialchars($row['location']);
                            ?>

                        </td>


                        <!-- TYPE -->

                        <td>

                            <?php
                            echo htmlspecialchars($row['venue_type'] ?? '');
                            ?>

                        </td>


                        <!-- CAPACITY -->

                        <td>

                            <?php
                            echo htmlspecialchars($row['capacity'] ?? 0);
                            ?>

                        </td>


                        <!-- PRICE -->

                        <td>

                            TZS
                            <?php
                            echo number_format(
                                (float)$row['price'],
                                0
                            );
                            ?>

                        </td>


                        <!-- DESCRIPTION -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['description'] ?? ''
                            );
                            ?>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php

                            $status_class =
                                ($row['status'] === 'Available')
                                ? 'available'
                                : 'unavailable';

                            ?>

                            <span
                                class="status <?php echo $status_class; ?>"
                            >

                                <?php
                                echo htmlspecialchars($row['status']);
                                ?>

                            </span>

                        </td>


                        <!-- CREATED -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $row['created_at']
                            );
                            ?>

                        </td>


                        <!-- ACTIONS -->

                        <td>

                            <div class="action-buttons">

                                <a
                                    href="edit-managed-venue.php?id=<?php echo $row['id']; ?>"
                                    class="edit-button"
                                >
                                    ✏️ Edit
                                </a>


                                <form
                                    action="delete-venue.php"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this venue?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo $row['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="delete-button"
                                    >
                                        🗑️ Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>


            <?php else: ?>

                <tr>

                    <td
                        colspan="11"
                        class="no-data"
                    >

                        No venues have been added yet.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>