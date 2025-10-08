<?php
session_start(); // ✅ Start session
include "db.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regno = $_POST['regno'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM staff WHERE regno = ? AND password = ?");
    $stmt->bind_param("ss", $regno, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // ✅ Save login info in session
        $_SESSION['regno'] = $regno;

        // Redirect to staff home page
        header("Location: staffhome.php");
        exit();
    } else {
        $message = "❌ Invalid regno or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    html,body{
      height: 100%;
    }
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: url('staff1.jpg') no-repeat top center/cover;
      min-height: 100vh;
      display:flex;
      align-items: center;
      justify-content: center;
      animation: fadeInBody 1s ease-in-out;
    }

    .login-card {
      background: linear-gradient(rgba(182, 174, 218, 0.97), rgba(12, 5, 2, 0.45)),
      url('login2.jpg') no-repeat center center/cover;
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(158, 102, 102, 0.3);
      text-align: center;
      animation: fadeInCard 1.5s ease-in-out;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      width: 100%;
      max-width: 480px;
      border: 2px solid mediumslateblue;
    }

    .login-card:hover {
      transform: scale(1.02);
      box-shadow: 0 16px 40px rgba(94, 63, 63, 0.4);
    }

    .login-card h2 {
      color:mintcream;
      margin-bottom: 25px;
      font-size: 1.9rem;
      font-weight: bold;
    }

    label {
      font-weight: bold;
      color: orange;
      text-align: left;
      display: block;
      font-size: 1rem;
    }

    .input-container {
      position: relative;
      width: 100%;
    }

    .input-container i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: mediumslateblue;
      font-size: 17px;
    }

    .input-container input {
      width: 100%;
      padding: 14px 10px 10px 35px;
      border: 2px solid mediumslateblue;
      border-radius: 10px;
      outline: none;
      font-size: 15px;
      transition: border-color 0.3s, box-shadow 0.3s;
      background-color: #fff;
    }

    .input-container input:focus {
      border-color: royalblue;
      box-shadow: 0 0 8px rgba(251, 250, 252, 0.3);
    }

    .password-container i.fa-eye,
    .password-container i.fa-eye-slash {
      right: 12px;
      left: auto;
      cursor: pointer;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      color: mediumslateblue;
      font-size: 17px;
    }

    input[type="submit"] {
      background: linear-gradient(90deg, mediumslateblue, royalblue);
      color: white;
      border: 2px solid mediumslateblue;
      padding: 14px;
      width: 100%;
      border-radius: 10px;
      cursor: pointer;
      font-size: 17px;
      margin-top: 18px;
      transition: transform 0.3s, background 0.3s;
    }

    input[type="submit"]:hover {
      background: linear-gradient(90deg, royalblue, mediumslateblue);
      transform: scale(1.04);
    }

    .link-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
      gap: 10px;
    }

    .link-buttons a {
      flex: 1;
      background: white;
      text-decoration: none;
      padding: 10px;
      border-radius: 8px;
      color: mediumslateblue;
      font-weight: bold;
      border: 2px solid mediumslateblue;
      transition: background 0.3s, transform 0.3s, color 0.3s;
      text-align: center;
    }

    .link-buttons a:hover {
      background:chartreuse;
      color: white;
      transform: scale(1.03);
    }

    @keyframes fadeInBody {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes fadeInCard {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .error-message {
      color: crimson;
      margin-top: 15px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h2>Staff Login</h2>
  <form method="post" action="">
    <div class="mb-3">
      <label>Reg No:</label>
      <div class="input-container">
        <i class="fa fa-user"></i>
        <input type="text" name="regno" autocomplete="off" placeholder="Enter your Reg No" required>
      </div>
    </div>
    <div class="mb-3">
      <label>Password:</label>
      <div class="input-container password-container">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" id="password" autocomplete="new-password" placeholder="Enter your Password" required>
        <i id="togglePassword" class="fa fa-eye"></i>
      </div>
    </div>
    <input type="submit" value="Login">
  </form>

  <div class="link-buttons">
    <a href="home.php">🏠 Home Page</a>
    <a href="student.php">🎓 Student Login</a>
  </div>

  <?php if($message != ""): ?>
    <div class="error-message"><?php echo $message; ?></div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const togglePassword = document.querySelector("#togglePassword");
  const password = document.querySelector("#password");
  togglePassword.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.classList.toggle("fa-eye-slash");
  });
</script>

</body>
</html>
