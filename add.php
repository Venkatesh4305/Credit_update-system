<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'employeedb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    // Retrieve form data
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $l = $_POST['l'];
    $t = $_POST['t'];
    $p = $_POST['p'];
    $c = $_POST['c'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    // Insert into database (Do not include 'id' as it will auto-increment)
    $sql = "INSERT INTO courses (code, title, L, T, P, C, year, semester) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiiiisi', $course_code, $course_name, $l, $t, $p, $c, $year, $semester);
    
    if ($stmt->execute()) {
        echo "<script>alert('Course saved successfully!');</script>";
    } else {
        echo "<script>alert('Error saving course: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch subjects from the database based on Year and Semester (default to 1, 1 if not provided)
$year = isset($_POST['year']) ? $_POST['year'] : 1; 
$semester = isset($_POST['semester']) ? $_POST['semester'] : 1;

$sql = "SELECT * FROM courses WHERE year = ? AND semester = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $year, $semester);
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection after fetching
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Form</title>
    <link rel="stylesheet" href="add.css">
    <style>
        /* Modal and Overlay Styling */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #courseBox {
    display: none; /* Hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #ffffff;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    width: 300px; /* Adjust the width */
    max-height: 80vh; /* Limit modal height to 80% of viewport height */
    overflow-y: auto; /* Enable vertical scrolling */
    z-index: 1000;
}


        #courseBox form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        #courseBox label {
            font-weight: bold;
            font-size: 14px;
        }

        #courseBox input, #courseBox select {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        #courseBox button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        #courseBox button[type="submit"] {
            background-color: #28a745;
            color: #ffffff;
        }

        #courseBox button[type="submit"]:hover {
            background-color: #218838;
        }

        #courseBox button[type="button"] {
            background-color: #dc3545;
            color: #ffffff;
        }

        #courseBox button[type="button"]:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="modal-overlay" id="modalOverlay"></div>
    <button onclick="openBox()">Add Course</button>

    <div id="courseBox">
        <form method="POST" oninput="validateCredits()">
            <label>Course Code: <input type="text" name="course_code" required></label>
            <label>Course Title: <input type="text" name="course_name" required></label>
            <label>L (0-4): 
                <input type="number" name="l" id="l" min="0" max="4" required>
            </label>
            <label>T (0, 2, 4): 
                <select name="t" id="t" required>
                    <option value="0">0</option>
                    <option value="2">2</option>
                    <option value="4">4</option>
                </select>
            </label>
            <label>P (0, 2, 4): 
                <select name="p" id="p" required>
                    <option value="0">0</option>
                    <option value="2">2</option>
                    <option value="4">4</option>
                </select>
            </label>
            <label>C: <input type="text" name="c" id="c" readonly></label>
            <label>Year (1-4): 
                <input type="number" name="year" id="year" min="1" max="4" required>
            </label>
            <label>Semester: 
                <select name="semester" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </label>
            <button type="submit" name="save_course">Save</button>
            <button type="button" onclick="closeBox()">Close</button>
        </form>
    </div>

    <script>
        // Open the modal
        function openBox() {
            document.getElementById('courseBox').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
        }

        // Close the modal
        function closeBox() {
            document.getElementById('courseBox').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        }

        function validateCredits() {
    let L = document.getElementById('l').value;
    let T = document.getElementById('t').value;
    let P = document.getElementById('p').value;
    let C = parseFloat(L) + parseFloat(T) / 2 + parseFloat(P) / 2;

    if (C > 4) {
        alert("Credit value cannot exceed 4. Resetting inputs.");
        document.getElementById('l').value = 0;
        document.getElementById('t').value = 0;
        document.getElementById('p').value = 0;
        document.getElementById('c').value = 0;
    } else {
        document.getElementById('c').value = C;
    }
}

    </script>
</body>
</html>

<?php
$conn->close();
?>
