
<?php

require_once "connection.php";

/* =================================
   GET FORM DATA
================================= */

$packageName = trim($_POST["package_name"] ?? "");
$pricePerPerson = floatval($_POST["price_per_person"] ?? 0);

$eventType = trim($_POST["event_type"] ?? "");
$guests = intval($_POST["guests"] ?? 0);
$eventDate = $_POST["event_date"] ?? null;
$eventTime = $_POST["event_time"] ?? null;
$location = trim($_POST["location"] ?? "");

$serviceStyle = trim($_POST["service_style"] ?? "");
$foodPreference = trim($_POST["food_preference"] ?? "");
$budget = trim($_POST["budget"] ?? "");

$additionalServices = trim($_POST["additional_services"] ?? "");

$foodCost = floatval($_POST["food_cost"] ?? 0);
$addonsCost = floatval($_POST["addons_cost"] ?? 0);
$estimatedTotal = floatval($_POST["estimated_total"] ?? 0);

$fullName = trim($_POST["full_name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$whatsapp = trim($_POST["whatsapp"] ?? "");
$email = trim($_POST["email"] ?? "");

$notes = trim($_POST["notes"] ?? "");

$status = "Pending";


/* =================================
   VALIDATION
================================= */

if (
    $packageName === "" ||
    $eventType === "" ||
    $guests <= 0 ||
    $eventDate === null ||
    $location === "" ||
    $serviceStyle === "" ||
    $foodPreference === "" ||
    $fullName === "" ||
    $phone === ""
) {

    die("Please provide all required catering information.");

}


/* =================================
   CREATE REQUEST ID
================================= */

$requestId =
    "CAT-" .
    date("YmdHis") .
    "-" .
    random_int(100, 999);


/* =================================
   INSERT INTO DATABASE
================================= */

$sql = "
    INSERT INTO catering_bookings (
        request_id,
        package_name,
        price_per_person,
        event_type,
        guests,
        event_date,
        event_time,
        location,
        service_style,
        food_preference,
        budget,
        additional_services,
        food_cost,
        addons_cost,
        estimated_total,
        full_name,
        phone,
        whatsapp,
        email,
        notes,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
    "ssdsiissssssdddssssss",
    $requestId,
    $packageName,
    $pricePerPerson,
    $eventType,
    $guests,
    $eventDate,
    $eventTime,
    $location,
    $serviceStyle,
    $foodPreference,
    $budget,
    $additionalServices,
    $foodCost,
    $addonsCost,
    $estimatedTotal,
    $fullName,
    $phone,
    $whatsapp,
    $email,
    $notes,
    $status
);


/* =================================
   SAVE BOOKING
================================= */

if ($stmt->execute()) {

    header(
        "Location: catering-confirmation.php?request_id=" .
        urlencode($requestId)
    );

    exit;

} else {

    die(
        "Catering booking could not be saved: " .
        $stmt->error
    );

}


$stmt->close();
$conn->close();

?>