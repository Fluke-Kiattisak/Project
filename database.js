const mongoose = require('mongoose');

mongoose.connect('mongodb://localhost/your-database-name', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

const Schema = mongoose.Schema;

// Define the User schema
const userSchema = new Schema({
  UserID: { type: Number, required: true, unique: true },
  Username: String,
  Email: String,
  Password: String,
  RegistrationDate: Date,
});

// Define the Predictions schema
const predictionSchema = new Schema({
  PredictionID: { type: Number, required: true, unique: true },
  UserID: { type: Number, ref: 'User' },
  DateOfPrediction: Date,
  PredictedPrice: Number,
  ActualPrice: Number,
  TradeSignal: String,
  RSISignal: String,
});

// Define the API Keys schema
const apiKeySchema = new Schema({
  KeyID: { type: Number, required: true, unique: true },
  UserID: { type: Number, ref: 'User' },
  AccessKey: String,
  SecretKey: String,
  Passphrase: String,
});

// Define the Trade History schema
const tradeHistorySchema = new Schema({
  TradeID: { type: Number, required: true, unique: true },
  UserID: { type: Number, ref: 'User' },
  DateTimeOfTrade: Date,
  TradeType: String, // 'Buy' or 'Sell'
  Amount: Number,
  PriceAtTrade: Number,
});

// Create models based on the schemas
const User = mongoose.model('User', userSchema);
const Prediction = mongoose.model('Prediction', predictionSchema);
const APIKey = mongoose.model('APIKey', apiKeySchema);
const TradeHistory = mongoose.model('TradeHistory', tradeHistorySchema);

module.exports = {
  User,
  Prediction,
  APIKey,
  TradeHistory,
};