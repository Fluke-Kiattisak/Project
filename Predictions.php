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
                        <a class="nav-link" href="Predictions.php" style="color: #000;">Predictions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Get-Start.php" >Current coin value</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php" >About</a>
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
    

        <div class="card mb-3 shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Coin Prediction</h2>
        <p class="card-text">Here are the latest predictions:</p>
        <div class="table-responsive">
            
        
        <form action="Prediction.php" method="post">
    <select name="currency" id="currencySelect" onchange="submitForm()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
        <option value="">Select a currency</option>
        <option value="ADA">ADA</option>
        <option value="BTC">BTC</option>
        <option value="ETH">ETH</option>
        <option value="BNB">BNB</option>
        <option value="USDT">USDT</option>
        <option value="GALA">GALA</option>
        <option value="MATIC">MATIC</option>
        <option value="OMG">OMG</option>
        <option value="FTM">FTM</option>
        <option value="ECT">ETC</option>
        <option value="BAND">BAND</option>
        <option value="SUSHI">SUSHI</option>
        <option value="NEO">NEO</option>
        <option value="LTC">LTC</option>
        <option value="CRO">CRO</option>
        <option value="SOL">SOL</option>
        <!-- Add other options here if necessary -->
    </select>
</form>

<script>
function submitForm() {
    var currencySelect = document.getElementById('currencySelect');
    if (currencySelect.value !== "") {
        currencySelect.form.submit();
    }
}
</script>

    </tbody>
</table>
</div>
</div>
</div>
                
                <p class="card-text">Charts</p>
            </div>
        </div>

        <footer class="text-center mt-5 py-4">
            <p>&copy; 2023 CryptoPredictions. All rights reserved.</p>
        </footer>
    </div>
</body>

<script>// Functions for showing/hiding login popup
    function openLoginPopup() {
        document.getElementById("loginPopup").style.display = "block";
        document.getElementById("overlay").style.display = "block";
    }

    function closeLoginPopup() {
        document.getElementById("loginPopup").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }

    
    // Fetch predictions and update the DOM
    fetch('/api/predictions')
    .then(response => response.json())
    .then(data => {
        const predictionsContainer = document.getElementById('predictionsContainer');
        data.forEach(prediction => {
            const predictionElement = document.createElement('div');
            predictionElement.innerHTML = `Prediction ID: ${prediction.PredictionID}, Predicted Price: ${prediction.PredictedPrice}, Actual Price: ${prediction.ActualPrice}`;
            predictionsContainer.appendChild(predictionElement);
        });
    })
    .catch(error => console.error('Error:', error));
</script>


</html>