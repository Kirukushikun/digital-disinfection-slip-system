<?php

namespace App\Livewire;

use Livewire\Component;

class SidebarUser extends Component
{
    public $currentRoute;

    public function mount($currentRoute)
    {
        $this->currentRoute = $currentRoute;
    }

    public function render()
    {
        return view('livewire.sidebar-user');
    }
}
