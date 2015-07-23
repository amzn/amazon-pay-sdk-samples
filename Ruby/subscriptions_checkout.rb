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
amazon_billing_agreement_id = 'AMAZON_BILLING_AGREEMENT_ID'
address_consent_token = 'ADDRESS_CONSENT_TOKEN'

# To get the buyers full address, if shipping/tax
# calculations are needed, you can use the following
# API call to obtain the billing agreement details.
client.get_billing_agreement_details(
  amazon_billing_agreement_id,
  address_consent_token: address_consent_token
)

# Next you will need to set the various details
# for this subscription with the following API call.
# There are additional optional parameters that
# are not used below.
client.set_billing_agreement_details(
  amazon_billing_agreement_id,
  seller_note: 'Your Seller Note',
  seller_billing_agreement_id: 'Your Transaction Id',
  store_name: 'Your Store Name',
  custom_information: 'Additional Information'
)

# Make the ConfirmBillingAgreement API call to confirm
# the Amazon Billing Agreement Id with the details set above.
# Be sure that everything is set correctly above before
# confirming.
client.confirm_billing_agreement(amazon_billing_agreement_id)

# The following API call is not needed at this point, but
# can be used in the future when you need to validate that
# the payment method is still valid with the associated billing
# agreement id.
client.validate_billing_agreement(amazon_billing_agreement_id)

# Set the amount for your first authorization.
amount = '10.00'

# Set a unique authorization reference id for your
# first transaction on the billing agreement.
authorization_reference_id = 'Your Unique Id'

# Now you can authorize your first transaction on the
# billing agreement id. Every month you can make the
# same API call to continue charging your buyer
# with the 'capture_now' parameter set to true. You can
# also make the Capture API call separately. There are
# additional optional parameters that are not used
# below.
response = client.authorize_on_billing_agreement(
  amazon_billing_agreement_id,
  authorization_reference_id,
  amount,
  currency_code: 'USD', # Default: USD
  seller_authorization_note: 'Your Authorization Note',
  transaction_timeout: 0, # Set to 0 for synchronous mode
  capture_now: true, # Set this to true if you want to capture the amount in the same API call
  seller_note: 'Your Seller Note',
  seller_order_id: 'Your Order Id',
  store_name: 'Your Store Name',
  custom_information: 'Additional Information'
)

# You will need the Amazon Authorization Id from the
# AuthorizeOnBillingAgreement API response if you decide
# to make the Capture API call separately.
amazon_authorization_id = response.get_element('AuthorizeOnBillingAgreementResponse/AuthorizeOnBillingAgreementResult/AuthorizationDetails','AmazonAuthorizationId')

# Set a unique id for your current capture of
# this transaction.
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

# The following API call should not be made until you
# are ready to terminate the billing agreement.
client.close_billing_agreement(
  amazon_billing_agreement_id,
  closure_reason: 'Reason For Closing'
)
