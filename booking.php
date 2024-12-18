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
    $validRoomTypes = ['budget', 'standard', 'luxury'];
    if (!in_array($roomType, $validRoomTypes)) {
        die('No room type selected');
    }

    //Validate dates and make sure departure date is after arrival
    if (strtotime($arrivalDate) >= strtotime($departureDate)) {
        die('Departure date must be after arrival date');
    }

    // Prepare data for insertion
    $featuresList = implode(',', $features); // Convert features array to a comma-separated string

    $statementGuest = $database->prepare("INSERT into guests(name, email) VALUES(:name, :email)");
    $statementBooking = $database->prepare("INSERT into bookings(guest_id, arrival_date, departure_date, room_id) VALUES(:guest_id, :arrivalDate, :departureDate, :room_id)");
    $statementFeatures = $databse->prepare("INSERT into features()");

    $statementGuest->execute([
        ':name' => $name,
        ':email' => $email
    ]);

    $guest_id = $database->lastInsertId();

    $statementBooking->execute([
        ':guest_id' => $guest_id,
        ':arrivalDate' => $arrivalDate,
        ':departureDate' => $departureDate
    ]);

    echo "Booking successfull!";
} else {

    die('Please fill out all required fields.'); //Stop script if field empty - necessary even though form field is required in html
}
