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
use GuzzleHttp\Exception\ClientException;

function transferCodeSend(string $transferCode, int $totalCost): array
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

// {
//     try {
//         $client = new Client();
//         $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
//             'headers' => [
//                 'Content-Type' => 'application/json',
//             ],
//             'json' => [
//                 'transferCode' => $transferCode,
//                 'totalCost' => $totalCost,
//             ],
//         ]);
//         return (string) $response->getBody();
//     } catch (Exception $e) {
//         error_log($e->getMessage());
//         return "error: " . $e->getMessage();
//     }
// }



// Function to connect to database 
function connectionDatabase(): PDO
{
    $database = new PDO('sqlite:/Users/annadahlberg/dev/yrgo/assignments/the-selkies-rest/app/database/bookings.db');
    $database->exec("PRAGMA foreign_keys = ON;");
    return $database;
}
