<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Bank | Registrati</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    class="bi bi-bank me-2" viewBox="0 0 16 16">
                    <path
                        d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.501.501 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89L8 0ZM3.777 3h8.447L8 1 3.777 3ZM2 6v7h1V6H2Zm2 0v7h2.5V6H4Zm3.5 0v7h1V6h-1Zm2 0v7H12V6H9.5ZM13 6v7h1V6h-1Zm2-1V4H1v1h14Zm-.39 9H1.39l-.25 1h13.72l-.25-1Z" />
                </svg>
                Secure Bank
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="text-center mb-4">Benvenuto in Secure Bank</h4>

                        <div class="security-features p-3 mb-4">
                            <h6 class="mb-2">Sicurezza garantita</h6>
                            <p class="mb-0 small">La tua sicurezza è la nostra priorità. Utilizziamo standard di
                                crittografia avanzati per proteggere i tuoi dati personali.</p>
                        </div>

                        <form action="./include/register.php" method="POST" id="registerForm" class="needs-validation">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">Nome*</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Cognome*</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email*</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="form-text">Utilizzeremo questa email per le comunicazioni importanti</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password*</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <ul class="password-requirements list-unstyled mt-2">
                                    <li>✓ Minimo 12 caratteri</li>
                                    <li>✓ Almeno una lettera maiuscola</li>
                                    <li>✓ Almeno una lettera minuscola</li>
                                    <li>✓ Almeno un numero</li>
                                    <li>✓ Almeno un carattere speciale (!@#$%^&*)</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Conferma Password*</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label small" for="termsCheck">
                                        Ho letto e accetto i <a href="#" class="text-decoration-none">Termini e
                                            Condizioni</a> e l'<a href="#" class="text-decoration-none">Informativa
                                            sulla Privacy</a>*
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Crea Account
                            </button>

                            <p class="text-center mb-0">
                                Hai già un account? <a href="login.php" class="text-decoration-none">Accedi</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

</body>

</html>