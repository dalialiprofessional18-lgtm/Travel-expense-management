<!-- Au début de ta vue, avant le contenu HTML -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<?php $title = "Mes déplacements"; ob_start(); ?>
<!-- Main Content -->
<main class="admin-main">
  <div class="container-fluid p-4 p-lg-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5">
      <div>
        <h1 class="h3 mb-0">Gestion des Déplacements</h1>
        <p class="text-muted mb-0">Gérez vos déplacements et demandes de mission</p>
      </div>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="exportDeplacements()">
          <i class="bi bi-download me-2"></i>Exporter
        </button>
        <a href="/deplacements/create" class="btn btn-primary">
          <i class="bi bi-plus-lg me-2"></i>Nouveau déplacement
        </a>
      </div>
    </div>

    <!-- Stats Widgets -->
    <div class="row g-4 g-lg-5 mb-5">
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-box"></i>
              </div>
              <div>
                <h6 class="mb-1 text-muted">Mes déplacements</h6>
                <?php
                $oldTotal = $total - 1;
                $percentageChange = $oldTotal > 0 ? round((($total - $oldTotal) / $oldTotal) * 100, 1) : 0;
                $isPositive = $percentageChange >= 0;
                ?>
                <h3 class="mb-0"><?= $total ?></h3>
                <?php if ($percentageChange != 0): ?>
                <div class="mb-1">
                  <small class="text-<?= $isPositive ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositive ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChange) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-clock"></i>
              </div>
              <div>
                <h6 class="mb-1 text-muted">En attente</h6>
                <?php
                $oldPending = $pending - 1;
                $percentageChangePending = $oldPending > 0 ? round((($pending - $oldPending) / $oldPending) * 100, 1) : 0;
                $isPositivePending = $percentageChangePending >= 0;
                ?>
                <h3 class="mb-0"><?= $pending ?></h3>
                <?php if ($percentageChangePending != 0): ?>
                <div class="mb-1">
                  <small class="text-<?= $isPositivePending ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositivePending ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChangePending) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                <i class="bi bi-check-circle"></i>
              </div>
              <div>
                <h6 class="mb-1 text-muted">Approuvées</h6>
                <?php
                $oldApproved = $approved - 1;
                $percentageChangeApproved = $oldApproved > 0 ? round((($approved - $oldApproved) / $oldApproved) * 100, 1) : 0;
                $isPositiveApproved = $percentageChangeApproved >= 0;
                ?>
                <h3 class="mb-0"><?= $approved ?></h3>
                <?php if ($percentageChangeApproved != 0): ?>
                <div class="mb-1">
                  <small class="text-<?= $isPositiveApproved ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositiveApproved ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChangeApproved) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                <i class="bi bi-x-circle"></i>
              </div>
              <div>
                <h6 class="mb-1 text-muted">Rejetées</h6>
                <h3 class="mb-0"><?= $rejecter ?></h3>
                <?php
                $oldRejected = $rejecter - 1;
                $percentageChangeRejected = $oldRejected > 0 ? round(((($rejecter - $oldRejected)+0.000001) / $oldRejected +0.000001) * 100, 1) : 0;
                $isPositiveRejected = $percentageChangeRejected >= 0;
                ?>
                <?php if ($percentageChangeRejected != 0): ?>
                <div class="mb-1">
                  <small class="text-<?= $isPositiveRejected ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositiveRejected ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChangeRejected) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 g-lg-5 mb-5">
      <!-- Évolution des Déplacements Chart -->
      <div class="col-lg-8">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-graph-up me-2"></i>Évolution des Déplacements
            </h5>
            <div class="btn-group btn-group-sm" role="group">
              <input type="radio" class="btn-check" name="salesPeriod" id="sales7d" autocomplete="off" checked />
              <label class="btn btn-outline-secondary" for="sales7d">7 Jours</label>
              <input type="radio" class="btn-check" name="salesPeriod" id="sales30d" autocomplete="off" />
              <label class="btn btn-outline-secondary" for="sales30d">30 Jours</label>
              <input type="radio" class="btn-check" name="salesPeriod" id="sales90d" autocomplete="off" />
              <label class="btn btn-outline-secondary" for="sales90d">90 Jours</label>
            </div>
          </div>
          <div class="card-body p-3 p-lg-4">
            <canvas id="salesChart" style="height: 320px"></canvas>
          </div>
        </div>
      </div>

      <!-- Status Distribution -->
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="bi bi-pie-chart me-2"></i>Répartition par Statut
            </h5>
          </div>
          <div class="card-body p-3 p-lg-4">
            <canvas id="statusChart" style="height: 280px"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Table des Déplacements -->
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col">
            <h5 class="card-title mb-0">Liste des Déplacements</h5>
          </div>
          <div class="col-auto">
            <div class="d-flex gap-2">
              <div class="position-relative">
                <input type="search" class="form-control form-control-sm" placeholder="Rechercher..." id="searchInput" style="width: 200px" />
                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"></i>
              </div>
              <select class="form-select form-select-sm" id="statusFilter" style="width: 180px">
                <option value="">Tous les statuts</option>
                <option value="Brouillon">Brouillon</option>
                <option value="Soumis">Soumis</option>
                <option value="Valide_manager">Validé Manager</option>
                <option value="Rejete_manager">Rejeté Manager</option>
                <option value="En_cours_admin">En cours Admin</option>
                <option value="Approuve">Approuvé</option>
                <option value="Rejete_admin">Rejeté Admin</option>
              </select>
              <select class="form-select form-select-sm" id="dateFilter" style="width: 150px">
                <option value="">Toutes les dates</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
                <option value="upcoming">À venir</option>
                <option value="past">Passés</option>
              </select>
              <button class="btn btn-sm btn-outline-secondary" id="resetFilters">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="bulk-actions-bar p-3 bg-light border-bottom d-none" id="bulkActionsBar">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted">
              <span id="selectedCount">0</span> déplacement(s) sélectionné(s)
            </span>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                <i class="bi bi-trash me-1"></i>Supprimer
              </button>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover mb-0" id="deplacementsTable">
            <thead class="table-light">
              <tr>
                <th style="width: 40px">
                  <input type="checkbox" class="form-check-input" id="selectAll" />
                </th>
                <th>Titre</th>
                <th>Lieu</th>
                <th>Dates</th>
                <th>Durée</th>
                <th>Statut</th>
                <th style="width: 120px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent as $d): ?>
              <tr class="deplacement-row" 
                  data-titre="<?= strtolower(htmlspecialchars($d['titre'])) ?>"
                  data-lieu="<?= strtolower(htmlspecialchars($d['lieu'])) ?>"
                  data-statut="<?= htmlspecialchars($d['note_statut']) ?>"
                  data-date-depart="<?= htmlspecialchars($d['date_depart']) ?>"
                  data-date-retour="<?= htmlspecialchars($d['date_retour']) ?>">
                <td>
                  <input type="checkbox" class="form-check-input row-checkbox" value="<?= $d['id'] ?>" />
                </td>
                <td>
                  <div class="fw-medium"><?= htmlspecialchars($d['titre']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($d['description'] ?? '') ?></small>
                </td>
                <td>
                  <span class="badge bg-light text-dark">
                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($d['lieu']) ?>
                  </span>
                </td>
                <td>
                  <div class="small">
                    <div><i class="bi bi-calendar-check me-1"></i><?= date('d/m/Y', strtotime($d['date_depart'])) ?></div>
                    <div><i class="bi bi-calendar-x me-1"></i><?= date('d/m/Y', strtotime($d['date_retour'])) ?></div>
                  </div>
                </td>
                <td>
                  <?php
                  $date1 = new DateTime($d['date_depart']);
                  $date2 = new DateTime($d['date_retour']);
                  $duree = $date1->diff($date2)->days;
                  ?>
                  <span class="badge bg-info"><?= $duree ?> jour<?= $duree > 1 ? 's' : '' ?></span>
                </td>
                <td>
                  <?php
                  $statusColors = [
                    'Brouillon' => 'secondary',
                    'Soumis' => 'info',
                    'Valide_manager' => 'primary',
                    'Rejete_manager' => 'danger',
                    'En_cours_admin' => 'warning',
                    'Approuve' => 'success',
                    'Rejete_admin' => 'danger'
                  ];
                  $color = $statusColors[$d['note_statut']] ?? 'secondary';
                  ?>
                  <span class="badge bg-<?= $color ?>">
                    <?= ucfirst(str_replace('_', ' ', $d['note_statut'])) ?>
                  </span>
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item" href="/notes/<?= $d['id'] ?>">
                          <i class="bi bi-eye me-2"></i>Voir détails
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="/deplacements/edit/<?= $d['id'] ?>">
                          <i class="bi bi-pencil me-2"></i>Modifier
                        </a>
                      </li>
                      <li><hr class="dropdown-divider" /></li>
                      <li>
                        <a class="dropdown-item text-danger" href="/deplacements/delete/<?= $d['id'] ?>" onclick="return deleteDeplacement()">
                          <i class="bi bi-trash me-2"></i>Supprimer
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div id="noResults" class="text-center p-5 d-none">
          <i class="bi bi-inbox fs-1 text-muted"></i>
          <p class="text-muted mt-2">Aucun déplacement trouvé</p>
        </div>

        <div class="d-flex justify-content-between align-items-center p-3" id="paginationContainer">
          <div class="text-muted" id="paginationInfo">
            Affichage de <span id="startItem">1</span> à <span id="endItem">10</span> sur <span id="totalItems">0</span> résultats
          </div>
          <nav>
            <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
// Préparer les données PHP pour JavaScript
const deplacementsData = <?= json_encode(array_map(function($d) {
    return [
        'id' => $d['id'],
        'date_depart' => $d['date_depart'],
        'date_retour' => $d['date_retour'],
        'statut' => $d['note_statut'],
        'lieu' => $d['lieu'],
        'titre' => $d['titre']
    ];
}, $recent)) ?>;

let salesChart = null;
let statusChart = null;

// Initialiser tout au chargement
document.addEventListener('DOMContentLoaded', function() {
    initSalesChart('7d');
    initStatusChart();
    initTableFeatures();
    
    // Gérer les changements de période
    document.querySelectorAll('input[name="salesPeriod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const period = this.id.replace('sales', '');
            initSalesChart(period);
        });
    });
});

// Graphique d'évolution avec Chart.js
function initSalesChart(period) {
    const days = period === '7d' ? 7 : (period === '30d' ? 30 : 90);
    const now = new Date();
    const categories = [];
    const seriesData = {
        nouveaux: [],
        approuves: [],
        rejetes: []
    };
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date(now);
        date.setDate(date.getDate() - i);
        date.setHours(0, 0, 0, 0);
        
        const nextDate = new Date(date);
        nextDate.setDate(nextDate.getDate() + 1);
        
        categories.push(period === '7d' ? 
            date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' }) :
            date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
        );
        
        let nouveauxCount = 0;
        let approuvesCount = 0;
        let rejetesCount = 0;
        
        deplacementsData.forEach(d => {
            const depart = new Date(d.date_depart);
            depart.setHours(0, 0, 0, 0);
            
            if (depart >= date && depart < nextDate) {
                nouveauxCount++;
                
                if (d.statut === 'Approuve') {
                    approuvesCount++;
                }
                if (d.statut === 'Rejete_admin' || d.statut === 'Rejete_manager') {
                    rejetesCount++;
                }
            }
        });
        
        seriesData.nouveaux.push(nouveauxCount);
        seriesData.approuves.push(approuvesCount);
        seriesData.rejetes.push(rejetesCount);
    }
    
    const ctx = document.getElementById('salesChart');
    
    if (salesChart) {
        salesChart.destroy();
    }
    
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: categories,
            datasets: [
                {
                    label: 'Nouveaux déplacements',
                    data: seriesData.nouveaux,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Approuvés',
                    data: seriesData.approuves,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Rejetés',
                    data: seriesData.rejetes,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Graphique de distribution avec Chart.js
function initStatusChart() {
    const statusCounts = {};
    
    deplacementsData.forEach(d => {
        const status = d.statut;
        if (!statusCounts[status]) {
            statusCounts[status] = 0;
        }
        statusCounts[status]++;
    });
    
    const statusLabels = {
        'Brouillon': 'Brouillon',
        'Soumis': 'Soumis',
        'Valide_manager': 'Validé Manager',
        'Rejete_manager': 'Rejeté Manager',
        'En_cours_admin': 'En cours Admin',
        'Approuve': 'Approuvé',
        'Rejete_admin': 'Rejeté Admin'
    };
    
    const colorMap = {
        'Brouillon': '#6c757d',
        'Soumis': '#0dcaf0',
        'Valide_manager': '#0d6efd',
        'Rejete_manager': '#dc3545',
        'En_cours_admin': '#ffc107',
        'Approuve': '#198754',
        'Rejete_admin': '#dc3545'
    };
    
    const labels = [];
    const data = [];
    const colors = [];
    
    Object.keys(statusCounts).forEach(status => {
        if (statusCounts[status] > 0) {
            labels.push(statusLabels[status] || status);
            data.push(statusCounts[status]);
            colors.push(colorMap[status] || '#6c757d');
        }
    });
    
    const ctx = document.getElementById('statusChart');
    
    if (statusChart) {
        statusChart.destroy();
    }
    
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' déplacement' + (context.parsed > 1 ? 's' : '');
                        }
                    }
                }
            }
        }
    });
}

// Initialiser les fonctionnalités du tableau
function initTableFeatures() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const resetFilters = document.getElementById('resetFilters');
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterTable);
    }
    
    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = '';
            dateFilter.value = '';
            filterTable();
        });
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.row-checkbox:not([disabled])').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkActions();
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    updatePagination();
}

// Filtrer le tableau
function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const statusValue = document.getElementById('statusFilter').value;
    const dateValue = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('.deplacement-row');
    let visibleCount = 0;
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    rows.forEach(row => {
        const titre = row.dataset.titre;
        const lieu = row.dataset.lieu;
        const statut = row.dataset.statut;
        const dateDepart = new Date(row.dataset.dateDepart);
        const dateRetour = new Date(row.dataset.dateRetour);
        dateDepart.setHours(0, 0, 0, 0);
        dateRetour.setHours(0, 0, 0, 0);
        
        let visible = true;
        
        if (searchValue && !titre.includes(searchValue) && !lieu.includes(searchValue)) {
            visible = false;
        }
        
        if (statusValue && statut !== statusValue) {
            visible = false;
        }
        
        if (dateValue) {
            const today = new Date(now);
            const weekStart = new Date(now);
            weekStart.setDate(weekStart.getDate() - weekStart.getDay());
            const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            
            switch(dateValue) {
                case 'today':
                    if (dateDepart > today || dateRetour < today) visible = false;
                    break;
                case 'week':
                    if (dateDepart < weekStart) visible = false;
                    break;
                case 'month':
                    if (dateDepart < monthStart) visible = false;
                    break;
                case 'upcoming':
                    if (dateDepart <= now) visible = false;
                    break;
                case 'past':
                    if (dateRetour >= now) visible = false;
                    break;
            }
        }
        
        row.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });
    
    const noResults = document.getElementById('noResults');
    const tableBody = document.querySelector('#deplacementsTable tbody');
    
    if (visibleCount === 0) {
        noResults.classList.remove('d-none');
        tableBody.style.display = 'none';
    } else {
        noResults.classList.add('d-none');
        tableBody.style.display = '';
    }
    
    updatePagination();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkedBoxes.length > 0) {
        bulkActionsBar.classList.remove('d-none');
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActionsBar.classList.add('d-none');
    }
}

function updatePagination() {
    const visibleRows = document.querySelectorAll('.deplacement-row:not([style*="display: none"])');
    const totalItems = visibleRows.length;
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('startItem').textContent = totalItems > 0 ? 1 : 0;
    document.getElementById('endItem').textContent = Math.min(10, totalItems);
}

function exportDeplacements() {
    const rows = Array.from(document.querySelectorAll('.deplacement-row:not([style*="display: none"])'));
    
    if (rows.length === 0) {
        alert('Aucun déplacement à exporter');
        return;
    }
    
    let csv = 'Titre,Lieu,Date Départ,Date Retour,Durée,Statut\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const titre = cells[1].querySelector('.fw-medium').textContent;
        const lieu = cells[2].textContent.trim();
        const dates = cells[3].querySelectorAll('div');
        const dateDepart = dates[0].textContent.replace(/[^\d\/]/g, '');
        const dateRetour = dates[1].textContent.replace(/[^\d\/]/g, '');
        const duree = cells[4].textContent.trim();
        const statut = cells[5].textContent.trim();
        
        csv += `"${titre}","${lieu}","${dateDepart}","${dateRetour}","${duree}","${statut}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'deplacements_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Supprimer un déplacement
function deleteDeplacement() {
    return confirm('Êtes-vous sûr de vouloir supprimer ce déplacement ?');
}

// Suppression groupée
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Veuillez sélectionner au moins un déplacement');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} déplacement(s) ?`)) {
        // Implémenter la logique de suppression ici
        console.log('Suppression de', checkedBoxes.length, 'déplacements');
        
        // Simuler la suppression
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            row.style.opacity = '0.5';
            setTimeout(() => {
                row.remove();
                updatePagination();
            }, 300);
        });
        
        // Réinitialiser la sélection
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }
}

</script>