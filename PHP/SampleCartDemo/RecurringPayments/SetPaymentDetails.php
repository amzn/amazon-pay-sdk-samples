<!DOCTYPE html>
<?php
    session_start();
    if ($_GET) {
        // Store the variables in the session
        $_SESSION["access_token"] = $_GET["access_token"];
    }
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
        <script type="text/javascript" src="js/jquery.knob.min.js"></script>
        <script type="text/javascript" src="js/notify.min.js"></script>
        
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
                background:rgba(0, 153, 153, 0.1);
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
        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start-over" href="#">Pay with Amazon PHP SDK Simple Recurring Payment</a>
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
    <p><pre><code><?php echo $_GET['access_token'];?></code></pre></p>
    <p>This is known as the address consent token. It is passed to the <em>GetBillingAgreementDetails</em> API 
    call to retrieve information about the billing agreement Id that is generated 
    by the widgets.</p>
    <p>If you see a error message in the widgets you will need to 
    start over. This usually indicates that your session has expired. If the problem 
    persists please contact developer support.</p>
    
    <div class="text-center" style="margin-top:40px;">
        <div id="addressBookWidgetDiv" style="width:320px; height:250px; display:inline-block;"></div>
        <div id="walletWidgetDiv" style="width:320px; height:250px; display:inline-block;"></div>
        <div id="consentWidgetDiv" style="width:320px; height:250px; display:inline-block;"></div>
        <div style="clear:both;"></div>
        <form class="form-horizontal" style="margin-top:40px;" role="form" method="post" action="ConfirmPaymentAndAuthorize.php">
            <button id="place-order" class="btn btn-lg btn-success">Confirm Subscription</button>
            <div id="ajax-loader" style="display:none;"><img src="images/ajax-loader.gif" /></div>
        </form>
    </div>
    <script type="text/javascript">
        $('#place-order').prop('disabled', true);
        var billingAgreementId;
        var access_token;
        new OffAmazonPayments.Widgets.AddressBook({
            sellerId: "YOUR_MERCHANT_ID",
            agreementType: 'BillingAgreement',
            onReady: function (billingAgreement) {
                billingAgreementId = billingAgreement.getAmazonBillingAgreementId();
                var access_token = "<?php echo $_SESSION['access_token'];?>";
                get_details(billingAgreementId,access_token);
    
                // render the consent and payment method widgets once the 
                // address book has loaded
                new OffAmazonPayments.Widgets.Consent({
                    sellerId: "YOUR_MERCHANT_ID",
                    // amazonBillingAgreementId obtained from the Amazon Address Book widget.
                    amazonBillingAgreementId: billingAgreementId,
                    design: {
                        designMode: 'responsive'
                    },
                    onReady: function (billingAgreementConsentStatus) {
                        // Called after widget renders
                        // getConsentStatus returns true or false
                        // true Ð checkbox is selected
                        // false Ð checkbox is unselected - default
                    },
                    onConsent: function (billingAgreementConsentStatus) {
                        buyerBillingAgreementConsentStatus = billingAgreementConsentStatus.getConsentStatus();
                        
                        if(buyerBillingAgreementConsentStatus == 'true') {
                            $('#place-order').prop('disabled', false);
                        } else {
                            $('#place-order').prop('disabled', true);
                        }
                        
                        get_details(billingAgreementId, access_token);
                        // getConsentStatus returns true or false
                        // true Ð checkbox is selected Ð buyer has consented
                        // false Ð checkbox is unselected Ð buyer has not consented
    
                        // Replace this code with the action that you want to perform
                        // after the consent checkbox is selected/unselected.
                    },
                    onError: function (error) {
                        // your error handling code
                    }
                }).bind("consentWidgetDiv");
    
                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: "YOUR_MERCHANT_ID",
                    amazonBillingAgreementId: billingAgreementId,           
                    onPaymentSelect: function (orderReference) {
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function (error) {
                        // your error handling code
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
            }
        }).bind("addressBookWidgetDiv");
        
        function get_details(billingAgreementId, access_token) {
            $.post("Apicalls/GetDetails.php", {
                amazon_billing_agreement_id: billingAgreementId,
                address_consent_token: access_token
            }).done(function (data) {
                $("#get_details_response").html(data);
            });
        }
    </script>

                </div>
            </div>
            <div class="jumbotron jumbotroncodecolor" style="padding-top:25px;" id="api-calls">


<p>This is the live response from the previous API call.</p> 
<pre id="get_details_response"><div class="text-center"><img src="/images/ajax-loader.gif" /></div></pre>

            </div>            
        
        </div>
    </body>
</html>