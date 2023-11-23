<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page or display an appropriate message
    exit('User not logged in.'); 
}

// Check if the username is set in the session
if (!isset($_SESSION['username'])) {
    exit('Username not set in the session.');
}

$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "project";

// Create database connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Error executing the query: " . $stmt->error);
}

if ($result->num_rows == 0) {
    die("No user found with the username: $username");
}

$userData = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Continue with the rest of your HTML/PHP for the settings page
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
                        <a class="nav-link" href="Get-Start.php" >Current coin value</a>
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
                    <!-- Dropdown for User Settings -->
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) : ?>
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
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.html">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="signup.html">Signup</a>
                        </li>
                    <?php endif; ?>

                    </li>
                </ul>
            </div>


        </nav>
        <div class="ml-auto">



        </div>


        </nav>
    <title>User Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Update Profile
        </div>
        <div class="card-body">
            <form action="update_settings.php" method="post">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>">

                <div class="form-group">
                    <label>Username: <span class="badge badge-secondary"><?php echo htmlspecialchars($userData['username']); ?></span></label>
                    <input type="text" class="form-control" name="new_username" ?>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" class="form-control" name="password" placeholder="New password">
                </div>
                <div class="form-group">
                    <label>Email: <span class="badge badge-secondary"><?php echo htmlspecialchars($userData['email']); ?></span></label>
                    <input type="email" class="form-control" name="email" ?>
                </div>
                <div class="form-group">
                    <label>ACCESS_KEY: <span class="badge badge-secondary"><?php echo htmlspecialchars($userData['ACCESS_KEY']); ?></span></label>
                    <input type="varchar" class="form-control" name="ACCESS_KEY" value="<?php echo htmlspecialchars($userData['ACCESS_KEY']); ?>">
                </div>
                <div class="form-group">
                    <label>SECRET_KEY: <span class="badge badge-secondary"><?php echo htmlspecialchars($userData['SECRET_KEY']); ?></span></label>
                    <input type="varchar" class="form-control" name="SECRET_KEY" value="<?php echo htmlspecialchars($userData['SECRET_KEY']); ?>">
                </div>
                <div class="form-group">
                    <label>PASS_PHRASE: <span class="badge badge-secondary"><?php echo htmlspecialchars($userData['PASS_PHRASE']); ?></span></label>
                    <input type="varchar" class="form-control" name="PASS_PHRASE" value="<?php echo htmlspecialchars($userData['PASS_PHRASE']); ?>">
                </div>
                <div class="form-group">
                    <label>IP Address: <span class="badge badge-secondary"><?php echo htmlspecialchars($userData['UserIP']); ?></span></label>
                    <input type="varchar" class="form-control" name="UserIP" value="<?php echo htmlspecialchars($userData['UserIP']); ?>">
                </div>

                <!-- Add other fields as necessary -->
                
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.9/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
