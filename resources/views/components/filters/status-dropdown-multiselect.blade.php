@props([
    'label' => 'Status',
    'wireModel' => 'filterStatus',
    'options' => [],
    'placeholder' => 'Select statuses',
    'fullWidth' => false, // If true, spans full width (md:col-span-2)
])

<div class="{{ $fullWidth ? 'md:col-span-2' : '' }}" x-data="{
    open: false,
    options: @js($options),
    selected: @entangle($wireModel).live,
    placeholder: '{{ $placeholder }}',
    get displayText() {
        if (!this.selected || this.selected.length === 0) {
            return this.placeholder;
        }
        if (this.selected.length === 1) {
            const key = String(this.selected[0]);
            return this.options[key] || this.placeholder;
        }
        return this.selected.length + ' selected';
    },
    closeDropdown() {
        this.open = false;
    },
    handleFocusIn(event) {
        const target = event.target;
        const container = $refs.statusDropdownContainer;
        if (this.open && !container.contains(target)) {
            if (target.tagName === 'INPUT' ||
                target.tagName === 'SELECT' ||
                target.tagName === 'TEXTAREA' ||
                (target.tagName === 'BUTTON' && target.closest('[x-data]') && !container.contains(target.closest('[x-data]')))) {
                this.closeDropdown();
            }
        }
    },
    toggleOption(value) {
        const index = this.selected.indexOf(value);
        if (index > -1) {
            this.selected.splice(index, 1);
        } else {
            this.selected.push(value);
        }
    },
    isSelected(value) {
        return this.selected.includes(value);
    }
}" x-ref="statusDropdownContainer" @click.outside="closeDropdown()"
    @focusin.window="handleFocusIn($event)">
    <div class="flex items-center justify-between mb-1">
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
        <button type="button" wire:click="$set('{{ $wireModel }}', [])"
            x-show="selected && selected.length > 0"
            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
            Clear
        </button>
    </div>

    <div class="relative">
        <button type="button" x-on:click="open = !open"
            class="inline-flex justify-between w-full px-4 py-2 text-sm font-medium bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500"
            :class="{ 'ring-2 ring-blue-500': open }">
            <span :class="{ 'text-gray-400': !selected || selected.length === 0 }"
                x-text="displayText"></span>
            <svg class="ml-2 -mr-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 111.414 1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1 z-50 max-h-60 overflow-y-auto"
            style="display: none;" @click.stop>
            <template x-for="[value, label] in Object.entries(options)" :key="value">
                <a href="#"
                    @click.prevent="toggleOption(value)"
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md transition-colors"
                    :class="{
                        'bg-blue-50 text-blue-700': isSelected(value)
                    }">
                    <div class="flex items-center">
                        <input type="checkbox" :checked="isSelected(value)"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mr-3"
                            @click.stop="toggleOption(value)">
                        <span x-text="label"></span>
                    </div>
                </a>
            </template>
        </div>
    </div>
</div>