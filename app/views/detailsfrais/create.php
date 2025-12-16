<?php $title = 'Ajouter un détail – Note #' ?>
<?php ob_start(); ?>

<div class="container-fluid py-4">
    <h2 class="mb-4 text-primary">
        Une note = une facture
    </h2>
    <p class="text-muted mb-4">
        Les factures sont uploadées immédiatement vers Firebase Storage pour une récupération permanente.
    </p>

    <form action="/details/store" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <input type="hidden" name="note_id" value="<?= $note->getId() ?>">

        <!-- اختيار الفئة بالأيقونات -->
        <div class="mb-4">
            <label class="form-label fw-bold">Catégorie de frais *</label>
            <div class="row g-3">
                <?php 
                $iconMap = [
                    'transport_longue_distance' => 'bi-train-front text-primary',
                    'transport_courte_distance' => 'bi-taxi-front text-success',
                    'hebergement'               => 'bi-building text-warning',
                    'repas_jour'                => 'bi-cup-straw text-danger',
                    'repas_pro'                 => 'bi-cup-hot text-info',
                    'autres'                    => 'bi-three-dots text-secondary'
                ];
                foreach ($categories as $c): 
                    $icon = $iconMap[$c->getType()] ?? 'bi-receipt text-muted';
                ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="text-center">
                        <input type="radio" class="btn-check" name="categorie_id" id="cat_<?= $c->getId() ?>" value="<?= $c->getId() ?>" required>
                        <label class="btn btn-outline-primary d-block p-4 rounded-3 shadow-sm" for="cat_<?= $c->getId() ?>">
                            <i class="bi <?= $icon ?> fs-1 d-block mb-2"></i>
                            <span class="small"><?= ucwords(str_replace('_', ' ', $c->getType())) ?></span>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- نوع النقل (إذا كانت الفئة نقل طويل) -->
        <div class="mb-3" id="transport-type-group" style="display:none;">
            <label class="form-label">Type de transport longue distance *</label>
            <select class="form-select" name="transport_type">
                <option value="">Sélectionner...</option>
                <option value="train">Train</option>
                <option value="avion">Avion</option>
                <option value="location_voiture">Location voiture</option>
            </select>
        </div>

        <!-- التاريخ والمبلغ -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Date *</label>
                <input type="date" name="date_frais" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Montant (MAD) *</label>
                <input type="number" step="0.01" name="montant_total" class="form-control form-control-lg" placeholder="0.00" required>
            </div>
        </div>

        <!-- Via Vélocé / Frais personnel -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="via_veloce" id="via_veloce">
                    <label class="form-check-label fw-bold" for="via_veloce">Via VÉLOCE</label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="frais_personnel" id="frais_personnel">
                    <label class="form-check-label" for="frais_personnel">Frais personnel</label>
                </div>
            </div>
        </div>

        <!-- رفع الملف -->
        <div class="mb-4">
            <label class="form-label d-block text-center fw-bold">Justificatif / Facture</label>
            <div class="border border-2 border-dashed rounded-4 p-5 text-center bg-light">
                <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                <p class="text-muted mb-2">Choisir une facture</p>
                <input type="file" name="justificatif" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                <small class="text-muted">PNG, JPG, PDF jusqu'à 10 MB</small>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="text-center">
            <button type="button" class="btn btn-outline-secondary btn-lg px-5" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary btn-lg px-5">
                Ajouter la note de frais
            </button>
        </div>
    </form>
</div>

<script>
// إظهار حقل نوع النقل فقط إذا اختار "Transport longue distance"
document.querySelectorAll('input[name="categorie_id"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const transportGroup = document.getElementById('transport-type-group');
        if (this.value == '1') { // افترض إن id=1 هو transport_longue_distance
            transportGroup.style.display = 'block';
        } else {
            transportGroup.style.display = 'none';
        }
    });
});
</script>

