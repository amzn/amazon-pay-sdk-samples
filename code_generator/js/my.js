// function getURLParameter(name, source) {
//    return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
//    '([^&;]+?)(&|#|;|$)').exec(source)||[,""])[1].replace(/\+/g,'%20'))||null;
//  }
// var accessToken = getURLParameter("access_token", location.hash);
// if (typeof accessToken === 'string' && accessToken.match(/^Atza/)) {
//   // showAddressBook("addressBookWidgetDiv");
//   // showWallet("walletWidgetDiv");
// }

window.onAmazonLoginReady = function() {
  console.log("onAmazonLoginReady", CLIENT_ID, SELLER_ID);
  amazon.Login.setClientId(CLIENT_ID);
  amazon.Login.setUseCookie(true);
};

document.addEventListener("DOMContentLoaded", init, false);

function init() {
  document.getElementById("seller_id_input").value = SELLER_ID;
  document.getElementById("client_id_input").value = CLIENT_ID;
  showButton();
}

function showButton() {
  var authRequest;
  OffAmazonPayments.Button("AmazonPayButton", SELLER_ID, {
    type:  "PwA",
    color: "Gold",
    size:  "medium",

    authorization: function() {
      console.log("client_id", CLIENT_ID);
      loginOptions = {scope: "profile payments:widget payments:shipping_address", popup: POPUP_FLG};
      authRequest = amazon.Login.authorize (loginOptions, "http://localhost:3000/id/" + CURRENT_PARAM_ID);
    }
  });

  var logoutBtn = document.getElementById("logout");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function () {
      amazon.Login.logout();
      window.location = "/";
    }, false);
  }
}

function showAddressBook (divId) {
  new OffAmazonPayments.Widgets.AddressBook({
      sellerId: SELLER_ID,
      onOrderReferenceCreate: function (orderReference) {
        orderReferenceId = orderReference.getAmazonOrderReferenceId();
        var el;
        if ((el = document.getElementById("orderReferenceId"))) {
          el.value = orderReferenceId;
        }
        console.log("onOrderReferenceCreate", orderReferenceId);
      },
      onAddressSelect: function () {
          // do stuff here like recalculate tax and/or shipping
      },
      design: {
          designMode: 'responsive'
      },
      onError: function (error) {
          // your error handling code
      }
  }).bind(divId);
}

function showWallet(divId) {
  new OffAmazonPayments.Widgets.Wallet({
          sellerId: SELLER_ID,
          onPaymentSelect: function () {
          },
          design: {
              designMode: 'responsive'
          },
          onError: function (error) {
              // your error handling code
          }
      }).bind(divId);
}
