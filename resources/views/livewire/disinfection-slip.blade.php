@php
    use Illuminate\Support\Facades\Auth;
    $isHatcheryAssigned = Auth::id() === $selectedSlip?->hatchery_guard_id;
    $isNotCompleted = $selectedSlip?->status != 2;
@endphp

<div>
    {{-- MAIN DETAILS MODAL --}}
    <x-modal-template show="showDetailsModal"
        title="{{ strtoupper($selectedSlip?->location?->location_name . ' DISINFECTION SLIP DETAILS') }}"
        max-width="max-w-3xl">

        @if ($selectedSlip)

            {{-- Date --}}
            <div class="grid grid-cols-3 mb-2">
                <div class="font-semibold text-gray-700">Date:</div>
                <div class="col-span-2 text-gray-900">
                    {{ $selectedSlip->created_at->format('M d, Y - h:i A') }}
                </div>
            </div>

            {{-- Slip Number --}}
            <div class="grid grid-cols-3 mb-2">
                <div class="font-semibold text-gray-700">Slip No:</div>
                <div class="col-span-2 text-gray-900 font-semibold">
                    {{ $selectedSlip->slip_id }}
                </div>
            </div>

            {{-- Plate --}}
            <div class="grid grid-cols-3 mb-2">
                <div class="font-semibold text-gray-700">Plate No:</div>
                <div class="col-span-2 text-gray-900">
                    @if ($isEditing)
                        <x-searchable-dropdown wire-model="truck_id" :options="$trucks->pluck('plate_number', 'id')"
                            placeholder="Select plate number..." search-placeholder="Search plates..." />
                    @else
                        {{ $selectedSlip->truck->plate_number ?? 'N/A' }}
                    @endif
                </div>
            </div>

            {{-- Destination --}}
            <div class="grid grid-cols-3 mb-2">
                <div class="font-semibold text-gray-700">Destination:</div>
                <div class="col-span-2 text-gray-900">
                    @if ($isEditing)
                        <x-searchable-dropdown wire-model="destination_id" :options="$locations->pluck('location_name', 'id')"
                            placeholder="Select destination..." search-placeholder="Search locations..." />
                    @else
                        {{ $selectedSlip->destination->location_name ?? 'N/A' }}
                    @endif
                </div>
            </div>

            {{-- Driver --}}
            <div class="grid grid-cols-3 mb-2">
                <div class="font-semibold text-gray-700">Driver Name:</div>
                <div class="col-span-2 text-gray-900">
                    @if ($isEditing)
                        <x-searchable-dropdown wire-model="driver_id" :options="$drivers->pluck('full_name', 'id')" placeholder="Select driver..."
                            search-placeholder="Search drivers..." />
                    @else
                        {{ $selectedSlip->driver?->first_name . ' ' . $selectedSlip->driver?->last_name ?? 'N/A' }}
                    @endif
                </div>
            </div>

            {{-- Reason / textarea expands when editing --}}
            <div class="grid grid-cols-3 mb-2">
                <div class="font-semibold text-gray-700">Reason:</div>
                <div class="col-span-2 text-gray-900">
                    @if ($isEditing)
                        <textarea wire:model="reason_for_disinfection" class="w-full border rounded px-2 py-1 text-sm" rows="6"></textarea>
                    @else
                        {{ $selectedSlip->reason_for_disinfection ?? 'N/A' }}
                    @endif
                </div>
            </div>

            {{-- Hidden Display Info when NOT editing --}}
            @if (!$isEditing)

                <div class="grid grid-cols-3 mb-2">
                    <div class="font-semibold text-gray-700">Hatchery Guard:</div>
                    <div class="col-span-2 text-gray-900">
                        {{ $selectedSlip->hatcheryGuard?->first_name . ' ' . $selectedSlip->hatcheryGuard?->last_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="grid grid-cols-3 mb-2">
                    <div class="font-semibold text-gray-700">Received By:</div>
                    <div class="col-span-2 text-gray-900">
                        {{ $selectedSlip->receivedGuard?->first_name . ' ' . $selectedSlip->receivedGuard?->last_name ?? 'N/A' }}
                    </div>
                </div>

                <div class="grid grid-cols-3 mb-2">
                    <div class="font-semibold text-gray-700">Completion Date:</div>
                    <div class="col-span-2 text-gray-900">
                        {{ $selectedSlip->completed_at ? $selectedSlip->completed_at->format('M d, Y h:i A') : 'N/A' }}
                    </div>
                </div>

                <div class="grid grid-cols-3 mb-2">
                    <div class="font-semibold text-gray-700">Attachment:</div>
                    <div class="col-span-2">
                        @if ($selectedSlip->attachment)
                            <button wire:click="openAttachmentModal('{{ $selectedSlip->attachment->file_path }}')"
                                class="text-orange-500 hover:text-orange-600 underline">
                                See Attachment
                            </button>
                        @else
                            N/A
                        @endif
                    </div>
                </div>

            @endif
        @else
            <p class="text-gray-500 text-center">No details available.</p>
        @endif

        {{-- Footer --}}
        <x-slot name="footer">
            @if (!$isEditing)
                <x-submit-button wire:click="closeDetailsModal" color="white">
                    Close
                </x-submit-button>

                @if ($isHatcheryAssigned && $isNotCompleted)
                    <x-submit-button wire:click="editDetailsModal" color="blue">
                        Edit
                    </x-submit-button>
                @endif
            @else
                <div class="flex justify-between w-full">
                    <div>
                        <x-submit-button wire:click="$set('showDeleteConfirmation', true)" color="red">
                            Delete
                        </x-submit-button>
                    </div>
                    <div class="flex gap-2">
                        <x-submit-button wire:click="$set('showCancelConfirmation', true)" color="white">
                            Cancel
                        </x-submit-button>

                        <x-submit-button wire:click="save" color="green">
                            Save
                        </x-submit-button>
                    </div>
                </div>
            @endif
        </x-slot>

    </x-modal-template>

    {{-- Cancel Confirmation Modal --}}
    <x-modal-template show="showCancelConfirmation" title="DISCARD CHANGES?" max-width="max-w-md">
        <div class="text-center py-4">
            <svg class="mx-auto mb-4 text-yellow-500 w-16 h-16" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
            <p class="text-gray-700 text-lg mb-2">Are you sure you want to cancel?</p>
            <p class="text-gray-500 text-sm">All unsaved changes will be lost.</p>
        </div>

        <x-slot name="footer">
            <x-submit-button wire:click="$set('showCancelConfirmation', false)" color="white">
                Continue Editing
            </x-submit-button>
            <x-submit-button wire:click="cancelEdit" color="red">
                Yes, Discard Changes
            </x-submit-button>
        </x-slot>
    </x-modal-template>

    {{-- Delete Confirmation Modal --}}
    <x-modal-template show="showDeleteConfirmation" title="DELETE SLIP?" max-width="max-w-md">
        <div class="text-center py-4">
            <svg class="mx-auto mb-4 text-red-500 w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                </path>
            </svg>
            <p class="text-gray-700 text-lg font-semibold mb-2">Delete this disinfection slip?</p>
            <p class="text-gray-500 text-sm mb-1">Slip No: <span
                    class="font-semibold">{{ $selectedSlip?->slip_id }}</span></p>
            <p class="text-red-600 text-sm font-medium">This action cannot be undone!</p>
        </div>

        <x-slot name="footer">
            <x-submit-button wire:click="$set('showDeleteConfirmation', false)" color="white">
                Cancel
            </x-submit-button>
            <x-submit-button wire:click="deleteSlip" color="red">
                Yes, Delete Slip
            </x-submit-button>
        </x-slot>
    </x-modal-template>

    {{-- Attachment Modal --}}
    @if ($showAttachmentModal)
        <x-modal-template show="showAttachmentModal" title="ATTACHMENT PREVIEW" max-width="max-w-xl">
            @if ($attachmentFile)
                <img src="{{ Storage::url($attachmentFile) }}" class="w-full rounded-lg shadow">
            @endif

            <x-slot name="footer">
                <x-submit-button wire:click="closeAttachmentModal"
                    class="px-4 py-2 text-sm bg-gray-200 rounded-lg hover:bg-gray-300">
                    Close
                </x-submit-button>
            </x-slot>
        </x-modal-template>
    @endif

</div>
