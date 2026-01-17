@props([
    'availableStatuses' => [],
    'filterResolved' => null,
    'filterIssueType' => null,
    'excludeDeletedItems' => true,
])

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Status Filter using shared component --}}
    <x-filters.status-dropdown 
        label="Status"
        wireModel="filterResolved"
        :options="$availableStatuses"
        placeholder="Select status"
    />

    {{-- Issue Type Filter using shared component --}}
    <x-filters.status-dropdown 
        label="Type"
        wireModel="filterIssueType"
        :options="['slip' => 'Slip Issues', 'misc' => 'Miscellaneous Issues']"
        placeholder="Select type"
    />

    {{-- Date From Filter --}}
    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-gray-700">From Date</label>
            <button type="button" wire:click="$set('filterCreatedFrom', '')" 
                x-show="$wire.filterCreatedFrom"
                class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                Clear
            </button>
        </div>
        <input type="date" wire:model.live="filterCreatedFrom"
            class="block w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">
    </div>

    {{-- Date To Filter --}}
    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-gray-700">To Date</label>
            <button type="button" wire:click="$set('filterCreatedTo', '')" 
                x-show="$wire.filterCreatedTo"
                class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                Clear
            </button>
        </div>
        <input type="date" wire:model.live="filterCreatedTo"
            class="block w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">
    </div>

    {{-- Exclude Deleted Items Checkbox (SuperAdmin Only) --}}
    <div class="md:col-span-2">
        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" wire:model.live="excludeDeletedItems" 
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
            <span class="text-sm font-medium text-gray-700">Exclude issues with deleted items (slips, users, etc.)</span>
        </label>
    </div>

</div>
