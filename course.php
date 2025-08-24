<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 50px;
        }
        h1, h3 {
            text-align: center;
        }
        form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        label {
            font-weight: 500;
        }
        select, button {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .save-button {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .save-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Course Details</h1>

        <!-- Form to select year and semester -->
        <form method="POST" action="">
            <label for="year">Year:</label>
            <select id="year" name="year" required>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
                <option value="4">Year 4</option>
            </select>

            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>

            <button type="submit" name="submit">Submit</button>
        </form>

        <?php
        // Database configuration
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $dbname = 'employeedb'; // Database name

        // Create connection
        $conn = mysqli_connect($host, $user, $password, $dbname);

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Handle the update form submission
        if (isset($_POST['save'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);
            $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $L = filter_var($_POST['L'], FILTER_SANITIZE_NUMBER_INT);
            $T = filter_var($_POST['T'], FILTER_SANITIZE_NUMBER_INT);
            $P = filter_var($_POST['P'], FILTER_SANITIZE_NUMBER_INT);
            $C = filter_var($_POST['C'], FILTER_SANITIZE_NUMBER_INT);

            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("UPDATE courses SET code=?, title=?, L=?, T=?, P=?, C=? WHERE id=?");
            if ($stmt === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }

            $stmt->bind_param("ssiiiii", $code, $title, $L, $T, $P, $C, $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Course updated successfully!');</script>";
            } else {
                echo "<script>alert('No changes made or error updating course: " . htmlspecialchars($stmt->error) . "');</script>";
            }
            $stmt->close();
        }

        // Check if the form to display courses has been submitted
        if (isset($_POST['submit'])) {
            $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
            $semester = filter_var($_POST['semester'], FILTER_SANITIZE_NUMBER_INT);

            // Fetch courses based on year and semester
            $sql = "SELECT id, code, title, L, T, P, C FROM courses WHERE year=? AND semester=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $year, $semester);
            $stmt->execute();
            $result = $stmt->get_result();

            // Display the courses in a table
            if ($result->num_rows > 0) {
                echo "<h3>Courses for Year $year, Semester $semester:</h3>";
                echo "<form method='POST' action=''>"; // Start the form for updates
                echo "<table>";
                echo "<tr><th>Course Code</th><th>Course Title</th><th>L</th><th>T</th><th>P</th><th>C</th><th>Action</th></tr>";

                while ($course = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><input type='text' name='code' value='" . htmlspecialchars($course['code']) . "'></td>";
                    echo "<td><input type='text' name='title' value='" . htmlspecialchars($course['title']) . "'></td>";
                    echo "<td><input type='text' name='L' value='" . htmlspecialchars($course['L']) . "'></td>";
                    echo "<td><input type='text' name='T' value='" . htmlspecialchars($course['T']) . "'></td>";
                    echo "<td><input type='text' name='P' value='" . htmlspecialchars($course['P']) . "'></td>";
                    echo "<td><input type='text' name='C' value='" . htmlspecialchars($course['C']) . "'></td>";
                    echo "<td><button class='save-button' type='submit' name='save'>Save</button></td>";
                    echo "<input type='hidden' name='id' value='" . htmlspecialchars($course['id']) . "'>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "</form>";
            } else {
                echo "<p>No courses found for Year $year, Semester $semester.</p>";
            }
            $stmt->close();
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </div>
</body>
</html>
