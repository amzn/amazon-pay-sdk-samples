require 'amazon_pay'

# Your Amazon Pay keys are available in your Seller Central account.
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

# These values are obtained from the Amazon Pay Address and Wallet widgets
amazon_order_reference_id = 'AMAZON_ORDER_REFERENCE_ID'
address_consent_token = 'ADDRESS_CONSENT_TOKEN'

# The following API call provides the order reference details for use cases
# where the buyer's full address is needed for shipping and tax calculations.
client.get_order_reference_details(
  amazon_order_reference_id,
  address_consent_token: address_consent_token
)

# Set the amount for the transaction.
amount = '10.00'

# Call SetOrderReferenceDetails to configure the Amazon Order Reference ID.
# There are additional optional parameters that are not used below.
client.set_order_reference_details(
  amazon_order_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_note: 'Your Seller Note',
  seller_order_id: 'Your Seller Order Id',
  store_name: 'Your Store Name'
)

# Call ConfirmOrderReference after SetOrderReferenceDetails when the buyer
# has confirmed all their order details.
client.confirm_order_reference(amazon_order_reference_id)

# Set a unique ID for your current authorization of this payment.
authorization_reference_id = 'Your Unique Id'

# Call Authorize to authorize the transaction with the payment processor.
# You can also collect the amount in this same call or call the Capture
# operation separately. There are additional optional parameters not used below.
response = client.authorize(
  amazon_order_reference_id,
  authorization_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_authorization_note: 'Your Authorization Note',
  transaction_timeout: 0, # Set to 0 for synchronous mode
  capture_now: true # Set to true to capture the amount in the same call
)

# You will need the Amazon Authorization Id from the Authorize response
# if you decide to call the Capture operation separately.
amazon_authorization_id = response.get_element(
  'AuthorizeResponse/AuthorizeResult/AuthorizationDetails',
  'AmazonAuthorizationId'
)

# Set a unique ID for your current capture of this payment.
capture_reference_id = 'Your Unique Id'

# Call Capture if you did not set the 'capture_now' parameter
# to 'true'. There are additional optional parameters that are not used below.
client.capture(
  amazon_authorization_id,
  capture_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_capture_note: 'Your Capture Note'
)

# Close the order reference once your one-time transaction is complete.
client.close_order_reference(amazon_order_reference_id)
