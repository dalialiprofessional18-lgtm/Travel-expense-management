<?php
// app/Views/admin/categories/create.php
?>
<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- En-tête de la page avec breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item">
                            <a href="/admin" class="text-decoration-none">
                                <i class="bi bi-house-door"></i> Accueil
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/admin/categories" class="text-decoration-none">Catégories</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Nouvelle catégorie</li>
                    </ol>
                </nav>
                <h1 class="h2 mb-0 fw-bold text-dark">
                    <i class="bi bi-folder-plus text-danger me-2"></i>
                    Créer une nouvelle catégorie
                </h1>
            </div>
            <a href="/admin/categories" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <div class="row g-4">
            <!-- Colonne principale - Formulaire -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <!-- En-tête avec gradient -->
                    <div class="card-header border-0 bg-gradient p-4" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                        <div class="text-white">
                            <h3 class="mb-1 fw-bold">
                                <i class="bi bi-pencil-square me-2"></i>
                                Informations de la catégorie
                            </h3>
                            <p class="mb-0 opacity-90 small">
                                Remplissez les détails de la nouvelle catégorie de frais
                            </p>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($_SESSION['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form action="/admin/categories/store" method="POST" id="categoryForm">
                            <!-- Type de catégorie -->
                            <div class="mb-4">
                                <label for="type" class="form-label fw-semibold">
                                    <i class="bi bi-tag text-danger me-2"></i>
                                    Type de catégorie <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg shadow-sm" 
                                       id="type" 
                                       name="type" 
                                       placeholder="Ex: transport_longue_distance"
                                       required>
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Utilisez des underscores (_) pour séparer les mots
                                </small>
                            </div>

                            <hr class="my-4">

                            <!-- Sous-catégories -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-3">
                                    <i class="bi bi-tags text-danger me-2"></i>
                                    Sous-catégories
                                </label>
                                
                                <div class="input-group input-group-lg shadow-sm mb-3">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-tag-fill text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control border-start-0" 
                                           id="subcategory-input" 
                                           placeholder="Tapez une sous-catégorie et appuyez sur Entrée">
                                    <button type="button" class="btn btn-danger" id="add-subcategory">
                                        <i class="bi bi-plus-lg me-2"></i>Ajouter
                                    </button>
                                </div>

                                <small class="form-text text-muted mb-3 d-block">
                                    <i class="bi bi-lightbulb me-1"></i>
                                    Ajoutez les sous-catégories une par une pour une meilleure organisation
                                </small>

                                <!-- Zone d'affichage des tags -->
                                <div id="subcategories-display" class="subcategories-container p-4 rounded-3 border-2">
                                    <div class="empty-state text-center py-5">
                                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3 mb-0">Aucune sous-catégorie ajoutée</p>
                                        <small class="text-muted">Commencez à ajouter des sous-catégories</small>
                                    </div>
                                </div>

                                <!-- Input caché pour stocker les sous-catégories -->
                                <input type="hidden" name="description" id="description-hidden">
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-end gap-2 mt-5 pt-4 border-top">
                                <a href="/admin/categories" class="btn btn-light btn-lg px-4 shadow-sm">
                                    <i class="bi bi-x-circle me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-danger btn-lg px-5 shadow-sm">
                                    <i class="bi bi-check-circle me-2"></i>Enregistrer la catégorie
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale - Aide et Exemples -->
            <div class="col-lg-4">
                <!-- Carte d'aide -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-question-circle text-danger me-2"></i>
                            Aide
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">
                                <i class="bi bi-1-circle text-danger me-2"></i>
                                Type de catégorie
                            </h6>
                            <p class="text-muted small mb-0">
                                Le type est l'identifiant principal de la catégorie. 
                                Utilisez un nom descriptif en minuscules avec des underscores.
                            </p>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">
                                <i class="bi bi-2-circle text-danger me-2"></i>
                                Sous-catégories
                            </h6>
                            <p class="text-muted small mb-0">
                                Les sous-catégories permettent de classifier les frais de manière détaillée. 
                                Ajoutez-les une par une pour faciliter la saisie.
                            </p>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm mb-0">
                            <small>
                                <i class="bi bi-lightbulb-fill me-1"></i>
                                <strong>Conseil :</strong> Choisissez des noms de sous-catégories clairs et précis 
                                pour faciliter la sélection par les employés.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Carte d'exemples -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-lightbulb text-warning me-2"></i>
                            Exemples de catégories
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="example-item mb-3 p-3 bg-light rounded-3">
                            <div class="d-flex align-items-start mb-2">
                                <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                                    <i class="bi bi-train-front text-danger"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold small">transport_longue_distance</h6>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Train</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Avion</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Bus</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="example-item mb-3 p-3 bg-light rounded-3">
                            <div class="d-flex align-items-start mb-2">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2">
                                    <i class="bi bi-taxi-front text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold small">transport_courte_distance</h6>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Taxi</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Métro</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Tram</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="example-item mb-3 p-3 bg-light rounded-3">
                            <div class="d-flex align-items-start mb-2">
                                <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2">
                                    <i class="bi bi-building text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold small">hebergement</h6>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Hôtel</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Location</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Nuitées</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="example-item p-3 bg-light rounded-3">
                            <div class="d-flex align-items-start mb-2">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                                    <i class="bi bi-egg-fried text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold small">repas_jour</h6>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Déjeuner</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Dîner</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">Collation</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* ============================================
   STYLES MODERNES POUR LA PAGE DE CRÉATION
   ============================================ */

/* Animations générales */
.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Boutons avec effet */
.btn {
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
}

.btn:active {
    transform: translateY(0);
}

/* Input focus effects */
.form-control:focus,
.input-group:focus-within {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
}

/* Container des sous-catégories */
.subcategories-container {
    min-height: 150px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
}

.subcategories-container:has(.subcategory-tag) {
    border-style: solid;
    border-color: #dc3545;
    background: #fff;
}

.empty-state {
    transition: all 0.3s ease;
}

.subcategories-container:has(.subcategory-tag) .empty-state {
    display: none;
}

/* Tags des sous-catégories */
.subcategory-tag {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    margin: 6px;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
    animation: slideInTag 0.4s ease-out;
}

@keyframes slideInTag {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.subcategory-tag::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transition: left 0.6s;
}

.subcategory-tag:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
}

.subcategory-tag:hover::before {
    left: 100%;
}

.subcategory-tag .remove-tag {
    margin-left: 12px;
    cursor: pointer;
    font-size: 20px;
    font-weight: bold;
    opacity: 0.7;
    transition: all 0.2s;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.subcategory-tag .remove-tag:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg);
}

/* Mode Dark */
[data-bs-theme="dark"] .subcategory-tag,
.dark-mode .subcategory-tag,
body.dark .subcategory-tag {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    color: #1a1a1a;
    border-color: rgba(0, 0, 0, 0.08);
    box-shadow: 0 3px 10px rgba(255, 255, 255, 0.15);
}

[data-bs-theme="dark"] .subcategory-tag::before,
.dark-mode .subcategory-tag::before,
body.dark .subcategory-tag::before {
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.05), transparent);
}

[data-bs-theme="dark"] .subcategory-tag:hover,
.dark-mode .subcategory-tag:hover,
body.dark .subcategory-tag:hover {
    background: linear-gradient(135deg, #ffffff 0%, #f1f3f5 100%);
    box-shadow: 0 6px 20px rgba(255, 255, 255, 0.25);
}

[data-bs-theme="dark"] .subcategory-tag .remove-tag,
.dark-mode .subcategory-tag .remove-tag,
body.dark .subcategory-tag .remove-tag {
    background: rgba(0, 0, 0, 0.1);
}

[data-bs-theme="dark"] .subcategory-tag .remove-tag:hover,
.dark-mode .subcategory-tag .remove-tag:hover,
body.dark .subcategory-tag .remove-tag:hover {
    background: rgba(0, 0, 0, 0.2);
}

/* Exemples dans la sidebar */
.example-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.example-item:hover {
    transform: translateX(5px);
    border-color: #dc3545;
    background: white !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Breadcrumb */
.breadcrumb-item a {
    color: #6c757d;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: #dc3545;
}

/* Input group styling */
.input-group-text {
    transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
    border-color: #dc3545;
    background-color: #fff5f5;
}

/* Responsive */
@media (max-width: 768px) {
    .subcategory-tag {
        padding: 8px 14px;
        font-size: 0.813rem;
        margin: 4px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subcategoryInput = document.getElementById('subcategory-input');
    const addButton = document.getElementById('add-subcategory');
    const displayArea = document.getElementById('subcategories-display');
    const hiddenInput = document.getElementById('description-hidden');
    const form = document.getElementById('categoryForm');
    
    let subcategories = [];

    // Fonction pour mettre à jour l'affichage
    function updateDisplay() {
        // Cacher le message vide si on a des tags
        if (subcategories.length > 0) {
            displayArea.innerHTML = '';
        }
        
        subcategories.forEach((subcat, index) => {
            const tag = document.createElement('span');
            tag.className = 'subcategory-tag';
            tag.innerHTML = `
                <i class="bi bi-tag-fill me-2" style="font-size: 0.75rem;"></i>
                ${subcat}
                <span class="remove-tag" data-index="${index}">×</span>
            `;
            displayArea.appendChild(tag);
        });
        
        // Mettre à jour l'input caché avec les valeurs séparées par virgule
        hiddenInput.value = subcategories.join(', ');
    }

    // Fonction pour ajouter une sous-catégorie
    function addSubcategory() {
        const value = subcategoryInput.value.trim();
        if (value && !subcategories.includes(value)) {
            subcategories.push(value);
            updateDisplay();
            subcategoryInput.value = '';
            subcategoryInput.focus();
            
            // Animation de succès
            addButton.innerHTML = '<i class="bi bi-check-lg"></i>';
            setTimeout(() => {
                addButton.innerHTML = '<i class="bi bi-plus-lg me-2"></i>Ajouter';
            }, 1000);
        } else if (subcategories.includes(value)) {
            // Alert si déjà existe
            subcategoryInput.classList.add('is-invalid');
            setTimeout(() => {
                subcategoryInput.classList.remove('is-invalid');
            }, 2000);
        }
    }

    // Ajouter avec le bouton
    addButton.addEventListener('click', addSubcategory);

    // Ajouter avec la touche Entrée
    subcategoryInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addSubcategory();
        }
    });

    // Supprimer une sous-catégorie
    displayArea.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            const index = parseInt(e.target.dataset.index);
            const tagElement = e.target.closest('.subcategory-tag');
            
            // Animation de suppression
            tagElement.style.animation = 'slideOutTag 0.3s ease-out';
            setTimeout(() => {
                subcategories.splice(index, 1);
                updateDisplay();
            }, 300);
        }
    });

    // Animation de sortie
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideOutTag {
            to {
                opacity: 0;
                transform: scale(0.5) translateY(-20px);
            }
        }
    `;
    document.head.appendChild(style);

    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        if (subcategories.length === 0) {
            e.preventDefault();
            
            // Alert moderne
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show border-0 shadow-sm';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Attention !</strong> Veuillez ajouter au moins une sous-catégorie avant de continuer.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            form.insertBefore(alertDiv, form.firstChild);
            subcategoryInput.focus();
            
            // Scroll vers le formulaire
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            return false;
        }
    });
});
</script>