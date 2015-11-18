<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="ConfirmAndAuthorize.aspx.cs" Inherits="SampleCartDemo.OneTimePayments.ConfirmAndAuthorize" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title></title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css"/>

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
                $('#ajax-loader').show();
            });
        });
    </script>
</head>
<body>
    <form id="AuthorizeandcaptureForm" runat="server">
        <div>
            <div class="container">

                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand start_over" href="index.aspx">Pay with Amazon CSHARP SDK Simple Checkout</a>
                        </div>
                        <div id="navbar" class="navbar-collapse collapse">
                            <ul class="nav navbar-nav navbar-right">
                                <li><a class="start_over" href="index.aspx">Start Over</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <div class="jumbotron jumbotroncolor" style="padding-top: 25px;" id="api-content">
                    <div id="section-content">

                        <h2>Confirm</h2>
                        <p>
                            Congratulations! You are now a proud owner of the official Pay with Amazon 
    CSHARP Software Development Kit!
                        </p>
                        <p>
                            At this point we will make the <em>Confirm</em> API call to confirm the order 
    reference and a subsequent <em>Authorize</em> and <em>Capture</em> API call. 
    If you used a test account associated with your email address you should receive 
    an email.
                        </p>

                    </div>
                </div>
                <div class="jumbotron jumbotroncodecolor" style="padding-top: 25px;" id="api-calls">
                    <h3>Code</h3>


                    <p>
                        The <em>Confirm</em> API call does not return any special values. If it were 
unsuccessful you would see an error response.
                    </p>
                    <pre runat="server" id="confirm"><code class="json"></code></pre>

                    <p>
                        The <em>Authorize</em> API call will authorize the order reference. Instead 
                        of making a separate <em>Capture</em> API call for <strong>(Recommended only for Digital Goods)</strong> we can set the <strong>CaptureNow</strong>
                        parameter to <strong>True</strong> and the funds will be captured in the same call. For <strong>Physical Goods</strong> it's highly recommended
                        to set the <strong>CaptureNow</strong> to <strong>false</strong> and then when the order is shipped call the below <strong>Capture</strong> API call to collect the payment
                    </p>
                    <pre runat="server" id="authorize"><div class="text-center"></div></pre>

                    <p>
                        The <em>Capture</em> API call will Capture the amount for the order reference.If the CaptureNow parameter was false <em>Capture</em> API call should be made after the 
                         <strong>Authorize API call</strong>
                    </p>
                    <pre runat="server" id="capture"><div class="text-center"></div></pre>
                </div>


            </div>
        </div>
    </form>
</body>
</html>
