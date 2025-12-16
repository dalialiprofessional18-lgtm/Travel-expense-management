<?php $title = 'Attribuer un déplacement'; ?>
<?php ob_start(); ?>

<style>
:root {
    --card-bg: #ffffff;
    --card-border: #e2e8f0;
    --text-primary: #1e293b;
    --text-muted: #64748b;
    --input-bg: #ffffff;
    --input-border: #cbd5e1;
    --accent: #6366f1;
    --success: #10b981;
}

[data-bs-theme="dark"] {
    --card-bg: #1e293b;
    --card-border: #334155;
    --text-primary: #f8fafc;
    --text-muted: #94a3b8;
    --input-bg: #0f172a;
    --input-border: #334155;
    --accent: #818cf8;
    --success: #34d399;
}

.glass-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

[data-bs-theme="dark"] .glass-card {
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}

.user-card {
    background: var(--card-bg);
    border: 2px solid var(--card-border);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.user-card:hover {
    border-color: var(--accent);
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
}

.user-card.selected {
    border-color: var(--success);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--card-border);
}

.form-control, .form-select {
    background-color: var(--input-bg);
    border: 2px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 14px;
    padding: 0.9rem 1.2rem;
    font-size: 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.btn-attribuer {
    background: linear-gradient(135deg, var(--success), #059669);
    border: none;
    border-radius: 14px;
    padding: 1rem 2.5rem;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    transition: all 0.3s ease;
}

.btn-attribuer:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(16, 185, 129, 0.5);
}

.btn-attribuer:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.search-box input {
    padding-left: 3rem;
}

.badge-role {
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Modal Styles */
.modal-content {
    background: var(--card-bg);
    color: var(--text-primary);
}

.btn-close-white {
    filter: brightness(0) invert(1);
}

[data-bs-theme="dark"] .btn-close {
    filter: brightness(0) invert(1);
}
</style>

<main class="admin-main py-5">
    <div class="container-fluid px-4 px-lg-5">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h4 class="display-5 fw-bold mb-2">Attribuer un déplacement</h4>
                <p class="text-muted">Créez un déplacement et assignez-le à un employé</p>
            </div>
            <a href="/manager" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <form id="attribuerForm" method="POST" action="/deplacements/attribuer">
            <div class="row g-5">
                
                <!-- Colonne gauche : Formulaire -->
                <div class="col-lg-7">
                    <div class="glass-card p-5">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-pencil-square text-primary me-2"></i>
                            Détails du déplacement
                        </h5>

                        <!-- Titre -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Titre de la mission</label>
                            <select id="typeDeplacement" name="titre_temp" class="form-select" required>
                                <option value="">-- Choisir un type --</option>
                                <option value="Mission commerciale">Mission commerciale</option>
                                <option value="Formation professionnelle">Formation professionnelle</option>
                                <option value="Visite client">Visite client</option>
                                <option value="Réunion d'affaires">Réunion d'affaires</option>
                                <option value="Conférence">Conférence</option>
                                <option value="Salon professionnel">Salon professionnel</option>
                                <option value="Audit terrain">Audit terrain</option>
                                <option value="Installation technique">Installation technique</option>
                                <option value="Support client">Support client</option>
                                <option value="Prospection commerciale">Prospection commerciale</option>
                                <option value="autre">Autre (personnalisé)</option>
                            </select>
                            <input type="text" id="titreCustom" name="titre_custom" class="form-control mt-3 d-none" placeholder="Votre titre personnalisé...">
                            <input type="hidden" name="titre" id="finalTitre">
                        </div>

                        <!-- Lieux -->
                        <div class="row g-3 mb-4">
                            <div class="col-lg-6">
                                <label class="form-label fw-bold">Lieu de départ</label>
                                <div id="lieuDepartSelector"></div>
                                <input type="hidden" name="lieu_depart" id="finalLieuDepart" required>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label fw-bold">Destination</label>
                                <div id="lieuDestinationSelector"></div>
                                <input type="hidden" name="lieu" id="finalLieuDest" required>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="row g-3 mb-4">
                            <div class="col-lg-6">
                                <label class="form-label fw-bold">Date de départ</label>
                                <input type="date" name="date_depart" id="dateDepart" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label fw-bold">Date de retour</label>
                                <input type="date" name="date_retour" id="dateRetour" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <!-- Objet -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Objet (facultatif)</label>
                            <textarea name="objet" id="objetInput" rows="4" class="form-control" placeholder="Décrivez les objectifs de cette mission..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Sélection employé -->
                <div class="col-lg-5">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-people-fill text-success me-2"></i>
                            Sélectionner un employé
                        </h5>

                        <!-- Recherche -->
                        <div class="search-box mb-4">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchUser" class="form-control" placeholder="Rechercher un employé...">
                        </div>

                        <!-- Liste des employés -->
                        <div id="usersList" style="max-height: 500px; overflow-y: auto;">
                            <?php if (empty($users)): ?>
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <p>Aucun employé disponible</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <div class="user-card mb-3" 
                                         data-user-id="<?= $user['id'] ?>"
                                         data-user-name="<?= htmlspecialchars($user['nom']) ?>"
                                         data-user-email="<?= htmlspecialchars($user['email']) ?>"
                                         data-user-role="<?= $user['role'] ?>"
                                         data-user-avatar="<?= $user['avatar_url'] ?? '/assets/images/default-avatar.png' ?>"
                                         data-user-phone="<?= $user['telephone'] ?? 'Non renseigné' ?>"
                                         data-user-job="<?= $user['poste'] ?? 'Employé' ?>"
                                         data-user-department="<?= $user['departement'] ?? 'Non spécifié' ?>"
                                         onclick="selectUser(this)">
                                        
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $user['avatar_url'] ?? '/assets/images/default-avatar.png' ?>" 
                                                 alt="<?= htmlspecialchars($user['nom']) ?>" 
                                                 class="user-avatar me-3">
                                            
                                            <div class="flex-grow-1">
                                                <div class="fw-bold"><?= htmlspecialchars($user['nom']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($user['email']) ?></div>
                                            </div>
                                            
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); viewUserProfile(this.closest('.user-card'))">
                                                    <i class="bi bi-eye"></i> Voir
                                                </button>
                                                <span class="badge bg-primary badge-role align-self-center">
                                                    <i class="bi bi-person me-1"></i>Employé
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Employé sélectionné -->
                        <input type="hidden" name="user_id" id="selectedUserId" required>
                        
                        <div id="selectedUserInfo" class="alert alert-info mt-4 d-none">
                            <div class="d-flex align-items-center">
                                <img id="selectedUserAvatar" class="user-avatar me-3" src="" alt="">
                                <div>
                                    <strong id="selectedUserName"></strong>
                                    <div class="small text-muted" id="selectedUserEmail"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton -->
                        <button type="submit" class="btn btn-attribuer w-100 mt-4" id="submitBtn" disabled>
                            <i class="bi bi-check-circle me-2"></i>
                            Créer et attribuer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- Modal Profil Utilisateur -->
<div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <!-- Cover Image -->
            <div class="position-relative" style="background: linear-gradient(135deg, var(--accent), #8b5cf6); height: 150px;">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Avatar Section -->
                <div class="text-center position-relative" style="margin-top: -75px;">
                    <img id="modalUserAvatar" 
                         src="" 
                         alt="Avatar" 
                         class="rounded-circle border border-5 border-white shadow" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                
                <!-- User Info -->
                <div class="px-4 pb-4">
                    <div class="text-center mb-4">
                        <h3 class="mb-1 fw-bold" id="modalUserName"></h3>
                        <p class="text-muted mb-2" id="modalUserJob"></p>
                        <span class="badge bg-success" id="modalUserStatus">
                            <i class="bi bi-check-circle me-1"></i>Disponible
                        </span>
                    </div>
                    
                    <div class="row g-4">
                        <!-- Contact Info -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3 p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-envelope-fill text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Email</small>
                                    <div class="fw-medium" id="modalUserEmail"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3 p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-telephone-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Téléphone</small>
                                    <div class="fw-medium" id="modalUserPhone"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3 p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-briefcase-fill text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Poste</small>
                                    <div class="fw-medium" id="modalUserPosition"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3 p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-building text-info" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Département</small>
                                    <div class="fw-medium" id="modalUserDepartment"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row g-3 mt-3">
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="h4 mb-0 fw-bold text-primary">12</div>
                                <small class="text-muted">Missions</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="h4 mb-0 fw-bold text-success">5.0</div>
                                <small class="text-muted">Note</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                                <div class="h4 mb-0 fw-bold text-warning">2 ans</div>
                                <small class="text-muted">Ancienneté</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-primary flex-fill" onclick="selectUserFromModal()">
                            <i class="bi bi-check-circle me-2"></i>Sélectionner cet employé
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ========================================
// 1. LocationSelector
// ========================================
class LocationSelector {
    constructor(containerId, finalInputId) {
        this.container = document.getElementById(containerId);
        this.finalInput = document.getElementById(finalInputId);
        this.init();
    }

    async init() {
        this.container.innerHTML = `
            <select class="form-select country-select mb-3" required>
                <option value="">Chargement des pays...</option>
            </select>
            <div class="city-container" style="display:none;">
                <select class="form-select city-select" disabled>
                    <option>Chargement des villes...</option>
                </select>
                <div class="spinner-border spinner-border-sm text-primary mt-2" style="display:none;"></div>
            </div>
        `;
        await this.loadCountries();
    }

    async loadCountries() {
        try {
            const res = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
            const data = await res.json();
            this.countries = data.map(c => ({code: c.cca2, name: c.name.common})).sort((a,b) => a.name.localeCompare(b.name));

            const select = this.container.querySelector('.country-select');
            select.innerHTML = `<option value="">Sélectionnez un pays</option>`;
            this.countries.forEach(c => select.add(new Option(c.name, c.code)));
        } catch {
            this.container.querySelector('.country-select').innerHTML = `<option value="">Erreur réseau</option>`;
        }
        this.attachEvents();
    }

    attachEvents() {
        const country = this.container.querySelector('.country-select');
        const city = this.container.querySelector('.city-select');
        const spinner = this.container.querySelector('.spinner-border');

        country.addEventListener('change', async () => {
            const code = country.value;
            const name = country.selectedOptions[0]?.text || '';

            if (!code) {
                this.container.querySelector('.city-container').style.display = 'none';
                this.finalInput.value = '';
                checkFormComplete();
                return;
            }

            this.container.querySelector('.city-container').style.display = 'block';
            spinner.style.display = 'inline-block';
            city.disabled = true;

            try {
                const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({country: name})
                });
                const data = await res.json();
                const cities = (data.data || []).sort();

                city.innerHTML = '<option value="">Choisissez une ville</option>';
                cities.forEach(c => city.add(new Option(c, c)));
                city.disabled = false;
            } catch {
                city.innerHTML = '<option value="">Indisponible</option>';
            }
            spinner.style.display = 'none';
        });

        city.addEventListener('change', () => {
            const ville = city.value;
            const pays = country.selectedOptions[0]?.text || '';
            const full = ville ? `${ville}, ${pays}` : '';
            this.finalInput.value = full;
            checkFormComplete();
        });
    }
}

// ========================================
// 2. Gestion de la sélection utilisateur
// ========================================
let selectedUser = null;
let currentModalUser = null;

function viewUserProfile(card) {
    currentModalUser = {
        id: card.dataset.userId,
        nom: card.dataset.userName,
        email: card.dataset.userEmail,
        avatar: card.dataset.userAvatar,
        phone: card.dataset.userPhone,
        job: card.dataset.userJob,
        department: card.dataset.userDepartment
    };
    
    // Remplir le modal avec les données
    document.getElementById('modalUserAvatar').src = currentModalUser.avatar;
    document.getElementById('modalUserName').textContent = currentModalUser.nom;
    document.getElementById('modalUserJob').textContent = currentModalUser.job;
    document.getElementById('modalUserEmail').textContent = currentModalUser.email;
    document.getElementById('modalUserPhone').textContent = currentModalUser.phone;
    document.getElementById('modalUserPosition').textContent = currentModalUser.job;
    document.getElementById('modalUserDepartment').textContent = currentModalUser.department;
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('userProfileModal'));
    modal.show();
}

function selectUserFromModal() {
    if (currentModalUser) {
        // Trouver la carte correspondante et la sélectionner
        const card = document.querySelector(`.user-card[data-user-id="${currentModalUser.id}"]`);
        if (card) {
            selectUser(card);
        }
        
        // Fermer le modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('userProfileModal'));
        modal.hide();
    }
}

function selectUser(card) {
    // Désélectionner tous
    document.querySelectorAll('.user-card').forEach(c => c.classList.remove('selected'));
    
    // Sélectionner celui-ci
    card.classList.add('selected');
    
    selectedUser = {
        id: card.dataset.userId,
        nom: card.dataset.userName,
        email: card.dataset.userEmail,
        avatar: card.dataset.userAvatar
    };
    
    // Mettre à jour l'input caché
    document.getElementById('selectedUserId').value = selectedUser.id;
    
    // Afficher les infos
    const infoDiv = document.getElementById('selectedUserInfo');
    infoDiv.classList.remove('d-none');
    document.getElementById('selectedUserAvatar').src = selectedUser.avatar;
    document.getElementById('selectedUserName').textContent = selectedUser.nom;
    document.getElementById('selectedUserEmail').textContent = selectedUser.email;
    
    checkFormComplete();
}

// ========================================
// 3. Recherche
// ========================================
function filterUsersBySearch() {
    const searchTerm = document.getElementById('searchUser').value.toLowerCase();
    const cards = document.querySelectorAll('.user-card');
    
    cards.forEach(card => {
        const name = card.dataset.userName.toLowerCase();
        const email = card.dataset.userEmail.toLowerCase();
        
        if (name.includes(searchTerm) || email.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// ========================================
// 4. Validation du formulaire
// ========================================
function checkFormComplete() {
    const titre = document.getElementById('finalTitre').value.trim();
    const depart = document.getElementById('finalLieuDepart').value.trim();
    const dest = document.getElementById('finalLieuDest').value.trim();
    const d1 = document.getElementById('dateDepart').value;
    const d2 = document.getElementById('dateRetour').value;
    const userId = document.getElementById('selectedUserId').value;

    const complete = titre && depart && dest && d1 && d2 && userId;
    document.getElementById('submitBtn').disabled = !complete;
}

// ========================================
// 5. Initialisation
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser les sélecteurs de lieu
    new LocationSelector('lieuDepartSelector', 'finalLieuDepart');
    new LocationSelector('lieuDestinationSelector', 'finalLieuDest');
    
    // Gestion du titre
    const selectTitre = document.getElementById('typeDeplacement');
    const customInput = document.getElementById('titreCustom');
    const finalTitre = document.getElementById('finalTitre');

    selectTitre.addEventListener('change', function() {
        if (this.value === 'autre') {
            customInput.classList.remove('d-none');
            customInput.disabled = false;
            customInput.required = true;
            customInput.focus();
            finalTitre.value = '';
        } else if (this.value) {
            customInput.classList.add('d-none');
            customInput.value = '';
            customInput.required = false;
            finalTitre.value = this.value;
        }
        checkFormComplete();
    });

    customInput.addEventListener('input', function() {
        finalTitre.value = this.value;
        checkFormComplete();
    });
    
    // Recherche
    document.getElementById('searchUser').addEventListener('input', filterUsersBySearch);
    
    // Validation des dates
    document.getElementById('dateDepart').addEventListener('change', checkFormComplete);
    document.getElementById('dateRetour').addEventListener('change', checkFormComplete);
});
</script>

