<?php
// Start session
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "employeedb"; // Your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$message = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee-id'];

    // Check if the ID field is empty
    if (empty($employee_id)) {
        $error = "Please enter your Employee ID!";
    } else {
        // Retrieve the password for the given Employee ID
        $sql = "SELECT password FROM credentials WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the Employee ID exists
        if ($result->num_rows > 0) {
            $userDetails = $result->fetch_assoc();

            if (isset($_POST['new-password']) && !empty($_POST['new-password'])) {
                // If a new password is provided, update the password
                $new_password = $_POST['new-password'];

                // Update the password in the database
                $update_sql = "UPDATE credentials SET password = ? WHERE employee_id = ?";
                $update_stmt = $conn->prepare($update_sql);

                if ($update_stmt === false) {
                    die("Error preparing statement: " . $conn->error);
                }

                $update_stmt->bind_param("ss", $new_password, $employee_id);
                if ($update_stmt->execute()) {
                    $message = "Password updated successfully!";
                } else {
                    $error = "Error updating password: " . $conn->error;
                }

                $update_stmt->close();
            } else {
                // Show the current password
                $message = "Your current password is: " . htmlspecialchars($userDetails['password']);
            }
        } else {
            $error = "Employee ID not found!";
        }

        $stmt->close();
    }
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-box .icon-container img {
            width: 50px;
            margin-bottom: 10px;
        }
        .login-box h2 {
            margin: 20px 0;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        .forgot-password {
            margin-top: 15px;
            font-size: 14px;
        }
        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="icon-container">
                <img src="1.png" alt="School Icon">
            </div>
            <h2>Forgot Password</h2>
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="employee-id">Employee ID</label>
                    <input type="text" id="employee-id" name="employee-id" placeholder="Enter your Employee ID" required>
                </div>
                <div class="form-group">
                    <label for="new-password">New Password (Optional)</label>
                    <input type="password" id="new-password" name="new-password" placeholder="Enter a new password">
                </div>
                <button type="submit" class="btn">Submit</button>
                <div class="message"><?php if ($message) { echo $message; } ?></div>
                <div class="error"><?php if ($error) { echo $error; } ?></div>
            </form>
        </div>
    </div>
</body>
</html>
