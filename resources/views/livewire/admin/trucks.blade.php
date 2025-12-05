<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Simple Header --}}
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Trucks from Disinfection Slips</h1>
                    <p class="text-gray-600 text-sm mt-1">View all trucks associated with disinfection slips</p>
                </div>
                
                {{-- Search and Filter Bar --}}
                <div class="flex gap-3 w-full lg:w-auto">
                    {{-- Search Bar --}}
                    <div class="relative flex-1 lg:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            class="block w-full pl-10 pr-10 py-2.5 bg-white border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Search slip no., plate no., driver or location...">
                        @if($search)
                            <button 
                                wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Filter Button --}}
                    <button 
                        wire:click="$toggle('showFilters')"
                        class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 relative">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters
                    </button>
                </div>
            </div>

            {{-- Active Filters Display --}}
            @if($filtersActive)
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-sm text-gray-600">Active filters:</span>
                    
                    @if($appliedStatus !== '')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Status: {{ $availableStatuses[$appliedStatus] }}
                            <button wire:click="removeFilter('status')" class="ml-1.5 inline-flex items-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if(!empty($appliedOrigin))
                        @foreach($appliedOrigin as $originId)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Origin: {{ $locations->find($originId)->location_name }}
                                <button wire:click="removeSpecificFilter('origin', {{ $originId }})" class="ml-1.5 inline-flex items-center">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    @endif

                    @if(!empty($appliedDestination))
                        @foreach($appliedDestination as $destinationId)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Destination: {{ $locations->find($destinationId)->location_name }}
                                <button wire:click="removeSpecificFilter('destination', {{ $destinationId }})" class="ml-1.5 inline-flex items-center">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    @endif

                    @if(!empty($appliedDriver))
                        @foreach($appliedDriver as $driverId)
                            @php
                                $driver = $drivers->find($driverId);
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Driver: {{ $driver->first_name }} {{ $driver->last_name }}
                                <button wire:click="removeSpecificFilter('driver', {{ $driverId }})" class="ml-1.5 inline-flex items-center">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    @endif

                    @if($appliedCreatedFrom)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            From: {{ \Carbon\Carbon::parse($appliedCreatedFrom)->format('M d, Y') }}
                            <button wire:click="removeFilter('createdFrom')" class="ml-1.5 inline-flex items-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if($appliedCreatedTo)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            To: {{ \Carbon\Carbon::parse($appliedCreatedTo)->format('M d, Y') }}
                            <button wire:click="removeFilter('createdTo')" class="ml-1.5 inline-flex items-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    <button wire:click="clearFilters" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors">
                        Clear all
                    </button>
                </div>
            @endif
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Slip No.
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehicle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Origin
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Destination
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($slips as $slip)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $slip->slip_id }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($slip->created_at)->format('M d, Y h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        // Determine what to show first based on search/filter
                                        $showDriverFirst = false;
                                        $showPlateFirst = true;
                                        
                                        // Check if searching for driver name
                                        if($search) {
                                            $driverName = strtolower($slip->driver->first_name . ' ' . $slip->driver->last_name);
                                            $searchLower = strtolower($search);
                                            if(str_contains($driverName, $searchLower)) {
                                                $showDriverFirst = true;
                                                $showPlateFirst = false;
                                            }
                                        }
                                        
                                        // Check if filtering by driver (takes precedence if both search and filter)
                                        if(!empty($appliedDriver)) {
                                            $showDriverFirst = true;
                                            $showPlateFirst = false;
                                        }
                                    @endphp
                                    
                                    @if($showDriverFirst)
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $slip->driver->first_name }} {{ $slip->driver->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $slip->truck->plate_number }}
                                        </div>
                                    @else
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $slip->truck->plate_number }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $slip->driver->first_name }} {{ $slip->driver->last_name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">
                                        {{ $slip->location->location_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">
                                        {{ $slip->destination->location_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($slip->status == 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Ongoing
                                        </span>
                                    @elseif($slip->status == 1)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Disinfected
                                        </span>
                                    @elseif($slip->status == 2)
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                            @if($slip->completed_at)
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    {{ \Carbon\Carbon::parse($slip->completed_at)->format('M d, Y h:i A') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">No disinfection slips found</h3>
                                        <p class="text-sm text-gray-500">
                                            @if($search)
                                                No results match your search "<span class="font-medium text-gray-700">{{ $search }}</span>".
                                            @else
                                                Get started by creating a new disinfection slip.
                                            @endif
                                        </p>
                                        @if($search)
                                            <button 
                                                wire:click="$set('search', '')"
                                                class="mt-4 inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-150">
                                                Clear search
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <x-buttons.nav-pagination :paginator="$slips" />
            </div>
        </div>

        {{-- Filter Modal --}}
        <x-modals.filter-modal>
            <x-slot name="filters">
                
                {{-- Status Filter --}}
                <div x-data="{
                    open: false,
                    selected: @entangle('filterStatus'),
                    options: @js($availableStatuses),
                    placeholder: 'All Statuses',
                    get displayText() {
                        if (this.selected !== '' && this.options[this.selected]) {
                            return this.options[this.selected];
                        }
                        return this.placeholder;
                    }
                }">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <button type="button" @click="selected = ''" 
                            x-show="selected !== ''"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Clear
                        </button>
                    </div>
                    
                    <div class="relative">
                        <!-- Dropdown Button -->
                        <button type="button" @click="open = !open"
                            class="inline-flex justify-between w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500"
                            :class="{ 'ring-2 ring-blue-500': open }">
                            <span x-text="displayText" :class="{ 'text-gray-400': selected === '' }"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2 -mr-1 transition-transform"
                                :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.outside="open = false" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" 
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75" 
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1 z-50"
                            style="display: none;">
                            
                            <!-- All Statuses Option -->
                            <a href="#" @click.prevent="selected = ''; open = false"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md"
                                :class="{ 'bg-blue-50 text-blue-700': selected === '' }">
                                <span>All Statuses</span>
                            </a>
                            
                            <!-- Status Options -->
                            <template x-for="[value, label] in Object.entries(options)" :key="value">
                                <a href="#" @click.prevent="selected = value; open = false"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md"
                                    :class="{ 'bg-blue-50 text-blue-700': selected === value }">
                                    <span x-text="label"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Origin Filter --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Origin</label>
                        <button type="button" wire:click="$set('filterOrigin', [])" 
                            @if(empty($filterOrigin)) style="display: none;" @endif
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Clear
                        </button>
                    </div>
                    <x-forms.searchable-dropdown 
                        wireModel="filterOrigin"
                        :options="$locations->pluck('location_name', 'id')->toArray()"
                        placeholder="Select origin locations..."
                        searchPlaceholder="Search locations..."
                        :multiple="true" />
                </div>

                {{-- Destination Filter --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Destination</label>
                        <button type="button" wire:click="$set('filterDestination', [])"
                            @if(empty($filterDestination)) style="display: none;" @endif
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Clear
                        </button>
                    </div>
                    <x-forms.searchable-dropdown 
                        wireModel="filterDestination"
                        :options="$locations->pluck('location_name', 'id')->toArray()"
                        placeholder="Select destination locations..."
                        searchPlaceholder="Search locations..."
                        :multiple="true" />
                </div>

                {{-- Driver Filter --}}
                <div class="mt-4">
                    @php
                        $driverOptions = $drivers->mapWithKeys(function($driver) {
                            return [$driver->id => $driver->first_name . ' ' . $driver->last_name];
                        })->toArray();
                    @endphp
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Driver</label>
                        <button type="button" wire:click="$set('filterDriver', [])"
                            @if(empty($filterDriver)) style="display: none;" @endif
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Clear
                        </button>
                    </div>
                    <x-forms.searchable-dropdown 
                        wireModel="filterDriver"
                        :options="$driverOptions"
                        placeholder="Select drivers..."
                        searchPlaceholder="Search drivers..."
                        :multiple="true" />
                </div>

                {{-- Created Date Range Filter --}}
                <div class="mt-4" x-data="{ fromDate: @entangle('filterCreatedFrom') }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Created Date Range</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">From</label>
                            <input type="date" wire:model="filterCreatedFrom" x-model="fromDate" max="{{ date('Y-m-d') }}"
                                class="py-2 px-3 block w-full border-gray-300 shadow-sm rounded-lg text-sm 
                                        focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">To</label>
                            <input type="date" wire:model="filterCreatedTo" :min="fromDate" max="{{ date('Y-m-d') }}"
                                class="py-2 px-3 block w-full border-gray-300 shadow-sm rounded-lg text-sm 
                                        focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

            </x-slot>
        </x-modals.filter-modal>
    </div>
</div>