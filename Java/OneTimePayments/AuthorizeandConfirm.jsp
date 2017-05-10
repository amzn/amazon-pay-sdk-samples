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
            code, samp, kbd {
          	font-family: "Courier New", Courier, monospace, sans-serif;
          	text-align: left;
          	color: #555;
          	}
            pre code {
            	line-height: 1.6em;
            	font-size: 11px;
            	}
            pre {
            	padding: 0.1em 0.5em 0.3em 0.7em;
            	border-left: 11px solid #ccc;
            	margin: 1.7em 0 1.7em 0.3em;
            	overflow: auto;
            	width: 93%;
            	}
            /* target IE7 and IE6 */
            *:first-child+html pre {
            	padding-bottom: 2em;
            	overflow-y: hidden;
            	overflow: visible;
            	overflow-x: auto;
            	}
            * html pre {
            	padding-bottom: 2em;
            	overflow: visible;
            	overflow-x: auto;
            	}
            .jumbotroncolor {
                background:rgba(0, 153, 153, 0.3);
            }
            .jumbotroncodecolor {
                background:rgba(255, 204, 153, 0.4);
            }
            #wordwrap {
                 white-space: pre-wrap;      /* CSS3 */
                 white-space: -moz-pre-wrap; /* Firefox */
                 white-space: -pre-wrap;     /* Opera <7 */
                 white-space: -o-pre-wrap;   /* Opera 7 */
                 word-wrap: break-word;      /* IE */
                 font-size: 15px !important;
            }
        </style></head>
        <%@ page import="java.io.IOException" %>
        <%@ page import="javax.servlet.ServletException" %>
        <%@ page import="javax.servlet.http.HttpServlet" %>
        <%@ page import="javax.servlet.http.HttpServletRequest" %>
        <%@ page import="javax.servlet.http.HttpServletResponse" %>
        <%@ page import="java.io.PrintWriter" %>
        <%@ page import="java.util.UUID" %>

        <%@ page import="com.amazon.pay.Client" %>
        <%@ page import="com.amazon.pay.Config" %>
        <%@ page import="com.amazon.pay.impl.PayClient" %>
        <%@ page import="com.amazon.pay.impl.PayConfig" %>
        <%@ page import="com.amazon.pay.types.Region" %>
        <%@ page import="com.amazon.pay.types.CurrencyCode" %>
        <%@ page import="com.amazon.pay.response.parser.*" %>
        <%@ page import="com.amazon.pay.request.*" %>

        <body>
<%

String consentToken = request.getParameter("consent_token");
String orderReferenceId = request.getParameter("oro_id");
String amount = "1.29";

Config configkey = new PayConfig()
      .withSellerId("A1WTD9YOAS1TT0")
      .withAccessKey("AKIAJDKORTDNIDV7PPQA")
      .withSecretKey("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")
      .withSandboxMode(true)
      .withRegion(Region.US)
      .withCurrencyCode(CurrencyCode.USD);
%>
<div class="container">

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand start-over" href="#">Amazon Pay JAVA SDK Simple Checkout</a>
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

<h2>Confirm</h2>
<p>Congratulations! You are now a proud owner of the official Amazon Pay
JAVA Software Development Kit!</p>
<p>Click <a href="https://github.com/amzn/amazon-pay-sdk-java" target="_blank">here</a> for Amazon Pay JAVA SDK to get the latest jar.</p>


<p>At this point we will make the <em>Confirm</em> API call to confirm the order
reference and a subsequent <em>Authorize</em> and <em>Capture</em> API call.
If you used a test account associated with your email address you should receive
an email.</p>

        </div>
        <%
        out.println("<pre><div id='wordwrap'><b>Order Reference ID</b> = <i>" + orderReferenceId + "</i><br /><b>Consent Token</b> = <i>" + consentToken + "</i></div></n></pre>");
                        out.println("</n>");
        %>
    </div>
    <div class="jumbotron jumbotroncodecolor" style="padding-top:25px;" id="api-calls">
    <h3>Code</h3>
<%
try {
            Client client = new PayClient(configkey);


            out.println("<p>Making a GetOrderReferenceDetails API Call. Usually you call GetOrderReferenceDetails before calling a SetOrderReferenceDetails to check Order details and also get a Zipcode/shipping_address to calculate shipping tax.</p>");

            final GetOrderReferenceDetailsRequest getOrderReferenceDetailsRequest  = new GetOrderReferenceDetailsRequest(orderReferenceId);
            getOrderReferenceDetailsRequest.setAccessToken(consentToken);
            final GetOrderReferenceDetailsResponseData responsOrderRef = client.getOrderReferenceDetails(getOrderReferenceDetailsRequest);
            out.println("<pre id='confirm'>" + responsOrderRef.toXML().replace("<", "&lt;").replace(">", "&gt;") + "</pre>");


            SetOrderReferenceDetailsRequest setOrderReferenceDetailsRequest = new SetOrderReferenceDetailsRequest(orderReferenceId, amount);
            setOrderReferenceDetailsRequest.setCustomInformation("Java SDK OneTime Checkout Sample");
            setOrderReferenceDetailsRequest.setStoreName("Java Cosmos Store");
            setOrderReferenceDetailsRequest.setSellerNote("1st Amazon Pay OneTime Checkout Order");
            client.setOrderReferenceDetails(setOrderReferenceDetailsRequest);

            ConfirmOrderReferenceRequest confirmOrderReferenceRequest = new ConfirmOrderReferenceRequest(orderReferenceId);
            client.confirmOrderReference(confirmOrderReferenceRequest);


            String uniqueAuthorizationRefereneceId = UUID.randomUUID().toString().replace("-", "");
            AuthorizeRequest authorizeRequest = new AuthorizeRequest(orderReferenceId,
                    uniqueAuthorizationRefereneceId, amount);
            authorizeRequest.setTransactionTimeout("0");
            authorizeRequest.setCaptureNow(true);
            AuthorizeResponseData authResponse = client.authorize(authorizeRequest);

            out.println("<p>The <i>Authorize</i> API call will authorize the order reference.<br />The Capture API call will capture the funds for the given order reference id. If you want to make a separate <i>Capture</i> API call, you can set the <strong>CaptureNow</strong> parameter to <strong>false</strong> and then make a Capture call to collect funds.</p><pre id='confirm'>" + authResponse.toXML().replace("<", "&lt;").replace(">", "&gt;") + "</pre>");

            out.println("<p>To get <i>Amazon Authorization Id</i>, add the following code snippet.</p><pre>String amazonAuthorizationId = authResponse.getDetails().getAmazonAuthorizationId();<br /></pre>");


            final GetOrderReferenceDetailsRequest getOrderReferenceDetailsRequest2  = new GetOrderReferenceDetailsRequest(orderReferenceId);
            final GetOrderReferenceDetailsResponseData orderrefResponse_2 = client.getOrderReferenceDetails(getOrderReferenceDetailsRequest2);
            out.println("<pre id='confirm'>" + orderrefResponse_2.toXML().replace("<", "&lt;").replace(">", "&gt;") + "</pre>");

        } catch (Exception e) {
            e.printStackTrace();
            out.println(e.getMessage());
        }

%>
</div>
</div>
</body>
</body>
</html>
