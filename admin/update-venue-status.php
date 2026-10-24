
<?php

session_start();

require_once "../connection.php";

$bookingId = trim($_POST['booking_id'] ?? '');
$status = trim($_POST['status'] ?? '');

$allowedStatuses = [
    'Pending',
    'Confirmed',
    'Cancelled'
];

if ($bookingId === '') {
    die("Venue booking ID is missing.");
}

if (!in_array($status, $allowedStatuses, true)) {
    die("Invalid booking status.");
}

$sql = "
    UPDATE venue_bookings
    SET status = ?
    WHERE booking_id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ss",
    $status,
    $bookingId
);

if ($stmt->execute()) {

    header("Location: venue-bookings.php");
    exit;

} else {

    die("Venue booking status could not be updated: " . $stmt->error);
}

$stmt->close();
$conn->close();

?>