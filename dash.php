<?php
session_start();

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employee_id = $_SESSION['employee_id'];

// Fetch user details
$sql = "SELECT * FROM credentials WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
    $name = htmlspecialchars($userDetails['emp_name']);
    $department = htmlspecialchars($userDetails['department']);
    $role = htmlspecialchars($userDetails['role']);
} else {
    header("Location: login.php");
    exit();
}
$stmt->close();

// Fetch profile picture
$sql = "SELECT image_data FROM EmployeeImages WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$imageResult = $stmt->get_result();
$profilePicture = null;

if ($imageResult->num_rows > 0) {
    $imageRow = $imageResult->fetch_assoc();
    $profilePicture = base64_encode($imageRow['image_data']);
}
$stmt->close();

// Fetch class schedule
$scheduleSql = "SELECT * FROM class_schedule WHERE instructor = ?";
$scheduleStmt = $conn->prepare($scheduleSql);
$scheduleStmt->bind_param("s", $employee_id); // Corrected to "s"
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();

$noScheduleMessage = ($scheduleResult->num_rows == 0) ? "No schedule found for this instructor." : "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <script src="dash.js" defer></script>
    <link rel="stylesheet" href="dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Class Schedule Section Styles */
        .card.full-width {
            width: 100%;
            margin: 20px 0;
        }

        .card-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-size: 1.2em;
        }

        .card-content {
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        td {
            font-size: 0.9em;
        }

        .no-schedule-message {
            font-size: 1.2em;
            color: #666;
            text-align: center;
        }

        /* Todo List Styles */
        .todo-item {
            padding: 10px;
            margin: 5px 0;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="profile">
            <img src="<?php echo $profilePicture ? 'data:image/jpeg;base64,' . $profilePicture : 'https://via.placeholder.com/100'; ?>" alt="Profile Picture">
            <h2><?php echo $name; ?></h2>
            <p class="emp-id">Employee - Id: <?php echo htmlspecialchars($employee_id); ?></p>
            <p>Department: <?php echo $department; ?></p>
            <p>Role: <?php echo $role; ?></p>
        </div>
        <nav>
            <ul>
                <li><a href="#"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                <li><a href="cstruct.php"><i class="fa-solid fa-book"></i> Course Structure</a></li>
                <li><a href="cupdate.php"><i class="fa-solid fa-credit-card"></i> Credit Updates</a></li>
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
                Welcome, <?php echo $name; ?>
            </div>
        </header>

        <section class="dashboard">
            <!-- Class Schedule Card -->
            <div class="card full-width">
                <div class="card-header">
                    <h3>Class Schedule</h3>
                </div>
                <div class="card-content">
                    <?php if ($noScheduleMessage): ?>
                        <p class="no-schedule-message"><?php echo $noScheduleMessage; ?></p>
                    <?php else: ?>
                        <table>
                            <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Instructor</th>
                                <th>Day</th>
                                <th>Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $scheduleResult->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['instructor']); ?></td>
                                    <td>
                                        <?php
                                        $days = [
                                            1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
                                            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'
                                        ];
                                        echo $days[(int)$row['day_of_week']] ?? 'Unknown';
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['start_time']) . ' - ' . htmlspecialchars($row['end_time']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Important Dates Card -->
            <div class="card full-width">
                <div class="card-header">
                    <h3>Important Dates</h3>
                    <button class="btn" id="addDateBtn">+ Add Date</button>
                </div>
                <!-- Hidden form for adding a new date -->
                <div id="dateForm" style="display:none; margin-top: 10px;">
                    <input type="text" id="dateTitle" placeholder="Enter title" required>
                    <input type="date" id="datePicker" required>
                    <button id="saveDateBtn">Save</button>
                </div>
                <!-- Added container for the dates -->
                <div id="importantDates"></div> <!-- This is where dates will be added -->
            </div>

            <!-- Todo List Card -->
            <div class="card full-width">
                <div class="card-header">
                    <h3>Todo List</h3>
                    <button class="btn" id="addTaskBtn">+ Add Task</button>
                </div>
                <div class="card-content">
                    <!-- Task Input Form -->
                    <div id="taskForm" style="display:none; margin-top: 10px;">
                        <input type="text" id="taskInput" placeholder="Enter a new task" required>
                        <button id="saveTaskBtn">Save</button>
                    </div>
                    <!-- Todo List Display -->
                    <div id="todoList"></div>
                </div>
            </div>

            <!-- Meetings Card -->
            <div class="card full-width">
                <div class="card-header">
                    <h3>Meetings</h3>
                </div>
                <div class="card-content">
                    <p>No meetings scheduled.</p>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    // Add Date functionality
    const addDateBtn = document.getElementById('addDateBtn');
    const dateForm = document.getElementById('dateForm');
    const saveDateBtn = document.getElementById('saveDateBtn');
    const importantDates = document.getElementById('importantDates');

    addDateBtn.addEventListener('click', function() {
        dateForm.style.display = 'block'; // Show the form
    });

    saveDateBtn.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission

        const title = document.getElementById('dateTitle').value.trim();
        const date = document.getElementById('datePicker').value;

        if (title && date) {
            const newDateEntry = document.createElement('p');
            newDateEntry.textContent = `${title} - ${date}`;
            importantDates.appendChild(newDateEntry);

            // Clear form
            document.getElementById('dateTitle').value = '';
            document.getElementById('datePicker').value = '';

            // Hide the form again
            dateForm.style.display = 'none';
        } else {
            alert('Please enter both title and date.');
        }
    });

    // Add Task functionality
    const addTaskBtn = document.getElementById('addTaskBtn');
    const taskForm = document.getElementById('taskForm');
    const saveTaskBtn = document.getElementById('saveTaskBtn');
    const taskInput = document.getElementById('taskInput');
    const todoList = document.getElementById('todoList');

    // Show task input form when "Add Task" button is clicked
    addTaskBtn.addEventListener('click', function() {
        taskForm.style.display = 'block'; // Show the form to input task
    });

    // Save task and add it to the Todo list
    saveTaskBtn.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent form submission

        const taskText = taskInput.value.trim();
        
        if (taskText) {
            const newTask = document.createElement('div');
            newTask.classList.add('todo-item');
            newTask.textContent = taskText;
            todoList.appendChild(newTask);

            // Clear input field and hide form again
            taskInput.value = '';
            taskForm.style.display = 'none';
        } else {
            alert('Please enter a task.');
        }
    });
</script>
</body>
</html>
