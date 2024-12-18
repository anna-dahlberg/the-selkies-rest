<?php

//Variables for information submitted in form

$name = $_POST['name'];
$email = $_POST['email'];
$arrivalDate = $_POST['arrivalDate'];
$departureDate = $_POST['departureDate'];
$roomType = $_POST['roomType'];
$features = $_POST['features'];
$transferCode = $_POST['transferCode'];


$database = new PDO('sqlite:/app/database/bookings.db');

$statement = $database->query('SELECT * from bookings');

$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);
