<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dmw";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ---------- Handle AJAX request for next appointment number ----------
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $date = $_GET['date'] ?? date('Y-m-d');
    $sql = "SELECT COUNT(*) AS total FROM dr_harland WHERE Date = '$date'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $next_ap_no = ($row['total'] ?? 0) + 1;
    echo $next_ap_no;
    exit; // stop further PHP execution
}

// ---------- Handle form submission ----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_name = $_POST['patient'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $symptoms = $_POST['symptoms'] ?? '';
    $date = $_POST['date'] ?? '';
    $ap_no = $_POST['ap'] ?? '';

    // Check if slots are full
    $sql = "SELECT COUNT(*) AS total FROM dr_harland WHERE Date = '$date'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $count = $row['total'] ?? 0;

    if ($count >= 15) {
        echo "<script>alert('Sorry, all 15 slots are full for this date.'); window.location.href='appointment.html';</script>";
        exit;
    }

    if (empty($ap_no)) {
        $ap_no = $count + 1;
    }

    $stmt = $conn->prepare("INSERT INTO dr_harland (Patient_Name, Age, Gender, Symptoms, Date, Ap_No) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssi", $patient_name, $age, $gender, $symptoms, $date, $ap_no);

    if ($stmt->execute()) {
        echo "<script>
        alert('Appointment confirmed! Your number is $ap_no');
        window.location.href='index.html';
      </script>";

    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>
