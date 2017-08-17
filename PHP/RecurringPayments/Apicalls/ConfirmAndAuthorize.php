<?php

session_start();

include '../../amazon-pay.phar';
require_once '../../config.php';

// Instantiate the client object with the configuration
$client = new AmazonPay\Client($amazonpay_config);
$requestParameters = array();

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
    $requestParameters['authorization_amount']        = '150.00';
    $requestParameters['authorization_reference_id']  = uniqid('A01_REF_');
    
    // Optional parameters
    $requestParameters['seller_authorization_note'] = 'Authorizing and capturing the payment';
    $requestParameters['transaction_timeout']       = 0;
    $requestParameters['capture_now']               = true;
    $requestParameters['inherit_shipping_address']  = true;
    $requestParameters['seller_note']               = 'sample for Recurring Payment';
    $requestParameters['seller_order_id']           = '1234-RecExample-Order';
    $requestParameters['store_name']                = 'PHP SDK Sample';
    $requestParameters['custom_information']        = 'Awesome Sample';
    $requestParameters['soft_descriptor']           = null;
    $requestParameters['platform_id']               = null;
    $requestParameters['mws_auth_token']            = null;
    
    $response = $client->authorizeOnBillingAgreement($requestParameters);
    $responsearray['authorize'] = json_decode($response->toJson());

    // Display the Billing Agreement Details Result with the below parameters
    $requestParameters = array();
    $requestParameters['amazon_billing_agreement_id'] = $_SESSION['amazon_billing_agreement_id'];
    $requestParameters['address_consent_token']       = $_SESSION['access_token'];
    
    $response = $client->getBillingAgreementDetails($requestParameters);
    $responsearray['details'] = json_decode($response->toJson());
    
    // Echo the Json encoded array for the Ajax success
    echo json_encode($responsearray);
}
