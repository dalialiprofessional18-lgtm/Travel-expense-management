<?php $title = 'Historique'; ob_start(); ?>

<main class="admin-main">
    <div class="container-fluid p-4 p-lg-5">
        
        <!-- Header Section with Glassmorphism -->
        <div class="history-header mb-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper me-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h1 class="display-5 fw-bold mb-0 gradient-text">Historique des Statuts</h1>
                            <div class="subtitle-line"></div>
                        </div>
                    </div>
                    
                    <?php if (isset($deplacement)): ?>
                        <div class="info-card glass-card">
                            <i class="bi bi-briefcase-fill me-2"></i>
                            <span class="text-muted-custom">Déplacement :</span>
                            <strong class="ms-2"><?= htmlspecialchars($deplacement->getTitre()) ?></strong>
                        </div>
                    <?php elseif (isset($note)): ?>
                        <div class="info-card glass-card">
                            <i class="bi bi-receipt me-2"></i>
                            <span class="text-muted-custom">Note de frais</span>
                            <strong class="ms-2">#<?= $note->getId() ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4 text-lg-end">
                    <div class="stats-card glass-card">
                        <div class="stat-item">
                            <div class="stat-number"><?= count($historiques) ?></div>
                            <div class="stat-label">Événements</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Container -->
        <div class="modern-timeline">
            <?php 
            $statusColors = [
                'brouillon' => ['color' => '#6c757d', 'icon' => 'pencil-square', 'label' => 'Brouillon'],
                'soumis' => ['color' => '#0dcaf0', 'icon' => 'send-fill', 'label' => 'Soumis'],
                'valide_manager' => ['color' => '#198754', 'icon' => 'check-circle-fill', 'label' => 'Validé Manager'],
                'rejetee_manager' => ['color' => '#dc3545', 'icon' => 'x-circle-fill', 'label' => 'Rejeté Manager'],
                'en_cours_admin' => ['color' => '#ffc107', 'icon' => 'hourglass-split', 'label' => 'En cours Admin'],
                'approuve' => ['color' => '#0d6efd', 'icon' => 'shield-check', 'label' => 'Approuvé'],
                'rejetee_admin' => ['color' => '#dc3545', 'icon' => 'shield-x', 'label' => 'Rejeté Admin']
            ];
            
            foreach ($historiques as $index => $h): 
                $statut = $h->getNouveauStatut();
                $config = $statusColors[$statut] ?? ['color' => '#6c757d', 'icon' => 'circle-fill', 'label' => $statut];
                $isLast = ($index === count($historiques) - 1);
            ?>
            
            <div class="timeline-item <?= $isLast ? 'timeline-item-last' : '' ?>" 
                 style="--item-color: <?= $config['color'] ?>; --animation-delay: <?= $index * 0.1 ?>s;">
                
                <!-- Timeline Node -->
                <div class="timeline-node">
                    <div class="node-outer">
                        <div class="node-inner">
                            <i class="bi bi-<?= $config['icon'] ?>"></i>
                        </div>
                    </div>
                    <?php if (!$isLast): ?>
                        <div class="timeline-connector"></div>
                    <?php endif; ?>
                </div>

                <!-- Timeline Content -->
                <div class="timeline-content">
                    <div class="timeline-card glass-card">
                        <!-- Card Header -->
                        <div class="card-header-timeline">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="status-info">
                                    <span class="status-badge" style="background: <?= $config['color'] ?>;">
                                        <i class="bi bi-<?= $config['icon'] ?> me-1"></i>
                                        <?= $config['label'] ?>
                                    </span>
                                    
                                    <?php if ($h->getAncienStatut()): ?>
                                        <div class="status-transition">
                                            <i class="bi bi-arrow-left-short"></i>
                                            <span class="ancien-statut">
                                                <?= $statusColors[$h->getAncienStatut()]['label'] ?? $h->getAncienStatut() ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="timestamp">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= date('d/m/Y', strtotime($h->getCreatedAt())) ?>
                                    <span class="time-separator">•</span>
                                    <?= date('H:i', strtotime($h->getCreatedAt())) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <?php if ($h->getCommentaire()): ?>
                        <div class="card-body-timeline">
                            <div class="comment-section">
                                <div class="comment-icon">
                                    <i class="bi bi-chat-left-quote-fill"></i>
                                </div>
                                <div class="comment-text">
                                    <?= nl2br(htmlspecialchars($h->getCommentaire())) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Card Footer -->
                        <div class="card-footer-timeline">
                            <div class="author-info">
                                <div class="author-avatar">
                                    <?php 
                                    $displayName = $h->getDisplayName();
                                    $initials = '';
                                    if ($displayName !== 'Système') {
                                        $parts = explode(' ', $displayName);
                                        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                                    }
                                    ?>
                                    <?php if ($displayName === 'Système'): ?>
                                        <i class="bi bi-cpu-fill"></i>
                                    <?php else: ?>
                                        <?= $initials ?>
                                    <?php endif; ?>
                                </div>
                                <div class="author-details">
                                    <div class="author-name"><?= htmlspecialchars($displayName) ?></div>
                                    <div class="author-role"><?= htmlspecialchars($h->getDisplayRole()) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <?php if (empty($historiques)): ?>
        <div class="empty-state">
            <div class="empty-state-content glass-card">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3 class="empty-title">Aucun historique disponible</h3>
                <p class="empty-text">Les changements de statut apparaîtront ici</p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<style>
/* ========== MODERN VARIABLES - LIGHT MODE ========== */
:root {
    --glass-bg-light: rgba(255, 255, 255, 0.8);
    --glass-bg-dark: rgba(0, 0, 0, 0.8);
    --glass-border-light: rgba(255, 255, 255, 0.3);
    --glass-border-dark: rgba(255, 255, 255, 0.1);
    --glass-shadow-light: 0 8px 32px rgba(0, 0, 0, 0.1);
    --glass-shadow-dark: 0 8px 32px rgba(0, 0, 0, 0.5);
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --text-primary-light: #1a1a2e;
    --text-primary-dark: #ffffff;
    --text-secondary-light: #6c757d;
    --text-secondary-dark: #a0a0a0;
    --bg-overlay-light: rgba(0, 0, 0, 0.05);
    --bg-overlay-dark: rgba(255, 255, 255, 0.05);
    --timeline-width: 4px;
    --node-size: 60px;
    --card-radius: 20px;
    
    /* Default to light mode */
    --glass-bg: var(--glass-bg-light);
    --glass-border: var(--glass-border-light);
    --glass-shadow: var(--glass-shadow-light);
    --text-primary: var(--text-primary-light);
    --text-secondary: var(--text-secondary-light);
    --bg-overlay: var(--bg-overlay-light);
}

/* ========== DARK MODE ========== */
[data-bs-theme="dark"],
.dark-mode,
body.dark-mode,
html[data-theme="dark"] {
    --glass-bg: var(--glass-bg-dark);
    --glass-border: var(--glass-border-dark);
    --glass-shadow: var(--glass-shadow-dark);
    --text-primary: var(--text-primary-dark);
    --text-secondary: var(--text-secondary-dark);
    --bg-overlay: var(--bg-overlay-dark);
}

/* Dark mode avec classe sur body ou html */
body.dark-mode .admin-main,
html.dark-mode .admin-main,
[data-bs-theme="dark"] .admin-main {
    color: var(--text-primary-dark);
}

/* ========== GLASSMORPHISM CARDS ========== */
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
    border-radius: var(--card-radius);
}

/* ========== HEADER SECTION ========== */
.history-header {
    position: relative;
    padding: 2rem 0;
}

.icon-wrapper {
    width: 70px;
    height: 70px;
    background: var(--primary-gradient);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    animation: float 3s ease-in-out infinite;
}

.icon-wrapper i {
    font-size: 2rem;
    color: white;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.subtitle-line {
    width: 100px;
    height: 4px;
    background: var(--primary-gradient);
    border-radius: 2px;
    margin-top: 0.5rem;
    animation: expandLine 0.6s ease-out;
}

@keyframes expandLine {
    from { width: 0; }
    to { width: 100px; }
}

.info-card {
    display: inline-flex;
    align-items: center;
    padding: 1rem 1.5rem;
    margin-top: 1rem;
    animation: slideInLeft 0.5s ease-out;
    color: var(--text-primary);
}

.text-muted-custom {
    color: var(--text-secondary) !important;
}

.stats-card {
    padding: 1.5rem;
    text-align: center;
    animation: slideInRight 0.5s ease-out;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 0.25rem;
}

/* ========== MODERN TIMELINE ========== */
.modern-timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline-item {
    position: relative;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
    animation-delay: var(--animation-delay);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ========== TIMELINE NODE ========== */
.timeline-node {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.node-outer {
    width: var(--node-size);
    height: var(--node-size);
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid var(--item-color);
    box-shadow: 0 0 0 6px rgba(var(--item-color-rgb), 0.1);
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.timeline-item:hover .node-outer {
    transform: scale(1.1);
    box-shadow: 0 0 0 10px rgba(var(--item-color-rgb), 0.15);
}

.node-inner {
    width: 40px;
    height: 40px;
    background: var(--item-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.node-inner i {
    font-size: 1.25rem;
    color: white;
}

.timeline-connector {
    width: var(--timeline-width);
    flex: 1;
    background: linear-gradient(to bottom, var(--item-color), rgba(108, 117, 125, 0.2));
    margin-top: 1rem;
    position: relative;
    border-radius: 2px;
}

.timeline-connector::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 0;
    background: var(--item-color);
    border-radius: 2px;
    animation: fillConnector 0.8s ease-out forwards;
    animation-delay: calc(var(--animation-delay) + 0.3s);
}

@keyframes fillConnector {
    to { height: 100%; }
}

/* ========== TIMELINE CARD ========== */
.timeline-card {
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.timeline-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    border-color: var(--item-color);
}

/* Dark mode hover effect */
[data-bs-theme="dark"] .timeline-card:hover,
.dark-mode .timeline-card:hover {
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
}

.card-header-timeline {
    padding: 1.5rem;
    border-bottom: 1px solid var(--bg-overlay);
    background: linear-gradient(135deg, var(--bg-overlay), transparent);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    animation: bounceIn 0.5s ease-out;
}

@keyframes bounceIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.status-transition {
    display: inline-flex;
    align-items: center;
    margin-left: 1rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.ancien-statut {
    opacity: 0.7;
    font-style: italic;
}

.timestamp {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
}

.time-separator {
    margin: 0 0.5rem;
    opacity: 0.5;
}

/* ========== COMMENT SECTION ========== */
.card-body-timeline {
    padding: 1.5rem;
}

.comment-section {
    display: flex;
    gap: 1rem;
    background: rgba(102, 126, 234, 0.08);
    padding: 1.25rem;
    border-radius: 15px;
    border-left: 4px solid var(--item-color);
}

[data-bs-theme="dark"] .comment-section,
.dark-mode .comment-section {
    background: rgba(102, 126, 234, 0.15);
}

.comment-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: var(--item-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.comment-text {
    flex: 1;
    color: var(--text-primary);
    font-style: italic;
    line-height: 1.6;
}

/* ========== AUTHOR INFO ========== */
.card-footer-timeline {
    padding: 1rem 1.5rem;
    background: var(--bg-overlay);
}

.author-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.author-avatar {
    width: 45px;
    height: 45px;
    background: var(--item-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.author-details {
    flex: 1;
}

.author-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.author-role {
    font-size: 0.8rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ========== EMPTY STATE ========== */
.empty-state {
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.5s ease-out;
}

.empty-state-content {
    text-align: center;
    padding: 3rem;
    max-width: 400px;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: float 3s ease-in-out infinite;
}

.empty-icon i {
    font-size: 3rem;
    color: white;
}

.empty-title {
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.empty-text {
    color: var(--text-secondary);
    margin: 0;
}

/* ========== ANIMATIONS ========== */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
    .timeline-item {
        grid-template-columns: 50px 1fr;
        gap: 1.5rem;
    }
    
    .node-outer {
        width: 50px;
        height: 50px;
    }
    
    .node-inner {
        width: 35px;
        height: 35px;
    }
    
    .card-header-timeline {
        padding: 1rem;
    }
    
    .card-body-timeline {
        padding: 1rem;
    }
    
    .comment-section {
        flex-direction: column;
    }
    
    .status-transition {
        display: block;
        margin-left: 0;
        margin-top: 0.5rem;
    }
    
    .timestamp {
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
}

/* ========== PRINT STYLES ========== */
@media print {
    .modern-timeline {
        display: block;
    }
    
    .timeline-item {
        page-break-inside: avoid;
        margin-bottom: 2rem;
    }
    
    .timeline-connector,
    .glass-card {
        border: 1px solid #ddd;
        box-shadow: none;
        backdrop-filter: none;
    }
}
</style>

