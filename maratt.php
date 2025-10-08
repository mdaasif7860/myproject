<?php
session_start();
include "db.php";

// Protect the page
if (!isset($_SESSION['regno']) || empty($_SESSION['regno'])) {
  header("Location: staff.php");
  exit();
}

$regno = $_SESSION['regno'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mark Attendance</title>
  <style>
    body {
      background: url('maratt1.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: Arial, sans-serif;
      color: #333;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    h2,
    h3 {
      text-align: center;
      color: #444;
    }

    a {
      display: inline-block;
      text-decoration: none;
      color: white;
      background: #28a745;
      padding: 8px 15px;
      border-radius: 6px;
      margin-bottom: 15px;
      transition: 0.3s;
    }

    a:hover {
      background: #218838;
    }

    label {
      font-weight: bold;
    }

    select,
    input[type="date"],
    input[type="submit"] {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin: 5px 0;
    }

    input[type="submit"] {
      background: #007bff;
      color: white;
      cursor: pointer;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      background: #0056b3;
    }

    .student-list {
      margin-top: 20px;
    }

    .student-item {
      background: #f9f9f9;
      padding: 10px;
      margin: 5px 0;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .student-item:nth-child(even) {
      background: #eef2f3;
    }

    .success {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 6px;
      margin-top: 10px;
      text-align: center;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 6px;
      margin-top: 10px;
      text-align: center;
    }

    .check-all-box {
      margin-bottom: 15px;
      font-weight: bold;
      text-align: right; /* ✅ Move to right side */
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>📋 Mark Attendance</h2>
    <a href="staffhome.php">🏠 Staff Home</a>

    <?php
    include "db.php";

    $date = $_POST['dt'] ?? '';
    $dept = $_POST['dept'] ?? '';

    $display_date = $date ? date('d-m-Y', strtotime($date)) : '';

    if (isset($_POST['submit'])) {
      $present = $_POST['present'] ?? [];
      $names = $_POST['names'];

      $db_date = date('Y-m-d', strtotime(str_replace('-', '/', $date)));

      $check = $conn->prepare("SELECT COUNT(*) AS count FROM stat WHERE dept = ? AND dt = ?");
      $check->bind_param("ss", $dept, $db_date);
      $check->execute();
      $check_result = $check->get_result()->fetch_assoc();

      if ($check_result['count'] > 0) {
        echo "<div class='error'>❌ Attendance for <b>$dept</b> on <b>$display_date</b> is already marked.</div>";
      } else {
        $absentForms = "";

        foreach ($names as $regno => $name) {
          $status = in_array($regno, $present) ? 'Present' : 'Absent';
          $stmt = $conn->prepare("INSERT INTO stat (regno, name, dept, dt, status) VALUES (?, ?, ?, ?, ?)");
          $stmt->bind_param("sssss", $regno, $name, $dept, $db_date, $status);
          $stmt->execute();

          if ($status === "Absent") {
            $emailQuery = $conn->prepare("SELECT email FROM student WHERE regno = ?");
            $emailQuery->bind_param("s", $regno);
            $emailQuery->execute();
            $emailResult = $emailQuery->get_result()->fetch_assoc();
            $email = $emailResult['email'] ?? '';

            if (!empty($email)) {
              $absentForms .= "
              <form class='absent-form'>
                <input type='hidden' name='to_email' value='" . htmlspecialchars($email, ENT_QUOTES) . "'>
                <input type='hidden' name='student_name' value='" . htmlspecialchars($name, ENT_QUOTES) . "'>
                <input type='hidden' name='dept' value='" . htmlspecialchars($dept, ENT_QUOTES) . "'>
                <input type='hidden' name='date' value='" . htmlspecialchars($display_date, ENT_QUOTES) . "'>
                <input type='hidden' name='message' value='Dear $name, You were marked Absent on $display_date in $dept department.'>
              </form>";
            }
          }
        }

        echo "<div class='success'>✅ Attendance saved for <b>$dept</b> on <b>$display_date</b></div>";
        echo $absentForms;

        if (!empty($absentForms)) {
          echo "
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              let forms = document.querySelectorAll('.absent-form');
              let delay = 1000;
              forms.forEach((f, i) => {
                setTimeout(() => {
                  emailjs.sendForm(
                    'service_bbvivij',
                    'template_ememxnj',
                    f,
                    'LCKzls4QAPmi_m0qC'
                  ).then(function() {
                      console.log('✅ Email sent to absent student');
                  }, function(error) {
                      console.error('❌ FAILED...', error);
                  });
                }, i * delay);
              });
              alert('Emails sent to all absent students!');
            });
          </script>";
        }
      }
    }
    ?>

    <form method="post">
      <label>Select Department:</label>
      <select name="dept" required autocomplete="new-password">
        <option value="">Select</option>
        <option value="AI" <?= $dept == 'AI' ? 'selected' : '' ?>>AI</option>
        <option value="DS" <?= $dept == 'DS' ? 'selected' : '' ?>>DS</option>
      </select>

      <label>Select Date:</label>
      <input type="date" name="dt" value="<?= $date ?>" required>

      <input type="submit" name="load" value="Load Students">
    </form>

    <?php
    if ($dept && isset($_POST['load']) && $date) {
      $db_date = date('Y-m-d', strtotime(str_replace('-', '/', $date)));
      $display_date = date('d-m-Y', strtotime($date));

      $check = $conn->prepare("SELECT COUNT(*) AS count FROM stat WHERE dept = ? AND dt = ?");
      $check->bind_param("ss", $dept, $db_date);
      $check->execute();
      $check_result = $check->get_result()->fetch_assoc();

      if ($check_result['count'] > 0) {
        echo "<div class='error'>❌ Attendance for <b>$dept</b> on <b>$display_date</b> is already marked. You cannot mark again.</div>";
      } else {
        $stmt = $conn->prepare("SELECT * FROM student WHERE dept = ?");
        $stmt->bind_param("s", $dept);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<form method='post'>";
        echo "<input type='hidden' name='dept' value='$dept'>";
        echo "<input type='hidden' name='dt' value='$date'>";
        echo "<h3>Mark Attendance for $dept - $display_date</h3>";

        // ✅ Overall check/uncheck box moved right
        echo "<div class='check-all-box'><label><input type='checkbox' id='checkAll' checked> Select / Deselect All</label></div>";

        echo "<div class='student-list'>";

        while ($row = $result->fetch_assoc()) {
          echo "<div class='student-item'>";
          echo "<span>{$row['regno']} - {$row['name']}</span>";
          echo "<span><input type='checkbox' class='student-check' name='present[]' value='{$row['regno']}' checked> Present</span>";
          echo "<input type='hidden' name='names[{$row['regno']}]' value='{$row['name']}'>";
          echo "</div>";
        }

        echo "</div><br><input type='submit' name='submit' value='Submit Attendance'>";
        echo "</form>";
      }
    }
    ?>

  </div>

  <!-- ✅ EmailJS scripts -->
  <script src="https://cdn.emailjs.com/dist/email.min.js"></script>
  <script>
    (function() {
      emailjs.init("LCKzls4QAPmi_m0qC");
    })();

    // ✅ JS for Select/Deselect All
    document.addEventListener("DOMContentLoaded", function() {
      const checkAll = document.getElementById("checkAll");
      const studentChecks = document.querySelectorAll(".student-check");

      if (checkAll) {
        checkAll.addEventListener("change", function() {
          studentChecks.forEach(chk => chk.checked = checkAll.checked);
        });
      }
    });
  </script>
</body>
</html>
