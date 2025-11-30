<x-layout>
    <x-navbar module="Dashboard">
        <x-slot:sidebar>
            @livewire('sidebar-user', ['currentRoute' => request()->route()->getName()])
        </x-slot:sidebar>
    </x-navbar>

    <div class="p-4 bg-white min-h-screen flex flex-col items-center">

        <!-- Card container -->
        <div class="w-full max-w-7xl rounded-xl shadow-md p-4 bg-white">

            <!-- Logo centered -->
            {{-- <div class="flex justify-center mb-8">
                <img src="{{ asset('images/brookdale-farms-logo.png') }}" alt="Brookdale Farms" class="h-24 object-contain">
            </div> --}}

            <!-- Cards container: stacked on mobile, horizontal on md+ -->
            <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">

                <!-- Incoming Trucks -->
                <a href="#" class="flex-1 flex flex-wrap items-center border border-orange-300 rounded-lg p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center space-x-3 min-w-[150px] text-gray-700 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l3 3 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5" />
                        </svg>
                        <span class="font-semibold text-lg whitespace-nowrap">Incoming Trucks Today</span>
                    </div>
                    <span class="text-red-600 font-bold text-2xl ml-auto min-w-[50px] mt-2 md:mt-0">0000</span>
                </a>

                <!-- Outgoing Trucks -->
                <a href="#" class="flex-1 flex flex-wrap items-center border border-green-300 rounded-lg p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center space-x-3 min-w-[150px] text-gray-700 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h3" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l3 3 4-4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5" />
                        </svg>
                        <span class="font-semibold text-lg whitespace-nowrap">Outgoing Trucks Today</span>
                    </div>
                    <span class="text-green-600 font-bold text-2xl ml-auto min-w-[50px] mt-2 md:mt-0">0000</span>
                </a>

                <!-- Create Disinfection Slip -->
                <a href="#" class="flex-1 flex items-center space-x-4 border border-orange-300 rounded-lg p-6 shadow-sm hover:shadow-md transition text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="font-semibold text-lg">Create Disinfection Slip</span>
                </a>
            </div>
        </div>
    </div>
</x-layout>
