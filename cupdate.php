<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection settings
$host = 'localhost'; // Change if necessary
$dbname = 'employeedb'; // Replace with your actual database name
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password;

// Create a new MySQLi instance
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$employee_id = $_SESSION['employee_id'];

// Fetch user details
$sql = "SELECT * FROM credentials WHERE employee_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
    $name = $userDetails['emp_name'];
    $department = $userDetails['department'];
    $role = $userDetails['role'];
} else {
    header("Location: login.php");
    exit();
}

$stmt->close();

// Fetch the user's profile picture
$sql = "SELECT image_data FROM EmployeeImages WHERE employee_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$imageResult = $stmt->get_result();
$profilePicture = null;

if ($imageResult->num_rows > 0) {
    $imageRow = $imageResult->fetch_assoc();
    $profilePicture = 'data:image/jpeg;base64,' . base64_encode($imageRow['image_data']);
}

$stmt->close();

// Handle save action
if (isset($_POST['save'])) {
    if (isset($_POST['course_data']) && is_array($_POST['course_data'])) {
        $errors = [];
        $totalCredits = 0;
        $changesMade = false;

        foreach ($_POST['course_data'] as $courseId => $course) {
            if (empty($course['id'])) {
                $errors[] = "Course ID is missing for a course with ID $courseId.";
                continue;
            }

            $courseId = $course['id'];
            $L = isset($course['L']) ? (int)$course['L'] : 0;
            $T = isset($course['T']) ? (int)$course['T'] : 0;
            $P = isset($course['P']) ? (int)$course['P'] : 0;

            // Calculate credits
            $C = calculateCredits($L, $T, $P);

            if ($C > 4) {
                $errors[] = "Credit value cannot exceed 4 for course with ID $courseId.";
                $L = $T = $P = 0;
                $C = 0;
            }

            $totalCredits += $C;

            if (is_numeric($courseId)) {
                // Check if values are different from the existing values
                $sql_check = "SELECT * FROM courses WHERE id = ?";
                $stmt_check = $mysqli->prepare($sql_check);
                $stmt_check->bind_param("i", $courseId);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                $stmt_check->close();

                if ($result_check->num_rows > 0) {
                    $existingCourse = $result_check->fetch_assoc();
                    if ($existingCourse['L'] != $L || $existingCourse['T'] != $T || $existingCourse['P'] != $P) {
                        // Update only if values changed
                        $sql_update = "UPDATE courses SET L = ?, T = ?, P = ?, C = ? WHERE id = ?";
                        $stmt_update = $mysqli->prepare($sql_update);
                        $stmt_update->bind_param("iiiii", $L, $T, $P, $C, $courseId);
                        $stmt_update->execute();

                        if ($stmt_update->affected_rows > 0) {
                            $changesMade = true;
                        }
                        $stmt_update->close();
                    }
                }
            } else {
                $errors[] = "Invalid course ID for course with ID $courseId.";
            }
        }

        if ($totalCredits < 22 || $totalCredits > 25) {
            $errors[] = "Total credits must be between 22 and 25. Current total credits: $totalCredits.";
        }

        if (!empty($errors)) {
            echo "<script>alert('" . implode("\\n", $errors) . "');</script>";
        } else {
            if (!$changesMade) {
                echo "<script>alert('No changes made to the courses.');</script>";
            } else {
                echo "<script>alert('All courses updated successfully! Total credits: $totalCredits');</script>";
            }
        }
    } else {
        echo "<script>alert('No course data provided.');</script>";
    }
}

// Fetch courses based on selected year and semester
$year = isset($_POST['year']) ? $_POST['year'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';

if (!empty($year) && !empty($semester)) {
    $sql_courses = "SELECT * FROM courses WHERE year = ? AND semester = ?";
    $stmt_courses = $mysqli->prepare($sql_courses);
    $stmt_courses->bind_param("ss", $year, $semester);
    $stmt_courses->execute();
    $result_courses = $stmt_courses->get_result();
    $stmt_courses->close();
} else {
    $result_courses = null;
}

// Function to calculate credits
function calculateCredits($L, $T, $P) {
    return $L + ($T / 2) + ($P / 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Update</title>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="cupdate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    /* Modal styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0, 0, 0); /* Black with opacity */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 5% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on your design */
        max-width: 600px;
        position: relative;
        overflow: auto; /* Add scroll bar if content overflows */
        max-height: 80vh; /* Set maximum height to avoid overflowing the viewport */
    }

        /* Close button styles */
        .close-btn {
            position: absolute;
            right: 25px;
            top: 10px;
            font-size: 20px;
            cursor: pointer;
            color: #aaa;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Styles for the iframe */
        #modal-iframe {
            width: 100%;
            height: 400px; /* Set a fixed height for the iframe */
            border: none; /* Remove border */
            overflow: auto; /* Add scroll bar to iframe content if necessary */
        }
</style>
</head>
<body>

    <div class="container">
        <aside class="sidebar">
            <div class="profile">
            <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" style="width:100px; height:100px; border-radius:50%;">


                <h2><?php echo htmlspecialchars($name); ?></h2>
                <p class="emp-id">Employee - Id: <?php echo htmlspecialchars($employee_id); ?></p>
                <p>Department: <?php echo htmlspecialchars($department); ?></p>
                <p>Role: <?php echo htmlspecialchars($role); ?></p>
            </div>
            <nav>
                <ul>
                    <li><a href="dash.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                    <li class="active"><a href="cstruct.php"><i class="fa-solid fa-book"></i> Course Structure</a></li>
                    <li><a href="cupdate.php"><i class="fa-solid fa-credit-card"></i> Credit Updates</a></li>
                  <!--  <li><a href="student.php"><i class="fa-solid fa-user-graduate"></i> Students</a></li>-->
                     <li><a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main>
            <header class="topbar">
                <div class="menu-icon">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="welcome-msg">
                    Welcome, <?php echo htmlspecialchars($name); ?>
                </div>
            </header>

            <section class="content">
                <h3>Course Update</h3>
                <form method="POST" class="filters">
                    <label for="year-select">Select Year:</label>
                    <select id="year-select" name="year" required>
                        <option value="" disabled selected>Select Year</option>
                        <option value="1" <?php echo ($year == '1') ? 'selected' : ''; ?>>Year 1</option>
                        <option value="2" <?php echo ($year == '2') ? 'selected' : ''; ?>>Year 2</option>
                        <option value="3" <?php echo ($year == '3') ? 'selected' : ''; ?>>Year 3</option>
                        <option value="4" <?php echo ($year == '4') ? 'selected' : ''; ?>>Year 4</option>
                    </select>

                    <label for="semester-select">Select Semester:</label>
                    <select id="semester-select" name="semester" required>
                        <option value="" disabled selected>Select Semester</option>
                        <option value="1" <?php echo ($semester == '1') ? 'selected' : ''; ?>>Semester 1</option>
                        <option value="2" <?php echo ($semester == '2') ? 'selected' : ''; ?>>Semester 2</option>
                    </select>

                    <button type="submit" class="load-btn">Load Structure</button>
                    <!-- Add Courses Button -->
<button type="button" class="add-btn" id="add-courses-btn">Add Courses</button>

<!-- Modal -->
<div id="add-courses-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="close-btn">&times;</span>
        <iframe id="modal-iframe" src="add.php" scrolling="yes"></iframe>
        </div>
</div>


                </form>

                <form method="POST">
                    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
                    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">

                    <button type="submit" name="save" class="save-btn">Save</button>

                    <table id="course-table">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>L</th>
                                <th>T</th>
                                <th>P</th>
                                <th>C</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalCredits = 0;
                            if ($result_courses && $result_courses->num_rows > 0) {
                                while ($row = $result_courses->fetch_assoc()) {
                                    echo "<tr data-course-id='" . $row['id'] . "'>";
                                    echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                    echo "<td>
                                            <input type='hidden' name='course_data[" . $row['id'] . "][id]' value='" . $row['id'] . "' />
                                            <select class='L' name='course_data[" . $row['id'] . "][L]' onchange='handleInputChange(event)'>
                                                <option value='0' " . ($row['L'] == 0 ? 'selected' : '') . ">0</option>
                                                <option value='1' " . ($row['L'] == 1 ? 'selected' : '') . ">1</option>
                                                <option value='2' " . ($row['L'] == 2 ? 'selected' : '') . ">2</option>
                                                <option value='3' " . ($row['L'] == 3 ? 'selected' : '') . ">3</option>
                                            </select>
                                        </td>";
                                    echo "<td>
                                            <select class='T' name='course_data[" . $row['id'] . "][T]' onchange='handleInputChange(event)'>
                                                <option value='0' " . ($row['T'] == 0 ? 'selected' : '') . ">0</option>
                                                <option value='2' " . ($row['T'] == 2 ? 'selected' : '') . ">2</option>
                                                <option value='4' " . ($row['T'] == 4 ? 'selected' : '') . ">4</option>
                                            </select>
                                        </td>";
                                    echo "<td>
                                            <select class='P' name='course_data[" . $row['id'] . "][P]' onchange='handleInputChange(event)'>
                                                <option value='0' " . ($row['P'] == 0 ? 'selected' : '') . ">0</option>
                                                <option value='2' " . ($row['P'] == 2 ? 'selected' : '') . ">2</option>
                                                <option value='4' " . ($row['P'] == 4 ? 'selected' : '') . ">4</option>
                                            </select>
                                        </td>";
                                    echo "<td><input type='number' class='C' value='" . htmlspecialchars($row['C']) . "' readonly></td>";
                                    echo "<td><button type='button' class='delete-btn' onclick='deleteCourse(" . $row['id'] . ")'>Delete</button></td>";
                                    echo "</tr>";
                                    $totalCredits += (int)$row['C'];
                                }
                            } else {
                                echo "<tr><td colspan='7'>No courses found</td></tr>";
                            }
                            ?>
                            <!-- Total Credits Row -->
                            <tr id="total-credits-row">
                                <td colspan="6"><b>Total Credits</b></td>
                                <td><span class="total-credits-value"><?php echo $totalCredits; ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                    
                </form>
            </section>
        </main>
    </div>

    <script>
        // JavaScript to handle credit calculations and UI changes
function handleInputChange(event) {
    const row = event.target.closest('tr');
    const L = parseInt(row.querySelector('.L').value) || 0;
    const T = parseInt(row.querySelector('.T').value) || 0;
    const P = parseInt(row.querySelector('.P').value) || 0;
    let C = calculateCredits(L, T, P);

    // If Credit (C) exceeds 4, reset values and show alert
    if (C > 4) {
        alert("Credit value cannot exceed 4.");
        row.querySelector('.L').value = 0;
        row.querySelector('.T').value = 0;
        row.querySelector('.P').value = 0;
        C = 0;
    }

    // Update the Credit (C) column
    row.querySelector('.C').value = C;

    // Update the total credits in the footer
    updateTotalCredits();
}

function calculateCredits(L, T, P) {
    return L + (T / 2) + (P / 2);
}

function updateTotalCredits() {
    let totalCredits = 0;
    document.querySelectorAll('.C').forEach(input => {
        totalCredits += parseFloat(input.value) || 0;
    });

    // Update the total credits displayed in the last row of the table
    document.querySelector('.total-credits-value').textContent = totalCredits;
}

// Add event listener to all select elements for credit calculations
document.querySelectorAll('.L, .T, .P').forEach(select => {
    select.addEventListener('change', handleInputChange);
});

        function calculateCredits(L, T, P) {
            return L + (T / 2) + (P / 2);
        }

        function updateTotalCredits() {
            let totalCredits = 0;
            document.querySelectorAll('.C').forEach(input => {
                totalCredits += parseFloat(input.value) || 0;
            });

            // Update the total credits displayed in the last row of the table
            document.querySelector('.total-credits-value').textContent = totalCredits.toFixed(2);
        }

        // Add event listener to all select elements for credit calculations
        document.querySelectorAll('.L, .T, .P').forEach(select => {
            select.addEventListener('change', handleInputChange);
        });

        function deleteCourse(courseId) {
            if (confirm("Are you sure you want to delete this course?")) {
                // Make an AJAX call to delete the course
                fetch('cdelete.php', {
                    method: 'POST',
                    body: JSON.stringify({ course_id: courseId }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Course deleted successfully!');
                        // Reload the page to reflect the change
                        location.reload();
                    } else {
                        alert('Failed to delete course');
                    }
                });
            }
        }
        // Get the modal and button
var modal = document.getElementById('add-courses-modal');
var btn = document.getElementById('add-courses-btn');
var span = document.getElementById('close-btn');

// When the user clicks the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

        
    </script>
</body>
</html>