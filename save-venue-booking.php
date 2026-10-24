

<?php

require_once "connection.php";

/* =================================
   GET FORM DATA
================================= */

$venueId      = intval($_POST["venue_id"] ?? 0);
$customerName = trim($_POST["fullname"] ?? "");
$phone        = trim($_POST["phone"] ?? "");
$email        = trim($_POST["email"] ?? "");

$eventType    = trim($_POST["event_type"] ?? "");
$eventDate    = $_POST["event_date"] ?? null;
$guests       = intval($_POST["guests"] ?? 0);

$venueType    = trim($_POST["venue_type"] ?? "");
$location     = trim($_POST["location"] ?? "");
$budget       = floatval($_POST["budget"] ?? 0);

$message      = trim($_POST["message"] ?? "");
$status       = "Pending";

/* =================================
   VALIDATE CUSTOMER DETAILS
================================= */

if ($customerName === "" || $phone === "") {
    die("Please provide your name and phone number.");
}

/* =================================
   VALIDATE SELECTED VENUE
================================= */

if ($venueId <= 0) {
    die("No venue was selected. Please return to the venues page and select a venue.");
}

$venueStmt = $conn->prepare(
    "SELECT id, venue_name, location, venue_type
     FROM venues
     WHERE id = ? AND status = 'Available'"
);

if (!$venueStmt) {
    die("Database error: " . $conn->error);
}

$venueStmt->bind_param("i", $venueId);
$venueStmt->execute();

$venueResult = $venueStmt->get_result();

if ($venueResult->num_rows === 0) {
    die("The selected venue is unavailable. Please choose another venue.");
}

$selectedVenue = $venueResult->fetch_assoc();
$venueStmt->close();

/* =================================
   USE THE SELECTED VENUE DETAILS
================================= */

$location = $selectedVenue["location"];
$venueType = $selectedVenue["venue_type"] ?? "";

/* =================================
   CREATE BOOKING ID
================================= */

$bookingId = "VEN-" . date("YmdHis") . "-" . random_int(100, 999);

/* =================================
   SAVE BOOKING
================================= */

$sql = "
    INSERT INTO venue_bookings (
        booking_id,
        venue_id,
        customer_name,
        phone,
        email,
        event_type,
        event_date,
        guests,
        venue_type,
        location,
        budget,
        message,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "sisssssissdss",
    $bookingId,
    $venueId,
    $customerName,
    $phone,
    $email,
    $eventType,
    $eventDate,
    $guests,
    $venueType,
    $location,
    $budget,
    $message,
    $status
);

/* =================================
   SAVE AND REDIRECT
================================= */

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header(
        "Location: venue-confirmation.php?booking_id=" .
        urlencode($bookingId)
    );
    exit;
}

die("Booking could not be saved: " . $stmt->error);

?>