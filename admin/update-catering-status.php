
<?php

session_start();

require_once "../connection.php";


/*
|--------------------------------------------------------------------------
| GET REQUEST DATA
|--------------------------------------------------------------------------
*/

$requestId = trim($_POST['request_id'] ?? '');
$status = trim($_POST['status'] ?? '');


/*
|--------------------------------------------------------------------------
| VALIDATE STATUS
|--------------------------------------------------------------------------
*/

$allowedStatuses = [
    'Pending',
    'Confirmed',
    'Cancelled'
];

if ($requestId === '') {
    die("Catering request ID is missing.");
}

if (!in_array($status, $allowedStatuses, true)) {
    die("Invalid booking status.");
}


/*
|--------------------------------------------------------------------------
| UPDATE CATERING BOOKING
|--------------------------------------------------------------------------
*/

$sql = "
    UPDATE catering_bookings
    SET status = ?
    WHERE request_id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ss",
    $status,
    $requestId
);


/*
|--------------------------------------------------------------------------
| SAVE UPDATE
|--------------------------------------------------------------------------
*/

if ($stmt->execute()) {

    header("Location: catering-orders.php");
    exit;

} else {

    die("Status could not be updated: " . $stmt->error);

}


$stmt->close();
$conn->close();

?>