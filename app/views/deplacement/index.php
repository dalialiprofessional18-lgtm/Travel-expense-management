<?php
use App\Helpers\Auth;
?>
<?php $title = 'Déplacements';
ob_start();
?>

<main class="admin-main">
      <div class="container-fluid p-4 p-lg-5">


    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mb-lg-5 gap-3">
      <div>
        <h1 class="h2 mb-1 fw-bold">Gestion des Déplacements</h1>
        <p class="text-muted mb-0">Gérez vos déplacements et demandes de mission</p>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="exportDeplacements()">
          <i class="bi bi-download"></i>
          <span class="d-none d-sm-inline">Exporter</span>
        </button>
        <?php if (Auth::id() == $owner_id): ?>
          <a href="/deplacements/create" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Nouveau déplacement</span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 g-lg-5 mb-5">
      <!-- Total Déplacements -->
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card border-0 shadow-sm h-100">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-airplane-fill fs-4"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 text-muted small">Mes déplacements</h6>
                <?php
                $totalDeplacements = count($deplacements);
                $oldTotal = max(0, $totalDeplacements - 1);
                $percentageChange = $oldTotal > 0 ? round((($totalDeplacements - $oldTotal) / $oldTotal) * 100, 1) : 0;
                $isPositive = $percentageChange >= 0;
                ?>
                <h3 class="mb-0 fw-bold"><?= $totalDeplacements ?></h3>
                <?php if ($percentageChange != 0): ?>
                <div class="mt-1">
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

      <!-- En cours -->
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card border-0 shadow-sm h-100">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-success bg-opacity-10 text-success me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-play-circle-fill fs-4"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 text-muted small">En cours</h6>
                <?php
                $enCours = 0;
                foreach ($deplacements as $d) {
                  if (strtotime($d->getDateDepart()) <= time() && strtotime($d->getDateRetour()) >= time()) {
                    $enCours++;
                  }
                }
                $oldEnCours = max(0, $enCours - 1);
                $percentageChangeEnCours = $oldEnCours > 0 ? round((($enCours - $oldEnCours) / $oldEnCours) * 100, 1) : 0;
                $isPositiveEnCours = $percentageChangeEnCours >= 0;
                ?>
                <h3 class="mb-0 fw-bold"><?= $enCours ?></h3>
                <?php if ($percentageChangeEnCours != 0): ?>
                <div class="mt-1">
                  <small class="text-<?= $isPositiveEnCours ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositiveEnCours ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChangeEnCours) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- À venir -->
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card border-0 shadow-sm h-100">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-info bg-opacity-10 text-info me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-calendar-event-fill fs-4"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 text-muted small">À venir</h6>
                <?php
                $aVenir = 0;
                foreach ($deplacements as $d) {
                  if (strtotime($d->getDateDepart()) > time()) {
                    $aVenir++;
                  }
                }
                $oldAVenir = max(0, $aVenir - 1);
                $percentageChangeAVenir = $oldAVenir > 0 ? round((($aVenir - $oldAVenir) / $oldAVenir) * 100, 1) : 0;
                $isPositiveAVenir = $percentageChangeAVenir >= 0;
                ?>
                <h3 class="mb-0 fw-bold"><?= $aVenir ?></h3>
                <?php if ($percentageChangeAVenir != 0): ?>
                <div class="mt-1">
                  <small class="text-<?= $isPositiveAVenir ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositiveAVenir ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChangeAVenir) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Terminés -->
      <div class="col-xl-3 col-lg-6">
        <div class="card stats-card border-0 shadow-sm h-100">
          <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center">
              <div class="stats-icon bg-secondary bg-opacity-10 text-secondary me-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="bi bi-check-circle-fill fs-4"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 text-muted small">Terminés</h6>
                <?php
                $termines = 0;
                foreach ($deplacements as $d) {
                  if (strtotime($d->getDateRetour()) < time()) {
                    $termines++;
                  }
                }
                $oldTermines = max(0, $termines - 1);
                $percentageChangeTermines = $oldTermines > 0 ? round((($termines - $oldTermines) / $oldTermines) * 100, 1) : 0;
                $isPositiveTermines = $percentageChangeTermines >= 0;
                ?>
                <h3 class="mb-0 fw-bold"><?= $termines ?></h3>
                <?php if ($percentageChangeTermines != 0): ?>
                <div class="mt-1">
                  <small class="text-<?= $isPositiveTermines ? 'success' : 'danger' ?>">
                    <i class="bi bi-arrow-<?= $isPositiveTermines ? 'up' : 'down' ?>"></i>
                    +<?= abs($percentageChangeTermines) ?>%
                  </small>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Déplacements Cards -->
    <?php if (empty($deplacements)): ?>
      <div class="text-center py-5">
        <div class="mb-4">
          <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
        </div>
        <h4 class="text-muted mb-3">Aucun déplacement</h4>
        <p class="text-muted mb-4">Commencez par créer votre premier déplacement</p>
        <?php if (Auth::id() == $owner_id): ?>
          <a href="/deplacements/create" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle-fill me-2"></i>Créer un déplacement
          </a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($deplacements as $d):
          // Déterminer le statut
          $dateDepart = strtotime($d->getDateDepart());
          $dateRetour = strtotime($d->getDateRetour());
          $now = time();
          
          if ($dateDepart > $now) {
            $statut = 'À venir';
            $statutClass = 'bg-info';
            $statutIcon = 'bi-calendar-event';
          } elseif ($dateRetour < $now) {
            $statut = 'Terminé';
            $statutClass = 'bg-secondary';
            $statutIcon = 'bi-check-circle-fill';
          } else {
            $statut = 'En cours';
            $statutClass = 'bg-success';
            $statutIcon = 'bi-play-circle-fill';
          }
          
          // Calculer la durée
          $duree = ceil(($dateRetour - $dateDepart) / 86400);
        ?>
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card border shadow-sm h-100 position-relative overflow-hidden hover-lift">
              <!-- Status Badge -->
              <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                <span class="badge <?= $statutClass ?> rounded-pill px-3 py-2 shadow-sm">
                  <i class="bi <?= $statutIcon ?> me-1"></i><?= $statut ?>
                </span>
              </div>

              <!-- Card Header with Gradient -->
              <div class="card-header border-0 bg-primary bg-gradient text-white py-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="bg-white bg-opacity-25 rounded-3 p-3 flex-shrink-0">
                    <i class="bi bi-geo-alt-fill fs-3"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="card-title mb-1 fw-bold text-white">
                      <?= htmlspecialchars($d->getTitre()) ?>
                    </h5>
                    <small class="text-white-50">
                      <i class="bi bi-clock me-1"></i><?= $duree ?> jour<?= $duree > 1 ? 's' : '' ?>
                    </small>
                  </div>
                </div>
              </div>

              <!-- Card Body -->
              <div class="card-body bg-body">
                <!-- Lieu -->
                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2 text-muted mb-1">
                    <i class="bi bi-pin-map-fill text-danger"></i>
                    <small class="text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Destination</small>
                  </div>
                  <p class="mb-0 fw-semibold">
                    <?= htmlspecialchars($d->getLieu()) ?>
                  </p>
                </div>

                <!-- Dates -->
                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2 text-muted mb-2">
                    <i class="bi bi-calendar-range-fill text-primary"></i>
                    <small class="text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Période</small>
                  </div>
                  <div class="d-flex align-items-center gap-2 small flex-wrap">
                    <span class="badge bg-body-secondary text-body border px-3 py-2">
                      <i class="bi bi-box-arrow-right text-success me-1"></i>
                      <?= date('d/m/Y', $dateDepart) ?>
                    </span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-body-secondary text-body border px-3 py-2">
                      <i class="bi bi-box-arrow-left text-danger me-1"></i>
                      <?= date('d/m/Y', $dateRetour) ?>
                    </span>
                  </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="border-top pt-3 mt-3">
                  <div class="row g-2 text-center">
                    <div class="col-6">
                      <div class="bg-body-secondary rounded-3 p-2">
                        <i class="bi bi-receipt-cutoff text-primary d-block fs-4 mb-1"></i>
                        <small class="text-muted d-block">Notes de frais</small>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="bg-body-secondary rounded-3 p-2">
                        <i class="bi bi-file-earmark-text text-info d-block fs-4 mb-1"></i>
                        <small class="text-muted d-block">Documents</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Card Footer -->
              <div class="card-footer bg-body border-top pt-3 pb-3">
                <div class="d-grid gap-2">
                  <a href="/notes/<?= $d->getId() ?>" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-receipt"></i>
                    <span>Voir la demande</span>
                  </a>
      <a  class="btn btn-primary btn-sm d-flex align-items-center justify-content-center gap-2" href="/deplacements/<?= $d->getId() ?>/map">
        <i class="bi bi-map me-2"></i>Voir la carte
      </a>
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="/historique/deplacement/<?= $d->getId() ?>" class="btn btn-outline-secondary flex-fill">
                      <i class="bi bi-clock-history me-1"></i>Historique
                    </a>
                    <?php if (Auth::id() == $owner_id): ?>
                      <a href="/deplacements/edit/<?= $d->getId() ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square"></i>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<style>
/* Stats Card Animation */
.stats-card {
  transition: all 0.3s ease;
  background-color: var(--bs-card-bg);
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

/* Dark mode enhancement for stats cards */
[data-bs-theme="dark"] .stats-card:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4) !important;
}

.stats-icon {
  transition: all 0.3s ease;
}

.stats-card:hover .stats-icon {
  transform: scale(1.1);
}

/* Hover lift for deployment cards */
.hover-lift {
  transition: all 0.3s ease-in-out;
}

.hover-lift:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}

/* Dark mode enhancement for deployment cards */
[data-bs-theme="dark"] .hover-lift:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.5) !important;
}

/* Badge animation */
.badge {
  transition: all 0.2s ease;
}

.hover-lift:hover .badge {
  transform: scale(1.05);
}

/* Button effects */
.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

/* Dark mode specific adjustments */
[data-bs-theme="dark"] .card {
  background-color: var(--bs-dark-bg-subtle);
}

[data-bs-theme="dark"] .bg-body-secondary {
  background-color: var(--bs-secondary-bg) !important;
}

/* Ensure gradient header stays visible in dark mode */
.card-header.bg-gradient {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Border visibility in dark mode */
[data-bs-theme="dark"] .card {
  border-color: var(--bs-border-color) !important;
}
</style>

