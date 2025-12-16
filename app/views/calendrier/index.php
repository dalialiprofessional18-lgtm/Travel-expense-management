<?php 
// views/calendrier/index.php
$title = 'Calendrier des Déplacements';

// Les données viennent du contrôleur
$deplacements = $data['deplacements'];
$stats = $data['statistiques'];
$prochains = $data['prochains_deplacements'];
$userRole = $data['user_role'];

// Fonction pour obtenir la couleur selon le type

function getCouleurType($type) {
    $couleurs = [
        'mission ' => '#FFC107',
        'formation' => '#4CAF50',
        'visite' => '#9C27B0',
        'reunion' => '#8B4513',
        'conference' => '#607D8B',
        'salon' => '#2196F3',
        'audit' => '#00BCD4',
        'installation' => '#E91E63',
        'support' => '#FF5722',
        'prospection' => '#FF69B4',
        'autre' => '#3B82F6',
    ];
    return $couleurs[$type] ?? '#3B82F6';
}

// Fonction pour compter les déplacements par type
function countByType($deplacements, $type) {
    return count(array_filter($deplacements, fn($d) => $d['type'] === $type));
}

// Fonction pour obtenir l'avatar URL
function getAvatarUrl($deplacement) {
    if (!empty($deplacement['avatar_path'])) {
        return $deplacement['avatar_path'];
    }
    return '/assets/images/default-avatar.png';
}

// Générer le calendrier du mois actuel
$mois = isset($_GET['mois']) ? intval($_GET['mois']) : date('n');
$annee = isset($_GET['annee']) ? intval($_GET['annee']) : date('Y');

$premier_jour = mktime(0, 0, 0, $mois, 1, $annee);
$nb_jours = date('t', $premier_jour);
$jour_semaine_debut = date('w', $premier_jour); // 0 (dimanche) à 6 (samedi)

// Organiser les déplacements par date (uniquement début et fin)
$deplacements_par_jour = [];
foreach ($deplacements as $dep) {
    $date_debut = $dep['date_depart'];
    $date_fin = $dep['date_retour'];
    
    
    // Ajouter au jour de début
    if (!isset($deplacements_par_jour[$date_debut])) {
        $deplacements_par_jour[$date_debut] = [];
    }
    $deplacements_par_jour[$date_debut][] = array_merge($dep, ['is_debut' => true]);
    
    // Ajouter au jour de fin seulement si différent du début
    if ($date_debut !== $date_fin) {
        if (!isset($deplacements_par_jour[$date_fin])) {
            $deplacements_par_jour[$date_fin] = [];
        }
        $deplacements_par_jour[$date_fin][] = array_merge($dep, ['is_debut' => false]);
    }
}

// Noms des mois en français
$mois_fr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

ob_start();
?>

<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0;
    }
    .calendar-day {
        min-height: 120px;
        border: 1px solid #e5e7eb;
        padding: 8px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .calendar-day:hover {
        background-color: #f9fafb;
    }
    .calendar-day.other-month {
        background-color: #f9fafb;
        opacity: 0.6;
    }
    .calendar-day.today {
        background-color: #dbeafe;
        border: 2px solid #3b82f6;
    }
    .event-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        margin-bottom: 4px;
        color: white;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .event-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .event-badge .user-avatar-mini {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid rgba(255,255,255,0.5);
        flex-shrink: 0;
    }
    .event-dot {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background-color: #f59e0b;
        border-radius: 50%;
    }
    .mini-calendar {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .mini-day {
        text-align: center;
        padding: 8px 4px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
    }
    .mini-day:hover {
        background-color: #f3f4f6;
    }
    .mini-day.today {
        background-color: #3b82f6;
        color: white;
        font-weight: bold;
    }
    .mini-day.has-event {
        border: 1px solid #f59e0b;
    }
    .upcoming-event-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 8px;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .upcoming-event-item {
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        transition: background-color 0.2s;
    }
    .upcoming-event-item:hover {
        background-color: #f9fafb;
    }
   
.event-wrapper {
    position: relative;
    z-index: 10;
}

.event-wrapper:hover {
    z-index: 9999;
}
/* Caché par défaut */
.user-hover-box {
    display: none;
    position: fixed;
    top: 110%;
    left: 0;
    background: #ffffff;
    border-radius: 10px;
    padding: 10px 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    z-index: 333333;
    min-width: 220px;
    gap: 10px;
}

/* AU HOVER DU CONTENEUR */
.event-wrapper:hover .user-hover-box {
    display: block;
}

.user-hover-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
}

.user-hover-info {
    display: flex;
    flex-direction: column;
    font-size: 0.85rem;
}

.user-hover-info small {
    color: #6b7280;
}

</style>

<div>
    <main class="admin-main">
        <div class="container-fluid p-4 p-lg-4">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-0">Calendrier</h1>
                    <p class="text-muted mb-0">Planifiez et suivez vos déplacements</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="window.location.href='/calendrier/export-pdf'">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <a href="/deplacements/create" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Nouveau Déplacement
                    </a>
                </div>
            </div>

            <div class="row g-4">
                
                <!-- Sidebar Gauche -->
                <div class="col-lg-3">
                    
                    <!-- Mini Calendar -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <a href="?mois=<?= $mois > 1 ? $mois-1 : 12 ?>&annee=<?= $mois > 1 ? $annee : $annee-1 ?>" class="btn btn-sm btn-light">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                                <h6 class="mb-0 fw-bold"><?= $mois_fr[$mois] ?> <?= $annee ?></h6>
                                <a href="?mois=<?= $mois < 12 ? $mois+1 : 1 ?>&annee=<?= $mois < 12 ? $annee : $annee+1 ?>" class="btn btn-sm btn-light">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                            
                            <!-- Mini Calendar Header -->
                            <div class="mini-calendar mb-2">
                                <?php foreach(['D','L','M','M','J','V','S'] as $jour): ?>
                                    <div class="text-center fw-semibold small text-muted"><?= $jour ?></div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Mini Calendar Days -->
                            <div class="mini-calendar">
                                <?php 
                                // Jours du mois précédent
                                for ($i = 0; $i < $jour_semaine_debut; $i++) {
                                    $prev_month_days = date('t', mktime(0, 0, 0, $mois-1, 1, $annee));
                                    $day = $prev_month_days - $jour_semaine_debut + $i + 1;
                                    echo "<div class='mini-day text-muted'>$day</div>";
                                }
                                
                                // Jours du mois actuel
                                for ($jour = 1; $jour <= $nb_jours; $jour++) {
                                    $date_courante = date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
                                    $is_today = ($date_courante === date('Y-m-d')) ? 'today' : '';
                                    $has_event = isset($deplacements_par_jour[$date_courante]) ? 'has-event' : '';
                                    echo "<div class='mini-day $is_today $has_event'>$jour</div>";
                                }
                                
                                // Jours du mois suivant pour remplir la grille
                                $total_cases = $jour_semaine_debut + $nb_jours;
                                $jours_suivants = (7 - ($total_cases % 7)) % 7;
                                for ($i = 1; $i <= $jours_suivants; $i++) {
                                    echo "<div class='mini-day text-muted'>$i</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Types de Déplacements -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold">Types de Déplacements</h6>
                        </div>
                        <div class="card-body p-3">
                            <?php 
                                           
                            $types = [
                                '#FFC107'=> ['label' => 'Mission commerciale', 'icon' => '🟡'],
                                '#4CAF50' => ['label' => 'Formation professionnelle', 'icon' => '📚'],
                                '#9C27B0' => ['label' => 'Visite client', 'icon' => '👤'],
                                '#8B4513' => ['label' => 'Réunion d\'affaires', 'icon' => '🏢'],
                                '#607D8B' => ['label' => 'Conférence', 'icon' => '🔧'],
                                '#2196F3' => ['label' => 'Salon professionnel', 'icon' => '🏬'],
                                '#00BCD4' => ['label' => 'Audit terrain', 'icon' => '🔍'],
                                '#E91E63' => ['label' => 'Installation technique', 'icon' => '⚙️'],
                                '#FF5722' => ['label' => 'Support client', 'icon' => '🔧'],
                                '#FF69B4' => ['label' => 'Prospection commerciale', 'icon' => '🎯'],
                                '#3B82F6' => ['label' => 'Autre (personnalisé)', 'icon' => '🔷'],
                            ];
                        
                            foreach ($types as $key => $info): 
                                $count = countByType($deplacements, $key);
                            ?>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-circle" style="width: 12px; height: 12px; background-color: <?= $key?>;"></span>
                                    <span class="small"><?= $info['label'] ?></span>
                                </div>
                                <span class="badge bg-light text-dark"><?= $count ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

                <!-- Calendrier Principal -->
                <div class="col-lg-9">
                    <div class="card shadow-sm">
                        
                        <!-- Header du calendrier -->
                        <div class="card-header bg-white p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="?mois=<?= $mois > 1 ? $mois-1 : 12 ?>&annee=<?= $mois > 1 ? $annee : $annee-1 ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                        <a href="?mois=<?= date('n') ?>&annee=<?= date('Y') ?>" class="btn btn-sm btn-outline-primary">Aujourd'hui</a>
                                        <a href="?mois=<?= $mois < 12 ? $mois+1 : 1 ?>&annee=<?= $mois < 12 ? $annee : $annee+1 ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <h5 class="mb-0 fw-bold"><?= $mois_fr[$mois] ?> <?= $annee ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Corps du calendrier -->
                        <div class="card-body p-0">
                            
                            <!-- En-tête des jours -->
                            <div class="calendar-grid">
                                <?php foreach(['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'] as $jour): ?>
                                    <div class="text-center py-3 fw-semibold bg-primary text-white border"><?= $jour ?></div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Grille du calendrier -->
                            <div class="calendar-grid">
                                <?php 
                                // Jours du mois précédent
                                $prev_month = $mois == 1 ? 12 : $mois - 1;
                                $prev_year = $mois == 1 ? $annee - 1 : $annee;
                                $prev_month_days = date('t', mktime(0, 0, 0, $prev_month, 1, $prev_year));
                                
                                for ($i = 0; $i < $jour_semaine_debut; $i++) {
                                    $day = $prev_month_days - $jour_semaine_debut + $i + 1;
                                    echo "<div class='calendar-day other-month'>
                                            <div class='fw-bold text-muted'>$day</div>
                                          </div>";
                                }
                                
                                // Jours du mois actuel
                                for ($jour = 1; $jour <= $nb_jours; $jour++) {
                                    $date_courante = date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
                                    $is_today = ($date_courante === date('Y-m-d')) ? 'today' : '';
                                    $events_du_jour = $deplacements_par_jour[$date_courante] ?? [];
                                    ?>
                                    <div class="calendar-day <?= $is_today ?>" 
                                         ondblclick="window.location.href='/deplacements/create?date=<?= $date_courante ?>'">
                                        
                                        <div class="fw-bold mb-1"><?= $jour ?></div>
                                        
                                        <!-- Affichage des déplacements -->
                                        <div class="d-flex flex-column gap-1">
                                            <?php 
                                            $max_visible = 3;
                                            foreach (array_slice($events_du_jour, 0, $max_visible) as $event): 
                                                $couleur = getCouleurType($event['type']);
                                                $badge_text = $event['is_debut'] ? '🚀 ' : '🏁 ';
                                                $badge_text .= htmlspecialchars(substr($event['title'], 0, 15));
                                                
                                                $tooltip = ($event['is_debut'] ? 'Début' : 'Fin') . ' : ' . htmlspecialchars($event['title']) . ' - ' . htmlspecialchars($event['lieu_destination']);
                                                if ($userRole === 'admin' || $userRole === 'manager') {
                                                    $tooltip .= ' (' . htmlspecialchars($event['employe'] ?? 'Utilisateur') . ')';
                                                }
                                            ?>
                                       <div class="event-wrapper">

    <div class="event-badge"
         style="background-color: <?= $couleur ?>;"
         onclick="window.location.href='/notes/<?= $event['id'] ?>'">

        <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
            <img src="<?= htmlspecialchars($event['avatar'] ?? '/assets/images/default-avatar.png') ?>"
                 alt="<?= htmlspecialchars($event['employe'] ?? 'U') ?>"
                 class="user-avatar-mini">
        <?php endif; ?>

        <span><?= $badge_text ?></span>
    </div>

    <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
        <!-- INFO UTILISATEUR (CACHÉE) -->
        <div class="user-hover-box">
            <img src="<?= htmlspecialchars($event['avatar'] ?? '/assets/images/default-avatar.png') ?>"
                 class="user-hover-avatar"
                 alt="">
            <div class="user-hover-info">
                <strong><?= htmlspecialchars($event['employe'] ?? 'Utilisateur') ?></strong>
                <small><?= htmlspecialchars($event['employe_role'] ?? '') ?></small>
                <?php if (!empty($event['employe_job_title'])): ?>
                    <small><?= htmlspecialchars($event['employe_job_title']) ?></small>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

                                            <?php endforeach; ?>
                                            
                                            <?php if (count($events_du_jour) > $max_visible): ?>
                                                <div class="event-badge" style="background-color: #6b7280">
                                                    <span>+<?= count($events_du_jour) - $max_visible ?> autre(s)</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Point orange si événements -->
                                        <?php if (!empty($events_du_jour)): ?>
                                            <div class="event-dot"></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                }
                                
                                // Jours du mois suivant pour remplir la grille
                                $total_cases = $jour_semaine_debut + $nb_jours;
                                $jours_suivants = (7 - ($total_cases % 7)) % 7;
                                for ($i = 1; $i <= $jours_suivants; $i++) {
                                    echo "<div class='calendar-day other-month'>
                                            <div class='fw-bold text-muted'>$i</div>
                                          </div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
document.querySelectorAll('.event-wrapper').forEach(wrapper => {
    const hoverBox = wrapper.querySelector('.user-hover-box');

    if (!hoverBox) return;

    wrapper.addEventListener('mouseenter', () => {
        const rect = wrapper.getBoundingClientRect();

        hoverBox.style.top = (rect.bottom - 150) + 'px';
        hoverBox.style.left = (rect.left - 650 )+'px';
        hoverBox.style.display = 'flex';
    });

    wrapper.addEventListener('mouseleave', () => {
        hoverBox.style.display = 'none';
    });
});
</script>
