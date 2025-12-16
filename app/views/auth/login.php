<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="./bootstrap-5.3.7-dist/css/bootstrap.min.css" />
</head>
<body class="bg-gradient d-flex flex-column" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">

    <div class="container-fluid flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 g-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 1000px;">
            
            <!-- Section Image -->
            <div class="col-lg-6 d-none d-lg-block position-relative bg-primary">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-5">
                    <div class="mb-4">
                    <img
                  src="../../assets/Logo.png"
                  alt="Logo"
                  height="100"
                  width="100"
                  class="d-inline-block align-text-top me-2"
                />
                    </div>
                    <h1 class="display-4 fw-bold mb-3 text-center">Bienvenue</h1>
                    <p class="lead text-center mb-4 opacity-75">Connectez-vous pour accéder à votre espace personnel</p>
                    <div class="d-flex gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 text-center" style="width: 80px;">
                            <div class="fs-3 mb-1">🔒</div>
                            <small class="d-block">Sécurisé</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 text-center" style="width: 80px;">
                            <div class="fs-3 mb-1">⚡</div>
                            <small class="d-block">Rapide</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3 text-center" style="width: 80px;">
                            <div class="fs-3 mb-1">✨</div>
                            <small class="d-block">Simple</small>
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

                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2 display-6">Connexion</h2>
                        <p class="text-muted">Entrez vos identifiants pour continuer</p>
                    </div>

                    <form action="/login" method="POST" class="mb-4">
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                </svg>
                                Adresse email
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg border-2 rounded-3 py-3" placeholder="exemple@email.com" required>
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                </svg>
                                Mot de passe
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password" id="passwordInput" class="form-control form-control-lg border-2 rounded-3 py-3 pe-5" placeholder="••••••••" required>
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted" onclick="togglePassword()" style="z-index: 10;">
                                    <svg id="eyeIcon" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember me & Forgot -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label text-muted small" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none small">Mot de passe oublié ?</a>
                        </div>

                        <script>
                        function togglePassword() {
                            const passwordInput = document.getElementById('passwordInput');
                            const eyeIcon = document.getElementById('eyeIcon');
                            
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                eyeIcon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
                            } else {
                                passwordInput.type = 'password';
                                eyeIcon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
                            }
                        }
                        </script>

                        <!-- Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 py-3 fw-semibold shadow-sm mb-3">
                            Se connecter
                            <svg class="ms-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                            </svg>
                        </button>

                        <!-- Divider -->
                        <div class="position-relative my-4">
                            <hr class="text-muted">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OU</span>
                        </div>

                        <!-- Social Login -->
                     
                    </form>

                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Pas encore de compte ?
                            <a href="/register" class="text-decoration-none fw-semibold">Inscrivez-vous gratuitement</a>
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