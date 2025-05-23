/**
 * Score Board - Toast Notification System
 * A lightweight, customizable toast notification system
 */

class ToastManager {
    constructor() {
        this.toastContainer = null;
        this.toasts = [];
        this.defaultDuration = 5000; // 5 seconds
        this.position = 'top-right';
        this.maxToasts = 5;
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!this.toastContainer) {
            this.toastContainer = document.createElement('div');
            this.toastContainer.className = 'toast-container';
            this.toastContainer.setAttribute('aria-live', 'polite');
            this.toastContainer.setAttribute('aria-atomic', 'true');
            document.body.appendChild(this.toastContainer);
            
            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                .toast-container {
                    position: fixed;
                    z-index: 9999;
                    padding: 15px;
                    pointer-events: none;
                }
                .toast-container.top-right {
                    top: 15px;
                    right: 15px;
                }
                .toast-container.top-left {
                    top: 15px;
                    left: 15px;
                }
                .toast-container.bottom-right {
                    bottom: 15px;
                    right: 15px;
                }
                .toast-container.bottom-left {
                    bottom: 15px;
                    left: 15px;
                }
                .toast-container.top-center {
                    top: 15px;
                    left: 50%;
                    transform: translateX(-50%);
                }
                .toast-container.bottom-center {
                    bottom: 15px;
                    left: 50%;
                    transform: translateX(-50%);
                }
                .toast {
                    display: flex;
                    align-items: center;
                    width: 350px;
                    min-height: 64px;
                    padding: 12px 16px;
                    margin-bottom: 10px;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    opacity: 0;
                    transform: translateY(15px);
                    transition: all 0.3s ease;
                    overflow: hidden;
                    pointer-events: auto;
                    position: relative;
                }
                .toast.show {
                    opacity: 1;
                    transform: translateY(0);
                }
                .toast.hide {
                    opacity: 0;
                    transform: translateY(-15px);
                }
                .toast-icon {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 24px;
                    height: 24px;
                    margin-right: 12px;
                    border-radius: 50%;
                    flex-shrink: 0;
                }
                .toast-content {
                    flex: 1;
                }
                .toast-title {
                    font-weight: bold;
                    margin-bottom: 4px;
                    font-size: 16px;
                }
                .toast-message {
                    margin: 0;
                    font-size: 14px;
                }
                .toast-close {
                    background: none;
                    border: none;
                    cursor: pointer;
                    font-size: 18px;
                    color: #6c757d;
                    margin-left: 8px;
                    padding: 0;
                    width: 24px;
                    height: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                    transition: background-color 0.2s;
                }
                .toast-close:hover {
                    background-color: rgba(0, 0, 0, 0.1);
                }
                .toast-progress {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    height: 3px;
                    width: 100%;
                    background-color: rgba(0, 0, 0, 0.1);
                }
                .toast-progress-bar {
                    height: 100%;
                    width: 100%;
                    transition: width linear;
                }
                .toast.success {
                    border-left: 4px solid #10B981;
                }
                .toast.success .toast-icon {
                    background-color: rgba(16, 185, 129, 0.2);
                    color: #10B981;
                }
                .toast.success .toast-progress-bar {
                    background-color: #10B981;
                }
                .toast.error {
                    border-left: 4px solid #EF4444;
                }
                .toast.error .toast-icon {
                    background-color: rgba(239, 68, 68, 0.2);
                    color: #EF4444;
                }
                .toast.error .toast-progress-bar {
                    background-color: #EF4444;
                }
                .toast.info {
                    border-left: 4px solid #3563E9;
                }
                .toast.info .toast-icon {
                    background-color: rgba(53, 99, 233, 0.2);
                    color: #3563E9;
                }
                .toast.info .toast-progress-bar {
                    background-color: #3563E9;
                }
                .toast.warning {
                    border-left: 4px solid #F59E0B;
                }
                .toast.warning .toast-icon {
                    background-color: rgba(245, 158, 11, 0.2);
                    color: #F59E0B;
                }
                .toast.warning .toast-progress-bar {
                    background-color: #F59E0B;
                }
                @media (max-width: 576px) {
                    .toast {
                        width: 100%;
                        max-width: 300px;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Set position class
        this.setPosition(this.position);
    }

    setPosition(position) {
        this.position = position;
        this.toastContainer.className = 'toast-container ' + position;
    }

    /**
     * Show a toast notification
     * @param {Object} options - Toast options
     * @param {string} options.type - Toast type (success, error, info, warning)
     * @param {string} options.title - Toast title
     * @param {string} options.message - Toast message
     * @param {number} options.duration - Duration in milliseconds
     * @param {boolean} options.dismissible - Whether the toast can be dismissed
     */
    show(options) {
        const {
            type = 'info',
            title = '',
            message = '',
            duration = this.defaultDuration,
            dismissible = true
        } = options;

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        // Set icon based on type
        let iconClass = '';
        switch (type) {
            case 'success':
                iconClass = 'bi-check-circle-fill';
                break;
            case 'error':
                iconClass = 'bi-x-circle-fill';
                break;
            case 'warning':
                iconClass = 'bi-exclamation-triangle-fill';
                break;
            case 'info':
            default:
                iconClass = 'bi-info-circle-fill';
                break;
        }
        
        // Create toast content
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="bi ${iconClass}"></i>
            </div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                ${message ? `<p class="toast-message">${message}</p>` : ''}
            </div>
            ${dismissible ? `<button class="toast-close" aria-label="Close">&times;</button>` : ''}
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        `;
        
        // Add to container
        this.toastContainer.appendChild(toast);
        
        // Limit the number of toasts
        this.toasts.push(toast);
        if (this.toasts.length > this.maxToasts) {
            this.removeToast(this.toasts[0]);
        }
        
        // Show toast with animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        // Add progress bar animation
        const progressBar = toast.querySelector('.toast-progress-bar');
        progressBar.style.transition = `width ${duration}ms linear`;
        
        // Start progress bar animation
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 10);
        
        // Close button event
        const closeButton = toast.querySelector('.toast-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                this.removeToast(toast);
            });
        }
        
        // Auto close after duration
        if (duration !== 0) {
            setTimeout(() => {
                this.removeToast(toast);
            }, duration);
        }
        
        return toast;
    }
    
    removeToast(toast) {
        if (!toast || !this.toastContainer.contains(toast)) return;
        
        toast.classList.add('hide');
        toast.classList.remove('show');
        
        setTimeout(() => {
            if (toast.parentNode === this.toastContainer) {
                this.toastContainer.removeChild(toast);
                this.toasts = this.toasts.filter(t => t !== toast);
            }
        }, 300);
    }
    
    // Convenience methods
    success(message, title = 'Success', duration = this.defaultDuration) {
        return this.show({ type: 'success', title, message, duration });
    }
    
    error(message, title = 'Error', duration = this.defaultDuration) {
        return this.show({ type: 'error', title, message, duration });
    }
    
    info(message, title = 'Information', duration = this.defaultDuration) {
        return this.show({ type: 'info', title, message, duration });
    }
    
    warning(message, title = 'Warning', duration = this.defaultDuration) {
        return this.show({ type: 'warning', title, message, duration });
    }
    
    // Clear all toasts
    clear() {
        while (this.toasts.length > 0) {
            this.removeToast(this.toasts[0]);
        }
    }
}

// Create global toast instance
const toast = new ToastManager();

// Example usage:
// toast.success('Your changes have been saved!');
// toast.error('Something went wrong!');
// toast.info('New update available');
// toast.warning('Your session will expire soon');
