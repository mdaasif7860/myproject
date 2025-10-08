<?php
// ---------------- CONFIGURATION ----------------
include "db.php";
session_start(); // Start session to store temporary messages

// ------------------- Add Staff -------------------
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_staff'])) {
    $regno    = trim($_POST['regno'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($regno) && !empty($name) && !empty($password)) {
        // Check duplicate before insert
        $check = $conn->prepare("SELECT regno FROM staff WHERE regno=?");
        $check->bind_param("s", $regno);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "<div class='alert alert-warning text-center'>⚠️ Duplicate Reg No: <b>" . htmlspecialchars($regno) . "</b></div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO staff (regno, name, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $regno, $name, $password);
            if ($stmt->execute()) {
                $_SESSION['message'] = "<div class='alert alert-success text-center'>✅ Staff added successfully!</div>";
            } else {
                $_SESSION['message'] = "<div class='alert alert-danger text-center'>❌ Error adding staff. Please try again.</div>";
            }
        }
    } else {
        $_SESSION['message'] = "<div class='alert alert-warning text-center'>⚠️ Please fill all fields.</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ------------------- Delete Staff -------------------
if (isset($_GET['delete'])) {
    $regno = $_GET['delete'];

    // Create backup table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS staff_backup LIKE staff");

    // Backup before delete
    $backup = $conn->prepare("INSERT IGNORE INTO staff_backup SELECT * FROM staff WHERE regno=?");
    $backup->bind_param("s", $regno);
    $backup->execute();

    // Delete staff
    $stmt = $conn->prepare("DELETE FROM staff WHERE regno=?");
    $stmt->bind_param("s", $regno);
    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success text-center'>🗑 Staff deleted successfully!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger text-center'>❌ Error deleting staff.</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ------------------- Rollback Staff -------------------
if (isset($_GET['rollback'])) {
    $backupExists = $conn->query("SHOW TABLES LIKE 'staff_backup'")->num_rows;
    if ($backupExists) {
        // Insert only those not already in staff
        $sql = "
            INSERT INTO staff (regno, name, password)
            SELECT b.regno, b.name, b.password 
            FROM staff_backup b
            WHERE NOT EXISTS (SELECT 1 FROM staff s WHERE s.regno = b.regno)
        ";
        $conn->query($sql);

        // Clear backup after rollback
        $conn->query("TRUNCATE TABLE staff_backup");

        $_SESSION['message'] = "<div class='alert alert-success text-center'>⏪ Rollback completed. Deleted staff restored!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-warning text-center'>❌ No backup found to rollback!</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ------------------- Fetch All Staff -------------------
$staffList = $conn->query("SELECT regno, name, password FROM staff ORDER BY regno ASC");

// Get and clear session message
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #2196f3, #1565c0);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            background: #fff;
            margin: 40px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 950px;
            animation: fadeIn 0.7s ease-in-out;
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
            color: #1565c0;
            font-weight: bold;
        }

        .top-buttons a,
        .top-buttons button {
            margin: 0 5px;
        }

        .table-responsive {
            border: 2px solid #1565c0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        table th {
            background-color: #1565c0;
            color: white;
            text-align: center;
        }

        table td,
        table th {
            padding: 12px;
            text-align: center;
        }

        .delete-btn {
            transition: 0.3s;
        }

        .delete-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>👨‍💼 Manage Staff</h2>

        <div class="mb-3 text-center top-buttons">
            <a href="http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=mohammed&table=staff" target="_blank" class="btn btn-info">🌐 Staff Table</a>
            <a href="admin_dashboard.php" class="btn btn-secondary">⬅ Dashboard</a>
            <a href="admin_logout.php" class="btn btn-warning">🚪 Logout</a>
            <a href="?rollback=1" class="btn btn-success">⏪ Rollback Deleted Staff</a>
        </div>
<br>
        <?php if (!empty($message)) echo $message; ?>

        <!-- Add Staff Form -->
        <form method="POST" autocomplete="off" id="staffForm" class="row g-2 mb-4">
            <div class="col-md-3"><input type="text" name="regno" placeholder="Register No" required class="form-control"></div>
            <div class="col-md-4"><input type="text" name="name" placeholder="Full Name" required class="form-control"></div>
            <div class="col-md-3"><input type="text" name="password" placeholder="Password" required class="form-control"></div>
            <div class="col-md-2"><button type="submit" name="add_staff" class="btn btn-primary w-100">➕ Add Staff</button></div>
        </form>
        <div class="mb-3 text-center top-buttons">
            <button type="button" class="btn btn-dark" onclick="showPasswordPrompt()">👁 Show Passwords</button>
        </div>
        <!-- Staff Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $staffList->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['regno']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td class="password-cell" data-pass="<?= htmlspecialchars($row['password']) ?>">----</td>
                            <td>
                                <a href="?delete=<?= urlencode($row['regno']) ?>" class="btn btn-danger btn-sm delete-btn" onclick="return confirm('Delete this staff?')">🗑 Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showPasswordPrompt() {
            const input = prompt("Enter password to view staff passwords:");
            if (input === "786") {
                const cells = document.querySelectorAll(".password-cell");
                cells.forEach(cell => {
                    cell.textContent = cell.getAttribute("data-pass");
                });
                alert("Passwords revealed ✅");
            } else {
                alert("Incorrect password ❌");
            }
        }

        window.onload = function() {
            document.getElementById("staffForm").reset();
        };
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>