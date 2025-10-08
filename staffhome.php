<?php
session_start();
include "db.php";

// Protect the page: only allow logged-in staff
if (!isset($_SESSION['regno']) || empty($_SESSION['regno'])) {
    // Redirect to staff login page if not logged in
    header("Location: staff.php");
    exit();
}

$regno = $_SESSION['regno']; // Logged-in staff ID
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome v6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: url('student1.jpg') no-repeat center center/cover;
            color: white;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: rgba(247, 19, 87, 0.2);
            text-align: center;
            padding: 25px;
            font-size: 32px;
            font-weight: bold;
        }

        /* Navbar styling */
        nav a {
            color: white !important;
            font-weight: bold;
            padding: 8px 15px !important;
            margin: 0 5px;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        nav a:hover {
            background-color: rgba(253, 6, 6, 0.72);
            transform: scale(1.05);
        }

        .card {
            background: rgba(71, 202, 152, 0.77);
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            transition: 0.3s ease;
            cursor: pointer;
            height: 100%;
        }

        .card:hover {
            background: rgba(82, 57, 224, 0.96);
            transform: translateY(-15px);
        }

        .card i {
            font-size: 60px;
            margin-bottom: 15px;
            color: #fdcc0af8;
        }

        .card a {
            color: white;
            text-decoration: none;
            display: block;
            font-weight: bold;
            margin-top: 10px;
            font-size: 20px;
        }

        footer {
            text-align: center;
            padding: 18px;
            background-color: rgba(255, 255, 255, 0.1);
            font-size: 15px;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <header>
        Staff Home
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(23, 226, 50, 0.53);">
        <div class="container justify-content-center">
            <a class="nav-link" href="home.php"><i class="fa-solid fa-house"></i> Home Page</a>
            <a class="nav-link" href="staff_logout.php"><i class="fa-solid fa-user-tie"></i> LogOut</a>
        </div>
    </nav>

    <main class="flex-fill">
        <div class="container py-5">

            <!-- First row: Mark, Modify, View -->
            <div class="row g-4 justify-content-center">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <i class="fa-solid fa-circle-check"></i>
                        <a href="maratt.php">Mark Attendance</a>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <a href="modatt.php">Modify Attendance</a>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <i class="fa-solid fa-eye"></i>
                        <a href="viewatt.php">View Attendance</a>
                    </div>
                </div>
            </div>

                        

        </div>
    </main>

    <footer>
        &copy; 2025 Staff Dashboard
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
