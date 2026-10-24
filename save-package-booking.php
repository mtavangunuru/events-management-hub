
<?php

require_once "connection.php";

header("Content-Type: application/json");

/*
    Receive JSON data from package-booking.php
*/
$input = file_get_contents("php://input");

$data = json_decode($input, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No booking data received."
    ]);
    exit;
}


/* ================================
   GET BOOKING DATA
================================ */

$fullName = trim($data["fullName"] ?? "");
$phone = trim($data["phone"] ?? "");
$email = trim($data["email"] ?? "");
$whatsapp = trim($data["whatsapp"] ?? "");
$contactMethod = trim($data["contactMethod"] ?? "");

$packageCode = trim($data["packageCode"] ?? "");
$packageName = trim($data["packageName"] ?? "");

$basePrice = floatval($data["basePrice"] ?? 0);

$eventType = trim($data["eventType"] ?? "");
$eventDate = !empty($data["eventDate"])
    ? $data["eventDate"]
    : null;

$guests = intval($data["guests"] ?? 0);

$location = trim($data["location"] ?? "");
$venueType = trim($data["venueType"] ?? "");

$additionalServices =
    json_encode(
        $data["additionalServices"] ?? [],
        JSON_UNESCAPED_UNICODE
    );

$estimatedTotal =
    floatval($data["estimatedTotal"] ?? 0);

$specialRequirements =
    trim($data["specialRequirements"] ?? "");

$status = "Pending";


/* ================================
   VALIDATION
================================ */

if ($fullName === "" || $phone === "" || $packageName === "") {

    echo json_encode([
        "success" => false,
        "message" => "Full name, phone and package are required."
    ]);

    exit;
}


/* ================================
   CREATE BOOKING ID
================================ */

$bookingId =
    "NUE-" .
    date("YmdHis") .
    "-" .
    random_int(100, 999);


/* ================================
   INSERT INTO MYSQL
================================ */

$sql = "
    INSERT INTO package_bookings (
        booking_id,
        full_name,
        phone,
        email,
        whatsapp,
        contact_method,
        package_code,
        package_name,
        base_price,
        event_type,
        event_date,
        guests,
        location,
        venue_type,
        additional_services,
        estimated_total,
        special_requirements,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Could not prepare database query.",
        "error" => $conn->error
    ]);

    exit;
}


/* ================================
   BIND VALUES
================================ */

$stmt->bind_param(
    "ssssssssdsissssdss",
    $bookingId,
    $fullName,
    $phone,
    $email,
    $whatsapp,
    $contactMethod,
    $packageCode,
    $packageName,
    $basePrice,
    $eventType,
    $eventDate,
    $guests,
    $location,
    $venueType,
    $additionalServices,
    $estimatedTotal,
    $specialRequirements,
    $status
);


/* ================================
   EXECUTE
================================ */

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Booking saved successfully.",
        "bookingId" => $bookingId
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Booking could not be saved.",
        "error" => $stmt->error
    ]);

}


$stmt->close();
$conn->close();

?>