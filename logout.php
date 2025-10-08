<?php
session_start();

// If no session exists, redirect to student login
if (!isset($_SESSION['role'])) {
    header("Location: student.php");
    exit();
}else{
    header("Location: staff.php");
}

// Save role before destroying session
$role = $_SESSION['role'];

// Destroy session
session_unset();
session_destroy();

// Redirect based on role
if ($role === "student") {
    header("Location: student.php");
    exit();
} elseif ($role === "staff") {
    header("Location: staff.php");
    exit();
} else {
    // fallback
    header("Location: student.php");
    exit();
}
?>
