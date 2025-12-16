<?php 
$title = "Carte du déplacement - " . htmlspecialchars($deplacement->getTitre());
ob_start(); 
?>

<!-- Main Content -->
<main class="admin-main">
  <div class="container-fluid p-4 p-lg-5">
    
    <!-- Header avec bouton retour -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="/employee">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/deplacements/<?= $userId ?>">Déplacements</a></li>
            <li class="breadcrumb-item active">Carte</li>
          </ol>
        </nav>
        <h1 class="h3 mb-0">
          <i class="bi bi-geo-alt-fill text-primary me-2"></i>
          <?= htmlspecialchars($deplacement->getTitre()) ?>
        </h1>
      </div>
      <a href="/notes/<?= $deplacement->getId() ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Retour aux détails
      </a>
    </div>

    <!-- Row principale -->
    <div class="row g-4">
      
      <!-- Carte (gauche - 70%) -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold">
                <i class="bi bi-map me-2"></i>Itinéraire du déplacement
              </h5>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary active" onclick="changeMapLayer('street')">
                  <i class="bi bi-map"></i> Plan
                </button>
                <button class="btn btn-outline-primary" onclick="changeMapLayer('satellite')">
                  <i class="bi bi-globe"></i> Satellite
                </button>
              </div>
            </div>
          </div>
          
          <div class="card-body p-0">
            <!-- La carte Leaflet -->
            <div id="map" style="height: 550px; width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <div class="d-flex align-items-center justify-content-center h-100" id="map-loader">
                <div class="text-center text-white">
                  <div class="spinner-border mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                  <h5 class="fw-bold">Chargement de la carte...</h5>
                  <p class="small opacity-75">Calcul de l'itinéraire en cours</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Barre d'infos sous la carte -->
          <div class="card-footer bg-light border-0 p-3">
            <div class="row text-center g-3">
              <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-center">
                  <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="bi bi-signpost-2 text-primary fs-4"></i>
                  </div>
                  <div class="text-start">
                    <small class="text-muted d-block">Distance</small>
                    <strong class="fs-5 text-primary" id="distance">
                      <span class="spinner-border spinner-border-sm"></span>
                    </strong>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-center">
                  <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="bi bi-clock text-success fs-4"></i>
                  </div>
                  <div class="text-start">
                    <small class="text-muted d-block">Durée estimée</small>
                    <strong class="fs-5 text-success" id="duration">
                      <span class="spinner-border spinner-border-sm"></span>
                    </strong>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-center">
                  <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                    <i class="bi bi-fuel-pump text-warning fs-4"></i>
                  </div>
                  <div class="text-start">
                    <small class="text-muted d-block">Frais estimés</small>
                    <strong class="fs-5 text-warning" id="fuelCost">-</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panneau d'infos (droite - 30%) -->
      <div class="col-lg-4">
        
        <!-- Infos du déplacement -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-primary bg-gradient text-white">
            <h6 class="mb-0 fw-bold">
              <i class="bi bi-info-circle me-2"></i>Informations
            </h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Départ</small>
              <div class="d-flex align-items-start">
                <i class="bi bi-geo-alt-fill text-success me-2 mt-1"></i>
                <div>
                  <strong class="d-block"><?= htmlspecialchars($deplacement->getLieuDepart()) ?></strong>
                  <small class="text-muted">
                    <?= date('d/m/Y', strtotime($deplacement->getDateDepart())) ?>
                  </small>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <div class="text-center text-muted">
                <i class="bi bi-arrow-down fs-4"></i>
              </div>
            </div>
            
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Destination</small>
              <div class="d-flex align-items-start">
                <i class="bi bi-geo-alt-fill text-danger me-2 mt-1"></i>
                <div>
                  <strong class="d-block"><?= htmlspecialchars($deplacement->getLieu()) ?></strong>
                  <small class="text-muted">
                    <?= date('d/m/Y', strtotime($deplacement->getDateRetour())) ?>
                  </small>
                </div>
              </div>
            </div>
            
            <hr>
            
            <div class="mb-3">
              <small class="text-muted d-block mb-1">Durée du séjour</small>
              <?php
              $debut = new DateTime($deplacement->getDateDepart());
              $fin = new DateTime($deplacement->getDateRetour());
              $duree = $debut->diff($fin)->days + 1;
              ?>
              <strong class="fs-5"><?= $duree ?> jour<?= $duree > 1 ? 's' : '' ?></strong>
            </div>
            
            <?php if ($deplacement->getObjet()): ?>
            <div>
              <small class="text-muted d-block mb-1">Objet</small>
              <p class="mb-0 small"><?= nl2br(htmlspecialchars($deplacement->getObjet())) ?></p>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Mode de transport détecté -->
        <div class="alert alert-info mb-4" id="transport-mode" style="display: none;">
          <i class="bi bi-info-circle me-2"></i>
          <small id="transport-text"></small>
        </div>

        <!-- Instructions -->
        <div class="card border-0 shadow-sm bg-light">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-lightbulb text-warning me-2"></i>Astuces
            </h6>
            <ul class="list-unstyled small mb-0">
              <li class="mb-2">
                <i class="bi bi-check-circle text-success me-2"></i>
                Cliquez sur les marqueurs pour plus d'infos
              </li>
              <li class="mb-2">
                <i class="bi bi-check-circle text-success me-2"></i>
                Les frais sont estimés selon le mode de transport
              </li>
              <li class="mb-2">
                <i class="bi bi-check-circle text-success me-2"></i>
                Distance calculée automatiquement
              </li>
              <li class="mb-0">
                <i class="bi bi-check-circle text-success me-2"></i>
                Géolocalisation par Photon (OpenStreetMap)
              </li>
            </ul>
          </div>
        </div>

        <!-- Actions rapides -->
        <div class="d-grid gap-2 mt-4">
          <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Imprimer la carte
          </button>
          <button class="btn btn-outline-secondary" onclick="shareMap()">
            <i class="bi bi-share me-2"></i>Partager l'itinéraire
          </button>
        </div>

      </div>
    </div>

  </div>
</main>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- JavaScript Carte - AVEC DÉTECTION AUTOMATIQUE DU MODE DE TRANSPORT -->
<script>
// ========== CONFIGURATION ==========
let map;
let currentLayer = 'street';
let mapInitialized = false;
let routePolyline = null;
let isAirRoute = false;

// Taux kilométrique en MAD/km (barème Maroc)
const RATE_PER_KM_MAD = 2.50; // 2,50 MAD par km (voiture)

// Récupération des données depuis PHP
const originCity = <?= json_encode($deplacement->getLieuDepart(), JSON_HEX_QUOT | JSON_HEX_APOS) ?>;
const destCity = <?= json_encode($deplacement->getLieu(), JSON_HEX_QUOT | JSON_HEX_APOS) ?>;

console.log('📍 Configuration:');
console.log('   Départ:', originCity);
console.log('   Destination:', destCity);

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
  console.log('🚀 Démarrage de l\'application carte...');
  
  // Vérifier que les adresses sont valides
  if (!originCity || !destCity || originCity.trim() === '' || destCity.trim() === '') {
    showError(
      'Adresses manquantes',
      'Le déplacement ne contient pas de lieu de départ ou de destination valide.',
      'Veuillez modifier le déplacement pour ajouter des adresses complètes.'
    );
    return;
  }
  
  // Initialiser la carte
  initMap();
  
  // Lancer le géocodage et le calcul d'itinéraire
  geocodeAndCalculateRoute();
});

// ========== INITIALISATION DE LA CARTE ==========
function initMap() {
  console.log('🗺️ Initialisation de la carte Leaflet...');
  
  try {
    // Créer la carte centrée sur l'Europe
    map = L.map('map', {
      center: [20, -80], // Centre Amérique/Caraïbes
      zoom: 4,
      zoomControl: true,
      scrollWheelZoom: true
    });
    
    // Couche de base (OpenStreetMap)
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 19
    });
    
    // Couche satellite (Esri)
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles © Esri',
      maxZoom: 19
    });
    
    // Ajouter la couche par défaut
    streetLayer.addTo(map);
    
    // Stocker les couches
    window.mapLayers = {
      street: streetLayer,
      satellite: satelliteLayer
    };
    
    mapInitialized = true;
    console.log('✅ Carte initialisée avec succès');
    
  } catch (error) {
    console.error('❌ Erreur initialisation carte:', error);
    showError('Erreur technique', 'Impossible d\'initialiser la carte.', error.message);
  }
}

// ========== GÉOCODAGE ET CALCUL D'ITINÉRAIRE ==========
async function geocodeAndCalculateRoute() {
  try {
    console.log('🔍 Démarrage du géocodage...');
    
    // Géocoder le départ
    console.log(`📍 Géocodage de "${originCity}"...`);
    const originCoords = await geocodeCity(originCity);
    console.log(`✅ Départ géocodé:`, originCoords);
    
    // Délai pour éviter de surcharger l'API
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Géocoder la destination
    console.log(`📍 Géocodage de "${destCity}"...`);
    const destCoords = await geocodeCity(destCity);
    console.log(`✅ Destination géocodée:`, destCoords);
    
    // Masquer le loader
    setTimeout(() => {
      const loader = document.getElementById('map-loader');
      if (loader) loader.style.display = 'none';
    }, 500);
    
    // Créer les marqueurs personnalisés
    const greenIcon = createMarkerIcon('#10b981');
    const redIcon = createMarkerIcon('#ef4444');
    
    // Ajouter les marqueurs sur la carte
    const originMarker = L.marker(originCoords, {icon: greenIcon})
      .addTo(map)
      .bindPopup(`
        <div class="text-center p-2">
          <strong class="d-block mb-1">🟢 Point de départ</strong>
          <span class="text-muted small">${originCity}</span>
        </div>
      `);
    
    const destMarker = L.marker(destCoords, {icon: redIcon})
      .addTo(map)
      .bindPopup(`
        <div class="text-center p-2">
          <strong class="d-block mb-1">🔴 Destination</strong>
          <span class="text-muted small">${destCity}</span>
        </div>
      `)
      .openPopup();
    
    // Essayer d'abord le calcul d'itinéraire routier
    console.log('🛣️ Tentative de calcul d\'itinéraire routier...');
    try {
      await calculateRoadRoute(originCoords, destCoords);
      console.log('✅ Itinéraire routier calculé');
    } catch (roadError) {
      console.log('⚠️ Pas d\'itinéraire routier disponible, passage en mode aérien');
      isAirRoute = true;
      
      // Calculer la distance aérienne
      calculateAirRoute(originCoords, destCoords);
      
      // Afficher le message de transport aérien
      showTransportMode('✈️ Transport aérien détecté - Distance calculée à vol d\'oiseau');
    }
    
    console.log('🎉 Carte affichée avec succès !');
    
  } catch (error) {
    console.error('❌ Erreur:', error);
    
    let errorTitle = 'Erreur de géolocalisation';
    let errorMessage = 'Impossible de calculer l\'itinéraire.';
    let errorDetails = '';
    
    if (error.message.includes('non trouvée')) {
      const cityName = error.message.split(':')[1]?.trim();
      errorTitle = 'Adresse introuvable';
      errorMessage = `L'adresse "${cityName}" est introuvable.`;
      errorDetails = 'Vérifiez l\'orthographe ou utilisez le format "Ville, Pays".';
    } else if (error.message.includes('HTTP') || error.message.includes('fetch')) {
      errorTitle = 'Erreur de connexion';
      errorMessage = 'Impossible de contacter le service de géolocalisation.';
      errorDetails = 'Vérifiez votre connexion internet et réessayez.';
    }
    
    showError(errorTitle, errorMessage, errorDetails);
  }
}

// ========== GÉOCODAGE VIA PHOTON API ==========
async function geocodeCity(cityName) {
  console.log(`🔍 Recherche de "${cityName}"...`);
  
  // URL de l'API Photon (OpenStreetMap, gratuit, sans clé)
  const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(cityName)}&limit=3&lang=fr`;
  
  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    
    if (!data.features || data.features.length === 0) {
      throw new Error(`Ville non trouvée: ${cityName}`);
    }
    
    // Photon retourne [longitude, latitude], on inverse pour Leaflet [lat, lon]
    const coords = data.features[0].geometry.coordinates;
    const result = [coords[1], coords[0]];
    
    // Afficher les infos du lieu trouvé
    const place = data.features[0].properties;
    console.log(`✅ Trouvé: ${place.name || place.city || cityName}`, place);
    
    return result;
    
  } catch (error) {
    console.error(`❌ Échec pour "${cityName}":`, error);
    throw error;
  }
}

// ========== CALCUL D'ITINÉRAIRE ROUTIER VIA OSRM ==========
async function calculateRoadRoute(originCoords, destCoords) {
  console.log('🛣️ Appel à l\'API OSRM pour le calcul d\'itinéraire routier...');
  
  // Format: lon,lat pour OSRM
  const startLonLat = `${originCoords[1]},${originCoords[0]}`;
  const endLonLat = `${destCoords[1]},${destCoords[0]}`;
  
  // API OSRM publique (gratuite, sans clé)
  const url = `https://router.project-osrm.org/route/v1/driving/${startLonLat};${endLonLat}?overview=full&geometries=geojson`;
  
  const response = await fetch(url);
  
  if (!response.ok) {
    throw new Error(`Route non disponible (${response.status})`);
  }
  
  const data = await response.json();
  
  if (!data.routes || data.routes.length === 0 || data.code !== 'Ok') {
    throw new Error('Aucun itinéraire routier trouvé');
  }
  
  const route = data.routes[0];
  
  // Distance en km
  const distanceKm = (route.distance / 1000).toFixed(0);
  
  // Durée en heures et minutes
  const durationSeconds = route.duration;
  const hours = Math.floor(durationSeconds / 3600);
  const minutes = Math.round((durationSeconds % 3600) / 60);
  
  // Frais kilométriques (barème Maroc : 2,50 MAD/km)
  const fuelCostMad = ((route.distance / 1000) * RATE_PER_KM_MAD).toFixed(2);
  
  console.log(`✅ Itinéraire routier: ${distanceKm} km, ${hours}h ${minutes}min, ${fuelCostMad} MAD`);
  
  // Tracer l'itinéraire sur la carte
  const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
  
  routePolyline = L.polyline(routeCoordinates, {
    color: '#667eea',
    weight: 4,
    opacity: 0.8,
    lineJoin: 'round'
  }).addTo(map);
  
  // Ajuster la vue
  map.fitBounds(routePolyline.getBounds(), {
    padding: [80, 80],
    maxZoom: 10
  });
  
  // Mettre à jour l'interface
  animateValue('distance', `${distanceKm} km`, 'text-primary');
  animateValue('duration', `${hours}h ${minutes}min`, 'text-success');
  animateValue('fuelCost', `${fuelCostMad} MAD`, 'text-warning');
  
  showTransportMode('🚗 Itinéraire routier - Frais à 2,50 MAD/km (barème Maroc)');
}

// ========== CALCUL D'ITINÉRAIRE AÉRIEN ==========
function calculateAirRoute(originCoords, destCoords) {
  console.log('✈️ Calcul de l\'itinéraire aérien...');
  
  // Distance à vol d'oiseau (formule de Haversine)
  const distance = calculateHaversineDistance(originCoords, destCoords);
  const distanceKm = distance.toFixed(0);
  
  // Pour un vol international, estimation de durée basée sur vitesse moyenne avion: 800 km/h
  // + 2h pour check-in, embarquement, récupération bagages
  const flightHours = distance / 800;
  const totalHours = flightHours + 2;
  const hours = Math.floor(totalHours);
  const minutes = Math.round((totalHours - hours) * 60);
  
  // Estimation des frais pour vol international
  // Utilisons 1,50 MAD/km comme estimation pour un vol
  const flightCostMad = (distance * 1.50).toFixed(2);
  
  console.log(`✅ Itinéraire aérien: ${distanceKm} km, ~${hours}h ${minutes}min (vol + escales), ~${flightCostMad} MAD`);
  
  // Tracer une ligne droite en pointillés
  routePolyline = L.polyline([originCoords, destCoords], {
    color: '#667eea',
    weight: 3,
    opacity: 0.6,
    dashArray: '10, 10',
    lineJoin: 'round'
  }).addTo(map);
  
  // Ajuster la vue
  map.fitBounds(routePolyline.getBounds(), {
    padding: [80, 80],
    maxZoom: 6
  });
  
  // Mettre à jour l'interface
  animateValue('distance', `${distanceKm} km`, 'text-primary');
  animateValue('duration', `~${hours}h ${minutes}min`, 'text-success');
  animateValue('fuelCost', `~${flightCostMad} MAD`, 'text-warning');
}

// ========== FORMULE DE HAVERSINE ==========
function calculateHaversineDistance(coord1, coord2) {
  const R = 6371; // Rayon de la Terre en km
  const lat1 = coord1[0] * Math.PI / 180;
  const lat2 = coord2[0] * Math.PI / 180;
  const deltaLat = (coord2[0] - coord1[0]) * Math.PI / 180;
  const deltaLon = (coord2[1] - coord1[1]) * Math.PI / 180;
  
  const a = Math.sin(deltaLat/2) * Math.sin(deltaLat/2) +
            Math.cos(lat1) * Math.cos(lat2) *
            Math.sin(deltaLon/2) * Math.sin(deltaLon/2);
  
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  
  return R * c; // Distance réelle à vol d'oiseau
}

// ========== AFFICHER LE MODE DE TRANSPORT ==========
function showTransportMode(message) {
  const alertDiv = document.getElementById('transport-mode');
  const textSpan = document.getElementById('transport-text');
  
  if (alertDiv && textSpan) {
    textSpan.textContent = message;
    alertDiv.style.display = 'block';
  }
}

// ========== CRÉATION D'ICÔNES DE MARQUEURS ==========
function createMarkerIcon(color) {
  const svgIcon = `
    <svg width="25" height="41" viewBox="0 0 25 41" xmlns="http://www.w3.org/2000/svg">
      <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 9.4 12.5 28.5 12.5 28.5S25 21.9 25 12.5C25 5.6 19.4 0 12.5 0zm0 17c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z" fill="${color}"/>
    </svg>
  `;
  
  return L.icon({
    iconUrl: 'data:image/svg+xml;base64,' + btoa(svgIcon),
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [0, -41]
  });
}

// ========== ANIMATION DES VALEURS ==========
function animateValue(elementId, value, colorClass) {
  const element = document.getElementById(elementId);
  if (!element) return;
  
  element.style.opacity = '0';
  element.style.transform = 'scale(0.8)';
  
  setTimeout(() => {
    element.innerHTML = `<span class="${colorClass}">${value}</span>`;
    element.style.transition = 'all 0.3s ease';
    element.style.opacity = '1';
    element.style.transform = 'scale(1)';
  }, 100);
}

// ========== CHANGER LE TYPE DE CARTE ==========
function changeMapLayer(type) {
  if (!mapInitialized) {
    console.warn('⚠️ Carte non initialisée');
    return;
  }
  
  // Retirer toutes les couches de tuiles
  map.eachLayer(layer => {
    if (layer instanceof L.TileLayer) {
      map.removeLayer(layer);
    }
  });
  
  // Ajouter la nouvelle couche
  window.mapLayers[type].addTo(map);
  currentLayer = type;
  
  // Mettre à jour les boutons
  document.querySelectorAll('.btn-group .btn').forEach(btn => {
    btn.classList.remove('active');
  });
  event.target.closest('button').classList.add('active');
  
  console.log('🗺️ Couche changée:', type);
}

// ========== AFFICHER UNE ERREUR ==========
function showError(title, message, details = '') {
  const mapElement = document.getElementById('map');
  
  mapElement.innerHTML = `
    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
      <div class="text-center p-5">
        <i class="bi bi-exclamation-triangle-fill text-warning mb-3" style="font-size: 5rem;"></i>
        <h4 class="mb-3">${title}</h4>
        <p class="text-muted mb-2">${message}</p>
        ${details ? `<p class="text-muted small mb-4">${details}</p>` : ''}
        <div class="d-flex gap-2 justify-content-center flex-wrap">
          <button class="btn btn-primary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-2"></i>Réessayer
          </button>
          <a href="/deplacements/<?= $userId ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour
          </a>
        </div>
      </div>
    </div>
  `;
  
  // Réinitialiser les stats
  document.getElementById('distance').innerHTML = '<span class="text-muted">-</span>';
  document.getElementById('duration').innerHTML = '<span class="text-muted">-</span>';
  document.getElementById('fuelCost').innerHTML = '<span class="text-muted">-</span>';
}

// ========== PARTAGER LA CARTE ==========
function shareMap() {
  const baseUrl = isAirRoute 
    ? `https://www.google.com/maps/dir/${encodeURIComponent(originCity)}/${encodeURIComponent(destCity)}`
    : `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${encodeURIComponent(originCity)};${encodeURIComponent(destCity)}`;
  
  if (navigator.share) {
    navigator.share({
      title: 'Itinéraire - <?= addslashes($deplacement->getTitre()) ?>',
      text: `Itinéraire de ${originCity} à ${destCity}`,
      url: baseUrl
    }).then(() => {
      console.log('✅ Partage réussi');
    }).catch((err) => {
      if (err.name !== 'AbortError') {
        fallbackShare(baseUrl);
      }
    });
  } else {
    fallbackShare(baseUrl);
  }
}

function fallbackShare(url) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(url).then(() => {
      alert('✅ Lien copié dans le presse-papier !\n\n' + url);
    }).catch(() => {
      prompt('Copiez ce lien:', url);
    });
  } else {
    prompt('Copiez ce lien:', url);
  }
}
</script>

<!-- CSS Personnalisé -->
<style>
/* Print styles */
@media print {
  .admin-sidebar, 
  .admin-header, 
  .card-footer, 
  .btn-group, 
  button, 
  .breadcrumb,
  .alert {
    display: none !important;
  }
  
  #map {
    height: 80vh !important;
    page-break-inside: avoid;
  }
  
  .card {
    border: 1px solid #ddd !important;
    box-shadow: none !important;
  }
}

/* Boutons de type de carte */
.btn-group .btn.active {
  background-color: #667eea;
  color: white;
  border-color: #667eea;
}

.btn-group .btn:hover:not(.active) {
  background-color: #f3f4f6;
}

/* Animation des statistiques */
#distance, #duration, #fuelCost {
  display: inline-block;
  transition: all 0.3s ease;
}

/* Popup Leaflet personnalisée */
.leaflet-popup-content-wrapper {
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.leaflet-popup-content {
  margin: 8px 12px;
  font-family: inherit;
}

/* Animation du loader */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
#map-loader {
  animation: pulse 2s ease-in-out infinite;
}
</style>
  