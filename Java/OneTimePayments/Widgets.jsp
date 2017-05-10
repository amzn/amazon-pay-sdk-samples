<%@ page language="java" contentType="text/html; charset=US-ASCII"
    pageEncoding="US-ASCII"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

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
                        <a class="navbar-brand start-over" href="#">Amazon Pay Java SDK Simple Checkout</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a class="start-over" href=".">Start Over</a></li>
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

    <p>This is known as the address consent token. It is passed to the <em>GetOrderReferenceDetails</em> API
    call to retrieve information about the order reference Id that is generated
    by the widgets.</p>
    <p>If you see a error message in the widgets you will need to
    start over. This usually indicates that your session has expired. If the problem
    persists please contact <a href="https://pay.amazon.com/us/contact">developer support</a></p>


        <h2>This page demonstrates the Address Book and Wallet Widgets</h2>

        <div class="text-center" style="margin-top:40px;">
            <div id="addressBookWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
            <div id="walletWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
            <div style="clear:both;"></div>

        <form action='AuthorizeandConfirm.jsp' id='orderform' method='post'>
            <input type="hidden" name="oro_id" id="oro_id" value="">
            <input type="hidden" name="consent_token" id="consent_token" value="">
            <button id="place-order" class="btn btn-lg btn-success">Place Order</button>
            <div id="ajax-loader" style="display:none;"><img src="images/ajax-loader.gif" /></div>
        <form>

        <script>
            window.onAmazonLoginReady = function() {
                amazon.Login.setClientId('amzn1.application-oa2-client.85a1943ade6145878067573f5f45a53d');
            };

            document.getElementById("consent_token").value = decodeURI(window.location.search.match(new RegExp('(?:[\?\&]access_token=)([^&]+)'))[1]);
        </script>
        <script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>

        <div id="addressBookWidgetDiv"></div>

        <div id="walletWidgetDiv"></div>

        <script>
            new OffAmazonPayments.Widgets.AddressBook({
                sellerId: 'A1WTD9YOAS1TT0',
                onOrderReferenceCreate: function(orderReference) {
                    console.log('addressBookWidget: ' + orderReference.getAmazonOrderReferenceId());
                    document.getElementById("oro_id").value = orderReference.getAmazonOrderReferenceId();
                  //  document.write('orderReferenceId=' + orderReference.getAmazonOrderReferenceId());
                },
                onAddressSelect: function(orderReference) {
                    console.log('on address select!');
                },
                displayMode : 'Edit',
                design: {
                    designMode: 'responsive'
                },
                onReady: function(orderReference) {
                },
                onError: function(error) {
                    console.log('addressBookWidget: ' + error.getErrorMessage());
                }
            }).bind("addressBookWidgetDiv");

            new OffAmazonPayments.Widgets.Wallet({
                sellerId: 'A1WTD9YOAS1TT0',
                onPaymentSelect: function(orderReference) {
                    console.log('on payment select!');
                },
                //onOrderReferenceCreate: function(orderReference) {
                //    console.log('walletWidget: ' + orderReference.getAmazonOrderReferenceId());
                //    document.getElementById("oro_id").value = orderReference.getAmazonOrderReferenceId();
                //},
                displayMode : 'Edit',
                design: {
                    designMode: 'responsive'
                },
                onError: function(error) {
                    console.log('walletWidget: ' + error.getErrorMessage());
                }
            }).bind("walletWidgetDiv");
        </script>

    </body>
</html>
