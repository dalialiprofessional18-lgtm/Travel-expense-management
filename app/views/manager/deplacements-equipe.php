<?php $title = 'Déplacements de l\'équipe'; ?>
<?php ob_start(); ?>

<style>
/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}

/* Stats Cards */
.stats-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.75rem;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.stats-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.stats-card:hover .stats-icon {
    transform: scale(1.1) rotate(5deg);
}

/* Clean Table Style */
.clean-table {
    font-size: 0.9rem;
}

.clean-table thead th {
    font-weight: 600;
    letter-spacing: 0.5px;
    border: none;
}

.clean-table tbody tr {
    transition: all 0.2s ease;
    cursor: pointer;
}

.clean-table tbody tr:hover {
    background-color: #f8f9fa;
}

.clean-table tbody td {
    vertical-align: middle;
    border: none;
}

/* Badges */
.badge {
    font-weight: 500;
    font-size: 0.75rem;
}

/* Dropdown */
.dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.dropdown-item {
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

/* État vide */
.empty-state i {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Pagination */
.pagination .page-link {
    border: 1px solid #dee2e6;
    margin: 0 2px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background-color: #667eea;
    color: white;
    border-color: #667eea;
}

.pagination .page-item.active .page-link {
    background-color: #667eea;
    border-color: #667eea;
}

/* Form controls */
.form-select, .form-control {
    border-color: #dee2e6;
    transition: all 0.3s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Filter chip active state */
.filter-chip {
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-chip.active {
    background-color: #667eea !important;
    color: white !important;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .clean-table {
        font-size: 0.85rem;
    }
}
</style>

<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- En-tête -->
        <div class="mb-4 mb-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 animate-fade-in">
                        <i class="bi bi-people text-primary me-2"></i>
                        Déplacements de l'équipe
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-2"></i>
                        Gérez tous les déplacements de vos employés
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/deplacements/attribuer" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>
                        Attribuer un déplacement
                    </a>
                    <a href="/manager" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes statistiques -->
        <div class="row g-4 g-lg-4 mb-5">
            <!-- Total déplacements -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Total déplacements</h6>
                                <h3 class="mb-0 fw-bold"><?= $stats['total'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validé Manager -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Validé Manager</h6>
                                <h3 class="mb-0 fw-bold"><?= $stats['valide_manager'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- En cours admin -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">En cours admin</h6>
                                <h3 class="mb-0 fw-bold"><?= $stats['en_cours_admin'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approuvé -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-check2-all"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Approuvé</h6>
                                <h3 class="mb-0 fw-bold"><?= $stats['approuve'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section tableau -->
        <div class="card border-0 shadow-sm animate-fade-in">
            <!-- En-tête avec filtres -->
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h5 class="mb-0 fw-bold">Liste des déplacements</h5>
                        <small class="text-muted">Tous les déplacements de votre équipe</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <!-- Recherche -->
                        <div class="input-group" style="max-width: 250px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Rechercher...">
                        </div>
                        
                        <!-- Filtre statut -->
                        <select id="statusFilter" class="form-select" style="width: auto;">
                            <option value="">Tous les statuts</option>
                            <option value="valide_manager">Validé Manager</option>
                            <option value="rejetee_manager">Rejeté Manager</option>
                            <option value="en_cours_admin">En cours admin</option>
                            <option value="approuve">Approuvé</option>
                            <option value="rejetee_admin">Rejeté Admin</option>
                        </select>
                        
                        <!-- Filtre date -->
                        <select id="dateFilter" class="form-select" style="width: auto;">
                            <option value="">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                            <option value="year">Cette année</option>
                        </select>
                        
                        <!-- Export -->
                        <button class="btn btn-outline-secondary" onclick="exportTable()">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>

                <!-- Filtres rapides par statut -->
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-secondary filter-chip active" data-status="">
                        <i class="bi bi-list me-1"></i>Tous (<?= $stats['total'] ?? 0 ?>)
                    </span>
                    <span class="badge bg-success filter-chip" data-status="valide_manager">
                        <i class="bi bi-check-circle me-1"></i>Validé Manager (<?= $stats['valide_manager'] ?? 0 ?>)
                    </span>
                    <span class="badge bg-danger filter-chip" data-status="rejetee_manager">
                        <i class="bi bi-x-circle me-1"></i>Rejeté Manager (<?= $stats['rejetee_manager'] ?? 0 ?>)
                    </span>
                    <span class="badge bg-warning filter-chip" data-status="en_cours_admin">
                        <i class="bi bi-hourglass me-1"></i>En cours admin (<?= $stats['en_cours_admin'] ?? 0 ?>)
                    </span>
                    <span class="badge bg-info filter-chip" data-status="approuve">
                        <i class="bi bi-check2-all me-1"></i>Approuvé (<?= $stats['approuve'] ?? 0 ?>)
                    </span>
                    <span class="badge bg-dark filter-chip" data-status="rejetee_admin">
                        <i class="bi bi-slash-circle me-1"></i>Rejeté Admin (<?= $stats['rejetee_admin'] ?? 0 ?>)
                    </span>
                </div>
            </div>

            <!-- Corps du tableau -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 clean-table" id="deplacementsTable">
                        <thead>
                            <tr class="border-bottom">
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Employé</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Titre</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Destination</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Dates</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Durée</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Statut</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php if (!empty($deplacements)): ?>
                                <?php foreach ($deplacements as $d): 
                                    if($d['note_statut'] != 'brouillon'):?>
                                    <tr class="border-bottom deplacement-row" 
                                        data-status="<?= htmlspecialchars($d['note_statut']) ?>"
                                        data-date-depart="<?= $d['date_depart'] ?>"
                                        data-search="<?= strtolower(htmlspecialchars($d['employe_nom'] . ' ' . $d['titre'] . ' ' . $d['lieu'])) ?>">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" class="form-check-input row-checkbox">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $d['employe_avatar'] ?? '/assets/images/default-avatar.png' ?>" 
                                                     alt="<?= htmlspecialchars($d['employe_nom']) ?>"
                                                     class="rounded-circle me-2"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                                <span class="fw-semibold"><?= htmlspecialchars($d['employe_nom']) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="mb-0 fw-semibold"><?= htmlspecialchars($d['titre']) ?></p>
                                                <?php if (!empty($d['objet'])): ?>
                                                    <small class="text-muted"><?= substr(htmlspecialchars($d['objet']), 0, 50) ?>...</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($d['lieu']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex flex-column">
                                                <span class="text-muted small">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    <?= date('d/m/Y', strtotime($d['date_depart'])) ?>
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="bi bi-arrow-return-right me-1"></i>
                                                    <?= date('d/m/Y', strtotime($d['date_retour'])) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php
                                            $debut = new DateTime($d['date_depart']);
                                            $fin = new DateTime($d['date_retour']);
                                            $duree = $debut->diff($fin)->days + 1;
                                            ?>
                                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                                <?= $duree ?> jour<?= $duree > 1 ? 's' : '' ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php
                                            $statusConfig = [
                                                'valide_manager' => ['color' => 'success', 'icon' => 'check-circle', 'text' => 'Validé Manager'],
                                                'rejetee_manager' => ['color' => 'danger', 'icon' => 'x-circle', 'text' => 'Rejeté Manager'],
                                                'en_cours_admin' => ['color' => 'warning', 'icon' => 'hourglass', 'text' => 'En cours admin'],
                                                'approuve' => ['color' => 'info', 'icon' => 'check2-all', 'text' => 'Approuvé'],
                                                'rejetee_admin' => ['color' => 'dark', 'icon' => 'slash-circle', 'text' => 'Rejeté Admin']
                                            ];
                                            $status = $statusConfig[$d['note_statut']] ?? ['color' => 'secondary', 'icon' => 'circle', 'text' => ucfirst($d['note_statut'])];
                                            ?>
                                            <span class="badge bg-<?= $status['color'] ?> rounded-pill px-3 py-2">
                                                <i class="bi bi-<?= $status['icon'] ?> me-1"></i>
                                                <?= $status['text'] ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item" href="/manager/deplacement/<?= $d['id'] ?>">
                                                            <i class="bi bi-eye me-2"></i>Voir détails
                                                        </a>
                                                    </li>
                                                    <?php if ($d['note_statut'] === 'soumis'): ?>
                                                    <li>
                                                        <a class="dropdown-item text-success" href="/manager/deplacement/<?= $d['id'] ?>/approuver">
                                                            <i class="bi bi-check-circle me-2"></i>Valider
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="/manager/deplacement/<?= $d['id'] ?>/rejeter">
                                                            <i class="bi bi-x-circle me-2"></i>Rejeter
                                                        </a>
                                                    </li>
                                                    <?php endif; ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="/deplacements/<?= $d['user_id'] ?>">
                                                            <i class="bi bi-person me-2"></i>Voir profil employé
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                                                                                                        <?php endif; ?>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="emptyState">
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun déplacement trouvé</h5>
                                            <p class="text-muted small">Les déplacements de votre équipe apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer avec pagination -->
            <?php if (!empty($deplacements)): ?>
            <div class="card-footer bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small" id="resultsInfo">
                        Affichage de <strong id="showingCount"><?= count($deplacements) ?></strong> sur <strong id="totalCount"><?= $totalCount ?? count($deplacements) ?></strong> déplacements
                    </div>
                    <nav aria-label="Pagination">
                        <ul class="pagination mb-0" id="pagination">
                            <?php
                            $currentPage = $page ?? 1;
                            $totalPages = $totalPages ?? 1;
                            $perPage = $perPage ?? 10;
                            ?>
                            
                            <!-- Bouton précédent -->
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= max(1, $currentPage - 1) ?>" <?= $currentPage <= 1 ? 'tabindex="-1"' : '' ?>>
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <!-- Numéros de page -->
                            <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Bouton suivant -->
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= min($totalPages, $currentPage + 1) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const filterChips = document.querySelectorAll('.filter-chip');
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    // Recherche en temps réel
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    // Filtre par statut (select)
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    
    // Filtre par date
    if (dateFilter) {
        dateFilter.addEventListener('change', filterTable);
    }
    
    // Filtres chips
    filterChips.forEach(chip => {
        chip.addEventListener('click', function() {
            filterChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const status = this.dataset.status;
            statusFilter.value = status;
            filterTable();
        });
    });
    
    // Fonction de filtrage
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const dateValue = dateFilter.value;
        
        const rows = document.querySelectorAll('.deplacement-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const searchData = row.dataset.search;
            const rowStatus = row.dataset.status;
            const rowDate = new Date(row.dataset.dateDepart);
            
            // Filtre recherche
            const matchesSearch = searchTerm === '' || searchData.includes(searchTerm);
            
            // Filtre statut
            const matchesStatus = statusValue === '' || rowStatus === statusValue;
            
            // Filtre date
            let matchesDate = true;
            if (dateValue) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                switch(dateValue) {
                    case 'today':
                        matchesDate = rowDate.toDateString() === today.toDateString();
                        break;
                    case 'week':
                        const weekAgo = new Date(today);
                        weekAgo.setDate(today.getDate() - 7);
                        matchesDate = rowDate >= weekAgo;
                        break;
                    case 'month':
                        matchesDate = rowDate.getMonth() === today.getMonth() && 
                                     rowDate.getFullYear() === today.getFullYear();
                        break;
                    case 'year':
                        matchesDate = rowDate.getFullYear() === today.getFullYear();
                        break;
                }
            }
            
            // Afficher/masquer la ligne
            if (matchesSearch && matchesStatus && matchesDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour le compteur
        document.getElementById('showingCount').textContent = visibleCount;
        
        // Afficher l'état vide si aucun résultat
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? '' : 'none';
        }
    }
    
    // Sélection multiple
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const visibleCheckboxes = Array.from(rowCheckboxes).filter(cb => {
                return cb.closest('tr').style.display !== 'none';
            });
            visibleCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    }
    
    // Clic sur la ligne pour voir les détails
    document.querySelectorAll('.deplacement-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Ne pas déclencher si on clique sur une action
            if (e.target.closest('.dropdown') || e.target.closest('.form-check-input')) {
                return;
            }
            // Récupérer l'URL du bouton "Voir détails"
            const detailsLink = this.querySelector('.dropdown-menu a[href*="/manager/deplacement/"]');
            if (detailsLink) {
                window.location.href = detailsLink.href;
            }
        });
    });
});

// Fonction d'export (CSV)
function exportTable() {
    const rows = document.querySelectorAll('.deplacement-row:not([style*="display: none"])');
    let csv = 'Employé,Titre,Destination,Date départ,Date retour,Durée,Statut\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const employe = cells[1].textContent.trim();
        const titre = cells[2].textContent.trim().split('\n')[0];
        const lieu = cells[3].textContent.trim();
        const dates = cells[4].textContent.trim().split('\n');
        const duree = cells[5].textContent.trim();
        const statut = cells[6].textContent.trim();
        
        csv += `"${employe}","${titre}","${lieu}","${dates[0]}","${dates[1]}","${duree}","${statut}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `deplacements_equipe_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>