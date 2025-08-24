<?php
$host = 'localhost';
$username = 'root';  // Replace with your database username
$password = '';      // Replace with your database password
$dbname = 'employeedb';   // Replace with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action === 'save') {
        $id = $_POST['classId'];
        $className = $_POST['className'];
        $startTime = $_POST['classStartTime'];
        $endTime = $_POST['classEndTime'];
        $instructor = $_POST['classInstructor'];

        // Handle days for the class times
        $daysTimes = [];
        for ($i = 1; $i <= 6; $i++) {
            $daysTimes[$i] = $_POST["classDay$i"];
        }

        if (empty($id)) {
            // Insert new class schedule
            $sql = "INSERT INTO class_schedule (class_name, start_time, end_time, instructor) VALUES ('$className', '$startTime', '$endTime', '$instructor')";
            $conn->query($sql);
            $classId = $conn->insert_id;  // Get the ID of the last inserted class

            // Insert class times for each day
            foreach ($daysTimes as $day => $time) {
                $sql = "INSERT INTO class_schedule_days (class_id, day_of_week, class_time) VALUES ('$classId', '$day', '$time')";
                $conn->query($sql);
            }
        } else {
            // Update class schedule
            $sql = "UPDATE class_schedule SET class_name='$className', start_time='$startTime', end_time='$endTime', instructor='$instructor' WHERE id='$id'";
            $conn->query($sql);

            // Update class times for each day
            foreach ($daysTimes as $day => $time) {
                $sql = "UPDATE class_schedule_days SET class_time='$time' WHERE class_id='$id' AND day_of_week='$day'";
                $conn->query($sql);
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['deleteId'];
        $sql = "DELETE FROM class_schedule WHERE id='$id'";
        $conn->query($sql);
        // Delete associated class schedule days
        $sql = "DELETE FROM class_schedule_days WHERE class_id='$id'";
        $conn->query($sql);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch faculty list for the dropdown
$facultySql = "SELECT employee_id, emp_name FROM credentials";
$facultyResult = $conn->query($facultySql);

// Fetch existing class schedule data
$scheduleSql = "SELECT * FROM class_schedule";
$scheduleResult = $conn->query($scheduleSql);

// Fetch class schedule days (class times for each day)
$classDaysSql = "SELECT * FROM class_schedule_days";
$classDaysResult = $conn->query($classDaysSql);
$classDays = [];
while ($row = $classDaysResult->fetch_assoc()) {
    $classDays[$row['class_id']][$row['day_of_week']] = $row['class_time'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: bold;
            color: #333;
        }

        .header button,
        .header a {
            text-decoration: none;
            color: white;
            background-color: #6A5ACD;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .header button:hover,
        .header a:hover {
            background-color: #5a4bb8;
        }

        /* Table Styles */
        table {
            width: 100%;
            margin-top: 2rem;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            text-align: left;
            padding: 1.2rem;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f8f9fa;
            color: #333;
            font-size: 1.1rem;
            font-weight: bold;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            color: #555;
            font-size: 1rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            width: 500px;
            max-height: 70vh;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .modal-content h3 {
            margin-top: 0;
            color: #333;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .modal-content form input,
        .modal-content form select,
        .modal-content form button {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1.2rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .modal-content form input[type="datetime-local"] {
            padding: 0.8rem;
        }

        .modal-content form select {
            font-size: 1rem;
        }

        .modal-content form button {
            background-color: #6A5ACD;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-content form button:hover {
            background-color: #4a3c8c;
        }

        .modal-content form button.cancel-button {
            background-color: #f44336;
            color: white;
        }

        .modal-content form button.cancel-button:hover {
            background-color: #d32f2f;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            table th,
            table td {
                padding: 0.8rem;
            }

            .modal-content {
                width: 90%;
            }
        }

        /* Class Days Section Styles */
        .class-days {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .class-days .day {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .class-days label {
            font-size: 1rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .class-days input[type="time"] {
            padding: 0.8rem;
            width: 100%;
            max-width: 120px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Class Schedule</h1>
        <div>
            <button onclick="openAddClassModal()">Add Class</button>
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Time</th>
                    <th>Instructor</th>
                    <th>Class Days</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $scheduleResult->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['start_time']; ?> - <?php echo $row['end_time']; ?></td>
                    <td><?php echo $row['instructor']; ?></td>
                    <td>
                        <?php
                        // Display class times for each day
                        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($daysOfWeek as $index => $day) {
                            echo $day . ": " . ($classDays[$row['id']][$index + 1] ?? 'Not set') . "<br>";
                        }
                        ?>
                    </td>
                    <td class="actions">
                        <button class="edit" onclick="editClass(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                            title="Edit">&#9998;</button>
                        <form style="display:inline;" method="POST">
                            <input type="hidden" name="deleteId" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="delete" class="delete"
                                title="Delete">&#128465;</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal" id="classModal">
        <div class="modal-content">
            <h3 id="modalTitle">Add Class</h3>
            <form method="POST">
                <input type="hidden" id="classId" name="classId">
                <input type="text" id="className" name="className" placeholder="Class Name" required>

                <!-- Start Time -->
                <label for="classStartTime">Start Time:</label>
                <input type="datetime-local" id="classStartTime" name="classStartTime" required>

                <!-- End Time -->
                <label for="classEndTime">End Time:</label>
                <input type="datetime-local" id="classEndTime" name="classEndTime" required>

                <!-- Instructor Dropdown -->
                <label for="classInstructor">Instructor:</label>
                <select id="classInstructor" name="classInstructor" required>
                    <option value="">Select Instructor</option>
                    <?php while ($faculty = $facultyResult->fetch_assoc()) : ?>
                    <option value="<?php echo $faculty['employee_id']; ?>"><?php echo $faculty['emp_name']; ?></option>
                    <?php endwhile; ?>
                </select>

                <!-- Class Days -->
                <h4>Class Days</h4>
                <div class="class-days">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    foreach ($days as $index => $day) : ?>
                        <div class="day">
                            <label for="classDay<?php echo $index + 1; ?>"><?php echo $day; ?></label>
                            <input type="time" id="classDay<?php echo $index + 1; ?>" name="classDay<?php echo $index + 1; ?>" />
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" name="action" value="save">Save Class</button>
                <button type="button" class="cancel-button" onclick="closeClassModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openAddClassModal() {
            document.getElementById('classModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = 'Add Class';
            document.getElementById('classId').value = '';
            document.getElementById('className').value = '';
            document.getElementById('classStartTime').value = '';
            document.getElementById('classEndTime').value = '';
            document.getElementById('classInstructor').value = '';
            // Reset days' input values
            for (let i = 1; i <= 6; i++) {
                document.getElementById('classDay' + i).value = '';
            }
        }

        function closeClassModal() {
            document.getElementById('classModal').style.display = 'none';
        }

        function editClass(classData) {
            document.getElementById('classModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = 'Edit Class';
            document.getElementById('classId').value = classData.id;
            document.getElementById('className').value = classData.class_name;
            document.getElementById('classStartTime').value = classData.start_time;
            document.getElementById('classEndTime').value = classData.end_time;
            document.getElementById('classInstructor').value = classData.instructor;

            // Fill days' input values
            for (let i = 1; i <= 6; i++) {
                const dayTime = classData['class_day' + i];
                if (dayTime) {
                    document.getElementById('classDay' + i).value = dayTime;
                }
            }
        }
    </script>
</body>

</html>
