<?php

//Handle request from payment.html
function HandleRequest($paymentRequest) {
    //Get the auth token
    $authToken = GetAuthToken();

    //Create urlhash
    $urlString = md5('passPhrase') . '5265f8eed6a19' . 'https://www.example.com/return.php' . 'https://www.example.com/notify.php';
    $urlHash = md5(strtolower($urlString));

    //Assemble request arguments
    $postData = array (
      'method' => 'processCard',
      'merchantUUID' => '5265f8eed6a19',
      'apiKey' => 'ksmnwxab',
      'accessToken' => $authToken,
      'transactionAmount' => $paymentRequest['amount'],
      'transactionCurrency' => $paymentRequest['currency'],
      'transactionProduct' => $paymentRequest['product'],
      'returnURL' => 'https://www.example.com/return.php',
      'notifyURL' => 'https://www.example.com/notify.php',
      'urlHash' => $urlHash,
      'hashSalt' => $hashSalt,
      'customerName' => $paymentRequest['name'],
      'customerCountry' => $paymentRequest['country'],
      'customerState' => $paymentRequest['state'],
      'customerCity' => $paymentRequest['city'],
      'customerAddress' => $paymentRequest['address'],
      'customerPostCode' => $paymentRequest['postCode'],
      'paymentCardNumber' => $paymentRequest['c_number'],
      'paymentCardExpiry' => $paymentRequest['c_expiry'],
      'paymentCardName' => $paymentRequest['c_name'],
    );

    //Send curl request, which redirects to return.php
    $response = SendCurlRequest($postData);
}

function GetAuthToken() {
    //Create urlhash
    $urlString = md5('passPhrase') . '5265f8eed6a19' . 'https://www.example.com/return.php' . 'https://www.example.com/notify.php';
    $urlHash = md5(strtolower($urlString));

    //Assemble request arguments
    $postData = array (
      'method' => 'getAccessToken',
      'merchantUUID' => '5265f8eed6a19',
      'apiKey' => 'ksmnwxab',
      'urlHash' => $urlHash
    );

    //Send curl request, and get back response
    $authResponse = SendCurlRequest($postData);

    $authToken = $authResponse['responseData']['token'];

    //return authentication token
    return $authToken;
}

function SendCurlRequest($data)
{
    // Setup the POST url
    define('MW_API_ENDPOINT', 'https://api.merchantwarrior.com/transfer/');

    // Setup POST data
    $postData = $data;

    // Setup CURL defaults
    $curl = curl_init();

    // Setup CURL params for this request
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, MW_API_ENDPOINT);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));

    // Run CURL
    $response = curl_exec($curl);
    $error = curl_error($curl);

    // Check for CURL errors
    if (isset($error) && strlen($error)) {
        throw new Exception("CURL Error: {$error}");
    }

    // Parse the XML
    $xml = simplexml_load_string($response);

    // Convert the result from a SimpleXMLObject into an array
    $xml = (array)$xml;

    // Validate the response - the only successful code is 0
    $status = ((int)$xml['responseCode'] === 0) ? true : false;

    // Make the response a little more useable
    $result = array (
        'status' => $status,
        'transactionID' => (isset($xml['transactionID']) ? $xml['transactionID'] : null),
        'responseData' => $xml
    );

    return $result;
}

?>
