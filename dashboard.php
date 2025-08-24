<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }

    .navbar {
        background-color: #ffffff;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar h1 {
        font-size: 1.5rem;
        color: #333333;
        margin: 0;
    }

    .navbar a {
        text-decoration: none;
        color: #0078D7;
        font-size: 1rem;
    }

    .dashboard {
        padding: 2rem;
    }

    .cards {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .card {
        background-color: #ffffff;
        width: 300px;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: #333333;
    }

    .card p {
        font-size: 1rem;
        color: #666666;
        margin: 0;
    }

    .card .icon {
        font-size: 2rem;
        color: #6A5ACD;
        margin-bottom: 1rem;
        display: block;
    }

    .card:hover {
        background-color: #f0f0f0;
        transform: translateY(-5px);
    }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <a href="logout.php">Sign out</a>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard">
        <div class="cards">
            <!-- Faculty Management -->
            <a href="faculity.php" class="card">
                <div class="icon">👤</div>
                <h3>Faculty Management</h3>
                <p>Manage faculty credentials and information</p>
            </a>

            <!-- Class Schedule -->
            <a href="classshedule.php" class="card">
                <div class="icon">📅</div>
                <h3>Class Schedule</h3>
                <p>Assign and manage class schedules</p>
            </a>

            <!-- Meetings -->
            <a href="meetings.php" class="card">
                <div class="icon">📹</div>
                <h3>Meetings</h3>
                <p>Schedule and manage meetings</p>
            </a>
        </div>
    </div>
</body>

</html>