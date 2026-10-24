
<?php

require_once "connection.php";


/* =================================
   GET FORM DATA
================================= */

$eventCode = trim($_POST["event_code"] ?? "");
$eventName = trim($_POST["event_name"] ?? "");
$eventDate = $_POST["event_date"] ?? null;
$eventTime = $_POST["event_time"] ?? null;
$eventLocation = trim($_POST["event_location"] ?? "");

$customerName = trim($_POST["customer_name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$whatsapp = trim($_POST["whatsapp"] ?? "");
$email = trim($_POST["email"] ?? "");
$contactMethod = trim($_POST["contact_method"] ?? "");

$ticketType = trim($_POST["ticket_type"] ?? "");

$pricePerTicket =
    floatval($_POST["price_per_ticket"] ?? 0);

$quantity =
    intval($_POST["quantity"] ?? 1);

$serviceFee =
    floatval($_POST["service_fee"] ?? 0);

$total =
    floatval($_POST["total"] ?? 0);

$status = "Pending";


/* =================================
   VALIDATION
================================= */

if (
    $eventName === "" ||
    $customerName === "" ||
    $phone === "" ||
    $ticketType === ""
) {

    die(
        "Please provide the required event, customer and ticket information."
    );

}


/* =================================
   CREATE TICKET ID
================================= */

$ticketId =
    "NUU-" .
    date("YmdHis") .
    "-" .
    random_int(100, 999);


/* =================================
   INSERT INTO DATABASE
================================= */

$sql = "
    INSERT INTO ticket_bookings (
        ticket_id,
        event_code,
        event_name,
        event_date,
        event_time,
        event_location,
        customer_name,
        phone,
        whatsapp,
        email,
        contact_method,
        ticket_type,
        price_per_ticket,
        quantity,
        service_fee,
        total,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    die(
        "Database error: " .
        $conn->error
    );

}


/* =================================
   BIND DATA
================================= */

$stmt->bind_param(
    "ssssssssssssdidds",
    $ticketId,
    $eventCode,
    $eventName,
    $eventDate,
    $eventTime,
    $eventLocation,
    $customerName,
    $phone,
    $whatsapp,
    $email,
    $contactMethod,
    $ticketType,
    $pricePerTicket,
    $quantity,
    $serviceFee,
    $total,
    $status
);


/* =================================
   SAVE
================================= */

if ($stmt->execute()) {

    header(
        "Location: ticket-confirmation.php?ticket_id=" .
        urlencode($ticketId)
    );

    exit;

} else {

    die(
        "Ticket booking could not be saved: " .
        $stmt->error
    );

}


$stmt->close();
$conn->close();

?>