<?php
// Start session to access session variables
session_start();

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";  
$password = "";  
$dbname = "employeedb";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$employee_id = $_SESSION['employee_id'];
$event_date = $_POST['event_date'];
$event_description = $_POST['event_description'];

// Insert the important date into the database
$sql = "INSERT INTO important_dates (employee_id, event_date, event_description) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $employee_id, $event_date, $event_description);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error inserting data.']);
}

$stmt->close();
$conn->close();
?>
    