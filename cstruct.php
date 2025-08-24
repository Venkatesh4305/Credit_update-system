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

// Fetch user details from the database based on employee_id
$employee_id = $_SESSION['employee_id'];
$sql = "SELECT * FROM credentials WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
    $name = $userDetails['emp_name']; // Assuming 'emp_name' is the correct column
    $department = $userDetails['department']; // Assuming 'department' is correct
    $role = $userDetails['role']; // Assuming 'role' is the correct column name
} else {
    // If no user found, redirect to login
    header("Location: login.php");
    exit();
}

// Fetch the user's profile picture from the EmployeeImages table
$sql = "SELECT image_data FROM EmployeeImages WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$imageResult = $stmt->get_result();
$profilePicture = null;

if ($imageResult->num_rows > 0) {
    $imageRow = $imageResult->fetch_assoc();
    $profilePicture = base64_encode($imageRow['image_data']); // Convert binary image data to Base64
}

$stmt->close();

// Variables to hold selected year and semester
$year = isset($_POST['year']) ? $_POST['year'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';

// Fetch course data from the database based on year and semester
if ($year && $semester) {
    $sql_courses = "SELECT * FROM courses WHERE year = '$year' AND semester = '$semester'";
    $result_courses = $conn->query($sql_courses);
    
    if (!$result_courses) {
        die("Error executing query: " . $conn->error); // Check for query execution errors
    }
} else {
    $result_courses = null; // No results if year and semester are not set
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Structure</title>
    <link rel="stylesheet" href="dash.css"> <!-- Retaining existing dashboard styles -->
    <link rel="stylesheet" href="cstruct.css"> <!-- Add custom styling for course structure -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile">
                <!-- Dynamically load profile picture -->
                <img src="<?php echo $profilePicture ? 'data:image/jpeg;base64,' . $profilePicture : 'https://via.placeholder.com/100'; ?>" alt="Profile Picture">
                
                <!-- Display user details -->
                <h2><?php echo htmlspecialchars($name); ?></h2> <!-- User's Name -->
                <p class="emp-id">Employee - Id: <?php echo htmlspecialchars($employee_id); ?></p> <!-- Employee ID -->
                <p>Department: <?php echo htmlspecialchars($department); ?></p> <!-- Department -->
                <p>Role: <?php echo htmlspecialchars($role); ?></p> <!-- Role -->
            </div>
            
            <nav>
                <ul>
                    <li><a href="dash.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                    <li class="active"><a href="cstruct.php"><i class="fa-solid fa-book"></i> Course Structure</a></li>
                    <li><a href="cupdate.php"><i class="fa-solid fa-credit-card"></i> Credit Updates</a></li>
                    <!---<li><a href="student.php"><i class="fa-solid fa-user-graduate"></i> Students</a></li>-->
                    <li><a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
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
                <h3>Course Structure</h3>
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
                    <button type="button" id="download-btn" class="download-btn">
                        <i class="bi bi-download"></i> Download PDF
                    </button>
                </form>
                
                <table id="course-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>L</th>
                            <th>T</th>
                            <th>P</th>
                            <th>C</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$totalCredits = 0; // Initialize the total credits variable
$totalHours = 0;   // Initialize the total hours variable

if ($result_courses && $result_courses->num_rows > 0) {
    // Output data of each row
    while ($row = $result_courses->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['L']) . "</td>";
        echo "<td>" . htmlspecialchars($row['T']) . "</td>";
        echo "<td>" . htmlspecialchars($row['P']) . "</td>";
        echo "<td>" . htmlspecialchars($row['C']) . "</td>";
        echo "</tr>";

        // Accumulate the total credits
        $totalCredits += (int)$row['C'];
        
        // Accumulate the total hours (L + T + P)
        $totalHours += (int)$row['L'] + (int)$row['T'] + (int)$row['P'];
    }
} else {
    echo "<tr><td colspan='6'>No courses found for the selected year and semester.</td></tr>";
}
?>
<!-- Display the total credits and total hours as separate rows -->

<tr>
    <td colspan="5" style="text-align: right; font-weight: bold;">Total Hours:</td>
    <td style="font-weight: bold;"><?php echo $totalHours; ?></td>
</tr>

                        <!-- Display the total credits as a separate row -->
                        <tr>
                            <td colspan="5" style="text-align: right; font-weight: bold;">Total Credits:</td>
                            <td style="font-weight: bold;"><?php echo $totalCredits; ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <script>
        document.getElementById("download-btn").addEventListener("click", function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const table = document.getElementById("course-table");
            const headers = [];
            const rows = [];

            // Extract headers
            table.querySelectorAll("thead th").forEach((header) => {
                headers.push(header.textContent.trim());
            });

            // Extract rows
            table.querySelectorAll("tbody tr").forEach((row) => {
                const rowData = [];
                row.querySelectorAll("td").forEach((cell) => {
                    rowData.push(cell.textContent.trim());
                });
                rows.push(rowData);
            });

            // Add title and header
            doc.setFontSize(18);
            doc.text("Course Structure", 14, 20);
            doc.setFontSize(12);
            doc.text(`Year: <?php echo htmlspecialchars($year); ?>, Semester: <?php echo htmlspecialchars($semester); ?>`, 14, 30);

         // Add table to the PDF
doc.autoTable({
    head: [headers],  // Header row
    body: rows,       // Data rows
    startY: 40,       // Start Y position for the table
    theme: "striped",  // Apply striped theme for better readability
    styles: { fontSize: 10, cellPadding: 2 }, // Customize font size and cell padding
});

// Add the total credits and total hours
doc.text("Total Credits: " + <?php echo $totalCredits; ?>, 14, doc.autoTable.previous.finalY + 10);
doc.text("Total Hours: " + <?php echo $totalHours; ?>, 14, doc.autoTable.previous.finalY + 20);

// Generate and save the PDF
const fileName = `Course_Structure_Year<?php echo htmlspecialchars($year); ?>_Sem<?php echo htmlspecialchars($semester); ?>.pdf`;
doc.save(fileName);

        });
    </script>
</body>
</html>
