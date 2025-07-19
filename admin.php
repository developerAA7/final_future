<?php
session_start();

// Set your admin password here
$admin_password = "developerAA";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "❌ wrong password!";
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// If not logged in, show password prompt
if (!isset($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="ta">
<head>
  <meta charset="UTF-8">
  <title>Admin Access</title>
  <style>
    body {
      background: #2c3e50;
      color: white;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .box {
      background: #34495e;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      width: 300px;
    }
    input {
      padding: 10px;
      width: 100%;
      border: none;
      border-radius: 5px;
      margin: 10px 0;
    }
    button {
      padding: 10px;
      background: #00c6ff;
      border: none;
      width: 100%;
      border-radius: 5px;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    .error {
      color: #ff6b6b;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <form method="POST" class="box">
    <h2>🔐 Admin Password</h2>
    <input type="password" name="password" placeholder="Enter Password" required />
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
  </form>
</body>
</html>
<?php
exit();
endif;
?>

<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "future_prediction");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$user_sql = "SELECT * FROM users ORDER BY id DESC";
$users_result = $conn->query($user_sql);
?>

<!DOCTYPE html>
<html lang="ta">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #d7d2cc, #304352);
      margin: 0;
      padding: 40px 20px;
      color: #333;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #fff;
    }
    .logout {
      text-align: right;
      margin-bottom: 10px;
    }
    .logout a {
      background: #ff4d4d;
      color: white;
      padding: 8px 15px;
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
    }
    .container {
      max-width: 1000px;
      margin: 0 auto;
    }
    .user-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 25px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }
    .user-card:hover {
      transform: scale(1.02);
    }
    .user-card h3 {
      margin-top: 0;
      color: #0072ff;
      font-size: 20px;
    }
    .user-card p {
      margin: 5px 0;
      line-height: 1.6;
    }
    .kids {
      margin-top: 10px;
      padding-left: 15px;
      background: #f9f9f9;
      border-radius: 8px;
      padding: 10px;
    }
    .kids ul {
      margin: 0;
      padding-left: 20px;
    }
    .kids li {
      margin-bottom: 5px;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="logout">
      <a href="admin.php?logout=1">🚪 Logout</a>
    </div>
    <h2>📋 All Registered Users</h2>

    <?php
    if ($users_result->num_rows > 0) {
        while ($row = $users_result->fetch_assoc()) {
            echo "<div class='user-card'>";
            echo "<h3>👤 " . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>📱 <strong>Mobile:</strong> " . htmlspecialchars($row['mobile']) . "</p>";
            echo "<p>🌙 <strong>Rasi:</strong> " . htmlspecialchars($row['rasi']) . "</p>";
            echo "<p>⭐ <strong>Natchathiram:</strong> " . htmlspecialchars($row['natchathiram']) . "</p>";
            echo "<p>👶 <strong>Has Kids:</strong> " . htmlspecialchars($row['has_kids']) . "</p>";

            if ($row['has_kids'] === 'yes') {
                $uid = $row['id'];
                $kids_sql = "SELECT * FROM kids WHERE user_id = $uid";
                $kids_result = $conn->query($kids_sql);

                if ($kids_result->num_rows > 0) {
                    echo "<div class='kids'><strong>👧 Children:</strong><ul>";
                    while ($kid = $kids_result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($kid['kid_name']) . " (Age: " . htmlspecialchars($kid['kid_age']) . ")</li>";
                    }
                    echo "</ul></div>";
                }
            }

            echo "</div>";
        }
    } else {
        echo "<p style='color:white;text-align:center;'>No user data found.</p>";
    }

    $conn->close();
    ?>
  </div>

</body>
</html>
