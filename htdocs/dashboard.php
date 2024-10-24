<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once('./include/db.php');

// Query per ottenere i dati dell'utente
$sql = "
    SELECT
        Users.Name as Name,
        Users.Surname as Surname,
        Users.IBAN as IBAN,
        Credit_cards.Card_number as Card_number,
        Credit_cards.Expiration as Expiration,
        Credit_cards.CVV as CVV,
        (COALESCE(SUM(CASE WHEN Transactions.IBAN_receiver = Users.IBAN THEN Transactions.Amount ELSE 0 END), 0) -
         COALESCE(SUM(CASE WHEN Transactions.IBAN_sender = Users.IBAN THEN Transactions.Amount ELSE 0 END), 0)) AS Balance
    FROM Users
    LEFT JOIN Credit_cards ON Credit_cards.User_ID = Users.User_ID
    LEFT JOIN Transactions ON Transactions.IBAN_sender = Users.IBAN OR Transactions.IBAN_receiver = Users.IBAN
    WHERE Users.User_ID = ?
    GROUP BY Users.User_ID, Users.Name, Users.Surname, Users.IBAN, Credit_cards.Card_number, Credit_cards.Expiration, Credit_cards.CVV;
";

$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $_SESSION['user']);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

$name = $row['Name'];
$surname = $row['Surname'];
$IBAN = $row['IBAN'];
$card_number = $row['Card_number'];
$expiration = $row['Expiration'];
$CVV = $row['CVV'];
$balance = $row['Balance'];

$stmt->close();
?>


<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Internet Banking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php
require_once('./include/navbar.php');
?>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 ms-sm-auto px-md-4 py-4">
            <!-- Overview Section -->
            <div class="row g-4 mb-4">
                <!-- Left Column (Saldo + IBAN) -->
                <div class="col-12 col-lg-6">
                    <div class="row g-4 mb-4">
                        <!-- Balance Card -->
                        <div class="col-12">
                            <div class="card card-balance h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Saldo Disponibile</h6>
                                        <span class="badge">Conto Corrente</span>
                                    </div>
                                    <h2 class="mb-1"><?php echo '€' . number_format($balance, 2, ',', '.'); ?></h2>
                                </div>
                            </div>
                        </div>

                        <!-- IBAN Card -->
                        <div class="col-12">
                            <div class="card iban-card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Coordinate Bancarie</h6>
                                    </div>
                                    <h5 class="mb-0"><?php echo wordwrap($IBAN, 4, ' ', true); ?></h5>
                                    <small class="text-muted">Banca: Secure Bank</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Credit Card) -->
                <div class="col-12 col-lg-6 d-flex justify-content-center align-items-start">
                    <div class="credit-card"
                        style="width: calc(85.6mm * 1.2); height: calc(53.98mm * 1.2); border-radius: 12px; background: linear-gradient(135deg, #032f57 0%, #0a6fc9 100%); padding: 30px; color: white; position: relative; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                        <!-- Intestazione della carta -->
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <small class="text-uppercase">Carta di Credito</small>
                                <p class="mb-0" style="font-size: 1.2rem;"><?php echo $name . " " . $surname; ?></p>
                            </div>
                        </div>
                        <!-- Numero della carta -->
                        <div class="card-number" id="card-number" style="letter-spacing: 2px; font-size: 1.5rem; margin: 1rem 0;">
                            <?php echo wordwrap($card_number, 4, ' ', true); ?>
                        </div>
                        <!-- Data di scadenza e CVV -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="d-block mb-1">Valida fino a</small>
                                <span style="font-size: 1.2rem;"><?php echo $expiration; ?></span>
                            </div>
                            <div>
                                <small class="d-block mb-1">CVV</small>
                                <span id="cvv" style="font-size: 1.2rem;"><?php echo $CVV; ?></span>
                            </div>
                            <div>
                            </div>
                            <svg width="60" height="36" viewBox="0 0 60 36" xmlns="http://www.w3.org/2000/svg" class="mastercard-logo">
                                <circle cx="20" cy="18" r="18" fill="#EB001B" />
                                <circle cx="40" cy="18" r="18" fill="#F79E1B" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Transazioni Recenti</h6>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead class="table">
                            <tr>
                                <th scope="col">Data</th>
                                <th scope="col">Pagamento</th>
                                <th scope="col">Causale</th>
                                <th scope="col">Importo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_transactions = "
                                SELECT Date, IBAN_sender, IBAN_receiver, Reason, Amount 
                                FROM Transactions 
                                WHERE IBAN_sender = ? OR IBAN_receiver = ?
                                ORDER BY Date DESC 
                                LIMIT 10;
                            ";
                            $stmt_transactions = $connection->prepare($sql_transactions);
                            $stmt_transactions->bind_param('ss', $IBAN, $IBAN);
                            $stmt_transactions->execute();
                            $result_transactions = $stmt_transactions->get_result();

                            if ($result_transactions->num_rows > 0) {
                                while ($transaction = $result_transactions->fetch_assoc()) {
                                    echo '<tr>';
                                    // Colonna Data
                                    echo '<td>' . date('d/m/Y', strtotime($transaction['Date'])) . '</td>';
                                    
                                    // Colonna Dettagli
                                    if ($transaction['IBAN_sender'] == $IBAN) {
                                        // Se l'utente è il mittente, mostra il destinatario
                                        $receiver = ($transaction['IBAN_receiver'] == 'secure bank') ? $transaction['IBAN_receiver'] : wordwrap($transaction['IBAN_receiver'], 4, ' ', true);
                                        echo '<td>Pagamento a ' . $receiver . '</td>';
                                    } else {
                                        // Se l'utente è il destinatario, mostra il mittente
                                        $sender = ($transaction['IBAN_sender'] == 'Secure Bank') ? $transaction['IBAN_sender'] : wordwrap($transaction['IBAN_sender'], 4, ' ', true);
                                        echo '<td>Ricevuto da ' . $sender . '</td>';
                                    }

                                    // Colonna Causale
                                    echo '<td>' . htmlspecialchars($transaction['Reason']) . '</td>';

                                    // Colonna Importo
                                    if ($transaction['IBAN_receiver'] == $IBAN) {
                                        // Pagamento ricevuto (verde)
                                        echo '<td><span class="badge bg-success">+' . '€' . number_format($transaction['Amount'], 2, ',', '.') . '</span></td>';
                                    } else {
                                        // Pagamento inviato (rosso)
                                        echo '<td><span class="badge bg-danger">-' . '€' . number_format($transaction['Amount'], 2, ',', '.') . '</span></td>';
                                    }

                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center">Nessuna transazione disponibile</td></tr>';
                            }

                            $stmt_transactions->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- card for do a payment -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Effettua un pagamento</h6>
                    </div>
                </div>
<div class="card-body">
    <form action="./include/payment.php" method="post">
        <div class="row mb-2">
            <div class="col">
                <label for="iban" class="form-label">IBAN Destinatario</label>
                <input type="text" class="form-control form-control-sm" id="iban" name="iban" required maxlength="34" placeholder="Inserisci IBAN">
            </div>
            <div class="col">
                <label for="amount" class="form-label">Importo (€)</label>
                <input type="number" class="form-control form-control-sm" id="amount" name="amount" min="0.01" step="0.01" required placeholder="0,00">
            </div>
        </div>
        <div class="mb-2">
            <label for="reason" class="form-label">Causale</label>
            <input type="text" class="form-control form-control-sm" id="reason" name="reason" required maxlength="255" placeholder="Inserisci la causale">
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-sm btn-primary">Invia pagamento</button>
        </div>
    </form>
</div>

        </main>
    </div>
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
