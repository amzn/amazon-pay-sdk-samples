﻿<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="index.aspx.cs" Inherits="SampleCartDemo.OneTimePayments.index" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Amazon Pay C# sample demo</title>
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
            background: rgba(0, 153, 153, 0.3);
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
            $('#place_order').on('click', function () {
                $(this).hide();
                $('#ajax_loader').show();
            });
        });
    </script>
</head>
<body>
    <form id="samplecartdemo" runat="server">
        <div>
            <div class="container">

                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand start_over" href="#">Amazon Pay C# SDK OneTime Checkout</a>
                        </div>
                        <div id="navbar" class="navbar-collapse collapse">
                            <ul class="nav navbar-nav navbar-right">
                                <li><a class="start_over" href="#">Start Over</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <div class="jumbotron jumbotroncolor" style="padding-top: 25px;" id="api-content">
                    <div id="section-content">

                        <h2>Test Cart</h2>
                        <p style="margin-top: 20px;">
                            This is a test cart to show which calls need to 
    be made to allow a buyer to make a purchase. You will need a <strong>test account</strong>
                            before proceeding. Test accounts can be created in Seller Central.
                        </p>
                        <p>Note: This is a <strong>sandbox</strong> transaction. Your <strong>payment method</strong> will <strong>not be charged</strong>.</p>
                        <div class="panel panel-default" style="margin-top: 25px;">
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
                                                <td>
                                                    <div class="btn btn-default">
                                                        <img class="media-object" src="../images/icon.png" alt="CSHARP SDK" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <div><strong>Deluxe Amazon Pay C# Software Development Kit</strong></div>
                                                    <div>
                                                        <em>This SDK will allow you to integrate with Amazon Pay seamlessly and efforlessly. 
                        For more information visit the <a target='_new' href='https://github.com/amzn/login-and-pay-with-amazon-sdk-csharp'>Amazon Pay GitHub site</a>.</em>
                                                    </div>

                                                </td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">19.95</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="text-center" style="margin-top: 40px;" id="AmazonPayButton"></div>
                        <script type='text/javascript'>
                            window.onAmazonLoginReady = function () {
                                amazon.Login.setClientId('<%=ConfigurationManager.AppSettings["lwa_client_id"]%>');
                                amazon.Login.setUseCookie(true);
                            };
                        </script>
                        <script type='text/javascript' src='https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'></script>
                        <script type='text/javascript'>
                            var authRequest;
                            OffAmazonPayments.Button("AmazonPayButton", '<%=ConfigurationManager.AppSettings["merchant_id"]%>', {
                                type: "PwA",
                                authorization: function () {
                                    loginOptions = { scope: "profile postal_code payments:widget payments:shipping_address", popup: true };
                                    authRequest = amazon.Login.authorize(loginOptions, "SetPaymentDetails.aspx");
                                },
                                onError: function (error) {
                                    // something bad happened
                                }
                            });
                        </script>

                    </div>
                </div>

            </div>
        </div>
    </form>
</body>
</html>
