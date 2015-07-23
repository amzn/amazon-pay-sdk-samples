require 'pay_with_amazon'

# Your Login and Pay with Amazon keys are
# available in your Seller Central account.
# Be sure to store your keys in a secure location. 
merchant_id = 'YOUR_MERCHANT_ID'
access_key = 'YOUR_ACCESS_KEY'
secret_key = 'YOUR_SECRET_KEY'

client = PayWithAmazon::Client.new(
  merchant_id,
  access_key,
  secret_key,
  sandbox: true
)

# These values are grabbed from the Login and Pay
# with Amazon Address and Wallet widgets
amazon_order_reference_id = 'AMAZON_ORDER_REFERENCE_ID'
address_consent_token = 'ADDRESS_CONSENT_TOKEN'

# To get the buyers full address if shipping/tax
# calculations are needed you can use the following
# API call to obtain the order reference details.
client.get_order_reference_details(
  amazon_order_reference_id,
  address_consent_token: address_consent_token
)

# Set the amount for the transaction.
amount = '10.00'

# Make the SetOrderReferenceDetails API call to
# configure the Amazon Order Reference Id.
# There are additional optional parameters that
# are not used below.
client.set_order_reference_details(
  amazon_order_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_note: 'Your Seller Note',
  seller_order_id: 'Your Seller Order Id',
  store_name: 'Your Store Name'
)

# Make the ConfirmOrderReference API call to
# confirm the details set in the API call
# above.
client.confirm_order_reference(amazon_order_reference_id)

# Set a unique id for your current authorization
# of this payment.
authorization_reference_id = 'Your Unique Id'

# Make the Authorize API call to authorize the
# transaction. You can also capture the amount
# in this API call or make the Capture API call
# separately. There are additional optional
# parameters not used below.
response = client.authorize(
  amazon_order_reference_id,
  authorization_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_authorization_note: 'Your Authorization Note',
  transaction_timeout: 0, # Set to 0 for synchronous mode
  capture_now: true # Set this to true if you want to capture the amount in the same API call
)

# You will need the Amazon Authorization Id from the
# Authorize API response if you decide to make the
# Capture API call separately.
amazon_authorization_id = response.get_element('AuthorizeResponse/AuthorizeResult/AuthorizationDetails','AmazonAuthorizationId')

# Set a unique id for your current capture of
# this payment.
capture_reference_id = 'Your Unique Id'

# Make the Capture API call if you did not set the
# 'capture_now' parameter to 'true'. There are
# additional optional parameters that are not used
# below.
client.capture(
  amazon_authorization_id,
  capture_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_capture_note: 'Your Capture Note'
)

# Close the order reference once your one time
# transaction is complete.
client.close_order_reference(amazon_order_reference_id)
