<?php 
$title = 'Gestion utilisateurs';
ob_start();

// Calculer les statistiques
$totalUsers = count($users);
$admins = count(array_filter($users, fn($u) => $u->getRole() === 'admin'));
$managers = count(array_filter($users, fn($u) => $u->getRole() === 'manager'));
$employes = $totalUsers - $admins - $managers;

// Calculer les inscriptions par jour (derniers 30 jours)
$registrationsByDay = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = 0;
    foreach ($users as $u) {
        if (method_exists($u, 'getCreatedAt')) {
            $userDate = date('Y-m-d', strtotime($u->getCreatedAt()));
            if ($userDate === $date) {
                $count++;
            }
        }
    }
    $registrationsByDay[$date] = $count;
}

// Calculer les utilisateurs actifs (exemple: dernière connexion dans les 7 jours)
$activeUsers = 0;
$newThisMonth = 0;
foreach ($users as $u) {
    if (method_exists($u, 'getLastLogin')) {
        $lastLogin = strtotime($u->getLastLogin());
        if ($lastLogin && (time() - $lastLogin) < (7 * 24 * 60 * 60)) {
            $activeUsers++;
        }
    }
    if (method_exists($u, 'getCreatedAt')) {
        $created = strtotime($u->getCreatedAt());
        if ($created && date('Y-m', $created) === date('Y-m')) {
            $newThisMonth++;
        }
    }
}
$activePercentage = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;
?>

<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5 mb-xl-6">
            <div>
                <h1 class="h3 mb-0">Gestion des utilisateurs</h1>
                <p class="text-muted mb-0">Gérer les utilisateurs, rôles et permissions</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='/users/import'">
                    <i class="bi bi-upload me-2"></i>Importer
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="exportUsers()">
                    <i class="bi bi-download me-2"></i>Exporter
                </button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='/users/create'">
                    <i class="bi bi-person-plus me-2"></i>Ajouter un utilisateur
                </button>
            </div>
        </div>

        <!-- User Stats Widgets -->
        <div class="row g-4 g-lg-5 g-xl-6 mb-5 mb-lg-5 mb-xl-6">
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted">Total Utilisateurs</h6>
                                <h3 class="mb-0"><?= $totalUsers ?></h3>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> +12% du mois dernier
                                </small>
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
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted">Utilisateurs Actifs</h6>
                                <h3 class="mb-0"><?= $activeUsers ?></h3>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> +8% de la semaine dernière
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted">Nouveaux ce mois</h6>
                                <h3 class="mb-0"><?= $newThisMonth ?></h3>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> +15% de croissance
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div id="activeUserChart" style="min-height: 40px; width: 50px;"></div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-muted">Taux d'activité</h6>
                                <h3 class="mb-0"><?= round($activePercentage) ?>%</h3>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> Dernières 24h
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Analytics Widgets Row -->
        <div class="row g-4 g-lg-5 g-xl-6 mb-5 mb-lg-5 mb-xl-6">
            <!-- User Growth Chart -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Tendances d'inscription des utilisateurs</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="growthPeriod" id="growth7d" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="growth7d" onclick="updateChartPeriod(7)">7J</label>
                            <input type="radio" class="btn-check" name="growthPeriod" id="growth30d" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary" for="growth30d" onclick="updateChartPeriod(30)">30J</label>
                            <input type="radio" class="btn-check" name="growthPeriod" id="growth90d" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="growth90d" onclick="updateChartPeriod(90)">90J</label>
                        </div>
                    </div>
                    <div class="card-body p-3 p-lg-4">
                        <div id="userGrowthChart" style="width: 100%; min-height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Role & Department Distribution -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribution des utilisateurs</h5>
                    </div>
                    <div class="card-body p-3 p-lg-4">
                        <!-- Role Distribution -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Par Rôle</h6>
                            <div id="roleDistributionChart"></div>
                        </div>
                        
                        <!-- Department Breakdown -->
                        <div>
                            <h6 class="text-muted mb-3">Statistiques des rôles</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Admin</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-danger" style="width: <?= $totalUsers > 0 ? ($admins / $totalUsers * 100) : 0 ?>%"></div>
                                    </div>
                                    <span class="small text-muted"><?= $admins ?></span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Manager</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-warning" style="width: <?= $totalUsers > 0 ? ($managers / $totalUsers * 100) : 0 ?>%"></div>
                                    </div>
                                    <span class="small text-muted"><?= $managers ?></span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Employé</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: <?= $totalUsers > 0 ? ($employes / $totalUsers * 100) : 0 ?>%"></div>
                                    </div>
                                    <span class="small text-muted"><?= $employes ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages de feedback -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">Répertoire des utilisateurs</h5>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <!-- Search -->
                            <div class="position-relative">
                                <input type="search" 
                                       class="form-control form-control-sm" 
                                       placeholder="Rechercher des utilisateurs..."
                                       id="searchInput"
                                       style="width: 200px;">
                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"></i>
                            </div>
                            
                            <!-- Status Filter -->
                            <select class="form-select form-select-sm" 
                                    id="statusFilter"
                                    style="width: 150px;">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="pending">En attente</option>
                            </select>
                            
                            <!-- Role Filter -->
                            <select class="form-select form-select-sm" 
                                    id="roleFilter"
                                    style="width: 150px;">
                                <option value="">Tous les rôles</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="employe">Employé</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th class="sortable" onclick="sortTable('name')">
                                    Nom 
                                    <i class="bi bi-arrow-down-up"></i>
                                </th>
                                <th class="sortable" onclick="sortTable('email')">
                                    Email
                                    <i class="bi bi-arrow-down-up"></i>
                                </th>
                                <th>Rôle</th>
                                <th>Statut</th>
                             
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <p class="mb-0">Aucun utilisateur trouvé</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr data-user-id="<?= $u->getId() ?>">
                                        <td>
                                            <input type="checkbox" class="form-check-input user-select-checkbox" value="<?= $u->getId() ?>">
                                        </td>
                                       
<td>
    <div class="d-flex align-items-center">
        <?php 
        $photo = null;
        
        // Méthode 1: Si c'est un array associatif avec l'ID comme clé
        if (isset($usersPhoto[$u->getId()])) {
            $photoObj = $usersPhoto[$u->getId()];
            if ($photoObj && method_exists($photoObj, 'getAvatarPath')) {
                $photo = $photoObj->getAvatarPath();
            }
        }
        
        // Méthode 2: Si c'est un array indexé numériquement, chercher
        if (!$photo && is_array($usersPhoto)) {
            foreach ($usersPhoto as $userPhoto) {
                if ($userPhoto && 
                    method_exists($userPhoto, 'getId') && 
                    method_exists($userPhoto, 'getAvatarPath') &&
                    $userPhoto->getId() == $u->getId()) {
                    $photo = $userPhoto->getAvatarPath();
                    break;
                }
            }
        }
        
        // Méthode 3: Si User a directement la méthode
        if (!$photo && method_exists($u, 'getAvatarPath')) {
            $photo = $u->getAvatarPath();
        }
        
        // Générer l'initiale pour l'avatar par défaut
        $initiale = strtoupper(substr($u->getNom(), 0, 1));
        ?>

        <?php if (!empty($photo)): ?>
            <img src="<?= htmlspecialchars($photo) ?>" 
                 class="rounded-circle me-2" 
                 width="40" 
                 height="40"
                 alt="<?= htmlspecialchars($u->getNom()) ?>"
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3csvg width=\'32\' height=\'32\' viewBox=\'0 0 32 32\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'%3e%3ccircle cx=\'16\' cy=\'16\' r=\'16\' fill=\'%236366f1\'/%3e%3ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'white\' font-size=\'14\' font-weight=\'600\'%3e<?= $initiale ?>%3c/text%3e%3c/svg%3e';">
        <?php else: ?>
            <img src="data:image/svg+xml,%3csvg width='32' height='32' viewBox='0 0 32 32' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='16' cy='16' r='16' fill='%236366f1'/%3e%3ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='white' font-size='14' font-weight='600'%3e<?= $initiale ?>%3c/text%3e%3c/svg%3e" 
                 class="rounded-circle me-2" 
                 width="40" 
                 height="40"
                 alt="<?= htmlspecialchars($u->getNom()) ?>">
        <?php endif; ?>

        <div>
            <div class="fw-medium"><?= htmlspecialchars($u->getNom()) ?></div>
            <small class="text-muted">ID: <?= $u->getId() ?></small>
        </div>
    </div>
</td>
                                        <td><?= htmlspecialchars($u->getEmail()) ?></td>
                                        <td>
                                            <span class="badge <?php
                                                echo match($u->getRole()) {
                                                    'admin' => 'bg-danger',
                                                    'manager' => 'bg-warning',
                                                    default => 'bg-primary'
                                                };
                                            ?>"><?= htmlspecialchars($u->getRole()) ?></span>
                                        </td>
<td>
    <?php if ($u->isVerified()): ?>
        <span class="badge bg-success">Actif</span>
    <?php else: ?>
        <span class="badge bg-secondary">Inactif</span>
    <?php endif; ?>
</td>

                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="/users/<?= $u->getId() ?>/edit">
                                                        <i class="bi bi-pencil me-2"></i>Modifier
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="/users/<?= $u->getId() ?>">
                                                        <i class="bi bi-eye me-2"></i>Voir le profil
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="/users/<?= $u->getId() ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash me-2"></i>Supprimer
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div class="text-muted">
                        Affichage de 1 à <?= count($users) ?> sur <?= count($users) ?> résultats
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Précédent</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>

<script>
// Données PHP converties en JavaScript
const registrationData = <?= json_encode($registrationsByDay) ?>;
const roleData = {
    admin: <?= $admins ?>,
    manager: <?= $managers ?>,
    employe: <?= $employes ?>
};

let currentPeriod = 30;
let userGrowthChart = null;
let roleChart = null;

// Fonction pour mettre à jour le graphique selon la période
function updateChartPeriod(days) {
    currentPeriod = days;
    
    const allDates = Object.keys(registrationData);
    const filteredDates = allDates.slice(-days);
    const filteredData = filteredDates.map(date => registrationData[date]);
    
    const formattedDates = filteredDates.map(date => {
        const d = new Date(date);
        return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    });
    
    if (userGrowthChart) {
        userGrowthChart.updateOptions({
            xaxis: {
                categories: formattedDates
            }
        });
        userGrowthChart.updateSeries([{
            name: 'Nouvelles inscriptions',
            data: filteredData
        }]);
    }
}

// Initialisation du graphique de croissance (ApexCharts)
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const growthOptions = {
        series: [{
            name: 'Nouvelles inscriptions',
            data: Object.values(registrationData).slice(-30)
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#6366f1'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: Object.keys(registrationData).slice(-30).map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
            }),
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' inscriptions';
                }
            }
        }
    };
    
    userGrowthChart = new ApexCharts(document.querySelector("#userGrowthChart"), growthOptions);
    userGrowthChart.render();
    
    // Role Distribution Chart (Donut)
    const roleOptions = {
        series: [roleData.admin, roleData.manager, roleData.employe],
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'Inter, sans-serif'
        },
        labels: ['Admin', 'Manager', 'Employé'],
        colors: ['#dc2626', '#f59e0b', '#6366f1'],
        legend: {
            show: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            fontWeight: 600,
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function(val, opts) {
                    const total = opts.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    const percent = ((val / total) * 100).toFixed(1);
                    return val + ' (' + percent + '%)';
                }
            }
        }
    };
    
    roleChart = new ApexCharts(document.querySelector("#roleDistributionChart"), roleOptions);
    roleChart.render();
    
    // Mini chart pour le taux d'activité
    const activeRateOptions = {
        series: [<?= round($activePercentage) ?>],
        chart: {
            type: 'radialBar',
            height: 50,
            width: 50,
            sparkline: {
                enabled: true
            }
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '50%'
                },
                dataLabels: {
                    show: false
                },
                track: {
                    background: '#f3f4f6'
                }
            }
        },
        colors: ['#10b981']
    };
    
    const activeChart = new ApexCharts(document.querySelector("#activeUserChart"), activeRateOptions);
    activeChart.render();
});

// Recherche en temps réel
document.getElementById('searchInput')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr[data-user-id]');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filtre par rôle
document.getElementById('roleFilter')?.addEventListener('change', function() {
    const role = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr[data-user-id]');
    
    rows.forEach(row => {
        if (!role) {
            row.style.display = '';
        } else {
            const badge = row.querySelector('.badge');
            const userRole = badge?.textContent.toLowerCase() || '';
            row.style.display = userRole === role ? '' : 'none';
        }
    });
});

// Fonction d'export
function exportUsers() {
    alert('Fonctionnalité d\'export en cours de développement');
    // Implémenter l'export CSV/Excel ici
}

// Select All checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-select-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Initialiser les tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>