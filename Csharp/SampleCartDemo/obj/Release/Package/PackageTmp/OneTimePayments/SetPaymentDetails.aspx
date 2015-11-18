<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="SetPaymentDetails.aspx.cs" Inherits="SampleCartDemo.OneTimePayments.SetPaymentDetails" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title></title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css" />

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
            margin-bottom: 10px;
        }

        #go-home {
            cursor: pointer;
        }

        pre code {
            overflow: scroll;
            word-wrap: normal;
            white-space: pre;
        }

        .jumbotroncolor {
            background: rgba(0, 153, 153, 0.15);
        }

        .jumbotroncodecolor {
            background: rgba(255, 204, 153, 0.4);
        }
    </style>

    <script type='text/javascript'>
        $(document).ready(function () {
            $('.start_over').on('click', function () {
                amazon.Login.logout();
                document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                window.location = 'index.aspx';
            });
            $('#place-order').on('click', function () {
                $(this).hide();
                $('#ajax-loader').show();
            });
        });
    </script>

    <script type='text/javascript'>
        window.onAmazonLoginReady = function () {
            amazon.Login.setClientId('<%=ConfigurationManager.AppSettings["lwa_client_id"]%>');
            amazon.Login.setUseCookie(true);
        };
    </script>
    <script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>
</head>
<body>
    <form id="setpaymentdetails" runat="server">

        <input type="hidden" id="address_consent_token" value="" />
        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start_over" href="index.aspx">Pay with Amazon C# SDK Simple Checkout</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbakr-nav navbar-right">
                            <li><a class="start_over" href="index.aspx">Start Over</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="jumbotron jumbotroncolor" style="padding-top: 25px;" id="api-content">
                <div id="section-content">

                    <h2>Select Shipping and Payment Method</h2>
                    <p style="margin-top: 20px;">
                        Select your billing address and payment method
    from the widgets below.
                    </p>
                    <p>
                        Notice in the URL above there are several parameters available.
    The 'access_token' should be saved in order to obtain address line one and
    two of the shipping address associated with the payment method.
                    </p>
                    <p>
                        <pre><textarea runat="server" id="access_token_text" class="form-control" readonly="readonly" rows="4"></textarea></pre>
                    </p>
                    <p>
                        This is known as the address consent token. It is passed to the <em>GetOrderReferenceDetails</em> API
    call to retrieve information about the order reference Id that is generated
    by the widgets.
                    </p>
                    <p>
                        Amazon OrderReference ID is shown below. This was generated when the Address Book Widget loaded - onOrderReferenceCreate function.
                        <pre><textarea runat="server" id="amazon_order_reference_id" class="form-control" readonly="readonly" rows="1"></textarea></pre>
                    </p>
                    <p>
                        If you see a error message in the widgets you will need to
    start over. This usually indicates that your session has expired. If the problem
    persists please contact developer support.
                    </p>

                    <div class="text-center" style="margin-top: 40px;">
                        <div id="addressBookWidgetDiv" style="width: 400px; height: 240px; display: inline-block;"></div>
                        <div id="walletWidgetDiv" style="width: 400px; height: 240px; display: inline-block;"></div>
                        <div style="clear: both;"></div>
                        <div class="form-group">
                            <div class="col-md-10">
                                <asp:Button ID="place_order" class="btn btn-success" runat="server" Text="Place Order" OnClick="PlaceOrder" />
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var oro = "";
                        var access_token = "";
                        new OffAmazonPayments.Widgets.AddressBook({
                            sellerId: '<%=ConfigurationManager.AppSettings["merchant_id"]%>',
                            onOrderReferenceCreate: function (orderReference) {

                                /* make a call to the back-end that will set order reference details
                                 * and get order reference details. This will set the order total
                                 * to 19.95 and return order reference details.
                                 *
                                 * Get the AddressConsentToken to be sent to the API call
                                 */

                                $("#amazon_order_reference_id").html(orderReference.getAmazonOrderReferenceId());
                                oro = orderReference.getAmazonOrderReferenceId();
                            },
                            onAddressSelect: function (orderReference) {
                                $.ajax({
                                    type: "POST",
                                    url: "SetPaymentDetails.aspx/MakeApiCallAndReturnJsonResponse",
                                    contentType: "application/json",
                                    data: JSON.stringify({
                                        amazonOrderReferenceId: oro,
                                        amount: "19.95",
                                        addressConsentToken: access_token
                                    }),
                                    dataType: "json",
                                    cache: false,
                                    success: function (data) {
                                        $.each(data.d, function (key, value) {
                                            if (key == "getOrderReferenceDetailsResponse") {
                                                $("#get_details_response").html(value);
                                            }
                                            else if (key == "setOrderReferenceDetailsResponse") {
                                                $("#setOrderReferenceDetailsResponse").html(value);
                                            }
                                        });
                                    }
                                });
                            },
                            design: {
                                designMode: 'responsive'
                            },
                            onError: function (error) {
                                // your error handling code
                            }
                        }).bind("addressBookWidgetDiv");

                        new OffAmazonPayments.Widgets.Wallet({
                            sellerId: '<%=ConfigurationManager.AppSettings["merchant_id"]%>',
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
            <p>This is the live response from the Set Order Reference Details API call.</p>
            <pre runat="server" id="setOrderReferenceDetailsResponse"><div class="text-center"><img src="../images/ajax-loader.gif" /></div></pre>
            <br />
            <br />
            <p>This is the live response from the Get Order Reference Details API call.</p>
            <pre runat="server" id="get_details_response"><div class="text-center"><img src="../images/ajax-loader.gif" /></div></pre>

        </div>

    </form>
</body>
</html>
