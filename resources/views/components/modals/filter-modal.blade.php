@props([
    'bodyClass' => '',
])

<x-modals.modal-template 
    show="showFilters" 
    title="Filter Options" 
    maxWidth="max-w-2xl" 
    backdropOpacity="70"
>
    <x-slot name="footer">
        <div class="flex justify-end gap-3 w-full sm:w-auto sm:flex-row-reverse">
            <x-buttons.submit-button
                @click.prevent="
                    // Sync all multiselect dropdowns before applying filters
                    window.dispatchEvent(new CustomEvent('sync-selections'));
                    // Small delay to ensure all syncs complete before applying filters
                    setTimeout(() => {
                        $wire.call('applyFilters').then(() => {
                            $wire.set('showFilters', false);
                        });
                    }, 50);
                "
                class="w-full sm:w-auto">
                Apply
            </x-buttons.submit-button>
            <x-buttons.submit-button
                @click.prevent="
                    $wire.call('clearFilters').then(() => {
                        $wire.set('showFilters', false);
                    });
                "
                color="white"
                class="w-full sm:w-auto">
                Clear
            </x-buttons.submit-button>
        </div>
    </x-slot>

    {{-- Content with overflow-visible for dropdowns --}}
    <div class="overflow-visible {{ $bodyClass }}">
        {{ $filters }}
    </div>
</x-modals.modal-template>
