<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$iban_sender = $_SESSION['IBAN'];
$iban_receiver = $_POST['iban'];
$amount = $_POST['amount'];
$reason = $_POST['reason'];

// Verifica che i campi non siano vuoti
if (empty($iban_receiver) || empty($amount) || empty($reason)) {
    header('Location: ../dashboard.php');
    exit;
}

// Verifica se l'IBAN del destinatario è valido
if ($iban_receiver === $iban_sender) {
    header('Location: ../dashboard.php');
    exit;
}

// 1. Controlla il saldo disponibile dell'utente
$sql_balance = "SELECT 
    (SUM(CASE WHEN IBAN_receiver = ? THEN Amount ELSE 0 END) - 
     SUM(CASE WHEN IBAN_sender = ? THEN Amount ELSE 0 END)) AS balance 
    FROM Transactions";
$stmt_balance = $connection->prepare($sql_balance);
$stmt_balance->bind_param('ss', $iban_sender, $iban_sender);
$stmt_balance->execute();
$result_balance = $stmt_balance->get_result();
$balance = $result_balance->fetch_assoc()['balance'] ?? 0;

// 2. Verifica che l'utente abbia fondi sufficienti
if ($balance < $amount) {
    header('Location: ../dashboard.php');
    exit;
}

// 3. Effettua la transazione
$sql_insert = "INSERT INTO Transactions (IBAN_sender, IBAN_receiver, Date, Reason, Amount) 
               VALUES (?, ?, NOW(), ?, ?)";
$stmt_insert = $connection->prepare($sql_insert);
$stmt_insert->bind_param('sssi', $iban_sender, $iban_receiver, $reason, $amount);

if ($stmt_insert->execute()) {
    $_SESSION['success'] = "Pagamento effettuato con successo!";
} else {
    $_SESSION['error'] = "Errore durante l'elaborazione del pagamento.";
}

// 4. Chiudi la connessione e reindirizza l'utente
$stmt_balance->close();
$stmt_insert->close();
$connection->close();

header('Location: ../dashboard.php');
exit;
?>
