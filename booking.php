<?php

declare(strict_types=1);

//Connection to database 
$database = new PDO('sqlite:/Users/annadahlberg/dev/yrgo/assignments/the-selkies-rest/app/database/bookings.db');

// Enable foreign key support
$database->exec("PRAGMA foreign_keys = ON;");

//Fetch previous bookings
$statement = $database->query('SELECT * FROM bookings');
$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

//Check if form data is set
if (isset($_POST['name'], $_POST['email'], $_POST['arrivalDate'], $_POST['departureDate'], $_POST['roomType']/*, $_POST['transferCode']*/)) {

    //Trim and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name'])); //Remove white space and sanatize characters
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); //validate email
    $arrivalDate = $_POST['arrivalDate'];
    $departureDate = $_POST['departureDate'];
    $roomType = $_POST['roomType'];
    $features = isset($_POST['features']) ? $_POST['features'] : [];
    //$transferCode = htmlspecialchars(trim($_POST['transferCode']));

    if (empty($name) || !$email || empty($arrivalDate) || empty($departureDate) || empty($roomType) /*|| empty($transferCode)*/) {
        die('Required fields must be filled in and valid');
    }
    //Check for empty fields

    //Validate room types 
    $validRoomTypes = ['Budget', 'Standard', 'Luxury'];
    if (!in_array($roomType, $validRoomTypes)) {
        die('No room type selected');
    }

    //Validate dates and make sure departure date is after arrival
    if (strtotime($arrivalDate) >= strtotime($departureDate)) {
        die('Departure date must be after arrival date');
    }

    //Room-id matching
    $roomStatement = $database->prepare("SELECT id FROM rooms WHERE type = :roomType LIMIT 1");
    $roomStatement->execute([
        ':roomType' => $roomType
    ]);

    $room = $roomStatement->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        die('Room type not found');
    };
    $room_id = $room['id'];

    //Insert guest information to guest table 
    $guestStatement = $database->prepare("INSERT into guests(name, email) VALUES(:name, :email)");
    $guestStatement->execute([
        ':name' => $name,
        ':email' => $email
    ]);

    $guest_id = $database->lastInsertId();

    //Insert booking information to booking table
    $bookingStatement = $database->prepare("INSERT into bookings(guest_id, arrival_date, departure_date, room_id) VALUES(:guest_id, :arrivalDate, :departureDate, :room_id)");
    $bookingStatement->execute([
        ':guest_id' => $guest_id,
        ':arrivalDate' => $arrivalDate,
        ':departureDate' => $departureDate
    ]);

    $booking_id = $database->lastInsertId();

    //Check if guest selected any features
    if (!empty($features)) {
        $featureStatement = $database->prepare("INSERT into rooms_bookings_features(booking_id, feature_id) VALUES(:booking_id, :feature_id)");

        foreach ($features as $feature) {
            $feature_id = intval($feature);
            $featureStatement->execute([
                ':booking_id' => $booking_id,
                ':feature_id' => $feature_id
            ]);
        }
    }

    echo "Booking successfull!";
} else {

    die('Please fill out all required fields.'); //Stop script if field empty - necessary even though form field is required in html
}
