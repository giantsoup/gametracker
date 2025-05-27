import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Initialize Livewire
Livewire.start();

// Make Alpine available to the window object
window.Alpine = Alpine;
