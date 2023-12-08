<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function generateSignature($secret, $timestamp, $method, $endpoint, $body = '')
{
    $strToSign = $timestamp . $method . $endpoint . $body;
    return base64_encode(hash_hmac('sha256', $strToSign, $secret, true));
}

function makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, $endpoint, $method = 'GET', $body = '', $userIP = '')
{
    $baseUrl = "https://api.kucoin.com";
    $timestamp = round(microtime(true) * 1000);
    $signature = generateSignature($secretKey, $timestamp, $method, $endpoint, $body);
    $passphrase = base64_encode(hash_hmac('sha256', $passPhrase, $secretKey, true));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $headers[] = "Content-Type: application/json"; // Add Content-Type header for POST request
    }

    $headers[] = "KC-API-KEY: $accessKey";
    $headers[] = "KC-API-SIGN: $signature";
    $headers[] = "KC-API-TIMESTAMP: $timestamp";
    $headers[] = "KC-API-PASSPHRASE: $passphrase";
    $headers[] = "KC-API-KEY-VERSION: 2";

    if (!empty($userIP)) {
        $headers[] = "X-USER-IP: $userIP";
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($response, true);
}

function placeKucoinOrder($accessKey, $secretKey, $passPhrase, $symbol, $side, $type, $price, $size, $userIP = '')
{
    $endpoint = "/api/v1/orders";
    $method = 'POST';
    $clientOid = uniqid('order_');

    $body = json_encode(array(
        'clientOid' => $clientOid,
        'symbol' => $symbol,
        'side' => $side,
        'type' => $type,
        'price' => $price,
        'size' => $size,
    ));

    return makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, $endpoint, $method, $body, $userIP);
}

$kucoinData = "";
$dbServername = "localhost";
$dbUsername = "root"; // Database username
$dbPassword = ""; // Database password
$dbName = "project"; // Database name

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming user submits a form with 'coin_pair' and 'trade_percent' fields
    $coinPair = $_POST['coin_pair']; // e.g., 'BTC-USDT'
    $tradePercent = $_POST['trade_percent']; // e.g., 50 for 50%


    // Extract base currency from coin pair (assuming format is 'BASE-QUOTE')
    list($baseCurrency,) = explode('-', $coinPair);

    // Fetch balance for the base currency
    $balanceData = makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, "/api/v1/accounts?currency=" . $baseCurrency, 'GET', '', $userIP);

    // Calculate trade size based on percentage and balance
    $balanceAmount = $balanceData['data'][0]['balance']; // Adjust according to actual API response format
    $tradeSize = ($balanceAmount * $tradePercent) / 100;

    // Place an order with calculated trade size (modify 'price' as needed)
    $orderData = placeKucoinOrder($accessKey, $secretKey, $passPhrase, $coinPair, 'buy', 'market', '', $tradeSize, $userIP);
    $kucoinData = "<b>Order Response:</b> " . json_encode($orderData);
} else {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && isset($_SESSION['username'])) {
        $sessionUsername = $_SESSION['username'];

        $conn = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT ACCESS_KEY, SECRET_KEY, PASS_PHRASE, UserIP FROM user WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sessionUsername);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $accessKey = $row['ACCESS_KEY'];
            $secretKey = $row['SECRET_KEY'];
            $passPhrase = $row['PASS_PHRASE'];
            $userIP = $row['UserIP'];

            // Existing code to fetch API credentials and make balance request

            $balanceData = makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, "/api/v1/accounts", 'GET', '', $userIP);
            $kucoinData = "Balance: " . json_encode($balanceData);

            $symbol = "BTC-USDT"; // Replace with the actual symbol or fetch dynamically
            $tradeData = fetchKucoinTradeData($accessKey, $secretKey, $passPhrase, $symbol, $userIP);

            if (!empty($tradeData['error'])) {
                $kucoinData .= "Trade Data Error: " . json_encode($tradeData['error']);
            } else {
                $kucoinData .= "Trade Data: " . json_encode($tradeData);
            }
        } else {
            $kucoinData = "API keys not found for the user.";
        }
        $stmt->close();
        $conn->close();
    } else {
        $kucoinData = "User is not logged in or username is not set in session.";
    }
}
function getBalance($accessKey, $secretKey, $passPhrase, $currency, $userIP)
{
    $endpoint = "/api/v1/accounts?currency=" . $currency;
    return makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, $endpoint, 'GET', '', $userIP);
}
function fetchKucoinTradeData($accessKey, $secretKey, $passPhrase, $symbol, $userIP)
{
    // Replace with the actual endpoint for trade data
    $endpoint = "/api/v1/order/client-order=" . $symbol;
    return makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, $endpoint, 'GET', '', $userIP);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Crypto Prediction Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</head>

<body class="bg-light">
    <div class="container">
        <div class="text-center my-4">
            <h1>Welcome to CryptoPredictions</h1>
        </div>

        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded mb-4 p-3 shadow">
            <img src="logo.png" alt="CryptoPredictions Logo" style="width: 100px;">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Front-end.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Predictions.php">Predictions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Get-Start.php">Current coin value</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>

                    <li class="nav-item ml-3">
                        <input type="text" placeholder="Search..." class="form-control">
                    </li>
                    <li class="nav-item ml-2">
                        <button class="btn btn-primary">Search</button>
                    </li>
                    <li>
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) : ?>
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Welcome <?php echo $_SESSION['username'];  ?>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a class="dropdown-item" href="profile.php">Profile</a>
                                        <a class="dropdown-item" href="settings.php">Settings</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php">Logout</a>
                                    </div>
                                </li>
                            </ul>
                        <?php else : ?>
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="login.html">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="signup.html">Signup</a>
                                </li>
                            </ul>
                        <?php endif; ?>

                    </li>
                </ul>
            </div>


        </nav>
        <div class="ml-auto">

        </div>
        </nav>

        <head>
            <title>Crypto Trading</title>

        </head>

        <body>
            <div class="container">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">KUCOIN Data</h2>
                        <div class="kucoin-data-display">
                            <?php
                            echo '<div class="container">'; // Start of container div

                            $dataDisplayed = false;

                            if (isset($balanceData)) {
                                $balanceDataStr = is_array($balanceData) ? json_encode($balanceData) : $balanceData;
                                echo '<div class="data-section">';
                                echo '<h2>Balance</h2>';
                                echo '<p>' . htmlspecialchars($balanceDataStr) . '</p>';
                                echo '</div>';
                                $dataDisplayed = true;
                            }

                            if (isset($tradeData)) {
                                $tradeDataStr = is_array($tradeData) ? json_encode($tradeData) : $tradeData;
                                echo '<div class="data-section">';
                                echo '<h2>Trade Data</h2>';
                                echo '<p>' . htmlspecialchars($tradeDataStr) . '</p>';
                                echo '</div>';
                                $dataDisplayed = true;
                            }

                            if (!$dataDisplayed) {
                                $kucoinDataStr = is_array($kucoinData) ? json_encode($kucoinData) : $kucoinData;
                                echo '<div class="data-message">';
                                echo '<h2>Kucoin Data</h2>';
                                echo '<p>' . htmlspecialchars($kucoinDataStr) . '</p>';
                                echo '</div>';
                            }

                            echo '</div>'; // End of container div
                            ?>





                        </div>


                        <form action="" method="post">
                            <div class="form-group">
                                <label for="trade_action">Trade Action:</label>
                                <select id="trade_action" name="trade_action" class="form-control" required>
                                    <option value="buy">Buy</option>
                                    <option value="sell">Sell</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="coin_pair">Choose a coin pair:</label>
                                <input type="text" id="coin_pair" name="coin_pair" class="form-control" placeholder="e.g., BTC-USDT" required>
                            </div>

                            <div class="form-group">
                                <label for="trade_percent">Percentage of Balance to Trade:</label>
                                <input type="number" id="trade_percent" name="trade_percent" class="form-control" min="1" max="100" placeholder="e.g., 50 for 50%" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Trade</button>
                        </form>
                    </div>
                </div>
            </div>


            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                    line-height: 1.6;
                    
                }

                .container {
                    width: 100%;
                    overflow: hidden;
                    
                }

                /* Section Styles */
                .data-section,
                .data-message {
                    background-color: #fff;
                    padding: 20px;
                    margin: 20px 0;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .data-section h2,
                .data-message h2 {
                    color: #0056b3;
                    margin-bottom: 10px;
                    border-bottom: 2px solid #eee;
                    padding-bottom: 5px;
                    font-size: 22px;
                }

                .data-section p,
                .data-message p {
                    font-size: 16px;
                    color: #333;
                }

                /* Table styles for structured data */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }

                table th,
                table td {
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: left;
                }

                table th {
                    background-color: #f2f2f2;
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .container {
                        width: 95%;
                        padding: 0 10px;
                    }

                    .data-section p,
                    .data-message p {
                        font-size: 14px;
                    }
                }
            </style>
        </body>

        <footer class="text-center mt-5 py-4">
            <p>&copy; 2023 CryptoPredictions. All rights reserved.</p>
        </footer>
    </div>

</body>

</html>