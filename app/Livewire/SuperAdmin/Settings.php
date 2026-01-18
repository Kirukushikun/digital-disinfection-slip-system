<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    protected $listeners = [
        'settings-updated' => '$refresh',
        'cleanup-completed' => '$refresh',
    ];

    public function getAttachmentRetentionDaysProperty()
    {
        return Setting::where('setting_name', 'attachment_retention_days')->value('value') ?? '30';
    }

    public function getResolvedIssuesRetentionMonthsProperty()
    {
        return Setting::where('setting_name', 'resolved_issues_retention_months')->value('value') ?? '3';
    }

    public function getSoftDeletedRetentionMonthsProperty()
    {
        return Setting::where('setting_name', 'soft_deleted_retention_months')->value('value') ?? '3';
    }

    public function getLogRetentionMonthsProperty()
    {
        return Setting::where('setting_name', 'log_retention_months')->value('value') ?? '3';
    }

    public function openCleanupPhotosModal()
    {
        $this->dispatch('openCleanupPhotosModal');
    }

    public function openCleanupIssuesModal()
    {
        $this->dispatch('openCleanupIssuesModal');
    }

    public function openCleanupSoftDeletedModal()
    {
        $this->dispatch('openCleanupSoftDeletedModal');
    }

    public function openCleanupLogsModal()
    {
        $this->dispatch('openCleanupLogsModal');
    }

    public function render()
    {
        return view('livewire.super-admin.settings');
    }
}

