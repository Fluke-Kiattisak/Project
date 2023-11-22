<?php
session_start();

// Check if the form is submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection settings
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

    // Get data from POST request
    $user_id = $_POST['UserID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    // Add other fields as necessary

    // Prepare your SQL statement
    $sql = "UPDATE user SET username = ?, email = ? WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Settings updated successfully.";
        // Redirect or perform other actions
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Handle the error or redirect
    echo "Invalid request method.";
}
?>
