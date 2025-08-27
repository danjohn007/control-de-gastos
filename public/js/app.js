/**
 * JavaScript principal para el sistema de control de gastos
 */

// Configuración global
window.App = {
    baseUrl: window.BASE_URL || '',
    
    // Formatear moneda
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(amount);
    },
    
    // Formatear fecha
    formatDate: function(date) {
        return new Intl.DateTimeFormat('es-MX').format(new Date(date));
    },
    
    // Mostrar notificación
    showNotification: function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    },
    
    // Confirmar eliminación
    confirmDelete: function(message = '¿Estás seguro de que deseas eliminar este elemento?') {
        return confirm(message);
    },
    
    // Validar formulario
    validateForm: function(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;
        
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    },
    
    // Cargar datos via AJAX
    loadData: function(url, callback) {
        fetch(this.baseUrl + url)
            .then(response => response.json())
            .then(data => callback(data))
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Error al cargar los datos', 'danger');
            });
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Confirmar eliminaciones
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete') || e.target.closest('.btn-delete')) {
            if (!App.confirmDelete()) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Validación en tiempo real
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Previsualización de archivos
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.querySelector('#' + this.id + '_preview');
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (this.files[0].type.startsWith('image/')) {
                        preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">`;
                    } else {
                        preview.innerHTML = `<p>Archivo seleccionado: ${this.files[0].name}</p>`;
                    }
                }.bind(this);
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Búsqueda en tiempo real
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        let timeout;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                // Implementar lógica de búsqueda
                console.log('Searching for:', this.value);
            }, 300);
        });
    });
    
    // Sidebar responsive
    const sidebarToggle = document.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('show');
        });
    }
});

// Funciones utilitarias específicas

// Actualizar totales en formularios
function updateTotals() {
    const amounts = document.querySelectorAll('.amount-input');
    let total = 0;
    
    amounts.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    const totalDisplay = document.querySelector('.total-display');
    if (totalDisplay) {
        totalDisplay.textContent = App.formatCurrency(total);
    }
}

// Filtros de fecha
function setDateFilter(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
            startDate = weekStart.toISOString().split('T')[0];
            endDate = new Date().toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date().toISOString().split('T')[0];
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = new Date().toISOString().split('T')[0];
            break;
    }
    
    const startInput = document.querySelector('#fecha_inicio');
    const endInput = document.querySelector('#fecha_fin');
    
    if (startInput) startInput.value = startDate;
    if (endInput) endInput.value = endDate;
}

// Exportar funciones globales
window.App = App;
window.updateTotals = updateTotals;
window.setDateFilter = setDateFilter;