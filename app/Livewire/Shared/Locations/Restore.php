<?php

namespace App\Livewire\Shared\Locations;

use App\Models\Location;
use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Restore extends Component
{
    public $showModal = false;
    public $locationId;
    public $locationName = ''; // Display name for the location
    public $isRestoring = false;

    // Configuration - minimum user_type required (2 = superadmin only)
    public $minUserType = 2;

    protected $listeners = [
        'openRestoreModal' => 'openModal'
    ];

    public function mount($config = [])
    {
        $this->minUserType = $config['minUserType'] ?? 2;
        $this->showModal = false; // Ensure modal is closed on mount
    }

    public function openModal($locationId)
    {
        $location = Location::onlyTrashed()->findOrFail($locationId);
        
        $this->locationId = $locationId;
        $this->locationName = $location->location_name;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['locationId', 'locationName', 'isRestoring']);
    }

    public function restore()
    {
        // Prevent multiple submissions
        if ($this->isRestoring) {
            return;
        }

        // Authorization check
        if (Auth::user()->user_type < $this->minUserType) {
            abort(403, 'Unauthorized action.');
        }

        $this->isRestoring = true;

        try {
            if (!$this->locationId) {
                return;
            }

            // Atomic restore: Only restore if currently deleted to prevent race conditions
            $restored = Location::onlyTrashed()
                ->where('id', $this->locationId)
                ->update(['deleted_at' => null]);
            
            if ($restored === 0) {
                // Location was already restored or doesn't exist
                $this->showModal = false;
                $this->reset(['locationId', 'locationName']);
                $this->dispatch('toast', message: 'This location was already restored or does not exist. Please refresh the page.', type: 'error');
                $this->dispatch('location-restored'); // Notify parent to refresh
                return;
            }
            
            // Now load the restored location
            $location = Location::findOrFail($this->locationId);
            
            // Log the restore action
            Logger::restore(
                Location::class,
                $location->id,
                "Restored location {$location->location_name}"
            );
            
            Cache::forget('locations_all');

            $this->showModal = false;
            $this->reset(['locationId', 'locationName']);
            $this->dispatch('location-restored');
            $this->dispatch('toast', message: "{$location->location_name} has been restored.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to restore location: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->isRestoring = false;
        }
    }

    public function render()
    {
        return view('livewire.shared.locations.restore');
    }
}
