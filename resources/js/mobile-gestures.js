/**
 * Mobile Gestures for Event Runner
 *
 * This script adds touch gesture support for mobile devices.
 * It enables swipe navigation between game sections and other touch-friendly interactions.
 *
 * Implements Task 6.2: Mobile Navigation and Gestures
 * - Swipe gestures for game cards
 * - Mobile-specific navigation patterns
 * - Optimized scrolling and viewport behavior
 * - Performance optimizations for mobile devices
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize touch gesture handling
    initSwipeGestures();

    // Initialize smooth scrolling behavior
    initSmoothScrolling();

    // Initialize mobile viewport optimizations
    initMobileViewportOptimization();
});

/**
 * Initialize swipe gesture detection and handling
 */
function initSwipeGestures() {
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;

    // Minimum distance required for a swipe
    const minSwipeDistance = 50;

    // Get all game cards that can be swiped
    const gameCards = document.querySelectorAll('.game-card');

    gameCards.forEach(card => {
        // Touch start event
        card.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, false);

        // Touch end event
        card.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            handleSwipe(card);
        }, false);
    });

    /**
     * Handle swipe gestures on game cards
     *
     * This function detects swipe direction and performs appropriate actions:
     * - Swipe right: Show game details or expand card
     * - Swipe left: Show quick actions based on game status
     */
    function handleSwipe(card) {
        // Calculate horizontal and vertical distance
        const horizontalDistance = touchEndX - touchStartX;
        const verticalDistance = touchEndY - touchStartY;

        // Only process horizontal swipes (ignore vertical scrolling)
        if (Math.abs(horizontalDistance) > Math.abs(verticalDistance)) {
            // Check if the swipe distance is significant enough
            if (Math.abs(horizontalDistance) >= minSwipeDistance) {
                if (horizontalDistance > 0) {
                    // Swipe right - show more options or navigate to game details
                    handleSwipeRight(card);
                } else {
                    // Swipe left - show quick actions based on game status
                    handleSwipeLeft(card);
                }
            }
        }
    }

    /**
     * Handle swipe right gesture on a game card
     *
     * @param {HTMLElement} card - The game card element
     */
    function handleSwipeRight(card) {
        // Get the game ID from the card's data attribute
        const gameId = card.getAttribute('data-game-id');

        // If the card has options container, show it
        const optionsContainer = card.querySelector('.card-options');
        if (optionsContainer) {
            optionsContainer.classList.remove('hidden');
            optionsContainer.classList.add('flex');
            return;
        }

        // If no options container but we have a game ID, navigate to game details
        if (gameId) {
            // Add a visual feedback before navigation
            card.classList.add('scale-95', 'transition-transform');
            setTimeout(() => {
                window.location.href = `/games/${gameId}`;
            }, 150);
        }
    }

    /**
     * Handle swipe left gesture on a game card
     *
     * @param {HTMLElement} card - The game card element
     */
    function handleSwipeLeft(card) {
        // Hide options container if it exists
        const optionsContainer = card.querySelector('.card-options');
        if (optionsContainer && !optionsContainer.classList.contains('hidden')) {
            optionsContainer.classList.remove('flex');
            optionsContainer.classList.add('hidden');
            return;
        }

        // Get the game status from the card
        const statusBadge = card.querySelector('[class*="bg-yellow-100"], [class*="bg-blue-100"], [class*="bg-green-100"], [class*="bg-neutral-100"]');
        if (!statusBadge) return;

        // Determine the game status based on the badge color
        let gameStatus = '';
        if (statusBadge.classList.contains('bg-yellow-100') || statusBadge.classList.contains('dark:bg-yellow-900/20')) {
            gameStatus = 'ready';
        } else if (statusBadge.classList.contains('bg-blue-100') || statusBadge.classList.contains('dark:bg-blue-900/20')) {
            gameStatus = 'playing';
        } else if (statusBadge.classList.contains('bg-green-100') || statusBadge.classList.contains('dark:bg-green-900/20')) {
            gameStatus = 'finished';
        } else {
            gameStatus = 'background';
        }

        // Find the status manager component
        const statusManager = card.querySelector('[wire\\:id]');
        if (!statusManager) return;

        // Show quick action menu based on game status
        showQuickActionMenu(card, gameStatus, statusManager);
    }

    /**
     * Show a quick action menu for the game based on its status
     *
     * @param {HTMLElement} card - The game card element
     * @param {string} status - The game status
     * @param {HTMLElement} statusManager - The Livewire status manager component
     */
    function showQuickActionMenu(card, status, statusManager) {
        // Create quick action menu if it doesn't exist
        let quickActionMenu = card.querySelector('.quick-action-menu');
        if (!quickActionMenu) {
            quickActionMenu = document.createElement('div');
            quickActionMenu.className = 'quick-action-menu absolute right-0 top-0 bottom-0 flex flex-col justify-center bg-white dark:bg-neutral-800 shadow-lg rounded-l-lg p-2 transform transition-transform';
            card.style.position = 'relative';
            card.appendChild(quickActionMenu);
        }

        // Clear existing buttons
        quickActionMenu.innerHTML = '';

        // Add appropriate action buttons based on status
        if (status === 'ready') {
            // Start Playing button
            addQuickActionButton(quickActionMenu, 'Start Playing', 'blue', () => {
                Livewire.find(statusManager.getAttribute('wire:id')).call('confirmAction', 'markAsPlaying');
            });
        } else if (status === 'playing') {
            // Mark as Finished button
            addQuickActionButton(quickActionMenu, 'Mark as Finished', 'green', () => {
                Livewire.find(statusManager.getAttribute('wire:id')).call('confirmAction', 'markAsFinished');
            });
        } else if (status === 'finished') {
            // Assign Points button
            const gameId = card.getAttribute('data-game-id');
            if (gameId) {
                addQuickActionButton(quickActionMenu, 'Assign Points', 'purple', () => {
                    window.location.href = `/games/${gameId}/points/wizard`;
                });
            }
        }

        // Add Close button
        addQuickActionButton(quickActionMenu, 'Close', 'neutral', () => {
            quickActionMenu.style.transform = 'translateX(100%)';
            setTimeout(() => {
                quickActionMenu.remove();
            }, 300);
        });

        // Animate the menu in
        quickActionMenu.style.transform = 'translateX(100%)';
        setTimeout(() => {
            quickActionMenu.style.transform = 'translateX(0)';
        }, 10);
    }

    /**
     * Add a button to the quick action menu
     *
     * @param {HTMLElement} menu - The quick action menu element
     * @param {string} text - The button text
     * @param {string} color - The button color (blue, green, purple, neutral)
     * @param {Function} onClick - The click handler function
     */
    function addQuickActionButton(menu, text, color, onClick) {
        const button = document.createElement('button');
        button.className = `min-h-[44px] px-4 py-2 mb-2 rounded-md text-white font-medium text-base flex items-center justify-center`;

        // Set button color
        if (color === 'blue') {
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else if (color === 'green') {
            button.classList.add('bg-green-600', 'hover:bg-green-700');
        } else if (color === 'purple') {
            button.classList.add('bg-purple-600', 'hover:bg-purple-700');
        } else {
            button.classList.add('bg-neutral-600', 'hover:bg-neutral-700');
        }

        button.textContent = text;
        button.addEventListener('click', onClick);
        menu.appendChild(button);
    }

    /**
     * Toggle display of card options based on swipe direction
     */
    function toggleCardOptions(card, show) {
        const optionsContainer = card.querySelector('.card-options');
        if (optionsContainer) {
            if (show) {
                optionsContainer.classList.remove('hidden');
                optionsContainer.classList.add('flex');
            } else {
                optionsContainer.classList.remove('flex');
                optionsContainer.classList.add('hidden');
            }
        }
    }
}

/**
 * Initialize smooth scrolling behavior for mobile
 */
function initSmoothScrolling() {
    // Get all section navigation links
    const sectionLinks = document.querySelectorAll('.section-link');

    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                // Smooth scroll to the target section
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Update active state in navigation
                document.querySelectorAll('.section-link').forEach(l => {
                    l.classList.remove('active-section');
                });
                this.classList.add('active-section');
            }
        });
    });
}

/**
 * Add mobile-specific navigation patterns
 *
 * This function adds:
 * - A floating action button for quick access to common actions
 * - A bottom navigation bar for mobile devices
 * - Section indicators for better navigation awareness
 */
function addMobileNavigation() {
    // Only add mobile navigation on small screens
    if (window.innerWidth > 768) return;

    // Add section IDs if they don't exist
    addSectionIds();

    // Add bottom navigation bar if it doesn't exist
    addBottomNavBar();

    // Add floating action button if it doesn't exist
    addFloatingActionButton();
}

/**
 * Add IDs to game section containers for navigation
 */
function addSectionIds() {
    // Add IDs to the main game sections if they don't have them
    const sections = [
        { selector: '.border-blue-200', id: 'currently-playing-section', title: 'Playing' },
        { selector: '.border-yellow-200', id: 'ready-to-start-section', title: 'Ready' },
        { selector: '.border-green-200', id: 'finished-section', title: 'Finished' },
        { selector: '.border-neutral-200:not(.game-card)', id: 'background-section', title: 'Background' }
    ];

    sections.forEach(section => {
        const element = document.querySelector(section.selector);
        if (element && !element.id) {
            element.id = section.id;
        }
    });
}

/**
 * Add a bottom navigation bar for mobile devices
 */
function addBottomNavBar() {
    // Check if bottom nav already exists
    if (document.getElementById('mobile-bottom-nav')) return;

    // Create bottom navigation bar
    const bottomNav = document.createElement('div');
    bottomNav.id = 'mobile-bottom-nav';
    bottomNav.className = 'fixed bottom-0 left-0 right-0 bg-white dark:bg-neutral-800 shadow-lg border-t border-neutral-200 dark:border-neutral-700 flex justify-around items-center py-2 z-50 md:hidden';

    // Add navigation items
    const navItems = [
        { id: 'currently-playing-section', icon: 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z', label: 'Playing' },
        { id: 'ready-to-start-section', icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6', label: 'Ready' },
        { id: 'finished-section', icon: 'M5 13l4 4L19 7', label: 'Finished' },
        { id: 'background-section', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', label: 'Background' }
    ];

    navItems.forEach(item => {
        const navItem = document.createElement('a');
        navItem.href = `#${item.id}`;
        navItem.className = 'section-link flex flex-col items-center justify-center px-3 py-1 min-h-[44px] min-w-[64px]';

        // Create icon
        const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        icon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        icon.setAttribute('class', 'h-6 w-6 mb-1 text-neutral-500 dark:text-neutral-400');
        icon.setAttribute('fill', 'none');
        icon.setAttribute('viewBox', '0 0 24 24');
        icon.setAttribute('stroke', 'currentColor');

        // Add paths to the icon
        const paths = item.icon.split(' M');
        paths.forEach((path, index) => {
            if (index > 0) path = 'M' + path;
            const pathElement = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            pathElement.setAttribute('stroke-linecap', 'round');
            pathElement.setAttribute('stroke-linejoin', 'round');
            pathElement.setAttribute('stroke-width', '2');
            pathElement.setAttribute('d', path);
            icon.appendChild(pathElement);
        });

        // Create label
        const label = document.createElement('span');
        label.className = 'text-xs text-neutral-500 dark:text-neutral-400';
        label.textContent = item.label;

        // Add icon and label to nav item
        navItem.appendChild(icon);
        navItem.appendChild(label);

        // Add click event
        navItem.addEventListener('click', function(e) {
            e.preventDefault();
            const targetElement = document.getElementById(item.id);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Update active state
                document.querySelectorAll('.section-link').forEach(link => {
                    link.classList.remove('active-section');
                });
                this.classList.add('active-section');
            }
        });

        bottomNav.appendChild(navItem);
    });

    // Add bottom nav to the page
    document.body.appendChild(bottomNav);

    // Add padding to the bottom of the page to account for the nav bar
    document.body.style.paddingBottom = '60px';
}

/**
 * Add a floating action button for quick access to common actions
 */
function addFloatingActionButton() {
    // Check if FAB already exists
    if (document.getElementById('mobile-fab')) return;

    // Create floating action button
    const fab = document.createElement('div');
    fab.id = 'mobile-fab';
    fab.className = 'fixed bottom-20 right-4 z-50 md:hidden';

    // Create main button
    const mainButton = document.createElement('button');
    mainButton.className = 'bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg';

    // Add icon to main button
    const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    icon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    icon.setAttribute('class', 'h-8 w-8');
    icon.setAttribute('fill', 'none');
    icon.setAttribute('viewBox', '0 0 24 24');
    icon.setAttribute('stroke', 'currentColor');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('stroke-linejoin', 'round');
    path.setAttribute('stroke-width', '2');
    path.setAttribute('d', 'M12 6v6m0 0v6m0-6h6m-6 0H6');

    icon.appendChild(path);
    mainButton.appendChild(icon);
    fab.appendChild(mainButton);

    // Add click event to main button
    mainButton.addEventListener('click', function() {
        // Navigate to create game page
        window.location.href = '/games/create';
    });

    // Add FAB to the page
    document.body.appendChild(fab);
}

/**
 * Add mobile-specific classes and attributes to game cards
 *
 * This function:
 * - Adds the game-card class to all game cards for swipe functionality
 * - Adds data-game-id attribute for navigation
 * - Adds data-status attribute for status-based actions
 */
function addMobileClass() {
    // Select all game cards by their distinctive classes
    document.querySelectorAll('.rounded-lg.border.border-neutral-200.bg-white, .rounded-lg.border.border-neutral-200.dark\\:border-neutral-700').forEach(card => {
        // Add the game-card class for swipe functionality
        card.classList.add('game-card');

        // Try to find the game ID from any links to the game
        const gameLink = card.querySelector('a[href^="/games/"]');
        if (gameLink) {
            const href = gameLink.getAttribute('href');
            const gameId = href.split('/').filter(Boolean).pop();
            if (gameId && !isNaN(parseInt(gameId))) {
                card.setAttribute('data-game-id', gameId);
            }
        }

        // Try to determine game status from status badges
        const statusBadges = {
            'bg-yellow-100': 'ready',
            'bg-blue-100': 'playing',
            'bg-green-100': 'finished',
            'bg-neutral-100': 'background'
        };

        // Check for status badges
        for (const [className, status] of Object.entries(statusBadges)) {
            if (card.querySelector(`[class*="${className}"]`)) {
                card.setAttribute('data-status', status);
                break;
            }
        }
    });

    // Add mobile navigation patterns
    addMobileNavigation();
}

/**
 * Initialize mobile viewport optimizations
 *
 * This function optimizes the viewport behavior for mobile devices:
 * - Ensures smooth scrolling
 * - Handles orientation changes
 * - Prevents elastic scrolling on iOS
 * - Optimizes performance for mobile devices
 */
function initMobileViewportOptimization() {
    // Ensure smooth scrolling
    document.documentElement.style.scrollBehavior = 'smooth';

    // Add event listener for orientation change
    window.addEventListener('orientationchange', () => {
        // Force redraw after orientation change to prevent visual glitches
        setTimeout(() => {
            window.scrollTo(0, window.scrollY + 1);
            setTimeout(() => {
                window.scrollTo(0, window.scrollY - 1);
            }, 100);
        }, 500);
    });

    // Prevent elastic scrolling on iOS when in a modal
    document.body.addEventListener('touchmove', function(event) {
        if (document.documentElement.classList.contains('overflow-hidden')) {
            event.preventDefault();
        }
    }, { passive: false });

    // Add viewport meta tag for better mobile experience if not already present
    if (!document.querySelector('meta[name="viewport"]')) {
        const viewportMeta = document.createElement('meta');
        viewportMeta.name = 'viewport';
        viewportMeta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        document.head.appendChild(viewportMeta);
    }

    // Add touch-action CSS for better touch handling
    const style = document.createElement('style');
    style.textContent = `
        .game-card {
            touch-action: pan-y;
            -webkit-overflow-scrolling: touch;
        }
        .touch-none {
            touch-action: none;
        }
    `;
    document.head.appendChild(style);
}

// Initialize mobile-specific classes when Livewire updates the DOM
document.addEventListener('livewire:load', function() {
    addMobileClass();

    // Re-initialize when Livewire updates the DOM
    Livewire.hook('message.processed', (message, component) => {
        addMobileClass();
        initSwipeGestures();
    });
});
