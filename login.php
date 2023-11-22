<?php
// Database configuration
$host = "localhost"; // or your database host
$dbUsername = "root"; // or your database username
$dbPassword = ""; // or your database password
$dbName = "project"; // your database name

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

    // SQL to check the existence of the user
    $sql = "SELECT password FROM user WHERE username = ?";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    // Execute
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetching the hashed password from the database
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verifying the password
        if (password_verify($password, $hashed_password)) {
            // Start session and set logged in
            session_start();
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;

            // Redirect to a logged-in page
            header("Location: Front-end.php"); // Replace 'welcome.php' with the page to redirect after login
            exit();
        } else {
            echo "Invalid username or password. <a href='login.html'>Try again</a>";
        }
    } else {
        echo "Invalid username or password. <a href='login.html'>Try again</a>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
