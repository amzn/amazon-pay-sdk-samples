using System;
using System.Collections.Generic;
using System.Web;
using System.Web.Services;
using System.Web.UI;
using System.Web.UI.WebControls;
using AmazonPay;
using AmazonPay.CommonRequests;
using AmazonPay.StandardPaymentRequests;
using AmazonPay.Responses;
using Newtonsoft.Json.Linq;

namespace SampleCartDemo.OneTimePayments
{
    public partial class ConfirmAndAuthorize : System.Web.UI.Page
    {
        private Client client;
        private string amazonAuthorizationId;
        private IList<string> amazonCaptureIdList = new List<string>();
        private bool captureNow;

        protected void Page_Load(object sender, EventArgs e)
        {
            client = Session["PayWithAmazon_clientObj"] as Client;
            MakeApiCallConfirmAndAuthorize();
        }

        public void MakeApiCallConfirmAndAuthorize()
        {
            ConfirmOrderReferenceApiCall();
            AuthorizeApiCall();
            CaptureApiCall();
        }

        public void ConfirmOrderReferenceApiCall()
        {
            ConfirmOrderReferenceRequest getRequestParameters = new ConfirmOrderReferenceRequest();
            getRequestParameters.WithAmazonOrderReferenceId(Session["amazonOrderReferenceId"].ToString());

            ConfirmOrderReferenceResponse confirmOrderReferenceResponse = client.ConfirmOrderReference(getRequestParameters);
            confirm.InnerHtml = confirmOrderReferenceResponse.GetJson();

        }

        public string GenerateRandomUniqueString()
        {
            Guid g = Guid.NewGuid();
            string GuidString = Convert.ToBase64String(g.ToByteArray());
            GuidString = GuidString.Replace("=", "");
            GuidString = GuidString.Replace("+", "");
            GuidString = GuidString.Replace("/", "");
            return GuidString;
        }

        public void AuthorizeApiCall()
        {

            string uniqueReferenceId = GenerateRandomUniqueString();

            AuthorizeRequest authRequestParameters = new AuthorizeRequest();
            authRequestParameters.WithAmazonOrderReferenceId(Session["amazonOrderReferenceId"].ToString())
                .WithAmount(decimal.Parse(Session["amount"].ToString()))
                .WithCurrencyCode(Regions.currencyCode.USD)
                .WithAuthorizationReferenceId(uniqueReferenceId)
                .WithTransactionTimeout(0)
                .WithCaptureNow(true)
                .WithSellerAuthorizationNote("Note");

            AuthorizeResponse authResponse = client.Authorize(authRequestParameters);

            // Authorize was not a success Get the Error code and the Error message
            if (!authResponse.GetSuccess())
            {
                string errorCode = authResponse.GetErrorCode();
                string errorMessage = authResponse.GetErrorMessage();
                authorize.InnerHtml = authResponse.GetJson();
            }
            else
            {
                amazonAuthorizationId = authResponse.GetAuthorizationId();
                captureNow = authResponse.GetCaptureNow();
                
                // If captureNow was true then the capture has already happened. save the capture id(s).
                if (captureNow)
                {
                    amazonCaptureIdList = authResponse.GetCaptureIdList();
                }
                authorize.InnerHtml = authResponse.GetJson();
            }
        }

        public void CaptureApiCall()
        {
            string captureId = "";
            string uniqueReferenceId = GenerateRandomUniqueString();

            // If the capture has not happened on the previous Authorize API call then capture the amount.
            if (!captureNow)
            {
                CaptureRequest captureRequestParameters = new CaptureRequest();
                captureRequestParameters.WithAmazonAuthorizationId(amazonAuthorizationId)
                    .WithAmount(decimal.Parse(Session["amount"].ToString()))
                    .WithCurrencyCode(Regions.currencyCode.USD)
                    .WithCaptureReferenceId(uniqueReferenceId)
                    .WithSellerCaptureNote("customNote");

                CaptureResponse captureResponse = client.Capture(captureRequestParameters);

                // Capture was not a success Get the Error code and the Error message
                if (!captureResponse.GetSuccess())
                {
                    string errorCode = captureResponse.GetErrorCode();
                    string errorMessage = captureResponse.GetErrorMessage();
                    capture.InnerHtml = "Capture API call Failed" + Environment.NewLine + captureResponse.GetJson();
                }
                else
                {
                    // In this example the below is to simply display the output
                    capture.InnerHtml = captureResponse.GetJson();
                }
            }
            else
            {
                // In this case the capture had already happened . running the GetCaptureDetails API call to get the output of the capture.
                GetCaptureDetailsRequest getCaptureRequestParameters = new GetCaptureDetailsRequest();
                foreach (string id in amazonCaptureIdList)
                {
                    captureId = id;
                }
                getCaptureRequestParameters.WithAmazonCaptureId(captureId);

                CaptureResponse getCaptureDetailsResponse = client.GetCaptureDetails(getCaptureRequestParameters);

                if (getCaptureDetailsResponse.GetSuccess())
                {
                    capture.InnerHtml = getCaptureDetailsResponse.GetJson();
                }
            }
        }
    }
}