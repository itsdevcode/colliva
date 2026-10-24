<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.marketplace')]
class Pricing extends Component
{
    public function render(): View
    {
        return view('livewire.pricing')->title('Pricing & Fees');
    }
}
