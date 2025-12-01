<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DisinfectionSlip;
use Illuminate\Support\Facades\Session;

class TruckList extends Component
{
    public $type = 'incoming'; // or pass dynamically if needed

    public function render()
    {
        $slips = DisinfectionSlip::with('truck')
        ->when($this->type === 'incoming', fn($q) =>
            $q->where('destination_id', Session::get('location_id'))
                ->where('status', [0, 1])
        )
        ->when($this->type === 'outgoing', fn($q) =>
            $q->where('location_id', Session::get('location_id'))
                ->where('status', [0, 1])
        )
        ->when($this->type === 'completed', fn($q) =>
            $q->where('location_id', Session::get('location_id'))
            ->orWhere('destination_id', Session::get('location_id'))
            ->where('status', 2)
        )
            ->orderBy('created_at', 'desc')
            ->get();    

        return view('livewire.truck-list', [
            'slips' => $slips
        ]);
    }
}
