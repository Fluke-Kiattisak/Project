import yfinance as yf
import pandas as pd
import matplotlib.pyplot as plt
import os
import concurrent.futures

class DataRetriever:
    def __init__(self, symbol, start_date, end_date):
        self.symbol = symbol
        self.start_date = start_date
        self.end_date = end_date

    def retrieve_data(self):
        ticker = yf.Ticker(self.symbol)
        data = ticker.history(start=self.start_date, end=self.end_date)
        return data

    def save_to_csv(self, filename):
        data = self.retrieve_data()
        if os.path.exists(filename):
            existing_data = pd.read_csv(filename, parse_dates=True, index_col='Date')
            data = data[~data.index.isin(existing_data.index)]
            data = pd.concat([existing_data, data])
        data.to_csv(filename)

class DataPlotter:
    def __init__(self, data, symbol):
        self.data = data
        self.symbol = symbol

    # def plot_graph(self):
    #     fig, ax1 = plt.subplots(figsize=(10, 6))
    #
    #     ax1.plot(self.data.index, self.data['Close'], 'b-', label='Closing Price')
    #     ax1.set_xlabel('Date')
    #     ax1.set_ylabel('Closing Price', color='b')
    #
    #     ax2 = ax1.twinx()
    #     ax2.plot(self.data.index, self.data['Open'], 'r-', label='Opening Price')
    #     ax2.set_ylabel('Opening Price', color='r')
    #
    #     plt.title(f"Historical Data - {self.symbol}")
    #     plt.grid(True)
    #
    #     lines1, labels1 = ax1.get_legend_handles_labels()
    #     lines2, labels2 = ax2.get_legend_handles_labels()
    #     ax1.legend(lines1 + lines2, labels1 + labels2)
    #
    #     plt.show()

    # def save_plot_to_image(self, filename):
    #     fig, ax1 = plt.subplots(figsize=(10, 6))
    #
    #     ax1.plot(self.data.index, self.data['Close'], 'b-', label='Closing Price')
    #     ax1.set_xlabel('Date')
    #     ax1.set_ylabel('Closing Price', color='b')
    #
    #     ax2 = ax1.twinx()
    #     ax2.plot(self.data.index, self.data['Open'], 'r-', label='Opening Price')
    #     ax2.set_ylabel('Opening Price', color='r')
    #
    #     plt.title(f"Historical Data - {self.symbol}")
    #     plt.grid(True)
    #
    #     lines1, labels1 = ax1.get_legend_handles_labels()
    #     lines2, labels2 = ax2.get_legend_handles_labels()
    #     ax1.legend(lines1 + lines2, labels1 + labels2)
    #
    #     plt.savefig(filename)
    #     plt.close()

class DataProcessor:
    def __init__(self, symbols_file, start_date, end_date, save_path):
        self.symbols_file = symbols_file
        self.start_date = start_date
        self.end_date = end_date
        self.save_path = save_path

    def read_symbols_from_file(self):
        with open(self.symbols_file, 'r') as file:
            symbols = [line.strip() for line in file.readlines()]
        return symbols

    def process_symbol(self, symbol):
        data_retriever = DataRetriever(symbol, self.start_date, self.end_date)
        filename = os.path.join(self.save_path, f"{symbol}_data.csv")
        data_retriever.save_to_csv(filename)
        # data_plotter = DataPlotter(data_retriever.retrieve_data(), symbol)
        # data_plotter.plot_graph()

    def process_symbols_concurrently(self):
        symbols = self.read_symbols_from_file()
        with concurrent.futures.ThreadPoolExecutor() as executor:
            executor.map(self.process_symbol, symbols)

# Paths #################################################################################
start_date = "2014-09-17"
end_date = "2023-11-16"
save_directory = "/DF 2013-2023"
symbols_file = "crypto_coins.txt"
data_processor = DataProcessor(symbols_file, start_date, end_date, save_directory)
data_processor.process_symbols_concurrently()
# Paths #################################################################################

# data_plotter = DataPlotter(data_retriever.retrieve_data(), symbol)
# data_plotter.plot_graph()
# data_plotter.save_plot_to_image(f"{symbol}_plot.png")