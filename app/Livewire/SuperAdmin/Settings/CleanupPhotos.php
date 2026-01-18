<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CleanupPhotos extends Component
{
    public $showModal = false;

    protected $listeners = ['openCleanupPhotosModal' => 'openModal'];

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
            $exitCode = \Illuminate\Support\Facades\Artisan::call('clean:photos');

            if ($exitCode === 0) {
                Logger::delete(
                    'Photo',
                    null,
                    "Manually ran Photo cleanup"
                );

                $this->showModal = false;
                $this->dispatch('cleanup-completed');
                $this->dispatch('toast', message: 'Photo cleanup completed successfully.', type: 'success');
            } else {
                $this->showModal = false;
                $this->dispatch('toast', message: 'Photo cleanup failed. Check logs for details.', type: 'error');
            }
        } catch (\Exception $e) {
            $this->showModal = false;
            $this->dispatch('toast', message: 'Error running Photo cleanup: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.super-admin.settings.cleanup-photos');
    }
}
