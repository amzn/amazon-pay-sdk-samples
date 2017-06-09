require 'amazon_pay'

# Your Amazon Pay keys are # available in your Seller Central account.
# Be sure to store your keys in a secure location. 
merchant_id = 'YOUR_MERCHANT_ID'
access_key = 'YOUR_ACCESS_KEY'
secret_key = 'YOUR_SECRET_KEY'

client = AmazonPay::Client.new(
  merchant_id,
  access_key,
  secret_key,
  sandbox: true
)

# These values are obtained from the Amazon Pay Address and Wallet widgets.
amazon_billing_agreement_id = 'AMAZON_BILLING_AGREEMENT_ID'
address_consent_token = 'ADDRESS_CONSENT_TOKEN'

# The following API call provides the order reference details for use cases
# where the buyer's full address is needed for shipping and tax calculations.
client.get_billing_agreement_details(
  amazon_billing_agreement_id,
  address_consent_token: address_consent_token
)

# Set the various details for this recurring payment.
# There are additional optional parameters that are not used below.
client.set_billing_agreement_details(
  amazon_billing_agreement_id,
  seller_note: 'Your Seller Note',
  seller_billing_agreement_id: 'Your Transaction Id',
  store_name: 'Your Store Name',
  custom_information: 'Additional Information'
)

# Call ConfirmBillingAgreement to confirm the Amazon Billing Agreement ID
# with the details set above once the buyer has confirmed them.
client.confirm_billing_agreement(amazon_billing_agreement_id)

# Call ValidateBillingAgreement in the future to validate that the payment
# method is still valid with the associated billing agreement ID.
client.validate_billing_agreement(amazon_billing_agreement_id)

# Set the amount for your first authorization.
amount = '10.00'

# Set a unique authorization reference ID for the first transaction on thexi
# billing agreement.
authorization_reference_id = 'Your Unique Id'

# Call AuthorizeOnBillingAgreement to charge the buyer with the 'capture_now'
# parameter set to 'true'. You can also call the Capture operation separately.
# There are additional optional parameters that are not used below.
response = client.authorize_on_billing_agreement(
  amazon_billing_agreement_id,
  authorization_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_authorization_note: 'Your Authorization Note',
  transaction_timeout: 0, # Set to 0 for synchronous mode
  capture_now: true, # Set to true to capture the amount in the same API call
  seller_note: 'Your Seller Note',
  seller_order_id: 'Your Order Id',
  store_name: 'Your Store Name',
  custom_information: 'Additional Information'
)

# You will need the Amazon Authorization ID from the AuthorizeOnBillingAgreement
# response in order to call the Capture operation separately.
amazon_authorization_id = response.get_element(
  'AuthorizeOnBillingAgreementResponse/AuthorizeOnBillingAgreementResult/AuthorizationDetails',
  'AmazonAuthorizationId'
)

# Set a unique ID for your current capture of this transaction.
capture_reference_id = 'Your Unique Id'

# Call the Capture operation if you did not set the 'capture_now' parameter
# to 'true'. There are additional optional parameters that are not used below.
client.capture(
  amazon_authorization_id,
  capture_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_capture_note: 'Your Capture Note'
)

# Call CloseBillingAgreemen when you are ready to terminate the billing
# agreement (eg - if the buyer cancels service).
client.close_billing_agreement(
  amazon_billing_agreement_id,
  closure_reason: 'Reason For Closing'
)
