<?php
namespace PayWithAmazon;

session_start();

require_once '../../../PayWithAmazon/Client.php';
require_once '../../config.php';

$config = array(
    'merchant_id' => $merchant_id,
    'access_key' => $access_key,
    'secret_key' => $secret_key,
    'client_id' => $client_id,
    'region' => 'us',
    'currency_Code' => 'USD',
    'sandbox' => true
);

// Instantiate the client object with the configuration
$client = new Client($config);
$requestParameters = array();

// Required parameters
$requestParameters['amazon_billing_agreement_id'] = $_POST['amazon_billing_agreement_id'];

// Optional parameters
$requestParameters['seller_note']       = 'This is testing API call';
$requestParameters['seller_order_id']   = '123456-TestOrder-123456';
$requestParameters['store_name']        = 'Saurons collectibles in Mordor';
$requestParameters['seller_billing_agreement_id']   = '1234-example-order';
$requestParameters['custom_information']= 'any custom information';
$requestParameters['merchant_id']         = null;
$requestParameters['platform_id']       = null;
$requestParameters['mws_auth_token']    = null;

// Set the Order details by making the SetOrderReferenceDetails API call
$response = $client->setBillingAgreementDetails($requestParameters);

// If the API call was a success Get the Order Details by making the GetOrderReferenceDetails API call
if($client->success)
{
    $requestParameters['address_consent_token'] = $_POST['address_consent_token'];
    $response = $client->getBillingAgreementDetails($requestParameters);
}
// Adding the Amazon Billing Agreement ID to the session so that we can use it in ConfirmAndAuthorize.php
$_SESSION['amazon_billing_agreement_id'] = $_POST['amazon_billing_agreement_id'];

// Pretty print the Json and then echo it for the Ajax success to take in
$json = json_decode($response->toJson());
echo json_encode($json, JSON_PRETTY_PRINT);
