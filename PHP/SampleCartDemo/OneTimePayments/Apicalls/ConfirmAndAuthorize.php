<?php
namespace PayWithAmazon;

session_start();

require_once '../../../PayWithAmazon/Client.php';
require_once '../../config.php';

$config = array('merchant_id'   => $merchant_id,
                'access_key'    => $access_key,
                'secret_key'    => $secret_key,
                'client_id'     => $client_id,
                'region'        => 'us',
                'currency_Code' => 'USD',
                'sandbox'       => true);

// Instantiate the client object with the configuration
$client = new Client($config);

// Create the parameters array to set the order
$requestParameters = array();
$requestParameters['amazon_order_reference_id'] = $_SESSION['amazon_order_reference_id'];
$requestParameters['mws_auth_token'] = null;

// Confirm the order by making the ConfirmOrderReference API call
$response = $client->confirmOrderReference($requestParameters);

$responsearray['confirm'] = json_decode($response->toJson());

// If the API call was a success make the Authorize (with Capture) API call
if($client->success)
{
    $requestParameters['authorization_amount'] = '19.95';
    $requestParameters['authorization_reference_id'] = uniqid('A01_REF_');
    $requestParameters['seller_Authorization_Note'] = 'Authorizing and capturing the payment';
    $requestParameters['transaction_timeout'] = 0;
    
    // For physical goods the capture_now is recommended to be set to false. The capture_now can be set to true if the order was a digital good
    $requestParameters['capture_now'] = false;
    $requestParameters['soft_descriptor'] = null;

    $response = $client->authorize($requestParameters);
    $responsearray['authorize'] = json_decode($response->toJson());
}

// Echo the Json encoded array for the Ajax success
echo json_encode($responsearray);
