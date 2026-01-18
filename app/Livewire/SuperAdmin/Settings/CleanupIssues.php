<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CleanupIssues extends Component
{
    public $showModal = false;

    protected $listeners = ['openCleanupIssuesModal' => 'openModal'];

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
            $exitCode = \Illuminate\Support\Facades\Artisan::call('clean:resolved-issues');

            if ($exitCode === 0) {
                Logger::delete(
                    'Issue',
                    null,
                    "Manually ran resolved issues cleanup"
                );

                $this->showModal = false;
                $this->dispatch('cleanup-completed');
                $this->dispatch('toast', message: 'Issues cleanup completed successfully.', type: 'success');
            } else {
                $this->showModal = false;
                $this->dispatch('toast', message: 'Issues cleanup failed. Check logs for details.', type: 'error');
            }
        } catch (\Exception $e) {
            $this->showModal = false;
            $this->dispatch('toast', message: 'Error running issues cleanup: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.super-admin.settings.cleanup-issues');
    }
}
