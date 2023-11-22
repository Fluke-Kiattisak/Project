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
                        <a class="nav-link" href="Predictions.php"style="color: #000;">Predictions</a>
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

<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["currency"]) && $_POST["currency"] !== "") {
    // Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    // Establish a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize the input
    $selectedCurrency = $conn->real_escape_string($_POST["currency"]);

    // Map the input to table names
    $tables = [
        "ADA" => "ada_usd_future_predictions",
        "BTC" => "btc_usd_future_predictions",
        "ETH" => "eth_usd_future_predictions",
        "BNB" => "bnb_usd_future_predictions",
        
    ];

    // Validate the selected currency
    if (!isset($tables[$selectedCurrency])) {
        die("Invalid currency selected.");
    }

    // Use the selected currency to choose the correct table
    $tableName = $tables[$selectedCurrency];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM `$tableName`");

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Include the styles for the table
    echo '<style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            .container {
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 20px;
            }
            th, td {
                padding: 8px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #4CAF50;
                color: white;
            }
            tr:hover {background-color: #f5f5f5;}
            select {
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #ddd;
                margin-bottom: 20px;
            }
          </style>';

    // Add a heading for the selected currency
    echo "<h2>" . htmlspecialchars($selectedCurrency) . " Predicted Price</h2>";
    
    // Start the table within a container
    echo '<div class="container">';
    echo "<table>";
    echo "<tr><th>Date</th><th>Predicted</th><th>TradeSignal</th></tr>";

    // Check if the query returned any rows and output data of each row
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Date"] . "</td>";
            echo "<td>" . $row["Predicted"] . "</td>";
            echo "<td>" . $row["TradeSignal"] . "</td>";
            echo "</tr>";
        }
    } else {
        // If no results were returned
        echo '<p>No results found for ' . htmlspecialchars($selectedCurrency) . '.</p>';
    }

    // End the table
    echo "</table>";
    echo '</div>';

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
</div>
</div>
