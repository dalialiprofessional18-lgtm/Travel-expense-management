<?php $title = 'Tableau de bord - Manager'; ?>
<?php ob_start(); ?>
<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- En-tête avec animation -->
        <div class="mb-4 mb-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 animate-fade-in">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        Tableau de bord Manager
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-2"></i>
                        <?= strftime('%A %d %B %Y', time()) ?>
                    </p>
                </div>
                <div>
                    <a href="/deplacements/attribuer" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>
                        Attribuer un déplacement
                    </a>
                </div>
            </div>
        </div>
  <!-- Cartes statistiques (même design que déplacements) -->
        <div class="row g-4 g-lg-4 mb-5">
            <!-- Carte 1: Membres de l'équipe -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Membres de l'équipe</h6>
                                <?php
                                $oldTeamCount = ($teamCount ?? 0) - 1;
                                $percentageTeam = $oldTeamCount > 0 ? round(((($teamCount ?? 0) - $oldTeamCount) / $oldTeamCount) * 100, 1) : 0;
                                $isPositiveTeam = $percentageTeam >= 0;
                                ?>
                                <h3 class="mb-0 fw-bold"><?= $teamCount ?? 0 ?></h3>
                                <?php if ($percentageTeam != 0): ?>
                                <div class="mb-1">
                                    <small class="text-<?= $isPositiveTeam ? 'success' : 'danger' ?>">
                                        <i class="bi bi-arrow-<?= $isPositiveTeam ? 'up' : 'down' ?>"></i>
                                        <?= $isPositiveTeam ? '+' : '' ?><?= abs($percentageTeam) ?>%
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte 2: Demandes en attente -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">En attente</h6>
                                <?php
                                $oldPending = ($pendingTeam ?? 0) - 1;
                                $percentagePending = $oldPending > 0 ? round(((($pendingTeam ?? 0) - $oldPending) / $oldPending) * 100, 1) : 0;
                                $isPositivePending = $percentagePending >= 0;
                                ?>
                                <h3 class="mb-0 fw-bold"><?= $pendingTeam ?? 0 ?></h3>
                                <?php if ($percentagePending != 0): ?>
                                <div class="mb-1">
                                    <small class="text-<?= $isPositivePending ? 'success' : 'danger' ?>">
                                        <i class="bi bi-arrow-<?= $isPositivePending ? 'up' : 'down' ?>"></i>
                                        <?= $isPositivePending ? '+' : '' ?><?= abs($percentagePending) ?>%
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte 3: Demandes approuvées -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Approuvées</h6>
                                <?php
                                $oldApproved = ($approvedTeam ?? 0) - 1;
                                $percentageApproved = $oldApproved > 0 ? round(((($approvedTeam ?? 0) - $oldApproved) / $oldApproved) * 100, 1) : 0;
                                $isPositiveApproved = $percentageApproved >= 0;
                                ?>
                                <h3 class="mb-0 fw-bold"><?= $approvedTeam ?? 0 ?></h3>
                                <?php if ($percentageApproved != 0): ?>
                                <div class="mb-1">
                                    <small class="text-<?= $isPositiveApproved ? 'success' : 'danger' ?>">
                                        <i class="bi bi-arrow-<?= $isPositiveApproved ? 'up' : 'down' ?>"></i>
                                        <?= $isPositiveApproved ? '+' : '' ?><?= abs($percentageApproved) ?>%
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte 4: Montant total -->
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-currency-euro"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">Montant total</h6>
                                <?php
                                $totalMontant = $stats['total_montant'] ?? 0;
                                $oldMontant = $totalMontant - 500;
                                $percentageMontant = $oldMontant > 0 ? round((($totalMontant - $oldMontant) / $oldMontant) * 100, 1) : 0;
                                $isPositiveMontant = $percentageMontant >= 0;
                                ?>
                                <h3 class="mb-0 fw-bold"><?= number_format($totalMontant, 0, ',', ' ') ?> MAD</h3>
                                <?php if ($percentageMontant != 0): ?>
                                <div class="mb-1">
                                    <small class="text-<?= $isPositiveMontant ? 'success' : 'danger' ?>">
                                        <i class="bi bi-arrow-<?= $isPositiveMontant ? 'up' : 'down' ?>"></i>
                                        <?= $isPositiveMontant ? '+' : '' ?><?= abs($percentageMontant) ?>%
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Graphiques principaux -->
        <div class="row g-4 mb-5">
            <!-- Graphique de distribution des statuts -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-pie-chart-fill text-primary me-2"></i>
                            Distribution des statuts
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique des déplacements par mois -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up text-primary me-2"></i>
                            Évolution des déplacements (6 derniers mois)
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section tableau des demandes -->
        <div class="card border-0 shadow-sm animate-fade-in">
            <!-- En-tête du tableau -->
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h3 class="mb-0 fw-bold h5">Liste des Déplacements</h3>
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
                            <option value="approuve">Approuvé</option>
                            <option value="en_attente">En attente</option>
                            <option value="valide_manager">Validé Manager</option>
                            <option value="rejetee">Rejeté</option>
                            <option value="soumis">Soumis</option>
                        </select>
                        <select class="form-select" id="dateFilter" style="width: auto;">
                            <option value="">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                        </select>
                        <button class="btn btn-outline-secondary" onclick="exportTableToExcel()">
                            <i class="bi bi-download me-1"></i>Exporter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Corps du tableau -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 clean-table">
                        <thead>
                            <tr class="border-bottom">
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light" style="width: 50px;">
                                    <input type="checkbox" class="form-check-input">
                                </th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Titre</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Lieu</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Dates</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Durée</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light">Statut</th>
                                <th class="px-4 py-3 text-uppercase small fw-semibold text-muted bg-light text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentTeamRequests)): ?>
                                <?php foreach ($recentTeamRequests as $index => $d): ?>
                                    <?php if ($d['note_statut'] !== 'Brouillon'): ?>
                                        <tr class="border-bottom deployment-row" 
                                            data-title="<?= strtolower(htmlspecialchars($d['titre'])) ?>"
                                            data-employee="<?= strtolower(htmlspecialchars($d['employe_nom'])) ?>"
                                            data-location="<?= strtolower(htmlspecialchars($d['lieu'])) ?>"
                                            data-status="<?= strtolower(trim($d['note_statut'])) ?>"
                                            data-date-start="<?= $d['date_depart'] ?>"
                                            data-date-end="<?= $d['date_retour'] ?>">
                                            <td class="px-4 py-3">
                                                <input type="checkbox" class="form-check-input">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-briefcase text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-semibold"><?= htmlspecialchars($d['titre']) ?></p>
                                                        <small class="text-muted"><?= htmlspecialchars($d['employe_nom']) ?></small>
                                                    </div>
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
                                                        <i class="bi bi-calendar-event me-1"></i>
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
                                                // Configuration complète des statuts avec couleurs distinctes
                                                $statusConfig = [
                                                    'approuve' => ['color' => 'success', 'text' => 'Approuvée', 'icon' => 'check-circle-fill'],
                                                    'valide_manager' => ['color' => 'primary', 'text' => 'Validée Manager', 'icon' => 'shield-check'],
                                                    'en_attente' => ['color' => 'warning', 'text' => 'En attente', 'icon' => 'hourglass-split'],
                                                    'rejetee' => ['color' => 'danger', 'text' => 'Rejetée', 'icon' => 'x-circle-fill'],
                                                    'soumis' => ['color' => 'secondary', 'text' => 'Soumis', 'icon' => 'clock'],
                                                    'brouillon' => ['color' => 'secondary', 'text' => 'Brouillon', 'icon' => 'file-earmark']
                                                ];
                                                
                                                // Normaliser le statut (minuscules, pas d'espaces)
                                                $statutKey = strtolower(trim($d['note_statut']));
                                                $status = $statusConfig[$statutKey] ?? ['color' => 'info', 'text' => ucfirst($d['note_statut']), 'icon' => 'info-circle'];
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
                                                        <li>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="bi bi-pencil me-2"></i>Modifier
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#">
                                                                <i class="bi bi-trash me-2"></i>Supprimer
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucune demande récente</h5>
                                            <p class="text-muted small">Les demandes de votre équipe apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer du tableau avec pagination -->
            <?php if (!empty($recentTeamRequests)): ?>
                <div class="card-footer bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="text-muted small">
                            Affichage de <strong><?= count($recentTeamRequests) ?></strong> demandes
                        </div>
                        <nav aria-label="Pagination">
                            <ul class="pagination mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des couleurs
    const colors = {
        blue: 'rgb(59, 130, 246)',
        orange: 'rgb(251, 146, 60)',
        green: 'rgb(34, 197, 94)',
        purple: 'rgb(168, 85, 247)',
        blueLight: 'rgba(59, 130, 246, 0.1)',
        orangeLight: 'rgba(251, 146, 60, 0.1)',
        greenLight: 'rgba(34, 197, 94, 0.1)',
        purpleLight: 'rgba(168, 85, 247, 0.1)',
    };

    // Données PHP
    const teamCount = <?= $teamCount ?? 0 ?>;
    const pendingTeam = <?= $pendingTeam ?? 0 ?>;
    const approvedTeam = <?= $approvedTeam ?? 0 ?>;
    const totalMontant = <?= $stats['total_montant'] ?? 0 ?>;

    // Mini graphiques sparkline
    function createSparkline(canvasId, data, color) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['', '', '', '', '', '', ''],
                datasets: [{
                    data: data,
                    borderColor: color,
                    backgroundColor: color.replace('rgb', 'rgba').replace(')', ', 0.1)'),
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    }

    // Créer les mini graphiques
    createSparkline('teamChart', [teamCount-3, teamCount-2, teamCount-1, teamCount-2, teamCount, teamCount+1, teamCount], colors.blue);
    createSparkline('pendingChart', [pendingTeam+2, pendingTeam+1, pendingTeam, pendingTeam-1, pendingTeam, pendingTeam+1, pendingTeam], colors.orange);
    createSparkline('approvedChart', [approvedTeam-2, approvedTeam-1, approvedTeam, approvedTeam+1, approvedTeam+2, approvedTeam+3, approvedTeam], colors.green);
    createSparkline('montantChart', [totalMontant*0.8, totalMontant*0.85, totalMontant*0.9, totalMontant*0.95, totalMontant, totalMontant*1.05, totalMontant], colors.purple);

    // Graphique de distribution (Donut)
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Approuvées', 'En attente', 'Rejetées'],
                datasets: [{
                    data: [approvedTeam, pendingTeam, (teamCount - approvedTeam - pendingTeam)],
                    backgroundColor: [colors.green, colors.orange, 'rgb(239, 68, 68)'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    // Graphique d'évolution (Bar Chart)
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx) {
        const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'];
        new Chart(evolutionCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Approuvées',
                        data: [12, 19, 15, 25, 22, approvedTeam],
                        backgroundColor: colors.greenLight,
                        borderColor: colors.green,
                        borderWidth: 2,
                        borderRadius: 8,
                    },
                    {
                        label: 'En attente',
                        data: [8, 11, 9, 12, 10, pendingTeam],
                        backgroundColor: colors.orangeLight,
                        borderColor: colors.orange,
                        borderWidth: 2,
                        borderRadius: 8,
                    },
                    {
                        label: 'Rejetées',
                        data: [2, 3, 1, 4, 2, 3],
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 2,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: true, color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>

<script>
// ========================================
// Filtres et Recherche
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const tableRows = document.querySelectorAll('.deployment-row');
    
    // Fonction de filtrage principale
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value.toLowerCase();
        const selectedDate = dateFilter.value;
        
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const title = row.dataset.title;
            const employee = row.dataset.employee;
            const location = row.dataset.location;
            const status = row.dataset.status;
            const dateStart = new Date(row.dataset.dateStart);
            const dateEnd = new Date(row.dataset.dateEnd);
            
            // Recherche textuelle
            const matchesSearch = !searchTerm || 
                title.includes(searchTerm) || 
                employee.includes(searchTerm) || 
                location.includes(searchTerm);
            
            // Filtre de statut
            const matchesStatus = !selectedStatus || status === selectedStatus;
            
            // Filtre de date
            let matchesDate = true;
            if (selectedDate) {
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                
                switch(selectedDate) {
                    case 'today':
                        matchesDate = dateStart <= today && dateEnd >= today;
                        break;
                    case 'week':
                        const weekStart = new Date(today);
                        weekStart.setDate(today.getDate() - today.getDay());
                        const weekEnd = new Date(weekStart);
                        weekEnd.setDate(weekStart.getDate() + 6);
                        matchesDate = dateStart <= weekEnd && dateEnd >= weekStart;
                        break;
                    case 'month':
                        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                        const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        matchesDate = dateStart <= monthEnd && dateEnd >= monthStart;
                        break;
                }
            }
            
            // Afficher ou cacher la ligne
            if (matchesSearch && matchesStatus && matchesDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Afficher message si aucun résultat
        updateEmptyState(visibleCount);
    }
    
    // Mettre à jour le message "aucun résultat"
    function updateEmptyState(count) {
        const tbody = document.querySelector('.clean-table tbody');
        let emptyRow = tbody.querySelector('.no-results-row');
        
        if (count === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.className = 'no-results-row';
                emptyRow.innerHTML = `
                    <td colspan="7" class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-search display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun résultat trouvé</h5>
                            <p class="text-muted small">Essayez de modifier vos critères de recherche</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
            emptyRow.style.display = '';
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }
    
    // Événements
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    dateFilter.addEventListener('change', filterTable);
    
    // Réinitialiser les filtres
    window.resetFilters = function() {
        searchInput.value = '';
        statusFilter.value = '';
        dateFilter.value = '';
        filterTable();
    };
});

// ========================================
// Export Excel
// ========================================
function exportTableToExcel() {
    const table = document.querySelector('.clean-table');
    const rows = table.querySelectorAll('tr:not([style*="display: none"])');
    
    let csv = [];
    
    // En-têtes
    const headers = [];
    table.querySelectorAll('thead th').forEach((th, index) => {
        if (index > 0) { // Ignorer la colonne checkbox
            headers.push(th.textContent.trim());
        }
    });
    csv.push(headers.join(','));
    
    // Données
    rows.forEach(row => {
        if (row.classList.contains('deployment-row') && row.style.display !== 'none') {
            const cols = row.querySelectorAll('td');
            const rowData = [];
            cols.forEach((col, index) => {
                if (index > 0 && index < cols.length - 1) { // Ignorer checkbox et actions
                    rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
                }
            });
            csv.push(rowData.join(','));
        }
    });
    
    // Télécharger
    const csvContent = csv.join('\n');
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'deplacements_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// ========================================
// Sélection multiple
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.querySelector('.clean-table thead input[type="checkbox"]');
    const rowCheckboxes = document.querySelectorAll('.clean-table tbody input[type="checkbox"]');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(rowCheckboxes).every(cb => 
                cb.checked || cb.closest('tr').style.display === 'none'
            );
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
});
</script>

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

    /* Stats Cards avec couleurs distinctes */
    .stats-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .card-blue { border-left: 4px solid rgb(59, 130, 246); }
    .card-orange { border-left: 4px solid rgb(251, 146, 60); }
    .card-green { border-left: 4px solid rgb(34, 197, 94); }
    .card-purple { border-left: 4px solid rgb(168, 85, 247); }

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

    .mini-chart {
        height: 60px;
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
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
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

    /* Responsive */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 1rem;
        }
        
        .clean-table {
            font-size: 0.85rem;
        }
        
        .mini-chart {
            height: 50px;
        }
    }

    /* Canvas responsive */
    canvas {
        max-height: 300px;
    }

    /* Card hover effects */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }
</style>

