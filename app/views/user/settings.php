<?php 
$title = 'Mes paramètres';
ob_start();
?>

<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- Breadcrumb -->
  

        <!-- Page Header -->
        <div class="mb-5">
            <h2 class="h3 mb-2">
                <i class="bi bi-gear-fill text-danger me-2"></i>
                Paramètres du compte
            </h3>
            <p class="text-muted mb-0">Gérez vos informations personnelles et préférences</p>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row">
            <!-- Menu latéral -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                                <i class="bi bi-person-circle me-2"></i>
                                Profil
                            </a>
                            <a href="#account" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="bi bi-shield-lock me-2"></i>
                                Compte et sécurité
                            </a>
                            <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="bi bi-palette me-2"></i>
                                Apparence
                            </a>
                            <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="bi bi-bell me-2"></i>
                                Notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-9">
                <div class="tab-content">
                    
                    <!-- Section Profil -->
                    <div class="tab-pane fade show active" id="profile">
                        <!-- Photo de couverture -->
                        

                        <!-- Photo de profil et informations -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">Photo de profil</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <?php if ($avatarUrl): ?>
                                            <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                                                 class="rounded-circle" 
                                                 width="100" 
                                                 height="100"
                                                 id="avatarPreview"
                                                 alt="Avatar">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" 
                                                 style="width: 100px; height: 100px;"
                                                 id="avatarPreview">
                                                <i class="bi bi-person fs-1 text-danger"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-1"><?= htmlspecialchars($user->getNom()) ?></h6>
                                        <p class="text-muted mb-2"><?= htmlspecialchars($user->getEmail()) ?></p>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('avatarInput').click()">
                                                <i class="bi bi-upload me-2"></i>Changer la photo
                                            </button>
                                            <?php if ($avatarUrl): ?>
                                                <form action="/settings/avatar/delete" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Supprimer la photo de profil ?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted d-block mt-2">JPG, PNG ou GIF. Max 2MB.</small>
                                    </div>
                                </div>
                                <form action="/settings/avatar" method="POST" enctype="multipart/form-data" id="avatarForm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <input type="file" 
                                           name="avatar" 
                                           id="avatarInput" 
                                           accept="image/*" 
                                           class="d-none"
                                           onchange="previewImage(this, 'avatarPreview'); this.form.submit();">
                                </form>
                            </div>
                        </div>

                        <!-- Informations personnelles -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">Informations personnelles</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="/settings/profile" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="nom" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($user->getNom()) ?>"
                                                   required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                   name="email" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($user->getEmail()) ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Titre du poste</label>
                                        <input type="text" 
                                               name="job_title" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user->getJobTitle() ?? '') ?>"
                                               placeholder="Ex: Développeur, Chef de projet...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Rôle actuel</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars(ucfirst($user->getRole())) ?>"
                                               disabled>
                                        <small class="text-muted">Le rôle ne peut être modifié que par un administrateur</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Détails d'expérience</label>
                                        <textarea name="experience_details" 
                                                  class="form-control" 
                                                  rows="5"
                                                  placeholder="Décrivez votre expérience professionnelle..."><?= htmlspecialchars($user->getExperienceDetails() ?? '') ?></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Section Compte et sécurité -->
                    <div class="tab-pane fade" id="account">
                        <!-- Changer le mot de passe -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-key-fill text-danger me-2"></i>
                                    Changer le mot de passe
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="/settings/password" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               name="current_password" 
                                               class="form-control" 
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               name="new_password" 
                                               class="form-control" 
                                               minlength="6"
                                               required>
                                        <small class="text-muted">Minimum 6 caractères</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               name="confirm_password" 
                                               class="form-control" 
                                               minlength="6"
                                               required>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-shield-check me-2"></i>Mettre à jour le mot de passe
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Informations du compte -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">Informations du compte</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">ID Utilisateur</label>
                                        <p class="mb-0 fw-semibold"><?= $user->getId() ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Date de création</label>
                                        <p class="mb-0 fw-semibold"><?= date('d/m/Y H:i', strtotime($user->getCreatedAt())) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Rôle</label>
                                        <p class="mb-0">
                                            <span class="badge <?php
                                                echo match($user->getRole()) {
                                                    'admin' => 'bg-danger',
                                                    'manager' => 'bg-warning',
                                                    default => 'bg-primary'
                                                };
                                            ?>"><?= htmlspecialchars(ucfirst($user->getRole())) ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Statut</label>
                                        <p class="mb-0">
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Actif
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Apparence -->
                    <div class="tab-pane fade" id="appearance">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">Préférences d'affichage</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="/settings/appearance" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold mb-3">Thème</label>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="theme" id="theme-light" value="light" checked>
                                                <label class="btn btn-outline-secondary w-100 text-start" for="theme-light">
                                                    <i class="bi bi-sun-fill me-2"></i>Clair
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="theme" id="theme-dark" value="dark">
                                                <label class="btn btn-outline-secondary w-100 text-start" for="theme-dark">
                                                    <i class="bi bi-moon-fill me-2"></i>Sombre
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="radio" class="btn-check" name="theme" id="theme-auto" value="auto">
                                                <label class="btn btn-outline-secondary w-100 text-start" for="theme-auto">
                                                    <i class="bi bi-circle-half me-2"></i>Auto
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Langue</label>
                                        <select name="language" class="form-select">
                                            <option value="fr" selected>Français</option>
                                            <option value="en">English</option>
                                            <option value="ar">العربية</option>
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-check-circle me-2"></i>Enregistrer les préférences
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Section Notifications -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">Préférences de notifications</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="/settings/notifications" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-1">Notifications par email</h6>
                                                <small class="text-muted">Recevoir des emails pour les mises à jour importantes</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="emailNotif" name="email_notifications" checked>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-1">Notes de frais validées</h6>
                                                <small class="text-muted">Notification quand une note est validée</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="noteValidated" name="note_validated" checked>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-1">Notes de frais rejetées</h6>
                                                <small class="text-muted">Notification quand une note est rejetée</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="noteRejected" name="note_rejected" checked>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-1">Nouveaux déplacements</h6>
                                                <small class="text-muted">Notification pour les nouveaux déplacements assignés</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="newTrip" name="new_trip" checked>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-check-circle me-2"></i>Enregistrer les préférences
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<style>
.list-group-item {
    border: none;
    border-left: 3px solid transparent;
    padding: 1rem 1.25rem;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #f9fafb;
    border-left-color: #dc2626;
}

.list-group-item.active {
    background-color: #fef2f2;
    border-left-color: #dc2626;
    color: #dc2626;
    font-weight: 500;
}

.card {
    border: none;
    border-radius: 12px;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #d1d5db;
    padding: 0.625rem 0.875rem;
    border-radius: 8px;
}

.form-control:focus, .form-select:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.1);
}

.btn-danger {
    background-color: #dc2626;
    border-color: #dc2626;
}

.btn-danger:hover {
    background-color: #b91c1c;
    border-color: #b91c1c;
}

.form-check-input:checked {
    background-color: #dc2626;
    border-color: #dc2626;
}

.cover-container {
    border-radius: 12px 12px 0 0;
}

.object-fit-cover {
    object-fit: cover;
}
</style>

<script>
function previewImage(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const target = document.getElementById(targetId);
            if (target.tagName === 'IMG') {
                target.src = e.target.result;
            } else {
                target.style.backgroundImage = `url(${e.target.result})`;
                target.style.backgroundSize = 'cover';
                target.style.backgroundPosition = 'center';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Animation des tabs
document.querySelectorAll('[data-bs-toggle="list"]').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        target.classList.add('show', 'active');
        
        document.querySelectorAll('[data-bs-toggle="list"]').forEach(t => {
            t.classList.remove('active');
        });
        this.classList.add('active');
    });
});
</script>


<script>
// Dark Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer la préférence sauvegardée
    const savedTheme = localStorage.getItem('theme') || '<?= $_SESSION['theme'] ?? 'light' ?>';
    
    // Appliquer le thème
    applyTheme(savedTheme);
    
    // Écouter les changements de radio buttons
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        if (radio.value === savedTheme) {
            radio.checked = true;
        }
        
        radio.addEventListener('change', function() {
            if (this.checked) {
                applyTheme(this.value);
                localStorage.setItem('theme', this.value);
            }
        });
    });
});

function applyTheme(theme) {
    const html = document.documentElement;
    
    if (theme === 'auto') {
        // Détecter la préférence système
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        html.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
    } else {
        html.setAttribute('data-bs-theme', theme);
    }
}

// Écouter les changements de préférence système si mode auto
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'auto') {
        document.documentElement.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
    }
});
</script>
