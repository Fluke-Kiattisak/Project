<?php
// Database connection details
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT coin_name, predicted_value, prediction_date FROM crypto_predictions";
$result = $conn->query($sql);

$predictions = array();

if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        array_push($predictions, $row);
    }
    echo json_encode($predictions);
} else {
    echo "0 results";
}
$conn->close();
?>
