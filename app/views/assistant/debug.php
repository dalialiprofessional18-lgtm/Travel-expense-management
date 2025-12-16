
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contexte Temps Réel - Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', monospace;
        }
        .json-container {
            background: #252526;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #3e3e42;
            overflow-x: auto;
        }
        .json-key {
            color: #9cdcfe;
        }
        .json-string {
            color: #ce9178;
        }
        .json-number {
            color: #b5cea8;
        }
        .json-boolean {
            color: #569cd6;
        }
        .refresh-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .stats-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #0e639c;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-database"></i>
                Contexte Temps Réel - Base de Données
                <span class="stats-badge" id="lastUpdate">Chargement...</span>
            </h1>
            <button class="btn btn-primary refresh-btn" onclick="loadContext()">
                <i class="bi bi-arrow-clockwise"></i> Actualiser
            </button>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card bg-dark text-white mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Statistiques Rapides</h6>
                        <div id="quickStats">Chargement...</div>
                    </div>
                </div>
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h6 class="card-title">Structure des Données</h6>
                        <ul class="list-unstyled" id="dataStructure"></ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="json-container">
                    <pre id="jsonOutput">Chargement des données...</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        let contextData = null;

        async function loadContext() {
            try {
                document.getElementById('lastUpdate').textContent = 'Chargement...';
                
                const response = await fetch('/assistant/context-debug');
                contextData = await response.json();
                
                // Afficher le JSON formaté
                document.getElementById('jsonOutput').textContent = 
                    JSON.stringify(contextData, null, 2);
                
                // Mettre à jour les stats rapides
                updateQuickStats(contextData);
                
                // Mettre à jour la structure
                updateDataStructure(contextData);
                
                // Timestamp
                document.getElementById('lastUpdate').textContent = 
                    'Mis à jour: ' + new Date().toLocaleTimeString('fr-FR');
                
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('jsonOutput').textContent = 
                    'Erreur de chargement: ' + error.message;
            }
        }

        function updateQuickStats(data) {
            const stats = data.statistics;
            let html = '';

            if (stats.users) {
                html += `<div class="mb-2">
                    <strong>👥 Utilisateurs:</strong> ${stats.users.total}
                </div>`;
            }

            if (stats.deplacements) {
                html += `<div class="mb-2">
                    <strong>✈️ Déplacements:</strong> ${stats.deplacements.total}
                </div>`;
            }

            if (stats.notes_frais) {
                html += `<div class="mb-2">
                    <strong>📋 Notes:</strong> ${stats.notes_frais.total}
                </div>`;
                html += `<div class="mb-2">
                    <strong>⏳ En attente:</strong> ${stats.notes_frais.soumis || 0}
                </div>`;
            }

            if (stats.equipe) {
                html += `<div class="mb-2">
                    <strong>👥 Équipe:</strong> ${stats.equipe.total_membres}
                </div>`;
            }

            if (stats.notes_en_attente) {
                html += `<div class="mb-2">
                    <strong>⏳ À valider:</strong> ${stats.notes_en_attente.total}
                </div>`;
            }

            document.getElementById('quickStats').innerHTML = html || 'Aucune donnée';
        }

        function updateDataStructure(data) {
            const structure = [];
            
            function analyzeObject(obj, prefix = '') {
                for (let key in obj) {
                    if (typeof obj[key] === 'object' && obj[key] !== null && !Array.isArray(obj[key])) {
                        structure.push({
                            name: prefix + key,
                            type: 'object',
                            count: Object.keys(obj[key]).length
                        });
                        analyzeObject(obj[key], prefix + key + '.');
                    } else if (Array.isArray(obj[key])) {
                        structure.push({
                            name: prefix + key,
                            type: 'array',
                            count: obj[key].length
                        });
                    }
                }
            }

            analyzeObject(data);

            const html = structure.map(item => `
                <li style="margin-bottom: 8px;">
                    <span style="color: #9cdcfe;">${item.name}</span>
                    <small style="color: #6a9955;">
                        (${item.type}: ${item.count} ${item.type === 'array' ? 'items' : 'props'})
                    </small>
                </li>
            `).join('');

            document.getElementById('dataStructure').innerHTML = html || '<li>Aucune structure</li>';
        }

        // Auto-refresh toutes les 30 secondes
        setInterval(loadContext, 30000);
        
        // Charger au démarrage
        loadContext();
    </script>
</body>
</html>

<?php
// ========================================
// EXEMPLE DE SORTIE JSON
// ========================================
/*
{
  "user": {
    "id": 2,
    "nom": "Mohamed",
    "email": "mohamed@sgfd.ma",
    "role": "manager"
  },
  "timestamp": "2024-12-10 21:30:45",
  "statistics": {
    "equipe": {
      "total_membres": 5,
      "membres": [
        {"id": 3, "nom": "Ahmed", "email": "ahmed@sgfd.ma"},
        {"id": 4, "nom": "Fatima", "email": "fatima@sgfd.ma"}
      ]
    },
    "deplacements_equipe": {
      "total": 12,
      "en_cours": 3,
      "this_month": 5
    },
    "notes_en_attente": {
      "total": 2,
      "montant_total": 4250.00,
      "details": [
        {
          "id": 15,
          "user_nom": "Ahmed",
          "destination": "Rabat",
          "montant_total": 2500.00,
          "created_at": "2024-12-08 14:30:00"
        },
        {
          "id": 16,
          "user_nom": "Fatima",
          "destination": "Marrakech",
          "montant_total": 1750.00,
          "created_at": "2024-12-09 10:15:00"
        }
      ]
    },
    "performance": {
      "taux_validation": 85.5,
      "delai_moyen_validation": 2.3
    }
  },
  "data": {
    "notes_a_valider": [...],
    "recent_validations": [...],
    "equipe_performance": [...]
  }
}
*/
?>