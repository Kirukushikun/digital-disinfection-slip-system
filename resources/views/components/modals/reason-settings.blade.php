{{-- Reasons Settings Modal --}}
<x-modals.modal-template 
    show="showReasonsModal" 
    title="Reasons Settings" 
    maxWidth="max-w-2xl"
>
    {{-- Modal body content will go here --}}
    <div class="space-y-4">
        <p class="text-sm text-gray-600">Configure reasons for incomplete disinfection slips.</p>
        
        {{-- Placeholder for reasons management UI --}}
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Reasons management UI coming soon</p>
        </div>
    </div>

    <x-slot name="footer">
        <button 
            @click="show = false" 
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
        >
            Cancel
        </button>
        <button 
            wire:click="saveSettings" 
            type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
        >
            Save Changes
        </button>
    </x-slot>
</x-modals.modal-template>