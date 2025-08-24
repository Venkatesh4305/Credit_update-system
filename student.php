<?php
// Start session to access session variables
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";  // Your database username
$password = "";  // Your database password
$dbname = "employeedb";  // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the students table exists
$tableCheck = "SHOW TABLES LIKE 'students'";
$tableResult = $conn->query($tableCheck);

if ($tableResult->num_rows == 0) {
    die("Error: The 'students' table does not exist in the database.");
}

// Fetch all students
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

// Fetch logged-in user's details
$employee_id = $_SESSION['employee_id'];
$userSql = "SELECT emp_name, department, role FROM credentials WHERE employee_id = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
    $name = $user['emp_name'];
    $department = $user['department'];
    $role = $user['role'];
} else {
    header("Location: login.php");
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            background-color: #f9f9f9;
            min-height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
        }

        .sidebar {
            background-color: #1f2c3c;
            color: #fff;
            width: 240px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        .profile-section {
            text-align: center;
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .name {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .employee-id {
            margin: 5px 0;
            font-size: 14px;
            color: #7f8c8d;
        }

        .designation {
            font-size: 14px;
            line-height: 1.5;
        }

        .menu ul {
            list-style: none;
            padding: 0;
            margin: 20px 0 0 0;
            width: 100%;
        }

        .menu li {
            width: 100%;
        }

        .menu li a {
            display: block;
            text-decoration: none;
            color: #fff;
            padding: 15px 20px;
            transition: background 0.3s;
        }

        .menu li a:hover,
        .menu li.active a {
            background-color: #34495e;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #fff;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            text-align: right;
            font-size: 16px;
            color: #555;
        }

        .content {
            padding: 20px;
            flex: 1;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filters select,
        .filters .load-btn {
            height: 35px;
            width: 200px;
            padding: 5px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .load-btn {
            background-color: #4A90E2;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .load-btn:hover {
            background-color: #357ABD;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding: 10px;
            }

            .profile-section {
                display: none;
            }

            .filters {
                flex-direction: column;
            }

            .header {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <h2 class="name"><?php echo htmlspecialchars($name); ?></h2>
            <p class="employee-id">Employee - ID: <?php echo htmlspecialchars($employee_id); ?></p>
            <p class="designation">Department: <?php echo htmlspecialchars($department); ?></p>
            <p class="designation">Role: <?php echo htmlspecialchars($role); ?></p>
        </div>
        <nav class="menu">
            <ul>
                <li><a href="dash.php">Dashboard</a></li>
                <li><a href="cstruct.php">Course Structure</a></li>
                <li class="active"><a href="student.php">Students</a></li>
                <li><a href="cupdate.php">Credit Updates</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header class="header">
            Welcome, <?php echo htmlspecialchars($name); ?>
        </header>

        <section class="content">
            <div class="card">
                <h3>Students</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row['student_id']) . "</td>
                                        <td>" . htmlspecialchars($row['name']) . "</td>
                                        <td>" . htmlspecialchars($row['department']) . "</td>
                                        <td>" . htmlspecialchars($row['year']) . "</td>
                                        <td>" . htmlspecialchars($row['status']) . "</td>
                                        <td><a href='edit_student.php?id=" . $row['student_id'] . "'>Edit</a></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No students found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
