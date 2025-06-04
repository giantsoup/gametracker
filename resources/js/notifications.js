/**
 * GameTracker Notifications and Error Handling
 *
 * This file contains functions for displaying notifications and handling errors.
 * It implements:
 * - Task 7.1: Visual Feedback and Animations (notifications, loading states)
 * - Task 7.2: Error Handling and Validation (error messages, graceful recovery)
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification system
    initNotifications();

    // Add form validation listeners
    initFormValidation();

    // Listen for Livewire events
    initLivewireListeners();
});

/**
 * Initialize the notification system
 */
function initNotifications() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 flex flex-col items-end space-y-2';
        document.body.appendChild(container);
    }
}

/**
 * Show a notification message
 *
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, info)
 * @param {number} duration - How long to show the notification in milliseconds
 */
function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    // Add icon based on type
    let iconPath = '';
    if (type === 'success') {
        iconPath = 'M5 13l4 4L19 7';
    } else if (type === 'error') {
        iconPath = 'M6 18L18 6M6 6l12 12';
    } else {
        iconPath = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }

    // Create icon
    const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    icon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    icon.setAttribute('class', 'h-5 w-5 mr-2 flex-shrink-0');
    icon.setAttribute('fill', 'none');
    icon.setAttribute('viewBox', '0 0 24 24');
    icon.setAttribute('stroke', 'currentColor');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('stroke-linejoin', 'round');
    path.setAttribute('stroke-width', '2');
    path.setAttribute('d', iconPath);

    icon.appendChild(path);

    // Create message text
    const text = document.createElement('span');
    text.textContent = message;

    // Create close button
    const closeButton = document.createElement('button');
    closeButton.className = 'ml-4 text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200';
    closeButton.setAttribute('aria-label', 'Close notification');

    const closeIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    closeIcon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    closeIcon.setAttribute('class', 'h-4 w-4');
    closeIcon.setAttribute('fill', 'none');
    closeIcon.setAttribute('viewBox', '0 0 24 24');
    closeIcon.setAttribute('stroke', 'currentColor');

    const closePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    closePath.setAttribute('stroke-linecap', 'round');
    closePath.setAttribute('stroke-linejoin', 'round');
    closePath.setAttribute('stroke-width', '2');
    closePath.setAttribute('d', 'M6 18L18 6M6 6l12 12');

    closeIcon.appendChild(closePath);
    closeButton.appendChild(closeIcon);

    // Add event listener to close button
    closeButton.addEventListener('click', function() {
        removeNotification(notification);
    });

    // Assemble notification
    notification.appendChild(icon);
    notification.appendChild(text);
    notification.appendChild(closeButton);

    // Add to container
    container.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Auto-remove after duration
    setTimeout(() => {
        removeNotification(notification);
    }, duration);

    return notification;
}

/**
 * Remove a notification with animation
 *
 * @param {HTMLElement} notification - The notification element to remove
 */
function removeNotification(notification) {
    notification.classList.remove('show');

    // Wait for animation to complete before removing
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    // Find all forms in the application
    document.querySelectorAll('form').forEach(form => {
        // Skip forms that already have validation
        if (form.hasAttribute('data-validation-initialized')) return;

        // Mark as initialized
        form.setAttribute('data-validation-initialized', 'true');

        // Add submit event listener
        form.addEventListener('submit', function(event) {
            // Skip validation for forms with novalidate attribute
            if (form.hasAttribute('novalidate')) return;

            // Check HTML5 validation
            if (!form.checkValidity()) {
                event.preventDefault();

                // Find the first invalid field
                const invalidField = form.querySelector(':invalid');
                if (invalidField) {
                    // Focus the invalid field
                    invalidField.focus();

                    // Show error message
                    showFieldError(invalidField);

                    // Show notification
                    showNotification('Please correct the errors in the form.', 'error');
                }
            }
        });

        // Add input event listeners for real-time validation
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', function() {
                validateField(field);
            });

            field.addEventListener('input', function() {
                // Clear error when user starts typing
                clearFieldError(field);
            });
        });
    });
}

/**
 * Validate a single form field
 *
 * @param {HTMLElement} field - The field to validate
 * @returns {boolean} - Whether the field is valid
 */
function validateField(field) {
    // Skip disabled fields
    if (field.disabled) return true;

    // Clear previous error
    clearFieldError(field);

    // Check validity
    if (!field.validity.valid) {
        showFieldError(field);
        return false;
    }

    return true;
}

/**
 * Show error message for a field
 *
 * @param {HTMLElement} field - The field with an error
 */
function showFieldError(field) {
    // Add error class to field
    field.classList.add('error-field');

    // Get error message
    let errorMessage = '';

    if (field.validity.valueMissing) {
        errorMessage = field.getAttribute('data-required-message') || 'This field is required.';
    } else if (field.validity.typeMismatch) {
        errorMessage = field.getAttribute('data-type-message') || 'Please enter a valid value.';
    } else if (field.validity.patternMismatch) {
        errorMessage = field.getAttribute('data-pattern-message') || 'Please match the requested format.';
    } else if (field.validity.tooShort) {
        errorMessage = field.getAttribute('data-minlength-message') ||
            `Please use at least ${field.getAttribute('minlength')} characters.`;
    } else if (field.validity.tooLong) {
        errorMessage = field.getAttribute('data-maxlength-message') ||
            `Please use no more than ${field.getAttribute('maxlength')} characters.`;
    } else if (field.validity.rangeUnderflow) {
        errorMessage = field.getAttribute('data-min-message') ||
            `Please enter a value greater than or equal to ${field.getAttribute('min')}.`;
    } else if (field.validity.rangeOverflow) {
        errorMessage = field.getAttribute('data-max-message') ||
            `Please enter a value less than or equal to ${field.getAttribute('max')}.`;
    } else if (field.validity.stepMismatch) {
        errorMessage = field.getAttribute('data-step-message') || 'Please enter a valid value.';
    } else if (field.validity.badInput) {
        errorMessage = field.getAttribute('data-input-message') || 'Please enter a valid value.';
    } else if (field.validity.customError) {
        errorMessage = field.validationMessage;
    } else {
        errorMessage = field.getAttribute('data-error-message') || 'Please enter a valid value.';
    }

    // Create error message element if it doesn't exist
    let errorElement = field.parentNode.querySelector('.field-error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'field-error-message text-sm text-red-600 dark:text-red-400 mt-1 error-animation';

        // Insert after the field or at the end of the parent
        if (field.nextSibling) {
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        } else {
            field.parentNode.appendChild(errorElement);
        }
    }

    // Set error message
    errorElement.textContent = errorMessage;

    // Add error class to parent for styling
    field.closest('[data-flux-field]')?.classList.add('has-error');
}

/**
 * Clear error message for a field
 *
 * @param {HTMLElement} field - The field to clear error for
 */
function clearFieldError(field) {
    // Remove error class
    field.classList.remove('error-field');

    // Remove error message element
    const errorElement = field.parentNode.querySelector('.field-error-message');
    if (errorElement) {
        errorElement.parentNode.removeChild(errorElement);
    }

    // Remove error class from parent
    field.closest('[data-flux-field]')?.classList.remove('has-error');
}

/**
 * Initialize Livewire event listeners
 */
function initLivewireListeners() {
    // Only initialize if Livewire is available
    if (typeof Livewire === 'undefined') return;

    // Listen for Livewire events
    document.addEventListener('livewire:load', function() {
        // Show notifications for Livewire flash messages
        Livewire.on('notify', function(message, type = 'info') {
            showNotification(message, type);
        });

        // Show error notifications
        Livewire.on('error', function(message) {
            showNotification(message, 'error');
        });

        // Show success notifications
        Livewire.on('success', function(message) {
            showNotification(message, 'success');
        });

        // Add loading state to buttons when Livewire is processing
        Livewire.hook('message.sent', (message, component) => {
            // Find all buttons with wire:loading
            document.querySelectorAll('[wire\\:loading]').forEach(el => {
                el.classList.add('opacity-75', 'cursor-wait');
            });
        });

        // Remove loading state when Livewire is done
        Livewire.hook('message.processed', (message, component) => {
            // Find all buttons with wire:loading
            document.querySelectorAll('[wire\\:loading]').forEach(el => {
                el.classList.remove('opacity-75', 'cursor-wait');
            });
        });
    });
}

// Export functions for use in other files
export { showNotification, initNotifications, initFormValidation };
