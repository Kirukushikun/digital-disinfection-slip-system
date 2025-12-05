<x-modals.modal-template show="showFilters" title="Filter Options">

    {{-- Filter Content Slot --}}
    {{ $filters }}

    {{-- Footer slot --}}
    <x-slot name="footer">
        <x-buttons.submit-button wire:click="clearFilters" @click="show = false" color="white"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 
                   rounded-lg hover:bg-gray-50 transition">
            Clear
        </x-buttons.submit-button>
        <x-buttons.submit-button wire:click="applyFilters" @click="show = false"
            class="px-4 py-2 text-sm font-medium text-white bg-orange-500 
                   rounded-lg hover:bg-orange-600 transition">
            Apply
        </x-buttons.submit-button>
    </x-slot>

</x-modals.modal-template>