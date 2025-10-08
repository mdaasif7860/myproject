<?php
// ---------------- CONFIGURATION ----------------
include "db.php";
session_start(); // For flash messages

$message = "";

// ------------------- Add Admin -------------------
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_admin'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $check = $conn->prepare("SELECT id FROM admin WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "<div class='alert alert-warning text-center'>⚠️ Username <b>" . htmlspecialchars($username) . "</b> already exists!</div>";
        } else {
            $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                $_SESSION['message'] = "<div class='alert alert-success text-center'>✅ Admin added successfully!</div>";
            } else {
                $_SESSION['message'] = "<div class='alert alert-danger text-center'>❌ Error adding admin. Try again.</div>";
            }
        }
    } else {
        $_SESSION['message'] = "<div class='alert alert-warning text-center'>⚠️ Please fill all fields.</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ------------------- Delete Admin -------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $conn->query("CREATE TABLE IF NOT EXISTS admin_backup LIKE admin");
    $conn->query("INSERT INTO admin_backup SELECT * FROM admin WHERE id='$id'");

    $stmt = $conn->prepare("DELETE FROM admin WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success text-center'>🗑 Admin deleted successfully!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger text-center'>❌ Error deleting admin.</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ------------------- Fetch All Admins -------------------
$adminList = $conn->query("SELECT id, username, password FROM admin ORDER BY id ASC");

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Admins</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #ff7e5f, #feb47b);
    font-family: 'Roboto', sans-serif;
    min-height: 100vh;
    padding: 20px 0;
}

.container {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
    padding: 35px;
    max-width: 1000px;
    animation: fadeIn 0.7s ease-in-out;
}

@keyframes fadeIn { from {opacity:0; transform: translateY(20px);} to {opacity:1; transform: translateY(0);} }

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #ff7e5f;
    font-weight: bold;
}

.top-buttons {
    text-align:center;
    margin-bottom:25px;
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:12px;
}

.top-buttons button, .top-buttons a {
    display:inline-block;
    padding:10px 18px;
    border-radius:8px;
    font-weight:bold;
    text-decoration:none;
    color:white;
    transition:0.3s, transform 0.3s;
    border:none;
}

.top-buttons .admin-table { background:#ff9800; }
.top-buttons .dashboard { background:#4caf50; }
.top-buttons .logout { background:#f44336; }
.top-buttons .view-pass { background:#2196f3; }

.top-buttons button:hover, .top-buttons a:hover {
    filter: brightness(1.2);
    transform: scale(1.05);
}

.message { margin-bottom: 20px; }

form {
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    justify-content:center;
    margin-bottom:30px;
}

form input {
    padding:12px;
    border-radius:8px;
    border:1px solid #ccc;
    flex:1;
    min-width:180px;
    transition:0.3s;
}

form input:focus {
    border-color:#ff7e5f;
    outline:none;
    box-shadow:0 0 8px rgba(255,126,95,0.5);
}

form button {
    background: #ff7e5f;
    color:white;
    border:none;
    border-radius:8px;
    padding:12px 20px;
    font-weight:500;
    cursor:pointer;
    transition:0.3s, transform 0.3s;
}

form button:hover { background:#feb47b; transform: scale(1.05); }

table {
    width:100%;
    border-collapse: collapse;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    text-align:center;
}

th, td { padding:12px; border-bottom:1px solid #ddd; }

th { background:#ff7e5f; color:white; }

tr:hover { background:#fdf1e6; transition:0.3s; }

.delete-btn {
    background:#e53935;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    transition:0.3s, transform 0.3s;
}

.delete-btn:hover { background:#b71c1c; transform:scale(1.05); }

.password-cell { font-weight:bold; }

@media (max-width:768px){
    .top-buttons { flex-direction:column; }
    form { flex-direction:column; gap:10px; }
}
</style>
</head>
<body>

<div class="container">
    <h2>👨‍💻 Manage Admins</h2>

    <div class="top-buttons">
        <a href="http://localhost/phpmyadmin/index.php?route=/sql&pos=0&db=mohammed&table=admin" target="_blank" class="admin-table">🌐 Admin Table</a>
        <a href="admin_dashboard.php" class="dashboard">⬅ Dashboard</a>
        <a href="admin_logout.php" class="logout">🚪 Logout</a>
        <button class="view-pass" onclick="showPasswords()">👁 View Passwords</button>
    </div>

    <?php if(!empty($message)) echo $message; ?>

    <form method="POST" id="adminForm" autocomplete="off">
        <input type="text" name="username" placeholder="Username" required autocomplete="new-password">
        <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
        <button type="submit" name="add_admin">➕ Add Admin</button>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $adminList->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td class="password-cell" data-pass="<?= htmlspecialchars($row['password']) ?>">-----</td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this admin?')">🗑 Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
window.onload = function() { document.getElementById("adminForm").reset(); };

function showPasswords() {
    let pass = prompt("Enter password to view admin passwords:");
    if(pass === "786"){
        document.querySelectorAll(".password-cell").forEach(cell => {
            cell.textContent = cell.dataset.pass;
        });
        alert("Passwords revealed ✅");
    } else {
        alert("Incorrect password ❌");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
