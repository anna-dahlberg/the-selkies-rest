<?php

declare(strict_types=1);
require(__DIR__ . '/vendor/autoload.php');

// Function to connect to database 
function connectionDatabase(): PDO

{   //Connection to database 
    $database = new PDO('sqlite:app/database/bookings.db');

    // Enable foreign key support
    $database->exec("PRAGMA foreign_keys = ON;");
    return $database;
}

// Function to retrieve bookings

function fetchBookings(PDO $database): array
{
    $statement = $database->query('SELECT * FROM bookings');
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}



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
use GuzzleHttp\Exception\ClientException;

function transferCodeSend(string $transferCode, float $totalCost): array
{
    try {
        $client = new Client();
        $response = $client->request('POST', 'https://www.yrgopelago.se/centralbank/transferCode', [
            'form_params' => [
                'transferCode' => $transferCode,
                'totalcost' => $totalCost
            ]
        ]);

        $body = $response->getBody();
        $stringBody = (string)$body;
        $arrayBody = json_decode($stringBody, true);

        // Ensure the response is an array and return it
        return is_array($arrayBody) ? $arrayBody : ['status' => 'error', 'message' => 'Invalid response format'];
    } catch (ClientException $e) {
        $errorResponse = $e->getResponse();
        $errorContent = $errorResponse->getBody()->getContents();

        // Decode the error content and return it
        return json_decode($errorContent, true) ?: ['status' => 'error', 'message' => 'Unable to parse error response'];
    } catch (Exception $e) {
        // Handle any other exceptions
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

//Function to deposit money
use GuzzleHttp\Exception\RequestException;

function makeDeposit(string $username, string $transferCode): string
{
    $client = new Client();
    try {
        $response = $client->post('https://www.yrgopelago.se/centralbank/deposit', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'user' => $username,
                'transferCode' => $transferCode,
            ],
        ]);

        $responseBody = $response->getBody()->getContents();
        // echo "API Response: " . $responseBody . "\n";
        return $responseBody;
    } catch (RequestException $e) {
        if ($e->hasResponse()) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            return 'Error Response: ' . $errorBody;
        }
        return 'Error: ' . $e->getMessage();
    }
}


//Function to generate JSON response
function generateBookingResponse(
    string $island,
    string $hotel,
    string $arrivalDate,
    string $departureDate,
    float $totalCost,
    int $stars,
    array $features = [],
    string $greeting = "Your adventure begins here! Thank you for booking with Selkies Rest. We’re looking forward to your visit!",
    string $randomImageUrl = "",
    string $homePageUrl = "/"
) {
    $response = [
        "island" => $island,
        "hotel" => $hotel,
        "arrival_date" => $arrivalDate,
        "departure_date" => $departureDate,
        "total_cost" => number_format($totalCost, 2),
        "stars" => $stars,
        "features" => $features,
        "additional_info" => [
            "greeting" => $greeting,
            "imageUrl" => $randomImageUrl,
            "home_link" => [
                "text" => "Take me home",
                "url" => $homePageUrl
            ]
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
