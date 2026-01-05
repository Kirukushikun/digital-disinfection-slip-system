<div class="grid grid-cols-1 gap-4">

    {{-- From Date Input --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Deleted From Date</label>
        <input type="date" wire:model.live="filterCreatedFrom"
            class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">
    </div>

    {{-- To Date Input --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Deleted To Date</label>
        <input type="date" wire:model.live="filterCreatedTo"
            class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">
    </div>

</div>
