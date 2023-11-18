
CREATE TABLE users (
    UserID SERIAL PRIMARY KEY,
    Username VARCHAR(255) UNIQUE NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    RegistrationDate TIMESTAMP
);

-- Create APIKey table
CREATE TABLE apikeys (
    KeyID SERIAL PRIMARY KEY,
    UserID INTEGER REFERENCES users(UserID),
    AccessKey VARCHAR(255),
    SecretKey VARCHAR(255),
    Passphrase VARCHAR(255)
);

-- Create TradeHistory table
CREATE TABLE tradehistory (
    TradeID SERIAL PRIMARY KEY,
    UserID INTEGER REFERENCES users(UserID),
    DateTimeOfTrade TIMESTAMP,
    TradeType VARCHAR(10), -- 'Buy' or 'Sell'
    Amount NUMERIC,
    PriceAtTrade NUMERIC
);

-- Create RiskManagement table
CREATE TABLE riskmanagement (
    UserID INTEGER REFERENCES users(UserID),
    PortSize NUMERIC NOT NULL,
    RiskPercentage NUMERIC NOT NULL,
    Automated BOOLEAN NOT NULL,
    PRIMARY KEY (UserID),
    CHECK (Automated IN (true, false))
);

-- Create RiskManagementSchema table
CREATE TABLE riskmanagementschema (
    UserID INTEGER REFERENCES users(UserID) PRIMARY KEY,
    PortfolioSize NUMERIC NOT NULL,
    RiskPerTrade NUMERIC NOT NULL,
    StopLossPercentage NUMERIC NOT NULL,
    TakeProfitPercentage NUMERIC NOT NULL,
    Automated BOOLEAN NOT NULL,
    VolatilityTolerance NUMERIC,
    Timeframe VARCHAR(10) CHECK (Timeframe IN ('short', 'medium', 'long')),
    Strategy VARCHAR(20) CHECK (Strategy IN ('conservative', 'balanced', 'aggressive')),
    MaxDrawdown NUMERIC NOT NULL,
    MaxConcurrentTrades INTEGER NOT NULL,
    MaxAssetAllocation NUMERIC,
    MaxSectorAllocation NUMERIC,
    BacktestingPeriod VARCHAR(255),
    HistoricalDataRange VARCHAR(255),
    RiskRewardRatio NUMERIC,
    PredicetedPrice NUMERIC
);
