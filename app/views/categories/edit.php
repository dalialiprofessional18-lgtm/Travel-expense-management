<?php
// app/Views/admin/categories/edit.php
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
                        <li class="breadcrumb-item active" aria-current="page">Modifier la catégorie</li>
                    </ol>
                </nav>
                <h1 class="h2 mb-0 fw-bold text-dark">
                    <i class="bi bi-pencil-square text-danger me-2"></i>
                    Modifier la catégorie
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
                                Modifiez les détails de la catégorie de frais
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

                        <form action="/admin/categories/update/<?= $category->getId() ?>" method="POST" id="categoryForm">
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
                                       value="<?= htmlspecialchars($category->getType()) ?>"
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
                            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                                <button type="button" 
                                        class="btn btn-outline-danger btn-lg px-4 shadow-sm"
                                        onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Supprimer
                                </button>
                                
                                <div class="d-flex gap-2">
                                    <a href="/admin/categories" class="btn btn-light btn-lg px-4 shadow-sm">
                                        <i class="bi bi-x-circle me-2"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-danger btn-lg px-5 shadow-sm">
                                        <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale - Informations et Aide -->
            <div class="col-lg-4">
                <!-- Carte d'informations -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle text-danger me-2"></i>
                            Informations
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">ID de la catégorie</small>
                            <h6 class="mb-0 fw-bold">#<?= $category->getId() ?></h6>
                        </div>
                        
                        <hr class="my-3">
                        
                        <?php if ($categorieDAO = new \App\Models\DAO\CategorieFraisDAO()): ?>
                            <?php if ($categorieDAO->isUsed($category->getId())): ?>
                                <div class="alert alert-warning border-0 shadow-sm mb-0">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2 fw-bold">Catégorie utilisée</h6>
                                            <p class="mb-0 small">
                                                Cette catégorie est utilisée dans des notes de frais. 
                                                La suppression est impossible tant qu'elle est utilisée.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success border-0 shadow-sm mb-0">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2 fw-bold">Catégorie libre</h6>
                                            <p class="mb-0 small">
                                                Cette catégorie n'est pas encore utilisée et peut être supprimée 
                                                sans affecter les notes de frais existantes.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

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
                                Modifiez-les selon vos besoins.
                            </p>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm mb-0">
                            <small>
                                <i class="bi bi-lightbulb-fill me-1"></i>
                                <strong>Conseil :</strong> Les modifications apportées seront visibles 
                                immédiatement pour tous les utilisateurs.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Historique ou stats (optionnel) -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-clock-history text-muted" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-3 mb-2 fw-bold">Dernière modification</h6>
                        <p class="text-muted small mb-0">
                            Cette catégorie a été modifiée pour la dernière fois récemment
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* ============================================
   STYLES MODERNES POUR LA PAGE D'ÉDITION
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

/* Alerts */
.alert {
    animation: slideInLeft 0.5s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .subcategory-tag {
        padding: 8px 14px;
        font-size: 0.813rem;
        margin: 4px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        width: 100%;
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
    
    // Charger les sous-catégories existantes
    const existingDesc = <?= json_encode($category->getDescription() ?? '') ?>;
    let subcategories = [];
    
    if (existingDesc) {
        subcategories = existingDesc.split(',').map(s => s.trim()).filter(s => s);
    }

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

    // Initialiser l'affichage
    updateDisplay();

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

// Fonction de confirmation de suppression
function confirmDelete() {
    // Modal Bootstrap moderne
    const modalHTML = `
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">Êtes-vous sûr de vouloir supprimer cette catégorie ?</h6>
                        <p class="text-center text-muted mb-0">
                            Cette action est irréversible et supprimera définitivement la catégorie.
                        </p>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-danger" onclick="executeDelete()">
                            <i class="bi bi-trash me-2"></i>Supprimer définitivement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Afficher la modal
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
    
    // Nettoyer après fermeture
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function executeDelete() {
    window.location.href = '/admin/categories/delete/<?= $category->getId() ?>';
}
</script>