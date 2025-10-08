<?php
session_start();
include "db.php";

// Protect the page: only allow logged-in students
if (!isset($_SESSION['regno']) || empty($_SESSION['regno'])) {
  // Redirect to login page if not logged in
  header("Location: student.php");
  exit();
}

$regno = $_SESSION['regno'];
?>

<!DOCTYPE html>
<html>

<head>
  <title>Student Dashboard</title>
  <style>
    body {
      background-color: #c2ad6769;
      background-size: cover;
      color: #222;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background: linear-gradient(90deg, #4CAF50, #2E8B57);
      color: white;
      padding: 18px;
      text-align: center;
      font-size: 26px;
      font-weight: bold;
      letter-spacing: 1px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
    }

    nav {
      background: #8a4c4cff;
      padding: 12px;
      display: flex;
      gap: 20px;
      justify-content: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    nav a {
      color: white;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 25px;
      background: linear-gradient(135deg, #00BFFF, #1E90FF);
      transition: all 0.3s ease-in-out;
      font-weight: bold;
    }

    nav a:hover {
      background: linear-gradient(135deg, #1E90FF, #0066CC);
      transform: scale(1.08);
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
    }

    main {
      max-width: 900px;
      margin: 30px auto;
      padding: 25px;
      background: rgba(226, 161, 161, 0.95);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      color: #2E8B57;
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
    }

    .info {
      background: linear-gradient(135deg, #71c7e9ff, #ebedee);
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 25px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .info:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
    }

    .info p {
      margin: 10px 0;
      color: #444;
      font-size: 16px;
    }

    .percentage-box {
      float: right;
      margin-left: 15px;
      padding: 15px 20px;
      background: linear-gradient(135deg, #f0f0f0, #d9fdd3);
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
      font-size: 16px;
      font-weight: bold;
      min-width: 150px;
    }

    .percentage {
      font-weight: bold;
      font-size: 20px;
      padding: 8px 14px;
      border-radius: 8px;
      display: inline-block;
      color: white;
    }

    .high {
      background-color: #28a745;
    }

    .low {
      background-color: #dc3545;
    }

    .days-red {
      color: #dc3545;
      font-weight: bold;
    }

    .days-orange {
      color: #ff8c00;
      font-weight: bold;
    }

    h3 {
      color: #333;
      margin-top: 30px;
      text-align: center;
      font-size: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 16px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    table th {
      background: linear-gradient(90deg, #4CAF50, #2E8B57);
      color: white;
      padding: 12px;
      text-align: center;
      font-size: 16px;
    }

    table td {
      background-color: #c277c2ff;
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
      transition: background 0.3s;
    }

    table tr:hover td {
      background-color: #af9644ff;
      cursor: pointer;
    }

    .status-present {
      color: #155724;
      font-weight: bold;
      background-color: #d4edda;
      border-radius: 5px;
      padding: 6px 10px;
      display: inline-block;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .status-absent {
      color: #f03b4dff;
      font-weight: bold;
      background-color: #f8d7da;
      border-radius: 5px;
      padding: 6px 10px;
      display: inline-block;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>

<body>

  <header>Student Dashboard</header>

  <nav>
    <a href="home.php">Home Page</a>
    <a href="logout.php">Logout</a>
  </nav>

  <main>
    <?php
    // Fetch student info
    $infoQuery = "SELECT * FROM student WHERE regno='$regno' LIMIT 1";
    $infoResult = mysqli_query($conn, $infoQuery);
    if ($row = mysqli_fetch_assoc($infoResult)) {
      echo "<h2>Welcome " . htmlspecialchars($row['name']) . "</h2>";
      echo "<div class='info'>
            <p><b>Reg No:</b> " . htmlspecialchars($row['regno']) . "</p>
            <p><b>Name:</b> " . htmlspecialchars($row['name']) . "</p>
            <p><b>Department:</b> " . htmlspecialchars($row['dept']) . "</p>
            <p><b>DOB:</b> " . date("d-m-Y", strtotime($row['dob'])) . "</p>
            <p><b>Email:</b> " . htmlspecialchars($row['email']) . "</p>
          </div>";
    }

    // Attendance stats
    $attQuery = "SELECT * FROM stat WHERE regno='$regno' ORDER BY dt DESC";
    $attResult = mysqli_query($conn, $attQuery);

    $totalClasses = mysqli_num_rows($attResult);
    $presentCount = 0;
    while ($row = mysqli_fetch_assoc($attResult)) {
      if (strtolower($row['status']) == 'present') $presentCount++;
    }

    $absentCount = $totalClasses - $presentCount;
    $percentage = ($totalClasses > 0) ? round(($presentCount / $totalClasses) * 100, 2) : 0;
    $percClass = ($percentage >= 75) ? "high" : "low";

    // Required days for 75%
    $neededDays = 0;
    if ($percentage < 75 && $totalClasses > 0) {
      $neededDays = ceil(((0.75 * $totalClasses) - $presentCount) / 0.25);
      $neededDays = max(0, $neededDays);
    }
    $daysClass = ($neededDays > 12) ? "days-red" : "days-orange";

    // Internal marks calculation
    $marks = 0;
    if ($percentage >= 75 && $percentage < 80) $marks = 1;
    elseif ($percentage >= 80 && $percentage < 85) $marks = 2;
    elseif ($percentage >= 85 && $percentage < 90) $marks = 3;
    elseif ($percentage >= 90 && $percentage < 95) $marks = 4;
    elseif ($percentage >= 95) $marks = 5;

    // Display stats
    echo "<div class='info'>
        <div class='percentage-box'>
            Attendance<br><span class='percentage $percClass'>$percentage%</span><br>
            Internal Marks: <b>$marks</b>
        </div>
        <p><b>Total Working Days:</b> $totalClasses</p>
        <p><b>Present Days:</b> $presentCount</p>
        <p><b>Absent Days:</b> $absentCount</p>";
    if ($percentage < 75) {
      echo "<p><b>Days required to reach 75%:</b> <span class='$daysClass'>$neededDays</span></p>";
    }
    echo "<div style='clear:both'></div></div>";

    // Attendance table
    echo "<h3>Attendance</h3><table><tr><th>Date</th><th>Status</th></tr>";
    $attResult = mysqli_query($conn, $attQuery);
    while ($row = mysqli_fetch_assoc($attResult)) {
      $date = date("d-m-Y", strtotime($row['dt']));
      $statusClass = strtolower($row['status']) == "present" ? "status-present" : "status-absent";
      echo "<tr><td>$date</td><td><span class='$statusClass'>" . $row['status'] . "</span></td></tr>";
    }
    echo "</table>";
    ?>
  </main>

</body>

</html>