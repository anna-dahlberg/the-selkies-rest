<?php

declare(strict_types=1);
require(__DIR__ . '/functions.php');

$errors = []; //empty array to catch errors
$username = 'anna';

//Connection to database 
$database = new PDO('sqlite:/Users/annadahlberg/dev/yrgo/assignments/the-selkies-rest/app/database/bookings.db');

// Enable foreign key support
$database->exec("PRAGMA foreign_keys = ON;");

//Fetch previous bookings
$statement = $database->query('SELECT * FROM bookings');
$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

//Check if form data is set
if (isset($_POST['name'], $_POST['email'], $_POST['arrivalDate'], $_POST['departureDate'], $_POST['roomType'], $_POST['transferCode'])) {

    //Trim and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name'])); //Remove white space and sanatize characters
    $email = trim($_POST['email']); //validate email
    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL); //sanitize email
    $arrivalDate = $_POST['arrivalDate'];
    $departureDate = $_POST['departureDate'];
    $roomType = ucfirst(strtolower($_POST['roomType']));

    $features = isset($_POST['features']) ? $_POST['features'] : [];
    $transferCode = htmlspecialchars(trim($_POST['transferCode']));

    if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) { //Validate email
        $errors[] = "Please enter a valid e-mail adress";
    }

    //Check for empty fields
    if (empty($name) || !$email || empty($arrivalDate) || empty($departureDate) || empty($roomType) /*|| empty($transferCode)*/) {
        $errors[] = "Please fill in all the required fields.";
    }

    //Validate room types 
    $validRoomTypes = ['Budget', 'Standard', 'Luxury'];
    if (!in_array($roomType, $validRoomTypes)) {
        $errors[] = "Please choose a valid room type";
    }

    //Validate dates and make sure departure date is after arrival
    if (strtotime($arrivalDate) >= strtotime($departureDate)) {
        $errors[] = "Departure date must be after arrival date";
    }

    if (strtotime($arrivalDate) < strtotime('today')) {
        $errors[] = "Cannot book dates in the past";
    }

    //Validate transferCode
    if (!isValidUuid($_POST['transferCode'])) {
        $errors[] = "Invalid transfer code format";
    }

    //Room-id matching
    $roomStatement = $database->prepare("SELECT id FROM rooms WHERE type = :roomType LIMIT 1");
    $roomStatement->execute([
        ':roomType' => $roomType
    ]);

    $room = $roomStatement->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        echo $roomType;
        $errors[] = "Room type not found";
    };
    $room_id = $room['id'];

    //Insert guest information to guest table 
    $guestStatement = $database->prepare("INSERT into guests(name, email) VALUES(:name, :email)");
    $guestStatement->execute([
        ':name' => $name,
        ':email' => $email
    ]);

    $guest_id = $database->lastInsertId();

    //Check availability
    $availabilityCheck = $database->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = :room_id AND (
            (arrival_date <= :arrivalDate AND departure_date > :arrivalDate)
        OR (arrival_date < :departureDate AND departure_date >= :departureDate)
        OR (arrival_date >= :arrivalDate AND departure_date <= :departureDate)
    )");

    $availabilityCheck->execute([
        ':room_id' => $room_id,
        ':arrivalDate' => $arrivalDate,
        ':departureDate' => $departureDate
    ]);

    if ($availabilityCheck->fetchColumn() > 0) {
        $errors[] = "The chosen room is unfortunately not available for the selected dates";
    }


    //Insert booking information to booking table
    $bookingStatement = $database->prepare("INSERT into bookings(guest_id, arrival_date, departure_date, room_id) VALUES(:guest_id, :arrivalDate, :departureDate, :room_id)");
    $bookingStatement->execute([
        ':guest_id' => $guest_id,
        ':arrivalDate' => $arrivalDate,
        ':departureDate' => $departureDate,
        ':room_id' => $room_id
    ]);

    $booking_id = $database->lastInsertId();

    //Calculation for number of nights
    $arrival = new DateTime($arrivalDate);
    $departure = new DateTime($departureDate);
    $nights = $departure->diff($arrival)->days;

    //Get the room price
    $roomPriceStatement = $database->prepare("SELECT price FROM rooms WHERE id = :room_id");
    $roomPriceStatement->execute([
        ':room_id' => $room_id
    ]);
    $roomPrice = $roomPriceStatement->fetch(PDO::FETCH_ASSOC)['price'];

    // Calculate base room cost
    $baseRoomCost = $roomPrice * $nights;

    // Calculate features cost (per stay, not per night)
    $featuresTotalCost = 0;
    if (!empty($features)) {
        $featurePriceStatement = $database->prepare("SELECT SUM(price) as total FROM features WHERE name IN (" . str_repeat('?,', count($features) - 1) . "?)");
        $featurePriceStatement->execute($features);
        $featuresTotalCost = $featurePriceStatement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Check for and apply discount
    $discountRate = 0;
    if ($nights >= 3) {
        $discountStmt = $database->prepare("SELECT discount_rate FROM discounts WHERE min_nights <= :nights ORDER BY discount_rate DESC LIMIT 1");
        $discountStmt->execute([':nights' => $nights]);
        $discountResult = $discountStmt->fetch(PDO::FETCH_ASSOC);
        if ($discountResult) {
            $discountRate = $discountResult['discount_rate'];
        }
    }

    // Calculate total cost
    $subtotal = $baseRoomCost + $featuresTotalCost;
    $totalCost = $subtotal - $discountRate;

    // Update booking with cost information
    $updateBookingStmt = $database->prepare("UPDATE bookings SET total_cost = :total_cost WHERE id = :booking_id");
    $updateBookingStmt->execute([
        ':total_cost' => $totalCost,
        ':booking_id' => $booking_id
    ]);

    // Send the transfer code and total cost to the API
    $response = transferCodeSend($transferCode, $totalCost);
    $depositResponse = "Not attempted"; // Initialize variable


    // Check if the API response is successful
    if (isset($response['status']) && $response['status'] === "success") {
        $transferCode = $response['transferCode'];

        //Make deposit 
        $depositResponse = makeDeposit($username, $transferCode);

        // Check if guest selected any features
        if (!empty($features)) {
            $featureStatement = $database->prepare("INSERT INTO rooms_bookings_features(booking_id, feature_id) VALUES(:booking_id, :feature_id)");

            foreach ($features as $feature) {
                // Query the features table to get the feature ID based on feature name
                $featureCheck = $database->prepare("SELECT id FROM features WHERE name = :feature LIMIT 1");
                $featureCheck->execute([':feature' => $feature]);
                $featureData = $featureCheck->fetch(PDO::FETCH_ASSOC);

                if (!$featureData) {
                    $errors[] = "Chosen feature $feature is invalid";
                }

                $feature_id = $featureData['id']; // Now we have the correct feature ID

                // Insert the feature into rooms_bookings_features
                $featureStatement->execute([
                    ':booking_id' => $booking_id,
                    ':feature_id' => $feature_id
                ]);
            }
        }
    }
    // Check if there are any errors
    if (!empty($errors)) {
        // Display errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit; // Stop further execution if there are errors
    }

    $imageUrls = [
        "https://unsplash.com/photos/yak-reclining-on-grass-field-vi48b5vFtbo",
        "https://unsplash.com/photos/domestic-yak-cKLr1zNnCzg",
        "https://unsplash.com/photos/a-yak-lying-on-the-grass-KdabhKD0tmk",
        "https://unsplash.com/photos/brown-yak-on-grass-field-u3XMyl-4OSY"
    ];

    $randomImageUrl = $imageUrls[rand(0, count($imageUrls) - 1)];

    $jsonResponse = generateBookingResponse(
        "Blackthorn Isle",
        "The Selkie\'s Rest",
        $arrivalDate,
        $departureDate,
        $totalCost,
        3,
        array_map(fn($feature) => ["name" => $feature, "cost" => 2.0], $features),
        "Your adventure begins here! Thank you for booking with Selkies Rest. We’re looking forward to your visit!",
        $randomImageUrl
    );

    // If no errors, display success message with responses
    header('Content-Type: application/json');
    exit;
} else {

    die('Please fill out all required fields.'); //Stop script if field empty - necessary even though form field is required in html
}
