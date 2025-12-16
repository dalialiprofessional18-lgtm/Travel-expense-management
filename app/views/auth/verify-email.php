<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Email</title>
    <link rel="stylesheet" href="./bootstrap-5.3.7-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-gradient d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
   
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                       
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-envelope-check-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-2">Vérifiez votre email</h2>
                            <p class="text-muted mb-0">
                                Un code à 6 chiffres a été envoyé à<br>
                                <strong><?= htmlspecialchars($email) ?></strong>
                            </p>
                        </div>
                        <!-- Form -->
                        <form action="/verify-email" method="POST" id="verifyForm">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Code de vérification</label>
                                <div class="d-flex gap-2 justify-content-center" id="codeInputs">
                                    <input type="text" class="form-control form-control-lg text-center fw-bold code-input" maxlength="1" style="width: 50px; font-size: 24px;" data-index="0">
                                    <input type="text" class="form-control form-control-lg text-center fw-bold code-input" maxlength="1" style="width: 50px; font-size: 24px;" data-index="1">
                                    <input type="text" class="form-control form-control-lg text-center fw-bold code-input" maxlength="1" style="width: 50px; font-size: 24px;" data-index="2">
                                    <input type="text" class="form-control form-control-lg text-center fw-bold code-input" maxlength="1" style="width: 50px; font-size: 24px;" data-index="3">
                                    <input type="text" class="form-control form-control-lg text-center fw-bold code-input" maxlength="1" style="width: 50px; font-size: 24px;" data-index="4">
                                    <input type="text" class="form-control form-control-lg text-center fw-bold code-input" maxlength="1" style="width: 50px; font-size: 24px;" data-index="5">
                                </div>
                                <input type="hidden" name="code" id="fullCode">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill fw-semibold mb-3" id="submitBtn" disabled>
                                <i class="bi bi-check-circle me-2"></i>Vérifier
                            </button>
                        </form>
                        <!-- Resend -->
                        <div class="text-center">
                            <p class="text-muted small mb-2">Vous n'avez pas reçu le code ?</p>
                            <button type="button" class="btn btn-link text-decoration-none" id="resendBtn" disabled>
                                <i class="bi bi-arrow-clockwise me-1"></i>Renvoyer le code
                            </button>
                            <div id="timer" class="text-muted small mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gestion des inputs du code
        const inputs = document.querySelectorAll('.code-input');
        const fullCodeInput = document.getElementById('fullCode');
        const submitBtn = document.getElementById('submitBtn');
        const resendBtn = document.getElementById('resendBtn');
        const timerDiv = document.getElementById('timer');

        let resendTimer = 60;
        let timerInterval;

        // Auto-focus et validation
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
               
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
               
                updateFullCode();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
               
                for (let i = 0; i < Math.min(pastedData.length, 6); i++) {
                    inputs[i].value = pastedData[i];
                }
               
                updateFullCode();
                if (pastedData.length === 6) {
                    document.getElementById('verifyForm').submit();
                }
            });
        });

        function updateFullCode() {
            const code = Array.from(inputs).map(input => input.value).join('');
            fullCodeInput.value = code;
           
            submitBtn.disabled = (code.length !== 6);
        }

        // Timer pour renvoyer le code
        function startTimer() {
            resendBtn.disabled = true;
            resendTimer = 60;
            timerDiv.textContent = `Renvoyer dans ${resendTimer} secondes`;

            timerInterval = setInterval(() => {
                resendTimer--;
                if (resendTimer <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    timerDiv.textContent = '';
                } else {
                    timerDiv.textContent = `Renvoyer dans ${resendTimer} secondes`;
                }
            }, 1000);
        }

        // Gestion du bouton "Renvoyer le code"
        resendBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('/resend-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: ''
                });

                const data = await response.json();

                if (data.success) {
                    alert('Code renvoyé avec succès !');
                    startTimer();
                } else {
                    alert(data.message || 'Erreur lors du renvoi du code');
                }
            } catch (error) {
                alert('Une erreur est survenue. Veuillez réessayer.');
            }
        });

        // Démarrer le timer au chargement de la page
        startTimer();

        // Focus sur le premier input au chargement
        inputs[0].focus();
    </script>
</body>
</html>