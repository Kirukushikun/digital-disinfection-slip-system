<div class="max-w-full bg-white border border-gray-200 rounded-xl shadow-sm p-4 m-4">

    {{-- Search + Filter --}}
    <div class="mb-4 flex items-center gap-3">

        {{-- Search Bar --}}
        <div class="relative w-full">
            <label class="sr-only">Search</label>
            <input type="text" wire:model.live="search"
                class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg sm:text-sm 
                        focus:border-blue-500 focus:ring-blue-500"
                placeholder="Search...">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </div>
        </div>

        {{-- Filter Button --}}
        <x-buttons.submit-button wire:click="$toggle('showFilters')" color="orange">
            <div class="flex items-center gap-2">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12m-7 8h2" />
                </svg>
                <span>Filter</span>
            </div>
        </x-buttons.submit-button>

        {{-- Add Button (Only for Outgoing) --}}
        @if ($type === 'outgoing')
            <x-buttons.submit-button wire:click="openCreateModal" color="blue">
                <div class="flex items-center gap-2">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add</span>
                </div>
            </x-buttons.submit-button>
        @endif
    </div>

    {{-- Filter Modal --}}
    <x-modals.filter-modal />

    {{-- CREATE MODAL --}}
    <x-modals.modal-template show="showCreateModal" title="CREATE NEW DISINFECTION SLIP" max-width="max-w-3xl">

        {{-- Plate Number --}}
        <div class="grid grid-cols-3 mb-4">
            <div class="font-semibold text-gray-700">Plate No:<span class="text-red-500">*</span></div>
            <div class="col-span-2">
                <x-forms.searchable-dropdown wire-model="truck_id" :options="$trucks->pluck('plate_number', 'id')"
                    placeholder="Select plate number..." search-placeholder="Search plates..." />
                @error('truck_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Destination --}}
        <div class="grid grid-cols-3 mb-4">
            <div class="font-semibold text-gray-700">Destination:<span class="text-red-500">*</span></div>
            <div class="col-span-2">
                <x-forms.searchable-dropdown wire-model="destination_id" :options="$locations->pluck('location_name', 'id')"
                    placeholder="Select destination..." search-placeholder="Search locations..." />
                @error('destination_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Driver Name --}}
        <div class="grid grid-cols-3 mb-4">
            <div class="font-semibold text-gray-700">Driver Name:<span class="text-red-500">*</span></div>
            <div class="col-span-2">
                <x-forms.searchable-dropdown wire-model="driver_id" :options="$drivers->pluck('full_name', 'id')" placeholder="Select driver..."
                    search-placeholder="Search drivers..." />
                @error('driver_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Reason for Disinfection --}}
        <div class="grid grid-cols-3 mb-4">
            <div class="font-semibold text-gray-700">Reason:<span class="text-red-500">*</span></div>
            <div class="col-span-2">
                <textarea wire:model="reason_for_disinfection"
                    class="w-full border rounded px-2 py-1 text-sm border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    rows="6" placeholder="Enter reason for disinfection..."></textarea>
                @error('reason_for_disinfection')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Footer --}}
        <x-slot name="footer">
            <x-buttons.submit-button wire:click="closeCreateModal" color="white">
                Cancel
            </x-buttons.submit-button>

            <x-buttons.submit-button wire:click="createSlip" color="blue">
                Create Slip
            </x-buttons.submit-button>
        </x-slot>

    </x-modals.modal-template>

    {{-- Disinfection Slip Details Modal --}}
    <livewire:trucks.disinfection-slip />

    {{-- Card List --}}
    <div wire:poll class="space-y-3">

        @forelse ($slips as $slip)
            @php
                $statusMap = [
                    0 => ['label' => 'Ongoing', 'color' => 'border-red-500 bg-red-50'],
                    1 => ['label' => 'Disinfecting', 'color' => 'border-orange-500 bg-orange-50'],
                    2 => ['label' => 'Completed', 'color' => 'border-green-500 bg-green-50'],
                ];
                $status = $slip->status;
            @endphp

            {{-- Card (Now Clickable) --}}
            <div wire:click="$dispatch('open-disinfection-details', { id: {{ $slip->id }} })"
                class="flex justify-between items-center p-4 border-l-4 rounded-lg shadow-sm transition hover:shadow-md cursor-pointer {{ $statusMap[$status]['color'] }}">

                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <div class="font-semibold text-gray-600">Slip ID:</div>
                    <div class="text-gray-800">{{ $slip->slip_id }}</div>

                    <div class="font-semibold text-gray-600">Plate #:</div>
                    <div class="text-gray-800">{{ $slip->truck->plate_number }}</div>
                </div>

                {{-- Right Side --}}
                <div class="flex flex-col items-end">
                    {{-- Status Badge --}}
                    <span
                        class="px-3 py-1 text-xs font-semibold rounded-full
                        {{ $status === 0 ? 'bg-red-100 text-red-700' : '' }}
                        {{ $status === 1 ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $status === 2 ? 'bg-green-100 text-green-700' : '' }}">
                        {{ $statusMap[$status]['label'] }}
                    </span>
                </div>
            </div>

        @empty

            <div class="text-center py-6 text-gray-500">
                No truck slips found.
            </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    <x-buttons.nav-pagination :paginator="$slips" />
</div>
