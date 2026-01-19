<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Logger;

class Update extends Component
{
    use WithFileUploads;

    // Settings values
    public $attachment_retention_days;
    public $default_guard_password;
    public $default_location_logo;
    public $log_retention_months;
    public $resolved_issues_retention_months;
    public $soft_deleted_retention_months;
    
    // File upload
    public $default_logo_file;
    public $current_logo_path;

    // Original values for change detection
    public $original_attachment_retention_days;
    public $original_default_guard_password;
    public $original_log_retention_months;
    public $original_resolved_issues_retention_months;
    public $original_soft_deleted_retention_months;

    public function mount()
    {
        // Load current settings
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $attachmentRetention = Setting::where('setting_name', 'attachment_retention_days')->first();
        $defaultPassword = Setting::where('setting_name', 'default_guard_password')->first();
        $defaultLogo = Setting::where('setting_name', 'default_location_logo')->first();
        $logRetention = Setting::where('setting_name', 'log_retention_months')->first();
        $resolvedIssuesRetention = Setting::where('setting_name', 'resolved_issues_retention_months')->first();
        $softDeletedRetention = Setting::where('setting_name', 'soft_deleted_retention_months')->first();

        $this->attachment_retention_days = $attachmentRetention ? $attachmentRetention->value : '30';
        // Don't populate the password field for security - keep it blank
        $this->default_guard_password = '';
        $this->default_location_logo = $defaultLogo ? $defaultLogo->value : 'images/logo/BGC.png';
        $this->log_retention_months = $logRetention ? $logRetention->value : '3';
        $this->resolved_issues_retention_months = $resolvedIssuesRetention ? $resolvedIssuesRetention->value : '3';
        $this->soft_deleted_retention_months = $softDeletedRetention ? $softDeletedRetention->value : '3';
        $this->current_logo_path = $this->default_location_logo;
        
        // Store original values for change detection
        $this->original_attachment_retention_days = $this->attachment_retention_days;
        // Store the actual stored password for change detection, but keep field blank
        $this->original_default_guard_password = $defaultPassword ? $defaultPassword->value : 'brookside25';
        $this->original_log_retention_months = $this->log_retention_months;
        $this->original_resolved_issues_retention_months = $this->resolved_issues_retention_months;
        $this->original_soft_deleted_retention_months = $this->soft_deleted_retention_months;
    }

    public function getHasChangesProperty()
    {
        $attachmentChanged = (string)$this->original_attachment_retention_days !== (string)$this->attachment_retention_days;
        // Password changed if user entered any non-empty password (since field starts blank)
        $passwordChanged = !empty($this->default_guard_password);

        $logRetentionChanged = (string)$this->original_log_retention_months !== (string)$this->log_retention_months;
        $resolvedIssuesRetentionChanged = (string)$this->original_resolved_issues_retention_months !== (string)$this->resolved_issues_retention_months;
        $softDeletedRetentionChanged = (string)$this->original_soft_deleted_retention_months !== (string)$this->soft_deleted_retention_months;
        $logoChanged = $this->default_logo_file !== null;

        return $attachmentChanged || $passwordChanged || $logRetentionChanged || $resolvedIssuesRetentionChanged || $softDeletedRetentionChanged || $logoChanged;
    }
    
    public function clearLogo()
    {
        $this->default_logo_file = null;
        $this->resetValidation('default_logo_file');
    }

    public function update()
    {
        // Authorization check
        if (Auth::user()->user_type < 2) {
            abort(403, 'Unauthorized action.');
        }

        $this->validate([
            'attachment_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            'default_guard_password' => ['nullable', 'string', 'min:6', 'max:255'],
            'log_retention_months' => ['required', 'integer', 'min:1', 'max:120'],
            'resolved_issues_retention_months' => ['required', 'integer', 'min:1', 'max:120'],
            'soft_deleted_retention_months' => ['required', 'integer', 'min:1', 'max:120'],
            'default_logo_file' => ['nullable', 'image', 'max:2048'], // 2MB max
        ], [
            'attachment_retention_days.required' => 'Photo retention days is required.',
            'attachment_retention_days.integer' => 'Photo retention days must be a number.',
            'attachment_retention_days.min' => 'Photo retention days must be at least 1 day.',
            'attachment_retention_days.max' => 'Photo retention days cannot exceed 365 days.',
            'default_guard_password.min' => 'Default guard password must be at least 6 characters.',
            'log_retention_months.required' => 'Log retention months is required.',
            'log_retention_months.integer' => 'Log retention months must be a number.',
            'log_retention_months.min' => 'Log retention months must be at least 1 month.',
            'log_retention_months.max' => 'Log retention months cannot exceed 120 months (10 years).',
            'resolved_issues_retention_months.required' => 'Resolved issues retention months is required.',
            'resolved_issues_retention_months.integer' => 'Resolved issues retention months must be a number.',
            'resolved_issues_retention_months.min' => 'Resolved issues retention months must be at least 1 month.',
            'resolved_issues_retention_months.max' => 'Resolved issues retention months cannot exceed 120 months (10 years).',
            'soft_deleted_retention_months.required' => 'Soft-deleted retention months is required.',
            'soft_deleted_retention_months.integer' => 'Soft-deleted retention months must be a number.',
            'soft_deleted_retention_months.min' => 'Soft-deleted retention months must be at least 1 month.',
            'soft_deleted_retention_months.max' => 'Soft-deleted retention months cannot exceed 120 months (10 years).',
            'default_logo_file.image' => 'The default logo must be an image file.',
            'default_logo_file.max' => 'The default logo must not be larger than 2MB.',
        ]);

        // Check if there are any changes (excluding logo file which is handled separately)
        $attachmentChanged = (string)$this->original_attachment_retention_days !== (string)$this->attachment_retention_days;
        $passwordChanged = !empty($this->default_guard_password);
        $logRetentionChanged = (string)$this->original_log_retention_months !== (string)$this->log_retention_months;
        $resolvedIssuesRetentionChanged = (string)$this->original_resolved_issues_retention_months !== (string)$this->resolved_issues_retention_months;
        $softDeletedRetentionChanged = (string)$this->original_soft_deleted_retention_months !== (string)$this->soft_deleted_retention_months;
        $logoChanged = $this->default_logo_file !== null;


        if (!$attachmentChanged && !$passwordChanged && !$logRetentionChanged && !$resolvedIssuesRetentionChanged && !$softDeletedRetentionChanged && !$logoChanged) {
            $this->dispatch('toast', message: 'No changes detected.', type: 'info');
            return;
        }

        // Capture old values for logging
        $oldSettings = [
            'attachment_retention_days' => Setting::where('setting_name', 'attachment_retention_days')->value('value'),
            'default_guard_password' => $passwordChanged ? Setting::where('setting_name', 'default_guard_password')->value('value') : '[UNCHANGED]',
            'default_location_logo' => Setting::where('setting_name', 'default_location_logo')->value('value'),
            'log_retention_months' => Setting::where('setting_name', 'log_retention_months')->value('value'),
            'resolved_issues_retention_months' => Setting::where('setting_name', 'resolved_issues_retention_months')->value('value'),
            'soft_deleted_retention_months' => Setting::where('setting_name', 'soft_deleted_retention_months')->value('value'),
        ];
        
        // Update or create settings
        Setting::updateOrCreate(
            ['setting_name' => 'attachment_retention_days'],
            ['value' => (string)$this->attachment_retention_days]
        );

        // Update password if a new one was provided (any non-empty value since field starts blank)
        if (!empty($this->default_guard_password)) {
        Setting::updateOrCreate(
            ['setting_name' => 'default_guard_password'],
            ['value' => $this->default_guard_password]
        );
        }

        Setting::updateOrCreate(
            ['setting_name' => 'log_retention_months'],
            ['value' => (string)$this->log_retention_months]
        );

        Setting::updateOrCreate(
            ['setting_name' => 'resolved_issues_retention_months'],
            ['value' => (string)$this->resolved_issues_retention_months]
        );

        Setting::updateOrCreate(
            ['setting_name' => 'soft_deleted_retention_months'],
            ['value' => (string)$this->soft_deleted_retention_months]
        );

        // Handle logo file upload
        if ($this->default_logo_file) {
            // Delete old logo if it exists and is not the default seeded one
            $oldLogoPath = $this->default_location_logo;
            if ($oldLogoPath && $oldLogoPath !== 'images/logo/BGC.png' && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }
            
            // Store new logo in images/logos/ (plural) directory for user uploads
            // images/logo/ (singular) is reserved for static/seed logos tracked in git
            $logoPath = $this->default_logo_file->store('images/logos', 'public');
            $this->default_location_logo = $logoPath;
            $this->current_logo_path = $logoPath;
            $this->default_logo_file = null;
        }

        Setting::updateOrCreate(
            ['setting_name' => 'default_location_logo'],
            ['value' => $this->default_location_logo]
        );
        
        // Log the settings update
        $newSettings = [
            'attachment_retention_days' => (string)$this->attachment_retention_days,
            'default_guard_password' => $passwordChanged ? $this->default_guard_password : '[UNCHANGED]',
            'default_location_logo' => $this->default_location_logo,
            'log_retention_months' => (string)$this->log_retention_months,
            'resolved_issues_retention_months' => (string)$this->resolved_issues_retention_months,
            'soft_deleted_retention_months' => (string)$this->soft_deleted_retention_months,
        ];
        
        Logger::update(
            Setting::class,
            null,
            "Updated system settings",
            $oldSettings,
            $newSettings
        );

        // Update original values after successful save
        $this->original_attachment_retention_days = (string)$this->attachment_retention_days;
        // Update the stored password value for comparison, and reset field to blank
        if ($passwordChanged) {
        $this->original_default_guard_password = $this->default_guard_password;
        }
        $this->default_guard_password = ''; // Reset field to blank for security
        $this->original_log_retention_months = (string)$this->log_retention_months;
        $this->original_resolved_issues_retention_months = (string)$this->resolved_issues_retention_months;
        $this->original_soft_deleted_retention_months = (string)$this->soft_deleted_retention_months;
        
        $this->dispatch('settings-updated');
        $this->dispatch('toast', message: 'Settings have been updated successfully.', type: 'success');
    }
    
    public function getDefaultLogoPathProperty()
    {
        return $this->current_logo_path;
    }

    public function render()
    {
        return view('livewire.super-admin.settings.update');
    }
}
