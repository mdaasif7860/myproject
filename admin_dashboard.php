<?php
session_start();

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
$adminName = $_SESSION['admin'];

// Flash message example (optional)
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
/* Fonts & Resets */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
* { margin:0; padding:0; box-sizing:border-box; font-family:'Roboto', sans-serif; }

/* Body */
body {
    background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    color: #333;
    min-height: 100vh;
}

/* Navbar */
.navbar {
    background: linear-gradient(135deg, #2E7D32, #1B5E20);
    color: #fff;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    position: relative;
}
.navbar h1 { font-size: 28px; letter-spacing: 1px; }
.navbar .right { display: flex; align-items: center; gap: 15px; }
.navbar span { font-weight: 500; font-size: 16px; }
.navbar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 8px 15px;
    border-radius: 8px;
    transition: 0.3s;
}
.navbar a.home { background: #0288d1; }
.navbar a.home:hover { background: #01579b; }
.navbar a.logout { background: #d32f2f; }
.navbar a.logout:hover { background: #9a0007; }

/* Flash message */
.flash-message {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 10px;
    font-weight: bold;
    font-size: 14px;
}
.flash-message.success { color:#155724; background:#d4edda; padding:6px 12px; border-radius:8px; }
.flash-message.error { color:#721c24; background:#f8d7da; padding:6px 12px; border-radius:8px; }

/* Main container */
.container {
    padding: 40px 20px;
    max-width: 1200px;
    margin: auto;
}

/* Welcome text */
.welcome {
    font-size: 26px;
    margin-bottom: 35px;
    color: #2E7D32;
    font-weight: bold;
    text-align: center;
}

/* Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    padding: 30px 20px;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    min-height: 320px;
    color: #fff;
}

/* Card specific colors */
.card.students { background: linear-gradient(145deg, #ff9800, #fb8c00); }
.card.staff { background: linear-gradient(145deg, #4caf50, #43a047); }
.card.attendance { background: linear-gradient(145deg, #2196f3, #1e88e5); }
.card.settings { background: linear-gradient(145deg, #9c27b0, #8e24aa); }

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* Card text */
.card h3 { 
    margin-bottom: 15px; 
    font-size: 32px; 
    font-weight: 700;
}
.card p { 
    font-size: 18px; 
    margin-bottom: 25px; 
    flex-grow: 1; 
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    line-height: 1.4;
}

/* Card button */
.card a {
    display:inline-block;
    padding:12px 24px;
    background: rgba(0,0,0,0.2);
    color:white;
    font-weight:bold;
    text-decoration:none;
    border-radius:8px;
    transition: background 0.3s ease;
}
.card a:hover { background: rgba(0,0,0,0.35); }

/* Danger link */
.danger-link {
    display:inline-block;
    text-align:center;
    padding:12px 20px;
    background:#ff5722;
    color:white;
    font-weight:bold;
    border-radius:8px;
    text-decoration:none;
    transition:0.3s;
    margin-bottom: 30px;
}
.danger-link:hover { background:#e64a19; }

/* Responsive */
@media(max-width:768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }
    .navbar h1 { margin-bottom:10px; }
    .navbar .right { flex-wrap: wrap; gap:10px; }
}
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>Admin Dashboard</h1>
    <div class="right">
        <a href="home.php" class="home">🏠 Home</a>
        <span>Welcome, <?php echo htmlspecialchars($adminName); ?> 👋</span>
        <a href="admin_logout.php" class="logout">Logout</a>
    </div>
    <?php if(!empty($message)): ?>
        <div class="flash-message <?= strpos($message,'✅')!==false ? 'success' : 'error' ?>"><?= $message ?></div>
    <?php endif; ?>
</div>

<!-- Dashboard -->
<div class="container">
    <div class="welcome">✨ Choose an option to manage your system ✨</div>

    <a href="https://mdaasif7860.github.io/asif/matting.html" target="_blank" class="danger-link">🌐 Danger security</a>
    <a href="https://dashboard.emailjs.com/admin/templates" target="_blank" class="danger-link">🌐 emailchange</a>
    <a href="https://mail.google.com/mail/u/2/#sent" target="_blank" class="danger-link">🌐 emailsend</a>
    <a href="https://mail.google.com/mail/u/0/#inbox" target="_blank" class="danger-link">🌐 emailreceive</a>

    <div class="cards">
        <div class="card students">
            <h3>📘 Manage Students</h3>
            <p>Add, edit, or remove student records.</p>
            <a href="manage_student.php">Go</a>
        </div>
        <div class="card staff">
            <h3>👨‍🏫 Manage Staff</h3>
            <p>Update staff information & permissions.</p>
            <a href="manage_staff.php">Go</a>
        </div>
        <div class="card attendance">
            <h3>📊 Attendance Reports</h3>
            <p>View and download student attendance reports.</p>
            <a href="attendance_report.php">Go</a>
        </div>
        <div class="card settings">
            <h3>⚙️ Settings</h3>
            <p>Update admin account settings.</p>
            <a href="settings.php">Go</a>
        </div>
    </div>
</div>

</body>
</html>