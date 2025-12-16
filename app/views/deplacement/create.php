<?php $title = 'Nouveau déplacement — Créer une mission'; ?>
<?php ob_start(); ?>

<style>
  /* Ton style magnifique reste identique */
  :root {
    --card-bg: #ffffff;
    --card-border: #e2e8f0;
    --text-primary: #1e293b;
    --text-muted: #64748b;
    --input-bg: #ffffff;
    --input-border: #cbd5e1;
    --header-gradient-start: #6366f1;
    --header-gradient-end: #4f46e5;
    --accent: #10b981;
  }

  [data-bs-theme="dark"] {
    --card-bg: #1e293b;
    --card-border: #334155;
    --text-primary: #f8fafc;
    --text-muted: #94a3b8;
    --input-bg: #0f172a;
    --input-border: #334155;
    --header-gradient-start: #818cf8;
    --header-gradient-end: #6366f1;
    --accent: #34d399;
  }

  .glass-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 28px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); transition: all 0.4s ease; }
  [data-bs-theme="dark"] .glass-card { box-shadow: 0 25px 50px rgba(0,0,0,0.6); }
  .glass-card:hover { transform: translateY(-10px); }

  .preview-card {
    background: var(--card-bg);
    border: 2px solid var(--accent);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(16,185,129,0.2);
    position: sticky;
    top: 100px;
    height: fit-content;
  }

  .preview-item { opacity: 0; transform: translateY(15px); transition: all 0.5s ease; }
  .preview-item.filled { opacity: 1; transform: translateY(0); }

  .btn-create {
    background: linear-gradient(135deg, var(--accent), #059669);
    border: none;
    border-radius: 18px;
    padding: 1.2rem 3rem;
    font-weight: 700;
    font-size: 1.3rem;
    box-shadow: 0 15px 35px rgba(16,185,129,0.4);
    transition: all 0.4s ease;
  }
  .btn-create:hover {
    transform: translateY(-6px);
    box-shadow: 0 30px 60px rgba(16,185,129,0.6);
  }

  .form-control, .form-select {
    background-color: var(--input-bg);
    border: 2px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 18px;
    padding: 1rem 1.4rem;
    font-size: 1.1rem;
  }
</style>

<main class="admin-main py-5">
  <div class="container-fluid px-4 px-lg-5">


    <div class="row g-5">
      <div class="col-lg-8">
        <div class="glass-card">
          <div class="card-body p-5">
              <h1 class="display-10">Créer un déplacement</h1>

            <form action="/deplacements/store" method="POST" id="deplacementForm">

              <!-- Titre -->
              <div class="mb-5">
                <label class="form-label fw-bold fs-5 mb-4 text-primary">Titre de la mission</label>
                <select id="typeDeplacement" name="titre_temp" class="form-select form-select-lg" required>
                  <option value="">-- Choisir un type --</option>
                  <option value="Mission commerciale">Mission commerciale</option>
                  <option value="Formation professionnelle">Formation professionnelle</option>
                  <option value="Visite client">Visite client</option>
                  <option value="Réunion d'affaires">Réunion d'affaires</option>
                  <option value="Conférence">Conférence</option>
                  <option value="Salon professionnel">Salon professionnel</option>
                  <option value="Audit terrain">Audit terrain</option>
                  <option value="Installation technique">Installation technique</option>
                  <option value="Support client">Support client</option>
                  <option value="Prospection commerciale">Prospection commerciale</option>
                  <option value="autre">Autre (personnalisé)</option>
                </select>

                <input type="text" id="titreCustom" name="titre" class="form-control form-control-lg mt-3 d-none" placeholder="Votre titre personnalisé...">
                <!-- Champ caché final pour titre -->
                <input type="hidden" name="titre" id="finalTitre">
              </div>

              <div class="row g-4">
                <div class="col-lg-6">
                  <label class="form-label fw-bold fs-5 mb-4 text-primary">Lieu de départ</label>
                  <div id="lieuDepartSelector"></div>
                  <!-- Champ caché obligatoire pour envoi -->
                  <input type="hidden" name="lieu_depart" id="finalLieuDepart" required>
                </div>
                <div class="col-lg-6">
                  <label class="form-label fw-bold fs-5 mb-4 text-primary">Lieu de destination</label>
                  <div id="lieuDestinationSelector"></div>
                  <input type="hidden" name="lieu" id="finalLieuDest" required>
                </div>

                <div class="col-lg-6">
                  <label class="form-label fw-bold fs-5 mb-4 text-primary">Date de départ</label>
                  <input type="date" name="date_depart" id="dateDepart" class="form-control form-control-lg" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-lg-6">
                  <label class="form-label fw-bold fs-5 mb-4 text-primary">Date de retour</label>
                  <input type="date" name="date_retour" id="dateRetour" class="form-control form-control-lg" min="<?= date('Y-m-d') ?>" required>
                </div>
              </div>

              <div class="mt-5">
                <label class="form-label fw-bold fs-5 mb-4 text-primary">Objet (facultatif)</label>
                <textarea name="objet" id="objetInput" rows="5" class="form-control form-control-lg" placeholder="Décrivez les objectifs..."></textarea>
              </div>

            </form>
          </div>
        </div>
      </div>

      <!-- Carte récapitulative droite -->
      <div class="col-lg-4">
        <div class="preview-card p-5">
          <div class="text-center mb-5">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 90px; height: 90px;">
              <i class="bi bi-eye fs-1"></i>
            </div>
            <h4 class="fw-bold">Aperçu en direct</h4>
            <small class="text-muted">Tout est prêt pour l’enregistrement</small>
          </div>

          <hr class="border-opacity-25">

          <div class="preview-content fs-6">
            <div id="previewTitre" class="preview-item mb-4">
              <strong>Titre :</strong><br>
              <span class="text-muted">Aucun titre</span>
            </div>

            <div id="previewDepart" class="preview-item mb-4">
              <strong>Départ :</strong><br>
              <span class="text-muted">Non renseigné</span>
            </div>

            <div id="previewDestination" class="preview-item mb-4">
              <strong>Destination :</strong><br>
              <span class="text-muted">Non renseignée</span>
            </div>

            <div id="previewDates" class="preview-item mb-4">
              <strong>Période :</strong><br>
              <span class="text-muted">Aucune date</span>
            </div>

            <div id="previewObjet" class="preview-item">
              <strong>Objet :</strong><br>
              <span class="text-muted fst-italic">Aucun objet</span>
            </div>
          </div>

          <hr class="mt-5 border-opacity-25">

          <button type="submit" form="deplacementForm" class="btn btn-create w-100" id="submitBtn" disabled>
            Créer la mission
          </button>
          <small class="text-muted d-block text-center mt-2">Remplissez tous les champs pour activer</small>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- LocationSelector intégré + preview + sécurisation envoi -->
<script>
class LocationSelector {
  constructor(containerId, finalInputId, previewId) {
    this.container = document.getElementById(containerId);
    this.finalInput = document.getElementById(finalInputId);
    this.preview = document.getElementById(previewId);
    this.init();
  }

  async init() {
    this.container.innerHTML = `
      <select class="form-select country-select mb-3" required>
        <option value="">Chargement des pays...</option>
      </select>
      <div class="city-container" style="display:none;">
        <select class="form-select city-select" disabled>
          <option>Chargement des villes...</option>
        </select>
        <div class="spinner-border spinner-border-sm text-primary mt-2" style="display:none;"></div>
      </div>
    `;

    await this.loadCountries();
  }

  async loadCountries() {
    try {
      const res = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
      const data = await res.json();
      this.countries = data.map(c => ({code: c.cca2, name: c.name.common})).sort((a,b) => a.name.localeCompare(b.name));

      const select = this.container.querySelector('.country-select');
      select.innerHTML = `<option value="">Sélectionnez un pays</option>`;
      this.countries.forEach(c => select.add(new Option(c.name, c.code)));
    } catch {
      this.container.querySelector('.country-select').innerHTML = `<option value="">Erreur réseau</option>`;
    }

    this.attachEvents();
  }

  attachEvents() {
    const country = this.container.querySelector('.country-select');
    const city = this.container.querySelector('.city-select');
    const spinner = this.container.querySelector('.spinner-border');

    country.addEventListener('change', async () => {
      const code = country.value;
      const name = country.selectedOptions[0]?.text || '';

      if (!code) {
        this.container.querySelector('.city-container').style.display = 'none';
        this.finalInput.value = '';
        this.updatePreview('Non renseigné');
        checkFormComplete();
        return;
      }

      this.container.querySelector('.city-container').style.display = 'block';
      spinner.style.display = 'inline-block';
      city.disabled = true;

      try {
        const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({country: name})
        });
        const data = await res.json();
        const cities = (data.data || []).sort();

        city.innerHTML = '<option value="">Choisissez une ville</option>';
        cities.forEach(c => city.add(new Option(c, c)));
        city.disabled = false;
      } catch {
        city.innerHTML = '<option value="">Indisponible</option>';
      }
      spinner.style.display = 'none';
    });

    city.addEventListener('change', () => {
      const ville = city.value;
      const pays = country.selectedOptions[0]?.text || '';
      const full = ville ? `${ville}, ${pays}` : '';
      this.finalInput.value = full;
      this.updatePreview(full || 'Non renseigné', !!full);
      checkFormComplete();
    });
  }

  updatePreview(text, success = false) {
    this.preview.innerHTML = `<strong>${this.preview.querySelector('strong').textContent}</strong><br>
      <span class="${success ? 'text-success fw-bold' : 'text-muted'}">${text}</span>`;
    if (success) this.preview.classList.add('filled');
    else this.preview.classList.remove('filled');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new LocationSelector('lieuDepartSelector', 'finalLieuDepart', 'previewDepart');
  new LocationSelector('lieuDestinationSelector', 'finalLieuDest', 'previewDestination');

  const selectTitre = document.getElementById('typeDeplacement');
  const customInput = document.getElementById('titreCustom');
  const finalTitre = document.getElementById('finalTitre');

  selectTitre.addEventListener('change', function () {
    if (this.value === 'autre') {
      customInput.classList.remove('d-none');
      customInput.disabled = false;
      customInput.required = true;
      customInput.focus();
      this.name = '';
      updateTitrePreview('Personnalisé...');
    } else if (this.value) {
      customInput.classList.add('d-none');
      customInput.value = '';
      this.name = 'titre_temp';
      finalTitre.value = this.value;
      updateTitrePreview(this.selectedOptions[0].text, true);
    }
    checkFormComplete();
  });

  customInput.addEventListener('input', function () {
    finalTitre.value = this.value;
    updateTitrePreview(this.value || 'Personnalisé...', true);
    checkFormComplete();
  });

  function updateTitrePreview(text, success = false) {
    const el = document.getElementById('previewTitre');
    el.innerHTML = `<strong>Titre :</strong><br><span class="${success ? 'text-success fw-bold' : 'text-muted'}">${text}</span>`;
    if (success) el.classList.add('filled');
  }

  // Dates
  const updateDates = () => {
    const d1 = document.getElementById('dateDepart').value;
    const d2 = document.getElementById('dateRetour').value;
    const el = document.getElementById('previewDates');
    if (d1 && d2) {
      const f1 = new Date(d1).toLocaleDateString('fr-FR', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
      const f2 = new Date(d2).toLocaleDateString('fr-FR', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
      el.innerHTML = `<strong>Période :</strong><br><span class="text-success fw-bold">Du ${f1}<br>au ${f2}</span>`;
      el.classList.add('filled');
    } else {
      el.innerHTML = `<strong>Période :</strong><br><span class="text-muted">Aucune date</span>`;
      el.classList.remove('filled');
    }
    checkFormComplete();
  };
  document.getElementById('dateDepart').addEventListener('change', updateDates);
  document.getElementById('dateRetour').addEventListener('change', updateDates);

  // Objet
  document.getElementById('objetInput').addEventListener('input', function () {
    const el = document.getElementById('previewObjet');
    if (this.value.trim()) {
      el.innerHTML = `<strong>Objet :</strong><br><span class="text-dark">${this.value}</span>`;
      el.classList.add('filled');
    } else {
      el.innerHTML = `<strong>Objet :</strong><br><span class="text-muted fst-italic">Aucun objet</span>`;
      el.classList.remove('filled');
    }
  });

  // Activer le bouton seulement quand tout est rempli
  function checkFormComplete() {
    const titre = finalTitre.value.trim();
    const depart = document.getElementById('finalLieuDepart').value.trim();
    const dest = document.getElementById('finalLieuDest').value.trim();
    const d1 = document.getElementById('dateDepart').value;
    const d2 = document.getElementById('dateRetour').value;

    const complete = titre && depart && dest && d1 && d2;
    document.getElementById('submitBtn').disabled = !complete;
  }
});
</script>

