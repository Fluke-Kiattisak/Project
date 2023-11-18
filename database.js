const mongoose = require('mongoose');
const crypto = require('crypto');
mongoose.connect('mongodb://mongo:27017/Final-project', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

const Schema = mongoose.Schema;

const userSchema = new Schema({ 
  UserID: { type: Number, required: true, unique: true,
  default: function(){
    return crypto.randomBytes(4).readUInt32LE(0);
  }
  },
  Username: {type: String, required: true, unique: true},
  Email: {type: String, required: true, unique: true},
  Password: {type: String, required: true, unique: true},
  RegistrationDate: Date,
});

userSchema.pre('save', function(next){
  try{
    const salt = crypto.randomBytes(16).toString('hex');
    const hashPassword = crypto.createHash('sha256').update(this.Password + salt).digest('hex');
    this.password = hashPassword;
    this.salt = salt;
    next();
  }catch(error){
    next(error);
  }
});

const apiKeySchema = new Schema({
  KeyID: { type: Number, required: true, unique: true },
  UserID: { type: Number, ref: 'User' },
  AccessKey: String,
  SecretKey: String,
  Passphrase: String,
});

const tradeHistorySchema = new Schema({
  TradeID: { type: Number, required: true, unique: true },
  UserID: { type: Number, ref: 'User' }, 
  DateTimeOfTrade: Date,
  TradeType: String, // 'Buy' or 'Sell'
  Amount: Number,
  PriceAtTrade: Number,
});

const riskManagementQuestion = new Schema({
  UserID: {type: Number, ref:'User'},
  portSize: {type: Number, required: true},
  riskPercentage: {type: Number, required: true},
  automated: {type: Boolean, required: true},
  
});
const RiskManagementSchema = new Schema({
  UserID: { type: Number, ref: 'User', required: true },
  PortfolioSize: { type: Number, required: true },
  RiskPerTrade: { type: Number, required: true },
  StopLossPercentage: { type: Number, required: true },
  TakeProfitPercentage: { type: Number, required: true },
  Automated: { type: Boolean, required: true },
  VolatilityTolerance: { type: Number },
  Timeframe: { type: String, enum: ['short', 'medium', 'long'], required: true },
  Strategy: { type: String, enum: ['conservative', 'balanced', 'aggressive'], required: true },

  MaxDrawdown: { type: Number, required: true }, //the maximum percentage or amount a portfolio can drop from its peak before reaching a new peak. 

  MaxConcurrentTrades: { type: Number, required: true }, //the maximum number of trades that a user is willing to have open at the same time. 

  DiversificationRules: {
      MaxAssetAllocation: { type: Number }, //The maximum percentage of the total investment portfolio that can be allocated to a single asset.
      MaxSectorAllocation: { type: Number }, //the maximum percentage of the portfolio that can be invested in a single sector.
  },

  BacktestingPreferences: {
      Period: { type: String },
      HistoricalDataRange: { type: String },
  }, //the trading strategy is tested against historical data

  RiskRewardRatio: { type: Number }, //It's a calculation used by investors to compare the expected returns of an investment to the amount of risk they are taking. 
  predicetedPrice: {type: Number},
});
const User = mongoose.model('User', userSchema);
const Prediction = mongoose.model('Prediction', predictionSchema);
const APIKey = mongoose.model('APIKey', apiKeySchema);
const TradeHistory = mongoose.model('TradeHistory', tradeHistorySchema);
const RiskManagement = mongoose.model('RiskManagement', riskManagementQuestion);

module.exports = {
  User,
  Prediction,
  APIKey,
  TradeHistory,
};