const express = require('express');
const path = require('path');
const favicon = require('serve-favicon');
const logger = require('morgan');
const cookieParser = require('cookie-parser');
const bodyParser = require('body-parser');
const session = require('express-session');
const swig = require('swig');
const methodOverride = require('method-override');
const axios = require('axios');
const Client = require('amazonpay').amazonPayClient;

const app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
//app.set('view engine', 'ejs');
app.set('view engine', 'html')
app.engine('html', swig.renderFile);

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(methodOverride());
app.use(session({
  secret: 'ak987239HKKl;nm3248',
  resave: true,
  saveUninitialized: true
}));
app.use(express.static(path.join(__dirname, 'public')));

app.get('/', (req,res) => {
  res.render('base.html');
});

app.post('/cart', (req,res) => {

  let configSettings = require('./config/settings');
  //Taking a shortcut here, and just setting the values from config file
  //instead of form
  req.session.merchant_id = configSettings['merchant_id'];
  req.session.mws_access_key = configSettings['mws_access_key'];
  req.session.mws_secret_key = configSettings['mws_secret_key'];
  req.session.client_id = configSettings['client_id'];

  res.render('cart.html',{session: req.session});

});

app.get('/set', (req,res) => {
  req.session.access_token = req.query.access_token;
  res.render('set.html',{session:req.session});
});

app.post('/confirm' , (req,res) => {

  console.log('/confirm: billingAgreementId = ' + req.session.billing_agreement_id);

  const configArgs = {
      'merchantId' : req.session.merchant_id,
      'accessKey' : req.session.mws_access_key,
      'secretKey' : req.session.mws_secret_key,
      'clientId' : req.session.client_id,
      'region' : 'us',
      'currencyCode' : 'USD',
      'sandbox' : true,
      'jsonResponse' : 'jsonString'
  };

  const client = new Client(configArgs);

  //confirm billing agreement
  const reqParam = {
    'amazonBillingAgreementId': req.session.billing_agreement_id
  };
  const response = client.confirmBillingAgreement(reqParam);

  response.then( result => {
    console.log( '/confirm = ' + result);
    let jsonOutput = JSON.parse(result).ConfirmBillingAgreementResult;
    console.log("ConfirmBillingAgreementResult jsonOutput = " + JSON.stringify(jsonOutput,null,4));
    res.render('confirm.html',{confirm_response: jsonOutput});
  })
  .catch( err => {
    console.log(err.body);
  });

});

app.post('/authorize', (req,res) => {
  const configArgs = {
      'merchantId' : req.session.merchant_id,
      'accessKey' : req.session.mws_access_key,
      'secretKey' : req.session.mws_secret_key,
      'clientId' : req.session.client_id,
      'region' : 'us',
      'currencyCode' : 'USD',
      'sandbox' : true,
      'jsonResponse' : 'jsonString'
  };

  const client = new Client(configArgs);

  const uuidv4 = require('uuid/v4');

  let reqParam = {
      'amazonBillingAgreementId': req.session.billing_agreement_id,
      'authorizationReferenceId': uuidv4().toString().replace(/-/g, ''),
      'amount': "3.45",
  };

  const response = client.authorizeOnBillingAgreement(reqParam);

  response.then( result => {
    console.log( '/authorize = ' + result);

    let jsonOutput = JSON.parse(result).AuthorizeOnBillingAgreementResult;
    console.log("AuthorizeOnBillingAgreementResult jsonOutput = " + JSON.stringify(jsonOutput,null,4));
    const reqParam = {
      'accessToken' : req.session.access_token,
      'amazonBillingAgreementId': req.session.billing_agreement_id
    };
    return client.getBillingAgreementDetails(reqParam);
  })
  .then( result => {
    console.log( '/authorize = ' + result);

    let jsonOutput = JSON.parse(result).GetBillingAgreementDetailsResult;
    console.log("GetBillingAgreementDetailsResult jsonOutput = " + JSON.stringify(jsonOutput,null,4));
    res.send(jsonOutput);
  })
  .catch( err => {
    console.log(err.body);
  });

});

app.post('/get_details', (req,res) => {

  req.session.billing_agreement_id = req.body.billingAgreementId;

  const configArgs = {
      'merchantId' : req.session.merchant_id,
      'accessKey' : req.session.mws_access_key,
      'secretKey' : req.session.mws_secret_key,
      'clientId' : req.session.client_id,
      'region' : 'us',
      'currencyCode' : 'USD',
      'sandbox' : true,
      'jsonResponse' : 'jsonString'
  };

  const client = new Client(configArgs);

  //confirm billing agreement
  const reqParam = {
    'accessToken' : req.session.access_token,
    'amazonBillingAgreementId': req.session.billing_agreement_id,
    storeName: "Amazon Pay Nodejs SDK"
  };
  const response = client.setBillingAgreementDetails(reqParam);

  response.then( result => {
    console.log( '/get_details = ' + result);

    let jsonOutput = JSON.parse(result).SetBillingAgreementDetailsResult;
    console.log("SetBillingAgreementDetailsResult jsonOutput = " + JSON.stringify(jsonOutput,null,4));
    const reqParam = {
      'accessToken' : req.session.access_token,
      'amazonBillingAgreementId': req.session.billing_agreement_id
    };
    return client.getBillingAgreementDetails(reqParam);
  })
  .then( result => {
    console.log( '/get_details = ' + result);

    let jsonOutput = JSON.parse(result).GetBillingAgreementDetailsResult;
    console.log("GetBillingAgreementDetailsResult jsonOutput = " + JSON.stringify(jsonOutput,null,4));
    res.send(jsonOutput);
  })
  .catch( err => {
    console.log(err.body);
  });

});


//==============================================================
// catch 404 and forward to error handler
app.use(function(req, res, next) {
  let err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
  app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {}
  });
});

module.exports = app;
