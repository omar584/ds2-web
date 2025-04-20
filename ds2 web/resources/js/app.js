// Gestion des notifications
const showNotification = (message, type = 'success') => {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss après 5 secondes
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
};

// Gestion des confirmations de suppression
document.addEventListener('click', (e) => {
    if (e.target.matches('[data-confirm]')) {
        const message = e.target.getAttribute('data-confirm') || 'Êtes-vous sûr ?';
        if (!confirm(message)) {
            e.preventDefault();
        }
    }
});

// Gestion du dark mode
const toggleDarkMode = () => {
    document.documentElement.classList.toggle('dark-mode');
    const isDarkMode = document.documentElement.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
};

// Appliquer le dark mode au chargement si nécessaire
if (localStorage.getItem('darkMode') === 'true' || 
    window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.classList.add('dark-mode');
}

// Gestion des formulaires
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.classList.contains('needs-validation')) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    }
});

// Gestion des tooltips Bootstrap
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Gestion des étapes d'objectif
const handleStepToggle = async (goalId, stepId, checkbox) => {
    try {
        const response = await fetch(`/goals/${goalId}/steps/${stepId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const stepItem = document.getElementById(`step-${stepId}`);
            stepItem.classList.toggle('completed');
            
            // Mettre à jour la barre de progression
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = `${data.progress}%`;
                progressBar.textContent = `${Math.round(data.progress)}%`;
            }
            
            // Mettre à jour le badge de statut
            const badge = checkbox.closest('.card-body').querySelector('.badge');
            if (badge) {
                if (checkbox.checked) {
                    badge.className = 'badge bg-success';
                    badge.textContent = 'Complété';
                } else {
                    badge.className = 'badge bg-primary';
                    badge.textContent = 'En cours';
                }
            }
            
            showNotification('Étape mise à jour avec succès');
        }
    } catch (error) {
        console.error('Erreur lors de la mise à jour de l\'étape:', error);
        showNotification('Une erreur est survenue lors de la mise à jour de l\'étape', 'danger');
    }
};

// Gestion de la carte
const initMap = (elementId, center = [46.2276, 2.2137], zoom = 6) => {
    const map = L.map(elementId).setView(center, zoom);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    return map;
};

// Export des fonctions pour une utilisation globale
window.showNotification = showNotification;
window.handleStepToggle = handleStepToggle;
window.initMap = initMap;
window.toggleDarkMode = toggleDarkMode; 