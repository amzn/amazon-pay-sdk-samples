using Newtonsoft.Json.Linq;
using AmazonPay;
using AmazonPay.RecurringPaymentRequests;
using AmazonPay.Responses;
using AmazonPay.StandardPaymentRequests;
using System;
using System.Collections.Generic;
using System.Web;
using System.Web.Services;
using System.Web.UI;
using System.Web.UI.WebControls;

namespace SampleCartDemo.RecurringPayments
{
    public partial class ConfirmAndAuthorize : System.Web.UI.Page
    {
        private static Client client;
        private static string amazonAuthorizationId;
        private static IList<string> amazonCaptureIdList = new List<string>();
        private static bool captureNow;
        private static Dictionary<string, string> apiResponse = new Dictionary<string, string>();

        protected void Page_Load(object sender, EventArgs e)
        {
            client = Session["PayWithAmazon_clientObj"] as Client;
            MakeConfirmBillingAgreementApiCall();
        }

        public void MakeConfirmBillingAgreementApiCall()
        {
            ConfirmBillingAgreementApiCall();

        }

        public void ConfirmBillingAgreementApiCall()
        {
            ConfirmBillingAgreementRequest getRequestParameters = new ConfirmBillingAgreementRequest();
            getRequestParameters.WithAmazonBillingreementId(Session["amazonBillingAgreementId"].ToString());

            ConfirmBillingAgreementResponse confirmBillingAgrementResponse = client.ConfirmBillingAgreement(getRequestParameters);
            confirm_response.InnerHtml = "<pre><code>" + confirmBillingAgrementResponse.GetJson() + "</code></pre>";

        }

        public static string GenerateRandomUniqueString()
        {
            Guid g = Guid.NewGuid();
            string GuidString = Convert.ToBase64String(g.ToByteArray());
            GuidString = GuidString.Replace("=", "");
            GuidString = GuidString.Replace("+", "");
            GuidString = GuidString.Replace("/", "");
            return GuidString;
        }

        [WebMethod]
        public static Dictionary<string, string> AuthorizeApiCall()
        {
            string uniqueReferenceId = GenerateRandomUniqueString();

            AuthorizeOnBillingAgreementRequest authRequestParameters = new AuthorizeOnBillingAgreementRequest();
            authRequestParameters.WithAmazonBillingAgreementId(HttpContext.Current.Session["amazonBillingAgreementId"].ToString())
                // The below code can be used to get the amount from the session. the amount was added into session in the SetPaymentDetails.aspx
                //.WithAmount(decimal.Parse(Session["amount"].ToString()))

                //For example we will be authorizing amount value of 1.99
                .WithAmount((decimal)1.99)
                .WithCurrencyCode(Regions.currencyCode.USD)
                .WithAuthorizationReferenceId(uniqueReferenceId)
                .WithTransactionTimeout(0)
                .WithCaptureNow(true)
                .WithSellerAuthorizationNote("Note");

            AuthorizeResponse authResponse = client.AuthorizeOnBillingAgreement(authRequestParameters);
            apiResponse["authorizeOnBillingAgreementResponse"] = authResponse.GetJson();
            if (!authResponse.GetSuccess())
            {
                string errorCode = authResponse.GetErrorCode();
                string errorMessage = authResponse.GetErrorMessage();
            }
            else
            {
                // AuthorizeOnBillingAgreement was a success 
                amazonAuthorizationId = authResponse.GetAuthorizationId();

                // Check if the Capture Now was set to true 
                captureNow = authResponse.GetCaptureNow();

                // If captureNow was true then the capture has already happened. Get the Capture ID(s) from the List
                if (captureNow)
                {
                    amazonCaptureIdList = authResponse.GetCaptureIdList();
                }
                CaptureApiCall();
                return apiResponse;
            }
            return apiResponse;
        }

        public static Dictionary<string, string> CaptureApiCall()
        {
            string captureId = "";
            string uniqueReferenceId = GenerateRandomUniqueString();

            // If the captureNow was not true then capture the amount for the Authorization ID
            if (!captureNow)
            {
                CaptureRequest captureRequestParameters = new CaptureRequest();
                captureRequestParameters.WithAmazonAuthorizationId(amazonAuthorizationId)
                    // The below code can be used to get the amount from the session. the amount was added into session in the SetPaymentDetails.aspx
                    //.WithAmount(decimal.Parse(Session["amount"].ToString()))

                    //For example we will be authorizing amount value of 1.99
                    .WithAmount((decimal)1.99)
                    .WithCurrencyCode(Regions.currencyCode.USD)
                    .WithCaptureReferenceId(uniqueReferenceId)
                    .WithSellerCaptureNote("customNote");

                CaptureResponse captureResponse = client.Capture(captureRequestParameters);
                apiResponse["captureResponse"] = captureResponse.GetJson();

                if (!captureResponse.GetSuccess())
                {
                    // API CALL FAILED, get the Error code and Error Message
                    string errorCode = captureResponse.GetErrorCode();
                    string errorMessage = captureResponse.GetErrorMessage();
                }
                else
                {
                    return apiResponse;
                }
            }
            else
            {
                // The captureNow was true therefore just disply the Captured response details
                GetCaptureDetailsRequest getCaptureRequestParameters = new GetCaptureDetailsRequest();
                foreach (string id in amazonCaptureIdList)
                {
                    // Here there can be multiple Capture ID's. For example purposes we are considering a single Capture ID.
                    captureId = id;
                }
                getCaptureRequestParameters.WithAmazonCaptureId(captureId);

                CaptureResponse getCaptureDetailsResponse = client.GetCaptureDetails(getCaptureRequestParameters);
                apiResponse["captureResponse"] = getCaptureDetailsResponse.GetJson();
                if (getCaptureDetailsResponse.GetSuccess())
                {
                    return apiResponse;
                }
            }
            return apiResponse;
        }
    }
}