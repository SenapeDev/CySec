<?php
session_start();
require_once("db.php");

if (isset($_SESSION['user'])) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];

// hash the password
$hashedPassword = hash('sha256', $password);

// generate a random IBAN number
$iban = "IT" . rand(10, 99) . "A" . rand(10000000000000, 99999999999999);

// generate a random Mastercard number with expiration date and CVV
$mastercard = "5" . rand(1, 5) . "1" . rand(0, 9) . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999);
$expirationDate = rand(1, 12) . "/" . rand(25, 28);
$cvv = rand(100, 999);

// register the user in the database
$query = $connection->prepare("INSERT INTO Users (Name, Surname, Email, Password, IBAN) VALUES (?, ?, ?, ?, ?)");
$query->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $iban);
$query->execute();

$insert_id = $connection->insert_id;

// register the Mastercard in the database
$query = $connection->prepare("INSERT INTO Credit_cards (User_ID, Card_number, Expiration, CVV) VALUES (?, ?, ?, ?)");
$query->bind_param("isss", $insert_id, $mastercard, $expirationDate, $cvv);
$query->execute();

// add a 'welcome bonus' of 1000€ to the user
$query = $connection->prepare("INSERT INTO Transactions (IBAN_sender, IBAN_receiver, Date, Reason, Amount) VALUES (?, ?, ?, ?, ?)");
$senderIban = "Secure Bank";
$date = date('Y-m-d H:i:s');
$reason = "Bonus di benvenuto";
$amount = 1000;
$query->bind_param("ssssd", $senderIban, $iban, $date, $reason, $amount);
$query->execute();

// redirect the user to the login page
header('Location: ../login.php');
# TVB <3
?>