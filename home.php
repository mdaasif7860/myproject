<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Selection</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Body with background */
        body {
            background: url('home1.jpg') no-repeat center center/cover;
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: Arial, sans-serif;
            animation: fadeIn 1.2s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Page title near the top */
        .page-title {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            padding: 12px 25px;
            border-radius: 10px;
            margin-top: 30px;
            margin-bottom: 40px;
        }

        /* Container for login cards */
        .login-container {
            flex: 1;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Card styling */
        .login-card {
            position: relative;
            width: 220px;
            height: 250px;
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
        }

        .login-card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        .login-card i {
            font-size: 50px;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .login-card h3 {
            margin: 10px 0 15px;
            font-size: 1.5rem;
            color: #333;
        }

        .login-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            border-radius: 6px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .login-card a:hover {
            background-color: #388E3C;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <!-- Page Title -->
    <div class="page-title">
        Student Attendance Management System
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <!-- Staff Login Card -->
        <div class="login-card">
            <i class="fas fa-user-tie"></i>
            <h3>Staff Login</h3>
            <a href="staff.php">Login</a>
        </div>

        <!-- Student Login Card -->
        <div class="login-card">
            <i class="fas fa-user-graduate"></i>
            <h3>Student Login</h3>
            <a href="student.php">Login</a>
        </div>

        <!-- Admin Login Card -->
        <div class="login-card">
            <i class="fas fa-user-shield"></i>
            <h3>Admin Login</h3>
            <a href="admin.php">Login</a>
        </div>
    </div>

</body>

</html>
