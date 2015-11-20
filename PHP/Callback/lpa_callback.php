<?php
/*
 * This sample demonstrates how to handle login via callback.
 *
 * Log in to Seller Central, Integration > MWS Access Key. You should see all
 *     of your keys, including merchantId and applicationId. Enter them below.
 */

$merchantId = "";
$applicationId = "";

if (trim($merchantId) === '' || trim($applicationId) === '') {
    echo "Invalid merchantId or applicationId.";
    exit;
} else {
    /* make sure no spaces were copied on accident */
    $merchantId = trim($merchantId);
    $applicationId = trim($applicationId);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LPA Callback</title>

        <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script type="text/javascript">
            $(document).ready(function () {
                $("#logout").on("click", function () {
                    amazon.Login.logout();
                    document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                    $("#AmazonPayButton").show();
                    $("#addressBookWidgetDiv").hide();
                    $("#walletWidgetDiv").hide();
                    $("#access-token").val("");
                    $("#order-reference").val("");
                });
            });
        </script>
        <script type="text/javascript">
            window.onAmazonLoginReady = function () {
                amazon.Login.setClientId('<?php echo $applicationId; ?>');
            };
        </script>
        <script src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>
        <!--<script src='https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js'></script>-->
        <style>
            body, html {
                padding:0;
                margin:0;
                text-align:center;
            }
            .content {
                margin:25px;
            }
            .pay-button {
                display:inline-block;
                border:3px dotted #ccc;
                width:206px;
                height:51px;
            }
            #addressBookWidgetArea, #walletWidgetArea {
                width:406px;
                height:234px;
                margin:3px; padding:0;
                display:inline-block;
                border:3px dotted #ccc;
            }
            #addressBookWidgetDiv, #walletWidgetDiv {
                width:400px;
                height:228px;
            }
            .widget-area {
                text-align:center;
            }
            #logout-area {
                margin:25px;
            }
            .oro-token {
                display:inline-block;
                width:700px;
                margin:25px;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="content">
                <div class="pay-button">
                    <div id="AmazonPayButton"></div>
                </div>
                <div style="clear:both;"></div>
                <div class="oro-token">
                    <div class="input-group">
                        <span class="input-group-addon" id="oro">Order Reference Id</span>
                        <input id="order-reference" class="form-control" readonly aria-describedby="oro" style="background-color:#fff;">
                    </div>
                    <div style="clear:both; margin:5px;"></div>
                    <div class="input-group">
                        <span class="input-group-addon" id="token">Access Token</span>
                        <input id="access-token" type="text" class="form-control" readonly aria-describedby="token" style="background-color:#fff;">
                    </div>
                </div>
                <div class="widget-area">
                    <div id="addressBookWidgetArea">
                        <div id="addressBookWidgetDiv"></div>
                    </div>
                    <div id="walletWidgetArea">
                        <div id="walletWidgetDiv"></div>
                    </div>
                </div>
                <div style="clear:both;"></div>
                <div id="logout-area">
                    <button id="logout" class="btn btn-default" type="button">Log Out</button>
                </div>
            </div>

            <script type="text/javascript">
            var authRequest;
            var orderReferenceId;

            OffAmazonPayments.Button("AmazonPayButton", "<?php echo $merchantId; ?>", {
                type: "PwA",
                authorization: function () {
                    loginOptions =
                            {
                                scope: "profile postal_code payments:widget payments:shipping_address",
                                popup: true
                            };
                    authRequest = amazon.Login.authorize(loginOptions, function (v) {

                        console.log(v);
                        $("#AmazonPayButton").hide();
                        $("#addressBookWidgetDiv").show();
                        $("#walletWidgetDiv").show();
                        $("#access-token").val(v.access_token);

                        new OffAmazonPayments.Widgets.AddressBook({
                            sellerId: '<?php echo $merchantId; ?>',
                            onOrderReferenceCreate: function (orderReference) {
                                orderReferenceId = orderReference.getAmazonOrderReferenceId();
                                $("#order-reference").val(orderReferenceId);
                            },
                            onAddressSelect: function () {
                                // different address selected
                            },
                            design: {
                                designMode: 'responsive'
                            },
                            onError: function (error) {
                                // your error handling code
                            }
                        }).bind("addressBookWidgetDiv");

                        new OffAmazonPayments.Widgets.Wallet({
                            sellerId: '<?php echo $merchantId; ?>',
                            onPaymentSelect: function () {
                                // order reference already used from address book
                            },
                            design: {
                                designMode: 'responsive'
                            },
                            onError: function (error) {
                                // your error handling code
                            }
                        }).bind("walletWidgetDiv");
                    });
                },
                onError: function (error) {
                    // your error handling code
                }
            });
            </script>
        </div>
    </body>
</html>