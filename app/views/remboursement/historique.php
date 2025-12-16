<?php $title = 'Historique des remboursements'; ?>
<?php ob_start(); ?>

<main class="main-content">
    <div class="container-fluid p-4">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0 fw-bold text-dark">
                    <i class="bi bi-cash-coin text-success me-2"></i>
                    Historique des remboursements
                </h1>
                <p class="text-muted mb-0">Consultez l'historique de vos remboursements</p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="bi bi-printer me-2"></i>Imprimer
            </button>
        </div>

        <!-- Statistiques globales -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total remboursé</p>
                                <h3 class="mb-0 fw-bold text-success">
                                    <?= number_format($stats['total_rembourse'], 2, ',', ' ') ?> MAD
                                </h3>
                            </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="bi bi-currency-euro text-success fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Nombre de remboursements</p>
                                <h3 class="mb-0 fw-bold text-primary">
                                    <?= $stats['nombre_remboursements'] ?>
                                </h3>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="bi bi-receipt text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Dernier remboursement</p>
                                <h3 class="mb-0 fw-bold text-info">
                                    <?= $stats['dernier_remboursement'] ? 
                                        date('d/m/Y', strtotime($stats['dernier_remboursement'])) : 
                                        'Aucun' ?>
                                </h3>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="bi bi-calendar-check text-info fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des remboursements -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul text-success me-2"></i>
                    Détail des remboursements
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($historiques)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4 py-3">Date</th>
                                    <th class="border-0 px-4 py-3">Déplacement</th>
                                    <th class="border-0 px-4 py-3">Lieu</th>
                                    <th class="border-0 px-4 py-3">Période</th>
                                    <th class="border-0 px-4 py-3 text-end">Montant remboursé</th>
                                    <th class="border-0 px-4 py-3 text-center">Lignes</th>
                                    <th class="border-0 px-4 py-3 text-center">Remboursé par</th>
                                    <th class="border-0 px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historiques as $hist): ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <strong><?= date('d/m/Y', strtotime($hist['date_remboursement'])) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= date('H:i', strtotime($hist['date_remboursement'])) ?></small>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold"><?= htmlspecialchars($hist['deplacement_titre']) ?></div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <i class="bi bi-geo-alt text-danger me-1"></i>
                                            <?= htmlspecialchars($hist['deplacement_lieu']) ?>
                                        </td>
                                        <td class="px-4 py-3 small text-muted">
                                            Du <?= date('d/m/Y', strtotime($hist['date_depart'])) ?>
                                            <br>
                                            Au <?= date('d/m/Y', strtotime($hist['date_retour'])) ?>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <span class="badge bg-success fs-6 px-3 py-2">
                                                <?= number_format($hist['montant_rembourse'], 2, ',', ' ') ?> MAD
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge bg-info">
                                                <?= count($hist['details']) ?> ligne<?= count($hist['details']) > 1 ? 's' : '' ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge bg-primary">
                                                <?= htmlspecialchars($hist['admin_nom']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="voirDetails(<?= $hist['id'] ?>)">
                                                <i class="bi bi-eye me-1"></i>Détails
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Ligne de détails extensible -->
                                    <tr id="details-<?= $hist['id'] ?>" style="display: none;" class="bg-light">
                                        <td colspan="8" class="px-4 py-3">
                                            <div class="card border-0">
                                                <div class="card-header bg-white">
                                                    <h6 class="mb-0 fw-bold text-success">
                                                        <i class="bi bi-list-check me-2"></i>
                                                        Détail des lignes remboursées
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-sm mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Catégorie</th>
                                                                <th>Description</th>
                                                                <th>Date</th>
                                                                <th class="text-end">Montant</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($hist['details'] as $detail): ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="badge bg-secondary">
                                                                            <?= htmlspecialchars($detail['categorie']) ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($detail['description']) ?></td>
                                                                    <td class="text-muted small">
                                                                        <?= date('d/m/Y', strtotime($detail['date_frais'])) ?>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <?= number_format($detail['montant_rembourse'], 2, ',', ' ') ?> MAD
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot class="table-light">
                                                            <tr>
                                                                <td colspan="3" class="text-end fw-bold">Total :</td>
                                                                <td class="text-end fw-bold text-success">
                                                                    <?= number_format($hist['montant_rembourse'], 2, ',', ' ') ?> MAD
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-0">Aucun remboursement enregistré</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<style>
@media print {
    .btn, .sidebar, nav { display: none !important; }
    .card { break-inside: avoid; }
}
</style>

<script>
function voirDetails(historiqueId) {
    const detailsRow = document.getElementById(`details-${historiqueId}`);
    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}
</script>

