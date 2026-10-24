
<?php

require_once "connection.php";


/* =================================
   GET FORM DATA
================================= */

$customerName = trim($_POST["customer_name"] ?? "");
$phone        = trim($_POST["phone"] ?? "");
$whatsapp     = trim($_POST["whatsapp"] ?? "");
$email        = trim($_POST["email"] ?? "");

$cakeSize     = floatval($_POST["cake_size"] ?? 0);
$flavour      = trim($_POST["flavour"] ?? "");
$design       = trim($_POST["design"] ?? "");
$quantity     = intval($_POST["quantity"] ?? 1);

$eventType    = trim($_POST["event_type"] ?? "");
$eventDate    = $_POST["event_date"] ?? null;
$eventTime    = $_POST["event_time"] ?? null;
$location     = trim($_POST["location"] ?? "");

$deliveryMethod =
    trim($_POST["delivery_method"] ?? "");

$deliveryFee =
    floatval($_POST["delivery_fee"] ?? 0);

$pricePerCake =
    floatval($_POST["price_per_cake"] ?? 0);

$cakeCost =
    floatval($_POST["cake_cost"] ?? 0);

$designFee =
    floatval($_POST["design_fee"] ?? 0);

$estimatedTotal =
    floatval($_POST["estimated_total"] ?? 0);

$specialRequirements =
    trim($_POST["special_requirements"] ?? "");

$status = "Pending";


/* =================================
   VALIDATION
================================= */

if (
    $customerName === "" ||
    $phone === "" ||
    $cakeSize <= 0 ||
    $flavour === ""
) {

    die(
        "Please provide the required customer and cake information."
    );

}


/* =================================
   CREATE ORDER ID
================================= */

$orderId =
    "CAKE-" .
    date("YmdHis") .
    "-" .
    random_int(100, 999);


/* =================================
   INSERT INTO DATABASE
================================= */

$sql = "
    INSERT INTO cake_orders (
        order_id,
        customer_name,
        phone,
        whatsapp,
        email,
        cake_size,
        flavour,
        design,
        quantity,
        event_type,
        event_date,
        event_time,
        location,
        delivery_method,
        delivery_fee,
        price_per_cake,
        cake_cost,
        design_fee,
        estimated_total,
        special_requirements,
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
    "sssssdssisssssdddddss",
    $orderId,
    $customerName,
    $phone,
    $whatsapp,
    $email,
    $cakeSize,
    $flavour,
    $design,
    $quantity,
    $eventType,
    $eventDate,
    $eventTime,
    $location,
    $deliveryMethod,
    $deliveryFee,
    $pricePerCake,
    $cakeCost,
    $designFee,
    $estimatedTotal,
    $specialRequirements,
    $status);

/* =================================
   SAVE ORDER
================================= */

if ($stmt->execute()) {

    header(
        "Location: cake-confirmation.php?order_id=" .
        urlencode($orderId)
    );

    exit;

} else {

    die(
        "Cake order could not be saved: " .
        $stmt->error
    );

}


$stmt->close();
$conn->close();

?>