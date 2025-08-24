<?php

session_start();

// Connect to the database
$servername ='localhost';
$username ='root';
$password ='';
$dbname ='employeedb';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update password functionality
if (isset($_POST['update-password'])) {
    $emp_id = $_POST['emp-id'];
    $new_password = $_POST['new-password'];

    // Sanitize input
    $emp_id = mysqli_real_escape_string($conn, $emp_id);
    $new_password = mysqli_real_escape_string($conn, $new_password);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hashing the password

    // Update password query
    $sql = "UPDATE employees SET password='$hashed_password' WHERE emp_id='$emp_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Password updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating password: " . $conn->error . "');</script>";
    }
}

// Change password functionality
if (isset($_POST['confirm-change'])) {
    $emp_id = $_POST['emp-id-change'];

    // Sanitize input
    $emp_id = mysqli_real_escape_string($conn, $emp_id);

    // Check if Employee ID exists
    $sql = "SELECT * FROM employees WHERE emp_id='$emp_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate a new random password or use a new one from the user
        $new_password = "newRandomPassword"; // Here you can change this to get a password from the user if necessary
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hashing the password

        // Update password query
        $sql_update = "UPDATE employees SET password='$hashed_password' WHERE emp_id='$emp_id'";

        if ($conn->query($sql_update) === TRUE) {
            echo "<script>alert('Password changed successfully!');</script>";
        } else {
            echo "<script>alert('Error updating password: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Employee ID not found.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="" method="POST">
            <label for="emp-id">Enter Employee ID:</label>
            <input type="text" id="emp-id" name="emp-id" placeholder="Employee ID" required><br><br>

            <label for="new-password">Enter New Password:</label>
            <input type="password" id="new-password" name="new-password" placeholder="New Password" required><br><br>

            <button type="submit" name="update-password">Update Your Password</button>
            <button type="button" onclick="promptChangePassword()">Change Password</button>
        </form>
    </div>

    <!-- Confirmation Pop-up -->
    <div id="popup" class="popup hidden">
        <form action="" method="POST">
            <p>Enter Employee ID to Change Password:</p>
            <input type="text" name="emp-id-change" placeholder="Employee ID" required><br><br>
            <button type="submit" name="confirm-change">Yes, Change Password</button>
            <button type="button" onclick="closePopup()">No</button>
        </form>
    </div>

    <script>
        function promptChangePassword() {
            document.getElementById('popup').classList.remove('hidden');
        }

        function closePopup() {
            document.getElementById('popup').classList.add('hidden');
        }
    </script>
</body>
</html>
