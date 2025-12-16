<?php
// === 1. Démarrer la session EN PREMIER (c'est vital) ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === 2. Vérifier la connexion sans déclencher de redirect ===
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;

// === 3. Charger Auth seulement si besoin (pour user() et les rôles) ===
if ($isLoggedIn && !class_exists('App\\Helpers\\Auth', false)) {
    require_once __DIR__ . '/../../app/Helpers/Auth.php';
}
use App\Helpers\Auth;

// === 4. NOUVEAU : Préparer les notifications ===
$notifications = $notifications ?? [];
$unreadCount = 0;
foreach ($notifications as $notif) {
    if (!$notif->isRead()) {
        $unreadCount++;
    }
}

// === 5. FONCTION HELPER : Formater la date ===
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return "À l'instant";
    if ($diff < 3600) return floor($diff / 60) . " min";
    if ($diff < 86400) return floor($diff / 3600) . " h";
    if ($diff < 604800) return floor($diff / 86400) . " j";
    return date('d/m/Y', $timestamp);
}

// === 6. FONCTION HELPER : Icône selon le type ===
function getNotificationIcon($type) {
    $icons = [
        'success' => 'check-circle-fill text-success',
        'warning' => 'exclamation-triangle-fill text-warning',
        'danger'  => 'x-circle-fill text-danger',
        'info'    => 'info-circle-fill text-info'
    ];
    return $icons[$type] ?? 'bell-fill text-secondary';
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Frais depelacement</title>

    <!-- SEO Meta Tags -->
    <meta
      name="description"
      content="Comprehensive product management with inventory tracking, categories, and analytics"
    />
        <meta name="keywords" content="bootstrap, admin, dashboard, calendar, events, scheduling">

    <meta
      name="keywords"
      content="bootstrap, admin, dashboard, products, inventory, e-commerce"
    />
    <link
      rel="icon"
      type="image/svg+xml"
      href="../bootstrap-5.3.7-dist/js/favicon-CvUZKS4z.svg"
    />
    <link
      rel="icon"
      type="image/png"
      href="../bootstrap-5.3.7-dist/js/favicon-B_cwPWBd.png"
    />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- PWA Manifest -->
    <link
      rel="manifest"
      href="../bootstrap-5.3.7-dist/js/manifest-DTaoG9pG.json"
    />

    <!-- Preload critical fonts -->
    <link
      rel="preload"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      as="style"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    
<!-- ========== STYLES CSS POUR LES NOTIFICATIONS ========== -->
<style>
/* Animation du bouton de notification */
#notifBtn {
  position: relative;
  border: 2px solid #e5e7eb;
}

#notifBtn:hover {
  background-color: #f9fafb;
  border-color: #667eea;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

#notifBtn i {
  transition: transform 0.3s ease;
}

#notifBtn:hover i {
  animation: bellRing 0.5s ease-in-out;
}

@keyframes bellRing {
  0%, 100% { transform: rotate(0deg); }
  10%, 30% { transform: rotate(-10deg); }
  20%, 40% { transform: rotate(10deg); }
  50% { transform: rotate(0deg); }
}

/* Badge notification pulsant */
#notifCount {
  animation: pulse 2s infinite;
  box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}
/* Positionner le logout tout en bas de la sidebar */
.sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    padding-bottom: 1rem;
}

.sidebar-nav .nav {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.sidebar-nav .nav .mt-auto {
    margin-top: auto !important;
}

/* Style du lien logout */
.sidebar-nav .nav-link.text-danger {
    font-weight: 500;
    border-radius: 8px;
    margin: 0 8px;
    padding: 10px 12px;
}

.sidebar-nav .nav-link.text-danger:hover {
    background-color: rgba(220, 38, 38, 0.1);
    color: #dc2626 !important;
    transform: translateX(4px);
}

.sidebar-nav .nav-link.text-danger i {
    color: #dc2626;
}
/* Dropdown menu animations */
.dropdown-menu {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Item de notification */
.notification-item {
  transition: all 0.2s ease;
}

.notification-item:hover {
  background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
}

/* État non lu */
.notification-item .unread {
  background-color: rgba(102, 126, 234, 0.05);
  border-left: 3px solid #667eea;
}

.notification-item .unread:hover {
  background-color: rgba(102, 126, 234, 0.08);
}

/* État lu */
.notification-item .read {
  opacity: 0.7;
}

.notification-item .read:hover {
  opacity: 1;
}

/* Icône de notification avec gradient */
.notification-icon {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Tronquer le texte sur 2 lignes - SUPPRIMÉ, on utilise le wrap maintenant */

/* Empêcher le scroll horizontal */
#notifList {
  overflow-x: hidden !important;
  overflow-y: auto;
}

.dropdown-menu {
  overflow-x: hidden !important;
}

.notification-content {
  overflow: hidden;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.notification-content strong,
.notification-content p {
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: normal;
  hyphens: auto;
}

/* Scrollbar personnalisée - uniquement verticale */
#notifList::-webkit-scrollbar {
  width: 6px;
}

#notifList::-webkit-scrollbar-track {
  background: #f1f5f9;
}

#notifList::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

#notifList::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* État vide avec animation */
.empty-state {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Boutons d'action rapide */
.notification-item .btn-link:hover {
  text-decoration: underline !important;
}

/* Responsive */
@media (max-width: 576px) {
  .dropdown-menu {
    width: 100vw !important;
    max-width: 380px;
    overflow-x: hidden !important;
  }
  
  .notification-content {
    max-width: calc(100% - 60px);
  }
}
</style>

    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script src="/bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap-5.3.7-dist/js/calendar-BW3gHHAd.js"></script>
    <script src="../../bootstrap-5.3.7-dist/js/calendar-BW3gHHAd.js"></script>
    <script src="./bootstrap-5.3.7-dist/js/calendar-BW3gHHAd.js"></script>
    <link rel="stylesheet" href="./#d1d5dbbootstrap-5.3.7-dist/css/bootstrap.min.css" />

    <script
  
      type="module"
      crossorigin
      src="./bootstrap-5.3.7-dist/js/messages-s3H8zlRi.js"
    ></script>    <script
  
      type="module"
      crossorigin
      src="/bootstrap-5.3.7-dist/js/messages-s3H8zlRi.js"
    ></script>
    <script
      type="module"
      crossorigin
      src="../bootstrap-5.3.7-dist/js/messages-s3H8zlRi.js"
    ></script>
    <link rel="stylesheet" href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../../bootstrap-5.3.7-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/bootstrap-5.3.7-dist/css/bootstrap.min.css" />
    <!-- Favicon -->
    <link
      rel="icon"
      type="image/svg+xml"
      href="./assets/favicon-CvUZKS4z.svg"
    />
    <link rel="icon" type="image/png" href="../assets/favicon-CvUZKS4z.png" />

    <!-- PWA Manifest -->
    <link rel="manifest" href="../assets/manifest-DTaoG9pG.json" />

    <!-- Preload critical fonts -->
    <link
      rel="preload"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      as="style"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <!-- ApexCharts CDN -->
    <script
      type="module"
      crossorigin
      src="../../bootstrap-5.3.7-dist/js/products-Bf5ZuPHt.js"
    ></script>
      <link
      rel="icon"
      type="image/svg+xml"
      href="bootstrap-5.3.7-dist/js/favicon-CvUZKS4z.svg"
    />
    <link
      rel="icon"
      type="image/png"
      href="bootstrap-5.3.7-dist/js/favicon-CvUZKS4z.png"
    />

    <!-- PWA Manifest -->
    <link
      rel="manifest"
      href="bootstrap-5.3.7-dist/js/manifest-DTaoG9pG.json"
    />

    <!-- Preload critical fonts -->
    <link
      rel="preload"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      as="style"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <script src="../../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>

    <script
      type="module"
      crossorigin
      src="./bootstrap-5.3.7-dist/js/messages-s3H8zlRi.js"
    ></script>
    <link rel="stylesheet" href="../../bootstrap-5.3.7-dist/css/bootstrap.min.css" />
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="./assets/favicon-CvUZKS4z.png">
    <link rel="icon" type="image/png" href="./assets/favicon-CvUZKS4z.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="./assets/manifest-DTaoG9pG.json">
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">  
  <script type="module" crossorigin src="../../bootstrap-5.3.7-dist/js/calendar-BW3gHHAd.js"></script>
  <script type="module" crossorigin src="/bootstrap-5.3.7-dist/js/calendar-BW3gHHAd.js"></script>

    <!-- Custom CSS for Flash Messages -->
    <style>
        /* Flash Messages Container */
        .flash-messages-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            width: calc(100% - 40px);
        }

        .flash-message {
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 8px;
            animation: slideInRight 0.4s ease-out;
            display: flex;
            align-items: center;
        }

        /* Animation d'entrée */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Auto-dismiss animation */
        .flash-message.fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }
/* User Menu Styles */
.dropdown button:hover {
    background-color: #f9fafb !important;
    border-color: #d1d5db !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.dropdown-menu {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-item {
    display: flex;
    align-items: center;
}

.dropdown-item:hover {
    background-color: #f3f4f6 !important;
    transform: translateX(4px);
}

.dropdown-item:active {
    background-color: #e5e7eb !important;
}

.dropdown-item i {
    font-size: 1.1rem;
}

/* Avatar glow effect on hover */
.dropdown button:hover img {
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
}

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        /* Responsive */
        @media (max-width: 576px) {
            .flash-messages-container {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
                width: calc(100% - 20px);
            }
        }
    </style>
    <style>
    /* === STYLES SIDEBAR - LIEN ACTIF EN BLANC === */
    .sidebar-nav .nav-link {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 4px 8px;
        padding: 10px 12px;
    }



    .sidebar-nav .nav-link.active i {
        color: white !important;
    }



    /* Badges dans sidebar */
    .sidebar-nav .badge {
        font-size: 0.75em;
    }
</style>

 </head>
<body<?php if ($view === 'calendrier/index'): ?>
    data-page="calendar" class="calendar-page"
    
<?php endif; ?><?php if ($view === 'messagerie/index'): ?>
data-page="messages" class="messages-page"    
<?php endif; ?>>

    <!-- Flash Messages - DOIT ÊTRE EN PREMIER -->
    <div class="flash-messages-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <!-- Admin App Container -->
    <div class="admin-app">
      <div class="admin-wrapper" id="admin-wrapper">
        <!-- Header -->
        <header class="admin-header">
          <nav
            class="navbar navbar-expand-lg navbar-light bg-white border-bottom"
          >
            <div class="container-fluid">
              <!-- Logo/Brand -->
              <a
                class="navbar-brand d-flex align-items-center"
                href="./index.html"
              >
                <img
                  src="<?='../../assets/Logo.png' ?? '/assets/Logo.png'?>"
                  alt="Logo"
                  height="50"
                  width="50"
                  class="d-inline-block align-text-top me-2"
                />
                <h6 class="h6 mb-0 fw-bold text-primary">MOROCODEMOVE</h6>
              </a>

              <!-- Search Bar with Alpine.js -->
              <div
                class="search-container flex-grow-1 mx-4"
              >
                <div class="position-relative">
                  <input
                    type="search"
                    class="rform-control"
                    placeholder="Search... (Ctrl+K)"
                  />
                  <i
                    class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3"
                  ></i>

                  <!-- Search Results Dropdown -->
                  <div

                    class="position-absolute top-100 start-0 w-100 bg-white border rounded-2 shadow-lg mt-1 z-3"
                  >
                    <template>
                      <a
                        class="d-block px-3 py-2 text-decoration-none text-dark border-bottom"
                      >
                        <div class="d-flex align-items-center">
                          <i class="bi bi-file-text me-2 text-muted"></i>
                          <small
                            class="ms-auto text-muted"
                          ></small>
                        </div>
                      </a>
                    </template>
                  </div>
                </div>
              </div>

              <!-- Right Side Icons -->
              <div class="navbar-nav flex-row">
                <!-- Theme Toggle with Alpine.js -->
                <div x-data="themeSwitch">
                  <button
                    class="btn btn-outline-secondary me-2"
                    type="button"
                    @click="toggle()"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title="Toggle theme"
                  >
                    <i
                      class="bi bi-sun-fill"
                      x-show="currentTheme === 'light'"
                    ></i>
                    <i
                      class="bi bi-moon-fill"
                      x-show="currentTheme === 'dark'"
                    ></i>
                  </button>
                </div>

                <!-- Fullscreen Toggle -->
                <button
                  class="btn btn-outline-secondary me-2"
                  type="button"
                  data-fullscreen-toggle
                  data-bs-toggle="tooltip"
                  data-bs-placement="bottom"
                  title="Toggle fullscreen"
                >
                  <i class="bi bi-arrows-fullscreen icon-hover"></i>
                </button><!-- ========== NOTIFICATIONS DROPDOWN - DESIGN MODERNE ========== -->
<div class="dropdown me-2">
  <button class="btn btn-outline-secondary position-relative rounded-circle p-2" 
          type="button" 
          data-bs-toggle="dropdown" 
          id="notifBtn"
          style="width: 42px; height: 42px; transition: all 0.3s ease;">
    <i class="bi bi-bell fs-5">
    <!-- Badge pour nouvelles notifications -->
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" 
          id="notifCount"
          >
      
    </span>
   </i>
  </button>
  
  <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" 
      style="width: 380px; border-radius: 16px; overflow: hidden;">
    
    <!-- En-tête moderne avec gradient -->
    <li class="sticky-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <div class="dropdown-header d-flex justify-content-between align-items-center text-white py-3 px-4">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-bell-fill"></i>
          <h6 class="mb-0 fw-bold">Notifications</h6>
        </div>
      </div>
    </li>
    
    <!-- Liste des notifications avec scroll vertical uniquement -->
    <div id="notifList" style="max-height: 420px; overflow-y: auto; overflow-x: hidden;">
      <?php if (empty($notifications)): ?>
        <!-- État vide avec illustration -->
        <li class="text-center py-5">
          <div class="empty-state">
            <div class="mb-3" style="font-size: 4rem; opacity: 0.2;">
              <i class="bi bi-bell-slash"></i>
            </div>
            <p class="text-muted mb-1 fw-semibold">Aucune notification</p>
            <small class="text-muted">Vous êtes à jour !</small>
          </div>
        </li>
      <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
          <li class="notification-item border-bottom">
            <a href="#" 
               class="dropdown-item p-0 <?= $notif->isRead() ? 'read' : 'unread' ?>" 
               data-notif-id="<?= $notif->getId() ?>"
               onclick="markAsRead(<?= $notif->getId() ?>)"
               style="transition: all 0.3s ease;">
              
              <div class="d-flex align-items-start p-3 gap-3 position-relative">
                <!-- Indicateur non lu -->
                <?php if (!$notif->isRead()): ?>
                  <span class="unread-indicator position-absolute top-50 start-0 translate-middle-y bg-primary rounded-circle" 
                        style="width: 8px; height: 8px; margin-left: 10px;"></span>
                <?php endif; ?>
                
                <!-- Icône avec badge coloré -->
                <div class="notification-icon flex-shrink-0" 
                     style="width: 48px; height: 48px;">
                  <div class="rounded-circle d-flex align-items-center justify-content-center h-100"
                       style="background: <?= 
                         $notif->getType() === 'success' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 
                         ($notif->getType() === 'danger' ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' :
                         ($notif->getType() === 'warning' ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' :
                         'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)')) ?>;">
                    <i class="bi bi-<?= getNotificationIcon($notif->getType()) ?> text-white fs-5"></i>
                  </div>
                </div>
                
                <!-- Contenu -->
                <div class="flex-grow-1 notification-content" style="min-width: 0; max-width: 100%;">
                  <div class="d-flex justify-content-between align-items-start mb-1 gap-2">
                    <strong class="flex-grow-1 notif-title" style="font-size: 0.9rem; word-wrap: break-word; line-height: 1.3;">
                      <?= htmlspecialchars($notif->getTitle()) ?>
                    </strong>
                    <!-- Badge de temps -->
                    <span class="badge bg-light text-muted rounded-pill flex-shrink-0 notif-badge" 
                          style="font-size: 0.7rem; font-weight: 500; white-space: nowrap;">
                      <?= timeAgo($notif->getCreatedAt()) ?>
                    </span>
                  </div>
                  
                  <p class="mb-2 text-muted small notif-message" style="font-size: 0.8rem; line-height: 1.5; word-wrap: break-word; white-space: normal;">
                    <?= htmlspecialchars($notif->getMessage()) ?>
                  </p>
                  
                  <!-- Actions rapides (optionnel) -->
                  <div class="d-flex gap-2 flex-wrap">
                                     


                  </div>
                </div>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <!-- Footer avec bouton "Voir tout" -->
    <li class="border-top">

    </li>
  </ul>
</div>

<!-- ========== JAVASCRIPT POUR LES INTERACTIONS ========== -->
<script>
// Marquer une notification comme lue
function markAsRead(notifId) {
  fetch(`/notifications/${notifId}/read`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Mettre à jour visuellement
      const notifItem = document.querySelector(`[data-notif-id="${notifId}"]`);
      if (notifItem) {
        notifItem.classList.remove('unread');
        notifItem.classList.add('read');
        
        // Retirer l'indicateur non lu
        const indicator = notifItem.querySelector('.unread-indicator');
        if (indicator) {
          indicator.remove();
        }
      }
    }
  })
  .catch(error => console.error('Erreur:', error));
}

// Marquer toutes les notifications comme lues
function markAllAsRead() {
  fetch('/notifications/mark-all-read', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Rafraîchir la page ou mettre à jour l'UI
      location.reload();
    }
  })
  .catch(error => console.error('Erreur:', error));
}

// Animation du badge lors de nouvelles notifications
function animateNotificationBadge() {
  const badge = document.getElementById('notifCount');
  if (badge && !badge.classList.contains('d-none')) {
    badge.style.animation = 'none';
    setTimeout(() => {
      badge.style.animation = 'pulse 2s infinite';
    }, 10);
  }
}

// Réinitialiser le compteur quand le dropdown s'ouvre
document.getElementById('notifBtn')?.addEventListener('shown.bs.dropdown', function() {
  const countBadge = document.getElementById('notifCount');
  if (countBadge) {
    setTimeout(() => {
      countBadge.classList.add('d-none');
      newNotificationsCount = 0;
    }, 300);
  }
});
</script>

<script>
// ========== WEBSOCKET NOTIFICATIONS - COMPTEUR UNIQUEMENT POUR NOUVELLES ==========
const socket = new WebSocket('ws://localhost:8080');
let newNotificationsCount = 0; // ✅ Compteur séparé pour WebSocket uniquement

<?php if ($isLoggedIn && isset($_SESSION['user_id'])): ?>
const userId = <?= json_encode((int)$_SESSION['user_id']) ?>;
console.log('🔐 User authentifié:', userId);
<?php else: ?>
console.warn('⚠️ Utilisateur non connecté, notifications désactivées');
<?php endif; ?>

socket.onopen = () => {
  console.log('✅ WebSocket connecté');
  
  if (userId !== null) {
    socket.send(JSON.stringify({
      type: 'auth', 
      user_id: userId
    }));
    console.log('🔑 Authentification envoyée pour userId:', userId);
  } else {
    console.error('❌ Impossible de s\'authentifier: userId non défini');
  }
};

socket.onerror = (err) => {
  console.error('❌ Erreur WebSocket:', err);
};

socket.onclose = (e) => {
  console.warn('🔌 WebSocket fermé', e.code, e.reason);
  
  if (userId !== null) {
    setTimeout(() => {
      console.log('🔄 Tentative de reconnexion...');
      window.location.reload();
    }, 3000);
  }
};

socket.onmessage = (e) => {
  console.log('📩 Message reçu:', e.data);
  
  try {
    const d = JSON.parse(e.data);
    
    if (d.type === 'notification') {
      console.log('🔔 Notification:', d.title);
      
      // ✅ 1. Incrémenter UNIQUEMENT le compteur WebSocket
      newNotificationsCount++;
      
      const countBadge = document.getElementById('notifCount');
      if (countBadge) {
        countBadge.textContent = newNotificationsCount;
        countBadge.classList.remove('d-none');
      }
      
      // ✅ 2. Déterminer le gradient selon le type de notification
      let gradientStyle = '';
      let iconName = '';
      
      switch(d.icon) {
        case 'success':
          gradientStyle = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
          iconName = 'check-circle-fill';
          break;
        case 'danger':
          gradientStyle = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
          iconName = 'x-circle-fill';
          break;
        case 'warning':
          gradientStyle = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
          iconName = 'exclamation-triangle-fill';
          break;
        default:
          gradientStyle = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
          iconName = 'info-circle-fill';
      }
      
      // 3. Créer le HTML de la nouvelle notification
      const notifHTML = `
        <li class="notification-item border-bottom">
          <a href="#" class="dropdown-item p-0 unread" data-notif-id="${d.id || 'new'}" style="transition: all 0.3s ease;">
            <div class="d-flex align-items-start p-3 gap-3 position-relative">
              <span class="unread-indicator position-absolute top-50 start-0 translate-middle-y bg-primary rounded-circle" 
                    style="width: 8px; height: 8px; margin-left: 10px;"></span>
              
              <div class="notification-icon flex-shrink-0" style="width: 48px; height: 48px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center h-100"
                     style="background: ${gradientStyle};">
                  <i class="bi bi-${iconName} text-white fs-5"></i>
                </div>
              </div>
              
              <div class="flex-grow-1 notification-content" style="min-width: 0; max-width: 100%;">
                <div class="d-flex justify-content-between align-items-start mb-1 gap-2">
                  <strong class="flex-grow-1 notif-title" style="font-size: 0.9rem; word-wrap: break-word; line-height: 1.3;">
                    ${escapeHtml(d.title)}
                  </strong>
                  <span class="badge bg-light text-muted rounded-pill flex-shrink-0 notif-badge" 
                        style="font-size: 0.7rem; font-weight: 500; white-space: nowrap;">
                    ${d.time || "À l'instant"}
                  </span>
                </div>
                
                <p class="mb-2 text-muted small notif-message" style="font-size: 0.8rem; line-height: 1.5; word-wrap: break-word; white-space: normal;">
                  ${escapeHtml(d.message)}
                </p>
              </div>
            </div>
          </a>
        </li>
      `;
      
      // 4. Ajouter dans la liste
      const list = document.getElementById('notifList');
      if (list) {
        // Vérifier si c'est l'état vide
        const emptyState = list.querySelector('.empty-state');
        if (emptyState) {
          list.innerHTML = notifHTML;
        } else {
          list.insertAdjacentHTML('afterbegin', notifHTML);
        }
      }
      
      // 5. Son de notification (optionnel)
      try {
        const audio = new Audio('/sounds/notif.mp3');
        audio.volume = 0.5;
        audio.play().catch(() => console.log('🔇 Son désactivé'));
      } catch (e) {
        console.log('🔇 Son non disponible');
      }
      
      // 6. Animation subtile du bouton
      const notifBtn = document.getElementById('notifBtn');
      if (notifBtn) {
        notifBtn.classList.add('animate__animated', 'animate__headShake');
        setTimeout(() => {
          notifBtn.classList.remove('animate__animated', 'animate__headShake');
        }, 1000);
      }
    }
  } catch (err) {
    console.error('❌ Erreur parsing notification:', err);
  }
};

// ✅ ÉVÉNEMENT : Quand le dropdown s'ouvre, reset le compteur
const notifBtnElement = document.getElementById('notifBtn');
if (notifBtnElement) {
  notifBtnElement.addEventListener('shown.bs.dropdown', function() {
    console.log('📂 Dropdown ouvert, reset du compteur');
    
    // Réinitialiser le compteur
    newNotificationsCount = 0;
    
    // Cacher le badge
    const countBadge = document.getElementById('notifCount');
    if (countBadge) {
      countBadge.classList.add('d-none');
    }
  });
}

// Fonction helper pour échapper HTML (sécurité XSS)
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Debug: afficher l'état de connexion toutes les 10 secondes
setInterval(() => {
  console.log('📊 État WebSocket:', socket.readyState, {
    0: 'CONNECTING',
    1: 'OPEN',
    2: 'CLOSING',
    3: 'CLOSED'
  }[socket.readyState]);
}, 10000);
</script>


            <!-- User Menu -->
<div class="dropdown">
  <button
    class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-3 py-2"
    type="button"
    data-bs-toggle="dropdown"
    aria-expanded="false"
    style="border: 2px solid #e5e7eb; transition: all 0.3s ease;"
  >
    <img
      src="<?= isset($avatarUrl) && $avatarUrl ? $avatarUrl : "data:image/svg+xml,%3csvg%20width='32'%20height='32'%20viewBox='0%200%2032%2032'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3c!--%20Background%20circle%20--%3e%3ccircle%20cx='16'%20cy='16'%20r='16'%20fill='url( %23avatarGradient )'/%3e%3c!--%20Person%20silhouette%20--%3e%3cg%20fill='white'%20opacity='0.9'%3e%3c!--%20Head%20--%3e%3ccircle%20cx='16'%20cy='12'%20r='5'/%3e%3c!--%20Body%20--%3e%3cpath%20d='M16%2018c-5.5%200-10%202.5-10%207v1h20v-1c0-4.5-4.5-7-10-7z'/%3e%3c/g%3e%3c!--%20Subtle%20border%20--%3e%3ccircle%20cx='16'%20cy='16'%20r='15.5'%20fill='none'%20stroke='rgba( 255, 255, 255, 0.2 )'%20stroke-width='1'/%3e%3c!--%20Gradient%20definition%20--%3e%3cdefs%3e%3clinearGradient%20id='avatarGradient'%20x1='0%25'%20y1='0%25'%20x2='100%25'%20y2='100%25'%3e%3cstop%20offset='0%25'%20style='stop-color:%236b7280;stop-opacity:1'%20/%3e%3cstop%20offset='100%25'%20style='stop-color:%234b5563;stop-opacity:1'%20/%3e%3c/linearGradient%3e%3c/defs%3e%3c/svg%3e" ?>"
      alt="User Avatar"
      width="32"
      height="32"
      class="rounded-circle me-2"
      style="border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
    />
    <span class="d-none d-md-inline fw-semibold">
      <?= $isLoggedIn ? htmlspecialchars(Auth::user()->getNom()) : 'Guest' ?>
    </span>
    <i class="bi bi-chevron-down ms-2" style="font-size: 0.75rem;"></i>
  </button>
  
  <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 220px;">
    <li>
      <a class="dropdown-item rounded-3 py-2 px-3 mb-1" href="/profile" style="transition: all 0.2s ease;">
        <i class="bi bi-person me-2 text-primary"></i>
        <span>Profile</span>
      </a>
    </li>
    <li>
      <a class="dropdown-item rounded-3 py-2 px-3 mb-1" href="/settings" style="transition: all 0.2s ease;">
        <i class="bi bi-gear me-2 text-primary"></i>
        <span>Settings</span>
      </a>
    </li>
    <li><hr class="dropdown-divider my-2" style="opacity: 0.1;" /></li>
    <li>
      <?php if ($isLoggedIn): ?>
        <a class="dropdown-item rounded-3 py-2 px-3 text-danger" href='/logout' style="transition: all 0.2s ease;">
          <i class="bi bi-box-arrow-right me-2"></i>
          <span>Logout</span>
        </a>
      <?php else: ?>
        <a href='/login' class='dropdown-item rounded-3 py-2 px-3 text-primary fw-semibold'>
          <i class="bi bi-box-arrow-in-right me-2"></i>
          <span>Connexion</span>
        </a>
      <?php endif; ?>
    </li>
  </ul>
</div>
              </div>
            </div>
          </nav>
        </header>
        <div class="fix">
          <!-- Sidebar -->
          <aside class="admin-sidebar" id="admin-sidebar">
            <div class="sidebar-content">
              <?php
// ========================================
// 1. FONCTION HELPER DANS LE HEADER
// Ajouter AVANT le HTML dans header.php
// ========================================

/**
 * Vérifie si une route est active
 */
function isActive($route, $exact = false) {
    $currentPath = $_SERVER['REQUEST_URI'];
    
    // Nettoyer le path (enlever query string)
    $currentPath = strtok($currentPath, '?');
    
    if ($exact) {
        // Correspondance exacte
        return $currentPath === $route;
    } else {
        // Correspondance partielle (commence par)
        return strpos($currentPath, $route) === 0;
    }
}

/**
 * Retourne 'active' si la route correspond
 */
function navActive($route, $exact = false) {
    return isActive($route, $exact) ? 'active' : '';
}

// ========================================
// 2. NAVIGATION CORRIGÉE (remplacer dans header.php)
// ========================================
?>

<nav class="sidebar-nav">
    <ul class="nav flex-column">
        <?php if ($isLoggedIn): ?>
            <?php
                $homeLink = '/employee';
                if (Auth::user()) {
                    if (Auth::isAdmin()) {
                        $homeLink = '/admin';
                    } elseif (Auth::hasRole(['manager'])) {
                        $homeLink = '/manager';
                    }
                }
            ?>
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= navActive($homeLink, true) ?>" href="<?= $homeLink ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                    <?php if (navActive($homeLink, true)): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Analytics -->

            
            <!-- Users (Admin only) -->
            <?php if (Auth::isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/users') ?>" href="/users">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                    <?php if (navActive('/users')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
            <!-- Les demandes (TOUTES) - Admin uniquement -->
            <?php if (Auth::isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/demandes')?> <?=navActive('/admin/note') ?>" href="/demandes">
                    <i class="bi bi-list-check"></i>
                    <span>Les demandes</span>
                    <?php if (navActive('/demandes')||navActive('/admin/note')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>  
            <?php
$isActiveee = (
    (navActive('/admin/categories') && !navActive('/admin/categories/create', true))
    || navActive('/admin/categories/edit')
);
?>

            <?php if (Auth::isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isActiveee ? 'active' : '' ?>" href="/admin/categories">
                    <i class="bi bi-list-check"></i>
                    <span>Categories</span>
        <?php if ($isActiveee): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
        <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
              <?php if (Auth::isAdmin()): ?>

            <li class="nav-item">
                <a class="nav-link <?= navActive('/admin/categories/create', true) ?>" href="/admin/categories/create">
                    <i class="bi bi-plus-circle"></i>
                    <span>Créer catagorie</span>
                    <?php if (navActive('/admin/categories/create', true)): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
           <?php endif; ?>

            <!-- Demandes équipe - Manager uniquement -->
            <?php if (Auth::hasRole(['manager'])): ?>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/manager/deplacements-equipe') ?>" href="/manager/deplacements-equipe">
                    <i class="bi bi-people"></i>
                    <span>Demandes équipe</span>
                    <?php if (navActive('/manager/deplacements-equipe')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <!-- Attribuer demandes - Manager uniquement -->
            <?php if (Auth::hasRole(['manager'])): ?>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/deplacements/attribuer') ?>" href="/deplacements/attribuer">
                    <i class="bi bi-person-plus"></i>
                    <span>Attribuer deplacements</span>
                    <?php if (navActive('/deplacements/attribuer')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
            <!-- Products -->
      
            
            <!-- Manager (Manager & Admin only) -->
            
            <!-- Créer Déplacement -->
           <?php if (!Auth::hasRole(['manager']) && !Auth::isAdmin()): ?>

            <li class="nav-item">
                <a class="nav-link <?= navActive('/deplacements/create', true) ?>" href="/deplacements/create">
                    <i class="bi bi-plus-circle"></i>
                    <span>Créer déplacement</span>
                    <?php if (navActive('/deplacements/create', true)): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
           <?php endif; ?>         

<?php
$isActiveeee = (
    (navActive('/deplacements') && !navActive('/deplacements/create', true))
    || navActive('/notes')
    || navActive('/historique/deplacement')
);
?>

            <!-- Mes Déplacements -->
                        <?php if (!Auth::hasRole(['manager']) && !Auth::isAdmin()): ?>

           <li class="nav-item">
    <a class="nav-link <?= $isActiveeee ? 'active' : '' ?>"
       href="/deplacements/<?= $userId ?? '' ?>">
       
        <i class="bi bi-airplane"></i>
        <span>Mes déplacements</span>

        <?php if ($isActiveeee): ?>
            <span class="badge bg-primary rounded-pill ms-auto">Active</span>
        <?php endif; ?>
    </a>
</li>
           <?php endif; ?>


            
            <!-- Messages -->
            <li class="nav-item">
                <a class="nav-link <?= navActive('/messagerie') ?>" href="/messagerie">
                    <i class="bi bi-chat-dots"></i>
                    <span>Messages</span>
                  
                    <?php if (navActive('/messagerie') ): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Calendrier -->
            <li class="nav-item">
                <a class="nav-link <?= navActive('/calendrier') ?>" href="/calendrier">
                    <i class="bi bi-calendar-event"></i>
                    <span>Calendrier</span>
                    <?php if (navActive('/calendrier')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Assistant IA -->
            <?php if (Auth::hasRole(['manager', 'admin'])): ?>
            <li class="nav-item">
                <a class="nav-link <?= navActive('/assistant') ?>" href="/assistant">
                    <i class="bi bi-robot"></i>
                    <span>Assistant IA</span>
                    <?php if (navActive('/assistant')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Settings -->

            
            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link <?= navActive('/profile') ?>" href="/profile">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                    <?php if (navActive('/profile')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= navActive('/settings') ?>" href="/settings">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                    <?php if (navActive('/settings')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>
            <!-- Help & Support -->
            <!-- Help & Support -->
             <?php
$isActiveeeeee = (
    (navActive('/suppor') && !navActive('/support/tickets', true))
);
?>
            <li class="nav-item">
                <a class="nav-link <?= $isActiveeeeee ? 'active' : '' ?>" href="/support">
                    <i class="bi bi-question-circle"></i>
                    <span>Help & Support</span>
                    <?php if ($isActiveeeeee): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>            <li class="nav-item">
                <a class="nav-link <?= navActive('/support/tickets') ?>" href="/support/tickets">
                    <i class="bi bi-list-check"></i>
                    <span>Mes tickets</span>
                    <?php if (navActive('/support/tickets')): ?>
                        <span class="badge bg-primary rounded-pill ms-auto">Active</span>
                    <?php endif; ?>
                </a>
            </li>

            <?php if ($isLoggedIn): ?>
                <!-- Séparateur avant Logout -->
                <li class="nav-item mt-auto pt-4">
                    <hr class="dropdown-divider mx-3 opacity-25">
                </li>

                <!-- Lien Logout en bas -->
                <li class="nav-item pb-3">
                    <a class="nav-link text-danger fw-medium" href="/logout">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        <span>Déconnexion</span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</nav>

<?php
// ========================================
// 3. VERSION ALTERNATIVE AVEC ARRAY DE ROUTES
// Plus maintenable pour beaucoup de liens
// ========================================

$navItems = [
    [
        'label' => 'Dashboard',
        'icon' => 'speedometer2',
        'url' => $homeLink,
        'exact' => true,
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Analytics',
        'icon' => 'graph-up',
        'url' => '/analytics',
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Users',
        'icon' => 'people',
        'url' => '/users',
        'roles' => ['admin']
    ],
    [
        'label' => 'Manager',
        'icon' => 'bag-check',
        'url' => '/manager',
        'roles' => ['admin', 'manager']
    ],
    [
        'label' => 'Créer déplacement',
        'icon' => 'plus-circle',
        'url' => '/deplacements/create',
        'exact' => true,
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Mes déplacements',
        'icon' => 'airplane',
        'url' => '/deplacements',
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Messages',
        'icon' => 'chat-dots',
        'url' => '/messagerie',
        'badge' => 3, // Nombre de messages non lus
        'badgeClass' => 'bg-danger',
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Calendrier',
        'icon' => 'calendar-event',
        'url' => '/calendrier',
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Assistant IA',
        'icon' => 'robot',
        'url' => '/assistant',
        'roles' => ['admin', 'manager']
    ],
    [
        'label' => 'Settings',
        'icon' => 'gear',
        'url' => '/settings',
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Profile',
        'icon' => 'person',
        'url' => '/profile',
        'roles' => ['admin', 'manager', 'employee']
    ],
    [
        'label' => 'Help & Support',
        'icon' => 'question-circle',
        'url' => '/help',
        'roles' => ['admin', 'manager', 'employee']
    ]
];

/**
 * Vérifie si l'utilisateur a l'un des rôles requis
 */
function hasAnyRole($requiredRoles) {
    if (!Auth::check()) return false;
    $userRole = Auth::user()->getRole();
    return in_array($userRole, $requiredRoles);
}

?>


<?php
// ========================================
// 4. CSS POUR L'ÉTAT ACTIVE (ajouter dans header.php)
// ========================================
?>
<style>
/* État actif de la navigation */
.nav-link {
    transition: all 0.3s ease;
    position: relative;
}

.nav-link.active {
    background-color: rgba(99, 102, 241, 0.1);
    color: #6366f1;
    font-weight: 600;
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 0 4px 4px 0;
}

.nav-link.active i {
    color: #6366f1;
    transform: scale(1.1);
}

.nav-link:hover:not(.active) {
    background-color: rgba(0, 0, 0, 0.03);
    transform: translateX(3px);
}

/* Badge styles */
.badge.bg-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
}

.badge.bg-danger {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>
            </div>
          </aside>
        </div>

        <!-- Floating Hamburger Menu -->
        <button
          class='hamburger-menu'
          type='button'
          data-sidebar-toggle
          aria-label='Toggle sidebar'
        >
          <i class='bi bi-list'></i>
        </button>




        <script>
      document.addEventListener("DOMContentLoaded", () => {
        const toggleButton = document.querySelector("[data-sidebar-toggle]");
        const wrapper = document.getElementById("admin-wrapper");

        if (toggleButton && wrapper) {
          const isCollapsed =
            localStorage.getItem("sidebar-collapsed") === "true";
          if (isCollapsed) {
            wrapper.classList.add("sidebar-collapsed");
            toggleButton.classList.add("is-active");
          }

          toggleButton.addEventListener("click", () => {
            const isCurrentlyCollapsed =
              wrapper.classList.contains("sidebar-collapsed");

            if (isCurrentlyCollapsed) {
              wrapper.classList.remove("sidebar-collapsed");
              toggleButton.classList.remove("is-active");
              localStorage.setItem("sidebar-collapsed", "false");
            } else {
              wrapper.classList.add("sidebar-collapsed");
              toggleButton.classList.add("is-active");
              localStorage.setItem("sidebar-collapsed", "true");
            }
          });
        }
      });
    </script>