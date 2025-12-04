<div class="p-4 sm:p-6 lg:p-8 bg-linear-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Disinfected Trucks Statistics -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Disinfected Vehicles
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Week to Date -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-blue-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-blue-100 rounded-xl group-hover:bg-blue-200 transition-colors">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Week</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">to Date</p>
                                </div>
                            </div>
                            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full font-medium">This
                                Week</span>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['week_disinfected']) }}</p>
                            <span
                                class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-blue-400 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

                <!-- Month to Date -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-purple-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-purple-100 rounded-xl group-hover:bg-purple-200 transition-colors">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Month</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">to Date</p>
                                </div>
                            </div>
                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-1 rounded-full font-medium">This
                                Month</span>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['month_disinfected']) }}</p>
                            <span
                                class="text-xs text-purple-600 bg-purple-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-purple-400 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

                <!-- Year to Date -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-green-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-green-100 rounded-xl group-hover:bg-green-200 transition-colors">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Year</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ date('Y') }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full font-medium">This
                                Year</span>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['year_disinfected']) }}</p>
                            <span
                                class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-green-400 to-green-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

                <!-- All Time Total -->
                <div
                    class="group relative overflow-hidden bg-linear-to-br from-yellow-500 to-orange-600 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-orange-400 hover:scale-105">
                    <div class="p-6 h-full flex flex-col justify-between">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="p-3 bg-white/20 backdrop-blur-sm rounded-xl group-hover:bg-white/30 transition-colors">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span
                                class="text-xs text-white bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full font-medium">All-Time</span>
                        </div>
                        <div>
                            <p class="text-4xl font-bold text-white mb-1">
                                {{ number_format($this->stats['total_disinfected']) }}</p>
                            <p class="text-sm text-yellow-100">Total Disinfected</p>
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 h-20 w-20 bg-white/10 rounded-full blur-xl"></div>
                </div>

            </div>
        </div>

        <!-- System Statistics -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                System Resources
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Guards Count -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-cyan-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-cyan-100 rounded-xl group-hover:bg-cyan-200 transition-colors">
                                    <svg class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Guards</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">System Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['total_guards']) }}</p>
                            <span
                                class="text-xs text-cyan-600 bg-cyan-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-cyan-400 to-cyan-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

                <!-- Drivers Count -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-pink-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-pink-100 rounded-xl group-hover:bg-pink-200 transition-colors">
                                    <svg class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Drivers</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Registered</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['total_drivers']) }}</p>
                            <span
                                class="text-xs text-pink-600 bg-pink-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-pink-400 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

                <!-- Plate Numbers Count -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-amber-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-amber-100 rounded-xl group-hover:bg-amber-200 transition-colors">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Trucks</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Unique Plates</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['total_plate_numbers']) }}</p>
                            <span
                                class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-amber-400 to-amber-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

                <!-- Locations Count -->
                <div
                    class="group relative overflow-hidden bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-teal-400">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-teal-100 rounded-xl group-hover:bg-teal-200 transition-colors">
                                    <svg class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Locations
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Active Sites</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between">
                            <p class="text-4xl font-bold text-gray-800">
                                {{ number_format($this->stats['total_locations']) }}</p>
                            <span
                                class="text-xs text-teal-600 bg-teal-50 px-2 py-1 rounded-full font-medium">Active</span>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-teal-400 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                    </div>
                </div>

            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#"
                    class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all group">
                    <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-blue-700">Manage Guards</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all group">
                    <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-purple-700">View Reports</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-all group">
                    <div class="p-2 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-green-700">Manage Locations</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-all group">
                    <div class="p-2 bg-amber-100 rounded-lg group-hover:bg-amber-200 transition-colors">
                        <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-amber-700">All Slips</span>
                </a>
            </div>
        </div>

    </div>
</div>
