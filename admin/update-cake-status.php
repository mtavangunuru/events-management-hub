
<?php

session_start();

require_once "../connection.php";

$orderId = trim($_POST['order_id'] ?? '');
$status = trim($_POST['status'] ?? '');

$allowedStatuses = [
    'Pending',
    'Confirmed',
    'Cancelled'
];

if ($orderId === '') {
    die("Cake order ID is missing.");
}

if (!in_array($status, $allowedStatuses, true)) {
    die("Invalid order status.");
}

$sql = "
    UPDATE cake_orders
    SET status = ?
    WHERE order_id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ss",
    $status,
    $orderId
);

if ($stmt->execute()) {

    header("Location: cake-orders.php");
    exit;

} else {

    die("Cake order status could not be updated: " . $stmt->error);
}

$stmt->close();
$conn->close();

?>