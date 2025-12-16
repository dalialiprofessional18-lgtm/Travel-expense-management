<?php $title = 'Détail de la demande - Administrateur'; ?>
<?php ob_start(); ?>
<main class="admin-main">
    <div class="container-fluid p-4">
        <!-- En-tête de la page avec breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none"><i class="bi bi-house-door"></i> Accueil</a></li>
                        <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none">Demandes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Détail</li>
                    </ol>
                </nav>
                <h1 class="h2 mb-0 fw-bold text-dark">
                    <i class="bi bi-shield-check text-danger me-2"></i>
                    Détail de la demande
                </h1>
            </div>
            <a href="/admin" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <div class="row g-4">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Carte informations déplacement -->
                <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <!-- Badge de statut flottant -->
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                        <?php
                        $statusConfig = [
                            'soumis' => ['icon' => 'hourglass-split', 'color' => 'warning', 'text' => 'En attente'],
                            'valide_manager' => ['icon' => 'check-circle-fill', 'color' => 'success', 'text' => 'Validée Manager'],
                            'rejetee_manager' => ['icon' => 'x-circle-fill', 'color' => 'danger', 'text' => 'Rejetée Manager'],
                            'en_cours_admin' => ['icon' => 'hourglass-split', 'color' => 'warning', 'text' => 'En cours Admin'],
                            'approuve' => ['icon' => 'shield-check', 'color' => 'primary', 'text' => 'Approuvée Admin'],
                            'rejetee_admin' => ['icon' => 'shield-x', 'color' => 'dark', 'text' => 'Rejetée Admin']
                        ];
                        $status = $statusConfig[$note->getStatut()] ?? $statusConfig['soumis'];
                        ?>
                        <span class="badge bg-<?= $status['color'] ?> fs-6 px-3 py-2 rounded-pill shadow">
                            <i class="bi bi-<?= $status['icon'] ?> me-1"></i><?= $status['text'] ?>
                        </span>
                    </div>

                    <!-- En-tête avec gradient -->
                    <div class="card-header border-0 bg-gradient p-4" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                        <div class="text-white">
                            <h3 class="mb-2 fw-bold">
                                <i class="bi bi-briefcase-fill me-2"></i>
                                <?= htmlspecialchars($deplacement->getTitre()) ?>
                            </h3>
                            <p class="mb-0 opacity-90">
                                <i class="bi bi-person me-1"></i>
                                Soumise par <strong><?= htmlspecialchars($employe->getNom()) ?></strong>
                                <span class="mx-2">•</span>
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= date('d/m/Y à H:i', strtotime($note->getCreatedAt())) ?>
                            </p>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Informations du déplacement -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                            <i class="bi bi-geo-alt-fill text-danger fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Lieu de mission</p>
                                        <h5 class="mb-0 fw-semibold"><?= htmlspecialchars($deplacement->getLieu()) ?></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                            <i class="bi bi-clock-history text-success fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Durée du déplacement</p>
                                        <h5 class="mb-0 fw-semibold">
                                            <?php 
                                            $debut = new DateTime($deplacement->getDateDepart());
                                            $fin = new DateTime($deplacement->getDateRetour());
                                            $duree = $debut->diff($fin)->days + 1;
                                            echo $duree . ' jour' . ($duree > 1 ? 's' : '');
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                            <i class="bi bi-calendar-check text-info fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Date de départ</p>
                                        <h5 class="mb-0 fw-semibold"><?= date('d/m/Y', strtotime($deplacement->getDateDepart())) ?></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                            <i class="bi bi-calendar-x text-warning fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Date de retour</p>
                                        <h5 class="mb-0 fw-semibold"><?= date('d/m/Y', strtotime($deplacement->getDateRetour())) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Montant total - Highlight -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="alert alert-light border-0 shadow-sm mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 small">Montant total des frais</p>
                                            <h3 class="mb-0 fw-bold text-danger">
                                                <?= number_format($note->getMontantTotal(), 2, ',', ' ') ?> MAD
                                            </h3>
                                        </div>
                                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                            <i style="font-size: 2rem;">MAD</i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success border-0 shadow-sm mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 small">Montant à rembourser</p>
                                            <h3 class="mb-0 fw-bold text-success" id="montantRembourserDisplay">
                                                0,00 MAD
                                            </h3>
                                        </div>
                                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                            <i class="bi bi-cash-coin text-success" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte détails des frais -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h4 class="mb-0 fw-bold">
                            <i class="bi bi-receipt text-danger me-2"></i>
                            Détail des frais
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($lignes)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 px-4 py-3">Action</th>
                                            <th class="border-0 px-4 py-3">Catégorie</th>
                                            <th class="border-0 px-4 py-3">Description</th>
                                            <th class="border-0 px-4 py-3">Date</th>
                                            <th class="border-0 px-4 py-3 text-end">Montant Veloce</th>
                                            <th class="border-0 px-4 py-3 text-end">Montant Personnel</th>
                                            <th class="border-0 px-4 py-3 text-end">Total</th>
                                            <th class="border-0 px-4 py-3 text-center">Justificatif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lignes as $ligne): ?>
                                            <tr id="ligne-<?= $ligne->getId() ?>">
                                                <td class="px-4 py-3">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success rounded-pill btn-rembourser"
                                                            data-ligne-id="<?= $ligne->getId() ?>"
                                                            data-montant="<?= $ligne->getMontantTotal() ?>"
                                                            onclick="toggleRemboursement(<?= $ligne->getId() ?>, <?= $ligne->getMontantTotal() ?>)">
                                                        <i class="bi bi-cash-coin me-1"></i>Rembourser
                                                    </button>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="badge rounded-pill" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                                                        <?= htmlspecialchars($ligne->getCategorie()->getType()) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($ligne->getDescription()) ?></p>
                                                </td>
                                                <td class="px-4 py-3 text-muted small">
                                                    <?= date('d/m/Y', strtotime($ligne->getDateFrais())) ?>
                                                </td>
                                                <td class="px-4 py-3 text-end">
                                                    <span class="text-success fw-semibold">
                                                        <?= number_format($ligne->getMontantVeloce(), 2, ',', ' ') ?> MAD
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-end">
                                                    <span class="text-info fw-semibold">
                                                        <?= number_format($ligne->getMontantPersonnel(), 2, ',', ' ') ?> MAD
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-end">
                                                    <span class="fw-bold text-dark">
                                                        <?= number_format($ligne->getMontantTotal(), 2, ',', ' ') ?> MAD
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <?php if ($ligne->getJustificatifPath()): ?>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                                                onclick="viewJustificatif('<?= htmlspecialchars($ligne->getJustificatifPath()) ?>', '<?= htmlspecialchars($ligne->getDescription()) ?>')">
                                                            <i class="bi bi-file-earmark-text me-1"></i>Voir
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Aucun</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="6" class="px-4 py-3 text-end fw-bold fs-5">Total général</td>
                                            <td class="px-4 py-3 text-end fw-bold fs-5 text-danger">
                                                <?= number_format($note->getMontantTotal(), 2, ',', ' ') ?> MAD
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                                <p class="text-muted mt-3 mb-0">Aucune ligne de frais disponible</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions Admin -->
                <?php if (in_array($note->getStatut(), ['valide_manager', 'rejetee_manager', 'en_cours_admin'])): ?>
                    <div class="card border-0 shadow-sm border-danger">
                        <div class="card-header bg-danger bg-opacity-10 border-0 p-4">
                            <h5 class="mb-0 fw-bold text-danger">
                                <i class="bi bi-shield-fill-exclamation me-2"></i>
                                Actions Administrateur
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Info décision manager -->
                            <?php if (in_array($note->getStatut(), ['valide_manager', 'rejetee_manager'])): ?>
                            <div class="alert alert-warning border-0 mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-2">Décision du manager</h6>
                                        <p class="mb-0 small">
                                            Cette note a été <strong><?= $note->getStatut() === 'valide_manager' ? 'validée' : 'rejetée' ?></strong> par le manager. 
                                            Vous pouvez approuver ou rejeter cette décision.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Formulaire principal pour les actions admin -->
                            <form id="adminActionForm" action="/admin/note/<?= $note->getId() ?>/approve" method="POST">
                                <input type="hidden" name="statut" id="adminStatutInput" value="">
                                <input type="hidden" name="montant_rembourser" id="montantRembourserInput" value="0">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Commentaire</label>
                                    <textarea name="commentaire" id="adminCommentaireInput" class="form-control" rows="4" placeholder="Ajoutez un commentaire ou une raison..."></textarea>
                                    <small class="text-muted">
                                        Le commentaire est facultatif pour l'approbation, <strong>obligatoire pour le rejet</strong>.
                                    </small>
                                </div>

                                <!-- Actions principales -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <button type="button" onclick="submitAdminAction('approuve')" class="btn btn-success w-100 shadow-sm">
                                            <i class="bi bi-shield-check me-2"></i>Approuver
                                        </button>
                                    </div>

                                    <div class="col-md-6">
                                        <button type="button" onclick="submitAdminAction('rejetee_admin')" class="btn btn-danger w-100 shadow-sm">
                                            <i class="bi bi-shield-x me-2"></i>Rejeter
                                        </button>
                                    </div>

                                    <?php if ($note->getStatut() !== 'en_cours_admin'): ?>
                                    <div class="col-12">
                                        <hr class="my-3">
                                        <button type="button" onclick="submitAdminAction('en_cours_admin')" class="btn btn-warning w-100 shadow-sm">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Mettre en cours d'examen
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </form>

                            <div class="alert alert-info border-0 mt-4 mb-0">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    Les actions admin sont définitives et notifient automatiquement l'employé et le manager.
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">
                <!-- Carte employé -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="<?= $employe->getAvatarUrl()  ?>" 
                                 class="rounded-circle shadow" 
                                 width="120" 
                                 height="120"
                                 alt="Avatar">
                            <span class="position-absolute bottom-0 end-0 translate-middle badge rounded-pill bg-success border border-3 border-white" style="padding: 0.5rem;">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        </div>
                        
                        <h5 class="mb-1 fw-bold"><?= htmlspecialchars($employe->getNom()) ?></h5>
                        <p class="text-muted mb-3"><?= htmlspecialchars($employe->getEmail()) ?></p>
                        
                        <div class="d-flex gap-2 justify-content-center mb-4">
                            <a href="mailto:<?= htmlspecialchars($employe->getEmail()) ?>" class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="bi bi-envelope me-1"></i>Email
                            </a>
                            <a href="/messagerie/conversation/<?= $conversationId ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-chat-dots"></i>Message
                            </a>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-muted mb-3 fw-semibold">Statistiques</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <div class="text-danger fw-bold fs-4 mb-1"><?= $stats['mois'] ?? 0 ?></div>
                                    <div class="text-muted small">Ce mois</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <div class="text-success fw-bold fs-4 mb-1"><?= $stats['approuvees'] ?? 0 ?></div>
                                    <div class="text-muted small">Approuvées</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-light rounded-3 p-3">
                                    <div class="text-info fw-bold fs-4 mb-1">
                                        <?= number_format($stats['total_montant'] ?? 0, 2, ',', ' ') ?> MAD
                                    </div>
                                    <div class="text-muted small">Total remboursé</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte historique -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-clock-history text-danger me-2"></i>
                            Historique
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            <div class="timeline-item pb-3">
                                <div class="d-flex align-items-start">
                                    <div class="timeline-icon bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-plus-circle text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold small">Demande créée</p>
                                        <p class="text-muted small mb-0"><?= date('d/m/Y H:i', strtotime($note->getCreatedAt())) ?></p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($note->getStatut() !== 'soumis'): ?>
                                <div class="timeline-item">
                                    <div class="d-flex align-items-start">
                                        <div class="timeline-icon bg-<?= in_array($note->getStatut(), ['valide_manager', 'approuve']) ? 'success' : 'danger' ?> bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-<?= in_array($note->getStatut(), ['valide_manager', 'approuve']) ? 'check-circle' : 'x-circle' ?> text-<?= in_array($note->getStatut(), ['valide_manager', 'approuve']) ? 'success' : 'danger' ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-semibold small"><?= $status['text'] ?></p>
                                            <p class="text-muted small mb-0"><?= date('d/m/Y H:i', strtotime($note->getUpdatedAt())) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal pour visualiser le justificatif -->
<div class="modal fade" id="justificatifModal" tabindex="-1" aria-labelledby="justificatifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                <h5 class="modal-title fw-bold" id="justificatifModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Justificatif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 600px;">
                <div id="justificatifLoader" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted mt-3">Chargement du justificatif...</p>
                </div>
                <iframe id="justificatifIframe" 
                        style="width: 100%; height: 600px; border: none; display: none;"
                        title="Justificatif"></iframe>
            </div>
            <div class="modal-footer border-0 bg-light">
                <a id="downloadJustificatif" href="#" class="btn btn-danger" download>
                    <i class="bi bi-download me-2"></i>Télécharger
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Smooth animations */
    .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Timeline styles */
    .timeline-item {
        position: relative;
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        width: 2px;
        height: calc(100% - 20px);
        background: linear-gradient(to bottom, #e5e7eb, transparent);
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Button hover effects */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Table hover effect */
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(220, 53, 69, 0.05);
        transform: scale(1.01);
    }

    /* Badge animation */
    .badge {
        animation: fadeInDown 0.5s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modal animation */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    /* Loader animation */
    #justificatifLoader {
        transition: opacity 0.3s ease;
    }

    /* Style pour les lignes remboursées */
    tr.remboursee {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    /* Animation pulse pour le montant */
    .pulse {
        animation: pulse 0.3s ease-in-out;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    /* Animation pour le bouton rembourser */
    .btn-rembourser.active {
        animation: btnPulse 0.5s ease;
    }
    
    @keyframes btnPulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
</style>

<script>
// Système de gestion des remboursements
let montantTotal = 0;
let lignesRemboursees = new Set();

function toggleRemboursement(ligneId, montant) {
    const btn = document.querySelector(`button[data-ligne-id="${ligneId}"]`);
    const ligne = document.getElementById(`ligne-${ligneId}`);
    
    if (lignesRemboursees.has(ligneId)) {
        // Annuler le remboursement
        lignesRemboursees.delete(ligneId);
        montantTotal -= parseFloat(montant);
        btn.classList.remove('btn-danger', 'active');
        btn.classList.add('btn-success');
        btn.innerHTML = '<i class="bi bi-cash-coin me-1"></i>Rembourser';
        ligne.classList.remove('remboursee');
    } else {
        // Ajouter au remboursement
        lignesRemboursees.add(ligneId);
        montantTotal += parseFloat(montant);
        btn.classList.remove('btn-success');
        btn.classList.add('btn-danger', 'active');
        btn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Annuler';
        ligne.classList.add('remboursee');
    }
    
    // Mettre à jour l'affichage
    updateMontantDisplay();
}

function updateMontantDisplay() {
    const displayElement = document.getElementById('montantRembourserDisplay');
    const inputElement = document.getElementById('montantRembourserInput');
    
    displayElement.textContent = montantTotal.toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' MAD';
    
    inputElement.value = montantTotal.toFixed(2);
    
    // Animation de pulse
    displayElement.classList.add('pulse');
    setTimeout(() => displayElement.classList.remove('pulse'), 300);
}

function submitAdminAction(statut) {
    const commentaire = document.getElementById('adminCommentaireInput').value.trim();
    
    // Validation : commentaire obligatoire pour le rejet
    if (statut === 'rejetee_admin' && commentaire === '') {
        alert('⚠️ Veuillez fournir une raison pour le rejet.');
        document.getElementById('adminCommentaireInput').focus();
        return;
    }
    
    // Messages de confirmation selon l'action
    let confirmMessage = '';
    
    switch(statut) {
        case 'approuve':
            if (montantTotal > 0) {
                confirmMessage = `Confirmer l'approbation finale de cette note de frais ?\n\nMontant à rembourser : ${montantTotal.toFixed(2)} MAD`;
            } else {
                confirmMessage = 'Confirmer l\'approbation finale de cette note de frais ?';
            }
            break;
        case 'rejetee_admin':
            confirmMessage = 'Confirmer le rejet définitif de cette note de frais ?';
            break;
        case 'en_cours_admin':
            confirmMessage = 'Mettre cette note en cours d\'examen administratif ?';
            break;
    }
    
    if (confirm(confirmMessage)) {
        // Définir le statut dans le champ caché
        document.getElementById('adminStatutInput').value = statut;
        
        // Soumettre le formulaire directement
        document.getElementById('adminActionForm').submit();
    }
}

function viewJustificatif(filePath, description) {
    // Nettoyer le chemin du fichier
    filePath = filePath.replace(/^\.\//, '');
    
    // Obtenir l'URL complète du fichier
    const fileUrl = '/' + filePath;
    
    // Mettre à jour le titre de la modal
    document.getElementById('justificatifModalLabel').innerHTML = 
        '<i class="bi bi-file-earmark-text me-2"></i>Justificatif - ' + description;
    
    // Afficher le loader et cacher l'iframe
    const loader = document.getElementById('justificatifLoader');
    const iframe = document.getElementById('justificatifIframe');
    loader.style.display = 'block';
    iframe.style.display = 'none';
    
    // Mettre à jour le lien de téléchargement
    const downloadLink = document.getElementById('downloadJustificatif');
    downloadLink.href = fileUrl;
    downloadLink.download = filePath;
    
    // Ouvrir la modal
    const modal = new bootstrap.Modal(document.getElementById('justificatifModal'));
    modal.show();
    
    // Charger le fichier dans l'iframe
    iframe.onload = function() {
        loader.style.display = 'none';
        iframe.style.display = 'block';
    };
    
    iframe.onerror = function() {
        loader.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Erreur lors du chargement du fichier</div>';
    };
    
    // Déterminer le type de fichier
    const extension = filePath.split('.').pop().toLowerCase();
    
    if (extension === 'pdf') {
        iframe.src = fileUrl;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
        // Pour les images
        iframe.srcdoc = `
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body {
                        margin: 0;
                        padding: 20px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        background: #f8f9fa;
                    }
                    img {
                        max-width: 100%;
                        max-height: 100%;
                        object-fit: contain;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-radius: 8px;
                    }
                </style>
            </head>
            <body>
                <img src="${fileUrl}" alt="Justificatif" />
            </body>
            </html>
        `;
    } else {
        iframe.src = fileUrl;
    }
}

// Réinitialiser l'iframe quand la modal se ferme
document.getElementById('justificatifModal')?.addEventListener('hidden.bs.modal', function () {
    const iframe = document.getElementById('justificatifIframe');
    iframe.src = '';
    iframe.srcdoc = '';
});
</script>
