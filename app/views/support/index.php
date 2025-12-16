<main class="admin-main">
  <div class="container-fluid p-4 p-lg-5"></div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="mb-4">
                    <i class="bi bi-question-circle text-primary"></i>
                    Support & Help
                </h1>

                <!-- FAQ Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-book"></i> Questions Fréquentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <!-- Question 1 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Comment créer un déplacement ?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Pour créer un déplacement, allez dans <strong>Déplacements</strong> → <strong>Nouveau Déplacement</strong>. Remplissez tous les champs obligatoires (titre, lieu, dates) et validez.
                                    </div>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Comment soumettre une note de frais ?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Après avoir créé un déplacement, accédez à la note de frais associée, ajoutez vos dépenses avec justificatifs, puis cliquez sur <strong>"Soumettre"</strong>.
                                    </div>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Que faire si ma note est rejetée ?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Consultez les commentaires du manager, corrigez les erreurs signalées, puis soumettez à nouveau votre note de frais.
                                    </div>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Comment modifier mon profil ?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Allez dans <strong>Paramètres</strong> → <strong>Profil</strong> pour modifier vos informations personnelles, avatar, et préférences.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guides Section -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text"></i> Guides d'Utilisation</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-arrow-right-circle text-success"></i>
                                <a href="#" class="text-decoration-none">Guide de démarrage rapide</a>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-arrow-right-circle text-success"></i>
                                <a href="#" class="text-decoration-none">Comment gérer vos déplacements</a>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-arrow-right-circle text-success"></i>
                                <a href="#" class="text-decoration-none">Soumettre et suivre vos notes de frais</a>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-arrow-right-circle text-success"></i>
                                <a href="#" class="text-decoration-none">Utiliser le calendrier</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-envelope"></i> Contacter le Support</h5>
                    </div>
                    <div class="card-body">
                        <p>Besoin d'aide supplémentaire ? Créez un ticket de support et notre équipe vous répondra dans les plus brefs délais.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                            <i class="bi bi-plus-circle"></i> Créer un Ticket
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Mes Tickets -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Mes Tickets</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tickets)): ?>
                            <p class="text-muted">Aucun ticket pour le moment</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach (array_slice($tickets, 0, 3) as $ticket): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="/support/ticket/<?= $ticket->getId() ?>" class="text-decoration-none">
                                            <?= htmlspecialchars(substr($ticket->getSubject(), 0, 30)) ?>...
                                        </a>
                                        <span class="badge bg-<?= $ticket->getStatus() === 'open' ? 'success' : 'secondary' ?>">
                                            <?= $ticket->getStatus() ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="/support/tickets" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                Voir tous mes tickets
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informations</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> support@entreprise.com</p>
                        <p><strong>Téléphone:</strong> +212 589-248758</p>
                        <p><strong>Horaires:</strong> Lun-Ven 9h-18h</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Créer Ticket -->
    <div class="modal fade" id="createTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer un Ticket de Support</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/support/ticket" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sujet *</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select name="category" class="form-select">
                                <option value="general">Général</option>
                                <option value="technique">Technique</option>
                                <option value="deplacement">Déplacement</option>
                                <option value="note_frais">Note de Frais</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Priorité</label>
                            <select name="priority" class="form-select">
                                <option value="low">Basse</option>
                                <option value="normal" selected>Normale</option>
                                <option value="high">Haute</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
                                </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
