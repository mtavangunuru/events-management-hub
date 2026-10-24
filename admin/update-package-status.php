
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
    die("Package booking ID is missing.");
}

if (!in_array($status, $allowedStatuses, true)) {
    die("Invalid booking status.");
}

$sql = "
    UPDATE package_bookings
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

    header("Location: package-bookings.php");
    exit;

} else {

    die("Package booking status could not be updated: " . $stmt->error);
}

$stmt->close();
$conn->close();

?>