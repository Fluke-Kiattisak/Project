import configparser
from coin_check import coinCheck

conf = configparser.ConfigParser()
conf.read('config.ini')

ACCESS_KEY = conf['confid']['access_key']
SECRET_KEY = conf['confid']['secret_key']
PASS_PHRASE = conf['confid']['pass_phrase']

coincheck = coinCheck(ACCESS_KEY=ACCESS_KEY, SECRET_KEY=SECRET_KEY, PASS_PHRASE=PASS_PHRASE)
balance = coincheck.balance()

symbol = "BTC-USDT"
side = "buy"
price = "10000"
size = "0.01"

response = coincheck.spot_trade(symbol, side, price, size)

print(coincheck.last)
print(balance)
print(response)
