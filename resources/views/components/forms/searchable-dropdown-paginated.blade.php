@props([
    'wireModel' => null,
    'searchProperty' => null,
    'placeholder' => 'Select an option...',
    'searchPlaceholder' => 'Search...',
    'label' => null,
    'error' => null,
    'disabled' => false,
    'multiple' => false,
    'maxShown' => 5,
    'max-shown' => 5,
    'perPage' => 20, // Number of items per page
    'dataMethod' => null, // Livewire method to fetch data
])

@php
$wireModel = $wireModel ?? $attributes->get('wire-model');
$searchProperty = $searchProperty ?? $attributes->get('search-property');
$searchPlaceholder = $searchPlaceholder ?? ($attributes->get('search-placeholder') ?? 'Search...');
$maxShown = $maxShown ?? $attributes->get('max-shown', 5);
$perPage = $perPage ?? $attributes->get('per-page', 20);
$dataMethod = $dataMethod ?? $attributes->get('data-method');

if (!$wireModel) {
    throw new \Exception('wireModel or wire-model attribute is required for searchable-dropdown-paginated component');
}

if (!$dataMethod) {
    throw new \Exception('dataMethod or data-method attribute is required for searchable-dropdown-paginated component');
}

// Generate unique ID for this dropdown instance
$dropdownId = 'dropdown_' . str_replace(['.', '[', ']'], '_', $wireModel) . '_' . uniqid();

// Calculate max height based on maxShown (approximately 40px per item)
$maxHeight = $maxShown * 40 . 'px';
@endphp

<div class="w-full" wire:key="dropdown-{{ $dropdownId }}">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Alpine.js for open state, search, and server-side pagination --}}
    @if ($multiple)
    <div class="relative" wire:ignore x-data="{
        open: false,
        searchTerm: '',
        options: [],
        localSelection: [],
        page: 1,
        hasMore: true,
        loading: false,
        selectedLabels: {},
        async init() {
            this.localSelection = $wire.get('{{ $wireModel }}') || [];
            this.loadSelectedLabels();
            // Eager load first 10 items for instant display
            await this.loadInitialOptions();
        },
        async loadInitialOptions() {
            try {
                const response = await Livewire.find('{{ $this->getId() }}').call('{{ $dataMethod }}', '', 1, 10);
                if (response && response.data) {
                    const newItems = Object.entries(response.data).map(([id, label]) => ({
                        id: Number(id),
                        label: label
                    }));
                    this.options = newItems;
                    this.hasMore = response.has_more;
                    this.page = 2; // Next load starts at page 2
                }
            } catch (error) {
                // Silent fail
            }
        },
        async loadSelectedLabels() {
            if (this.localSelection.length > 0) {
                try {
                    const response = await Livewire.find('{{ $this->getId() }}').call('{{ $dataMethod }}', '', 1, {{ $perPage }}, this.localSelection);
                    if (response && response.data) {
                        this.selectedLabels = response.data;
                    }
                } catch (error) {
                    // Silent fail
                }
            }
        },
        async loadOptions(reset = false) {
            if (this.loading) return;
            
            if (reset) {
                this.page = 1;
                this.options = [];
                this.hasMore = true;
            }
            
            if (!this.hasMore) return;
            
            this.loading = true;
            try {
                const response = await Livewire.find('{{ $this->getId() }}').call('{{ $dataMethod }}', this.searchTerm, this.page, {{ $perPage }});
                
                if (response && response.data) {
                    // Convert object to array and append - maintains database order like table rows
                    const newItems = Object.entries(response.data).map(([id, label]) => ({
                        id: Number(id),
                        label: label
                    }));
                    this.options = [...this.options, ...newItems];
                    this.hasMore = response.has_more;
                    this.page++;
                }
            } catch (error) {
                // Silent fail
            } finally {
                this.loading = false;
            }
        },
        async toggleDropdown() {
            if (this.open) {
                this.closeDropdown();
            } else {
                this.open = true;
                if (this.options.length === 0) {
                    await this.loadOptions(true);
                }
            }
        },
        async handleSearch() {
            await this.loadOptions(true);
        },
        handleScroll(event) {
            const container = event.target;
            const threshold = 50; // Load more when 50px from bottom
            
            if (container.scrollHeight - container.scrollTop - container.clientHeight < threshold) {
                this.loadOptions();
            }
        },
        closeDropdown() {
            this.open = false;
            // If user searched, reset to initial state on next open
            if (this.searchTerm !== '') {
                this.searchTerm = '';
                this.options = [];
                this.page = 1;
                this.hasMore = true;
            } else {
                // Keep first 10 items (eager loaded), reset pagination for scrolled items
                this.options = this.options.slice(0, 10);
                this.page = 2;
                this.hasMore = true;
            }
        },
        syncToLivewire() {
            Livewire.find('{{ $this->getId() }}').set('{{ $wireModel }}', [...this.localSelection]);
            this.loadSelectedLabels();
        },
        updateSelection(val) {
            const index = this.localSelection.indexOf(val);
            if (index > -1) {
                this.localSelection.splice(index, 1);
            } else {
                this.localSelection.push(val);
            }
        },
        getDisplayText() {
            if (this.localSelection.length === 0) return '{{ $placeholder }}';
            
            // Use selectedLabels for display
            if (this.localSelection.length === 1) {
                const id = this.localSelection[0];
                const option = this.options.find(opt => opt.id === id);
                return this.selectedLabels[id] || (option ? option.label : '') || '{{ $placeholder }}';
            }
            
            return this.localSelection.length + ' selected';
        },
        handleFocusIn(event) {
            const target = event.target;
            const container = $refs.dropdownContainer;
            if (this.open && !container.contains(target)) {
                if (target.tagName === 'INPUT' ||
                    target.tagName === 'SELECT' ||
                    target.tagName === 'TEXTAREA' ||
                    (target.tagName === 'BUTTON' && target.closest('[x-data]') && !container.contains(target.closest('[x-data]')))) {
                    this.closeDropdown();
                }
            }
        }
    }" @else <div class="relative" wire:ignore x-data="{
        open: false,
        searchTerm: '',
        options: [],
        page: 1,
        hasMore: true,
        loading: false,
        selectedLabel: '',
        async init() {
            this.loadSelectedLabel();
            // Eager load first 10 items for instant display
            await this.loadInitialOptions();
        },
        async loadInitialOptions() {
            try {
                const response = await Livewire.find('{{ $this->getId() }}').call('{{ $dataMethod }}', '', 1, 10);
                if (response && response.data) {
                    const newItems = Object.entries(response.data).map(([id, label]) => ({
                        id: Number(id),
                        label: label
                    }));
                    this.options = newItems;
                    this.hasMore = response.has_more;
                    this.page = 2; // Next load starts at page 2
                }
            } catch (error) {
                // Silent fail
            }
        },
        async loadSelectedLabel() {
            try {
                const selectedId = Livewire.find('{{ $this->getId() }}').get('{{ $wireModel }}');
                if (selectedId) {
                    const response = await Livewire.find('{{ $this->getId() }}').call('{{ $dataMethod }}', '', 1, {{ $perPage }}, [selectedId]);
                    if (response && response.data && response.data[selectedId]) {
                        this.selectedLabel = response.data[selectedId];
                    }
                }
            } catch (error) {
                // Silent fail
            }
        },
        async loadOptions(reset = false) {
            if (this.loading) return;
            
            if (reset) {
                this.page = 1;
                this.options = [];
                this.hasMore = true;
            }
            
            if (!this.hasMore) return;
            
            this.loading = true;
            try {
                const response = await Livewire.find('{{ $this->getId() }}').call('{{ $dataMethod }}', this.searchTerm, this.page, {{ $perPage }});
                
                if (response && response.data) {
                    // Convert object to array and append - maintains database order like table rows
                    const newItems = Object.entries(response.data).map(([id, label]) => ({
                        id: Number(id),
                        label: label
                    }));
                    this.options = [...this.options, ...newItems];
                    this.hasMore = response.has_more;
                    this.page++;
                }
            } catch (error) {
                // Silent fail
            } finally {
                this.loading = false;
            }
        },
        async toggleDropdown() {
            if (this.open) {
                this.closeDropdown();
            } else {
                this.open = true;
                if (this.options.length === 0) {
                    await this.loadOptions(true);
                }
            }
        },
        async handleSearch() {
            await this.loadOptions(true);
        },
        handleScroll(event) {
            const container = event.target;
            const threshold = 50; // Load more when 50px from bottom
            
            if (container.scrollHeight - container.scrollTop - container.clientHeight < threshold) {
                this.loadOptions();
            }
        },
        closeDropdown() {
            this.open = false;
            // If user searched, reset to initial state on next open
            if (this.searchTerm !== '') {
                this.searchTerm = '';
                this.options = [];
                this.page = 1;
                this.hasMore = true;
            } else {
                // Keep first 10 items (eager loaded), reset pagination for scrolled items
                this.options = this.options.slice(0, 10);
                this.page = 2;
                this.hasMore = true;
            }
        },
        async selectOption(id) {
            Livewire.find('{{ $this->getId() }}').set('{{ $wireModel }}', Number(id));
            const option = this.options.find(opt => opt.id === id);
            this.selectedLabel = option ? option.label : '';
            this.closeDropdown();
        },
        getDisplayText() {
            try {
                const selected = Livewire.find('{{ $this->getId() }}').get('{{ $wireModel }}');
                if (!selected) return '{{ $placeholder }}';
                const option = this.options.find(opt => opt.id === selected);
                return this.selectedLabel || (option ? option.label : '') || '{{ $placeholder }}';
            } catch (error) {
                return this.selectedLabel || '{{ $placeholder }}';
            }
        },
        handleFocusIn(event) {
            const target = event.target;
            const container = $refs.dropdownContainer;
            if (this.open && !container.contains(target)) {
                if (target.tagName === 'INPUT' ||
                    target.tagName === 'SELECT' ||
                    target.tagName === 'TEXTAREA' ||
                    (target.tagName === 'BUTTON' && target.closest('[x-data]') && !container.contains(target.closest('[x-data]')))) {
                    this.closeDropdown();
                }
            }
        }
    }" @endif
            x-ref="dropdownContainer"
            @if ($multiple) @sync-selections.window="syncToLivewire()" @endif
            @click.outside="closeDropdown()"
            @click.stop
            @focusin.window="handleFocusIn($event)"
            x-init="$watch('open', value => { if (!value) { /* Dropdown closed */ } })">
            <!-- Dropdown Button -->
            <button type="button" x-on:click.stop="toggleDropdown()" @disabled($disabled)
                class="inline-flex justify-between w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed hover:cursor-pointer cursor-pointer"
                :class="{ 'ring-2 ring-blue-500': open }">
                <span
                    :class="{ 'text-gray-400': @if ($multiple) localSelection.length === 0 @else !(function() { try { return Livewire.find('{{ $this->getId() }}').get('{{ $wireModel }}'); } catch(e) { return false; } })() @endif }">
                    <span x-text="getDisplayText()"></span>
                </span>
                <svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5 ml-2 -mr-1 transition-transform"
                    :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1"
                style="display: none; z-index: 9999;" x-cloak @click.stop>

                <!-- Search Input -->
                @if ($searchProperty)
                    <input type="text" x-model="searchTerm" 
                        x-on:input.debounce.300ms="handleSearch()"
                        x-on:keydown.escape="closeDropdown()"
                        x-on:click.stop
                        class="block w-full px-4 py-2 text-gray-800 border rounded-md border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="{{ $searchPlaceholder }}" autocomplete="off">
                @endif

                <!-- Options List with Infinite Scroll -->
                <div class="overflow-y-auto" style="max-height: {{ $maxHeight }};" 
                    x-on:scroll="handleScroll($event)"
                    x-on:click.stop>
                    <template x-for="option in options" :key="option.id">
                        <a href="#"
                            @if ($multiple) x-on:click.prevent.stop="updateSelection(option.id)"
                        @else
                            x-on:click.prevent.stop="selectOption(option.id)" @endif
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md transition-colors"
                            :class="@if ($multiple) localSelection.includes(option.id) @else (function() { try { return Livewire.find('{{ $this->getId() }}').get('{{ $wireModel }}') == option.id; } catch(e) { return false; } })() @endif
                                ? 'bg-blue-50 text-blue-700' : ''">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div x-text="option.label.split(' @')[0]"></div>
                                    <div x-show="option.label.includes(' @')" class="text-xs text-gray-500" x-text="'@' + option.label.split(' @')[1]"></div>
                                </div>
                                @if ($multiple)
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"
                                        x-show="localSelection.includes(option.id)" style="display: none;">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </a>
                    </template>
                    
                    <!-- Loading indicator -->
                    <div x-show="loading" class="px-4 py-2 text-center text-sm text-gray-500">
                        Loading more...
                    </div>
                    
                    <!-- No results message -->
                    <div x-show="!loading && options.length === 0"
                        class="px-4 py-6 text-center text-sm text-gray-500" style="display: none;">
                        No results found
                    </div>
                </div>
            </div>
        </div>

        @if ($error)
            <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
        @endif
</div>
