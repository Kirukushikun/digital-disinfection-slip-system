<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CleanupSoftDeleted extends Component
{
    public $showModal = false;

    protected $listeners = ['openCleanupSoftDeletedModal' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function cleanup()
    {
        // Authorization check
        if (Auth::user()->user_type < 2) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Use Artisan::call() instead of direct instantiation
            $exitCode = \Illuminate\Support\Facades\Artisan::call('clean:soft-deleted');

            if ($exitCode === 0) {
                Logger::delete(
                    'Multiple Models',
                    null,
                    "Manually ran soft-deleted records cleanup"
                );

                $this->showModal = false;
                $this->dispatch('cleanup-completed');
                $this->dispatch('toast', message: 'Soft-deleted records cleanup completed successfully.', type: 'success');
            } else {
                $this->showModal = false;
                $this->dispatch('toast', message: 'Soft-deleted records cleanup failed. Check logs for details.', type: 'error');
            }
        } catch (\Exception $e) {
            $this->showModal = false;
            $this->dispatch('toast', message: 'Error running soft-deleted records cleanup: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.super-admin.settings.cleanup-soft-deleted');
    }
}
