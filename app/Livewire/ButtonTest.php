<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class ButtonTest extends Component
{
    public $counter = 0;

    public $message = '';

    public function increment()
    {
        $this->counter++;
        $this->message = 'Counter incremented!';
    }

    public function decrement()
    {
        $this->counter--;
        $this->message = 'Counter decremented!';
    }

    public function resetCounter()
    {
        $this->counter = 0;
        $this->message = 'Counter reset!';
    }

    public function render()
    {
        return view('livewire.button-test');
    }
}
