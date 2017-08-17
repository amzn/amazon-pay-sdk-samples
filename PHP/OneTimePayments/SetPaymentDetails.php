<?php
    session_start();
    require_once "../config.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/sample.css">
    </head>

    <body>

        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start-over" href="#">Amazon Pay PHP SDK Sample: One-Time Payment Checkout</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a class="start-over" href="#">Logout and Start Over</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="jumbotron jumbotroncolor" style="padding-top:25px;" id="api-content">
                <div id="section-content">

                    <h2>Select Shipping and Payment Method</h2>
                    <p style="margin-top:20px;">Select your shipping address and payment method from the widgets below.</p>
                    <p>
                         Notice in the URL above there are several parameters available.
                         The 'access_token' should be saved in order to obtain line one and
                         two of the shipping address the buyer selects in the address widget.
                    </p>
                    <p><pre><?php echo $_GET['access_token'];?></pre></p>
                    <p>
                        This access_token is passed to the <em>GetOrderReferenceDetails</em> API
                        call to retrieve information about the Order Reference ID that is generated
                        by the widgets.
                    </p>
                    <p>
                        If you see a error message in the widgets you will need to
                        start over. This usually indicates that your session has expired. If the problem
                        persists please contact developer support.
                    </p>

                    <div class="text-center" style="margin-top:40px;">
                        <div id="addressBookWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
                        <div id="walletWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
                        <div style="clear:both;"></div>

                        <form class="form-horizontal" style="margin-top:40px;" role="form" method="post" action="ConfirmPaymentAndAuthorize.php">
                            <button id="place-order" class="btn btn-lg btn-success">Place Order</button>
                            <div id="ajax-loader" style="display:none;"><img src="images/ajax-loader.gif" /></div>
                        </form>
                    </div>

                    <p><br>
                        Notice that there are many credit cards listed in the Payment Method widget.
                        Be sure to browse all the cards.
                        The cards with red asterisks are special cards that test different decline scenarios:<br>
                        <ul>
                            <li>5656 - Simulates TransactionTimedOut reason code</li>
                            <li>4545 - Simulates PaymentMethodNotAllowed constraint</li>
                            <li>2323 - Simulates AmazonRejected reason code</li>
                            <li>3434 - Simulates InvalidPaymentMethod reason code</li>
                        </ul>
                    </p>

                </div>
            </div>

            <p>This is the live response from the previous API call.</p>
            <pre id="get_details_response">
                <div class="text-center"><img src="images/ajax-loader.gif" /></div>
            </pre>

        </div>

        <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <script type='text/javascript'>
            window.onAmazonLoginReady = function () {
                try {
                    amazon.Login.setClientId('<?php print $amazonpay_config['client_id']; ?>');
                    amazon.Login.setUseCookie(true);
                } catch (err) {
                    alert(err);
                }
            };

            window.onAmazonPaymentsReady = function () {
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",
                    onOrderReferenceCreate: function (orderReference) {

                        /* Make a call to the back-end that will SetOrderReferenceDetails
                         * and GetOrderReferenceDetails. This will set the order total
                         * to 19.95 and return order reference details.
                         */

                        var access_token = '<?php print $_GET["access_token"];?>';

                        $.post("Apicalls/GetDetails.php", {
                            orderReferenceId: orderReference.getAmazonOrderReferenceId(),
                            accessToken: access_token
                        }).done(function (data) {
                            try {
                                JSON.parse(data);
                            } catch (err) {
                            }
                            $("#get_details_response").html(data);
                        });
                    },
                    onAddressSelect: function (orderReference) {
                        // If you want to prohibit shipping to certain countries, this is where you would handle that
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function (error) {
                        // your error handling code
                        alert("AddressBook Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                    }
                }).bind("addressBookWidgetDiv");

                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",
                    onPaymentSelect: function (orderReference) {
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function (error) {
                        // your error handling code
                        alert("Wallet Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                    }
                }).bind("walletWidgetDiv");


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

            };

        </script>
        <script async="async" type='text/javascript' src="<?php echo getWidgetsJsURL($amazonpay_config); ?>"></script>
    </body>
</html>
