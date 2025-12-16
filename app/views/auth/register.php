<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="./bootstrap-5.3.7-dist/css/bootstrap.min.css" />
</head>
<body class="bg-gradient d-flex flex-column" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 100vh;">

    <div class="container-fluid flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 g-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1100px;">
            
            <!-- Section Image -->
            <div class="col-lg-6 d-none d-lg-block position-relative bg-success">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-5">
                    <div class="mb-4">

                      <img
                  src="../../assets/Logo.png"
                  alt="Logo"
                  height="120"
                  width="120"
                  class="d-inline-block align-text-top me-2"
                />

                    </div>
                    <h1 class="display-4 fw-bold mb-3 text-center">Rejoignez-nous</h1>
                    <p class="lead text-center mb-4 opacity-75">Créez votre compte en quelques secondes</p>
                    <div class="d-flex gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 text-center" style="width: 80px;">
                            <div class="fs-3 mb-1">📝</div>
                            <small class="d-block">Gratuit</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 text-center" style="width: 80px;">
                            <div class="fs-3 mb-1">🚀</div>
                            <small class="d-block">Rapide</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 text-center" style="width: 80px;">
                            <div class="fs-3 mb-1">🎯</div>
                            <small class="d-block">Facile</small>
                        </div>
                    </div>
                 
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 p-4">
                    <div class="d-flex gap-2 justify-content-center">
                        <div class="bg-white rounded-circle" style="width: 8px; height: 8px; opacity: 0.5;"></div>
                        <div class="bg-white rounded-circle" style="width: 8px; height: 8px;"></div>
                        <div class="bg-white rounded-circle" style="width: 8px; height: 8px; opacity: 0.5;"></div>
                    </div>
                </div>
            </div>

            <!-- Section Formulaire -->
            <div class="col-lg-6 bg-white">
                <div class="p-5 h-100 d-flex flex-column justify-content-center">
                    
                    <!-- Logo mobile -->
                    <div class="text-center mb-4 d-lg-none">
                           <div class="text-center mb-4 d-lg-none">
                        <div class="bg-gray bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                           <img
                  src="../../assets/Logo.png"
                  alt="Logo"
                  height="100"
                  width="100"
                  class="d-inline-block align-text-top me-2"
                />
                        </div>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 display-6">Créer un compte</h2>
                        <p class="text-muted">Commencez votre aventure avec nous</p>
                    </div>

                    <form action="/register" method="POST" class="mb-4">
                        
                        <!-- Nom complet -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                Nom complet
                            </label>
                            <input type="text" name="nom" class="form-control form-control-lg border-2 rounded-3 py-3" placeholder="Jean Dupont" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                </svg>
                                Adresse email
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg border-2 rounded-3 py-3" placeholder="exemple@email.com" required>
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                Mot de passe
                            </label>
                            <input type="password" name="password" class="form-control form-control-lg border-2 rounded-3 py-3" placeholder="••••••••" minlength="8" required>
                            <small class="text-muted">Minimum 8 caractères</small>
                        </div>

                        <!-- Confirmer mot de passe -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                Confirmer le mot de passe
                            </label>
                            <input type="password" name="password_confirm" class="form-control form-control-lg border-2 rounded-3 py-3" placeholder="••••••••" required>
                        </div>


                        <!-- Button -->
                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-3 py-3 fw-semibold shadow-sm mb-3">
                            S'inscrire
                            <svg class="ms-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                            </svg>
                        </button>

                        <!-- Divider -->
                        <div class="position-relative my-4">
                            <hr class="text-muted">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OU</span>
                        </div>

                        <!-- Social Register -->
                  
                    </form>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Vous avez déjà un compte ?
                            <a href="/login" class="text-decoration-none fw-semibold">Se connecter</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="w-100 py-4 text-gray text-center mt-5" style="background: rgba(199, 199, 199, 0.13);">
        <div class="container">
            <p class="mb-0 small">&copy; 2024 Mon Application - Tous droits réservés</p>
        </div>
    </footer>

    <script src="./bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>