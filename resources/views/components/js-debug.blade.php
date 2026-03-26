@if(app()->environment('local', 'development', 'testing'))
<script>
    const logDashboardComponent = () => {
        if (! window.Livewire?.all) {
            console.error('Livewire is not loaded! This could be why buttons are not working.');

            return;
        }

        const dashboardComponent = window.Livewire
            .all()
            .find((component) => component.name === 'dashboard');

        if (dashboardComponent) {
            console.log('Dashboard component found:', dashboardComponent.id, dashboardComponent.name);
        } else {
            console.error('Dashboard component not found');
        }
    };

    document.addEventListener('livewire:init', () => {
        console.log('Livewire is loaded and available');

        window.Livewire.hook('component.init', ({ component }) => {
            console.log('Component initialized:', component.id, component.name);
        });

        window.Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            const callNames = Array.isArray(commit.calls)
                ? commit.calls.map((call) => call.method).join(', ')
                : 'none';
            const updateNames = commit.updates ? Object.keys(commit.updates).join(', ') : 'none';

            console.log('Commit queued:', component.id, `calls=${callNames}`, `updates=${updateNames}`);

            respond(() => {
                console.log('Commit response received:', component.id);
            });

            succeed(({ effects }) => {
                const effectNames = effects ? Object.keys(effects).join(', ') : 'none';

                console.log('Commit processed:', component.id, `effects=${effectNames}`);
            });

            fail(() => {
                console.error('Commit failed:', component.id);
            });
        });

        window.Livewire.hook('request', ({ url, options, respond, succeed, fail }) => {
            console.log('Request started:', url, options?.method ?? 'POST');

            respond(({ status }) => {
                console.log('Request response received:', status);
            });

            succeed(({ status }) => {
                console.log('Request succeeded:', status);
            });

            fail(({ status, content }) => {
                console.error('Request failed:', status, typeof content === 'string' ? content.slice(0, 200) : '');
            });
        });
    });

    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized');
        logDashboardComponent();
    });

    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM fully loaded and parsed');

        window.addEventListener('error', (event) => {
            console.error('JavaScript error detected:', event.message, 'at', event.filename, 'line', event.lineno);
        });

        setTimeout(() => {
            const layoutButtons = document.querySelectorAll('button[wire\\:click^="switchLayout"]');
            console.log('Found layout buttons:', layoutButtons.length);

            layoutButtons.forEach(button => {
                button.addEventListener('click', function () {
                    console.log('Layout button clicked:', this.getAttribute('wire:click'));

                    setTimeout(() => {
                        console.log('Current active layout buttons:', document.querySelectorAll('button[wire\\:click^="switchLayout"].bg-white').length);
                    }, 500);
                });
            });

            const testCounterButton = document.getElementById('test-counter-button');

            if (testCounterButton) {
                console.log('Found test counter button');

                testCounterButton.addEventListener('click', () => {
                    console.log('Test counter button clicked');

                    setTimeout(() => {
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

            logDashboardComponent();
        }, 1000);
    });
</script>
@endif
