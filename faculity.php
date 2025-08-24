<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';  // Change if needed
$password = '';      // Change if needed
$dbname = 'employeedb';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'save') {
        $id = $_POST['facultyId'];
        $empId = $_POST['facultyEmpId'];
        $name = $_POST['facultyName'];
        $department = $_POST['facultyDepartment'];
        $subject = $_POST['facultySubject'];
        $role = $_POST['facultyRole'];

        if (empty($id)) {
            $sql = "INSERT INTO credentials (employee_id, emp_name, department, sub_name, role) 
                    VALUES ('$empId', '$name', '$department', '$subject', '$role')";
        } else {
            $sql = "UPDATE credentials SET emp_name='$name', department='$department', sub_name='$subject', role='$role' 
                    WHERE employee_id='$id'";
        }

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Faculty details saved successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        exit;
    }

    if ($action === 'delete') {
        $deleteId = $_POST['delete_id'];
        $sql = "DELETE FROM credentials WHERE employee_id='$deleteId'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Faculty deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management</title>
    <link rel="stylesheet" href="faculity.css">
</head>

<body>
    <div class="navbar">
        <h1>Faculty Management</h1>
        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>

    <div class="container">
        <div class="header">
            <h2>Manage Faculty</h2>
            <button id="addFacultyBtn" onclick="showModal()">Add Faculty</button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Subject</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="facultyTable">
                <?php
                $sql = "SELECT * FROM credentials";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) : ?>
                <tr id="row-<?php echo $row['employee_id']; ?>">
                    <td><?php echo $row['employee_id']; ?></td>
                    <td><?php echo $row['emp_name']; ?></td>
                    <td><?php echo $row['department']; ?></td>
                    <td><?php echo $row['sub_name']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <button onclick='editFaculty(<?php echo json_encode($row); ?>)'>✏️</button>
                        <button onclick="deleteFaculty(<?php echo $row['employee_id']; ?>)">❌</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="modal" id="facultyModal">
        <div class="modal-content">
            <h3 id="modalTitle">Add Faculty</h3>
            <form id="facultyForm">
                <input type="hidden" id="facultyId" name="facultyId">
                <input type="text" id="facultyEmpId" name="facultyEmpId" placeholder="Employee ID" required>
                <input type="text" id="facultyName" name="facultyName" placeholder="Name" required>
                <input type="text" id="facultyDepartment" name="facultyDepartment" placeholder="Department" required>
                <input type="text" id="facultySubject" name="facultySubject" placeholder="Subject" required>
                <input type="text" id="facultyRole" name="facultyRole" placeholder="Role" required>
                <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
    function showModal() {
        document.getElementById('facultyModal').style.display = 'flex';
        document.getElementById('facultyForm').reset();
        document.getElementById('modalTitle').innerText = 'Add Faculty';
    }

    function editFaculty(faculty) {
        document.getElementById('facultyId').value = faculty.employee_id;
        document.getElementById('facultyEmpId').value = faculty.employee_id;
        document.getElementById('facultyName').value = faculty.emp_name;
        document.getElementById('facultyDepartment').value = faculty.department;
        document.getElementById('facultySubject').value = faculty.sub_name;
        document.getElementById('facultyRole').value = faculty.role;
        document.getElementById('modalTitle').innerText = 'Edit Faculty';
        document.getElementById('facultyModal').style.display = 'flex';
    }

    function deleteFaculty(id) {
        if (confirm("Are you sure?")) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&delete_id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`row-${id}`).remove();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            });
        }
    }

    document.getElementById('facultyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    });

    function closeModal() {
        document.getElementById('facultyModal').style.display = 'none';
    }
    </script>
</body>
</html>
