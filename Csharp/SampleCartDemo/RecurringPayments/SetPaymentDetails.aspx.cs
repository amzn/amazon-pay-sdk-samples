using Newtonsoft.Json.Linq;
using AmazonPay;
using AmazonPay.RecurringPaymentRequests;
using AmazonPay.Responses;
using System;
using System.Collections.Generic;
using System.Configuration;
using System.Web;
using System.Web.Services;
using System.Web.UI;
using System.Web.UI.WebControls;

namespace SampleCartDemo.RecurringPayments
{
    public partial class SetPaymentDetails : System.Web.UI.Page
    {
        string access_token = "";
        private static AmazonPay.CommonRequests.Configuration clientConfig = null;
        private static Client client = null;
        private static Dictionary<string, string> apiResponse = new Dictionary<string, string>();

        protected void Page_Load(object sender, EventArgs e)
        {
            access_token = Request.QueryString["access_token"];
            access_token_text.InnerHtml = access_token;
            clientConfig = new AmazonPay.CommonRequests.Configuration();

            clientConfig.WithAccessKey(ConfigurationManager.AppSettings["access_key"])
                .WithSecretKey(ConfigurationManager.AppSettings["secret_key"])
                .WithMerchantId(ConfigurationManager.AppSettings["merchant_id"])
                .WithClientId(ConfigurationManager.AppSettings["lwa_client_id"])
                .WithSandbox(true)
                .WithRegion(Regions.supportedRegions.us);

            client = new Client(clientConfig);
            Session.Add("PayWithAmazon_clientObj", client);
        }

        [WebMethod]
        public static Dictionary<string, string> MakeApiCallAndReturnJsonResponse(string amazonBillingAgreementId, string amount, string addressConsentToken = "")
        {
            SetOrderReferenceDetailsApiCall(amazonBillingAgreementId);
            GetOrderReferenceDetailsApiCall(amazonBillingAgreementId);
            HttpContext.Current.Session.Add("amazonBillingAgreementId", amazonBillingAgreementId);
            HttpContext.Current.Session.Add("amount", amount);
            return apiResponse;
        }

        public static void GetOrderReferenceDetailsApiCall(string amazonBillingAgreementId, string addressConsentToken = null)
        {
            GetBillingAgreementDetailsRequest getRequestParameters = new GetBillingAgreementDetailsRequest();
            getRequestParameters.WithAmazonBillingAgreementId(amazonBillingAgreementId)
                .WithaddressConsentToken(null);

            BillingAgreementDetailsResponse getOrderReferenceDetailsResponse = client.GetBillingAgreementDetails(getRequestParameters);

            // IResponse is an interface method for common response methods for each response class 
            IResponse interfaceresp = (IResponse)getOrderReferenceDetailsResponse;
            
            apiResponse["getBillingAgreementDetailsResponse"] = JObject.Parse(getOrderReferenceDetailsResponse.GetJson()).ToString();

        }

        public static void SetOrderReferenceDetailsApiCall(string amazonBillingAgreementId)
        {
            SetBillingAgreementDetailsRequest setRequestParameters = new SetBillingAgreementDetailsRequest();
            setRequestParameters.WithAmazonBillingAgreementId(amazonBillingAgreementId)
                .WithSellerNote("Note");

            BillingAgreementDetailsResponse setResponse = client.SetBillingAgreementDetails(setRequestParameters);

            if (!setResponse.GetSuccess())
            {
                apiResponse["setBillingAgreementDetailsResponse"] = "SetBillingAgreementDetails API call Failed" + Environment.NewLine + setResponse.GetJson();
            }
            else
            {
                apiResponse["setBillingAgreementDetailsResponse"] = setResponse.GetJson();
            }
        }

        protected void PlaceOrder(object sender, EventArgs e)
        {
            Response.Redirect("~/RecurringPayments/ConfirmAndAuthorize.aspx");
        }
    }
}