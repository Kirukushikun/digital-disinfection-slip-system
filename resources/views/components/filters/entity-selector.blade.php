@props([
    'label' => 'Entity',
    'wireModel' => 'filterEntity',
    'dataMethod' => 'getPaginatedEntities',
    'searchProperty' => 'searchFilterEntity',
    'placeholder' => 'Select...',
    'searchPlaceholder' => 'Search...',
    'multiple' => true,
    'perPage' => 20,
])

<div x-data="{ filterValue: @entangle($wireModel) }">
    <div class="flex items-center justify-between mb-1">
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
        <button type="button" wire:click="$set('{{ $wireModel }}', {{ $multiple ? '[]' : 'null' }})"
            x-show="filterValue && filterValue.length > 0"
            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
            Clear
        </button>
    </div>
    <x-forms.searchable-dropdown-paginated 
        wireModel="{{ $wireModel }}"
        data-method="{{ $dataMethod }}" 
        search-property="{{ $searchProperty }}" 
        placeholder="{{ $placeholder }}" 
        search-placeholder="{{ $searchPlaceholder }}" 
        :multiple="$multiple" 
        :per-page="$perPage" />
</div>
