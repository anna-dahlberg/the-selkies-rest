<?php

declare(strict_types=1);

// Function to check if transferCode is valid 

function isValidUuid(string $uuid): bool
{

    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }

    return true;
}


//Function to connect to database 
function connectionDatabase(): PDO
{
    $database = new PDO('sqlite:/Users/annadahlberg/dev/yrgo/assignments/the-selkies-rest/app/database/bookings.db');
    $database->exec("PRAGMA foreign_keys = ON;");
    return $database;
}
