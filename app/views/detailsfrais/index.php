<?php $title = 'Frais Prévisionnels — ' . htmlspecialchars(isset($deplacementTitre)); ?>
<?php ob_start(); ?>

<main class="admin-main">
  <div class="container-fluid p-4 p-lg-5">
    
    <!-- 🎨 HEADER MODERNE -->
    <div class="row align-items-center mb-5">
      <div class="col-md-8">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Accueil</a></li>
            <li class="breadcrumb-item"><a href="/deplacements" class="text-decoration-none">Déplacements</a></li>
            <li class="breadcrumb-item active">Frais Prévisionnels</li>
          </ol>
        </nav>
        <h1 class="display-6 fw-bold mb-2">Gestion des Frais Prévisionnels</h1>
        <p class="text-muted lead mb-0">
          <i class="bi bi-calendar-check me-2"></i>
          Gérez vos déplacements et demandes de mission
        </p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
          <button type="button" class="btn btn-outline-primary" onclick="exportDeplacements()">
            <i class="bi bi-download me-2"></i>Exporter
          </button>
          <?php if (in_array($note->getStatut(), ['brouillon', 'rejetee_manager'])): ?>
          <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-2"></i>Ajouter détails
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="row g-4">
      
      <!-- 📊 LEFT SECTION - TABLE ÉLÉGANTE -->
      <div class="col-lg-8">
        
        <!-- Card principale avec ombre -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          
          <!-- Header avec gradient -->
          <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h4 class="mb-1 fw-bold">
                  <i class="bi bi-receipt-cutoff me-2"></i>FRAIS PRÉVISIONNELS
                </h4>
                <small class="opacity-75">Détails des dépenses par catégorie</small>
              </div>
            </div>
          </div>

          <!-- Corps du tableau -->
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="py-3 ps-4" style="width: 35%">
                      <i class="bi bi-tag me-2 text-primary"></i>CATÉGORIE
                    </th>
                    <th class="text-center py-3" style="width: 15%">
                      <i class="bi bi-building me-2 text-success"></i>VÉLOCE
                    </th>
                    <th class="text-center py-3" style="width: 15%">
                      <i class="bi bi-wallet2 me-2 text-warning"></i>PERSO
                    </th>
                    <th class="py-3">
                      <i class="bi bi-chat-text me-2 text-info"></i>DÉTAILS
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $categoryIcons = [
                      'transport_longue_distance' => 'bi-train-front text-primary',
                      'transport_courte_distance' => 'bi-taxi-front text-success',
                      'hebergement' => 'bi-building text-warning',
                      'repas_jour' => 'bi-cup-straw text-danger',
                      'repas_pro' => 'bi-cup-hot text-info',
                      'autres' => 'bi-three-dots text-secondary'
                  ];

                  $categoriesMap = [];
                  foreach ($categories as $cat) {
                      if ($cat && method_exists($cat, 'getId')) {
                          $categoriesMap[$cat->getId()] = $cat;
                      }
                  }

                  $grandTotalVeloce = 0;
                  $grandTotalPerso = 0;
                  $totalNotes = 0;
                  ?>

                  <?php foreach ($categories as $cat): ?>
                      <?php if (!$cat) continue; ?>
                      <?php
                      $categoryDetails = [];
                      $totalVeloce = 0;
                      $totalPerso = 0;

                      foreach ($details as $d):
                          if ($d->getCategorieId() == $cat->getId()):
                              $categoryDetails[] = $d;
                              $totalVeloce += $d->getMontantVeloce();
                              $totalPerso += $d->getMontantPersonnel();
                              $totalNotes++;
                          endif;
                      endforeach;

                      $grandTotalVeloce += $totalVeloce;
                      $grandTotalPerso += $totalPerso;
                      
                      $icon = $categoryIcons[$cat->getType()] ?? 'bi-receipt text-muted';
                      ?>

                      <tr class="border-bottom">
                        <td class="ps-4 py-4">
                          <div class="d-flex align-items-start gap-3">
                            <div class="bg-light rounded-circle p-2 flex-shrink-0">
                              <i class="bi <?= $icon ?> fs-5"></i>
                            </div>
                            <div>
                              <h6 class="mb-1 fw-bold"><?= htmlspecialchars($cat->getType()) ?></h6>
                              <small class="text-muted"><?= htmlspecialchars($cat->getDescription()) ?></small>
                            </div>
                          </div>
                        </td>
                        
                        <td class="text-center align-middle py-1">
                          <?php if ($totalVeloce > 0): ?>
                            <div class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                              <i class="bi bi-check-lg fs-5"></i>
                            </div>
                          <?php else: ?>
                            <div class="badge bg-light text-muted p-2 rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                              <i class="bi bi-dash fs-5"></i>
                            </div>
                          <?php endif; ?>
                        </td>
                        
                        <td class="text-center align-middle py-1">
                          <?php if ($totalPerso > 0): ?>
                            <div class="badge bg-warning bg-opacity-10 text-warning p-2 rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                              <i class="bi bi-check-lg fs-5"></i>
                            </div>
                          <?php else: ?>
                            <div class="badge bg-light text-muted p-2 rounded-circle" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                              <i class="bi bi-dash fs-5"></i>
                            </div>
                          <?php endif; ?>
                        </td>
                        
                        <td class="align-middle py-1 pe-4">
                          <?php if (!empty($categoryDetails)): ?>
                            <div class="d-flex flex-column gap-3">
                              <?php foreach ($categoryDetails as $d): 
                                  $montant = $d->getMontantVeloce() + $d->getMontantPersonnel();
                                  $description = trim($d->getDescription()) ?: 'Aucun détail';
                              ?>
                                <div class="card border-start border-3 border-primary bg-light">
                                  <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                      <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                          <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">
                                            <i class="bi bi-receipt me-1"></i><?= number_format($montant, 2) ?> MAD
                                          </span>
                                        </div>
                                        <p class="mb-0 small text-dark"><?= htmlspecialchars($description) ?></p>
                                      </div>
                                        
                                      <div class="btn-group btn-group-sm flex-shrink-0">
                                        <?php if (in_array($note->getStatut(), ['brouillon', 'rejetee_manager'])): ?>
                                        <a href="/details/edit/<?= $d->getId() ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Modifier">
                                          <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if ($d->getJustificatifPath()): ?>
                                          <a href="<?=$d->getJustificatifPath() ?>" 
                                             target="_blank" 
                                             class="btn btn-outline-danger" 
                                             title="Voir le justificatif">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                          </a>
                                        <?php endif; ?>

                                        <?php if (in_array($note->getStatut(), ['brouillon', 'rejetee_manager'])): ?>
                                        <a href="/details/<?= $d->getId() ?>/delete" 
                                           class="btn btn-outline-danger"
                                           title="Supprimer"
                                           onclick="event.preventDefault(); 
                                                    if(confirm('Êtes-vous sûr de vouloir supprimer ce détail ?')) 
                                                        window.location.href=this.href;">
                                          <i class="bi bi-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          <?php else: ?>
                            <div class="text-center py-3">
                              <i class="bi bi-inbox text-muted fs-4 d-block mb-2"></i>
                              <small class="text-muted">Aucun frais enregistré</small>
                            </div>
                          <?php endif; ?>
                        </td>
                      </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Footer avec date -->
          <div class="card-footer bg-light border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                <i class="bi bi-calendar3 me-2"></i>Dernière mise à jour: <strong><?= date('d/m/Y à H:i') ?></strong>
              </small>
              <small class="text-muted">
                <i class="bi bi-shield-check me-2"></i>Données sécurisées
              </small>
            </div>
          </div>
        </div>

      </div>

      <!-- 💰 RIGHT SECTION - RÉSUMÉ & ACTIONS -->
      <div class="col-lg-4">
        
        <!-- 📊 Résumé Financier -->
        <div class="card border-0 shadow-lg rounded-4 mb-4">
          <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-0 fw-bold">
              <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>
              Résumé Financier
            </h5>
          </div>
          <div class="card-body p-4">
            
            <!-- Nombre de notes -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-primary bg-opacity-10 rounded-3 mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="bg-primary rounded-circle p-2">
                  <i class="bi bi-receipt-cutoff text-white"></i>
                </div>
                <span class="fw-semibold">Nombre des détails</span>
              </div>
              <span class="badge bg-primary rounded-pill fs-5 px-3 py-2"><?= $totalNotes ?></span>
            </div>

            <!-- Total VÉLOCE -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-success bg-opacity-10 rounded-3 mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="bg-success rounded-circle p-2">
                  <i class="bi bi-building text-white"></i>
                </div>
                <span class="fw-semibold">Total VÉLOCE</span>
              </div>
              <span class="fs-5 fw-bold text-success"><?= number_format($grandTotalVeloce, 2) ?>MAD</span>
            </div>

            <!-- Total Personnel -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-warning bg-opacity-10 rounded-3 mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="bg-warning rounded-circle p-2">
                  <i class="bi bi-wallet2 text-white"></i>
                </div>
                <span class="fw-semibold">Total Personnel</span>
              </div>
              <span class="fs-5 fw-bold text-warning"><?= number_format($grandTotalPerso, 2) ?>MAD</span>
            </div>

            <!-- Séparateur -->
            <hr class="my-4">

            <!-- Total Général -->
            <div class="p-4 rounded-4 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <div class="text-white opacity-75 small mb-1">TOTAL GÉNÉRAL</div>
              <div class="fw-bold text-white display-6"><?= number_format($grandTotalVeloce + $grandTotalPerso, 2) ?> MAD</div>
            </div>

            <!-- ✅ AFFICHAGE DU MONTANT REMBOURSÉ (si approuvé) -->
            <?php if ($note->getStatut() === 'approuve' && method_exists($note, 'getTotaleRembosement') && $note->getTotaleRembosement() > 0): ?>
            <div class="mt-4 p-4 rounded-4 text-center border border-2 border-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
              <div class="text-white opacity-90 small mb-2">
                <i class="bi bi-check-circle-fill me-2"></i>MONTANT REMBOURSÉ
              </div>
              <div class="fw-bold text-white display-6"><?= number_format($note->getTotaleRembosement(), 2) ?> MAD</div>
              <small class="text-white opacity-75 d-block mt-2">Remboursement approuvé</small>
            </div>
            <?php endif; ?>

            <!-- ✅ AFFICHAGE AMÉLIORÉ DES COMMENTAIRES -->
            <?php if ($commentaireManager || $commentaireAdmin): ?>
              <div class="position-relative my-4">
                <hr class="border-2">
                <div class="position-absolute top-50 start-50 translate-middle bg-white px-3">
                  <i class="bi bi-chat-dots-fill text-primary"></i>
                </div>
              </div>
              
              <div class="mb-0">
                <h6 class="fw-bold mb-3 d-flex align-items-center">
                  <i class="bi bi-chat-quote-fill text-primary me-2 fs-5"></i>
                  <span>Historique des commentaires</span>
                </h6>
                
                <!-- Commentaire Manager -->
                <?php if ($commentaireManager): ?>
                  <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #f59e0b !important;">
                    <div class="card-body p-3">
                      <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                          <div class="rounded-circle d-flex align-items-center justify-content-center"
                               style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="bi bi-person-badge-fill text-white fs-4"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <div class="p-3 bg-warning bg-opacity-10 rounded-3">
                            <p class="mb-0 small text-dark" style="line-height: 1.6;">
                              <i class="bi bi-quote text-warning me-2"></i>
                              <?= nl2br(htmlspecialchars($commentaireManager)) ?>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                
                <!-- Commentaire Admin -->
                <?php if ($commentaireAdmin): ?>
                  <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #3b82f6 !important;">
                    <div class="card-body p-3">
                      <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                          <div class="rounded-circle d-flex align-items-center justify-content-center"
                               style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class="bi bi-shield-fill-check text-white fs-4"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <div class="p-3 bg-info bg-opacity-10 rounded-3">
                            <p class="mb-0 small text-dark" style="line-height: 1.6;">
                              <i class="bi bi-quote text-info me-2"></i>
                              <?= nl2br(htmlspecialchars($commentaireAdmin)) ?>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

          </div>
        </div>

        <!-- ⚙️ Actions -->
        <div class="card border-0 shadow-lg rounded-4 mb-4">
          <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="mb-0 fw-bold">
              <i class="bi bi-gear-fill text-primary me-2"></i>
              Actions
            </h5>
          </div>
          <div class="card-body p-4">

            <!-- Impression -->
            <div class="d-grid gap-2 mb-4">
              <a href="/notes/<?= $note->getId() ?>/pdf/download" class="btn btn-danger shadow-sm">
                <i class="bi bi-download me-2"></i>Télécharger PDF
              </a>
              
              <a href="/notes/<?= $note->getId() ?>/pdf/preview" target="_blank" class="btn btn-outline-danger">
                <i class="bi bi-eye me-2"></i>Prévisualiser PDF
              </a>
            </div>

            <!-- Statut Badge -->
            <?php 
            $statut = $note->getStatut(); 
            
            $badgeClass = '';
            $badgeIcon = '';
            $badgeText = '';
            
            switch($statut) {
                case 'brouillon':
                    $badgeClass = 'bg-warning';
                    $badgeIcon = 'bi-pencil-square';
                    $badgeText = 'Brouillon';
                    break;
                case 'soumis':
                    $badgeClass = 'bg-info';
                    $badgeIcon = 'bi-hourglass-split';
                    $badgeText = 'En attente de validation';
                    break;
                case 'valide_manager':
                    $badgeClass = 'bg-primary';
                    $badgeIcon = 'bi-check-circle-fill';
                    $badgeText = 'Validée par le Manager';
                    break;
                case 'rejetee_manager':
                    $badgeClass = 'bg-danger';
                    $badgeIcon = 'bi-x-circle-fill';
                    $badgeText = 'Rejetée par le Manager';
                    break;
                case 'en_cours_admin':
                    $badgeClass = 'bg-info';
                    $badgeIcon = 'bi-hourglass-split';
                    $badgeText = 'En cours - Administration';
                    break;
                case 'approuve':
                    $badgeClass = 'bg-success';
                    $badgeIcon = 'bi-check-circle-fill';
                    $badgeText = 'Approuvée - Administration';
                    break;
                case 'rejetee_admin':
                    $badgeClass = 'bg-danger';
                    $badgeIcon = 'bi-x-circle-fill';
                    $badgeText = 'Rejetée - Administration';
                    break;
                default:
                    $badgeClass = 'bg-secondary';
                    $badgeIcon = 'bi-question-circle';
                    $badgeText = ucfirst($statut);
            }
            ?>
            
            <div class="text-center mb-4">
              <span class="badge fs-6 px-4 py-3 rounded-pill <?= $badgeClass ?> shadow-sm">
                <i class="bi <?= $badgeIcon ?> me-2"></i>
                <?= $badgeText ?>
              </span>
            </div>

            <!-- Boutons d'action selon le statut -->
            <?php if ($statut === 'brouillon'): ?>
              <form action="/notes/updateStatut/<?= $note->getId() ?>" method="POST">
                <input type="hidden" name="statut" value="soumis">
                <button type="submit" class="btn btn-success w-100 py-3 mb-3 shadow-sm">
                  <i class="bi bi-send me-2"></i>Soumettre la demande de frais
                </button>
              </form>

            <?php elseif ($statut === 'soumis'): ?>
              <form action="/notes/updateStatut/<?= $note->getId() ?>" method="POST"
                    onsubmit="return confirm('Annuler la soumission ? La note repassera en brouillon.');">
                <input type="hidden" name="statut" value="brouillon">
                <button type="submit" class="btn btn-warning w-100 py-3 mb-3 shadow-sm">
                  <i class="bi bi-arrow-counterclockwise me-2"></i>Annuler la soumission
                </button>
              </form>

            <?php elseif ($statut === 'valide_manager'): ?>
              <div class="alert alert-primary border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                  <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                  <div>
                    <h6 class="alert-heading mb-1 fw-bold">Validée par le Manager</h6>
                    <p class="mb-0 small">Votre demande a été approuvée. Elle est maintenant en attente de validation d'admin.</p>
                  </div>
                </div>
              </div>
              <button class="btn btn-primary w-100 py-3 mb-3" disabled>
                <i class="bi bi-lock me-2"></i>En attente de validation d'admin
              </button>

            <?php elseif ($statut === 'rejetee_manager'): ?>
              <div class="alert alert-danger border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                  <i class="bi bi-x-circle-fill fs-4 me-3"></i>
                  <div>
                    <h6 class="alert-heading mb-1 fw-bold">Rejetée par le Manager</h6>
                    <p class="mb-0 small">Votre demande a été refusée. Vous pouvez la modifier pour la resoumettre.</p>
                  </div>
                </div>
              </div>
              
              <form action="/notes/updateStatut/<?= $note->getId() ?>" method="POST"
                    onsubmit="return confirm('Repasser cette note en brouillon pour la modifier ?');">
                <input type="hidden" name="statut" value="brouillon">
                <button type="submit" class="btn btn-warning w-100 py-3 mb-3 shadow-sm">
                  <i class="bi bi-pencil-square me-2"></i>Modifier et resoumettre
                </button>
              </form>

            <?php elseif ($statut === 'en_cours_admin'): ?>
              <div class="alert alert-info border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                  <i class="bi bi-hourglass-split fs-4 me-3"></i>
                  <div>
                    <h6 class="alert-heading mb-1 fw-bold">En cours - Administration</h6>
                    <p class="mb-0 small">Votre demande validée par le manager est en cours de traitement par l'administration.</p>
                  </div>
                </div>
              </div>
              <button class="btn btn-info w-100 py-3 mb-3" disabled>
                <i class="bi bi-clock-history me-2"></i>En cours de traitement administratif
              </button>

            <?php elseif ($statut === 'approuve'): ?>
              <div class="alert alert-success border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                  <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                  <div>
                    <h6 class="alert-heading mb-1 fw-bold">Approuvée - Administration</h6>
                    <p class="mb-0 small">Votre demande a été approuvée définitivement par l'Administration.</p>
                  </div>
                </div>
              </div>
              <button class="btn btn-success w-100 py-3 mb-3" disabled>
                <i class="bi bi-lock me-2"></i>Approuvée définitivement
              </button>

            <?php elseif ($statut === 'rejetee_admin'): ?>
              <div class="alert alert-danger border-0 shadow-sm mb-3" role="alert">
                <div class="d-flex align-items-center">
                  <i class="bi bi-x-circle-fill fs-4 me-3"></i>
                  <div>
                    <h6 class="alert-heading mb-1 fw-bold">Rejetée - Administration</h6>
                    <p class="mb-0 small">Votre demande a été refusée par l'administration. Contactez votre manager pour plus d'informations.</p>
                  </div>
                </div>
              </div>
              <button class="btn btn-danger w-100 py-3 mb-3" disabled>
                <i class="bi bi-lock me-2"></i>Rejetée par l'administration
              </button>

            <?php else: ?>
              <button class="btn btn-secondary w-100 py-3 mb-3" disabled>
                <i class="bi bi-lock me-2"></i>Statut: <?= ucfirst($statut) ?>
              </button>
            <?php endif; ?>

            <!-- Supprimer (uniquement si pas validée définitivement) -->
            <?php if (!in_array($statut, ['approuve'])): ?>
              <button class="btn btn-outline-danger w-100 py-3"
                      onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette note de frais ?')) window.location.href='/notes/<?= $note->getId() ?>/delete';">
                <i class="bi bi-trash me-2"></i>Supprimer la demande de frais
              </button>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- MODALS... (le reste du code reste identique) -->

<style>
.card[style*="border-left"]:hover {
  transform: translateX(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
  transition: all 0.3s ease;
}

.bi-quote {
  font-size: 1.2rem;
  opacity: 0.5;
}

.badge.bg-success-subtle,
.badge.bg-danger-subtle {
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<!-- MODAL AJOUT -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Un détails = une facture</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="/details/store" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          <input type="hidden" name="note_id" value="<?= $note->getId() ?>">
          <div class="mb-4">
            <label class="form-label fw-bold">Catégorie de frais *</label>
            <div class="row g-3">
              <?php
              $iconMap = [
                  'transport_longue_distance' => 'bi-train-front text-primary',
                  'transport_courte_distance' => 'bi-taxi-front text-success',
                  'hebergement' => 'bi-building text-warning',
                  'repas_jour' => 'bi-cup-straw text-danger',
                  'repas_pro' => 'bi-cup-hot text-info',
                  'autres' => 'bi-three-dots text-secondary'
              ];
              foreach ($categories as $c):
                  $icon = $iconMap[$c->getType()] ?? 'bi-receipt text-muted';
              ?>
                <div class="col-6 col-md-4">
                  <div class="text-center">
                    <input type="radio" class="btn-check" name="categorie_id" id="add_cat_<?= $c->getId() ?>" value="<?= $c->getId() ?>" required>
                    <label class="btn btn-outline-primary d-block p-3 rounded-3 h-100" for="add_cat_<?= $c->getId() ?>">
                      <i class="bi <?= $icon ?> fs-1 d-block mb-2"></i>
                      <span class="small d-block"><?= ucwords(str_replace('_', ' ', $c->getType())) ?></span>
                    </label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="mb-4" id="subcategory-group">
            <label class="form-label fw-bold" id="subcategory-label">Type *</label>
            <select name="subcategory" class="form-select" id="subcategory-select">
              <option value="">Sélectionner...</option>
            </select>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Date *</label>
              <input type="date" name="date_frais" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Montant (MAD) *</label>
              <input type="number" step="0.01" name="montant_total" class="form-control" required>
            </div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="via_veloce" id="add_via_veloce">
                <label class="form-check-label fw-bold" for="add_via_veloce">Via VÉLOCE</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="frais_personnel" id="add_frais_personnel">
                <label class="form-check-label" for="add_frais_personnel">Frais personnel</label>
              </div>
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label">Description (facultatif)</label>
            <input type="text" name="description" class="form-control" placeholder="Ex: Taxi gare → hôtel">
          </div>
          <div class="mb-4">
            <label class="form-label d-block text-center fw-bold">Justificatif / Facture</label>
            <div class="border border-2 border-dashed rounded-4 p-4 text-center bg-light">
              <i class="bi bi-cloud-upload fs-1 text-muted mb-3 d-block"></i>
              <p class="text-muted mb-2">Choisir une facture</p>
              <input type="file" name="justificatif" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
              <small class="text-muted d-block mt-2">PNG, JPG, PDF jusqu'à 10 MB</small>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary px-4">Ajouter détails de frais</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL EDIT -->
<?php if(isset($randomId) && $randomId !== null): ?>
<div class="modal fade show" id="editModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Modifier le détail de frais</h5>
        <button type="button" class="btn-close" 
        onclick="let modal = document.getElementById('editModal');
                 modal.classList.remove('show');
                 modal.style.display = 'none';
                 window.location.href = '/notes/<?= $note->getDeplacementId() ?>';
                " 
        data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/details/update" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="note_id" value="<?= $note->getId() ?>">
          <input type="hidden" name="detail_id" value="<?= $iddd?>">

          <div class="mb-4">
            <label class="form-label fw-bold">Catégorie de frais *</label>
            <div class="row g-3">
              <?php foreach ($categories as $c):
                  $checked = ($categorie_id == $c->getId()) ? 'checked' : '';
                  $icon = $iconMap[$c->getType()] ?? 'bi-receipt text-muted';
              ?>
                <div class="col-6 col-md-4">
                  <div class="text-center">
                    <input type="radio" class="btn-check" name="categorie_id" id="edit_cat_<?= $c->getId() ?>" value="<?= $c->getId() ?>" <?= $checked ?> required>
                    <label class="btn btn-outline-primary d-block p-3 rounded-3 h-100" for="edit_cat_<?= $c->getId() ?>">
                      <i class="bi <?= $icon ?> fs-1 d-block mb-2"></i>
                      <span class="small d-block"><?= ucwords(str_replace('_', ' ', $c->getType())) ?></span>
                    </label>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="mb-4" id="edit-subcategory-group">
            <label class="form-label fw-bold" id="edit-subcategory-label">Type *</label>
            <select name="subcategory" class="form-select" id="edit-subcategory-select">
              <option value="">Sélectionner...</option>
            </select>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Date *</label>
              <input type="date" name="date_frais" class="form-control" value="<?= $date_frais?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Montant (MAD) *</label>
              <input type="number" step="0.01" name="montant_total" class="form-control"
                     value="<?= $montant_veloce + $montant_personnel ?>" required>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="via_veloce" id="edit_via_veloce"
                       <?= $montant_veloce > 0 ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold" for="edit_via_veloce">Via VÉLOCE</label>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="frais_personnel" id="edit_frais_personnel"
                       <?= $montant_personnel > 0 ? 'checked' : '' ?>>
                <label class="form-check-label" for="edit_frais_personnel">Frais personnel</label>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" 
            onclick="let modal = document.getElementById('editModal');
                     modal.classList.remove('show');
                     modal.style.display = 'none';
                     window.location.href = '/notes/<?= $note->getDeplacementId() ?>';
            " data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-warning px-4">Sauvegarder</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<script>
const subcategoryMap = {};
<?php foreach ($categories as $c): ?>
    subcategoryMap[<?= $c->getId() ?>] = <?= json_encode(array_values(array_filter(array_map('trim', explode(',', $c->getDescription() ?? ''))))) ?>;
<?php endforeach; ?>

document.getElementById('addModal')?.addEventListener('shown.bs.modal', function () {
    document.querySelectorAll('#addModal input[name="categorie_id"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const group = document.getElementById('subcategory-group');
            const select = document.getElementById('subcategory-select');
            const label = document.getElementById('subcategory-label');
            select.innerHTML = '<option value="">Sélectionner...</option>';
            if (this.value && subcategoryMap[this.value]?.length > 0) {
                subcategoryMap[this.value].forEach(opt => select.add(new Option(opt, opt)));
                const catName = this.closest('label').querySelector('span').textContent.trim();
                label.textContent = `Type de ${catName} *`;
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
            }
        });
    });
});

<?php if (isset($randomId) && $randomId !== null && !empty($description)): ?>
    window.addEventListener('DOMContentLoaded', function() {
        const editCatId = <?= $categorie_id ?>;
        const editSaved = "<?= addslashes(trim($description)) ?>";
        
        if (subcategoryMap[editCatId]?.includes(editSaved)) {
            const editSelect = document.getElementById('edit-subcategory-select');
            const editGroup = document.getElementById('edit-subcategory-group');
            const editLabel = document.getElementById('edit-subcategory-label');
            
            editSelect.innerHTML = '<option value="">Sélectionner...</option>';
            subcategoryMap[editCatId].forEach(opt => {
                const option = new Option(opt, opt);
                if (opt === editSaved) option.selected = true;
                editSelect.add(option);
            });
            
            editGroup.style.display = 'block';
            
            const checkedRadio = document.querySelector('#editModal input[name="categorie_id"]:checked');
            if (checkedRadio) {
                const catName = checkedRadio.closest('label').querySelector('span').textContent.trim();
                editLabel.textContent = `Type de ${catName} *`;
            }
        }
    });
<?php endif; ?>

document.querySelectorAll('#editModal input[name="categorie_id"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const group = document.getElementById('edit-subcategory-group');
        const select = document.getElementById('edit-subcategory-select');
        const label = document.getElementById('edit-subcategory-label');
        select.innerHTML = '<option value="">Sélectionner...</option>';
        if (this.value && subcategoryMap[this.value]?.length > 0) {
            subcategoryMap[this.value].forEach(opt => select.add(new Option(opt, opt)));
            const catName = this.closest('label').querySelector('span').textContent.trim();
            label.textContent = `Type de ${catName} *`;
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
        }
    });
});
</script>

