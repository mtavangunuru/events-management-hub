
<?php
require_once "../connection.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: venues.php");
    exit;
}

$id = intval($_GET['id']);


/* ================================
   GET VENUE
================================ */

$stmt = $conn->prepare("SELECT * FROM venues WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: venues.php");
    exit;
}

$venue = $result->fetch_assoc();

$stmt->close();


/* ================================
   UPDATE VENUE
================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $venue_name = trim($_POST['venue_name']);
    $location = trim($_POST['location']);
    $venue_type = trim($_POST['venue_type']);
    $capacity = intval($_POST['capacity']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);

    $image_path = $venue['image'];


    /* ================================
       HANDLE NEW PHOTO
    ================================= */

    if (
        isset($_FILES['venue_image']) &&
        $_FILES['venue_image']['error'] === UPLOAD_ERR_OK
    ) {

        $file_name = $_FILES['venue_image']['name'];
        $file_tmp = $_FILES['venue_image']['tmp_name'];
        $file_size = $_FILES['venue_image']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];


        if (!in_array($extension, $allowed_extensions)) {

            $error = "Invalid image format. Please use JPG, PNG or WEBP.";

        } elseif ($file_size > 5 * 1024 * 1024) {

            $error = "Image is too large. Maximum size is 5MB.";

        } else {

            $upload_folder = "../uploads/venues/";

            if (!is_dir($upload_folder)) {
                mkdir($upload_folder, 0777, true);
            }


            $new_file_name =
                "venue_" .
                date("YmdHis") .
                "_" .
                rand(100, 999) .
                "." .
                $extension;


            $new_file_path =
                $upload_folder . $new_file_name;


            if (move_uploaded_file($file_tmp, $new_file_path)) {

                /*
                 * Delete old image if it exists
                 */
                if (!empty($venue['image'])) {

                    $old_image =
                        "../" . $venue['image'];

                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }


                $image_path =
                    "uploads/venues/" . $new_file_name;

            } else {

                $error = "Failed to upload the new image.";

            }
        }
    }


    /* ================================
       SAVE CHANGES
    ================================= */

    if (!isset($error)) {

        $stmt = $conn->prepare(
            "UPDATE venues SET
                venue_name = ?,
                location = ?,
                venue_type = ?,
                capacity = ?,
                price = ?,
                description = ?,
                image = ?,
                status = ?
             WHERE id = ?"
        );


        $stmt->bind_param(
            "sssidsssi",
            $venue_name,
            $location,
            $venue_type,
            $capacity,
            $price,
            $description,
            $image_path,
            $status,
            $id
        );


        if ($stmt->execute()) {

            header("Location: venues.php?updated=1");
            exit;

        } else {

            $error = "Failed to update venue: " . $stmt->error;

        }

        $stmt->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Venue | Nuustar Admin</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family:
                Arial,
                sans-serif;

            background: #f4f6f9;

            color: #333;
        }


        .container {
            max-width: 900px;

            margin: 40px auto;

            padding: 20px;
        }


        .page-header {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;
        }


        .page-header h1 {
            margin: 0;

            color: #0b2d5c;
        }


        .back-button {
            background: #0b2d5c;

            color: white;

            text-decoration: none;

            padding: 10px 18px;

            border-radius: 6px;
        }


        .form-card {
            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 5px 20px rgba(0,0,0,0.08);
        }


        .form-group {
            margin-bottom: 20px;
        }


        label {
            display: block;

            margin-bottom: 8px;

            font-weight: 600;

            color: #0b2d5c;
        }


        input,
        textarea,
        select {
            width: 100%;

            padding: 12px;

            border: 1px solid #ddd;

            border-radius: 6px;

            font-size: 14px;
        }


        textarea {
            min-height: 120px;

            resize: vertical;
        }


        .current-image {
            margin-top: 10px;
        }


        .current-image img {
            width: 220px;

            height: 140px;

            object-fit: cover;

            border-radius: 8px;

            border: 1px solid #ddd;
        }


        .image-note {
            color: #777;

            font-size: 13px;

            margin-top: 7px;
        }


        .error {
            background: #ffe5e5;

            color: #b00020;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;
        }


        .save-button {
            background: #f4c430;

            color: #071f40;

            border: none;

            padding: 13px 25px;

            border-radius: 7px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;
        }


        .save-button:hover {
            background: #e0b51f;
        }

    </style>

</head>


<body>


<div class="container">


    <div class="page-header">

        <h1>
            Edit Venue
        </h1>

        <a
            href="venues.php"
            class="back-button">

            ← Back to Venues

        </a>

    </div>



    <?php if (isset($error)): ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>



    <div class="form-card">


        <form
            method="POST"
            enctype="multipart/form-data">


            <!-- Venue Name -->

            <div class="form-group">

                <label>
                    Venue Name
                </label>

                <input
                    type="text"
                    name="venue_name"
                    value="<?php echo htmlspecialchars($venue['venue_name']); ?>"
                    required>

            </div>



            <!-- Location -->

            <div class="form-group">

                <label>
                    Location
                </label>

                <input
                    type="text"
                    name="location"
                    value="<?php echo htmlspecialchars($venue['location']); ?>"
                    required>

            </div>



            <!-- Venue Type -->

            <div class="form-group">

                <label>
                    Venue Type
                </label>

                <input
                    type="text"
                    name="venue_type"
                    value="<?php echo htmlspecialchars($venue['venue_type']); ?>">

            </div>



            <!-- Capacity -->

            <div class="form-group">

                <label>
                    Capacity
                </label>

                <input
                    type="number"
                    name="capacity"
                    value="<?php echo htmlspecialchars($venue['capacity']); ?>">

            </div>



            <!-- Price -->

            <div class="form-group">

                <label>
                    Price (TSh)
                </label>

                <input
                    type="number"
                    name="price"
                    step="0.01"
                    value="<?php echo htmlspecialchars($venue['price']); ?>">

            </div>



            <!-- Description -->

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"><?php echo htmlspecialchars($venue['description']); ?></textarea>

            </div>



            <!-- Current Photo -->

            <div class="form-group">

                <label>
                    Current Photo
                </label>


                <?php if (!empty($venue['image'])): ?>

                    <div class="current-image">

                        <img
                            src="../<?php echo htmlspecialchars($venue['image']); ?>"
                            alt="Current venue photo">

                    </div>

                <?php else: ?>

                    <p>
                        No photo uploaded.
                    </p>

                <?php endif; ?>

            </div>



            <!-- New Photo -->

            <div class="form-group">

                <label>
                    Change Venue Photo
                </label>

                <input
                    type="file"
                    name="venue_image"
                    accept=".jpg,.jpeg,.png,.webp">

                <div class="image-note">

                    Leave this empty if you do not want
                    to change the current photo.

                </div>

            </div>



            <!-- Status -->

            <div class="form-group">

                <label>
                    Status
                </label>

                <select name="status">

                    <option
                        value="Available"
                        <?php
                        if ($venue['status'] === 'Available') {
                            echo 'selected';
                        }
                        ?>>

                        Available

                    </option>


                    <option
                        value="Unavailable"
                        <?php
                        if ($venue['status'] === 'Unavailable') {
                            echo 'selected';
                        }
                        ?>>

                        Unavailable

                    </option>

                </select>

            </div>



            <button
                type="submit"
                class="save-button">

                Save Changes

            </button>


        </form>

    </div>

</div>


</body>

</html>