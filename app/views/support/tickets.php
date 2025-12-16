
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-section {
            background: var(--primary-gradient);
            color: white;
            padding: 4rem 0 3rem;
            margin-bottom: 3rem;
            border-radius: 0 0 50px 50px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .stat-icon.open {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-icon.progress {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-icon.closed {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .ticket-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 1.5rem;
        }

        .ticket-card:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.15);
        }

        .ticket-header {
            padding: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-body {
            padding: 1.5rem;
        }

        .ticket-footer {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-open {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .status-progress {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .status-closed {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .priority-badge {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .priority-low {
            background: #e3f2fd;
            color: #1976d2;
        }

        .priority-normal {
            background: #fff3e0;
            color: #f57c00;
        }

        .priority-high {
            background: #ffebee;
            color: #c62828;
        }

        .category-tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #f0f0f0;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #666;
            margin-right: 0.5rem;
        }

        .btn-view-ticket {
            background: var(--primary-gradient);
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-view-ticket:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-new-ticket {
            background: var(--success-gradient);
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 5px 20px rgba(17, 153, 142, 0.3);
            transition: all 0.3s ease;
        }

        .btn-new-ticket:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(17, 153, 142, 0.5);
            color: white;
        }

        .filter-tabs {
            background: white;
            border-radius: 50px;
            padding: 0.5rem;
            display: inline-flex;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filter-tab {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-tab.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .ticket-id {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        .ticket-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .ticket-meta {
            color: #999;
            font-size: 0.9rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-box input {
            border-radius: 50px;
            padding: 1rem 1.5rem 1rem 3rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }
            
            .filter-tabs {
                display: flex;
                flex-direction: column;
                width: 100%;
            }
            
            .filter-tab {
                margin-bottom: 0.5rem;
            }
        }
    </style>

<main class="admin-main">
  <div class="container-fluid p-4 p-lg-5">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="mb-3">
                        <i class="bi bi-ticket-perforated-fill"></i>
                        Mes Tickets de Support
                    </h1>
                    <p class="lead mb-0">Suivez et gérez tous vos tickets de support en un seul endroit</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <button type="button" class="btn btn-new-ticket" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouveau Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stat-icon open">
                        <i class="bi bi-folder-fill text-white"></i>
                    </div>
                    <h3 class="mb-1"><?= count(array_filter($tickets, fn($t) => $t->getStatus() === 'open')) ?></h3>
                    <p class="text-muted mb-0">Tickets Ouverts</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stat-icon progress">
                        <i class="bi bi-hourglass-split text-white"></i>
                    </div>
                    <h3 class="mb-1"><?= count(array_filter($tickets, fn($t) => $t->getStatus() === 'in_progress')) ?></h3>
                    <p class="text-muted mb-0">En Cours</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stat-icon closed">
                        <i class="bi bi-check-circle-fill text-white"></i>
                    </div>
                    <h3 class="mb-1"><?= count(array_filter($tickets, fn($t) => $t->getStatus() === 'closed')) ?></h3>
                    <p class="text-muted mb-0">Tickets Résolus</p>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" id="searchTickets" placeholder="Rechercher un ticket par sujet ou catégorie...">
        </div>

        <!-- Filter Tabs -->
        <div class="text-center mb-4">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">
                    <i class="bi bi-grid-fill me-2"></i>Tous
                </button>
                <button class="filter-tab" data-filter="open">
                    <i class="bi bi-folder me-2"></i>Ouverts
                </button>
                <button class="filter-tab" data-filter="in_progress">
                    <i class="bi bi-hourglass-split me-2"></i>En Cours
                </button>
                <button class="filter-tab" data-filter="closed">
                    <i class="bi bi-check-circle me-2"></i>Résolus
                </button>
            </div>
        </div>

        <!-- Tickets List -->
        <div id="ticketsList">
            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3 class="text-muted">Aucun ticket pour le moment</h3>
                    <p class="text-muted">Créez votre premier ticket de support pour commencer</p>
                    <button type="button" class="btn btn-new-ticket mt-3" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer un Ticket
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-card" data-status="<?= $ticket->getStatus() ?>" data-subject="<?= strtolower($ticket->getSubject()) ?>" data-category="<?= strtolower($ticket->getCategory()) ?>">
                        <div class="ticket-header">
                            <div>
                                <span class="ticket-id">#<?= str_pad($ticket->getId(), 5, '0', STR_PAD_LEFT) ?></span>
                                <span class="category-tag ms-2">
                                    <i class="bi bi-tag-fill me-1"></i>
                                    <?= ucfirst($ticket->getCategory()) ?>
                                </span>
                            </div>
                            <div>
                                <span class="priority-badge priority-<?= $ticket->getPriority() ?>">
                                    <?= strtoupper($ticket->getPriority()) ?>
                                </span>
                            </div>
                        </div>

                        <div class="ticket-body">
                            <h3 class="ticket-title"><?= htmlspecialchars($ticket->getSubject()) ?></h3>
                            <p class="text-muted mb-3">
                                <?= htmlspecialchars(substr($ticket->getMessage(), 0, 150)) ?>...
                            </p>
                            <div class="ticket-meta">
                                <i class="bi bi-calendar3 me-2"></i>
                                Créé le <?= date('d/m/Y à H:i', strtotime($ticket->getCreatedAt())) ?>
                            </div>
                        </div>

                        <div class="ticket-footer">
                            <span class="status-badge status-<?= $ticket->getStatus() ?>">
                                <?php
                                    $statusLabels = [
                                        'open' => 'Ouvert',
                                        'in_progress' => 'En Cours',
                                        'closed' => 'Résolu'
                                    ];
                                    echo $statusLabels[$ticket->getStatus()] ?? $ticket->getStatus();
                                ?>
                            </span>
                            <a href="/support/ticket/<?= $ticket->getId() ?>" class="btn btn-view-ticket">
                                <i class="bi bi-eye-fill me-2"></i>
                                Voir Détails
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Créer Ticket -->
    <div class="modal fade" id="createTicketModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header" style="background: var(--primary-gradient); color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Créer un Nouveau Ticket
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="/support/ticket" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-chat-text me-2 text-primary"></i>
                                Sujet *
                            </label>
                            <input type="text" name="subject" class="form-control form-control-lg" 
                                   placeholder="Décrivez brièvement votre problème..." required
                                   style="border-radius: 15px;">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-tag me-2 text-primary"></i>
                                    Catégorie
                                </label>
                                <select name="category" class="form-select form-select-lg" style="border-radius: 15px;">
                                    <option value="general">Général</option>
                                    <option value="technique">Technique</option>
                                    <option value="deplacement">Déplacement</option>
                                    <option value="note_frais">Note de Frais</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-exclamation-triangle me-2 text-primary"></i>
                                    Priorité
                                </label>
                                <select name="priority" class="form-select form-select-lg" style="border-radius: 15px;">
                                    <option value="low">Basse</option>
                                    <option value="normal" selected>Normale</option>
                                    <option value="high">Haute</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-chat-quote me-2 text-primary"></i>
                                Message *
                            </label>
                            <textarea name="message" class="form-control" rows="6" required
                                      placeholder="Décrivez votre problème en détail..."
                                      style="border-radius: 15px;"></textarea>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Plus vous donnez de détails, plus vite nous pourrons vous aider
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer" style="border: none; padding: 1.5rem;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 50px; padding: 0.6rem 2rem;">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-new-ticket">
                            <i class="bi bi-send-fill me-2"></i>
                            Envoyer le Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
                                </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                const tickets = document.querySelectorAll('.ticket-card');

                tickets.forEach(ticket => {
                    if (filter === 'all' || ticket.getAttribute('data-status') === filter) {
                        ticket.style.display = 'block';
                    } else {
                        ticket.style.display = 'none';
                    }
                });
            });
        });

        // Search functionality
        document.getElementById('searchTickets').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tickets = document.querySelectorAll('.ticket-card');

            tickets.forEach(ticket => {
                const subject = ticket.getAttribute('data-subject');
                const category = ticket.getAttribute('data-category');
                
                if (subject.includes(searchTerm) || category.includes(searchTerm)) {
                    ticket.style.display = 'block';
                } else {
                    ticket.style.display = 'none';
                }
            });
        });
    </script>
