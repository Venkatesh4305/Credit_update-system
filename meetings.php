<?php
$host = 'localhost';
$username = 'root';  // Replace with your database username
$password = '';      // Replace with your database password
$dbname = 'admin';   // Replace with your database name

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
        $id = $_POST['meetingId'];
        $meetingName = $_POST['meetingName'];
        $startTime = $_POST['meetingStartTime'];
        $endTime = $_POST['meetingEndTime'];

        if (empty($id)) {
            $sql = "INSERT INTO meetings (meeting_name, start_time, end_time) VALUES ('$meetingName', '$startTime', '$endTime')";
        } else {
            $sql = "UPDATE meetings SET meeting_name='$meetingName', start_time='$startTime', end_time='$endTime' WHERE id='$id'";
        }

        if ($conn->query($sql)) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    if ($action === 'delete') {
        $id = $_POST['deleteId'];
        $sql = "DELETE FROM meetings WHERE id='$id'";
        $conn->query($sql);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch meetings
$sql = "SELECT * FROM meetings";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meetings Schedule</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header h1 {
        font-size: 1.5rem;
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
    }

    .header button:hover,
    .header a:hover {
        background-color: #5a4bb8;
    }

    table {
        width: 100%;
        margin-top: 1.5rem;
        background: white;
        border-collapse: collapse;
    }

    table th,
    table td {
        text-align: left;
        padding: 1rem;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #f2f2f2;
    }

    .actions button {
        background-color: transparent;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
    }

    .actions .edit {
        color: #6A5ACD;
    }

    .actions .delete {
        color: #f44336;
    }

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
        border-radius: 8px;
        width: 400px;
    }

    .modal-content h3 {
        margin-top: 0;
    }

    .modal-content form input,
    .modal-content form select,
    .modal-content form button {
        width: 100%;
        padding: 0.8rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .modal-content form button {
        background-color: #6A5ACD;
        color: white;
        border: none;
        cursor: pointer;
    }

    .modal-content form button.cancel-button {
        background-color: #f44336;
    }
    </style>
</head>

<body>
    <div class="header">
        <h1>Meetings Schedule</h1>
        <div>
            <button onclick="openAddMeetingModal()">Add Meeting</button>
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Meeting Name</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['meeting_name']; ?></td>
                    <td><?php echo $row['start_time']; ?> - <?php echo $row['end_time']; ?></td>
                    <td class="actions">
                        <button class="edit" onclick="editMeeting(<?php echo htmlspecialchars(json_encode($row)); ?>)"
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

    <div class="modal" id="meetingModal">
        <div class="modal-content">
            <h3 id="modalTitle">Add Meeting</h3>
            <form method="POST">
                <input type="hidden" id="meetingId" name="meetingId">
                <input type="text" id="meetingName" name="meetingName" placeholder="Meeting Name" required>

                <!-- Start Time -->
                <label for="meetingStartTime">Start Time:</label>
                <input type="datetime-local" id="meetingStartTime" name="meetingStartTime" required>

                <!-- End Time -->
                <label for="meetingEndTime">End Time:</label>
                <input type="datetime-local" id="meetingEndTime" name="meetingEndTime" required>

                <button type="submit" name="action" value="save">Save</button>
                <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function editMeeting(meetingData) {
        // Fill modal with meeting data
        document.getElementById('meetingId').value = meetingData.id;
        document.getElementById('meetingName').value = meetingData.meeting_name;
        document.getElementById('meetingStartTime').value = meetingData.start_time;
        document.getElementById('meetingEndTime').value = meetingData.end_time;
        document.getElementById('modalTitle').innerText = 'Edit Meeting';
        document.getElementById('meetingModal').style.display = 'flex';
    }

    function openAddMeetingModal() {
        document.getElementById('meetingId').value = '';
        document.getElementById('meetingName').value = '';
        document.getElementById('meetingStartTime').value = '';
        document.getElementById('meetingEndTime').value = '';
        document.getElementById('modalTitle').innerText = 'Add Meeting';
        document.getElementById('meetingModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('meetingModal').style.display = 'none';
    }
    </script>
</body>

</html>