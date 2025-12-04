@props(['show', 'file', 'selectedSlip' => null])

<x-modals.modal-template :show="$show" title="Attachment Preview" max-width="max-w-4xl">

    @php
        $fileUrl = Storage::url($file);
        $extension = strtolower(pathinfo($file ?? '', PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $isReceivingGuard = \Illuminate\Support\Facades\Auth::id() === $selectedSlip?->received_guard_id;
        $status = $selectedSlip?->status ?? null;
    @endphp

    @if (in_array($extension, $imageExtensions))
        {{-- IMAGE PREVIEW ONLY --}}
        <img src="{{ $fileUrl }}" class="border shadow-md max-h-[80vh] object-contain mx-auto rounded-lg"
            alt="Attachment Preview">
    @else
        {{-- NO PREVIEW — ONLY LINK --}}
        <p class="text-sm text-gray-600 text-center p-4">
            This file type cannot be previewed.<br>
            <a href="{{ $fileUrl }}" target="_blank" class="text-orange-500 font-semibold underline">
                Download attachment
            </a>
        </p>
    @endif

    <x-slot name="footer">
        <div class="flex justify-between w-full">
            {{-- Left side: Remove button (only if receiving guard and disinfecting) --}}
            <div>
                @if ($isReceivingGuard && $status == 1)
                    <x-buttons.submit-button wire:click="$set('showRemoveAttachmentConfirmation', true)" color="red">
                        Remove Attachment
                    </x-buttons.submit-button>
                @endif
            </div>

            {{-- Right side: Close button --}}
            <div>
                <x-buttons.submit-button wire:click="closeAttachmentModal" color="white">
                    Close
                </x-buttons.submit-button>
            </div>
        </div>
    </x-slot>

</x-modals.modal-template>
