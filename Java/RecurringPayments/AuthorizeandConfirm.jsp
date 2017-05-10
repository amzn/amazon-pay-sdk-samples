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
        <%@ page import="java.util.Date" %>
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
        <%@ page import="com.amazon.pay.response.model.GetBillingAgreementDetailsResponse" %>
        <%@ page import="com.amazon.pay.request.*" %>

        <body>
<%

String consentToken = request.getParameter("consent_token");
String billingAgreementId = request.getParameter("billing_agreement_id");
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
                <a class="navbar-brand start-over" href="#">Amazon Pay JAVA SDK Recurring Checkout</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="start-over" href="https://www.dseteam.net/tarishah/test_recurring/">Start Over</a></li>
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

<p>At this point we will make the <em>Confirm</em> API call to confirm the billing agreement and a subsequent <em>Authorize</em> and <em>Capture</em> API call.
If you used a test account associated with your email address you should receive
an email.</p>

        </div>
        <%
        out.println("<pre><div id='wordwrap'><b>Billing Agreement ID</b> = <i>" + billingAgreementId + "</i><br /><b>Consent Token</b> = <i>" + consentToken + "</i></div></n></pre>");
        %>
    </div>
    <div class="jumbotron jumbotroncodecolor" style="padding-top:25px;" id="api-calls">
    <h3>Code</h3>
<%
      try {
            Client client = new PayClient(configkey);


            out.println("<p>Making a GetBillingAgreementDetails API Call. Usually you call GetBillingAgreementDetails before calling a SetOrderReferenceDetails to check Order details and also get a Zipcode/shipping_address to calculate shipping tax.</p>");

            GetBillingAgreementDetailsRequest getBillingAgreementDetailsRequest = new GetBillingAgreementDetailsRequest(billingAgreementId);
            getBillingAgreementDetailsRequest.setAddressConsentToken(consentToken);
            final GetBillingAgreementDetailsResponseData badetailsResponse = client.getBillingAgreementDetails(getBillingAgreementDetailsRequest);

            out.println("<pre id='confirm'>"+badetailsResponse.toXML().replace("<", "&lt;").replace(">", "&gt;") + "</pre>");

            out.println("<p>To make a <i>SetOrderReferenceDetails</i> API call, please add the below code snippet.</p>");

            out.println("<pre id='confirm'>SetBillingAgreementDetailsRequest setBillingAgreementDetailsRequest = new SetBillingAgreementDetailsRequest(billingAgreementId);<br />setBillingAgreementDetailsRequest.setCustomInformation('JAVA Subscription Sample');<br />setBillingAgreementDetailsRequest.setStoreName('Java Cosmos Store');<br />setBillingAgreementDetailsRequest.setSellerNote('1st Amazon Pay Subscription');<br />client.setBillingAgreementDetails(setBillingAgreementDetailsRequest);</pre>");

            ConfirmBillingAgreementRequest confirmBillingAgreementRequest = new ConfirmBillingAgreementRequest(billingAgreementId);
            client.confirmBillingAgreement(confirmBillingAgreementRequest);


            String uniqueAuthorizationRefereneceId = UUID.randomUUID().toString().replace("-", "");
            AuthorizeOnBillingAgreementRequest authOnBillingRequest = new AuthorizeOnBillingAgreementRequest(billingAgreementId , uniqueAuthorizationRefereneceId , amount);
            authOnBillingRequest.setTransactionTimeout("0");
            authOnBillingRequest.setCaptureNow(false);

            AuthorizeOnBillingAgreementResponseData authresponse = client.authorizeOnBillingAgreement(authOnBillingRequest);

            out.println("<p>The <i>Authorize</i> API call will authorize the order reference. Instead of making a separate <i>Capture</i> API call we can set the <strong>CaptureNow</strong> parameter to <strong>True</strong> and the funds will be captured in the same call.</p><pre id='confirm'>" + authresponse.toXML().replace("<", "&lt;").replace(">", "&gt;") + "</pre>");

            out.println("<p>To get <i>Amazon Authorization Id</i>, add the following code snippet.</p><pre>String amazonAuthorizationId = authresponse.getDetails().getAmazonAuthorizationId();<br /></pre>");

            out.println("If you want to set <strong>CaptureNow</strong> parameter to <strong>True</strong>, make <strong><i>authOnBillingRequest.setCaptureNow(true);</i></strong> in the AuthorizeOnBillingAgreementRequest call and add the below CaptureRequest Snippet to make a Capture call.");

            out.println("<pre id='confirm'>String uniqueCaptureRefereneceId = UUID.randomUUID().toString().replace('-', '');<br />CaptureRequest captureRequest = new CaptureRequest(amazonAuthorizationId, uniqueCaptureRefereneceId , '0.50');<br />captureRequest.setSellerCaptureNote('Test Capture Call');<br />CaptureResponseData responseCapture = client.capture(captureRequest);<br /></pre>");

            GetBillingAgreementDetailsRequest getBillingAgreementDetailsRequest2 = new GetBillingAgreementDetailsRequest(billingAgreementId);
            final GetBillingAgreementDetailsResponseData badetailsResponse2 = client.getBillingAgreementDetails(getBillingAgreementDetailsRequest2);
            out.println("<pre id='confirm'>" + badetailsResponse2.toXML().replace("<", "&lt;").replace(">", "&gt;") + "</pre>");

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
