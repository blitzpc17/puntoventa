  
        
        //solo e sun ejemplo para manejar los estados de los checkbox
        // JavaScript para manejar los checkboxes
        document.querySelectorAll('.checkbox-input').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                console.log('Checkbox cambiado:', this.id, this.checked);
                
                // Efecto visual adicional
                if (this.checked) {
                    this.parentElement.style.transform = 'scale(1.02)';
                    setTimeout(() => {
                        this.parentElement.style.transform = 'scale(1)';
                    }, 150);
                }
            });
        });
 