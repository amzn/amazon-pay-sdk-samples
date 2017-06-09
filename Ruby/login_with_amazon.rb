require 'amazon_pay'

# Your client id is located in your Seller
# Central account. Be sure to store your keys
# in a secure location. 
client_id = 'Your Client Id'

login = AmazonPay::Login.new(
  client_id,
  region: :na,  # Default: :na
  sandbox: true # Default: false
)

# The access token is available in the return URL
# parameters after a user has logged in.
access_token = 'User Access Token'

# Make the 'get_login_profile' api call.
profile = login.get_login_profile(access_token)

name = profile['name']
email = profile['email']
user_id = profile['user_id']
