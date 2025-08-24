<?php
// Include database connection settings
$host = 'localhost'; // Change if necessary
$dbname = 'employeedb'; // Replace with your actual database name
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password

// Create a new MySQLi instance
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the request method is POST and if the course ID is provided
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['course_id'])) {
    $courseId = (int)$data['course_id'];

    // SQL query to delete the course
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();

    // Check if the deletion was successful
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete the course.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No course ID provided.']);
}

// Close the connection
$mysqli->close();
?>
