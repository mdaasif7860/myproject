<?php
// ---------------- CONFIGURATION ----------------
include "db.php";

$message = ""; // To store success/error messages

// ------------------- Add Student -------------------
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_student'])) {
    $regno  = trim($_POST['regno'] ?? '');
    $name   = trim($_POST['name'] ?? '');
    $dept   = trim($_POST['dept'] ?? '');
    $dob    = trim($_POST['dob'] ?? '');
    $email  = trim($_POST['email'] ?? '');

    if (!empty($regno) && !empty($name) && !empty($dept) && !empty($dob) && !empty($email)) {
        $check = $conn->prepare("SELECT regno FROM student WHERE regno=?");
        $check->bind_param("s", $regno);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "<div class='error'>⚠️ Reg No <b>" . htmlspecialchars($regno) . "</b> already exists!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO student (regno, name, dept, dob, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $regno, $name, $dept, $dob, $email);
            if ($stmt->execute()) {
                header("Location: manage_student.php?success=1");
                exit;
            } else {
                $message = "<div class='error'>❌ Error adding student. Please try again.</div>";
            }
        }
    } else {
        $message = "<div class='error'>⚠️ Please fill all fields.</div>";
    }
}

// ------------------- Delete Student -------------------
if (isset($_GET['delete'])) {
    $regno = $_GET['delete'];

    // Backup before delete
    $conn->query("CREATE TABLE IF NOT EXISTS student_backup LIKE student");
    $conn->query("INSERT INTO student_backup SELECT * FROM student WHERE regno='$regno'");

    $stmt = $conn->prepare("DELETE FROM student WHERE regno=?");
    $stmt->bind_param("s", $regno);
    if ($stmt->execute()) {
        $message = "<div class='success'>🗑 Student deleted successfully!</div>";
    } else {
        $message = "<div class='error'>❌ Error deleting student.</div>";
    }
}

// ------------------- Rollback -------------------
if (isset($_GET['rollback'])) {
    $backupExists = $conn->query("SHOW TABLES LIKE 'student_backup'")->num_rows;
    if ($backupExists) {
        $conn->query("INSERT IGNORE INTO student SELECT * FROM student_backup");
        $conn->query("TRUNCATE TABLE student_backup");
        $message = "<div class='success'>⏪ Rollback completed. Deleted students restored!</div>";
    } else {
        $message = "<div class='error'>❌ No backup found to rollback!</div>";
    }
}

// ------------------- Fetch All Students -------------------
$studentList = $conn->query("SELECT regno, name, dept, dob, email FROM student ORDER BY dept ASC, regno ASC");

if (isset($_GET['success'])) {
    $message = "<div class='success'>✅ Student added successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 1150px;
            animation: fadeIn 0.6s ease-in-out;
            transition: transform 0.3s;
        }

        .container:hover {
            transform: translateY(-3px);
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
            text-align: center;
            margin-bottom: 25px;
            color: #2e7d32;
            font-weight: bold;
        }

        .message {
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
        }

        form input {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #2e7d32;
            font-size: 14px;
            transition: 0.3s;
        }

        form input:focus {
            border-color: #1b5e20;
            box-shadow: 0 0 8px rgba(46, 125, 50, 0.4);
        }

        /* Top buttons */
        .top-buttons a {
            padding: 10px 16px;
            margin: 0 6px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
            text-decoration: none;
        }

        .top-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .top-buttons .table-btn {
            background: #0288d1;
            color: #fff;
        }

        .top-buttons .table-btn:hover {
            background: #01579b;
        }

        .top-buttons .dashboard-btn {
            background: #8e24aa;
            color: #fff;
        }

        .top-buttons .dashboard-btn:hover {
            background: #6a1b9a;
        }

        .top-buttons .logout-btn {
            background: #e53935;
            color: #fff;
        }

        .top-buttons .logout-btn:hover {
            background: #b71c1c;
        }

        .top-buttons .rollback-btn {
            background: #ff9800;
            color: #fff;
        }

        .top-buttons .rollback-btn:hover {
            background: #ef6c00;
        }

        /* Table styling */
        .table-responsive {
            border: 2px solid #2e7d32;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        table th {
            background: linear-gradient(135deg, #43a047, #2e7d32);
            color: white;
            text-align: center;
            font-size: 15px;
            letter-spacing: 0.5px;
        }

        table td,
        table th {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #a5d6a7;
        }

        tr:nth-child(even) {
            background-color: #f1f8f1;
        }

        tr:hover {
            background: #c8e6c9;
            transition: 0.3s;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>🎓 Manage Students</h2>

        <div class="top-buttons">
            <a href="http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=mohammed&table=student" target="_blank" class="table-btn">🌐 Student Table</a>
            <a href="admin_dashboard.php" class="dashboard-btn">⬅ Dashboard</a>
            <a href="admin_logout.php" class="logout-btn">🚪 Logout</a>
            <a href="?rollback=1" class="rollback-btn">⏪ Rollback Deleted Students</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off" id="studentForm" class="row g-2">
            <div class="col-md-2"><input type="text" name="regno" placeholder="Register No" required class="form-control"></div>
            <div class="col-md-2"><input type="text" name="name" placeholder="Full Name" required class="form-control"></div>
            <div class="col-md-2"><input type="text" name="dept" placeholder="Department" required class="form-control"></div>
            <div class="col-md-2"><input type="date" name="dob" required class="form-control"></div>
            <div class="col-md-2"><input type="email" name="email" placeholder="Email" required class="form-control"></div>
            <div class="col-md-2"><button type="submit" name="add_student" class="btn btn-success w-100">➕ Add Student</button></div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Dept</th>
                        <th>DOB</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $studentList->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['regno']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['dept']) ?></td>
                            <td><?= htmlspecialchars($row['dob']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <a href="?delete=<?= urlencode($row['regno']) ?>"
                                    class="btn btn-danger btn-sm w-100"
                                    onclick="return confirm('Delete this student?')">
                                    🗑 Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.onload = function() {
            document.getElementById("studentForm").reset();
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>