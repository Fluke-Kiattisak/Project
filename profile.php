<?php
session_start();

function generateSignature($secret, $timestamp, $method, $endpoint, $body = '') {
    $strToSign = $timestamp . $method . $endpoint . $body;
    return base64_encode(hash_hmac('sha256', $strToSign, $secret, true));
}

$kucoinData = "";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT ACCESS_KEY, SECRET_KEY, PASS_PHRASE, UserIP FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $accessKey = $row['ACCESS_KEY'];
        $secretKey = $row['SECRET_KEY'];
        $passPhrase = $row['PASS_PHRASE'];
        $userIP = $row['UserIP']; // Assuming UserIP is stored

        // Example API request: Fetching Account Balance
        $balanceData = makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, "/api/v1/accounts", 'GET', '', $userIP);

        // Format and display data
        $kucoinData = "<b>Balance:</b> " . json_encode($balanceData);
    } else {
        $kucoinData = "API keys not found for the user.";
    }
    $conn->close();
} else {
    $kucoinData = "User is not logged in or UserID is not set in session.";
}

function makeKucoinApiRequest($accessKey, $secretKey, $passPhrase, $endpoint, $method = 'GET', $body = '', $userIP = '') {
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
    }
    $headers = array(
        "KC-API-KEY: $accessKey",
        "KC-API-SIGN: $signature",
        "KC-API-TIMESTAMP: $timestamp",
        "KC-API-PASSPHRASE: $passphrase",
        "KC-API-KEY-VERSION: 2"
    );
    if (!empty($userIP)) {
        $headers[] = "X-USER-IP: $userIP"; // Adjust as per KUCOIN's requirement
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($response, true);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crypto Prediction Website</title>
    <!-- Other head elements -->
</head>
<body class="bg-light">
    <div class="container">
        <!-- Navigation and other content -->

        <!-- KUCOIN Data Display -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">KUCOIN Data</h2>
                <p class="card-text"><?php echo htmlspecialchars($kucoinData); ?></p>
            </div>
        </div>

        <!-- Footer and other content -->
    </div>
</body>
</html>


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
                        <a class="nav-link" href="Get-Start.html" >Current coin value</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact</a>
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
        <        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h2 class="card-title">KUCOIN Data</h2>
                <p class="card-text"><?php echo htmlspecialchars($kucoinData); ?></p>
            </div>
        </div>

        <footer class="text-center mt-5 py-4">
            <p>&copy; 2023 CryptoPredictions. All rights reserved.</p>
        </footer>
    </div>

</body>

</html>