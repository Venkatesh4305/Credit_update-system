<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link rel="stylesheet" href="login.css">
    <style>
        /* Additional CSS for loading */
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Arial", sans-serif;
            margin: 0;
        }

        .loader {
            display: none;
            align-items: center;
            justify-content: center;
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 999;
        }

        .book-wrapper {
            width: 150px;
            height: fit-content;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: relative;
        }

        .book {
            width: 100%;
            height: auto;
            filter: drop-shadow(10px 10px 5px rgba(0, 0, 0, 0.137));
        }

        .book-page {
            width: 50%;
            height: auto;
            position: absolute;
            animation: paging 0.15s linear infinite;
            transform-origin: left;
        }

        @keyframes paging {
            0% { transform: rotateY(0deg) skewY(0deg); }
            50% { transform: rotateY(90deg) skewY(-20deg); }
            100% { transform: rotateY(180deg) skewY(0deg); }
        }
    </style>
</head>
<body>
    <div class="loader" id="loader">
        <div class="book-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 126 75" class="book">
                <rect stroke-width="5" stroke="#e05452" rx="7.5" height="70" width="121" y="2.5" x="2.5"></rect>
                <line stroke-width="5" stroke="#e05452" y2="75" x2="63.5" x1="63.5"></line>
                <path stroke-linecap="round" stroke-width="4" stroke="#c18949" d="M25 20H50"></path>
                <path stroke-linecap="round" stroke-width="4" stroke="#c18949" d="M101 20H76"></path>
                <path stroke-linecap="round" stroke-width="4" stroke="#c18949" d="M16 30L50 30"></path>
                <path stroke-linecap="round" stroke-width="4" stroke="#c18949" d="M110 30L76 30"></path>
            </svg>
        </div>
    </div>
    
    <div>
        <h2>Employee Login</h2>
        <form id="loginForm" action="" method="POST">
            <div>
                <input type="number" name="employee_id" placeholder="Employee ID" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            
            // Show the loader
            document.getElementById('loader').style.display = 'flex';

            // Simulate loading delay
            setTimeout(function() {
                // Redirect to another page (e.g., dashboard.php)
                window.location.href = 'dashboard.php';
            }, 3000); // 3 seconds
        });
    </script>
</body>
</html>
