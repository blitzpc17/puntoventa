class Select2ProductManager {
    constructor(selector, placeholder, noResultsText, apiUrl, options = {}) {
        this.selector = selector;
        this.placeholder = placeholder;
        this.noResultsText = noResultsText;
        this.apiUrl = apiUrl;
        this.cache = null;
        this.cacheTime = null;
        this.cacheDuration = options.cacheDuration || 10 * 60 * 1000;
        this.select2Instance = null;
        this.isVisible = options.initialVisible !== undefined ? options.initialVisible : true; // Nuevo: visibilidad inicial
        this.toggleCallback = options.onToggle || null;
        this.initialized = false;
        
        this.initSelect2();
        this.setupToggleStyles();
    }
    
    async initSelect2() {
        await this.loadInitialData();
        
        this.select2Instance = $(this.selector).select2({
            data: this.cache,
            placeholder: this.placeholder,
            minimumInputLength: 1,
            language: {
                noResults: () => this.noResultsText
            },
            width: '100%'
        });
        
        // Configurar visibilidad inicial basada en this.isVisible
        const container = this.getSelect2Container();
        if (this.isVisible) {
            container.addClass('select2-visible').removeClass('select2-hidden')
                .css({
                    'opacity': '1',
                    'height': 'auto',
                    'width': '100%',
                    'transform': 'scale(1) translateY(0)'
                });
        } else {
            container.addClass('select2-hidden').removeClass('select2-visible')
                .css({
                    'opacity': '0',
                    'height': '0',
                    'width': '0',
                    'margin': '0',
                    'padding': '0',
                    'transform': 'scale(0.8) translateY(-10px)'
                });
        }
        
        this.initialized = true;
    }
    
    async loadInitialData() {
        try {
            const response = await fetch(this.apiUrl);
            this.cache = await response.json();
            this.cacheTime = Date.now();
        } catch (error) {
            console.error('Error loading products:', error);
        }
    }
    
    // MÉTODO PARA OBTENER EL CONTENEDOR
    getSelect2Container() {
        return $(this.selector).next('.select2-container');
    }
    
    // MÉTODO TOGGLE INTEGRADO
    toggle(show = null) {
        if (!this.initialized) {
            console.warn('Select2 no está inicializado aún');
            return this.isVisible;
        }
        
        const container = this.getSelect2Container();
        const shouldShow = show !== null ? show : !this.isVisible;
        
        if (shouldShow) {
            // Mostrar con animación
            container
                .removeClass('select2-hidden')
                .addClass('select2-visible')
                .css({
                    'opacity': '0',
                    'transform': 'scale(0.95) translateY(-5px)',
                    'height': 'auto',
                    'width': '100%'
                })
                .animate({
                    'opacity': '1',
                    'transform': 'scale(1) translateY(0)'
                }, 300, () => {
                    $(this.selector).select2('enable');
                });
            
            this.isVisible = true;
        } else {
            // Ocultar con animación
            container
                .removeClass('select2-visible')
                .addClass('select2-hidden')
                .animate({
                    'opacity': '0',
                    'transform': 'scale(0.95) translateY(-5px)'
                }, 300, () => {
                    container.css({
                        'height': '0',
                        'width': '0',
                        'margin': '0',
                        'padding': '0'
                    });
                    $(this.selector).select2('enable');
                });
            
            this.isVisible = false;
        }
        
        // Ejecutar callback si existe
        if (this.toggleCallback) {
            this.toggleCallback(this.isVisible);
        }
        
        // Actualizar checkbox si existe
        this.updateToggleControl();
        
        return this.isVisible;
    }
    
    // MÉTODO PARA CREAR CHECKBOX DE CONTROL
    createToggleControl(container = 'body', label = 'Mostrar/Ocultar Select') {
        const controlId = `toggle-${this.selector.replace('#', '')}`;
        
        const toggleHTML = `
            <div class="select2-toggle-control">
                <label class="select2-toggle-checkbox">
                    <input type="checkbox" id="${controlId}" ${this.isVisible ? 'checked' : ''}>
                    ${label}
                    <span class="select2-status-indicator"></span>
                </label>
            </div>
        `;
        
        $(container).prepend(toggleHTML);
        
        // Event listener para el checkbox
        $(`#${controlId}`).on('change', () => {
            this.toggle();
        });
        
        // Actualizar indicador inicial
        this.updateToggleIndicator(controlId);
        
        return controlId;
    }
    
    // ACTUALIZAR INDICADOR VISUAL
    updateToggleIndicator(controlId = null) {
        const indicator = controlId ? 
            $(`#${controlId}`).siblings('.select2-status-indicator') :
            $(`[id^="toggle-${this.selector.replace('#', '')}"]`).siblings('.select2-status-indicator');
        
        if (indicator.length) {
            if (this.isVisible) {
                indicator.css('background-color', '#4caf50');
            } else {
                indicator.css('background-color', '#f44336');
            }
        }
    }
    
    // ACTUALIZAR CONTROL DE TOGGLE
    updateToggleControl() {
        const checkbox = $(`[id^="toggle-${this.selector.replace('#', '')}"]`);
        if (checkbox.length) {
            checkbox.prop('checked', this.isVisible);
            this.updateToggleIndicator(checkbox.attr('id'));
        }
    }
    
    // SETUP DE ESTILOS (solo una vez)
    setupToggleStyles() {
        if (!document.getElementById('select2-toggle-styles')) {
            const style = document.createElement('style');
            style.id = 'select2-toggle-styles';
            style.textContent = `
                .select2-container--default.select2-hidden {
                    opacity: 0 !important;
                    height: 0 !important;
                    width: 0 !important;
                    overflow: hidden !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    border: none !important;
                    transform: scale(0.8) translateY(-10px) !important;
                    pointer-events: none !important;
                    transition: all 0.3s ease-in-out !important;
                }

                .select2-container--default.select2-visible {
                    opacity: 1 !important;
                    height: auto !important;
                    width: 100% !important;
                    transform: scale(1) translateY(0) !important;
                    pointer-events: auto !important;
                    transition: all 0.3s ease-in-out !important;
                }

                .select2-toggle-control {
                    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
                    border: 2px solid #90caf9 !important;
                    border-radius: 10px !important;
                    padding: 15px 20px !important;
                    margin-bottom: 20px !important;
                }

                .select2-toggle-checkbox {
                    display: flex !important;
                    align-items: center !important;
                    gap: 10px !important;
                    cursor: pointer !important;
                    font-weight: 600 !important;
                    color: #1976d2 !important;
                }

                .select2-toggle-checkbox input[type="checkbox"] {
                    width: 18px !important;
                    height: 18px !important;
                    accent-color: #1976d2 !important;
                    cursor: pointer !important;
                }

                .select2-toggle-checkbox:hover {
                    color: #1565c0 !important;
                }

                .select2-status-indicator {
                    display: inline-block !important;
                    width: 12px !important;
                    height: 12px !important;
                    border-radius: 50% !important;
                    margin-left: 8px !important;
                    background-color: #4caf50 !important;
                    animation: pulseStatus 2s infinite !important;
                }

                .select2-toggle-checkbox input:not(:checked) ~ .select2-status-indicator {
                    background-color: #f44336 !important;
                }

                @keyframes pulseStatus {
                    0% { opacity: 1; }
                    50% { opacity: 0.5; }
                    100% { opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // MÉTODOS ADICIONALES
    show() {
        return this.toggle(true);
    }
    
    hide() {
        return this.toggle(false);
    }
    
    getVisibility() {
        return this.isVisible;
    }
    
    // Destructor
    destroy() {
        if (this.select2Instance) {
            this.select2Instance.select2('destroy');
        }
    }
}