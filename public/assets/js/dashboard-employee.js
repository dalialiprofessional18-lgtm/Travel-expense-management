/**
 * Dashboard Employee - Gestion des graphiques et statistiques
 */

// Préparer les données (seront injectées depuis PHP)
let deplacementsData = window.deplacementsData || [];
let salesChartInstance = null;
let statusChartInstance = null;

// Initialiser tout au chargement
document.addEventListener('DOMContentLoaded', function() {
    initSalesChart('7d');
    initStatusChart();
    updateStatsSummary();
    initTableFeatures();
    
    // Gérer les changements de période
    document.querySelectorAll('input[name="salesPeriod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const period = this.id.replace('sales', '');
            initSalesChart(period);
        });
    });
    
    // Mise à jour automatique toutes les 30 secondes
    setInterval(() => {
        const currentPeriod = document.querySelector('input[name="salesPeriod"]:checked')?.id.replace('sales', '') || '7d';
        initSalesChart(currentPeriod);
        updateStatsSummary();
        updateStatusChart();
    }, 30000);
});

/**
 * Graphique d'évolution des déplacements
 */
function initSalesChart(period) {
    const days = period === '7d' ? 7 : (period === '30d' ? 30 : 90);
    const now = new Date();
    const categories = [];
    const seriesData = {
        nouveaux: [],
        approuves: [],
        rejetes: []
    };
    
    // Générer les catégories de dates
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date(now);
        date.setDate(date.getDate() - i);
        date.setHours(0, 0, 0, 0);
        
        const nextDate = new Date(date);
        nextDate.setDate(nextDate.getDate() + 1);
        
        categories.push(period === '7d' ? 
            date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' }) :
            date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
        );
        
        // Compter les déplacements
        let nouveauxCount = 0;
        let approuvesCount = 0;
        let rejetesCount = 0;
        
        deplacementsData.forEach(d => {
            const depart = new Date(d.date_depart);
            depart.setHours(0, 0, 0, 0);
            
            if (depart >= date && depart < nextDate) {
                nouveauxCount++;
                
                if (d.statut === 'approuve') {
                    approuvesCount++;
                }
                if (d.statut === 'rejete_admin' || d.statut === 'rejete_manager') {
                    rejetesCount++;
                }
            }
        });
        
        seriesData.nouveaux.push(nouveauxCount);
        seriesData.approuves.push(approuvesCount);
        seriesData.rejetes.push(rejetesCount);
    }
    
    const options = {
        series: [
            {
                name: 'Nouveaux déplacements',
                data: seriesData.nouveaux,
                color: '#0d6efd'
            },
            {
                name: 'Approuvés',
                data: seriesData.approuves,
                color: '#198754'
            },
            {
                name: 'Rejetés',
                data: seriesData.rejetes,
                color: '#dc3545'
            }
        ],
        chart: {
            type: 'area',
            height: 320,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                opacityFrom: 0.6,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: -45,
                rotateAlways: period !== '7d',
                style: {
                    fontSize: '11px',
                    fontFamily: 'inherit'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            title: {
                text: 'Nombre de déplacements',
                style: {
                    fontSize: '12px',
                    fontWeight: 500
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '13px',
            markers: {
                width: 12,
                height: 12,
                radius: 3
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val) {
                    return val + ' déplacement' + (val > 1 ? 's' : '');
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4
        }
    };
    
    const chartElement = document.querySelector("#salesChart");
    if (!chartElement) return;
    
    chartElement.innerHTML = '';
    
    if (salesChartInstance) {
        salesChartInstance.destroy();
    }
    
    salesChartInstance = new ApexCharts(chartElement, options);
    salesChartInstance.render();
}

/**
 * Graphique de distribution des statuts
 */
function initStatusChart() {
    const statusCounts = {};
    
    deplacementsData.forEach(d => {
        const status = d.statut;
        statusCounts[status] = (statusCounts[status] || 0) + 1;
    });
    
    const statusLabels = {
        'Brouillon': 'Brouillon',
        'Soumis': 'Soumis',
        'Valide_manager': 'Validé Manager',
        'Rejete_manager': 'Rejeté Manager',
        'En_cours_admin': 'En cours Admin',
        'Approuve': 'Approuvé',
        'Rejete_admin': 'Rejeté Admin'
    };
    
    const colorMap = {
        'Brouillon': '#6c757d',
        'Soumis': '#0dcaf0',
        'Valide_manager': '#0d6efd',
        'Rejete_manager': '#dc3545',
        'En_cours_admin': '#ffc107',
        'Approuve': '#198754',
        'Rejete_admin': '#dc3545'
    };
    
    const labels = [];
    const series = [];
    const colors = [];
    
    Object.keys(statusCounts).forEach(status => {
        if (statusCounts[status] > 0) {
            labels.push(statusLabels[status] || status);
            series.push(statusCounts[status]);
            colors.push(colorMap[status] || '#6c757d');
        }
    });
    
    const options = {
        series: series,
        chart: {
            type: 'donut',
            height: 220,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        labels: labels,
        colors: colors,
        legend: { show: false },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.config.series[opts.seriesIndex];
            },
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                colors: ['#fff']
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '60%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '13px',
                            fontWeight: 600,
                            offsetY: -5
                        },
                        value: {
                            show: true,
                            fontSize: '20px',
                            fontWeight: 'bold',
                            offsetY: 5
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '13px',
                            fontWeight: 600,
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' déplacement' + (val > 1 ? 's' : '');
                }
            }
        }
    };
    
    const chartElement = document.querySelector("#statusChart");
    if (!chartElement) return;
    
    chartElement.innerHTML = '';
    
    if (statusChartInstance) {
        statusChartInstance.destroy();
    }
    
    statusChartInstance = new ApexCharts(chartElement, options);
    statusChartInstance.render();
}

/**
 * Mettre à jour les statistiques
 */
function updateStatsSummary() {
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    let activeTrips = 0;
    
    deplacementsData.forEach(d => {
        const depart = new Date(d.date_depart);
        const retour = new Date(d.date_retour);
        depart.setHours(0, 0, 0, 0);
        retour.setHours(0, 0, 0, 0);
        
        if (depart <= now && retour >= now) {
            activeTrips++;
        }
    });
    
    // Mettre à jour l'UI si nécessaire
}

/**
 * Mettre à jour le graphique des statuts
 */
function updateStatusChart() {
    if (statusChartInstance) {
        initStatusChart();
    }
}

/**
 * Initialiser les fonctionnalités du tableau
 */
function initTableFeatures() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const resetFilters = document.getElementById('resetFilters');
    const selectAll = document.getElementById('selectAll');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterTable);
    }
    
    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (dateFilter) dateFilter.value = '';
            filterTable();
        });
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.row-checkbox:not([disabled])').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkActions();
        });
    }
    
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    updatePagination();
}

// Autres fonctions: filterTable, updateBulkActions, updatePagination, exportDeplacements, etc.
// (Je ne répète pas tout le code pour gagner de l'espace)