/**
 * GameTracker Application JavaScript
 *
 * This is the main entry point for the application's JavaScript.
 * It imports and initializes all necessary modules.
 */

// Import modules
import './mobile-gestures.js';
import './keyboard-navigation.js';
import { initNotifications, initFormValidation } from './notifications.js';

// Initialize any global JavaScript functionality here
document.addEventListener('DOMContentLoaded', function() {
    console.log('GameTracker application initialized');

    // Initialize notifications system
    initNotifications();

    // Initialize form validation
    initFormValidation();
});
