import os
import html
import json
import time
import random
import logging
# Import 'Flask' for creating a flask application,
# 'session' to store and retrieve session variables in every view
# 'render_template' to render a HTML template with the given context variables
# 'url_for' to get the URL corresponding to a view
# 'redirect' to redirect to a given URL
# 'request' to access the request object which contains the request data
# 'flash' to display messages in the template
from flask import Flask, session, render_template, url_for, redirect, request, flash
from amazon_pay.client import AmazonPayClient

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
    session['success_url'] = request.form['success-url']
    session['failure_url'] = request.form['failure-url']
    session['order_reference_id'] = 'S01-9969307-1083016'
    return render_template('cart.html')


@app.route('/set', methods=['GET'])
def set():
    session['access_token'] = request.args.get('access_token')
    return render_template('set.html')


@app.route('/confirm', methods=['POST'])
def confirm():
    from amazon_pay.client import AmazonPayClient

    pretty_confirm = None
    pretty_authorize = None

    client = AmazonPayClient(
        mws_access_key=session['mws_access_key'],
        mws_secret_key=session['mws_secret_key'],
        merchant_id=session['merchant_id'],
        sandbox=True,
        region='eu',
        currency_code='EUR',
        log_enabled=True,
        log_file_name="log.log",
        log_level="DEBUG")

    print(session)
    if not str(session['failure_url']):
       response = client.confirm_order_reference(
            amazon_order_reference_id=session['order_reference_id'],
            success_url=session['success_url'],
            failure_url=None)
    else:
       response = client.confirm_order_reference(
            amazon_order_reference_id=session['order_reference_id'],
            success_url=session['success_url'],
            failure_url=session['failure_url'])

    pretty_confirm = json.dumps(
        json.loads(
            response.to_json()),
        indent=4)

    return pretty_confirm


@app.route('/get_details', methods=['POST'])
def get_details():
    from amazon_pay.client import AmazonPayClient

    client = AmazonPayClient(
        mws_access_key=session['mws_access_key'],
        mws_secret_key=session['mws_secret_key'],
        merchant_id=session['merchant_id'],
        sandbox=True,
        region='eu',
        currency_code='EUR',
        log_enabled=True,
        log_file_name="log.log",
        log_level="DEBUG")

    order_reference_id = request.form['orderReferenceId']
    session['order_reference_id'] = order_reference_id

    print(session['order_reference_id'])

    response = client.set_order_reference_details(
        amazon_order_reference_id=order_reference_id,
        order_total='19.95')

    if response.success:
        response = client.get_order_reference_details(
            amazon_order_reference_id=order_reference_id,
            address_consent_token=session['access_token'])

    pretty = json.dumps(
        json.loads(
            response.to_json()),
        indent=4)

    return pretty

@app.route('/finish', methods=['GET'])
def finish():
    from amazon_pay.client import AmazonPayClient

    pretty_confirm = None
    pretty_authorize = None

    client = AmazonPayClient(
        mws_access_key=session['mws_access_key'],
        mws_secret_key=session['mws_secret_key'],
        merchant_id=session['merchant_id'],
        sandbox=True,
        region='eu',
        currency_code='EUR',
        log_enabled=True,
        log_file_name="log.log",
        log_level="DEBUG")

    response = client.authorize(
        amazon_order_reference_id=session['order_reference_id'],
        authorization_reference_id=rand(),
        authorization_amount='19.95',
        transaction_timeout=0,
        capture_now=False)

    pretty_authorize = json.dumps(
        json.loads(
            response.to_json()),
        indent=4)
    if response.success:
        return render_template('confirm.html', authorize=pretty_authorize)
    else:
        client.cancel_order_reference(
          amazon_order_reference_id=session['order_reference_id'],
          cancelation_reason=None,
          merchant_id=session['merchant_id'],
          mws_auth_token=None)
    #TODO add error handling for canceled orders
    return pretty_authorize

def rand():
    return random.randint(0, 9999) + random.randint(0, 9999)


if __name__ == '__main__':
    app.run(host='0.0.0.0',port='5000')
