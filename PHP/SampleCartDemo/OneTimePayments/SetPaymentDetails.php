<!DOCTYPE html>
<?php
    session_start();
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
        <link rel="stylesheet" href="css/prism.css">

        <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/prism.js"></script>

        <style>
            body {
                padding-top: 40px;
                padding-bottom: 50px;
            }
            .lpa-sdk {
                padding: 40px 15px;
                text-align: center;
            }
            .input-group {
                margin-bottom:10px;
            }
            #go-home {
                cursor:pointer;
            }
            pre code {
                overflow:scroll;
                word-wrap:normal;
                white-space:pre;
            }
            .jumbotroncolor {
                background:rgba(0, 153, 153, 0.15);
            }
            .jumbotroncodecolor {
                background:rgba(255, 204, 153, 0.4);
            }
        </style>

        <script type='text/javascript'>
        $(document).ready(function() {
            $('.start-over').on('click', function() {
                amazon.Login.logout();
                document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                window.location = 'index.php';
            });
            $('#place-order').on('click', function() {
                $(this).hide();
                $('#ajax-loader').show();
            });
        });
        </script>

    <script type='text/javascript'>
        window.onAmazonLoginReady = function () {
            amazon.Login.setClientId('YOUR_LOGIN_WITH_AMAZON_CLIENT_ID');
            amazon.Login.setUseCookie(true);
        };
    </script>
    <script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>

    </head>
    <body>
        <input type="hidden" id="mws_access_key" value="">
        <input type="hidden" id="mws_secret_key" value="">
        <input type="hidden" id="merchant_id" value="">
        <input type="hidden" id="client_id" value="">
        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start-over" href="#">Pay with Amazon PHP SDK Simple Checkout</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a class="start-over" href="#">Start Over</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="jumbotron jumbotroncolor" style="padding-top:25px;" id="api-content">
                <div id="section-content">

    <h2>Select Shipping and Payment Method</h2>
    <p style="margin-top:20px;">Select your billing address and payment method
    from the widgets below.</p>
    <p>Notice in the URL above there are several parameters available.
    The 'access_token' should be saved in order to obtain address line one and
    two of the shipping address associated with the payment method.</p>
    <p><pre><?php echo $_GET['access_token'];?></pre></p>
    <p>This is known as the address consent token. It is passed to the <em>GetOrderReferenceDetails</em> API
    call to retrieve information about the order reference Id that is generated
    by the widgets.</p>
    <p>If you see a error message in the widgets you will need to
    start over. This usually indicates that your session has expired. If the problem
    persists please contact developer support.</p>

    <div class="text-center" style="margin-top:40px;">
        <div id="addressBookWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
        <div id="walletWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
        <div style="clear:both;"></div>
        <form class="form-horizontal" style="margin-top:40px;" role="form" method="post" action="ConfirmPaymentAndAuthorize.php">
            <button id="place-order" class="btn btn-lg btn-success">Place Order</button>
            <div id="ajax-loader" style="display:none;"><img src="images/ajax-loader.gif" /></div>
        </form>
    </div>
    <script type="text/javascript">
        new OffAmazonPayments.Widgets.AddressBook({
            sellerId: "YOUR_MERCHANT_ID",
            onOrderReferenceCreate: function (orderReference) {

                /* make a call to the back-end that will set order reference details
                 * and get order reference details. This will set the order total
                 * to 19.95 and return order reference details.
                 *
                 * Get the AddressConsentToken to be sent to the API call
                 */
               var access_token = "";

                $.post("Apicalls/GetDetails.php", {
                    orderReferenceId: orderReference.getAmazonOrderReferenceId(),
                    addressConsentToken: access_token,
                }).done(function (data) {
                   $("#get_details_response").html(data);
                });
            },
            onAddressSelect: function (orderReference) {
            },
            design: {
                designMode: 'responsive'
            },
            onError: function (error) {
                // your error handling code
            }
        }).bind("addressBookWidgetDiv");

        new OffAmazonPayments.Widgets.Wallet({
            sellerId: "YOUR_MERCHANT_ID",
            onPaymentSelect: function (orderReference) {
            },
            design: {
                designMode: 'responsive'
            },
            onError: function (error) {
                // your error handling code
            }
        }).bind("walletWidgetDiv");
    </script>

                </div>
            </div>
<p>This is the live response from the previous API call.</p>
<pre id="get_details_response"><div class="text-center"><img src="/images/ajax-loader.gif" /></div></pre>

            </div>

        </div>
    </body>
</html>