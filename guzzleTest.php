<?php

require 'vendor/autoload.php'; // Load Guzzle

use GuzzleHttp\Client;

// Create a client to "talk" to the API
$client = new Client(['base_uri' => 'https://www.yrgopelago.se/centralbank/']);

// Send your startcode to the API
$response = $client->request('POST', 'startCode', [
    'json' => [
        'startcode' => '6f115b9a-564b-47ff-a842-0cb2d67c445f'
    ]
]);

// Get the reply from the API
echo $response->getBody(); // Show what the server replied
