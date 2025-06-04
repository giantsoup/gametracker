/**
 * GameTracker Keyboard Navigation
 *
 * This file contains functions to improve keyboard navigation for accessibility.
 * It implements Task 8.2: Performance and Accessibility - Keyboard navigation support
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize keyboard navigation
    initKeyboardNavigation();

    // Re-initialize when Livewire updates the DOM
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.processed', (message, component) => {
            initKeyboardNavigation();
        });
    }
});

/**
 * Initialize keyboard navigation for the application
 */
function initKeyboardNavigation() {
    // Add keyboard navigation for game cards
    initGameCardKeyboardNav();

    // Add keyboard navigation for section headings
    initSectionHeadingNav();

    // Add keyboard navigation for interactive elements
    initInteractiveElementsNav();
}

/**
 * Initialize keyboard navigation for game cards
 */
function initGameCardKeyboardNav() {
    // Find all game cards
    const gameCards = document.querySelectorAll('.game-card');

    gameCards.forEach((card, index) => {
        // Make the card focusable if it isn't already
        if (!card.hasAttribute('tabindex')) {
            card.setAttribute('tabindex', '0');
        }

        // Add keyboard event listener
        card.addEventListener('keydown', function(e) {
            // Enter or Space key to activate the card's primary action
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();

                // Find the primary action button in the card
                const primaryButton = card.querySelector('[wire\\:click], [href]');
                if (primaryButton) {
                    primaryButton.click();
                }
            }

            // Arrow keys for navigation between cards
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                const nextCard = gameCards[index + 1];
                if (nextCard) {
                    nextCard.focus();
                }
            }

            if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                const prevCard = gameCards[index - 1];
                if (prevCard) {
                    prevCard.focus();
                }
            }

            // Home key to go to the first card
            if (e.key === 'Home') {
                e.preventDefault();
                gameCards[0].focus();
            }

            // End key to go to the last card
            if (e.key === 'End') {
                e.preventDefault();
                gameCards[gameCards.length - 1].focus();
            }
        });
    });
}

/**
 * Initialize keyboard navigation for section headings
 */
function initSectionHeadingNav() {
    // Find all section headings
    const sectionHeadings = document.querySelectorAll('h2, h3');

    sectionHeadings.forEach((heading, index) => {
        // Make the heading focusable if it isn't already
        if (!heading.hasAttribute('tabindex')) {
            heading.setAttribute('tabindex', '0');
        }

        // Add keyboard event listener
        heading.addEventListener('keydown', function(e) {
            // Enter or Space key to expand/collapse the section
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();

                // Find the section content
                const sectionId = heading.id.replace('-heading', '-section');
                const section = document.getElementById(sectionId);

                if (section) {
                    // Toggle the section visibility
                    const isHidden = section.classList.contains('hidden');
                    if (isHidden) {
                        section.classList.remove('hidden');
                    } else {
                        section.classList.add('hidden');
                    }
                }
            }

            // Arrow keys for navigation between headings
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextHeading = sectionHeadings[index + 1];
                if (nextHeading) {
                    nextHeading.focus();
                }
            }

            if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevHeading = sectionHeadings[index - 1];
                if (prevHeading) {
                    prevHeading.focus();
                }
            }
        });
    });
}

/**
 * Initialize keyboard navigation for interactive elements
 */
function initInteractiveElementsNav() {
    // Find all interactive elements
    const interactiveElements = document.querySelectorAll('button, [role="button"], a, input, select, textarea, [tabindex="0"]');

    interactiveElements.forEach(element => {
        // Add the keyboard focus indicator class
        element.classList.add('keyboard-focus-indicator');

        // Ensure the element has a tabindex if it's not a native interactive element
        if (!['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA'].includes(element.tagName) && !element.hasAttribute('tabindex')) {
            element.setAttribute('tabindex', '0');
        }
    });
}

/**
 * Add a keyboard shortcut for the "Skip to content" link
 */
document.addEventListener('keydown', function(e) {
    // Alt+S to focus the "Skip to content" link
    if (e.altKey && e.key === 's') {
        e.preventDefault();

        const skipLink = document.querySelector('.skip-to-content');
        if (skipLink) {
            skipLink.focus();
        }
    }
});
