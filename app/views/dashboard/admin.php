<?php $title = 'Tableau de bord - Administrateur'; ?>
<?php ob_start(); ?>
<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- En-tête -->
        <div class="mb-4 mb-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 animate-fade-in">
                        <i class="bi bi-shield-fill-check text-danger me-2"></i>
                        Tableau de bord Administrateur
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-2"></i>
                        <?= strftime('%A %d %B %Y') ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger shadow-sm" onclick="exportToExcel()">
                        <i class="bi bi-download me-2"></i>
                        Exporter Excel
                    </button>
                    <a href="/users" class="btn btn-danger shadow-sm">
                        <i class="bi bi-people-fill me-2"></i>
                        Gérer Utilisateurs
                    </a>
                </div>
            </div>
        </div>

        <!-- Cartes statistiques -->
        <div class="row g-4 g-lg-4 mb-5">
            <!-- Total Utilisateurs -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Total Utilisateurs</h6>
                                <h3 class="mb-0 fw-bold"><?= $totalUsers ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Déplacements -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-airplane-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Déplacements</h6>
                                <h3 class="mb-0 fw-bold"><?= $totalDeplacements ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- En attente -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">En attente</h6>
                                <h3 class="mb-0 fw-bold"><?= $pendingDeplacements ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Montant Total -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-currency-euro"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Montant Total</h6>
                                <h3 class="mb-0 fw-bold"><?= number_format($totalMontant, 2, ',', ' ') ?> MAD</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques statistiques -->
        <div class="row g-4 mb-5">
            <!-- Graphique des statuts -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-pie-chart-fill text-danger me-2"></i>
                            Répartition par statut
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique des montants mensuels -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up text-success me-2"></i>
                            Évolution mensuelle des montants
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="monthlyChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top 5 employés -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-bar-chart-fill text-info me-2"></i>
                            Top 5 employés par montant
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="employeesChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Répartition par type de déplacement -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-layers-fill text-warning me-2"></i>
                            Montants par statut
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="amountByStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des notes de frais -->
        <div class="card border-0 shadow-sm animate-fade-in">
            <!-- En-tête du tableau -->
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h3 class="mb-0 fw-bold h5">
                            <i class="bi bi-list-check text-danger me-2"></i>
                            Toutes les demandes de remboursement
                        </h3>
                        <small class="text-muted"><?= count($allNotes) ?> note(s) de frais</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <div class="input-group" style="max-width: 250px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Rechercher...">
                        </div>
                        <select class="form-select" id="statusFilter" style="width: auto;">
                            <option value="">Tous les statuts</option>
                            <option value="valide_manager">Validé Manager (<?= $notesByStatus['valide_manager'] ?? 0 ?>)</option>
                            <option value="en_cours_admin">En cours Admin (<?= $notesByStatus['en_cours_admin'] ?? 0 ?>)</option>
                            <option value="approuve">Approuvé (<?= $notesByStatus['approuve'] ?? 0 ?>)</option>
                            <option value="rejetee_admin">Rejeté Admin (<?= $notesByStatus['rejetee_admin'] ?? 0 ?>)</option>
                        </select>
                    </div>
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
                        <tbody>
                            <?php if (empty($allNotes)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucune demande</h5>
                                            <p class="text-muted small">Les notes de frais apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $adminVisibleStatuses = ['valide_manager', 'en_cours_admin', 'approuve', 'rejetee_admin'];
                                ?>
                                <?php foreach ($allNotes as $note): ?>
                                    <?php 
                                    if (!in_array($note['statut'], $adminVisibleStatuses)) {
                                        continue;
                                    }
                                    
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
                                        data-employe="<?= htmlspecialchars(strtolower($note['employe_nom'])) ?>"
                                        data-deplacement="<?= htmlspecialchars(strtolower($note['deplacement_titre'])) ?>">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" class="form-check-input row-select" value="<?= $note['note_id'] ?>">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <?php if ($note['employe_avatar']): ?>
                                                        <img src="<?= htmlspecialchars($note['employe_avatar']) ?>" 
                                                             class="rounded-circle" 
                                                             width="40" 
                                                             height="40"
                                                             alt="Avatar">
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
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-warning" href="/admin/note/<?= $note['note_id'] ?>/revoke" onclick="return confirmRevoke(<?= $note['note_id'] ?>)">
                                                                <i class="bi bi-arrow-counterclockwise me-2"></i>Révoquer décision
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
        </div>

    </div>
</main>

<style>
    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

<script>
// Données PHP vers JavaScript
const chartData = {
    statusCounts: <?= json_encode($notesByStatus ?? []) ?>,
    allNotes: <?= json_encode($allNotes ?? []) ?>
};

// Initialisation des graphiques
document.addEventListener('DOMContentLoaded', function() {
    initStatusChart();
    initMonthlyChart();
    initEmployeesChart();
    initAmountByStatusChart();
    initTableFilters();
});

// Graphique en donut - Répartition par statut
function initStatusChart() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;
    
    const statusConfig = {
        'valide_manager': { label: 'Validé Manager', color: '#0d6efd' },
        'en_cours_admin': { label: 'En cours Admin', color: '#ffc107' },
        'approuve': { label: 'Approuvé', color: '#198754' },
        'rejetee_admin': { label: 'Rejeté Admin', color: '#dc3545' }
    };
    
    const labels = [];
    const data = [];
    const colors = [];
    
    Object.keys(statusConfig).forEach(key => {
        const count = chartData.statusCounts[key] || 0;
        if (count > 0) {
            labels.push(statusConfig[key].label);
            data.push(count);
            colors.push(statusConfig[key].color);
        }
    });
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: { size: 12 }
                    }
                }
            }
        }
    });
}

// Graphique en ligne - Évolution mensuelle
function initMonthlyChart() {
    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;
    
    // Grouper par mois
    const monthlyData = {};
    chartData.allNotes.forEach(note => {
        const date = new Date(note.created_at);
        const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        monthlyData[monthKey] = (monthlyData[monthKey] || 0) + parseFloat(note.montant_total);
    });
    
    // Trier et formater
    const sortedMonths = Object.keys(monthlyData).sort();
    const labels = sortedMonths.map(m => {
        const [year, month] = m.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
    });
    const data = sortedMonths.map(m => monthlyData[m]);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant (MAD)',
                data: data,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toFixed(0) + ' MAD'
                    }
                }
            }
        }
    });
}

// Graphique en barres - Top 5 employés
function initEmployeesChart() {
    const ctx = document.getElementById('employeesChart');
    if (!ctx) return;
    
    // Grouper par employé
    const employeeData = {};
    chartData.allNotes.forEach(note => {
        const name = note.employe_nom;
        employeeData[name] = (employeeData[name] || 0) + parseFloat(note.montant_total);
    });
    
    // Top 5
    const sorted = Object.entries(employeeData)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5);
    
    const labels = sorted.map(e => e[0]);
    const data = sorted.map(e => e[1]);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant total (MAD)',
                data: data,
                backgroundColor: '#0dcaf0',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toFixed(0) + ' MAD'
                    }
                }
            }
        }
    });
}

// Graphique en barres - Montants par statut
function initAmountByStatusChart() {
    const ctx = document.getElementById('amountByStatusChart');
    if (!ctx) return;
    
    const statusConfig = {
        'valide_manager': { label: 'Validé Manager', color: '#0d6efd' },
        'en_cours_admin': { label: 'En cours Admin', color: '#ffc107' },
        'approuve': { label: 'Approuvé', color: '#198754' },
        'rejetee_admin': { label: 'Rejeté Admin', color: '#dc3545' }
    };
    
    // Grouper montants par statut
    const statusAmounts = {};
    chartData.allNotes.forEach(note => {
        const status = note.statut;
        statusAmounts[status] = (statusAmounts[status] || 0) + parseFloat(note.montant_total);
    });
    
    const labels = [];
    const data = [];
    const colors = [];
    
    Object.keys(statusConfig).forEach(key => {
        const amount = statusAmounts[key] || 0;
        if (amount > 0) {
            labels.push(statusConfig[key].label);
            data.push(amount);
            colors.push(statusConfig[key].color);
        }
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant (MAD)',
                data: data,
                backgroundColor: colors,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toFixed(0) + ' MAD'
                    }
                }
            }
        }
    });
}

// Filtres de tableau
function initTableFilters() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const selectAll = document.getElementById('selectAll');
    const rows = document.querySelectorAll('.note-row');

    searchInput?.addEventListener('input', filterTable);
    statusFilter?.addEventListener('change', filterTable);

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const employe = row.dataset.employe;
            const deplacement = row.dataset.deplacement;
            const status = row.dataset.status;
            
            const matchSearch = !searchTerm || 
                               employe.includes(searchTerm) || 
                               deplacement.includes(searchTerm);
            
            const matchStatus = !selectedStatus || status === selectedStatus;
            
            if (matchSearch && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        const tbody = document.querySelector('#notesTable tbody');
        const emptyRow = tbody.querySelector('.empty-state');
        
        if (visibleCount === 0 && !emptyRow) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-search display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun résultat</h5>
                            <p class="text-muted small">Essayez un autre filtre</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    selectAll?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-select');
        const visibleCheckboxes = Array.from(checkboxes).filter(cb => 
            cb.closest('tr').style.display !== 'none'
        );
        
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
}

function confirmReject(noteId) {
    const motif = prompt('Motif du rejet (obligatoire):');
    if (motif && motif.trim()) {
        window.location.href = `/admin/note/${noteId}/reject?motif=${encodeURIComponent(motif)}`;
        return false;
    }
    return false;
}

function confirmRevoke(noteId) {
    const motif = prompt('Motif de la révocation (obligatoire):');
    if (motif && motif.trim()) {
        window.location.href = `/admin/note/${noteId}/revoke?motif=${encodeURIComponent(motif)}`;
        return false;
    }
    return false;
}

// Export Excel
function exportToExcel() {
    const table = document.getElementById('notesTable');
    const rows = Array.from(table.querySelectorAll('tbody tr.note-row')).filter(row => row.style.display !== 'none');
    
    if (rows.length === 0) {
        alert('Aucune donnée à exporter');
        return;
    }
    
    let csv = 'Employé,Email,Déplacement,Lieu,Manager,Montant,Date,Statut\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const employe = cells[1].querySelector('.fw-semibold').textContent;
        const email = cells[1].querySelector('.text-muted').textContent;
        const deplacement = cells[2].querySelector('.fw-medium').textContent;
        const lieu = cells[2].querySelector('.text-muted').textContent.replace(/\s+/g, ' ').trim();
        const manager = cells[3].textContent.trim();
        const montant = cells[4].textContent.trim();
        const date = cells[5].textContent.trim();
        const statut = cells[6].querySelector('.badge').textContent.trim();
        
        csv += `"${employe}","${email}","${deplacement}","${lieu}","${manager}","${montant}","${date}","${statut}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `notes_frais_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
