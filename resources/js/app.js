import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Initialize Livewire
Livewire.start();

// Make Alpine available to the window object for debugging
window.Alpine = Alpine;

// Log when Livewire is initialized
console.log('Livewire initialized from app.js');
