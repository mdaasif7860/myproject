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
<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $a = $_POST['regno'];
    $b = $_POST['dob'];
    $c = $_POST['name'];
    $d = $_POST['dept'];
    $e = $_POST['email'];

    // Check for duplicate regno
    $check = mysqli_query($conn, "SELECT * FROM student WHERE regno = '$a'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "⚠️ RegNo '$a' already exists!";
    } else {
        $query1 = "INSERT INTO student(regno, name, dept, dob, email) VALUES('$a', '$c', '$d', '$b', '$e')";
        if (mysqli_query($conn, $query1)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } else {
            $msg = "❌ Insert failed: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Student</title>
    <style>
        body {
            background: url('addstu1.jpg') no-repeat top center/cover fixed;
            color: #4a148c;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #6a1b9a;
        }

        .home-container {
            text-align: center;
            margin: 20px 0;
        }

        .home-box a {
            display: inline-block;
            background: #28a745;
            color: white;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .home-box a:hover {
            background: #ff500aff;
        }

        form {
            background: #a0dbdb;
            padding: 20px;
            width: 400px;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type=text],
        input[type=date],
        input[type=email],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #aaa;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type=text]:focus,
        input[type=date]:focus,
        input[type=email]:focus,
        select:focus {
            border-color: #6a1b9a;
            box-shadow: 0 0 5px #9c27b0;
        }

        input[type=submit] {
            margin-top: 15px;
            background: #6a1b9a;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type=submit]:hover {
            background: #4a148c;
        }

        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 90%;
            background: #fff;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            animation: fadeIn 0.8s ease-in-out;
        }

        th,
        td {
            padding: 10px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #6a1b9a;
            color: white;
        }

        tr:hover {
            background-color: #f3e5f5;
        }

        .message {
            text-align: center;
            padding: 10px;
            font-weight: bold;
            border-radius: 8px;
            margin: 10px auto;
            width: fit-content;
            animation: fadeIn 0.8s ease-in-out;
        }

        .success {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .error {
            background: #ffcdd2;
            color: #c62828;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <h1>📋 Add Student</h1>

    <div class="home-container">
        <p class="home-box">
            <a href="staffhome.php">🏠 Staff Home</a>
        </p>
    </div>

    <?php
    if (isset($_GET['success'])) {
        echo "<div class='message success'>✅ Student added successfully.</div>";
    }
    if (isset($msg)) {
        echo "<div class='message error'>$msg</div>";
    }
    ?>

    <form method="post" action="">
        <label>Regno</label>
        <input type="text" name="regno" autocomplete="off" required>

        <label>Name</label>
        <input type="text" name="name" autocomplete="off" required>

        <label>Department</label>
        <select name="dept" required>
            <option value="">-- Select Department --</option>
            <option value="AI">AI</option>
            <option value="DS">DS</option>
        </select>

        <label>DOB</label>
        <input type="date" name="dob" required>

        <label>Email</label>
        <input type="email" name="email" autocomplete="off" required>

        <input type="submit" value="Submit">
    </form>

    <hr>

    <?php
    $result = mysqli_query($conn, "SELECT * FROM student");
    if (mysqli_num_rows($result) > 0) {
        echo "<table>
    <tr>
        <th>Reg No</th>
        <th>Name</th>
        <th>Department</th>
        <th>DOB</th>
        <th>Email</th>
    </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
            <td>{$row['regno']}</td>
            <td>{$row['name']}</td>
            <td>{$row['dept']}</td>
            <td>{$row['dob']}</td>
            <td>{$row['email']}</td>
        </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center; color:gray;'>No students found.</p>";
    }
    ?>

</body>

</html>