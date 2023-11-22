<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Crypto Prediction Website</title>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap CSS if you are using Bootstrap components -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        header {
            padding: 1px;
            background-color: #dddddd;
            color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .logo img {
            width: 100px;
            /* Adjust as per your logo's aspect ratio */
        }

        nav ul {
            padding: 0;
            list-style-type: none;
            text-align: center;
        }

        nav ul li {
            display: inline;
            margin: 0 10px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        .search-box {
            margin-top: 0px;
        }

        .search-box input,
        .search-box button {
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;

        }

        .search-box button {
            margin-left: 5px;
            background-color: #007bff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="content">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="logo">
                    <img src="logo.png" alt="CryptoPredictions Logo">
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav" style = "margin-left: 370px";>
                    <!-- Your navigation links here -->
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="Front-end.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="Predictions.php">Predictions</a></li>
                        <li class="nav-item"><a class="nav-link" href="Get-Start.php">Coin prices</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <div class="search-box form-inline">
                    <input class="form-control mr-sm-2" type="text" placeholder="Search...">
                    <button class="btn btn-success my-2 my-sm-0" type="submit" style="font-family: Arial, sans-serif;">Search</button>
                </div>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) : ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Welcome <?php echo $_SESSION['username']; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="profile.php">Profile</a>
                                <a class="dropdown-item" href="settings.php">Settings</a>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                <?php else : ?>
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="login.html">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="signup.html">Signup</a></li>
                    </ul>
                <?php endif; ?>
    </div>
    </nav>
    </header>
    <!-- Additional content can be added here -->
    </div>

    <!-- Bootstrap JavaScript and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.9/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>


<section class="hero">
    <div class="hero-content">
        <h1>Welcome to CryptoPredictions</h1>
        <p>Stay ahead with accurate cryptocurrency price predictions.</p> <br>
        <a href="Get-Start.html" class="btn">Get Started</a>
    </div>
</section> <br>

<section class="crypto-container">
    <h2>Current Cryptocurrency Prices</h2>

    <div class="crypto-box-container">
        <div class="crypto-box" id="bitcoin-box">
            <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" alt="Bitcoin Logo" class="crypto-logo">
            <div class="crypto-name">Bitcoin</div>
            <div class="crypto-price" id="bitcoin-price">Loading...</div>
        </div>

        <div class="crypto-box" id="ethereum-box">
            <img src="https://cryptologos.cc/logos/ethereum-eth-logo.png" alt="Ethereum Logo" class="crypto-logo">
            <div class="crypto-name">Ethereum</div>
            <div class="crypto-price" id="ethereum-price">Loading...</div>
        </div>

        <div class="crypto-box" id="binancecoin-box">
            <img src="https://cryptologos.cc/logos/binance-coin-bnb-logo.png" alt="Binance Coin Logo" class="crypto-logo">
            <div class="crypto-name">Binance</div>
            <div class="crypto-price" id="binancecoin-price">Loading...</div>
        </div>
    </div>
</section>

<section class="about">
    <div class="about-content">
        <h2>About CryptoPredictions</h2>
        <p>CryptoPredictions is a leading platform for accurate cryptocurrency price predictions. Our team
            of experts analyzes market trends, historical data, and technical indicators to provide reliable
            predictions for various cryptocurrencies.</p>
        <a href="#" class="btn">Learn More</a>
    </div>
</section>

<footer>
    <p>&copy; 2023 CryptoPredictions. All rights reserved.</p>
</footer>

<script>
    function fetchCryptoPrices() {
        $.ajax({
            url: "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,binancecoin&vs_currencies=usd",
            method: "GET",
            success: function(data) {
                $('#bitcoin-price').text(`$${data.bitcoin.usd}`);
                $('#ethereum-price').text(`$${data.ethereum.usd}`);
                $('#binancecoin-price').text(`$${data.binancecoin.usd}`);
            },
            error: function(error) {
                console.log("Error fetching crypto prices:", error);
            }
        });
    }
    $(document).ready(function() {
        fetchCryptoPrices(); // Fetch prices when page loads
        setInterval(fetchCryptoPrices, 5000); // Refresh every 60 seconds
    });
    // Functions for showing/hiding login popup
    function openLoginPopup() {
        document.getElementById("loginPopup").style.display = "block";
        document.getElementById("overlay").style.display = "block";
    }

    function closeLoginPopup() {
        document.getElementById("loginPopup").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }
</script>
</script>
</body>
</div>
</body>

</html>