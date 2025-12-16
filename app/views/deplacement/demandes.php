<?php $title = 'Toutes les demandes - Administrateur'; ?>
<?php ob_start(); ?>
<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- En-tête -->
        <div class="mb-4 mb-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dashboard" class="text-danger">Tableau de bord</a></li>
                            <li class="breadcrumb-item active">Demandes</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-2 animate-fade-in">
                        <i class="bi bi-list-check text-danger me-2"></i>
                        Toutes les demandes de remboursement
                    </h1>
                    <p class="text-muted mb-0">
                        <span id="totalCount"><?= $totalNotes ?></span> demande(s) au total
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger shadow-sm" onclick="exportToExcel()">
                        <i class="bi bi-download me-2"></i>
                        Exporter Excel
                    </button>
                    <button class="btn btn-outline-secondary shadow-sm" onclick="printTable()">
                        <i class="bi bi-printer me-2"></i>
                        Imprimer
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <small class="text-muted">Validé Manager</small>
                                <h4 class="mb-0 fw-bold"><?= $notesByStatus['valide_manager'] ?? 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <small class="text-muted">En cours Admin</small>
                                <h4 class="mb-0 fw-bold"><?= $notesByStatus['en_cours_admin'] ?? 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted">Approuvé</small>
                                <h4 class="mb-0 fw-bold"><?= $notesByStatus['approuve'] ?? 0 ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-currency-euro"></i>
                            </div>
                            <div>
                                <small class="text-muted">Montant Total</small>
                                <h4 class="mb-0 fw-bold"><?= number_format($totalMontant, 0, ',', ' ') ?> MAD</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des demandes -->
        <div class="card border-0 shadow-sm">
            <!-- Filtres JavaScript -->
            <div class="card-header bg-white border-0 p-4">
                <div class="row g-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   id="searchInput"
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Rechercher (nom, email, déplacement, lieu...)">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select id="statusFilter" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="valide_manager">Validé Manager (<?= $notesByStatus['valide_manager'] ?? 0 ?>)</option>
                            <option value="en_cours_admin">En cours Admin (<?= $notesByStatus['en_cours_admin'] ?? 0 ?>)</option>
                            <option value="approuve">Approuvé (<?= $notesByStatus['approuve'] ?? 0 ?>)</option>
                            <option value="rejetee_admin">Rejeté Admin (<?= $notesByStatus['rejetee_admin'] ?? 0 ?>)</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button id="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-2"></i>Réinitialiser
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-funnel me-1"></i>
                        <span id="filteredCount"><?= count($notes) ?></span> résultat(s) affiché(s)
                    </small>
                </div>
            </div>

            <!-- Corps du tableau -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 clean-table" id="notesTable">
                        <thead>
                            <tr class="border-bottom">
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Employé</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Déplacement</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Manager</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Montant</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Date</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Statut</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php if (empty($notes)): ?>
                                <tr id="emptyRow">
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucune demande trouvée</h5>
                                            <p class="text-muted small">Les notes de frais apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($notes as $note): ?>
                                    <?php 
                                    $statusConfig = [
                                        'valide_manager' => ['color' => 'primary', 'text' => 'Validé Manager', 'icon' => 'check-circle'],
                                        'en_cours_admin' => ['color' => 'warning', 'text' => 'En cours Admin', 'icon' => 'hourglass-split'],
                                        'approuve' => ['color' => 'success', 'text' => 'Approuvé', 'icon' => 'check-circle-fill'],
                                        'rejetee_admin' => ['color' => 'danger', 'text' => 'Rejeté Admin', 'icon' => 'x-circle-fill']
                                    ];
                                    $status = $statusConfig[$note['statut']] ?? ['color' => 'secondary', 'text' => ucfirst($note['statut']), 'icon' => 'info-circle'];
                                    ?>
                                    <tr class="border-bottom note-row" 
                                        data-status="<?= htmlspecialchars($note['statut']) ?>"
                                        data-search="<?= htmlspecialchars(strtolower($note['employe_nom'] . ' ' . $note['employe_email'] . ' ' . $note['deplacement_titre'] . ' ' . $note['deplacement_lieu'])) ?>">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" class="form-check-input row-select" value="<?= $note['note_id'] ?>">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <?php if (!empty($note['employe_avatar'])): ?>
                                                        <img src="/uploads/avatars/<?= htmlspecialchars($note['employe_avatar']) ?>" 
                                                             class="rounded-circle" 
                                                             width="40" 
                                                             height="40"
                                                             alt="Avatar"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="avatar-sm rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; display: none;">
                                                            <i class="bi bi-person text-danger"></i>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="avatar-sm rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-person text-danger"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($note['employe_nom']) ?></p>
                                                    <small class="text-muted"><?= htmlspecialchars($note['employe_email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="mb-0 fw-medium"><?= htmlspecialchars($note['deplacement_titre']) ?></p>
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($note['deplacement_lieu']) ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if ($note['manager_nom']): ?>
                                                <span class="text-muted small"><?= htmlspecialchars($note['manager_nom']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Aucun manager</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="fw-bold text-danger"><?= number_format($note['montant_total'], 2, ',', ' ') ?> MAD</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-muted small">
                                                <?= date('d/m/Y', strtotime($note['created_at'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-<?= $status['color'] ?> rounded-pill px-3 py-2">
                                                <i class="bi bi-<?= $status['icon'] ?> me-1"></i><?= $status['text'] ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item" href="/admin/note/<?= $note['note_id'] ?>/view">
                                                            <i class="bi bi-eye me-2"></i>Voir détails
                                                        </a>
                                                    </li>
                                                    <?php if ($note['statut'] === 'valide_manager'): ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-success" href="/admin/note/<?= $note['note_id'] ?>/approve" onclick="return confirm('Approuver cette note ?')">
                                                                <i class="bi bi-check-circle-fill me-2"></i>Approuver
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="/admin/note/<?= $note['note_id'] ?>/reject" onclick="return confirmReject(<?= $note['note_id'] ?>)">
                                                                <i class="bi bi-x-circle-fill me-2"></i>Rejeter
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="card-footer bg-white border-0 p-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <!-- Première page -->
                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=1">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        </li>
                        
                        <!-- Page précédente -->
                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Pages -->
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif;
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor;
                        
                        if ($endPage < $totalPages): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>

                        <!-- Page suivante -->
                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        
                        <!-- Dernière page -->
                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $totalPages ?>">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Info pagination -->
                <p class="text-center text-muted small mt-3 mb-0">
                    Page <?= $currentPage ?> sur <?= $totalPages ?> 
                    (<?= ($currentPage - 1) * $perPage + 1 ?> - <?= min($currentPage * $perPage, $totalNotes) ?> sur <?= $totalNotes ?> résultats)
                </p>
            </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<style>
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.clean-table tbody tr {
    transition: all 0.2s ease;
}

.clean-table tbody tr:hover {
    background-color: rgba(220, 38, 38, 0.02);
    transform: translateX(2px);
}

.pagination .page-link {
    color: #dc2626;
    border-color: #e5e7eb;
}

.pagination .page-link:hover {
    background-color: #dc2626;
    color: white;
    border-color: #dc2626;
}

.pagination .page-item.active .page-link {
    background-color: #dc2626;
    border-color: #dc2626;
}

.pagination .page-item.disabled .page-link {
    color: #9ca3af;
}

.note-row.d-none {
    display: none !important;
}

@media print {
    .btn, .pagination, .card-header, .dropdown {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');
    const selectAll = document.getElementById('selectAll');
    const rows = document.querySelectorAll('.note-row');
    const filteredCount = document.getElementById('filteredCount');
    const emptyRow = document.getElementById('emptyRow');

    // Fonction de filtrage
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const searchData = row.dataset.search;
            const rowStatus = row.dataset.status;
            
            const matchSearch = !searchTerm || searchData.includes(searchTerm);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            
            if (matchSearch && matchStatus) {
                row.classList.remove('d-none');
                visibleCount++;
            } else {
                row.classList.add('d-none');
            }
        });
        
        // Mettre à jour le compteur
        filteredCount.textContent = visibleCount;
        
        // Afficher message si aucun résultat
        if (visibleCount === 0 && !emptyRow) {
            const tbody = document.getElementById('tableBody');
            const noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.innerHTML = `
                <td colspan="8" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-search display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun résultat</h5>
                        <p class="text-muted small">Essayez de modifier vos filtres</p>
                    </div>
                </td>
            `;
            tbody.appendChild(noResultRow);
        } else {
            const noResultRow = document.getElementById('noResultRow');
            if (noResultRow) {
                noResultRow.remove();
            }
        }
    }

    // Événements de filtrage
    searchInput?.addEventListener('input', filterTable);
    statusFilter?.addEventListener('change', filterTable);

    // Réinitialiser les filtres
    resetBtn?.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        filterTable();
    });

    // Select all checkboxes
    selectAll?.addEventListener('change', function() {
        const visibleCheckboxes = Array.from(document.querySelectorAll('.row-select'))
            .filter(cb => !cb.closest('tr').classList.contains('d-none'));
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

// Confirmation de rejet
function confirmReject(noteId) {
    const motif = prompt('Motif du rejet (obligatoire):');
    if (motif && motif.trim()) {
        window.location.href = `/admin/note/${noteId}/reject?motif=${encodeURIComponent(motif)}`;
        return false;
    }
    return false;
}

// Export Excel (à implémenter)
function exportToExcel() {
    alert('Export Excel en cours de développement...');
}

// Print
function printTable() {
    window.print();
}
</script>
