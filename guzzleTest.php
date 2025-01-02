<?php

require __DIR__ . '/vendor/autoload.php'; // Load Guzzle

use GuzzleHttp\Client;

// Create a client to "talk" to the API
$client = new Client();

try {
    $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'transferCode' => $transferCode,
            'totalcost' => $totalCost,
        ],
    ]);

    // Display the response
    echo $response->getBody();
} catch (\GuzzleHttp\Exception\RequestException $e) {
    // Catch and display errors
    echo $e->getMessage();
}
