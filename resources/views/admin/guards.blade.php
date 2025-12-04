<x-layout>
    <x-navigation.navbar module="Guards">
        <x-slot:sidebar>
            <livewire:sidebar.sidebar-admin :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navigation.navbar>

</x-layout>
