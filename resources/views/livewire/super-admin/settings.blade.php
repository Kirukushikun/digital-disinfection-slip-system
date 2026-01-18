<div class="min-h-screen bg-gray-50 p-4 sm:p-6" wire:poll>
    <div class="max-w-6xl mx-auto">
        {{-- Settings Form Card --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                <p class="text-gray-600 text-sm mt-1">Manage system configuration and preferences</p>
            </div>

            <livewire:super-admin.settings.update />
        </div>
<div class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            {{-- Manual Cleanup Operations Section --}}
            <div>
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    {{-- Header --}}
                    <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">Manual Cleanup Operations</h2>
                        <p class="text-gray-600 text-sm mt-1">Run cleanup operations manually</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Photo Cleanup --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Photo Cleanup</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Delete photos older than the retention period. This removes uploaded photos and documents that are no longer needed.
                                        </p>
                                        <div class="mt-3 text-xs text-gray-500">
                                            <span class="font-medium">Retention:</span> {{ $this->attachmentRetentionDays }} days
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <button wire:click="openCleanupPhotosModal"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Clean Up
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Issues Cleanup --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Issues Cleanup</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Delete resolved issues older than the retention period to keep the system clean and focused on active issues.
                                        </p>
                                        <div class="mt-3 text-xs text-gray-500">
                                            <span class="font-medium">Retention:</span> {{ $this->resolvedIssuesRetentionMonths }} months
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <button wire:click="openCleanupIssuesModal"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Clean Up
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Soft-Deleted Records Cleanup --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Soft-Deleted Records Cleanup</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Permanently delete soft-deleted records (users, vehicles, drivers, locations, slips) older than the retention period.
                                        </p>
                                        <div class="mt-3 text-xs text-gray-500">
                                            <span class="font-medium">Retention:</span> {{ $this->softDeletedRetentionMonths }} months
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <button wire:click="openCleanupSoftDeletedModal"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Clean Up
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Audit Logs Cleanup --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Audit Logs Cleanup</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Delete audit trail logs older than the retention period to manage database size and focus on recent activities.
                                        </p>
                                        <div class="mt-3 text-xs text-gray-500">
                                            <span class="font-medium">Retention:</span> {{ $this->logRetentionMonths }} months
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <button wire:click="openCleanupLogsModal"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Clean Up
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cleanup Components --}}
            <livewire:super-admin.settings.cleanup-photos />
            <livewire:super-admin.settings.cleanup-issues />
            <livewire:super-admin.settings.cleanup-soft-deleted />
            <livewire:super-admin.settings.cleanup-logs />
        </div>
    </div>
</div>
