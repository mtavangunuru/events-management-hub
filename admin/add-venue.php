
<?php

session_start();

require_once "../connection.php";

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| SAVE VENUE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $venue_name = trim($_POST['venue_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $venue_type = trim($_POST['venue_type'] ?? '');
    $capacity = intval($_POST['capacity'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');

    /*
    |--------------------------------------------------------------------------
    | VALIDATE REQUIRED FIELDS
    |--------------------------------------------------------------------------
    */

    if ($venue_name === '' || $location === '') {

        $message = "Venue name and location are required.";
        $message_type = "error";

    } else {

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPLOAD
        |--------------------------------------------------------------------------
        */

        $image_path = "";

        if (isset($_FILES['venue_image']) && $_FILES['venue_image']['error'] !== UPLOAD_ERR_NO_FILE) {

            $image = $_FILES['venue_image'];

            if ($image['error'] !== UPLOAD_ERR_OK) {

                $message = "There was a problem uploading the image.";
                $message_type = "error";

            } else {

                $allowed_types = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!in_array($image['type'], $allowed_types, true)) {

                    $message = "Only JPG, PNG and WEBP images are allowed.";
                    $message_type = "error";

                } elseif ($image['size'] > 5 * 1024 * 1024) {

                    $message = "Image size must not exceed 5MB.";
                    $message_type = "error";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE UPLOAD FOLDER
                    |--------------------------------------------------------------------------
                    */

                    $upload_directory = "../uploads/venues/";

                    if (!is_dir($upload_directory)) {
                        mkdir($upload_directory, 0777, true);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE UNIQUE FILE NAME
                    |--------------------------------------------------------------------------
                    */

                    $extension = strtolower(
                        pathinfo($image['name'], PATHINFO_EXTENSION)
                    );

                    $new_file_name =
                        "venue_" .
                        date("YmdHis") .
                        "_" .
                        random_int(100, 999) .
                        "." .
                        $extension;

                    $destination =
                        $upload_directory .
                        $new_file_name;


                    /*
                    |--------------------------------------------------------------------------
                    | MOVE IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (move_uploaded_file($image['tmp_name'], $destination)) {

                        $image_path =
                            "uploads/venues/" .
                            $new_file_name;

                    } else {

                        $message = "The image could not be uploaded.";
                        $message_type = "error";
                    }
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE TO DATABASE
        |--------------------------------------------------------------------------
        */

        if ($message_type !== "error") {

            $sql = "
                INSERT INTO venues (
                    venue_name,
                    location,
                    venue_type,
                    capacity,
                    price,
                    description,
                    image,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {

                $message = "Database error: " . $conn->error;
                $message_type = "error";

            } else {

                $stmt->bind_param(
                    "sssissss",
                    $venue_name,
                    $location,
                    $venue_type,
                    $capacity,
                    $price,
                    $description,
                    $image_path,
                    $status
                );


                if ($stmt->execute()) {

                    header("Location: venues.php?success=1");
                    exit;

                } else {

                    $message =
                        "Venue could not be saved: " .
                        $stmt->error;

                    $message_type = "error";
                }

                $stmt->close();
            }
        }
    }
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

    <title>Add Venue | Nuustar Events Admin</title>

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
            min-height: 120px;
            resize: vertical;
        }

        .form-group small {
            display: block;
            margin-top: 6px;
            color: #6b7280;
        }

        .image-preview {
            margin-top: 10px;
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
            display: none;
            object-fit: cover;
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

        .message {
            max-width: 900px;
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
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
            Add New Venue
        </h1>

    </div>


    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <div class="form-card">

        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- VENUE NAME -->

            <div class="form-group">

                <label>
                    Venue Name *
                </label>

                <input
                    type="text"
                    name="venue_name"
                    placeholder="Example: Golden Palace Garden"
                    required
                >

            </div>


            <!-- LOCATION -->

            <div class="form-group">

                <label>
                    Location *
                </label>

                <input
                    type="text"
                    name="location"
                    placeholder="Example: Mbezi Beach, Dar es Salaam"
                    required
                >

            </div>


            <!-- VENUE TYPE -->

            <div class="form-group">

                <label>
                    Venue Type
                </label>

                <select name="venue_type">

                    <option value="">
                        Select Venue Type
                    </option>

                    <option value="Wedding Venue">
                        Wedding Venue
                    </option>

                    <option value="Event Hall">
                        Event Hall
                    </option>

                    <option value="Garden">
                        Garden
                    </option>

                    <option value="Hotel">
                        Hotel
                    </option>

                    <option value="Conference Hall">
                        Conference Hall
                    </option>

                    <option value="Outdoor Venue">
                        Outdoor Venue
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <!-- CAPACITY -->

            <div class="form-group">

                <label>
                    Capacity
                </label>

                <input
                    type="number"
                    name="capacity"
                    min="0"
                    placeholder="Example: 300"
                >

            </div>


            <!-- PRICE -->

            <div class="form-group">

                <label>
                    Starting Price (TZS)
                </label>

                <input
                    type="number"
                    name="price"
                    min="0"
                    step="0.01"
                    placeholder="Example: 1500000"
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    placeholder="Describe the venue, facilities, parking, decoration options, etc."
                ></textarea>

            </div>


            <!-- PHOTO -->

            <div class="form-group">

                <label>
                    Venue Photo
                </label>

                <input
                    type="file"
                    name="venue_image"
                    accept=".jpg,.jpeg,.png,.webp"
                    onchange="previewImage(event)"
                >

                <small>
                    Allowed: JPG, PNG, WEBP. Maximum size: 5MB.
                </small>

                <img
                    id="imagePreview"
                    class="image-preview"
                    alt="Venue preview"
                >

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label>
                    Status
                </label>

                <select name="status">

                    <option value="Available">
                        Available
                    </option>

                    <option value="Unavailable">
                        Unavailable
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="button-group">

                <button
                    type="submit"
                    class="save-button"
                >
                    💾 Save Venue
                </button>

                <a
                    href="venues.php"
                    class="cancel-button"
                >
                    Cancel
                </a>

            </div>


        </form>

    </div>

</div>


<script>

function previewImage(event) {

    const image = document.getElementById("imagePreview");

    const file = event.target.files[0];

    if (file) {

        image.src = URL.createObjectURL(file);

        image.style.display = "block";

    } else {

        image.style.display = "none";

    }

}

</script>

</body>

</html>