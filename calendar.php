<?php

header('Content-Type: application/json');

// Database connection
$pdo = new PDO('sqlite:/Users/annadahlberg/dev/yrgo/assignments/the-selkies-rest/app/database/bookings.db');

// Fetch booked dates
$query = $pdo->query("SELECT arrival_date, departure_date FROM bookings");
$bookedDates = [];

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $start = new DateTime($row['arrival_date']);
    $end = new DateTime($row['departure_date']);

    while ($start <= $end) {
        $bookedDates[] = $start->format('Y-m-d');
        $start->modify('+1 day');
    }
}

echo json_encode(['bookedDates' => $bookedDates]);
// Database connection
