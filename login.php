<?php
// Start session to store user info upon login
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";  // Your database username
$password = "";  // Your database password
$dbname = "employeedb";  // Your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the employee_id and password are set
    if (isset($_POST['employee-id']) && isset($_POST['password'])) {
        $employee_id = $_POST['employee-id'];
        $pass = $_POST['password'];

        // Check if the fields are not empty
        if (empty($employee_id) || empty($pass)) {
            $error = "Please enter both Employee ID and Password!";
        } else {
            // Query the database for the user credentials using employee_id
            $sql = "SELECT * FROM credentials WHERE employee_id = ?";
            $stmt = $conn->prepare($sql);

            // Check if the statement was prepared successfully
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("s", $employee_id);  // Bind parameter for employee_id
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if a user is found with matching employee_id
            if ($result->num_rows > 0) {
                $userDetails = $result->fetch_assoc();

                // If password is stored in plain text (not recommended)
                if ($pass === $userDetails['password']) {
                    // Password is correct, create session and redirect to dashboard
                    $_SESSION['employee_id'] = $employee_id;  // Store employee_id in session
                    header("Location: dash.php");  // Redirect to dashboard
                    exit();  // Ensure the script stops executing here
                }
                // If password is hashed (recommended)
                elseif (password_verify($pass, $userDetails['password'])) {
                    // Password is correct
                    $_SESSION['employee_id'] = $employee_id;
                    header("Location: dash.php");  // Redirect to dashboard
                    exit();
                } else {
                    // Invalid password, show error message
                    $error = "Invalid Employee ID or Password!";
                }
            } else {
                // No user found with the given employee_id
                $error = "Invalid Employee ID or Password!";
            }

            $stmt->close();
        }
    } else {
        $error = "Please fill in all fields!";
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
    <title>Teacher Portal Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="icon-container">
                <img src="1.png" alt="School Icon">
            </div>
            <h2>Teacher Portal</h2>
            <form action="login.php" method="POST" class="login-form">
            <div class="form-group">
    <label for="employee-id">Employee ID</label>
    <input type="text" id="employee-id" name="employee-id" placeholder="Enter your Employee ID" required>
</div>

<div class="form-group">
    <label for="password">Password</label>
    <div class="password-container">
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <span class="eye-icon" onclick="togglePassword()">👁️</span>
    </div>
</div>

                <button type="submit" class="login-btn">Sign In</button>
                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot password?</a>
                </div>

                <!-- Admin Login Button Redirect -->
                <button type="button" class="admin-login-btn" onclick="window.location.href='adminlogin.php';">Admin Login</button>
            </form>
        </div>
    </div>
    <script src="login.js"></script>
    <script>
        // JavaScript to show error message as a popup
        <?php if ($error) { echo "alert('$error');"; } ?>

        // JavaScript to toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.querySelector('.eye-icon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.textContent = '🙈'; // Change to "eye closed" icon
            } else {
                passwordField.type = 'password';
                eyeIcon.textContent = '👁️'; // Change back to "eye open" icon
            }
        }
    </script>
</body>
</html>
