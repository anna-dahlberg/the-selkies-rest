<?php

declare(strict_types=1);
require(__DIR__ . '/functions.php');

session_start();

$errors = []; //empty array to catch errors
$username = 'anna';

//Connect to database & fetch previous bookings
$database = connectionDatabase();
$bookings = fetchBookings($database);

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

    //Validate dates and make sure departure date is not before arrival date
    if (strtotime($departureDate) < strtotime($arrivalDate)) {
        $errors[] = "Departure date cannot be before arrival date.";
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


    //Check availability
    $availabilityCheck = $database->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = :room_id AND (
        (arrival_date <= :departureDate AND departure_date >= :arrivalDate)
    )");

    $availabilityCheck->execute([
        ':room_id' => $room_id,
        ':arrivalDate' => $arrivalDate,
        ':departureDate' => $departureDate
    ]);

    if ($availabilityCheck->fetchColumn() > 0) {
        $errors[] = "The chosen room is unfortunately not available for the selected dates";
    }

    //Calculation for number of days
    $arrival = new DateTime($arrivalDate);
    $departure = new DateTime($departureDate);
    $days = ($departure->diff($arrival)->days) + 1;

    //Get the room price
    $roomPriceStatement = $database->prepare("SELECT price FROM rooms WHERE id = :room_id");
    $roomPriceStatement->execute([
        ':room_id' => $room_id
    ]);
    $roomPrice = $roomPriceStatement->fetch(PDO::FETCH_ASSOC)['price'];

    // Calculate base room cost
    $baseRoomCost = $roomPrice * $days;


    // Check for and apply discount
    $discountRate = 0;
    if ($days >= 3) {
        $discountStmt = $database->prepare("SELECT id, discount_rate FROM discounts WHERE min_days <= :days ORDER BY discount_rate DESC LIMIT 1");
        $discountStmt->execute([':days' => $days]);
        $discountResult = $discountStmt->fetch(PDO::FETCH_ASSOC);
        if ($discountResult) {
            $discountId = $discountResult['id'];
            $discountRate = $discountResult['discount_rate'];
        }
    }

    // Calculate features cost (per stay, not per day)
    $featuresTotalCost = 0;
    $validFeatures = [];

    if (!empty($features)) {
        $placeholders = str_repeat('?,', count($features) - 1) . '?';
        $featureCheck = $database->prepare("SELECT id, name, price FROM features WHERE name IN ($placeholders)");

        $featureCheck->execute($features);
        $validFeatures = $featureCheck->fetchAll(PDO::FETCH_ASSOC);

        if (count($validFeatures) !== count($features)) {
            $errors[] = "One or more selected features are invalid";
        }

        $featuresTotalCost = array_sum(array_column($validFeatures, 'price'));
    }

    // Calculate total cost
    $subtotal = $baseRoomCost + $featuresTotalCost;
    $totalCost = $subtotal - $discountRate;


    if (empty($errors)) {
        try {
            $database->beginTransaction();

            //Insert guest information to guest table 
            $guestStatement = $database->prepare("INSERT into guests(name, email) VALUES(:name, :email)");
            $guestStatement->execute([
                ':name' => $name,
                ':email' => $email
            ]);
            $guest_id = $database->lastInsertId();

            //Insert booking information to bookings table
            $bookingStatement = $database->prepare("INSERT into bookings(guest_id, arrival_date, departure_date, room_id, total_cost, discount_id) VALUES(:guest_id, :arrivalDate, :departureDate, :room_id, :total_cost, :discount_id)");
            $bookingStatement->execute([
                ':guest_id' => $guest_id,
                ':arrivalDate' => $arrivalDate,
                ':departureDate' => $departureDate,
                ':room_id' => $room_id,
                ':total_cost' => $totalCost,
                ':discount_id' => $discountId ?? null
            ]);
            $booking_id = $database->lastInsertId();

            // Insert features if any
            if (!empty($validFeatures)) {
                $featureStatement = $database->prepare(
                    "INSERT INTO rooms_bookings_features(booking_id, feature_id) VALUES(:booking_id, :feature_id)"
                );

                foreach ($validFeatures as $feature) {
                    $featureStatement->execute([
                        ':booking_id' => $booking_id,
                        ':feature_id' => $feature['id']
                    ]);
                }
            }

            // Only process transfer code after database operations succeed
            $response = transferCodeSend($transferCode, $totalCost);
            if (!isset($response['status']) || $response['status'] !== "success") {
                throw new Exception("Transfer code verification failed");
            }

            $depositResponse = makeDeposit($username, $response['transferCode']);

            $database->commit();


            $imageUrls = [
                "https://unsplash.com/photos/yak-reclining-on-grass-field-vi48b5vFtbo",
                "https://unsplash.com/photos/domestic-yak-cKLr1zNnCzg",
                "https://unsplash.com/photos/a-yak-lying-on-the-grass-KdabhKD0tmk",
                "https://unsplash.com/photos/brown-yak-on-grass-field-u3XMyl-4OSY"
            ];

            $randomImageUrl = $imageUrls[rand(0, count($imageUrls) - 1)];

            $jsonResponse = generateBookingResponse(
                "Blackthorn Isle",
                "The Selkie's Rest",
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
        } catch (Exception $e) {
            $database->rollBack();
            $errors[] = "Booking failed: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        // Send user to error page
        session_start();
        $_SESSION['errors'] = $errors;
        header('Location: views/errorPage.php');
        exit; // Stop further execution if there are errors
    }
}
