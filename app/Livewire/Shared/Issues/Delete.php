<?php

namespace App\Livewire\Shared\Issues;

use App\Models\Issue;
use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Delete extends Component
{
    public $showModal = false;
    public $issueId;
    public $issueName = '';
    public $isDeleting = false;

    // Configuration - minimum user_type required (1 = admin, 2 = superadmin)
    public $minUserType = 2;

    protected $listeners = [
        'openDeleteModal' => 'openModal',
        'openIssueDeleteModal' => 'openModal'
    ];

    public function mount($config = [])
    {
        $this->minUserType = $config['minUserType'] ?? 2;
        $this->showModal = false; // Ensure modal is closed on mount
    }

    public function openModal($issueId)
    {
        // Prevent opening if already deleting
        if ($this->isDeleting) {
            return;
        }

        // Prevent opening if already open for the same issue
        if ($this->showModal && $this->issueId == $issueId) {
            return;
        }

        // If modal is already open for a different issue, close it first
        if ($this->showModal && $this->issueId != $issueId) {
            $this->closeModal();
        }

        $issue = Issue::findOrFail($issueId);
        $this->issueId = $issueId;
        $this->issueName = $issue->slip_id ? "issue for slip " . ($issue->slip->slip_id ?? 'N/A') : "miscellaneous issue";
        $this->showModal = true;
    }

    public function closeModal()
    {
        // Prevent closing if currently deleting
        if ($this->isDeleting) {
            return;
        }

        $this->showModal = false;
        $this->reset(['issueId', 'issueName', 'isDeleting']);
    }

    public function delete()
    {
        // Prevent multiple submissions
        if ($this->isDeleting) {
            return;
        }

        // Authorization check
        if (Auth::user()->user_type < $this->minUserType) {
            abort(403, 'Unauthorized action.');
        }

        $this->isDeleting = true;

        try {
            $issue = Issue::findOrFail($this->issueId);
            $issueType = $issue->slip_id ? "for slip " . ($issue->slip->slip_id ?? 'N/A') : "for misc";
            $oldValues = $issue->only(['user_id', 'slip_id', 'description', 'resolved_at']);

            // Atomic delete: Only delete if not already deleted to prevent race conditions
            $deleted = Issue::where('id', $this->issueId)
                ->whereNull('deleted_at') // Only delete if not already deleted
                ->update(['deleted_at' => now()]);

            if ($deleted === 0) {
                // Issue was already deleted by another process
                $this->reset(['issueId', 'issueName', 'isDeleting']);
                $this->showModal = false;
                $this->dispatch('toast', message: "This issue was already deleted by another administrator. Please refresh the page.", type: 'error');
                return;
            }

            Logger::delete(
                Issue::class,
                $issue->id,
                "Deleted issue {$issueType}",
                $oldValues
            );

            Cache::forget('issues_all');

            $this->reset(['issueId', 'issueName', 'isDeleting']);
            $this->showModal = false;

            $this->dispatch('issue-deleted');
            $this->dispatch('toast', message: "Issue ID:{$issue->id} has been deleted successfully.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: "Failed to delete Issue ID:{$issue->id}: " . $e->getMessage(), type: 'error');
        } finally {
            $this->isDeleting = false;
        }
    }

    public function render()
    {
        return view('livewire.shared.issues.delete');
    }
}
