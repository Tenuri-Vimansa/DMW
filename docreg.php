<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";  // MySQL password
$dbname = "dmw";
$port = 3306;    // adjust if your MySQL uses custom port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("<script>alert('Connection failed: " . $conn->connect_error . "'); window.history.back();</script>");
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $First_Name      = trim($_POST['firstName']);
    $Last_Name       = trim($_POST['lastName']);
    $Email           = trim($_POST['email']);
    $Contact         = trim($_POST['phone']);
    $Speciality      = $_POST['specialty'];
    $working_Yrs     = $_POST['experience'];
    $WorkingHospital = trim($_POST['hospital']);
    $Doctor_Id       = trim($_POST['doctorId']);
    $Password        = $_POST['password'];
    $Re_password     = $_POST['confirmPassword'];

    // Password validation
    if ($Password !== $Re_password) {
        die("<script>alert('Passwords do not match'); window.history.back();</script>");
    }

    // Hash password
    $passwordHash = password_hash($Password, PASSWORD_BCRYPT);

    // Prepare SQL
    $stmt = $conn->prepare("INSERT INTO Doctor (First_Name, Last_Name, Email, Contact, Speciality, working_Yrs, WorkingHospital, Doctor_Id, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $First_Name, $Last_Name, $Email, $Contact, $Speciality, $working_Yrs, $WorkingHospital, $Doctor_Id, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='doctor-login.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
