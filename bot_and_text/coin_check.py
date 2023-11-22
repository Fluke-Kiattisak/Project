import base64
import json
import requests
import hmac
import hashlib
import time

class coinCheck(object):

    def __init__(self, ACCESS_KEY, SECRET_KEY, PASS_PHRASE):
        self.access_key = ACCESS_KEY
        self.secret_key = SECRET_KEY
        self.pass_phrase = PASS_PHRASE
        self.url = 'https://api.kucoin.com'

    def _request(self, endpoint, params=None, method='GET'):
        ACCESS_NONCE = int(time.time() * 1000)
        full_url = self.url + endpoint
        str_to_sign = str(ACCESS_NONCE) + method + endpoint

        if method == 'POST' and params:
            body = json.dumps(params)
            str_to_sign += body
        else:
            body = ''

        SIGNATURE = base64.b64encode(
            hmac.new(self.secret_key.encode('utf-8'), str_to_sign.encode('utf-8'), hashlib.sha256).digest())

        passphrase = base64.b64encode(hmac.new(self.secret_key.encode('utf-8'), self.pass_phrase.encode('utf-8'), hashlib.sha256).digest())

        headers = {
            "KC-API-SIGN": SIGNATURE,
            "KC-API-TIMESTAMP": str(ACCESS_NONCE),
            "KC-API-KEY": self.access_key,
            "KC-API-PASSPHRASE": passphrase,
            "KC-API-KEY-VERSION": "2",
            "Content-Type": "application/json"
        }

        if method == 'GET':
            req = requests.request('get', url=full_url, headers=headers)
        else:
            req = requests.post(url=full_url, headers=headers, data=body)

        return req.json()

    def ticker(self):
        endpoint = '/api/v1/market/orderbook/level1?symbol=BTC-USDT'
        response = self._request(endpoint=endpoint)
        return response

    @property
    def last(self):
        return self.ticker()['data']['price']

    def order_books(self, params=None):
        endpoint = '/api/v3/market/orderbook/level2'
        return self._request(endpoint=endpoint, params=params)

    def balance(self):
        endpoint = '/api/v1/accounts'
        return self._request(endpoint=endpoint)

    @property
    def position(self):
        balance = self.balance()
        return {k: v for k, v in balance.items()
            if isinstance(v, str) and float(v)}

    def order(self, params):
        endpoint = '/api/v1/orders'
        return self._request(endpoint=endpoint, params=params, method='POST')

    def spot_trade(self, symbol, side, price, size, order_type="limit"):
        """
        Place a spot order.

        Parameters:
        - symbol: Trading pair (e.g. "BTC-USDT")
        - side: Order side ("buy" or "sell")
        - price: Price per cryptocurrency unit
        - size: Amount of cryptocurrency to buy/sell
        - order_type: Type of order ("limit" or "market")

        Returns:
        - JSON response from the KuCoin API
        """

        params = {
            "clientOid": str(int(time.time() * 1000)),  # unique order id
            "side": side,
            "symbol": symbol,
            "price": price,
            "size": size,
            "type": order_type,
        }

        endpoint = "/api/v1/orders"
        return self._request(endpoint=endpoint, params=params, method='POST')