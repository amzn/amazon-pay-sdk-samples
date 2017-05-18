import json
import random
# Import 'Flask' for creating a flask application,
# 'session' to store and retrieve session variables in every view
# 'render_template' to render a HTML template with the given context variables
# 'url_for' to get the URL corresponding to a view
# 'redirect' to redirect to a given URL
# 'request' to access the request object which contains the request data
# 'flash' to display messages in the template
from flask import Flask, session, render_template, url_for, redirect, request, flash

app = Flask(__name__)
app.secret_key = 'my_super_secret_key!'


@app.route('/')
def index():
    return render_template('base.html')


@app.route('/cart', methods=['POST'])
def cart():
    session['merchant_id'] = request.form['merchant-id']
    session['mws_access_key'] = request.form['mws-access-key']
    session['mws_secret_key'] = request.form['mws-secret-key']
    session['client_id'] = request.form['client-id']
    return render_template('cart.html')


@app.route('/set', methods=['GET'])
def set():
    session['access_token'] = request.args.get('access_token')
    return render_template('set.html')


@app.route('/confirm', methods=['POST'])
def confirm():
    from amazon_pay.client import AmazonPayClient

    client = AmazonPayClient(
        mws_access_key=session['mws_access_key'],
        mws_secret_key=session['mws_secret_key'],
        merchant_id=session['merchant_id'],
        sandbox=True,
        region='na',
        currency_code='USD',
        log_enabled=True,
        log_file_name="log.txt",
        log_level="DEBUG")

    response = client.confirm_billing_agreement(
        amazon_billing_agreement_id=session['billing_agreement_id'])

    pretty = json.dumps(
        json.loads(
            response.to_json()),
        indent=4)
    return render_template('confirm.html', confirm_response=pretty)


@app.route('/authorize', methods=['POST'])
def authorize():
    from amazon_pay.client import AmazonPayClient

    client = AmazonPayClient(
        mws_access_key=session['mws_access_key'],
        mws_secret_key=session['mws_secret_key'],
        merchant_id=session['merchant_id'],
        sandbox=True,
        region='na',
        currency_code='USD',
        log_enabled=True,
        log_file_name="log.txt",
        log_level="DEBUG")

    response = client.authorize_on_billing_agreement(
        amazon_billing_agreement_id=session['billing_agreement_id'],
        authorization_reference_id=rand(),
        authorization_amount='1.99',
        seller_authorization_note='Python SDK payment authorization.',
        transaction_timeout=0,
        capture_now=True)

    response = client.get_billing_agreement_details(
        amazon_billing_agreement_id=session['billing_agreement_id'],
        address_consent_token=session['access_token'])

    pretty = json.dumps(
        json.loads(
            response.to_json()),
        indent=4)
    return pretty


@app.route('/get_details', methods=['POST'])
def get_details():
    from amazon_pay.client import AmazonPayClient

    client = AmazonPayClient(
        mws_access_key=session['mws_access_key'],
        mws_secret_key=session['mws_secret_key'],
        merchant_id=session['merchant_id'],
        sandbox=True,
        region='na',
        currency_code='USD',
        log_enabled=True,
        log_file_name="log.txt",
        log_level="DEBUG")

    billing_agreement_id = request.form['billingAgreementId']
    session['billing_agreement_id'] = billing_agreement_id

    response = client.set_billing_agreement_details(
        amazon_billing_agreement_id=billing_agreement_id,
        store_name='Amazon Pay Python SDK')

    if response.success:
        response = client.get_billing_agreement_details(
            amazon_billing_agreement_id=billing_agreement_id,
            address_consent_token=session['access_token'])

    pretty = json.dumps(
        json.loads(
            response.to_json()),
        indent=4)

    return pretty


def rand():
    return random.randint(0, 9999) + random.randint(0, 9999)


if __name__ == '__main__':
    app.run(debug=True)
