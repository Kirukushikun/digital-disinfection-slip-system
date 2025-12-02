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
        <x-submit-button wire:click="$toggle('showFilters')"
            class="w-auto px-4 py-2 flex items-center gap-2 whitespace-nowrap bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12m-7 8h2" />
            </svg>
            Filter
        </x-submit-button>
    </div>

    {{-- Filter Modal --}}
    <x-filter-modal />

    {{-- Disinfection Slip Details Modal --}}
    <livewire:disinfection-slip />

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

            {{-- Card --}}
            <div
                class="flex justify-between items-center p-4 border-l-4 rounded-lg shadow-sm transition hover:shadow-md {{ $statusMap[$status]['color'] }}">

                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <div class="font-semibold text-gray-600">Slip ID:</div>
                    <div class="text-gray-800">{{ $slip->slip_id }}</div>

                    <div class="font-semibold text-gray-600">Plate #:</div>
                    <div class="text-gray-800">{{ $slip->truck->plate_number }}</div>
                </div>


                {{-- Right Side --}}
                <div class="flex flex-col items-end">
                    {{-- Action btn --}}
                    <x-submit-button wire:click="$dispatch('open-disinfection-details', { id: {{ $slip->id }} })"
                        class="px-4 py-2 whitespace-nowrap bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
                        View
                    </x-submit-button>

                </div>
            </div>

        @empty

            <div class="text-center py-6 text-gray-500">
                No truck slips found.
            </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    <x-nav-pagination :paginator="$slips" />
</div>
