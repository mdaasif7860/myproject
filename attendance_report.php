<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Viewer</title>
  <style>
    body {
      background: url('staff1.jpg') no-repeat top center/cover;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      color: #333;
      animation: fadeIn 0.8s ease-in-out;
    }

    h1 {
      text-align: center;
      color: #2b7a0b;
    }

    a,
    .print-btn {
      text-decoration: none;
      background-color: #4CAF50;
      color: white;
      padding: 6px 12px;
      margin: 4px;
      border-radius: 5px;
      display: inline-block;
      font-weight: bold;
      transition: background-color 0.3s, transform 0.2s;
      border: none;
      cursor: pointer;
    }

    a:hover,
    .print-btn:hover {
      background-color: #388E3C;
      transform: scale(1.05);
    }

    form {
      background-color: white;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      margin: 20px auto;
      animation: slideDown 0.6s ease-in-out;
    }

    label {
      font-weight: bold;
    }

    select,
    input[type="date"],
    input[type="submit"] {
      width: 100%;
      padding: 8px;
      margin: 8px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
      border: none;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
      transform: scale(1.05);
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 10px;
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      animation: fadeInTable 0.8s ease-in-out;
    }

    th,
    td {
      text-align: left;
      padding: 12px;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    tr:hover {
      background-color: #dcedc1;
      transition: background-color 0.3s;
    }

    .print-container {
      text-align: left;
      margin-bottom: 10px;
    }

    .info-box {
      text-align: center;
      background: rgba(255, 255, 255, 0.9);
      padding: 10px;
      border-radius: 8px;
      margin: 10px auto;
      max-width: 500px;
      font-weight: bold;
    }

    .red-text {
      color: red;
      font-weight: bold;
    }

    .orange-text {
      color: orange;
      font-weight: bold;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes slideDown {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    @keyframes fadeInTable {
      from {
        opacity: 0;
        transform: scale(0.98);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    @media print {

      form,
      a,
      .print-btn,
      .info-box {
        display: none;
      }

      body {
        background: white;
        color: black;
      }
    }
  </style>
</head>

<body>

  <h1>Attendance Viewer</h1>

  <div style="text-align:center;">
    
  <a href="admin_dashboard.php">⬅Back To Dashboard</a>
<a href="admin_logout.php" style="background-color:#f44336;">Logout</a>  
</div>

  <form method="post">
    <label for="dept">Department:</label>
    <select id="dept" name="dept" required>
      <option value="">Select Department</option>
      <option value="ai">AI</option>
      <option value="ds">Data Science</option>
    </select>

    <label for="dt">Select Date:</label>
    <input type="date" id="dt" name="dt" required>

    <input type="submit" value="View Attendance">
  </form>

  <?php
  include "db.php"; // database connection

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $a = $_POST['dept'];
    $b = $_POST['dt'];
    $dbDate = $b;
  } else {
    $a = '';
    $dbDate = date('Y-m-d');
  }

  $query = "SELECT * FROM stat WHERE dept='$a' AND dt='$dbDate' ORDER BY regno";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    $formattedDate = date("d-m-Y", strtotime($dbDate));

    // Show Department and Date above table
    echo "<div class='info-box'>Department: <b>" . strtoupper($a) . "</b> | Date: <b>$formattedDate</b></div>";

    echo "<div class='print-container'><button class='print-btn' onclick='window.print()'>Print Attendance</button></div>";
    echo "<table>";
    echo "<tr>
            <th>Reg No</th>
            <th>Name</th>
            <th>Status</th>
            <th>Percentage</th>
            <th>Internal Mark</th>
            <th>Required Days to reach 75%</th>
          </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
      $reg = $row['regno'];
      $dept = $row['dept'];

      // Total classes & Present classes
      $totalQuery = "SELECT COUNT(*) as total FROM stat WHERE regno='$reg' AND dept='$dept'";
      $presentQuery = "SELECT COUNT(*) as present FROM stat WHERE regno='$reg' AND dept='$dept' AND status='Present'";

      $totalRes = mysqli_query($conn, $totalQuery);
      $presentRes = mysqli_query($conn, $presentQuery);

      $totalRow = mysqli_fetch_assoc($totalRes);
      $presentRow = mysqli_fetch_assoc($presentRes);

      $total = $totalRow['total'];
      $present = $presentRow['present'];

      $percentage = ($total > 0) ? round(($present / $total) * 100, 2) : 0;

      // Internal Marks
      if ($percentage >= 96) {
        $internal = 5;
      } elseif ($percentage >= 91) {
        $internal = 4;
      } elseif ($percentage >= 86) {
        $internal = 3;
      } elseif ($percentage >= 81) {
        $internal = 2;
      } elseif ($percentage >= 75) {
        $internal = 1;
      } else {
        $internal = 0;
      }

      // Calculate required days to reach 75%
      $requiredDays = 0;
      if ($percentage < 75) {
        while ((($present + $requiredDays) / ($total + $requiredDays)) * 100 < 75) {
          $requiredDays++;
        }
      }

      // Decide color
      if ($requiredDays > 12) {
        $daysText = "<span class='red-text'>$requiredDays</span>";
      } elseif ($requiredDays > 0) {
        $daysText = "<span class='orange-text'>$requiredDays</span>";
      } else {
        $daysText = "<span style='color:green;font-weight:bold;'>0</span>";
      }

      echo "<tr>";
      echo "<td>" . $row['regno'] . "</td>";
      echo "<td>" . $row['name'] . "</td>";
      echo "<td>" . $row['status'] . "</td>";
      echo "<td>" . $percentage . "%</td>";
      echo "<td>" . $internal . "</td>";
      echo "<td>" . $daysText . "</td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    $formattedDate = date("d-m-Y", strtotime($dbDate));
    echo "<p style='text-align:center;color:red;font-weight:bold;'>No attendance records found for <b>" . strtoupper($a) . "</b> on <b>$formattedDate</b>.</p>";
  }
  ?>

</body>

</html>
