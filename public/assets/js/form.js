// Form Enhancement Functions
class FormEnhancer {
    constructor() {
        this.init();
    }

    init() {
        this.setupImageUpload();
        this.setupFormValidation();
        this.setupProgressBar();
        this.setupSmoothScrolling();
        console.log('Form enhancer initialized successfully!');
    }

    // Enhanced Progress Bar
    updateProgressBar() {
        const form = document.getElementById('registrationForm') || document.getElementById('editForm');
        if (!form) return;

        const inputs = form.querySelectorAll('input[required], select[required]');
        let filledInputs = 0;
        
        inputs.forEach(input => {
            if (input.type === 'radio') {
                if (document.querySelector(`input[name="${input.name}"]:checked`)) {
                    filledInputs++;
                }
            } else if (input.value.trim() !== '') {
                filledInputs++;
            }
        });
        
        const progress = (filledInputs / inputs.length) * 100;
        const progressBar = document.getElementById('progressFill');
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
    }

    // Enhanced Image Upload with Preview
    setupImageUpload() {
        const photoInput = document.getElementById('photo');
        if (!photoInput) return;

        const uploadArea = document.getElementById('uploadArea');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg') || document.getElementById('photo-preview');
        const removeBtn = document.getElementById('removeImage');
        
        // File input change event
        photoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.handleFileUpload(file);
            }
        });
        
        // Drag and drop functionality
        if (uploadArea) {
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        photoInput.files = files;
                        this.handleFileUpload(file);
                    } else {
                        this.showNotification('ກະລຸນາເລືອກໄຟລ໌ຮູບພາບເທົ່ານັ້ນ', 'error');
                    }
                }
            });
        }
        
        // Remove image functionality
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                photoInput.value = '';
                if (imagePreview) imagePreview.classList.add('hidden');
                if (uploadArea) uploadArea.style.display = 'flex';
                this.updateProgressBar();
            });
        }
    }

    handleFileUpload(file) {
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            this.showNotification('ຂະໜາດໄຟລ້ນໃຫຍ່ເກີນໄປ! ກະລຸນາເລືອກໄຟລ໌ທີ່ນ້ອຍກວ່າ 5MB', 'error');
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            this.showNotification('ປະເພດໄຟລ່ບໍ່ຖືກຕ້ອງ! ກະລຸນາເລືອກ JPG, PNG ຫຼື GIF', 'error');
            return;
        }
        
        // Create file reader
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewImg = document.getElementById('previewImg') || document.getElementById('photo-preview');
            const uploadArea = document.getElementById('uploadArea');
            const imagePreview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('photo-placeholder');
            
            if (previewImg) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
            }
            
            if (uploadArea) uploadArea.style.display = 'none';
            if (imagePreview) imagePreview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            
            // Update file info
            this.updateFileInfo(file);
            this.updateProgressBar();
        };
        reader.readAsDataURL(file);
    }

    updateFileInfo(file) {
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileType = document.getElementById('fileType');
        
        if (fileName) fileName.textContent = file.name;
        if (fileSize) fileSize.textContent = this.formatFileSize(file.size);
        if (fileType) fileType.textContent = file.type.split('/')[1].toUpperCase();
    }

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Enhanced Form Validation
    setupFormValidation() {
        const form = document.getElementById('registrationForm') || document.getElementById('editForm');
        if (!form) return;

        const inputs = form.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            input.addEventListener('input', () => {
                this.clearFieldError(input);
                this.updateProgressBar();
            });
            
            input.addEventListener('change', () => {
                this.validateField(input);
                this.updateProgressBar();
            });
        });
        
        // Form submit validation
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const requiredFields = form.querySelectorAll('input[required], select[required]');
            
            requiredFields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                this.showNotification('ກະລຸນາຕື່ມຂໍ້ມູນໃຫ້ຄົບຖ້ວນ', 'error');
                return;
            }
            
            // Show loading
            this.showLoading(true);
            const submitBtn = form.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>ກຳລັງປະມວນຜົນ...';
                submitBtn.disabled = true;
            }
        });
    }

    // Field validation
    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.getAttribute('name');
        
        this.clearFieldError(field);
        
        if (field.hasAttribute('required') && !value) {
            this.showFieldError(field, 'ກະລຸນາຕື່ມຂໍ້ມູນໃນຊ່ອງນີ້');
            return false;
        }
        
        // Email validation
        if (fieldName === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showFieldError(field, 'ຮູບແບບອີເມວບໍ່ຖືກຕ້ອງ');
                return false;
            }
        }
        
        // Phone validation
        if (fieldName === 'phone' && value) {
            const phoneRegex = /^[0-9+\-\s()]+$/;
            if (!phoneRegex.test(value)) {
                this.showFieldError(field, 'ເບີໂທບໍ່ຖືກຕ້ອງ');
                return false;
            }
        }
        
        // Date validation
        if (field.type === 'date' && value) {
            const today = new Date();
            const birthDate = new Date(value);
            const age = today.getFullYear() - birthDate.getFullYear();
            
            if (age < 10 || age > 100) {
                this.showFieldError(field, 'ວັນເກິດບໍ່ສົມເຫດສົມຜົນ');
                return false;
            }
        }
        
        field.classList.add('input-success');
        return true;
    }

    // Show field error
    showFieldError(field, message) {
        field.classList.add('input-error');
        field.classList.remove('input-success');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        field.parentNode.appendChild(errorDiv);
    }

    // Clear field error
    clearFieldError(field) {
        field.classList.remove('input-error');
        const errorMessage = field.parentNode.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }

    // Show notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'success' ? 'bg-green-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'error' ? 'fa-times-circle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Hide notification after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // Show/hide loading overlay
    showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            if (show) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
    }

    // Setup progress bar tracking
    setupProgressBar() {
        // Update progress bar on page load
        this.updateProgressBar();
    }

    // Setup smooth scrolling
    setupSmoothScrolling() {
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('focus', function() {
                this.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new FormEnhancer();
});
// Auto-submit form when changing filters
document.addEventListener('DOMContentLoaded', function() {
    const majorSelect = document.querySelector('select[name="major"]');
    const yearSelect = document.querySelector('select[name="year"]');
    
    majorSelect?.addEventListener('change', function() {
        this.form.submit();
    });
    
    yearSelect?.addEventListener('change', function() {
        this.form.submit();
    });
});