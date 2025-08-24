<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'employeedb'; // Database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is set in the query string
if (isset($_GET['id'])) {
    $courseId = $_GET['id'];

    // Fetch the course details from the database
    $stmt = $conn->prepare("SELECT code, title, L, T, P, C FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the course exists
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        echo "Course not found.";
        exit();
    }

    // Check if the form has been submitted to update the course
    if (isset($_POST['submit'])) {
        // Get the updated values from the form
        $code = $_POST['code'];
        $title = $_POST['title'];
        $L = $_POST['L'];
        $T = $_POST['T'];
        $P = $_POST['P'];
        $C = $_POST['C'];

        // Update the course in the database
        $updateStmt = $conn->prepare("UPDATE courses SET code = ?, title = ?, L = ?, T = ?, P = ?, C = ? WHERE id = ?");
        $updateStmt->bind_param("ssiiiii", $code, $title, $L, $T, $P, $C, $courseId);

        if ($updateStmt->execute()) {
            echo "<script>
                    alert('Course updated successfully.');
                    window.location.href = 'dash.php'; // Redirect to dashboard
                  </script>";
        } else {
            echo "<p>Error updating course: " . $conn->error . "</p>";
        }
    }

} else {
    echo "Invalid course ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f0f2f5;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        label {
            margin-bottom: 5px;
            display: block;
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #2563eb;
        }
    </style>

    <script>
        function confirmChanges() {
            // Check if any changes were made
            const code = document.getElementById('code').value;
            const originalCode = '<?php echo addslashes($course["code"]); ?>';

            // If the code has changed, show a confirmation dialog
            if (code !== originalCode) {
                return confirm('Are you sure you want to change the course code?');
            }
            return true; // Continue submission if no changes or if confirmed
        }
    </script>
</head>
<body>

<h2>Edit Course</h2>

<!-- Form to edit course details -->
<form method="POST" action="" onsubmit="return confirmChanges();">
    <label for="code">Course Code:</label>
    <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($course['code']); ?>" required>

    <label for="title">Course Title:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>

    <label for="L">L (Lecture Hours):</label>
    <input type="number" id="L" name="L" value="<?php echo htmlspecialchars($course['L']); ?>" required>

    <label for="T">T (Tutorial Hours):</label>
    <input type="number" id="T" name="T" value="<?php echo htmlspecialchars($course['T']); ?>" required>

    <label for="P">P (Practical Hours):</label>
    <input type="number" id="P" name="P" value="<?php echo htmlspecialchars($course['P']); ?>" required>

    <label for="C">C (Credits):</label>
    <input type="number" id="C" name="C" value="<?php echo htmlspecialchars($course['C']); ?>" required>

    <input type="submit" name="submit" value="Update Course">
</form>

</body>
</html>
