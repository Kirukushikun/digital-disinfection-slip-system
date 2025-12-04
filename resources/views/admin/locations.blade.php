<x-layout>
    <x-navigation.navbar module="Locations">
        <x-slot:sidebar>
            <livewire:sidebar.sidebar-admin :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navigation.navbar>

</x-layout>
