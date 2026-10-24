
<?php

session_start();

require_once "../connection.php";

$ticketId = trim($_POST['ticket_id'] ?? '');
$status = trim($_POST['status'] ?? '');

$allowedStatuses = [
    'Pending',
    'Confirmed',
    'Cancelled'
];

if ($ticketId === '') {
    die("Ticket ID is missing.");
}

if (!in_array($status, $allowedStatuses, true)) {
    die("Invalid ticket status.");
}

$sql = "
    UPDATE ticket_bookings
    SET status = ?
    WHERE ticket_id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ss",
    $status,
    $ticketId
);

if ($stmt->execute()) {

    header("Location: ticket-orders.php");
    exit;

} else {

    die("Ticket status could not be updated: " . $stmt->error);
}

$stmt->close();
$conn->close();

?>