/**
 * Task Manager Plugin - JavaScript principal
 *
 * Ce fichier contient toutes les fonctionnalités JavaScript nécessaires
 * au bon fonctionnement du plugin Task Manager, utilisant Alpine.js
 * pour l'interactivité côté client.
 */

// Import d'Alpine.js si pas déjà chargé
if (typeof Alpine === 'undefined') {
    console.warn('Alpine.js n\'est pas chargé. Le plugin Task Manager nécessite Alpine.js.');
}

/**
 * Composant principal du Task Manager
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('taskManager', () => ({
        // État global
        loading: false,
        notifications: [],
        currentView: 'list', // list, grid, kanban
        showFilters: false,
        selectedTasks: [],
        selectAll: false,

        // Filtres
        filters: {
            search: '',
            status: '',
            priority: '',
            assignedTo: '',
            categoryId: '',
            dueDateFrom: '',
            dueDateTo: '',
            createdBy: '',
            isPublic: ''
        },

        // Tri
        sortBy: 'created_at',
        sortDirection: 'desc',

        // Pagination
        perPage: 15,
        currentPage: 1,

        /**
         * Initialisation du composant
         */
        init() {
            this.loadFromLocalStorage();
            this.setupEventListeners();
            this.initializeTooltips();
            this.initializeDatePickers();
        },

        /**
         * Configuration des écouteurs d'événements
         */
        setupEventListeners() {
            // Écoute des événements Livewire
            this.$watch('filters', (value) => {
                this.saveToLocalStorage();
                this.debounce(() => this.applyFilters(), 300);
            });

            // Écoute des changements de vue
            this.$watch('currentView', (value) => {
                this.saveToLocalStorage();
                this.initializeView();
            });

            // Écoute des sélections
            this.$watch('selectedTasks', (value) => {
                this.updateSelectAllState();
            });
        },

        /**
         * Initialisation des tooltips
         */
        initializeTooltips() {
            // Utilisation de Tippy.js si disponible
            if (typeof tippy !== 'undefined') {
                tippy('[data-tippy-content]', {
                    placement: 'top',
                    animation: 'scale',
                    duration: [200, 150],
                    theme: 'task-manager'
                });
            }
        },

        /**
         * Initialisation des sélecteurs de date
         */
        initializeDatePickers() {
            // Utilisation de Flatpickr si disponible
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.date-picker', {
                    dateFormat: 'Y-m-d',
                    locale: 'fr',
                    allowInput: true,
                    clickOpens: true
                });
            }
        },

        /**
         * Initialisation de la vue actuelle
         */
        initializeView() {
            switch (this.currentView) {
                case 'kanban':
                    this.initializeKanban();
                    break;
                case 'grid':
                    this.initializeGrid();
                    break;
                case 'list':
                default:
                    this.initializeList();
                    break;
            }
        },

        /**
         * Initialisation du mode Kanban
         */
        initializeKanban() {
            // Configuration du drag & drop pour Kanban
            if (typeof Sortable !== 'undefined') {
                const kanbanColumns = document.querySelectorAll('.kanban-column');
                kanbanColumns.forEach(column => {
                    const taskList = column.querySelector('.kanban-tasks');
                    if (taskList) {
                        Sortable.create(taskList, {
                            group: 'tasks',
                            animation: 150,
                            ghostClass: 'kanban-task-ghost',
                            chosenClass: 'kanban-task-chosen',
                            dragClass: 'kanban-task-drag',
                            onEnd: (evt) => {
                                this.handleTaskMove(evt);
                            }
                        });
                    }
                });
            }
        },

        /**
         * Initialisation du mode Grille
         */
        initializeGrid() {
            // Configuration spécifique pour la vue grille
            this.initializeMasonry();
        },

        /**
         * Initialisation du mode Liste
         */
        initializeList() {
            // Configuration spécifique pour la vue liste
            this.initializeTableSorting();
        },

        /**
         * Initialisation de Masonry pour la grille
         */
        initializeMasonry() {
            if (typeof Masonry !== 'undefined') {
                const grid = document.querySelector('.task-grid');
                if (grid) {
                    new Masonry(grid, {
                        itemSelector: '.task-grid-card',
                        columnWidth: '.task-grid-card',
                        percentPosition: true,
                        gutter: 24
                    });
                }
            }
        },

        /**
         * Initialisation du tri de tableau
         */
        initializeTableSorting() {
            const tableHeaders = document.querySelectorAll('.task-table th[data-sort]');
            tableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const field = header.dataset.sort;
                    this.sortBy(field);
                });
            });
        },

        /**
         * Gestion du déplacement de tâches (Kanban)
         */
        handleTaskMove(evt) {
            const taskId = evt.item.dataset.taskId;
            const newStatus = evt.to.dataset.status;

            if (taskId && newStatus) {
                this.updateTaskStatus(taskId, newStatus);
            }
        },

        /**
         * Application des filtres
         */
        applyFilters() {
            this.loading = true;
            this.currentPage = 1;

            // Émission d'événement pour Livewire
            this.$dispatch('filters-applied', this.filters);

            // Simulation de chargement
            setTimeout(() => {
                this.loading = false;
            }, 500);
        },

        /**
         * Réinitialisation des filtres
         */
        resetFilters() {
            this.filters = {
                search: '',
                status: '',
                priority: '',
                assignedTo: '',
                categoryId: '',
                dueDateFrom: '',
                dueDateTo: '',
                createdBy: '',
                isPublic: ''
            };

            this.applyFilters();
        },

        /**
         * Changement de tri
         */
        sortBy(field) {
            if (this.sortBy === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = field;
                this.sortDirection = 'asc';
            }

            this.applyFilters();
        },

        /**
         * Changement de vue
         */
        changeView(view) {
            this.currentView = view;
        },

        /**
         * Sélection/désélection de toutes les tâches
         */
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedTasks = this.getAllTaskIds();
            } else {
                this.selectedTasks = [];
            }
        },

        /**
         * Obtention de tous les IDs de tâches
         */
        getAllTaskIds() {
            const taskElements = document.querySelectorAll('[data-task-id]');
            return Array.from(taskElements).map(el => el.dataset.taskId);
        },

        /**
         * Mise à jour de l'état de sélection globale
         */
        updateSelectAllState() {
            const allTaskIds = this.getAllTaskIds();
            this.selectAll = allTaskIds.length > 0 &&
                           allTaskIds.every(id => this.selectedTasks.includes(id));
        },

        /**
         * Sélection/désélection d'une tâche
         */
        toggleTaskSelection(taskId) {
            const index = this.selectedTasks.indexOf(taskId);
            if (index > -1) {
                this.selectedTasks.splice(index, 1);
            } else {
                this.selectedTasks.push(taskId);
            }
        },

        /**
         * Actions en lot
         */
        bulkAction(action, value = null) {
            if (this.selectedTasks.length === 0) {
                this.showNotification('Aucune tâche sélectionnée', 'warning');
                return;
            }

            switch (action) {
                case 'status':
                    this.bulkUpdateStatus(value);
                    break;
                case 'priority':
                    this.bulkUpdatePriority(value);
                    break;
                case 'assign':
                    this.bulkAssign(value);
                    break;
                case 'delete':
                    this.bulkDelete();
                    break;
                default:
                    console.warn('Action en lot non reconnue:', action);
            }
        },

        /**
         * Mise à jour en lot du statut
         */
        bulkUpdateStatus(status) {
            this.selectedTasks.forEach(taskId => {
                this.updateTaskStatus(taskId, status);
            });

            this.selectedTasks = [];
            this.showNotification('Statut mis à jour pour les tâches sélectionnées', 'success');
        },

        /**
         * Mise à jour en lot de la priorité
         */
        bulkUpdatePriority(priority) {
            this.selectedTasks.forEach(taskId => {
                this.updateTaskPriority(taskId, priority);
            });

            this.selectedTasks = [];
            this.showNotification('Priorité mise à jour pour les tâches sélectionnées', 'success');
        },

        /**
         * Assignation en lot
         */
        bulkAssign(userId) {
            this.selectedTasks.forEach(taskId => {
                this.assignTask(taskId, userId);
            });

            this.selectedTasks = [];
            this.showNotification('Tâches assignées avec succès', 'success');
        },

        /**
         * Suppression en lot
         */
        bulkDelete() {
            if (confirm('Êtes-vous sûr de vouloir supprimer les tâches sélectionnées ?')) {
                this.selectedTasks.forEach(taskId => {
                    this.deleteTask(taskId);
                });

                this.selectedTasks = [];
                this.showNotification('Tâches supprimées avec succès', 'success');
            }
        },

        /**
         * Mise à jour du statut d'une tâche
         */
        updateTaskStatus(taskId, status) {
            this.loading = true;

            fetch(`/tasks/${taskId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateTaskElement(taskId, { status });
                    this.showNotification('Statut mis à jour', 'success');
                } else {
                    this.showNotification(data.error || 'Erreur lors de la mise à jour', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.showNotification('Erreur lors de la mise à jour', 'error');
            })
            .finally(() => {
                this.loading = false;
            });
        },

        /**
         * Mise à jour de la priorité d'une tâche
         */
        updateTaskPriority(taskId, priority) {
            this.loading = true;

            fetch(`/tasks/${taskId}/priority`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ priority })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateTaskElement(taskId, { priority });
                    this.showNotification('Priorité mise à jour', 'success');
                } else {
                    this.showNotification(data.error || 'Erreur lors de la mise à jour', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.showNotification('Erreur lors de la mise à jour', 'error');
            })
            .finally(() => {
                this.loading = false;
            });
        },

        /**
         * Assignation d'une tâche
         */
        assignTask(taskId, userId) {
            this.loading = true;

            fetch(`/tasks/${taskId}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateTaskElement(taskId, { assigned_to: userId });
                    this.showNotification('Tâche assignée', 'success');
                } else {
                    this.showNotification(data.error || 'Erreur lors de l\'assignation', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.showNotification('Erreur lors de l\'assignation', 'error');
            })
            .finally(() => {
                this.loading = false;
            });
        },

        /**
         * Suppression d'une tâche
         */
        deleteTask(taskId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                this.loading = true;

                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.removeTaskElement(taskId);
                        this.showNotification('Tâche supprimée', 'success');
                    } else {
                        this.showNotification(data.error || 'Erreur lors de la suppression', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    this.showNotification('Erreur lors de la suppression', 'error');
                })
                .finally(() => {
                    this.loading = false;
                });
            }
        },

        /**
         * Mise à jour d'un élément de tâche dans le DOM
         */
        updateTaskElement(taskId, updates) {
            const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
            if (!taskElement) return;

            // Mise à jour des attributs de données
            Object.keys(updates).forEach(key => {
                taskElement.dataset[key] = updates[key];
            });

            // Mise à jour des classes CSS
            if (updates.status) {
                taskElement.className = taskElement.className.replace(/status-\w+/g, '');
                taskElement.classList.add(`status-${updates.status}`);
            }

            // Mise à jour du contenu affiché
            this.updateTaskDisplay(taskElement, updates);
        },

        /**
         * Mise à jour de l'affichage d'une tâche
         */
        updateTaskDisplay(taskElement, updates) {
            // Mise à jour du badge de statut
            if (updates.status) {
                const statusBadge = taskElement.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = this.getStatusLabel(updates.status);
                    statusBadge.className = `status-badge ${updates.status}`;
                }
            }

            // Mise à jour du badge de priorité
            if (updates.priority) {
                const priorityBadge = taskElement.querySelector('.priority-badge');
                if (priorityBadge) {
                    priorityBadge.textContent = this.getPriorityLabel(updates.priority);
                    priorityBadge.className = `priority-badge ${updates.priority}`;
                }
            }
        },

        /**
         * Suppression d'un élément de tâche du DOM
         */
        removeTaskElement(taskId) {
            const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskElement) {
                taskElement.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    taskElement.remove();
                }, 300);
            }
        },

        /**
         * Obtention du libellé de statut
         */
        getStatusLabel(status) {
            const labels = {
                pending: 'En attente',
                in_progress: 'En cours',
                completed: 'Terminée',
                cancelled: 'Annulée'
            };
            return labels[status] || status;
        },

        /**
         * Obtention du libellé de priorité
         */
        getPriorityLabel(priority) {
            const labels = {
                low: 'Faible',
                medium: 'Moyenne',
                high: 'Élevée',
                urgent: 'Urgente'
            };
            return labels[priority] || priority;
        },

        /**
         * Affichage de notifications
         */
        showNotification(message, type = 'info') {
            const notification = {
                id: Date.now(),
                message,
                type,
                timestamp: new Date()
            };

            this.notifications.push(notification);

            // Auto-suppression après 5 secondes
            setTimeout(() => {
                this.removeNotification(notification.id);
            }, 5000);
        },

        /**
         * Suppression d'une notification
         */
        removeNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        },

        /**
         * Fonction de debounce
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Sauvegarde dans le localStorage
         */
        saveToLocalStorage() {
            const data = {
                filters: this.filters,
                currentView: this.currentView,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection,
                perPage: this.perPage
            };
            localStorage.setItem('taskManagerState', JSON.stringify(data));
        },

        /**
         * Chargement depuis le localStorage
         */
        loadFromLocalStorage() {
            const saved = localStorage.getItem('taskManagerState');
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    this.filters = data.filters || this.filters;
                    this.currentView = data.currentView || this.currentView;
                    this.sortBy = data.sortBy || this.sortBy;
                    this.sortDirection = data.sortDirection || this.sortDirection;
                    this.perPage = data.perPage || this.perPage;
                } catch (error) {
                    console.warn('Erreur lors du chargement des données sauvegardées:', error);
                }
            }
        }
    }));
});

/**
 * Composant pour les cartes de tâches
 */
Alpine.data('taskCard', (taskData) => ({
    task: taskData,
    showDetails: false,
    isEditing: false,

    init() {
        this.initializeCard();
    },

    initializeCard() {
        // Initialisation spécifique à la carte
    },

    toggleDetails() {
        this.showDetails = !this.showDetails;
    },

    editTask() {
        this.isEditing = true;
        // Logique d'édition
    },

    saveTask() {
        this.isEditing = false;
        // Logique de sauvegarde
    },

    cancelEdit() {
        this.isEditing = false;
        // Annulation des modifications
    }
}));

/**
 * Composant pour les formulaires de tâches
 */
Alpine.data('taskForm', () => ({
    form: {
        title: '',
        description: '',
        status: 'pending',
        priority: 'medium',
        due_date: '',
        assigned_to: '',
        category_id: '',
        is_public: false
    },
    errors: {},
    loading: false,

    init() {
        this.initializeForm();
    },

    initializeForm() {
        // Initialisation du formulaire
    },

    validate() {
        this.errors = {};

        if (!this.form.title.trim()) {
            this.errors.title = 'Le titre est requis';
        }

        if (this.form.due_date && new Date(this.form.due_date) < new Date()) {
            this.errors.due_date = 'La date d\'échéance doit être dans le futur';
        }

        return Object.keys(this.errors).length === 0;
    },

    async submit() {
        if (!this.validate()) {
            return;
        }

        this.loading = true;

        try {
            const response = await fetch('/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.form)
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect || '/tasks';
            } else {
                this.errors = data.errors || {};
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.errors.general = 'Une erreur est survenue';
        } finally {
            this.loading = false;
        }
    }
}));

/**
 * Utilitaires globaux
 */
window.TaskManager = {
    // Fonction pour formater les dates
    formatDate(date, format = 'DD/MM/YYYY') {
        if (!date) return '';

        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year);
    },

    // Fonction pour formater les durées
    formatDuration(minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;

        if (hours > 0) {
            return `${hours}h ${mins}m`;
        }
        return `${mins}m`;
    },

    // Fonction pour tronquer le texte
    truncate(text, length = 100) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    },

    // Fonction pour générer des couleurs
    generateColor(text) {
        let hash = 0;
        for (let i = 0; i < text.length; i++) {
            hash = text.charCodeAt(i) + ((hash << 5) - hash);
        }

        const hue = hash % 360;
        return `hsl(${hue}, 70%, 50%)`;
    }
};

// Export pour utilisation dans d'autres modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.TaskManager;
}
