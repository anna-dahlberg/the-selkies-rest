<?php

declare(strict_types=1);
require(__DIR__ . '/vendor/autoload.php');

// Function to check if transferCode is valid 

function isValidUuid(string $uuid): bool
{

    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }

    return true;
}

// Function to send transferCode and total cost to the central bank API
use GuzzleHttp\Client;

function transferCodeSend(string $transferCode, float $totalCost): string
{
    try {
        $client = new Client();
        $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'transferCode' => $transferCode,
                'totalCost' => $totalCost,
            ],
        ]);
        return (string) $response->getBody();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return "error: " . $e->getMessage();
    }
}



// Function to connect to database 
function connectionDatabase(): PDO
{
    $database = new PDO('sqlite:/Users/annadahlberg/dev/yrgo/assignments/the-selkies-rest/app/database/bookings.db');
    $database->exec("PRAGMA foreign_keys = ON;");
    return $database;
}
