import os
import pandas as pd
import numpy as np
import joblib

def load_model(model_path):
    return joblib.load(model_path)

def load_new_data(csv_path):
    df = pd.read_csv(csv_path, parse_dates=['Date'])
    df['Date'] = pd.to_datetime(df['Date']).dt.tz_localize(None)
    df.set_index('Date', inplace=True)
    if 'Close' in df.columns:
        df.rename(columns={'Close': 'Actual'}, inplace=True)
    elif 'Actual' not in df.columns:
        raise KeyError("The dataframe does not contain a 'Close' or 'Actual' column.")
    return df

def preprocess_data(df, lookback=30):
    if 'Actual' not in df.columns:
        raise KeyError("The dataframe does not contain 'Actual' column necessary for preprocessing.")
    lagged_data = []
    for i in range(lookback, len(df)):
        lagged_data.append(df['Actual'].iloc[i - lookback:i].values.flatten())
    return np.array(lagged_data)

def predict_with_model(model, data):
    if data.ndim == 1:
        data = data.reshape(1, -1)
    return model.predict(data)

def calculate_moving_average(prices, window):
    return pd.Series(prices).rolling(window=window).mean().values

def calculate_rsi(prices, window=14):
    deltas = np.diff(prices)
    seed = deltas[:window + 1]
    up = seed[seed >= 0].sum() / window
    down = -seed[seed < 0].sum() / window
    rs = up / down
    rsi = np.zeros_like(prices)
    rsi[:window] = 100. - 100. / (1. + rs)

    for i in range(window, len(prices)):
        delta = deltas[i - 1]

        if delta > 0:
            upval = delta
            downval = 0.
        else:
            upval = 0.
            downval = -delta

        up = (up * (window - 1) + upval) / window
        down = (down * (window - 1) + downval) / window
        rs = up / down
        rsi[i] = 100. - 100. / (1. + rs)

    return rsi

def generate_trade_signals(actual_prices, predicted_prices, window_short=5, window_long=15, divergence_threshold=0.01):
    trade_signals = []
    rsi_signals = []

    short_ma = calculate_moving_average(actual_prices, window_short)
    long_ma = calculate_moving_average(actual_prices, window_long)
    rsi = calculate_rsi(actual_prices)

    for i in range(len(predicted_prices)):
        current_price = actual_prices[i - 1]
        predicted_change = (predicted_prices[i] - current_price) / current_price
        divergence = abs(predicted_prices[i] - current_price) / current_price

        # RSI signals
        if rsi[i] < 30:
            rsi_signals.append('Oversold')
        elif rsi[i] > 70:
            rsi_signals.append('Overbought')
        else:
            rsi_signals.append('Neutral')

        # Trend Confirmation
        if short_ma[i] > long_ma[i]:
            trend = 'up'
        else:
            trend = 'down'

        # Buy Logic
        if predicted_change > 0 and trend == 'up' and divergence > divergence_threshold:
            trade_signals.append('Buy')
        # Sell Logic
        elif predicted_change < 0 and trend == 'down' and divergence > divergence_threshold:
            trade_signals.append('Sell')
        else:
            trade_signals.append('Hold')

    return trade_signals, rsi_signals

def generate_future_trade_signals(predicted_prices):
    signals = []
    for i in range(len(predicted_prices) - 1):
        if predicted_prices[i + 1] > predicted_prices[i]:  # Checking for upward momentum.
            signals.append('Buy')
        else:
            signals.append('Sell')
    signals.append('N/A')  # For the last prediction which does not have a next day
    return signals

def predict_future_prices(model, df, lookback=30, days=30):
    # Ensure that the DataFrame index is a datetime index
    df.index = pd.to_datetime(df.index)
    last_known_date = df.index[-1]

    # Check that the last known date is a proper timestamp
    if not isinstance(last_known_date, pd.Timestamp):
        raise ValueError("The index of the DataFrame is not in the correct datetime format.")

    # Generate a range of future dates starting from the day after the last known date
    future_dates = pd.date_range(start=last_known_date + pd.Timedelta(days=1), periods=days)

    # Prepare a list of the last 'lookback' known prices to be used for prediction
    recent_prices = df['Actual'].tail(lookback).tolist()

    # Initialize a DataFrame to hold future predictions with the future dates as the index
    future_predictions = pd.DataFrame(index=future_dates, columns=['Predicted', 'Trade Signal'])

    # Predict future prices for each date in the future_dates range
    for date in future_dates:
        recent_prices_array = np.array(recent_prices[-lookback:]).reshape(1, -1)
        predicted_price = model.predict(recent_prices_array)[0]
        future_predictions.loc[date, 'Predicted'] = predicted_price
        recent_prices.append(predicted_price)

    # Generate trade signals for the predicted prices
    future_predictions['Trade Signal'] = generate_future_trade_signals(future_predictions['Predicted'].values)

    return future_predictions



def save_predictions_to_csv(df, predictions, output_path, coin_name):
    df = df.iloc[-len(predictions):].copy()
    df['Predicted'] = predictions
    df['Trade Signal'], df['RSI Signal'] = generate_trade_signals(df['Actual'].values, df['Predicted'].values)

    predictions_file = os.path.join(output_path, f'{coin_name}_predictions.csv')
    df.to_csv(predictions_file, columns=['Actual', 'Predicted', 'Trade Signal', 'RSI Signal'], date_format='%Y-%m-%d')
    print(f"Predictions saved to {predictions_file}")


# Paths #################################################################################
model_folder = '/RF_pickles'
data_folder = '/DF 2013-2023'
output_folder_A = '/Actual_Prediction'
output_folder_F = '/Future_Prediction'
# Paths #################################################################################

for model_file in os.listdir(model_folder):
    if model_file.endswith('.pkl'):
        coin_name = model_file.split('_')[0]
        model_path = os.path.join(model_folder, model_file)
        csv_path = os.path.join(data_folder, f'{coin_name}_data.csv')

        if not os.path.exists(csv_path):
            print(f"No CSV data for {coin_name}. Skipping...")
            continue

        model = load_model(model_path)
        new_data = load_new_data(csv_path)

        print(f"Last known date in new data for {coin_name}: {new_data.index[-1]}")

        if new_data.index.tz is not None:
            new_data.index = new_data.index.tz_localize(None)

        preprocessed_data = preprocess_data(new_data)

        if preprocessed_data.shape[0] > 0:
            predictions = predict_with_model(model, preprocessed_data)
            save_predictions_to_csv(new_data, predictions, output_folder_A, coin_name)

            saved_predictions_df = pd.read_csv(
                os.path.join(output_folder_A, f'{coin_name}_predictions.csv'),
                parse_dates=['Date']
            )

            saved_predictions_df['Date'] = pd.to_datetime(saved_predictions_df['Date']).dt.tz_localize(None)
            saved_predictions_df.set_index('Date', inplace=True)

            print(f"Last known date in saved predictions for {coin_name}: {saved_predictions_df.index[-1]}")

            if 'Actual' in saved_predictions_df.columns:
                new_data = new_data.combine_first(saved_predictions_df[['Actual']])

            future_predictions = predict_future_prices(model, new_data, lookback=30, days=30)

            print(f"First future date for {coin_name}: {future_predictions.index[0]}")
            print(f"Last future date for {coin_name}: {future_predictions.index[-1]}")

            future_predictions_file = os.path.join(output_folder_F, f'{coin_name}_future_predictions.csv')
            future_predictions.reset_index(inplace=True)
            future_predictions.rename(columns={'index': 'Date'}, inplace=True)
            future_predictions.to_csv(future_predictions_file, columns=['Date', 'Predicted', 'Trade Signal'],
                                      date_format='%Y-%m-%d', index=False)
            print(f"Future predictions saved to {future_predictions_file}")