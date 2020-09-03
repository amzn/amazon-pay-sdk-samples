#Use python3
#source activate python3

pip install amazon_pay

export FLASK_APP=recurring_payment.py
export FLASK_ENV=development
flask run

starts server on http://localhost:5000

