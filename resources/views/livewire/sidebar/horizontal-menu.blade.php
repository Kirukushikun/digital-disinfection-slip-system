@props([
    'currentRoute', // passed from Livewire mount
])

@php
    $user = auth()->user();
    
    // Determine user type and menu configuration
    $isSuperGuard = ($user->user_type === 0 && $user->super_guard) || ($user->user_type === 2 && $user->isGuardView());
    
    if ($isSuperGuard) {
        $menuType = 'superguard';
        $routePrefix = 'user';
        $dashboardRoute = 'user.dashboard';
        $dashboardLabel = 'Home';
        $dataManagementLabel = 'Data';
        $dataManagementRoutes = ['user.data.guards', 'user.data.drivers', 'user.data.locations', 'user.data.vehicles'];
        $showAdmins = false;
        $showSettings = false;
        $showAuditTrail = false;
        $tripsAsDropdown = true;
        $tripsRoutes = ['user.incoming-slips', 'user.outgoing-slips', 'user.completed-slips'];
        $tripsDropdownItems = [
            ['route' => 'user.incoming-slips', 'label' => 'Incoming', 'icon' => '<img src="https://cdn-icons-png.flaticon.com/512/8591/8591505.png" alt="Incoming" class="w-5 h-5 object-contain" />'],
            ['route' => 'user.outgoing-slips', 'label' => 'Outgoing', 'icon' => '<img src="https://cdn-icons-png.flaticon.com/512/7468/7468319.png" alt="Outgoing" class="w-5 h-5 object-contain" />'],
            ['route' => 'user.completed-slips', 'label' => 'Completed', 'icon' => '<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
        ];
    } else {
        $effectiveUserType = $user->effectiveUserType();
        
        if ($effectiveUserType === 0) {
            // Regular User/Guard
            $menuType = 'user';
            $routePrefix = 'user';
            $dashboardRoute = 'user.dashboard';
            $dashboardLabel = 'Dashboard';
            $dataManagementLabel = null; // No data management for regular users
            $dataManagementRoutes = [];
            $showAdmins = false;
            $showSettings = false;
            $showAuditTrail = false;
            $tripsAsDropdown = true;
            $tripsRoutes = ['user.incoming-slips', 'user.outgoing-slips', 'user.completed-slips'];
            $tripsDropdownItems = [
                ['route' => 'user.incoming-slips', 'label' => 'Incoming Slips', 'icon' => '<img src="https://cdn-icons-png.flaticon.com/512/8591/8591505.png" alt="Incoming" class="w-5 h-5 object-contain" />'],
                ['route' => 'user.outgoing-slips', 'label' => 'Outgoing Slips', 'icon' => '<img src="https://cdn-icons-png.flaticon.com/512/7468/7468319.png" alt="Outgoing" class="w-5 h-5 object-contain" />'],
                ['route' => 'user.completed-slips', 'label' => 'Completed Slips', 'icon' => '<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
            ];
        } elseif ($effectiveUserType === 1) {
            // Admin
            $menuType = 'admin';
            $routePrefix = 'admin';
            $dashboardRoute = 'admin.dashboard';
            $dashboardLabel = 'Dashboard';
            $dataManagementLabel = 'Data Management';
            $dataManagementRoutes = ['admin.guards', 'admin.drivers', 'admin.vehicles', 'admin.locations'];
            $showAdmins = false;
            $showSettings = false;
            $showAuditTrail = true;
            $tripsAsDropdown = false;
            $tripsRoutes = ['admin.slips'];
            $tripsDropdownItems = [];
        } else {
            // SuperAdmin
            $menuType = 'superadmin';
            $routePrefix = 'superadmin';
            $dashboardRoute = 'superadmin.dashboard';
            $dashboardLabel = 'Dashboard';
            $dataManagementLabel = 'Data Management';
            $dataManagementRoutes = ['superadmin.guards', 'superadmin.admins', 'superadmin.drivers', 'superadmin.vehicles', 'superadmin.locations'];
            $showAdmins = true;
            $showSettings = true;
            $showAuditTrail = true;
            $tripsAsDropdown = false;
            $tripsRoutes = ['superadmin.slips'];
            $tripsDropdownItems = [];
        }
    }
    
    $isDataManagementActive = !empty($dataManagementRoutes) && in_array($currentRoute, $dataManagementRoutes);
    $isTripsActive = in_array($currentRoute, $tripsRoutes);
@endphp

<div class="flex items-center gap-2">
    {{-- Dashboard --}}
    <x-navigation.horizontal-menu-item href="{{ route($dashboardRoute) }}" :active="$currentRoute === $dashboardRoute"
        icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>'>
        {{ $dashboardLabel }}
    </x-navigation.horizontal-menu-item>

    {{-- Data Management Dropdown (if applicable) --}}
    @if (!empty($dataManagementRoutes))
        <x-navigation.horizontal-menu-dropdown label="{{ $dataManagementLabel }}" :active="$isDataManagementActive"
            icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>'>
            @if ($menuType === 'superguard' || $menuType === 'user')
                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.data.guards') }}" :active="$currentRoute === $routePrefix . '.data.guards'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>'
                    :indent="true">
                    Guards
                </x-navigation.sidebar-menu-item>

                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.data.drivers') }}" :active="$currentRoute === $routePrefix . '.data.drivers'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>'
                    :indent="true">
                    Drivers
                </x-navigation.sidebar-menu-item>
            @else
                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.guards') }}" :active="$currentRoute === $routePrefix . '.guards'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>'
                    :indent="true">
                    Guards
                </x-navigation.sidebar-menu-item>

                @if ($showAdmins)
                    <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.admins') }}" :active="$currentRoute === $routePrefix . '.admins'"
                        icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>'
                        :indent="true">
                        Admins
                    </x-navigation.sidebar-menu-item>
                @endif

                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.drivers') }}" :active="$currentRoute === $routePrefix . '.drivers'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>'
                    :indent="true">
                    Drivers
                </x-navigation.sidebar-menu-item>
            @endif

            @if ($menuType === 'superguard' || $menuType === 'user')
                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.data.vehicles') }}" :active="$currentRoute === $routePrefix . '.data.vehicles'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>'
                    :indent="true">
                    Vehicles
                </x-navigation.sidebar-menu-item>
            @else
                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.vehicles') }}" :active="$currentRoute === $routePrefix . '.vehicles'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>'
                    :indent="true">
                    Vehicles
                </x-navigation.sidebar-menu-item>
            @endif

            @if ($menuType === 'superguard' || $menuType === 'user')
                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.data.locations') }}" :active="$currentRoute === $routePrefix . '.data.locations'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>'
                    :indent="true">
                    Locations
                </x-navigation.sidebar-menu-item>
            @else
                <x-navigation.sidebar-menu-item href="{{ route($routePrefix . '.locations') }}" :active="$currentRoute === $routePrefix . '.locations'"
                    icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>'
                    :indent="true">
                    Locations
                </x-navigation.sidebar-menu-item>
            @endif
        </x-navigation.horizontal-menu-dropdown>
    @endif

    {{-- Trips --}}
    @if ($tripsAsDropdown)
        <x-navigation.horizontal-menu-dropdown label="Trips" :active="$isTripsActive"
            icon='<img src="https://cdn-icons-png.flaticon.com/512/605/605863.png" alt="Trips" class="w-5 h-5 object-contain" />'>
            @foreach ($tripsDropdownItems as $item)
                <x-navigation.sidebar-menu-item href="{{ route($item['route']) }}" :active="$currentRoute === $item['route']"
                    icon="{!! $item['icon'] !!}"
                    :indent="true">
                    {{ $item['label'] }}
                </x-navigation.sidebar-menu-item>
            @endforeach
        </x-navigation.horizontal-menu-dropdown>
    @else
        <x-navigation.horizontal-menu-item href="{{ route($routePrefix . '.slips') }}" :active="$currentRoute === $routePrefix . '.slips'"
            icon='<img src="https://cdn-icons-png.flaticon.com/512/605/605863.png" alt="Trips" class="w-5 h-5 object-contain" />'>
            Trips
        </x-navigation.horizontal-menu-item>
    @endif

    {{-- Issues --}}
    <x-navigation.horizontal-menu-item href="{{ route($routePrefix . '.issues') }}" :active="$currentRoute === $routePrefix . '.issues'"
        icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
            </svg>'>
        Issues
    </x-navigation.horizontal-menu-item>

    {{-- Audit Trail (Admin and SuperAdmin only) --}}
    @if ($showAuditTrail)
        <x-navigation.horizontal-menu-item href="{{ route($routePrefix . '.audit-trail') }}" :active="$currentRoute === $routePrefix . '.audit-trail'"
            icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>'>
            Audit Trail
        </x-navigation.horizontal-menu-item>
    @endif

    {{-- Settings (SuperAdmin only) --}}
    @if ($showSettings)
        <x-navigation.horizontal-menu-item href="{{ route($routePrefix . '.settings') }}" :active="$currentRoute === $routePrefix . '.settings'"
            icon='<svg xmlns="https://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>'>
            Settings
        </x-navigation.horizontal-menu-item>
    @endif
</div>
