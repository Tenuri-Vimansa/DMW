<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dmw";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $DoctorId = $_POST['doctorId'] ?? '';
    $Password = $_POST['password'] ?? '';

 
    $stmt = $conn->prepare("SELECT Password FROM Doctor WHERE Doctor_Id = ?");
    $stmt->bind_param("s", $DoctorId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($Password, $hashedPassword)) {
            $_SESSION['doctor_id'] = $DoctorId;
            header("Location: doctorprofile.html");
            exit();
        } else {
            echo "<script>alert('Invalid Doctor ID or Password'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid Doctor ID or Password'); window.location.href='login.html';</script>";
    }
    $stmt->close();
}

$conn->close();
?>
