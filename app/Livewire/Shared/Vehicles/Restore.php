<?php

namespace App\Livewire\Shared\Vehicles;

use App\Models\Vehicle;
use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Restore extends Component
{
    public $showModal = false;
    public $vehicleId;
    public $vehicleName = ''; // Display name for the vehicle
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

    public function openModal($vehicleId)
    {
        $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);
        
        $this->vehicleId = $vehicleId;
        $this->vehicleName = $vehicle->vehicle;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['vehicleId', 'vehicleName', 'isRestoring']);
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
            if (!$this->vehicleId) {
                return;
            }

            // Atomic restore: Only restore if currently deleted to prevent race conditions
            $restored = Vehicle::onlyTrashed()
                ->where('id', $this->vehicleId)
                ->update(['deleted_at' => null]);
            
            if ($restored === 0) {
                // Vehicle was already restored or doesn't exist
                $this->showModal = false;
                $this->reset(['vehicleId', 'vehicleName']);
                $this->dispatch('toast', message: 'This vehicle was already restored or does not exist. Please refresh the page.', type: 'error');
                $this->dispatch('vehicle-restored'); // Notify parent to refresh
                return;
            }
            
            // Now load the restored vehicle
            $vehicle = Vehicle::findOrFail($this->vehicleId);
            
            // Log the restore action
            Logger::restore(
                Vehicle::class,
                $vehicle->id,
                "Restored vehicle {$vehicle->vehicle}"
            );
            
            Cache::forget('vehicles_all');

            $this->showModal = false;
            $this->reset(['vehicleId', 'vehicleName']);
            $this->dispatch('vehicle-restored');
            $this->dispatch('toast', message: "{$vehicle->vehicle} has been restored.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to restore vehicle: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->isRestoring = false;
        }
    }

    public function render()
    {
        return view('livewire.shared.vehicles.restore');
    }
}
