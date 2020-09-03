NodeJS example for recurring payments.
Adapted from Python/recurring_payment example.

To run:

1. Create file config/settings.js under NodeJS/recurring_payment
   with you credentials from Seller Central.

  module.exports = {

    merchant_id:"XXXXXXXXXXXXXXXXX"

    mws_access_key:"XXXXXXXXXXXXXXXX"

    mws_secret_key:"XXXXXXXXXXXXXXXXXXXX"

    client_id:"XXXXXXXXXXXXXXXXX"

}

2. In Amazon Pay sandbox account, add http://localhost:3000 to Allowed Javascript origins

3. npm install

4. With debug messages: DEBUG=* nodemon bin/www.js

   Without debug messages: node bin/www.js

5. Server starts at http://localhost:3000

6. On the first form, just click submit. Settings have been defined in Step 1.

7. Other caveates, some raw output from the API calls will not be displayed. But you can checked the payment went through by looking at 'Manage Transactions' in your Amazon seller sandbox account.
