<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB Connection
$conn = new mysqli("localhost", "root", "", "future_prediction");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect user info
$name = $_POST['name'];
$mobile = $_POST['mobile'];
$rasi = $_POST['rasi'];
$natchathiram = $_POST['natchathiram'];
$has_kids = $_POST['has_kids'] ?? 'no';

// Check for duplicate mobile
$stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<script>alert('இந்த மொபைல் எண் ஏற்கனவே பதிவு செய்யப்பட்டுள்ளது.'); window.location.href='index.html';</script>";
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Insert into users
$stmt = $conn->prepare("INSERT INTO users (name, mobile, rasi, natchathiram, has_kids) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $mobile, $rasi, $natchathiram, $has_kids);
$stmt->execute();
$user_id = $stmt->insert_id;
$stmt->close();

// Insert kids if any
if ($has_kids == "yes" && !empty($_POST['kid_name'])) {
    $kid_names = $_POST['kid_name'];
    $kid_ages = $_POST['kid_age'];

    $stmt = $conn->prepare("INSERT INTO kids (user_id, kid_name, kid_age) VALUES (?, ?, ?)");
    for ($i = 0; $i < count($kid_names); $i++) {
        $kid_name = $kid_names[$i];
        $kid_age = $kid_ages[$i];
        $stmt->bind_param("isi", $user_id, $kid_name, $kid_age);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();
echo "<script>localStorage.setItem('username', " . json_encode($name) . "); window.location.href='predict.html';</script>";
?>
