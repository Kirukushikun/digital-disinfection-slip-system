<div>
{{-- Reasons Settings Modal --}}
<x-modals.modal-template
    show="showReasonsModal"
    title="Reasons Settings"
    maxWidth="max-w-3xl"
>
    <div class="space-y-4">
        {{-- Search Bar and Create Button --}}
        <div class="flex gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live="searchReasonSettings"
                    class="block w-full pl-10 {{ $this->searchReasonSettings ? 'pr-20' : 'pr-12' }} py-2.5 bg-white border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                    placeholder="Search reasons...">

                {{-- Right Side Buttons Container --}}
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                    {{-- Clear Button (X) - Only when search has text --}}
                    @if($this->searchReasonSettings)
                        <button wire:click="$set('searchReasonSettings', '')" type="button"
                            class="flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-150 hover:cursor-pointer cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif

                    {{-- Filter Dropdown --}}
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" type="button" title="Filter by status"
                            class="flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-150 focus:outline-none hover:cursor-pointer cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            @if($this->filterReasonStatus !== 'all')
                                <span class="ml-1 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                </span>
                            @endif
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                            style="display: none;">
                            <div class="py-1">
                                <button wire:click="$set('filterReasonStatus', 'all')" @click="open = false" type="button"
                                    class="w-full text-left px-4 py-2 text-sm {{ $this->filterReasonStatus === 'all' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    All
                                </button>
                                <button wire:click="$set('filterReasonStatus', 'enabled')" @click="open = false" type="button"
                                    class="w-full text-left px-4 py-2 text-sm {{ $this->filterReasonStatus === 'enabled' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    Enabled
                                </button>
                                <button wire:click="$set('filterReasonStatus', 'disabled')" @click="open = false" type="button"
                                    class="w-full text-left px-4 py-2 text-sm {{ $this->filterReasonStatus === 'disabled' ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                    Disabled
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-buttons.submit-button wire:click="openCreateReasonModal" color="blue" size="lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create
                </div>
            </x-buttons.submit-button>
        </div>

        {{-- Reasons List Container --}}
        <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200">
            @forelse($this->reasons as $index => $reason)
                <div class="flex items-center gap-3 p-3">
                    {{-- Reason Display/Edit --}}
                    <div class="flex-1 flex items-center gap-3">
                        @if($editingReasonId === $reason->id)
                            <input
                                type="text"
                                wire:model="editingReasonText"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Reason text"
                            >
                        @else
                            <span class="text-sm text-gray-900">{{ $reason->reason_text }}</span>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        {{-- Edit/Save Button --}}
                        @if($editingReasonId === $reason->id)
                            <button
                                wire:click="saveReasonEdit"
                                type="button"
                                class="flex items-center justify-center w-9 h-9 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                title="Save"
                            >
                                <svg wire:loading.remove wire:target="saveReasonEdit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <svg wire:loading wire:target="saveReasonEdit" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        @else
                            <button
                                wire:click="startEditingReason({{ $reason->id }})"
                                type="button"
                                class="flex items-center justify-center w-9 h-9 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="Edit"
                            >
                                <svg wire:loading.remove wire:target="startEditingReason({{ $reason->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <svg wire:loading wire:target="startEditingReason({{ $reason->id }})" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        @endif

                        {{-- Disable/Enable Button --}}
                        @if($editingReasonId !== $reason->id)
                        <button
                            wire:click="toggleReasonDisabled({{ $reason->id }})"
                            type="button"
                            class="flex items-center justify-center w-9 h-9 rounded-lg transition-colors {{ $reason->is_disabled ? 'text-green-600 hover:bg-green-50' : 'text-yellow-600 hover:bg-yellow-50' }}"
                            title="{{ $reason->is_disabled ? 'Enable' : 'Disable' }}"
                        >
                            <span wire:loading.remove wire:target="toggleReasonDisabled({{ $reason->id }})">
                                @if($reason->is_disabled)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                @endif
                            </span>
                            <svg wire:loading wire:target="toggleReasonDisabled({{ $reason->id }})" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        @endif

                        {{-- Delete Button (SuperAdmin only) --}}
                        @if($editingReasonId !== $reason->id && Auth::user()->user_type === 2)
                            <button
                                wire:click="confirmDeleteReason({{ $reason->id }})"
                                type="button"
                                class="flex items-center justify-center w-9 h-9 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Delete"
                            >
                                <svg wire:loading.remove wire:target="confirmDeleteReason({{ $reason->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <svg wire:loading wire:target="confirmDeleteReason({{ $reason->id }})" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No reasons found.</p>
                </div>
            @endforelse
        </div>

    </div>

    <x-slot name="footer">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 w-full">
            {{-- Pagination --}}
            <div class="flex-1">
                @if($this->reasons->hasPages())
                    <x-buttons.nav-pagination :paginator="$this->reasons" pageName="reasonsPage" />
                @endif
            </div>

            {{-- Close Button --}}
            <x-buttons.submit-button
                wire:click="attemptCloseReasonsModal"
                color="gray"
                size="lg"
                :fullWidth="false"
            >
                Close
            </x-buttons.submit-button>
        </div>
    </x-slot>
</x-modals.modal-template>

{{-- Create Reason Modal --}}
@if($this->showCreateReasonModal)
    <div class="fixed inset-0 z-60 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/80 transition-opacity" wire:click="closeCreateReasonModal"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full max-w-lg">
                <div class="px-6 py-4 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Create Reason</h3>
                </div>

                <div class="px-6 py-4">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason Text <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                wire:model="newReasonText"
                                maxlength="255"
                                class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter reason text"
                            >
                            @error('newReasonText')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button wire:click="closeCreateReasonModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 hover:cursor-pointer cursor-pointer">
                        Cancel
                    </button>
                    <button wire:click.prevent="createReason" wire:loading.attr="disabled" wire:target="createReason"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="createReason">Create</span>
                        <span wire:loading.inline-flex wire:target="createReason" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Delete Confirmation Modal --}}
@if($this->showReasonsDeleteConfirmation)
<div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/80 transition-opacity" wire:click="$set('showReasonsDeleteConfirmation', false)"></div>

    {{-- Modal Panel --}}
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md sm:max-w-lg" @click.stop>
                <div class="bg-white px-4 py-5 sm:px-6 sm:py-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 sm:ml-4 text-lg font-semibold text-gray-900">Delete Reason</h3>
                    </div>
                </div>

                <div class="px-4 py-3 sm:px-6 sm:py-4">
                    <p class="text-sm text-gray-600">
                        Are you sure you want to delete this reason? Any disinfection slips using this reason will show "No Reason" instead.
                    </p>
                </div>

                <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gray-50 flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-3">
                    <x-buttons.submit-button
                        wire:click="$set('showReasonsDeleteConfirmation', false)"
                        color="gray"
                        size="lg"
                        :fullWidth="true"
                        class="sm:fullWidth"
                    >
                        Cancel
                    </x-buttons.submit-button>

                    <x-buttons.submit-button
                        wire:click="deleteReason"
                        color="red"
                        size="lg"
                        :fullWidth="true"
                        class="sm:fullWidth"
                    >
                        Delete Reason
                    </x-buttons.submit-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>