<?php $title = "Créer un nouvel utilisateur"; ?>
<?php ob_start(); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        Ajouter un nouvel utilisateur
                    </h3>
                </div>

                <div class="card-body p-5">
                    <form action="/users/store" method="POST">
                        <div class="row g-4">

                            <!-- Nom complet -->
                            <div class="col-md-6">
                                <label for="nom" class="form-label fw-bold">
                                    Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nom" id="nom" class="form-control form-control-lg"
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required autofocus>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">
                                    Adresse e-mail <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>

                            <!-- Mot de passe -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">
                                    Mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg" required>
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label fw-bold">
                                    Confirmer le mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirm" id="password_confirm"
                                       class="form-control form-control-lg" required>
                            </div>

                            <!-- Rôle -->
                            <div class="col-md-6">
                                <label for="role" class="form-label fw-bold">Rôle</label>
                                <select name="role" id="role" class="form-select form-select-lg">
                                    <option value="employe" <?= ($_POST['role'] ?? '') === 'employe' ? 'selected' : '' ?>>
                                        Employé
                                    </option>
                                    <option value="manager" <?= ($_POST['role'] ?? '') === 'manager' ? 'selected' : '' ?>>
                                        Manager
                                    </option>
                                    <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                        Administrateur
                                    </option>
                                </select>
                            </div>

                            <!-- Manager (facultatif) -->
                            <div class="col-md-6">
                                <label for="manager_id" class="form-label fw-bold">
                                    Manager direct (facultatif)
                                </label>
                                <select name="manager_id" id="manager_id" class="form-select form-select-lg">
                                    <option value="">-- Aucun manager --</option>
                                    <?php
                                    $userDAO = new \App\Models\DAO\UserDAO();
                                    $managers = $userDAO->findByRole(['manager', 'admin']);
                                    foreach ($managers as $mgr): ?>
                                        <option value="<?= $mgr->getId() ?>"
                                            <?= (isset($_POST['manager_id']) && $_POST['manager_id'] == $mgr->getId()) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($mgr->getNom()) ?>
                                            (<?= ucfirst($mgr->getRole()) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-success btn-lg">
                                Créer l'utilisateur
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/users" class="btn btn-secondary">
                            Retour à la liste des utilisateurs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

