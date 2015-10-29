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
    'currency_code' => 'USD',
    'sandbox' => true
);

// Instantiate the client object with the configuration
$client = new Client($config);

// Create the parameters array to set the order
$requestParameters                                = array();
$requestParameters['amazon_billing_agreement_id'] = $_SESSION['amazon_billing_agreement_id'];
$requestParameters['mws_auth_token']              = null;

// Confirm the order by making the ConfirmOrderReference API call
if ($_POST['action'] == 'confirm') {
    $response = $client->confirmBillingAgreement($requestParameters);
    $json     = json_decode($response->toJson());
    echo json_encode($json, JSON_PRETTY_PRINT);
}

if ($_POST['action'] == 'authorize') {
    // Create the parameters array to set the order
    $requestParameters = array();
    
    // Required parameters
    $requestParameters['amazon_billing_agreement_id'] = $_SESSION['amazon_billing_agreement_id'];
    $requestParameters['mws_auth_token']              = null;
    $requestParameters['authorization_amount']        = '1.99';
    $requestParameters['authorization_reference_id']  = uniqid('A01_REF_');
    
    // Optional parameters
    $requestParameters['seller_authorization_note'] = 'Authorizing and capturing the payment';
    $requestParameters['transaction_timeout']       = 0;
    $requestParameters['capture_now']               = true;
    $requestParameters['inherit_shipping_address']  = true;
    $requestParameters['seller_note']               = 'sample for Recurring Payment';
    $requestParameters['seller_order_id']           = '1234-RecExample-Order';
    $requestParameters['store_name']                = 'Saurons fair in Mordor';
    $requestParameters['custom_information']        = 'Awesome Sample';
    $requestParameters['soft_descriptor']           = null;
    $requestParameters['platform_id']               = null;
    $requestParameters['mws_auth_token']            = null;
    
    $response = $client->authorizeOnBillingAgreement($requestParameters);
    
    // Display the Billing Agreement Details Result with the bwlo parameters
    $requestParameters = array();
    $requestParameters['amazon_billing_agreement_id'] = $_SESSION['amazon_billing_agreement_id'];
    $requestParameters['address_consent_token']       = $_SESSION['access_token'];
    
    $response = $client->getBillingAgreementDetails($requestParameters);
    
    // Convert the response to Json
    $json     = json_decode($response->toJson());
    echo json_encode($json, JSON_PRETTY_PRINT);
}
