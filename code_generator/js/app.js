var app = angular.module('myApp', ['ngAnimate', 'ui.bootstrap', 'pascalprecht.translate']);

app.controller('indexCtrl', function ($scope, $templateCache, $timeout, $translate, $location) {

    // #####################
    // Define variables
    // #####################
    hljs.initHighlightingOnLoad();
    new Clipboard('#htmlcopybutton');
    new Clipboard(".copy-btn");

    $scope.codeHtml = $templateCache.get('compile-me');
    $scope.language = 'US';
    $scope.billing_address = 'false';
    $scope.urlCopy = '';
    $scope.urlCopyStatus = false;

    var regions_js_urls = {
        "sandbox": {
            "US": "https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js",
            "UK": "https://static-eu.payments-amazon.com/OffAmazonPayments/gbp/sandbox/lpa/js/Widgets.js",
            "DE": "https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js",
            "JP": "https://static-fe.payments-amazon.com/OffAmazonPayments/jp/sandbox/lpa/js/Widgets.js"
        }
    };

    $scope.buttonTypes = [
        {
            name: "Login with Amazon",
            value: "LwA",
            disabled: false
        },
        {
            name: "Login",
            value: "Login",
            disabled: false
        },
        {
            name: "Pay with Amazon",
            value: "PwA",
            disabled: false
        },
        {
            name: "Pay",
            value: "Pay",
            disabled: false
        },
        {
            name: "A",
            value: "A",
            disabled: false
        }
    ];

    $scope.awp = {
        crient_id: "your_client_id",
        seller_id: "your_seller_merchant_id",
        redirect_url: "callback.html",
        region: "US",
        region_url: regions_js_urls["sandbox"]["JP"],
        asynchronous: 'async',
        displayInfo: {
            scope: {
                login: "login",
                addressbook: "addressbook",
                wallet: "wallet",
                consent: ""
            }
        },
        buttonInfo: {
            type: "PwA",
            color: "Gold",
            size: "medium",
            popup: true,
            scope: {
                profile: "profile",
                // profile_user_id: " ",
                // profile_postal_code: "",
                payments_widget: " payments:widget",
                payments_shipping_address: " payments:shipping_address",
                payments_billing_address: ""
            }
        },
        implementationType: "onetime",
        redirect_url_type: "url",
        widget_size: "responsive",
        widget_size_width: "400",
        widget_size_height: "228"
    };

    $scope.script = {
        useCookie: '',
        htmlStart: "<html>",
        bodyStart: "<body>",
        htmlEnd: "</html>",
        bodyEnd: "</body>",
        start: "<script type='text/javascript'>",
        end: "</script>",
        logoutButton: '<button type="button" name="button" id="Logout">Logout</button>',
        buttonDivContainer:"<div style=\"text-align: center; border: 1px solid #bbb;border-radius: 3px;padding:5px;margin:5px;\">",
        buttonDivContainerEnd:"</div>",
        buttonDiv: '<div id="AmazonPayButton"></div>',
        buttonExplanation: '',
        walletWidgetDiv: '<div id="walletWidgetDiv" style="height:250px"></div>',
        addressBookWidgetDiv: '<div id="addressBookWidgetDiv" style="height:250px"></div>',
        consentWidgetDiv: '<div id="consentWidgetDiv" style="height:250px"></div>',
        widgetJS: "",
        amp: "&",
        asyncFunctionCall: "widgetInit();",
        asyncFunctionStart: "function widgetInit() {",
        asyncFunctionEnd: "}",
        showLoginButton: "",
        showAddressBookWidget: "",
        showWalletWidget: "",
        showLoginButtonDetail: "",
        showAddressBookWidgetDetail: "",
        showWalletWidgetDetail: "",
        showConsentWidgetDetail: "",
        agreementType: ""
    };

    // #####################
    // Define Functions
    // #####################
    $scope.changeLanguage = function () {
        $translate.use($scope.language);
        $scope.buttonTypes[0].name = $translate.instant('BUTTON_LABEL_LWA');
        $scope.buttonTypes[2].name = $translate.instant('BUTTON_LABEL_PWA');
        $scope.changeDisplayScope();
    };

    $scope.changeRegion = function () {
        var currentRegion = $scope.awp.region;
        OffAmazonPayments = null;

        $scope.awp.asynchronous = 'async';
        if (currentRegion == 'JP') {
            $scope.buttonTypes[1].disabled = true;
            $scope.buttonTypes[3].disabled = true;
            $scope.buttonTypes[4].disabled = true;
            $scope.language = "JP";
            //$translate.use('JP');
            $scope.changeLanguage();
        } else {
            $scope.buttonTypes[1].disabled = false;
            $scope.buttonTypes[3].disabled = false;
            $scope.buttonTypes[4].disabled = false;
            switch (currentRegion) {
                case 'UK':
                case 'DE':
                    $scope.awp.buttonInfo.scope.profile = 'profile';
                    $scope.awp.buttonInfo.scope.payments_widget = ' payments:widget';
                    $scope.awp.buttonInfo.scope.payments_shipping_address = ' payments:shipping_address';
                    if ($scope.billing_address == "true") {
                        $scope.awp.buttonInfo.scope.payments_billing_address = ' payments:billing_address';
                    }
                    break;
            }
        }
        $scope.changeDisplayScope();

        $timeout(function () {
            var sEl = document.getElementById('widgets_script');
            if (sEl) {
                sEl.remove();
            }
            var newScript = document.createElement("script");
            newScript.type = "text/javascript";
            newScript.id = "widgets_script";
            newScript.src = regions_js_urls["sandbox"][currentRegion];
            document.body.appendChild(newScript);
        }, 100);
        $scope.updateScriptTag();
    };
    
    $scope.showUrlPre = function () {
        $scope.urlCopyStatus = !$scope.urlCopyStatus;
    };

    $scope.urlCopyFunction = function () {
        var absUrl = $location.absUrl();
        var urlParsingNode = document.createElement("a");
        urlParsingNode.setAttribute("href", absUrl);

        var urlStr = $location.protocol() + "://" + $location.host() + ":" + $location.port() + urlParsingNode.pathname +
            "?seller_id=" + $scope.awp.seller_id +
            "&client_id=" + $scope.awp.crient_id +
            "&region=" + $scope.awp.region +
            "&buttonInfo_color=" + $scope.awp.buttonInfo.color +
            "&buttonInfo_type=" + $scope.awp.buttonInfo.type +
            "&buttonInfo_size=" + $scope.awp.buttonInfo.size +
            "&displayInfo_scope_login=" + $scope.awp.displayInfo.scope.login +
            "&displayInfo_scope_addressbook=" + $scope.awp.displayInfo.scope.addressbook +
            "&displayInfo_scope_wallet=" + $scope.awp.displayInfo.scope.wallet +
            "&displayInfo_scope_consent=" + $scope.awp.displayInfo.scope.consent +
            "&buttonInfo_scope_profile=" + $scope.awp.buttonInfo.scope.profile +
            "&buttonInfo_scope_payments_widget=" + $scope.awp.buttonInfo.scope.payments_widget +
            "&buttonInfo_scope_payments_shipping_address=" + $scope.awp.buttonInfo.scope.payments_shipping_address +
            "&buttonInfo_scope_payments_billing_address=" + $scope.awp.buttonInfo.scope.payments_billing_address +
            "&buttonInfo_popup=" + $scope.awp.buttonInfo.popup +
            "&implementationType=" + $scope.awp.implementationType +
            "&widget_size=" + $scope.awp.widget_size +
            "&widget_size_width=" + $scope.awp.widget_size_width +
            "&widget_size_height=" + $scope.awp.widget_size_height +
            "&redirect_url_type=" + $scope.awp.redirect_url_type +
            "&redirect_url=" + $scope.awp.redirect_url +
            "&asynchronous=" + $scope.awp.asynchronous;
        $scope.urlCopy = urlStr;
    };

    $scope.changeDisplayScope = function () {

        if ($scope.awp.region == 'JP' && $scope.awp.displayInfo.scope.login == 'login') {
            $scope.script.buttonExplanation = '<label style="font-size: 14px;line-height: 23px;">Amazonアカウントにご登録の住所・クレジット<br>カード情報を利用して、簡単にご注文が出来ます。<br></label>';
        } else {
            $scope.script.buttonExplanation = '';
        }

        if ($scope.awp.displayInfo.scope.login == 'login') {
            $scope.script.showLoginButton = "showLoginButton();";

            var redirectStr = "";
            if ($scope.awp.redirect_url_type == "javascript") {
                // Popup Only
                $scope.awp.buttonInfo.popup = true;

                var callRedrawMethod = "";
                if ($scope.awp.displayInfo.scope.addressbook == 'addressbook') {
                    callRedrawMethod = "                    showAddressBookWidget();";
                } else if ($scope.awp.displayInfo.scope.wallet == 'wallet') {
                    callRedrawMethod = "                    showWalletWidget(null);";
                } else {
                    callRedrawMethod = "";
                }

                redirectStr = "function(t) {\n" +
                    "                    // console.log(t.access_token);\n" +
                    "                    // console.log(t.expires_in);\n" +
                    "                    // console.log(t.token_type);\n" +
                    callRedrawMethod + "\n" +
                    "                }";
            } else {
                redirectStr = "\"" + $scope.awp.redirect_url + "\"";
            }

            $scope.script.showLoginButtonDetail = "function showLoginButton() {\n" +
                "        var authRequest;\n" +
                "        OffAmazonPayments.Button(\"AmazonPayButton\", \"" + $scope.awp.seller_id + "\", {\n" +
                "          type:  \"" + $scope.awp.buttonInfo.type + "\",\n" +
                "          color: \"" + $scope.awp.buttonInfo.color + "\",\n" +
                "          size:  \"" + $scope.awp.buttonInfo.size + "\",\n" +
                "\n" +
                "          authorization: function() {\n" +
                "            loginOptions = {scope: \"" + $scope.awp.buttonInfo.scope.profile +  "" + $scope.awp.buttonInfo.scope.payments_widget + "" + $scope.awp.buttonInfo.scope.payments_shipping_address + "" + $scope.awp.buttonInfo.scope.payments_billing_address + "\", popup: " + $scope.awp.buttonInfo.popup + "};\n" +
                "            authRequest = amazon.Login.authorize (loginOptions, " + redirectStr + ");\n" +
                "          }\n" +
                "        });\n" +
                "    }\n"
            "\n";

        } else {
            $scope.script.showLoginButton = "";
            $scope.script.showLoginButtonDetail = "";
        }

        var targetObjectName = "";
        var targetObjectNameL = "";
        if ($scope.awp.implementationType == 'autopay') {
            targetObjectName = "billingAgreement";
            targetObjectNameL = "BillingAgreement";
        } else {
            targetObjectName = "orderReference";
            targetObjectNameL = "OrderReference";
        }

        var designMode = "";
        if ($scope.awp.widget_size == 'custom') {
            designMode = "size: {width:'" + $scope.awp.widget_size_width + "px', height:'" + $scope.awp.widget_size_height + "px'}";
        } else {
            designMode = "designMode: '" + $scope.awp.widget_size + "'";
        }

        if ($scope.awp.displayInfo.scope.addressbook == 'addressbook') {
            $scope.script.addressBookWidgetDiv = "<div id=\"addressBookWidgetDiv\" style=\"height:250px\"></div>"
            $scope.script.showAddressBookWidget = "showAddressBookWidget();";

            var showWalletWidget = "";
            if ($scope.awp.displayInfo.scope.wallet == 'wallet') {
                showWalletWidget =
                    "              // Wallet\n" +
                    "              showWalletWidget(" + targetObjectName + "Id);\n";
            }
            var showConsentWidget = "";
            if ($scope.awp.displayInfo.scope.consent == 'consent') {
                showConsentWidget =
                    "              // Consent\n" +
                    "              showConsentWidget(billingAgreement);\n";
            }
            $scope.script.showAddressBookWidgetDetail = "function showAddressBookWidget() {\n" +
                "        // AddressBook\n" +
                "        new OffAmazonPayments.Widgets.AddressBook({\n" +
                "          sellerId: '" + $scope.awp.seller_id + "',\n" +
                "          " + $scope.script.agreementType + "\n" +
                "          onReady: function (" + targetObjectName + ") {\n" +
                "              var " + targetObjectName + "Id = " + targetObjectName + ".getAmazon" + targetObjectNameL + "Id();\n" +
                "              var el;\n" +
                "              if ((el = document.getElementById(\"" +targetObjectName + "Id\"))) {\n" +
                "                el.value = " + targetObjectName + "Id;\n" +
                "              }\n" +
                showWalletWidget +
                showConsentWidget +
                "          },\n" +
                "          onAddressSelect: function (" + targetObjectName + ") {\n" +
                "              " + $translate.instant('COMMENT_onAddressSelect') + "\n" +
                "          },\n" +
                "          design: {\n" +
                "              " + designMode + "\n" +
                "          },\n" +
                "          onError: function (error) {\n" +
                "              " + $translate.instant('ERROR_ON_ERROR_COMMENT')  + "\n" +
                "              console.log('OffAmazonPayments.Widgets.AddressBook', error.getErrorCode(), error.getErrorMessage());\n" +
                "              switch (error.getErrorCode()) {\n" +
                "                case 'AddressNotModifiable':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_AddressNotModifiable') + "\n" +
                "                    break;\n" +
                "                case 'BuyerNotAssociated':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_BuyerNotAssociated') + "\n" +
                "                    break;\n" +
                "                case 'BuyerSessionExpired':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_BuyerSessionExpired') + "\n" +
                "                    break;\n" +
                "                case 'InvalidAccountStatus':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_InvalidAccountStatus') + "\n" +
                "                    break;\n" +
                "                case 'InvalidOrderReferenceId':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_InvalidOrderReferenceId') + "\n" +
                "                    break;\n" +
                "                case 'InvalidParameterValue':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_InvalidParameterValue') + "\n" +
                "                    break;\n" +
                "                case 'InvalidSellerId':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_InvalidSellerId') + "\n" +
                "                    break;\n" +
                "                case 'MissingParameter':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_MissingParameter') + "\n" +
                "                    break;\n" +
                "                case 'PaymentMethodNotModifiable':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_PaymentMethodNotModifiable') + "\n" +
                "                    break;\n" +
                "                case 'ReleaseEnvironmentMismatch':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_ReleaseEnvironmentMismatch') + "\n" +
                "                    break;\n" +
                "                case 'StaleOrderReference':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_StaleOrderReference') + "\n" +
                "                    break;\n" +
                "                case 'UnknownError':\n" +
                "                    " + $translate.instant('ERROR_COMMENT_UnknownError') + "\n" +
                "                    break;\n" +
                "                default:\n" +
                "                    " + $translate.instant('ERROR_COMMENT_UnknownError2')  + "\n" +
                "              }\n" +
                "          }\n" +
                "        }).bind(\"addressBookWidgetDiv\");\n" +
                "    }\n";
        } else {
            $scope.script.addressBookWidgetDiv = "";
            $scope.script.showAddressBookWidget = "";
            $scope.script.showAddressBookWidgetDetail =  "";
        }


        if ($scope.awp.displayInfo.scope.wallet == 'wallet') {
            $scope.script.walletWidgetDiv = "<div id=\"walletWidgetDiv\" style=\"height:250px\"></div>"
            if ($scope.awp.displayInfo.scope.addressbook != 'addressbook') {
                $scope.script.showWalletWidget = "showWalletWidget(null);";
            } else {
                $scope.script.showWalletWidget = "";
            }
            var showConsentWidget = "";
            var agreementType = "";

            if ($scope.awp.implementationType == 'autopay') {
                agreementType = "          " + $scope.script.agreementType + "\n";
                if ($scope.awp.displayInfo.scope.addressbook != 'addressbook' && $scope.awp.displayInfo.scope.consent == 'consent') {
                    showConsentWidget =
                        "          onReady: function(billingAgreement) {\n" +
                        "                showConsentWidget(billingAgreement);\n" +
                        "          },\n";
                }
            }
            $scope.script.showWalletWidgetDetail = "function showWalletWidget(" + targetObjectName + "Id) {\n" +
                "        // Wallet\n" +
                "        new OffAmazonPayments.Widgets.Wallet({\n" +
                "          sellerId: '" + $scope.awp.seller_id + "',\n" +
                agreementType +
                "          amazon" + targetObjectNameL + "Id: " + targetObjectName + "Id,\n" +
                "          onReady: function(" + targetObjectName + ") {\n" +
                "              console.log(" + targetObjectName + ".getAmazon" + targetObjectNameL + "Id());\n" +
                "          },\n" +
                "          onPaymentSelect: function() {\n" +
                "              console.log(arguments);\n" +
                "          },\n" +
                showConsentWidget +
                "          design: {\n" +
                "              " + designMode + "\n" +
                "          },\n" +
                "          onError: function(error) {\n" +
                "              " + $translate.instant('ERROR_ON_ERROR_COMMENT')  + "\n" +
                "              console.log('OffAmazonPayments.Widgets.Wallet', error.getErrorCode(), error.getErrorMessage());\n" +
                "          }\n" +
                "        }).bind(\"walletWidgetDiv\");\n" +
                "    }\n";

        } else {
            $scope.script.walletWidgetDiv = "";
            $scope.script.showWalletWidget = "";
            $scope.script.showWalletWidgetDetail = "";
        }


        if ($scope.awp.displayInfo.scope.consent == 'consent') {
            $scope.script.consentWidgetDiv = "<div id=\"consentWidgetDiv\" style=\"height:250px\"></div>"
            $scope.script.showConsentWidget = "showConsentWidget();";
            $scope.script.showConsentWidgetDetail = "function showConsentWidget(billingAgreement) {\n" +
                "        // Consent\n" +
                "        new OffAmazonPayments.Widgets.Consent({\n" +
                "          sellerId: '" + $scope.awp.seller_id + "',\n" +
                "          amazonBillingAgreementId: billingAgreement.getAmazonBillingAgreementId(),\n" +
                "          onReady: function(billingAgreementConsentStatus){\n" +
                "              console.log(billingAgreementConsentStatus.getConsentStatus());\n" +
                "          },\n" +
                "          onConsent: function(billingAgreementConsentStatus){\n" +
                "              console.log(billingAgreementConsentStatus.getConsentStatus());\n" +
                "          },\n" +
                "          design: {\n" +
                "              " + designMode + "\n" +
                "          },\n" +
                "          onError: function(error) {\n" +
                "              " + $translate.instant('ERROR_ON_ERROR_COMMENT')  + "\n" +
                "              console.log('OffAmazonPayments.Widgets.Consent', error.getErrorCode(), error.getErrorMessage());\n" +
                "          }\n" +
                "        }).bind(\"consentWidgetDiv\");\n" +
                "    }\n";

        } else {
            $scope.script.consentWidgetDiv = "";
            $scope.script.showConsentWidget = "";
            $scope.script.showConsentWidgetDetail = "";
        }
        $scope.urlCopyFunction();
    };

    $scope.changeImplementationType = function () {

        if ($scope.awp.implementationType == 'autopay') {
            $scope.awp.displayInfo.scope.consent = 'consent';
            $scope.script.agreementType = "agreementType: 'BillingAgreement',";

        } else {
            $scope.awp.displayInfo.scope.consent = '';
            $scope.script.agreementType = " ";
        }
        $scope.changeDisplayScope();
    };

    $scope.updateScriptTag = function () {
        $scope.urlCopyFunction();
        var currentRegion = $scope.awp.region;
        $scope.script.widgetJS = "<script type=\"text/javascript\" \n    src=\"" + regions_js_urls["sandbox"][currentRegion] + "\" \n     " + $scope.awp.asynchronous + "></script>";
    };

    $scope.copyHTML = function () {
    };

    $scope.$watch('awp.buttonInfo.popup', function (newValue, oldValue) {
        if (!newValue) {
            $scope.script.useCookie = "amazon.Login.setUseCookie(true);";
        } else {
            $scope.script.useCookie = "";
        }
    });

    $scope.$watch('awp.asynchronous', function (newValue, oldValue) {
        if (newValue == 'async') {
            $scope.script.asyncFunctionCall = "widgetInit();";
            $scope.script.asyncFunctionStart = "function widgetInit() {";
            $scope.script.asyncFunctionEnd = "}";
        } else {
            $scope.script.asyncFunctionCall = "";
            $scope.script.asyncFunctionStart = "";
            $scope.script.asyncFunctionEnd = "";
        }
    });

    $scope.genButton = function () {
        $scope.urlCopyFunction();
        amazon.Login.setClientId($scope.awp.crient_id);
        var authRequest;
        document.getElementById("AmazonPayButton").innerHTML = "";
        OffAmazonPayments.Button("AmazonPayButton", $scope.awp.crient_id, {
            type: $scope.awp.buttonInfo.type,
            color: $scope.awp.buttonInfo.color,
            size: $scope.awp.buttonInfo.size,

            authorization: function () {

                var scope = "";
                if ($scope.awp.buttonInfo.scope.profile) {
                    scope += $scope.awp.buttonInfo.scope.profile + " ";
                }

                if ($scope.awp.buttonInfo.scope.payments_widget) {
                    scope += $scope.awp.buttonInfo.scope.payments_widget + " ";
                }

                if ($scope.awp.buttonInfo.scope.payments_shipping_address) {
                    scope += $scope.awp.buttonInfo.scope.payments_shipping_address + " ";
                }

                if ($scope.awp.buttonInfo.scope.payments_billing_address) {
                    scope += $scope.awp.buttonInfo.scope.payments_billing_address + " ";
                }

                loginOptions = {scope: scope, popup: $scope.awp.buttonInfo.popup};
                authRequest = amazon.Login.authorize(loginOptions, $scope.awp.redirect_url);
            }
        });
    };

    // #####################
    // Initialize Controller
    // #####################
    var searchObject = $location.search();
    console.log(searchObject);
    if (searchObject.seller_id != undefined) {
        $scope.awp.seller_id = searchObject.seller_id;
    }
    if (searchObject.client_id != undefined) {
        $scope.awp.crient_id = searchObject.client_id;
    }
    if (searchObject.region != undefined) {
        $scope.awp.region = searchObject.region;
        if ($scope.awp.region == "JP") {
            $scope.language = "JP";
        }
    }
    if (searchObject.buttonInfo_color != undefined) {
        $scope.awp.buttonInfo.color = searchObject.buttonInfo_color;
    }
    if (searchObject.buttonInfo_type != undefined) {
        $scope.awp.buttonInfo.type = searchObject.buttonInfo_type;
    }
    if (searchObject.buttonInfo_size != undefined) {
        $scope.awp.buttonInfo.size = searchObject.buttonInfo_size;
    }
    if (searchObject.implementationType != undefined) { // Need to check before evaluating scope.consent
        $scope.awp.implementationType = searchObject.implementationType;
    }
    if (searchObject.displayInfo_scope_login != undefined) {
        $scope.awp.displayInfo.scope.login = searchObject.displayInfo_scope_login;
    }
    if (searchObject.displayInfo_scope_addressbook != undefined) {
        $scope.awp.displayInfo.scope.addressbook = searchObject.displayInfo_scope_addressbook;
    }
    if (searchObject.displayInfo_scope_wallet != undefined) {
        $scope.awp.displayInfo.scope.wallet = searchObject.displayInfo_scope_wallet;
    }
    if (searchObject.displayInfo_scope_consent != undefined) {
        $scope.awp.displayInfo.scope.consent = searchObject.displayInfo_scope_consent;
    }
    if (searchObject.buttonInfo_scope_profile != undefined) {
        $scope.awp.buttonInfo.scope.profile = searchObject.buttonInfo_scope_profile;
    }
    if (searchObject.buttonInfo_scope_payments_widget != undefined) {
        $scope.awp.buttonInfo.scope.payments_widget = searchObject.buttonInfo_scope_payments_widget;
    }
    if (searchObject.buttonInfo_scope_payments_shipping_address != undefined) {
        $scope.awp.buttonInfo.scope.payments_shipping_address = searchObject.buttonInfo_scope_payments_shipping_address;
    }
    if (searchObject.buttonInfo_scope_payments_billing_address != undefined) {
        $scope.awp.buttonInfo.scope.payments_billing_address = searchObject.buttonInfo_scope_payments_billing_address;
    }
    if (searchObject.billing_address != undefined) {
        $scope.billing_address = searchObject.billing_address;
    } else {
        $scope.billing_address = "false";
    }
    if (searchObject.buttonInfo_popup != undefined) {
            $scope.awp.buttonInfo.popup = (searchObject.buttonInfo_popup == "true");
            $scope.changeImplementationType();
    }
    if (searchObject.widget_size != undefined) {
        $scope.awp.widget_size = searchObject.widget_size;
    }
    if (searchObject.widget_size_width != undefined) {
        $scope.awp.widget_size_width = searchObject.widget_size_width;
    }
    if (searchObject.widget_size_height != undefined) {
        $scope.awp.widget_size_height = searchObject.widget_size_height;
    }
    if (searchObject.redirect_url_type != undefined) {
        $scope.awp.redirect_url_type = searchObject.redirect_url_type;
    }
    if (searchObject.redirect_url != undefined) {
        $scope.awp.redirect_url = searchObject.redirect_url;
    }
    $scope.changeRegion();
    if (searchObject.asynchronous != undefined) {
        $scope.awp.asynchronous = searchObject.asynchronous;
    }
    $scope.updateScriptTag();
    $scope.changeDisplayScope();

    }).config(function ($translateProvider, $locationProvider) {
        $translateProvider.translations('US', {
            CLIENT_PARAMETERS: 'Client parameters',
            BUTTON_WIDGET_PARAMETERS: 'Button widget parameters',
            CLIENT_ID: 'Client ID',
            SELLER_ID: 'Seller ID',
            REGION: 'Region',

            BUTTON_COLOR: 'Button color',
            BUTTON_TYPE: 'Button type',
            BUTTON_SIZE: 'Button size',

            WIDGET_SIZE: 'Widget size',
            WIDGET_SIZE_RESPONSIVE: 'responsive',
            WIDGET_SIZE_SMARTPHONE: 'smartphoneCollapsible',
            WIDGET_SIZE_CUSTOM: 'custom',
            WIDGET_SIZE_WIDTH_TOOLTIP: 'Widget Width(px)',
            WIDGET_SIZE_HEIGHT_TOOLTIP: 'Widget Height(px)',

            BUTTON_LABEL_LWA: 'Login with Amazon',
            BUTTON_LABEL_PWA: 'Pay with Amazon',

            TARGET: 'Target',
            TARGET_LOGIN_BUTTON: 'Login Button',
            TARGET_ADDRESS_BOOK_WIDGET: 'Address Book Widget',
            TARGET_WALLET_WIDGET: 'Wallet Widget',
            TARGET_CONSENT_WIDGET: 'Consent Widget',

            SCOPE: 'Scope',
            SCOPE_TOOLTIP: 'The value of the parameter scope that you select influences both the content returned in the response of the call and the type of consent screen that Amazon Payments displays to the buyer to secure their permission for sharing their information.',
            SCOPE_PROFILE_TOOLTIP: 'Gives access to the full user profile (username, email address, and userID) after login.',
            SCOPE_WIDGET_TOOLTIP: 'Required to show the Amazon Payments widgets (address and wallet widget) on your page.',
            SCOPE_ADDRESS_TOOLTIP: 'Gives access to the full shipping address via the GetOrderReferenceDetails API call as soon as an address has been selected in the address widget.',
            SCOPE_BILLING_ADDRESS_TOOLTIP: 'Gives access to the full billing address via the GetOrderReferenceDetails API call as soon as an credit card has been selected in the wallet widget.',

            POPUP: 'Popup',

            IMPLEMENTATION_TYPE: 'Implementation Type',
            IMPLEMENTATION_TYPE_ONETIME: 'One Time',
            IMPLEMENTATION_TYPE_AUTOPAY: 'Auto Pay',

            REDIRECT_URL: "Redirect URL/Javascript Function",
            ERROR_ON_ERROR_COMMENT: "// Error handling code \n              // We also recommend that you implement an onError handler in your code. \n              // @see https://payments.amazon.com/documentation/lpwa/201954960",
            ERROR_COMMENT_AddressNotModifiable: "// You cannot modify the shipping address when the order reference is in the given state.",
            ERROR_COMMENT_BuyerNotAssociated: "// The buyer is not associated with the given order reference. \n                    // The buyer must sign in before you render the widget.",
            ERROR_COMMENT_BuyerSessionExpired: "// The buyer's session with Amazon has expired. \n                    // The buyer must sign in before you render the widget.",
            ERROR_COMMENT_InvalidAccountStatus: "// Your merchant account is not in an appropriate state to execute this request. \n                    // For example, it has been suspended or you have not completed registration.",
            ERROR_COMMENT_InvalidOrderReferenceId: "// The specified order reference identifier is invalid.",
            ERROR_COMMENT_InvalidParameterValue: "// The value assigned to the specified parameter is not valid.",
            ERROR_COMMENT_InvalidSellerId: "// The merchant identifier that you have provided is invalid. Specify a valid SellerId.",
            ERROR_COMMENT_MissingParameter: "// The specified parameter is missing and must be provided.",
            ERROR_COMMENT_PaymentMethodNotModifiable: "// You cannot modify the payment method when the order reference is in the given state.",
            ERROR_COMMENT_ReleaseEnvironmentMismatch: "// You have attempted to render a widget in a release environment that does not match the release environment of the Order Reference object. \n                    // The release environment of the widget and the Order Reference object must match.",
            ERROR_COMMENT_StaleOrderReference: "// The specified order reference was not confirmed in the allowed time and is now canceled. \n                    // You cannot associate a payment method and an address with a canceled order reference.",
            ERROR_COMMENT_UnknownError: "// There was an unknown error in the service.",
            ERROR_COMMENT_UnknownError2: "// Oh My God, What's going on?",

            COMMENT_onAddressSelect: "// do stuff here like recalculate tax and/or shipping",
            INCLUDE_SCRIPT : "Script async attribute"
        });
        $translateProvider.translations('JP', {
            CLIENT_PARAMETERS: 'クライアントパラメータ',
            BUTTON_WIDGET_PARAMETERS: 'ウィジェットボタンパラメータ',
            CLIENT_ID: 'クライアントID',
            SELLER_ID: 'セラーID',
            REGION: 'リージョン',

            BUTTON_COLOR: 'ボタンの色',
            BUTTON_TYPE: 'ボタンの種類',
            BUTTON_SIZE: 'ボタンサイズ',

            WIDGET_SIZE: 'ウィジェットサイズ',
            WIDGET_SIZE_RESPONSIVE: 'レスポンシブ',
            WIDGET_SIZE_SMARTPHONE: 'スマートフォン',
            WIDGET_SIZE_CUSTOM: 'カスタム',
            WIDGET_SIZE_WIDTH_TOOLTIP: 'ウィジエットの幅(px)を指定してください。',
            WIDGET_SIZE_HEIGHT_TOOLTIP: 'ウィジエットの高さ(px)を指定してください。',

            BUTTON_LABEL_LWA: 'Amazonアカウントでログイン',
            BUTTON_LABEL_PWA: 'Amazonアカウントでお支払い',

            TARGET: '表示対象',
            TARGET_LOGIN_BUTTON: 'ログインボタン',
            TARGET_ADDRESS_BOOK_WIDGET: 'アドレス帳ウィジェット',
            TARGET_WALLET_WIDGET: 'お支払い方法ウィジェット',
            TARGET_CONSENT_WIDGET: '定期支払同意ウィジェット',

            SCOPE: 'スコープ',
            SCOPE_TOOLTIP: '購入者の情報を取得するスコープを設定します。',
            SCOPE_PROFILE_TOOLTIP: 'ログイン後、購入者のプロファイル (ユーザ名, メールアドレス, ユーザID) が取得可能となります。',
            SCOPE_WIDGET_TOOLTIP: 'サイト上で、ウィジェット(アドレス帳、お支払い方法)を表示するために必要となります。',
            SCOPE_ADDRESS_TOOLTIP: 'GetOrderReferenceDetails APIにて、購入者がアドレス帳ウィジェットで選択したお届け先の住所を取得する際に必要となります。',
            SCOPE_BILLING_ADDRESS_TOOLTIP: 'GetOrderReferenceDetails APIにて、購入者がお支払い方法ウィジェットで選択したクレジットカードの請求先住所を取得する際に必要となります。',

            POPUP: 'POPUP',

            IMPLEMENTATION_TYPE: '実装種別',
            IMPLEMENTATION_TYPE_ONETIME: 'ワンタイムペイメント',
            IMPLEMENTATION_TYPE_AUTOPAY: '定期支払い',

            REDIRECT_URL: "リダイレクト先URL/Javascript関数",
            ERROR_ON_ERROR_COMMENT: "// エラー処理 \n              // エラーが発生した際にonErrorハンドラーを使って処理することをお勧めします。 \n              // @see https://payments.amazon.com/documentation/lpwa/201954960",
            ERROR_COMMENT_AddressNotModifiable: "// オーダーリファレンスIDのステータスが正しくない場合は、お届け先の住所を変更することができません。",
            ERROR_COMMENT_BuyerNotAssociated: "// 購入者とリファレンスIDが正しく関連付けられていません。 \n            　　　    // ウィジェットを表示する前に購入者はログインする必要があります。",
            ERROR_COMMENT_BuyerSessionExpired: "// 購入者のセッションの有効期限が切れました。 \n       　　　　        // ウィジェットを表示する前に購入者はログインする必要があります。",
            ERROR_COMMENT_InvalidAccountStatus: "// マーチャントID（セラーID）がリクエストを実行する為に適切な状態ではありません。 \n      　　　　         // 考えられる理由 ： 制限がかかっているか、正しく登録が完了されていません。",
            ERROR_COMMENT_InvalidOrderReferenceId: "// オーダーリファレンスIDが正しくありません。",
            ERROR_COMMENT_InvalidParameterValue: "// 指定されたパラメータの値が正しくありません。",
            ERROR_COMMENT_InvalidSellerId: "// マーチャントID（セラーID）が正しくありません。",
            ERROR_COMMENT_MissingParameter: "// 指定されたパラメータが正しくありません。",
            ERROR_COMMENT_PaymentMethodNotModifiable: "// オーダーリファレンスIDのステータスが正しくない場合はお支払い方法を変更することができません。",
            ERROR_COMMENT_ReleaseEnvironmentMismatch: "// 使用しているオーダーリファレンスオブジェクトがリリース環境と一致しません。",
            ERROR_COMMENT_StaleOrderReference: "// 使用しているオーダーリファレンスIDがキャンセルされています。 \n                　　　// キャンセルされたオーダーリファレンスIDでウィジェットを関連付けすることはできません。",
            ERROR_COMMENT_UnknownError: "// 不明なエラーが発生しました。(UnknownError)",
            ERROR_COMMENT_UnknownError2: "// 不明なエラーが発生しました。",

            COMMENT_onAddressSelect: "// お届け先の住所が変更された時に呼び出されます、ここで手数料などの再計算ができます。",
            INCLUDE_SCRIPT : "Script async 属性"
        });
        $translateProvider.preferredLanguage('US')
            .fallbackLanguage('US');
        $locationProvider.html5Mode(true);

    }).directive('highlight', function ($interpolate, $window) {
    return {
        restrict: 'EA',
        scope: true,
        compile: function (tElem, tAttrs) {
            var interpolateFn = $interpolate(tElem.html(), true);
            tElem.html(''); // stop automatic intepolation

            return function (scope, elem, attrs) {
                scope.$watch(interpolateFn, function (value) {
                    elem.html(hljs.highlight('javascript', value).value);
                });
            }
        }
    };
});
