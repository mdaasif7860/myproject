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
$message = "";

// Handle delete on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $a = trim($_POST['regno']);

    $query1 = "DELETE FROM student WHERE regno = '$a'";
    $result1 = mysqli_query($conn, $query1);

    if ($result1 && mysqli_affected_rows($conn) > 0) {
        $message = "<p class='success'>✅ Student with Reg No <b>$a</b> deleted successfully.</p>";
    } else {
        $message = "<p class='error'>⚠️ No student found with Reg No <b>$a</b>.</p>";
    }
}

// Fetch student list
$query = "SELECT * FROM student";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url(delstu1.jpg) no-repeat top center/cover fixed;
            color: #2c3e50;
            margin: 0;
            padding: 20px;
        }

        h1,
        h2 {
            text-align: center;
            color: #08f793ff;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #ecde13ff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #e40926ff;
        }

        form {
            background: #a0dbdb;
            padding: 20px;
            border-radius: 8px;
            max-width: 350px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #32c7f5ff;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #2ecc71;
            outline: none;
        }

        input[type="submit"] {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #c0392b;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #106596ff;
            text-align: center;
        }

        th {
            background: #4527caff;
            color: white;
        }

        tr {
            background: #e5e9e9ff;
            color: #382b2bff;
        }

        tr:hover {
            background: #7d81b693;
        }

        /* ✅ Success & Error Messages */
        .success,
        .error {
            max-width: 500px;
            margin: 15px auto;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <h1>🗑 Delete Student</h1>
    <div style="text-align:center;">
        <a href="staffhome.php">🏠 Staff Home</a>
    </div>

    <?php echo $message; ?>

    <form method="post">
        <label for="regno">Reg No:</label>
        <input type="text" id="regno" name="regno" required autocomplete="off">
        <input type="submit" value="Delete">
    </form>

    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<h2 style='text-align:center;'>📋 Current Student Records</h2>";
        echo "<table>";
        echo "<tr><th>Reg No</th><th>Name</th><th>Department</th><th>DOB</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$row['regno']}</td>
                <td>{$row['name']}</td>
                <td>{$row['dept']}</td>
                <td>{$row['dob']}</td>
              </tr>";
        }

        echo "</table>";
    } else {
        echo "<p style='text-align:center;'>No student records found.</p>";
    }
    ?>

</body>

</html>