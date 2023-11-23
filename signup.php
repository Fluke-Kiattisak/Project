<?php
// Database configuration
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $email = $conn->real_escape_string($_POST['email']); 
    $name = $conn->real_escape_string($_POST['name']);
    $DateOfBirth = $conn->real_escape_string($_POST['DateOfBirth']);
    $ACCESS_KEY = $conn->real_escape_string($_POST['ACCESS_KEY']); 
    $SECRET_KEY = $conn->real_escape_string($_POST['SECRET_KEY']);
    $PASS_PHRASE = $conn->real_escape_string($_POST['PASS_PHRASE']);
    $UserIP = $conn->real_escape_string($_POST['UserIP']);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = "INSERT INTO user (username, password, email, name, DateofBirth, ACCESS_KEY, SECRET_KEY, PASS_PHRASE, UserIP) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $username, $hashed_password, $email, $name, $DateOfBirth, $ACCESS_KEY, $SECRET_KEY, $PASS_PHRASE, $UserIP);

    // Execute and check
    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>
                Signup successful. <a href='login.html' class='alert-link'>Login here</a>.
              </div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>
                Error: " . htmlspecialchars($stmt->error) . "
              </div>";
    }
    

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
