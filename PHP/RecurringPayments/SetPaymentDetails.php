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
                        <a class="navbar-brand start-over" href="#">Amazon Pay PHP SDK Sample: Recurring Payment Checkout</a>
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
                        The 'access_token' should be saved in order to obtain address line one and
                        two of the shipping address associated with the payment method.
                    </p>
                    <p><pre><code><?php echo $_GET['access_token'];?></code></pre></p>
                    <p>
                        This is known as the address consent token. It is passed to the <em>GetBillingAgreementDetails</em> API
                        call to retrieve information about the Billing Agreement ID that is generated
                        by the widgets.
                    </p>
                    <p>
                        If you see a error message in the widgets you will need to
                        start over. This usually indicates that your session has expired. If the problem
                        persists please contact developer support.
                    </p>

                    <div class="text-center" style="margin-top:40px;">
                        <div id="addressBookWidgetDiv" style="width:320px; height:250px; display:inline-block;"></div>
                        <div id="walletWidgetDiv" style="width:320px; height:250px; display:inline-block;"></div>
                        <div id="consentWidgetDiv" style="width:320px; height:250px; display:inline-block;"></div>
                        <div style="clear:both;"></div>
                        <form class="form-horizontal" style="margin-top:40px;" role="form" method="post" action="ConfirmPaymentAndAuthorize.php">
                            <button id="confirm-subscription" class="btn btn-lg btn-success" disabled>Confirm Subscription</button>
                            <div id="ajax-loader" style="display:none;"><img src="images/ajax-loader.gif" /></div>
                        </form>
                    </div>

                    <p><br>
                        The "Confirm Subscription" button is disabled when either of the following conditions are true:
                        <ul>
                            <li>The consent checkbox allowing future purchases for this payment method is not checked.</li>
                            <li>The "4545" test credit card simulating the PaymentMethodNotAllowed constraint is selected.</li>
                        </ul>
                    </p>

                </div>

            </div>
                <div class="jumbotron jumbotroncodecolor" style="padding-top:25px;" id="api-calls">
                <p>This is the live response from the previous API call.</p>
                <pre id="get_details_response"><div class="text-center"><img src="images/ajax-loader.gif" /></div></pre>
            </div>

        </div>

        <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        
        <script type='text/javascript'>

            window.onAmazonLoginReady = function () {
                amazon.Login.setClientId('<?php print $amazonpay_config['client_id']; ?>');
                amazon.Login.setUseCookie(true);
            };

            window.onAmazonPaymentsReady = function () {
                var billingAgreementId;
                var access_token;
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",
                    agreementType: 'BillingAgreement',
                    onReady: function(billingAgreement) {
                        billingAgreementId = billingAgreement.getAmazonBillingAgreementId();
                        var access_token = "<?php echo $_GET['access_token'];?>";
                        get_details(billingAgreementId, access_token);

                        // render the consent and payment method widgets once the
                        // address book has loaded
                        new OffAmazonPayments.Widgets.Consent({
                            sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",

                            // amazonBillingAgreementId obtained from the Amazon Address Book widget.
                            amazonBillingAgreementId: billingAgreementId,

                            design: {
                                designMode: 'responsive'
                            },
                            onReady: function(billingAgreementConsentStatus) {
                            },
                            onConsent: function(billingAgreementConsentStatus) {
                                get_details(billingAgreementId, access_token);
                            },
                            onError: function (error) {
                                // your error handling code
                                alert("Consent Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                            }
                        }).bind("consentWidgetDiv");
            
                        new OffAmazonPayments.Widgets.Wallet({
                            sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",
                            amazonBillingAgreementId: billingAgreementId,
                            onPaymentSelect: function(orderReference) {
	                        get_details(billingAgreementId, access_token);
                            },
                            design: {
                                designMode: 'responsive'
                            },
                            onError: function (error) {
                                // your error handling code
                                alert("Wallet Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                            }
                        }).bind("walletWidgetDiv");
                    },
                    onAddressSelect: function (orderReference) {
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function (error) {
                        // your error handling code
                        alert("Address Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                    }
                }).bind("addressBookWidgetDiv");
        
                function get_details(billingAgreementId, access_token) {
                    $.post("Apicalls/GetDetails.php", {
                        amazon_billing_agreement_id: billingAgreementId,
                        accessToken: access_token
                    }).done(function (data) {

                        if (data) {
                            try {
                                var details = jQuery.parseJSON(data).GetBillingAgreementDetailsResult.BillingAgreementDetails;
                                var message = data;
                                if (details.Constraints) {
                                    $('#confirm-subscription').prop('disabled', true);

                                    var constraints = [];
                                    if (details.Constraints.Constraint instanceof Array) {
                                       constraints = details.Constraints.Constraint;
                                    } else {
                                        constraints[0] = details.Constraints.Constraint;
                                    }

                                    message = "<font color='red'><strong>Failed with Constraint(s):\n";
                                    constraints.forEach(function(entry) {
                                        message += entry.ConstraintID + ": " + entry.Description + "\n";
                                    });
                                    message += "</strong></font>\n" + data;
                                } else {
                                    // if there are no constraints, enable the "Confirm Subscription" button
                                    $('#confirm-subscription').prop('disabled', false);
                                }
                                $("#get_details_response").html(message);

                            } catch (err) {
                                $("#get_details_response").html(data);
                                alert(err);
                            }
                        }

                    });
                }

            };

            $(document).ready(function() {
                $('.start-over').on('click', function() {
                    amazon.Login.logout();
                    document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                    window.location = 'index.php';
                });
            });

        </script>
        <script async="async" type='text/javascript' src="<?php echo getWidgetsJsURL($amazonpay_config); ?>"></script>

    </body>
</html>
