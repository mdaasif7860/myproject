<?php
session_start();
include "db.php";

// Protect the page: only allow logged-in students
if (!isset($_SESSION['regno']) || empty($_SESSION['regno'])) {
  // Redirect to login page if not logged in
  header("Location: staff.php");
  exit();
}

$regno = $_SESSION['regno'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Modify Attendance</title>
    <style>
        body {
            background: url('modatt1.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
        }

        header {
            background: #f7b731;
            padding: 15px;
            text-align: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        a {
            display: inline-block;
            background: #20bf6b;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            margin-bottom: 15px;
        }

        a:hover {
            background: #26de81;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-right: 5px;
        }

        select,
        input[type="date"],
        input[type="submit"] {
            padding: 8px;
            margin: 5px 10px 5px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            background: #3867d6;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #4b7bec;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background: #f8c291;
        }

        tr:nth-child(even) {
            background: #fef5e7;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <header>Modify Attendance</header>

    <div class="container">
        <a href="staffhome.php">🏠 Staff Home</a>

        <?php
        include "db.php";

        $dept = $_POST['dept'] ?? '';
        $dt = $_POST['dt'] ?? '';

        // Convert for display
        $dt_display = $dt ? date("d-m-Y", strtotime($dt)) : '';

        // ✅ Step 1: Handle attendance update
        if (isset($_POST['update'])) {
            $present = $_POST['present'] ?? [];
            $names = $_POST['names'] ?? [];

            if (is_array($names)) {
                foreach ($names as $regno => $name) {
                    $status = in_array($regno, $present) ? 'Present' : 'Absent';

                    $stmt = $conn->prepare("UPDATE stat SET status=? WHERE regno=? AND dt=? AND dept=?");
                    $stmt->bind_param("ssss", $status, $regno, $dt, $dept);
                    $stmt->execute();
                }

                echo "<p class='success'>✅ Attendance updated for $dept on $dt_display</p><hr>";
            } else {
                echo "<p class='error'>❌ Error: Attendance data missing.</p>";
            }
        }
        ?>

        <!-- Step 2: Form for selecting department and date -->
        <form method="post">
            <label>Select Department:</label>
            <select name="dept" required>
                <option value="">--Select--</option>
                <option value="AI" <?= $dept == 'AI' ? 'selected' : '' ?>>AI</option>
                <option value="DS" <?= $dept == 'DS' ? 'selected' : '' ?>>DS</option>
            </select>

            <label>Select Date:</label>
            <input type="date" name="dt" value="<?= $dt ?>" required>

            <input type="submit" name="load" value="Load Attendance">
        </form>

        <?php
        // ✅ Step 3: Load existing attendance and display update form
        if (isset($_POST['load']) && $dept && $dt) {
            $stmt = $conn->prepare("SELECT * FROM stat WHERE dept=? AND dt=?");
            $stmt->bind_param("ss", $dept, $dt);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo "<p class='error'>❌ No attendance records found for $dept on $dt_display.</p>";
            } else {
                echo "<form method='post'>";
                echo "<input type='hidden' name='dept' value='$dept'>";
                echo "<input type='hidden' name='dt' value='$dt'>";
                echo "<h3>Modify Attendance for $dept - $dt_display</h3>";
                echo "<table>";
                echo "<tr><th>Reg No</th><th>Name</th><th>Status</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    $regno = htmlspecialchars($row['regno']);
                    $name = htmlspecialchars($row['name']);
                    $checked = ($row['status'] === 'Present') ? "checked" : "";

                    echo "<tr>";
                    echo "<td>$regno</td>";
                    echo "<td>$name</td>";
                    echo "<td><input type='checkbox' name='present[]' value='$regno' $checked> Present</td>";
                    echo "</tr>";
                    echo "<input type='hidden' name='names[$regno]' value='$name'>";
                }

                echo "</table><br>";
                echo "<input type='submit' name='update' value='Update Attendance'>";
                echo "</form>";
            }
        }
        ?>
    </div>

</body>

</html>