
<?php 
$title = 'Modifier l\'utilisateur';
ob_start();
?>

<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard" class="text-danger">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="/users" class="text-danger">Utilisateurs</a></li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-pencil-square text-danger me-2"></i>
                    Modifier l'utilisateur
                </h1>
                <p class="text-muted mb-0">Modifier les informations de <?= htmlspecialchars($userToEdit->getNom()) ?></p>
            </div>
            <a href="/users" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Formulaire -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informations de l'utilisateur</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="/users/<?= $userToEdit->getId() ?>/update" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            
                            <!-- Nom -->
                            <div class="mb-3">
                                <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="nom" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($userToEdit->getNom()) ?>"
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($userToEdit->getEmail()) ?>"
                                       required>
                            </div>

                            <!-- Rôle -->
                            <div class="mb-3">
                                <label class="form-label">Rôle <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="employe" <?= $userToEdit->getRole() === 'employe' ? 'selected' : '' ?>>Employé</option>
                                    <option value="manager" <?= $userToEdit->getRole() === 'manager' ? 'selected' : '' ?>>Manager</option>
                                    <option value="admin" <?= $userToEdit->getRole() === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                </select>
                            </div>

                            <!-- Manager -->
                            <div class="mb-3">
                                <label class="form-label">Manager (optionnel)</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">Aucun manager</option>
                                    <?php foreach ($managers as $manager): ?>
                                        <?php if ($manager->getId() !== $userToEdit->getId()): ?>
                                            <option value="<?= $manager->getId() ?>" 
                                                    <?= $userToEdit->getManagerId() == $manager->getId() ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($manager->getNom()) ?> (<?= $manager->getRole() ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Sélectionnez le manager de cet utilisateur</small>
                            </div>

                            <!-- Titre du poste -->
                            <div class="mb-3">
                                <label class="form-label">Titre du poste</label>
                                <input type="text" 
                                       name="job_title" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($userToEdit->getJobTitle() ?? '') ?>"
                                       placeholder="Ex: Développeur, Chef de projet...">
                            </div>

                            <!-- Détails d'expérience -->
                            <div class="mb-3">
                                <label class="form-label">Détails d'expérience</label>
                                <textarea name="experience_details" 
                                          class="form-control" 
                                          rows="4"
                                          placeholder="Décrivez l'expérience professionnelle..."><?= htmlspecialchars($userToEdit->getExperienceDetails() ?? '') ?></textarea>
                            </div>

                            <hr class="my-4">

                            <!-- Mot de passe -->
                            <h6 class="mb-3">Changer le mot de passe (optionnel)</h6>
                            <p class="text-muted small">Laissez vide si vous ne souhaitez pas modifier le mot de passe</p>
                            
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       placeholder="••••••••">
                                <small class="text-muted">Minimum 6 caractères</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" 
                                       name="confirm_password" 
                                       class="form-control" 
                                       placeholder="••••••••">
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between">
                                <a href="/users" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <!-- Informations actuelles -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">Informations actuelles</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <?php if ($userToEdit->getAvatarPath()): ?>
                                <img src="<?= htmlspecialchars($userToEdit->getAvatarPath()) ?>" 
                                     class="rounded-circle" 
                                     width="100" 
                                     height="100"
                                     alt="<?= htmlspecialchars($userToEdit->getNom()) ?>">
                            <?php else: ?>
                                <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="bi bi-person fs-1 text-danger"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>ID:</strong> <?= $userToEdit->getId() ?>
                            </li>
                            <li class="mb-2">
                                <strong>Email:</strong><br>
                                <small><?= htmlspecialchars($userToEdit->getEmail()) ?></small>
                            </li>
                            <li class="mb-2">
                                <strong>Rôle actuel:</strong><br>
                                <span class="badge <?php
                                    echo match($userToEdit->getRole()) {
                                        'admin' => 'bg-danger',
                                        'manager' => 'bg-warning',
                                        default => 'bg-primary'
                                    };
                                ?>"><?= htmlspecialchars($userToEdit->getRole()) ?></span>
                            </li>
                            <li class="mb-2">
                                <strong>Créé le:</strong><br>
                                <small><?= date('d/m/Y H:i', strtotime($userToEdit->getCreatedAt())) ?></small>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="card-title mb-0">Actions rapides</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/users/<?= $userToEdit->getId() ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-2"></i>Voir le profil
                            </a>
                            <form action="/users/<?= $userToEdit->getId() ?>" 
                                  method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-trash me-2"></i>Supprimer l'utilisateur
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
.card {
    border: none;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #d1d5db;
    padding: 0.625rem 0.875rem;
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
</style>
