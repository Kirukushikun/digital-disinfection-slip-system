<?php

namespace App\Livewire\Shared\Slips;

use App\Models\Reason;
use App\Services\Logger;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Disable extends Component
{
    public $showModal = false;
    public $reasonId;
    public $reasonDisabled = false;
    public $reasonText = '';
    public $isToggling = false;

    protected $listeners = ['openDisableModal' => 'openModal'];

    public function openModal($reasonId)
    {
        $reason = Reason::findOrFail($reasonId);
        $this->reasonId = $reasonId;
        $this->reasonDisabled = $reason->is_disabled;
        $this->reasonText = $reason->reason_text;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['reasonId', 'reasonDisabled', 'reasonText', 'isToggling']);
    }

    public function toggle()
    {
        $user = Auth::user();

        // Authorization check - allow admin, superadmin, or super guards
        if (!($user->user_type === 1 || $user->user_type === 2 || ($user->user_type === 0 && $user->super_guard))) {
            return $this->redirect('/', navigate: true);
        }

        if ($this->isToggling) {
            return;
        }

        $this->isToggling = true;

        try {
            DB::beginTransaction();

            $reason = Reason::findOrFail($this->reasonId);
            $oldStatus = $reason->is_disabled;
            $newStatus = !$oldStatus;

            $updated = Reason::where('id', $this->reasonId)
                ->update(['is_disabled' => $newStatus]);

            if ($updated === 0) {
                throw new \Exception('Reason not found or update failed');
            }

            Logger::log(
                'update',
                Reason::class,
                $reason->id,
                "Toggled reason status: {$reason->reason_text}",
                ['is_disabled' => $oldStatus],
                ['is_disabled' => $newStatus]
            );

            DB::commit();

            $this->reasonText = Reason::findOrFail($this->reasonId)->reason_text;

            $message = !$oldStatus ? "\"{$this->reasonText}\" has been disabled successfully." : "\"{$this->reasonText}\" has been enabled successfully.";

            $this->showModal = false;
            $this->reset(['reasonId', 'reasonDisabled', 'reasonText']);
            $this->dispatch('reason-status-toggled');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', message: 'Failed to toggle status: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->isToggling = false;
        }
    }

    public function render()
    {
        return view('livewire.shared.slips.disable');
    }
}