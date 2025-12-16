/**
 * Déplacements List - Filtrage et gestion de la liste
 */

function filterTable() {
    const searchValue = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const statusValue = document.getElementById('statusFilter')?.value || '';
    const dateValue = document.getElementById('dateFilter')?.value || '';
    
    const rows = document.querySelectorAll('.deplacement-row');
    let visibleCount = 0;
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    rows.forEach(row => {
        const titre = row.dataset.titre?.toLowerCase() || '';
        const lieu = row.dataset.lieu?.toLowerCase() || '';
        const statut = row.dataset.statut || '';
        const dateDepart = new Date(row.dataset.dateDepart);
        const dateRetour = new Date(row.dataset.dateRetour);
        dateDepart.setHours(0, 0, 0, 0);
        dateRetour.setHours(0, 0, 0, 0);
        
        let visible = true;
        
        // Filtre de recherche
        if (searchValue && !titre.includes(searchValue) && !lieu.includes(searchValue)) {
            visible = false;
        }
        
        // Filtre de statut
        if (statusValue && statut !== statusValue) {
            visible = false;
        }
        
        // Filtre de date
        if (dateValue) {
            const today = new Date(now);
            const weekStart = new Date(now);
            weekStart.setDate(weekStart.getDate() - weekStart.getDay());
            const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            
            switch(dateValue) {
                case 'today':
                    if (dateDepart > today || dateRetour < today) visible = false;
                    break;
                case 'week':
                    if (dateDepart < weekStart) visible = false;
                    break;
                case 'month':
                    if (dateDepart < monthStart) visible = false;
                    break;
                case 'upcoming':
                    if (dateDepart <= now) visible = false;
                    break;
                case 'past':
                    if (dateRetour >= now) visible = false;
                    break;
            }
        }
        
        row.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });
    
    // Afficher/masquer le message "Aucun résultat"
    const noResults = document.getElementById('noResults');
    const tableBody = document.querySelector('#deplacementsTable tbody');
    
    if (noResults && tableBody) {
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
            tableBody.style.display = 'none';
        } else {
            noResults.classList.add('d-none');
            tableBody.style.display = '';
        }
    }
    
    updatePagination();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    
    if (bulkActionsBar && selectedCount) {
        if (checkedBoxes.length > 0) {
            bulkActionsBar.classList.remove('d-none');
            selectedCount.textContent = checkedBoxes.length;
        } else {
            bulkActionsBar.classList.add('d-none');
        }
    }
}

function updatePagination() {
    const visibleRows = document.querySelectorAll('.deplacement-row:not([style*="display: none"])');
    const totalItems = visibleRows.length;
    
    const totalItemsEl = document.getElementById('totalItems');
    const startItemEl = document.getElementById('startItem');
    const endItemEl = document.getElementById('endItem');
    
    if (totalItemsEl) totalItemsEl.textContent = totalItems;
    if (startItemEl) startItemEl.textContent = totalItems > 0 ? 1 : 0;
    if (endItemEl) endItemEl.textContent = Math.min(10, totalItems);
}

function exportDeplacements() {
    const rows = Array.from(document.querySelectorAll('.deplacement-row:not([style*="display: none"])'));
    
    if (rows.length === 0) {
        alert('Aucun déplacement à exporter');
        return;
    }
    
    let csv = 'Titre,Lieu,Date Départ,Date Retour,Durée,Statut\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const titre = cells[1]?.querySelector('.fw-medium')?.textContent || '';
        const lieu = cells[2]?.textContent.trim() || '';
        const dates = cells[3]?.querySelectorAll('div') || [];
        const dateDepart = dates[0]?.textContent.replace(/[^\d\/]/g, '') || '';
        const dateRetour = dates[1]?.textContent.replace(/[^\d\/]/g, '') || '';
        const duree = cells[4]?.textContent.trim() || '';
        const statut = cells[5]?.textContent.trim() || '';
        
        csv += `"${titre}","${lieu}","${dateDepart}","${dateRetour}","${duree}","${statut}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'deplacements_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function deleteDeplacement() {
    return confirm('Êtes-vous sûr de vouloir supprimer ce déplacement ?');
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Veuillez sélectionner au moins un déplacement');
        return;
    }
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${checkedBoxes.length} déplacement(s) ?`)) {
        checkedBoxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row) {
                row.style.opacity = '0.5';
                setTimeout(() => {
                    row.remove();
                    updatePagination();
                }, 300);
            }
        });
        
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        updateBulkActions();
    }
}