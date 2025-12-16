/**
 * Location Selector - Version avec API GeoNames (gratuite et complète)
 * Couvre TOUS les pays avec TOUTES leurs villes
 */

class LocationSelector {
  constructor(containerId, inputName, label, defaultValue = null) {
    this.container = document.getElementById(containerId);
    this.inputName = inputName;
    this.label = label;
    this.defaultValue = defaultValue;
    this.countries = [];
    this.cities = [];
    
    this.init();
  }
  
  async init() {
    await this.loadCountries();
    this.render();
    
    if (this.defaultValue) {
      this.parseAndSetDefault(this.defaultValue);
    }
  }
  
  async loadCountries() {
    try {
      const response = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
      const data = await response.json();
      
      this.countries = data
        .map(country => ({
          code: country.cca2,
          name: country.name.common
        }))
        .sort((a, b) => a.name.localeCompare(b.name, 'fr'));
      
      console.log('✅ Pays chargés:', this.countries.length);
    } catch (error) {
      console.error('❌ Erreur chargement pays:', error);
      this.countries = this.getFallbackCountries();
    }
  }
  
  async loadCities(countryCode) {
    try {
      console.log('🔄 Chargement des villes pour:', countryCode);
      
      // MÉTHODE 1: GeoNames API (GRATUITE - sans clé pour lecture)
      const cities = await this.fetchCitiesFromGeoNames(countryCode);
      
      if (cities.length > 0) {
        this.cities = cities;
        console.log(`✅ ${this.cities.length} villes chargées via GeoNames`);
        return;
      }
      
      // MÉTHODE 2: CountriesNow API (alternative gratuite)
      const citiesFromCountriesNow = await this.fetchCitiesFromCountriesNow(countryCode);
      
      if (citiesFromCountriesNow.length > 0) {
        this.cities = citiesFromCountriesNow;
        console.log(`✅ ${this.cities.length} villes chargées via CountriesNow`);
        return;
      }
      
      // MÉTHODE 3: Fallback avec les principales villes
      console.warn('⚠️ APIs ne répondent pas, utilisation du fallback');
      this.cities = this.getFallbackCities(countryCode);
      
    } catch (error) {
      console.error('❌ Erreur chargement villes:', error);
      this.cities = this.getFallbackCities(countryCode);
    }
  }
  
  async fetchCitiesFromGeoNames(countryCode) {
    try {
      // GeoNames API - GRATUITE sans inscription (limité à 1000 résultats)
      // Pour plus de 1000 villes, inscrivez-vous sur geonames.org
      const url = `https://secure.geonames.org/searchJSON?country=${countryCode}&featureClass=P&maxRows=1000&orderby=population&username=demo`;
      
      const response = await fetch(url);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data.geonames && data.geonames.length > 0) {
        // Dédupliquer et trier
        const cities = [...new Set(data.geonames.map(city => city.name))];
        return cities.sort((a, b) => a.localeCompare(b, 'fr'));
      }
      
      return [];
    } catch (error) {
      console.error('Erreur GeoNames:', error.message);
      return [];
    }
  }
  
  async fetchCitiesFromCountriesNow(countryCode) {
    try {
      // Obtenir le nom du pays depuis le code
      const country = this.countries.find(c => c.code === countryCode);
      if (!country) return [];
      
      // CountriesNow API - GRATUITE et sans clé
      const url = `https://countriesnow.space/api/v0.1/countries/cities`;
      
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          country: country.name
        })
      });
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data.data && Array.isArray(data.data)) {
        return data.data.sort((a, b) => a.localeCompare(b, 'fr'));
      }
      
      return [];
    } catch (error) {
      console.error('Erreur CountriesNow:', error.message);
      return [];
    }
  }
  
  render() {
    this.container.innerHTML = `
      <div class="location-selector">
        <label class="form-label fw-semibold">${this.label}</label>
        
        <!-- Select Pays -->
        <div class="mb-3">
          <select class="form-select country-select" required>
            <option value="">Sélectionnez un pays...</option>
            ${this.countries.map(country => 
              `<option value="${country.code}">${country.name}</option>`
            ).join('')}
          </select>
        </div>
        
        <!-- Select Ville -->
        <div class="city-container" style="display: none;">
          <div class="position-relative">
            <select class="form-select city-select" required disabled>
              <option value="">🏙️ Sélectionnez une ville...</option>
            </select>
            <div class="position-absolute top-50 end-0 translate-middle-y me-3">
              <div class="spinner-border spinner-border-sm text-primary" style="display: none;"></div>
            </div>
          </div>
          <small class="text-muted city-count" style="display: none;">📊 <span></span> villes disponibles</small>
        </div>
        
        <!-- Input caché pour la valeur finale -->
        <input type="hidden" name="${this.inputName}" class="final-input" value="" required>
        
        <small class="text-muted d-block mt-2">📍 Format: Ville, Pays</small>
      </div>
    `;
    
    this.attachEvents();
    this.initSelect2();
  }
  
  attachEvents() {
    const countrySelect = this.container.querySelector('.country-select');
    const citySelect = this.container.querySelector('.city-select');
    const cityContainer = this.container.querySelector('.city-container');
    const spinner = this.container.querySelector('.spinner-border');
    const finalInput = this.container.querySelector('.final-input');
    const cityCount = this.container.querySelector('.city-count');
    const cityCountSpan = cityCount.querySelector('span');
    
    countrySelect.addEventListener('change', async (e) => {
      const countryCode = e.target.value;
      const countryName = e.target.options[e.target.selectedIndex].text;
      
      if (!countryCode) {
        cityContainer.style.display = 'none';
        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="">🏙️ Sélectionnez une ville...</option>';
        finalInput.value = '';
        cityCount.style.display = 'none';
        return;
      }
      
      cityContainer.style.display = 'block';
      spinner.style.display = 'inline-block';
      citySelect.disabled = true;
      citySelect.innerHTML = '<option value="">⏳ Chargement des villes...</option>';
      cityCount.style.display = 'none';
      
      await this.loadCities(countryCode);
      
      spinner.style.display = 'none';
      
      if (this.cities.length > 0) {
        citySelect.innerHTML = `
          <option value="">🏙️ Sélectionnez une ville...</option>
          ${this.cities.map(city => 
            `<option value="${city}">${city}</option>`
          ).join('')}
        `;
        citySelect.disabled = false;
        
        cityCountSpan.textContent = this.cities.length;
        cityCount.style.display = 'block';
        
        if (window.jQuery && $.fn.select2) {
          $(citySelect).select2('destroy');
          this.initSelect2();
        }
      } else {
        citySelect.innerHTML = `<option value="">❌ Aucune ville disponible pour ${countryName}</option>`;
        cityCount.style.display = 'none';
      }
    });
    
    citySelect.addEventListener('change', (e) => {
      const cityName = e.target.value;
      const countryName = countrySelect.options[countrySelect.selectedIndex].text;
      
      if (cityName && countryName) {
        finalInput.value = `${cityName}, ${countryName}`;
        console.log('✅ Localisation sélectionnée:', finalInput.value);
      } else {
        finalInput.value = '';
      }
    });
  }
  
  initSelect2() {
    if (!window.jQuery || !$.fn.select2) {
      console.warn('⚠️ Select2 non disponible');
      return;
    }
    
    $(this.container).find('.country-select, .city-select').select2({
      theme: 'bootstrap-5',
      allowClear: true,
      width: '100%',
      language: {
        noResults: () => "Aucun résultat trouvé",
        searching: () => "Recherche en cours...",
        inputTooShort: () => "Tapez pour rechercher"
      }
    });
  }
  
  parseAndSetDefault(locationString) {
    const parts = locationString.split(',').map(s => s.trim());
    if (parts.length === 2) {
      const [cityName, countryName] = parts;
      
      const country = this.countries.find(c => 
        c.name.toLowerCase() === countryName.toLowerCase()
      );
      
      if (country) {
        const countrySelect = this.container.querySelector('.country-select');
        countrySelect.value = country.code;
        countrySelect.dispatchEvent(new Event('change'));
        
        setTimeout(() => {
          const citySelect = this.container.querySelector('.city-select');
          const cityOption = Array.from(citySelect.options).find(opt => 
            opt.value.toLowerCase() === cityName.toLowerCase()
          );
          
          if (cityOption) {
            citySelect.value = cityOption.value;
            citySelect.dispatchEvent(new Event('change'));
          }
        }, 3000);
      }
    }
  }
  
  
}

window.LocationSelector = LocationSelector;