@if(app()->environment('local', 'development', 'testing'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded and parsed');

        // Check if Livewire is loaded
        if (window.Livewire) {
            console.log('Livewire is loaded and available');

            // Log Livewire hooks for debugging
            window.Livewire.hook('component.initialized', component => {
                console.log('Component initialized:', component.id);
            });

            window.Livewire.hook('element.initialized', (el, component) => {
                console.log('Element initialized:', el, 'for component:', component.id);
            });

            window.Livewire.hook('element.updating', (fromEl, toEl, component) => {
                console.log('Element updating:', fromEl, 'to:', toEl, 'for component:', component.id);
            });

            window.Livewire.hook('element.updated', (el, component) => {
                console.log('Element updated:', el, 'for component:', component.id);
            });

            window.Livewire.hook('message.sent', message => {
                console.log('Message sent:', message);
            });

            window.Livewire.hook('message.failed', message => {
                console.log('Message failed:', message);
            });

            window.Livewire.hook('message.received', message => {
                console.log('Message received:', message);
            });

            window.Livewire.hook('message.processed', message => {
                console.log('Message processed:', message);
            });
        } else {
            console.error('Livewire is not loaded! This could be why buttons are not working.');
        }

        // Check for any JavaScript errors
        window.addEventListener('error', function(event) {
            console.error('JavaScript error detected:', event.message, 'at', event.filename, 'line', event.lineno);
        });

        // Add specific debugging for layout buttons
        setTimeout(function() {
            // Get all layout buttons
            const layoutButtons = document.querySelectorAll('button[wire\\:click^="switchLayout"]');
            console.log('Found layout buttons:', layoutButtons.length);

            // Add click event listeners to log when buttons are clicked
            layoutButtons.forEach(button => {
                button.addEventListener('click', function() {
                    console.log('Layout button clicked:', this.getAttribute('wire:click'));

                    // Check if the click event is propagating
                    setTimeout(function() {
                        console.log('Current active layout buttons:', document.querySelectorAll('button[wire\\:click^="switchLayout"].bg-white').length);
                    }, 500);
                });
            });

            // Get test counter button
            const testCounterButton = document.getElementById('test-counter-button');
            if (testCounterButton) {
                console.log('Found test counter button');

                // Add click event listener to log when button is clicked
                testCounterButton.addEventListener('click', function() {
                    console.log('Test counter button clicked');

                    // Check if the click event is propagating
                    setTimeout(function() {
                        // Using a more compatible selector approach
                        const spans = document.querySelectorAll('span');
                        for (const span of spans) {
                            if (span.textContent.includes('Test Counter:')) {
                                console.log('Current test counter value:', span.textContent);
                                break;
                            }
                        }
                    }, 500);
                });
            } else {
                console.error('Test counter button not found');
            }

            // Check if Livewire is properly initialized
            if (window.Livewire) {
                console.log('Livewire object:', window.Livewire);
                console.log('Livewire components:', window.Livewire.components);

                // Check if the Dashboard component is registered
                const dashboardComponent = window.Livewire.components.components.find(c => c.name === 'dashboard');
                if (dashboardComponent) {
                    console.log('Dashboard component found:', dashboardComponent);
                } else {
                    console.error('Dashboard component not found');
                }
            }
        }, 1000); // Wait for the DOM to be fully loaded
    });
</script>
@endif
