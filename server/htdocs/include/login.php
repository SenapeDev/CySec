<?php
session_start();
require_once("db.php");

if (isset($_SESSION['user'])) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
$hasedPassword = hash('sha256', $password);

$query = $connection->prepare("SELECT * FROM Users WHERE Email = ? AND Password = ?");
$query->bind_param("ss", $email, $hasedPassword);
$query->execute();

$result = $query->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['user'] = $row['User_ID'];
    $_SESSION['IBAN'] = $row['IBAN'];
    header('Location: ../dashboard.php');
    exit;
} else {
    header('Location: ../login.php?error=1');
    exit;
}

?>