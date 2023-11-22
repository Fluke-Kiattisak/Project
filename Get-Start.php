<?php
session_start();
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
                        <a class="nav-link" href="Get-Start.php" style="color: #000;">Current coin value</a>
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
                                        Welcome <?php echo $_SESSION['username']; ?>
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


        <section>
            <h2 class="mb-4">Current Cryptocurrency Prices</h2>
            <div id="cryptoList"></div>
        </section>

        <footer class="text-center mt-5 py-4">
            <p>&copy; 2023 CryptoPredictions. All rights reserved.</p>
        </footer>
    </div>

    <script>

        // Script for login popup
        function openLoginPopup() {
            document.getElementById("loginPopup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }

        function closeLoginPopup() {
            document.getElementById("loginPopup").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }

        // Simulate a successful login
        function simulateLogin() {
            
            var username = "Fluke";
            document.getElementById("usernameDisplay").textContent = username;
            document.getElementById("loginBtn").style.display = "none"; // Hide login button
            document.getElementById("userGreeting").style.display = "inline"; // Show user greeting
            closeLoginPopup();
        }

        // event listener to the login form's submit button
        document.addEventListener('DOMContentLoaded', function() {
            var loginForm = document.querySelector("#loginPopup form");
            loginForm.onsubmit = function(event) {
                event.preventDefault(); // Prevent form from submitting normally
                simulateLogin(); // Simulate the login process
            };
        });
        let cryptoData = [];

        function getChangeColor(percentage) {
            return percentage < 0 ? 'text-danger' : 'text-success';
        }

        function renderCryptos(data) {
            const container = document.querySelector('#cryptoList');
            container.innerHTML = "";
            data.forEach(crypto => {
                container.innerHTML += `
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <img src="${crypto.image}" alt="${crypto.name} Logo" style="width: 40px; height: 40px;">
                                    <strong class="ml-2">${crypto.name}</strong>
                                </div>
                                <div>
                                    <span class="badge badge-primary">${crypto.current_price.toFixed(2)} USD</span>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>1hr: <span class="${getChangeColor(crypto.price_change_percentage_1h_in_currency)}">${crypto.price_change_percentage_1h_in_currency.toFixed(2)}%</span></span>
                                <span>24hr: <span class="${getChangeColor(crypto.price_change_percentage_24h)}">${crypto.price_change_percentage_24h.toFixed(2)}%</span></span>
                                <span>7day: <span class="${getChangeColor(crypto.price_change_percentage_7d_in_currency)}">${crypto.price_change_percentage_7d_in_currency.toFixed(2)}%</span></span>
                                <span>Market Cap: ${crypto.market_cap.toLocaleString()} USD</span>
                                <span>Volume: ${crypto.total_volume.toLocaleString()} USD</span>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        function fetchCryptoPrices() {
            $.ajax({
                url: "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&limit=100&sparkline=false&price_change_percentage=1h%2C24h%2C7d",
                method: "GET",
                success: function(data) {
                    cryptoData = data;
                    renderCryptos(data);
                },
                error: function(error) {
                    console.log("Error fetching crypto prices:", error);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetchCryptoPrices();
            setInterval(fetchCryptoPrices, 5000);

            const searchButton = document.querySelector('.btn-primary');
            searchButton.addEventListener('click', function() {
                const searchTerm = document.querySelector('.form-control').value.toLowerCase();
                const filteredData = cryptoData.filter(crypto => crypto.name.toLowerCase().includes(searchTerm));
                renderCryptos(filteredData);
            });
        });

        
    </script>
</body>

</html>
