<?php

namespace App\Livewire\Shared\Issues;

use App\Models\Issue;
use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Restore extends Component
{
    public $showModal = false;
    public $issueId;
    public $issueName = ''; // Display name for the issue
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

    public function openModal($issueId)
    {
        $issue = Issue::onlyTrashed()->with(['slip'])->findOrFail($issueId);
        
        $this->issueId = $issueId;
        $this->issueName = $issue->slip_id ? "this slip " . ($issue->slip->slip_id ?? 'N/A') : "this miscellaneous issue";
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['issueId', 'issueName', 'isRestoring']);
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
            if (!$this->issueId) {
                return;
            }

            // Atomic restore: Only restore if currently deleted to prevent race conditions
            // Do the atomic update first, then load the model only if successful
            $restored = Issue::onlyTrashed()
                ->where('id', $this->issueId)
                ->update(['deleted_at' => null]);
            
            if ($restored === 0) {
                // Issue was already restored or doesn't exist
                $this->showModal = false;
                $this->reset(['issueId', 'issueName']);
                $this->dispatch('toast', message: "This issue was already restored or does not exist. Please refresh the page.", type: 'error');
                $this->dispatch('issue-restored'); // Notify parent to refresh
                return;
            }
            
            // Now load the restored issue
            $issue = Issue::with(['slip'])->findOrFail($this->issueId);
            $issueType = $issue->slip_id ? "for slip " . ($issue->slip->slip_id ?? 'N/A') : "for misc";
            
            Logger::restore(
                Issue::class,
                $issue->id,
                "Restored issue {$issueType}"
            );
            
            Cache::forget('issues_all');
            $this->showModal = false;
            $this->reset(['issueId', 'issueName']);
            $this->dispatch('issue-restored');
            $this->dispatch('toast', message: "Issue ID:{$issue->id} has been restored successfully.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: "Failed to restore Issue ID:{$issue->id}: " . $e->getMessage(), type: 'error');
        } finally {
            $this->isRestoring = false;
        }
    }

    public function render()
    {
        return view('livewire.shared.issues.restore');
    }
}
