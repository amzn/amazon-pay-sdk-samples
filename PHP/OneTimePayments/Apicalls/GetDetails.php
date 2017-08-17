<?php

session_start();

include '../../amazon-pay.phar';
require_once '../../config.php';

// Instantiate the client object with the configuration
$client = new AmazonPay\Client($amazonpay_config);
$requestParameters = array();

// Create the parameters array to set the order
$requestParameters['amount']            = '19.95';
$requestParameters['currency_code']     = $amazonpay_config['currency_code'];
$requestParameters['seller_note']       = 'Testing PHP SDK Samples';
$requestParameters['seller_order_id']   = '123456-TestOrder-123456';
$requestParameters['store_name']        = 'SDK Sample Store Name';
$requestParameters['seller_order_id']   = '1234-example-order';
$requestParameters['custom_information']= 'Any custom information';
$requestParameters['mws_auth_token']    = null; // only non-null if calling API on behalf of someone else
$requestParameters['amazon_order_reference_id'] = $_POST['orderReferenceId'];

// Set the Order details by making the SetOrderReferenceDetails API call
$response = $client->setOrderReferenceDetails($requestParameters);

// If the API call was a success Get the Order Details by making the GetOrderReferenceDetails API call
if ($client->success)
{
    $requestParameters['access_token'] = $_POST['accessToken'];
    $response = $client->getOrderReferenceDetails($requestParameters);
}
// Adding the Order Reference ID to the session so that we can use it in ConfirmAndAuthorize.php
$_SESSION['amazon_order_reference_id'] = $_POST['orderReferenceId'];

// Pretty print the Json and then echo it for the Ajax success to take in
$json = json_decode($response->toJson());
echo json_encode($json, JSON_PRETTY_PRINT);

?>

