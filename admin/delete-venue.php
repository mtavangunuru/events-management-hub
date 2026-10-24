
<?php

session_start();

require_once "../connection.php";

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    die("Invalid venue booking ID.");
}

$sql = "DELETE FROM venue_bookings WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: venue-bookings.php");
    exit;

} else {

    die("Venue booking could not be deleted: " . $stmt->error);
}

$stmt->close();

$conn->close();

?>