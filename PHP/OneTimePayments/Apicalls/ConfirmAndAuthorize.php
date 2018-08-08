<?php

session_start();

include '../../amazon-pay.phar';
require_once '../../config.php';

// Instantiate the client object with the configuration
$client = new AmazonPay\Client($amazonpay_config);

// Create the parameters array to set the order
$requestParameters = array();
$requestParameters['amazon_order_reference_id'] = $_SESSION['amazon_order_reference_id'];
$requestParameters['mws_auth_token'] = null;

// Confirm the order by making the ConfirmOrderReference API call
$response = $client->confirmOrderReference($requestParameters);

$responsearray['confirm'] = json_decode($response->toJson());

// If the API call was a success make the Authorize (with Capture) API call
if ($client->success)
{
    $requestParameters['authorization_amount'] = '19.95';
    $requestParameters['currency_code'] = 'EUR';
    $requestParameters['authorization_reference_id'] = uniqid();
    $requestParameters['seller_authorization_note'] = 'Authorizing and capturing the payment';
    $requestParameters['transaction_timeout'] = 0;
    
    // For physical goods the capture_now is recommended to be set to false
    // When set to false, you will need to make a separate Capture API call in order to get paid
    // If you are selling digital goods or plan to ship the physical good immediately, set it to true
    $requestParameters['capture_now'] = false;
    $requestParameters['soft_descriptor'] = null;

    $response = $client->authorize($requestParameters);
    $responsearray['authorize'] = json_decode($response->toJson());
}

// Echo the Json encoded array for the Ajax success
echo json_encode($responsearray);
