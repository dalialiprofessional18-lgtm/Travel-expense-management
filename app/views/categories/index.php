<?php
// app/Views/admin/categories/index.php
?>
<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5 mb-xl-6">
            <div>
                <h1 class="h3 mb-0">Gestion des Catégories</h1>
                <p class="text-muted mb-0">Gérer les catégories de frais et sous-catégories</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="exportCategories()">
                    <i class="bi bi-download me-2"></i>Exporter
                </button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='/admin/categories/create'">
                    <i class="bi bi-plus-lg me-2"></i>Ajouter une catégorie
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 g-lg-5 g-xl-6 mb-5 mb-lg-5 mb-xl-6">
            <?php 
            $totalCategories = count($categories);
            $totalSubcategories = 0;
            $categoriesWithSub = 0;
            foreach ($categories as $cat) {
                $subcats = array_filter(array_map('trim', explode(',', $cat->getDescription() ?? '')));
                $totalSubcategories += count($subcats);
                if (count($subcats) > 0) $categoriesWithSub++;
            }
            $avgSubPerCat = $totalCategories > 0 ? round($totalSubcategories / $totalCategories, 1) : 0;
            ?>
            
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted">Total Catégories</h6>
                                <h3 class="mb-0"><?= $totalCategories ?></h3>
                                <small class="text-success">
                                    <i class="bi bi-check-circle"></i> Actives
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
                                <i class="bi bi-tags-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted">Sous-catégories</h6>
                                <h3 class="mb-0"><?= $totalSubcategories ?></h3>
                                <small class="text-muted">
                                    <i class="bi bi-diagram-3"></i> Total définies
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
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-muted">Catégories complètes</h6>
                                <h3 class="mb-0"><?= $categoriesWithSub ?></h3>
                                <small class="text-muted">
                                    <i class="bi bi-percent"></i> <?= $totalCategories > 0 ? round(($categoriesWithSub / $totalCategories) * 100) : 0 ?>% du total
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
                            <div id="avgSubChart" style="min-height: 40px; width: 50px;"></div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-muted">Moyenne</h6>
                                <h3 class="mb-0"><?= $avgSubPerCat ?></h3>
                                <small class="text-muted">
                                    <i class="bi bi-graph-up"></i> Sous-cat/catégorie
                                </small>
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

        <!-- Categories Table -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">Répertoire des catégories</h5>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <!-- Search -->
                            <div class="position-relative">
                                <input type="search" 
                                       class="form-control form-control-sm" 
                                       placeholder="Rechercher une catégorie..."
                                       id="searchInput"
                                       style="width: 250px;">
                                <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted"></i>
                            </div>
                            
                            <!-- Sort Filter -->
                            <select class="form-select form-select-sm" 
                                    id="sortFilter"
                                    onchange="sortCategories(this.value)"
                                    style="width: 180px;">
                                <option value="type-asc">Type (A-Z)</option>
                                <option value="type-desc">Type (Z-A)</option>
                                <option value="sub-desc">Plus de sous-cat</option>
                                <option value="sub-asc">Moins de sous-cat</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="categoriesTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Type de catégorie</th>
                                <th>Sous-catégories</th>
                                <th>Nombre</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <p class="mb-2">Aucune catégorie trouvée</p>
                                        <button class="btn btn-primary btn-sm" onclick="window.location.href='/admin/categories/create'">
                                            <i class="bi bi-plus-lg me-1"></i> Créer une catégorie
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php 
                                    $subcats = array_filter(array_map('trim', explode(',', $cat->getDescription() ?? '')));
                                    $subCount = count($subcats);
                                    ?>
                                    <tr data-category-id="<?= $cat->getId() ?>" data-subcount="<?= $subCount ?>">
                                        <td>
                                            <input type="checkbox" class="form-check-input category-select-checkbox" value="<?= $cat->getId() ?>">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape bg-gradient-primary shadow text-center border-radius-md me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-tag-fill text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium"><?= htmlspecialchars($cat->getType()) ?></div>
                                                    <small class="text-muted">ID: <?= $cat->getId() ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="subcategories-wrapper">
                                                <?php 
                                                $displayCount = 0;
                                                foreach ($subcats as $subcat): 
                                                    if (empty($subcat)) continue;
                                                    $displayCount++;
                                                    if ($displayCount <= 4):
                                                ?>
                                                    <span class="subcategory-pill">
                                                        <i class="bi bi-tag-fill me-1"></i>
                                                        <?= htmlspecialchars($subcat) ?>
                                                    </span>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                if ($subCount > 4):
                                                ?>
                                                    <span class="subcategory-pill more-pill">
                                                        <i class="bi bi-three-dots me-1"></i>
                                                        +<?= $subCount - 4 ?> autres
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($subCount === 0): ?>
                                                    <span class="text-muted small fst-italic">
                                                        <i class="bi bi-dash-circle me-1"></i>Aucune sous-catégorie
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $subCount > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $subCount ?> sous-cat
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="/admin/categories/edit/<?= $cat->getId() ?>">
                                                            <i class="bi bi-pencil me-2"></i>Modifier
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" 
                                                           href="/admin/categories/delete/<?= $cat->getId() ?>"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                            <i class="bi bi-trash me-2"></i>Supprimer
                                                        </a>
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
                <?php if (!empty($categories)): ?>
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div class="text-muted">
                        Affichage de 1 à <?= count($categories) ?> sur <?= count($categories) ?> résultats
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
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<style>
.stats-card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

.stats-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
}

.icon-shape {
    border-radius: 10px;
}

.badge {
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

tr {
    transition: all 0.2s ease;
}

tr:hover {
    background-color: rgba(99, 102, 241, 0.05);
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #6c757d;
}

/* ============================================
   DESIGN ÉLÉGANT DES SOUS-CATÉGORIES
   ============================================ */

.subcategories-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 8px 0;
}

/* Mode Light (par défaut) - Background Noir */
.subcategory-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 4px;
    background: linear-gradient(135deg, #567fa7ff 0%, #486aaaff 100%);
    color: #ffffff;
    border-radius: 20px;
    font-size: 0.5rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.subcategory-pill::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.5s;
}

.subcategory-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
}

.subcategory-pill:hover::before {
    left: 100%;
}

.subcategory-pill i {
    font-size: 0.75rem;
    opacity: 0.9;
}

.subcategory-pill.more-pill {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-weight: 600;
}

.subcategory-pill.more-pill:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
    transform: translateY(-2px) scale(1.02);
}

/* Mode Dark - Background Blanc */
[data-bs-theme="dark"] .subcategory-pill,
.dark-mode .subcategory-pill,
body.dark .subcategory-pill {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    color: #1a1a1a;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
}

[data-bs-theme="dark"] .subcategory-pill::before,
.dark-mode .subcategory-pill::before,
body.dark .subcategory-pill::before {
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.05), transparent);
}

[data-bs-theme="dark"] .subcategory-pill:hover,
.dark-mode .subcategory-pill:hover,
body.dark .subcategory-pill:hover {
    background: linear-gradient(135deg, #ffffff 0%, #f1f3f5 100%);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
}

[data-bs-theme="dark"] .subcategory-pill.more-pill,
.dark-mode .subcategory-pill.more-pill,
body.dark .subcategory-pill.more-pill {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Animation d'apparition */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.subcategory-pill {
    animation: fadeInUp 0.4s ease-out;
    animation-fill-mode: both;
}

.subcategory-pill:nth-child(1) { animation-delay: 0.05s; }
.subcategory-pill:nth-child(2) { animation-delay: 0.1s; }
.subcategory-pill:nth-child(3) { animation-delay: 0.15s; }
.subcategory-pill:nth-child(4) { animation-delay: 0.2s; }
.subcategory-pill:nth-child(5) { animation-delay: 0.25s; }

/* Responsive */
@media (max-width: 768px) {
    .subcategory-pill {
        padding: 6px 12px;
        font-size: 0.75rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mini chart pour la moyenne
    const avgChartOptions = {
        series: [<?= $avgSubPerCat ?>],
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
        colors: ['#6366f1']
    };
    
    const avgChart = new ApexCharts(document.querySelector("#avgSubChart"), avgChartOptions);
    avgChart.render();
});

// Recherche en temps réel
document.getElementById('searchInput')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#categoriesTable tbody tr[data-category-id]');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Tri des catégories
function sortCategories(sortType) {
    const tbody = document.querySelector('#categoriesTable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-category-id]'));
    
    rows.sort((a, b) => {
        const typeA = a.querySelector('.fw-medium').textContent.toLowerCase();
        const typeB = b.querySelector('.fw-medium').textContent.toLowerCase();
        const countA = parseInt(a.dataset.subcount);
        const countB = parseInt(b.dataset.subcount);
        
        switch(sortType) {
            case 'type-asc':
                return typeA.localeCompare(typeB);
            case 'type-desc':
                return typeB.localeCompare(typeA);
            case 'sub-desc':
                return countB - countA;
            case 'sub-asc':
                return countA - countB;
            default:
                return 0;
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Select All checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.category-select-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Fonction d'export
function exportCategories() {
    alert('Fonctionnalité d\'export en cours de développement');
}

// Initialiser les tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>