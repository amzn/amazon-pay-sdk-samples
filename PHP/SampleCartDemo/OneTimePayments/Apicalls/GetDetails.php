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
$requestParameters = array();

// Create the parameters array to set the order
$requestParameters['amount']            = '19.95';
$requestParameters['currency_code']     = 'USD';
$requestParameters['seller_note']       = 'This is testing API call';
$requestParameters['seller_order_id']   = '123456-TestOrder-123456';
$requestParameters['store_name']        = 'Saurons collectibles in Mordor';
$requestParameters['seller_Id']         = null;
$requestParameters['seller_order_id']   = '1234-example-order';
$requestParameters['platform_id']       = null;
$requestParameters['custom_information']= 'any custom information';
$requestParameters['mws_auth_token']    = null;
$requestParameters['amazon_order_reference_id'] = $_POST['orderReferenceId'];

// Set the Order details by making the SetOrderReferenceDetails API call
$response = $client->setOrderReferenceDetails($requestParameters);

// If the API call was a success Get the Order Details by making the GetOrderReferenceDetails API call
if($client->success)
{
    $requestParameters['address_consent_token']    = $_POST['addressConsentToken'];
    $response = $client->getOrderReferenceDetails($requestParameters);
}
// Adding the Order Reference ID to the session so that we can use it in ConfirmAndAuthorize.php
$_SESSION['amazon_order_reference_id'] = $_POST['orderReferenceId'];

// Pretty print the Json and then echo it for the Ajax success to take in
$json = json_decode($response->toJson());
echo json_encode($json, JSON_PRETTY_PRINT);
