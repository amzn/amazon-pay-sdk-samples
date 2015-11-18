using System;
using System.Collections.Generic;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using PayWithAmazon;
using PayWithAmazon.StandardPaymentRequests;
using PayWithAmazon.CommonRequests;
using PayWithAmazon.Responses;
using Newtonsoft.Json;
using System.Configuration;
using System.Web.Services;
using Newtonsoft.Json.Linq;

namespace SampleCartDemo.OneTimePayments
{
    public partial class SetPaymentDetails : System.Web.UI.Page
    {
        string access_token = "";
        private static PayWithAmazon.CommonRequests.Configuration clientConfig = null;
        private static Client client = null;
        private static Dictionary<string, string> apiResponse = new Dictionary<string, string>();

        protected void Page_Load(object sender, EventArgs e)
        {
            access_token = Request.QueryString["access_token"];
            access_token_text.InnerHtml = access_token;
            clientConfig = new PayWithAmazon.CommonRequests.Configuration();

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
        public static Dictionary<string, string> MakeApiCallAndReturnJsonResponse(string amazonOrderReferenceId, string amount, string addressConsentToken = "")
        {
            SetOrderReferenceDetailsApiCall(amazonOrderReferenceId, amount);
            GetOrderReferenceDetailsApiCall(amazonOrderReferenceId, amount);
            HttpContext.Current.Session.Add("amazonOrderReferenceId", amazonOrderReferenceId);
            HttpContext.Current.Session.Add("amount", amount);
            return apiResponse;
        }

        public static void GetOrderReferenceDetailsApiCall(string amazonOrderReferenceId, string addressConsentToken = null)
        {
            GetOrderReferenceDetailsRequest getRequestParameters = new GetOrderReferenceDetailsRequest();
            getRequestParameters.WithAmazonOrderReferenceId(amazonOrderReferenceId)
                .WithaddressConsentToken(null);

            OrderReferenceDetailsResponse getOrderReferenceDetailsResponse = client.GetOrderReferenceDetails(getRequestParameters);
           
            // IResponse is an interface method for common response methods for each response class 
            IResponse interfaceresp = (IResponse)getOrderReferenceDetailsResponse;
            apiResponse["getOrderReferenceDetailsResponse"] = JObject.Parse(getOrderReferenceDetailsResponse.GetJson()).ToString();

        }

        public static void SetOrderReferenceDetailsApiCall(string amazonOrderReferenceId, string amount)
        {
            SetOrderReferenceDetailsRequest setRequestParameters = new SetOrderReferenceDetailsRequest();
            setRequestParameters.WithAmazonOrderReferenceId(amazonOrderReferenceId)
                .WithAmount(decimal.Parse(amount))
                .WithCurrencyCode(Regions.currencyCode.USD)
                .WithSellerNote("Note");

            OrderReferenceDetailsResponse setResponse = client.SetOrderReferenceDetails(setRequestParameters);

            if (!setResponse.GetSuccess())
            {
                apiResponse["setOrderReferenceDetailsResponse"] = "SetOrderReferenceDetails API call Failed" + Environment.NewLine + setResponse.GetJson();
            }
            else
            {
                apiResponse["setOrderReferenceDetailsResponse"] = setResponse.GetJson();
            }
        }

        protected void PlaceOrder(object sender, EventArgs e)
        {
            Response.Redirect("~/OneTimePayments/ConfirmAndAuthorize.aspx");
        }
    }
}