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
                        <a class="navbar-brand" href="#">Amazon Pay PHP SDK Sample: Recurring Payment Checkout</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a id="Logout" href="#">Logout and Start Over</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="jumbotron jumbotroncolor" style="padding-top:25px;" id="api-content">
                <div id="section-content">
                
                    <h2>Test Cart</h2>
                    <p style="margin-top:20px;">
                        This is a test cart to show which calls need to
                        be made to allow a buyer to make a purchase. You will need a <strong>test account</strong>
                        before proceeding. Test accounts can be created in Seller Central.
                    </p>
                    <p>Note: This is a <strong>sandbox</strong> transaction. Your <strong>payment method</strong> will <strong>not be charged</strong>.</p>

                    <div class="panel panel-default" style="margin-top:25px;">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Description</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><div class="btn btn-default"><img class="media-object" src="images/icon.png" alt="PHP SDK"></div></td>
                                            <td>
                                                <div><strong>
                                                    Amazon Pay PHP SDK Monthly Newsletter
                                                </strong></div>
                                                <div><em>
                                                    Stay up to late with the latest changes with our monthly newsletter.<br>
                                                    For more information visit the <a target='_new' href='https://github.com/amzn/amazon-pay-sdk-php'>Amazon Pay PHP SDK GitHub</a>.
                                                </em></div>
                                            </td>
                                            <td class="text-center">1</td>
                                            <td class="text-center">19.95</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="text-center" style="margin-top:40px;" id="AmazonPayButton"></div>

                </div>
            </div>
        </div>

        <script type='text/javascript'>
            window.onAmazonLoginReady = function () {
                try {
                    amazon.Login.setClientId("<?php echo $amazonpay_config['client_id']; ?>");
                    amazon.Login.setUseCookie(true);
                } catch (err) {
                    alert(err);
                }
            };

            window.onAmazonPaymentsReady = function () {
                var authRequest;
                OffAmazonPayments.Button("AmazonPayButton", "<?php echo $amazonpay_config['merchant_id']; ?>", {
                    type: "PwA",       // PwA, Pay, A, LwA, Login
                    color: "DarkGray", // Gold, LightGray, DarkGray
                    size: "medium",    // small, medium, large, x-large
                    language: "en-GB", // for Europe/UK regions only: en-GB, de-DE, fr-FR, it-IT, es-ES
                    authorization: function() {
                        loginOptions = { scope: "profile postal_code payments:widget payments:shipping_address", popup: true };
                        authRequest = amazon.Login.authorize(loginOptions, "SetPaymentDetails.php");
                    },
                    onError: function(error) {
                        // something bad happened
                    }
                });

                document.getElementById('Logout').onclick = function() {
                    amazon.Login.logout();
                    document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                    window.location = 'index.php';
                };

            };
        </script>
        <script async="async" type='text/javascript' src="<?php echo getWidgetsJsURL($amazonpay_config); ?>"></script>

    </body>
</html>
