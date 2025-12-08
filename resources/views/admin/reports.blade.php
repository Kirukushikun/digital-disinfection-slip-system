<x-layout>
    <x-navigation.navbar module="Reports">
        <x-slot:sidebar>
            <livewire:sidebar.sidebar-admin :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navigation.navbar>

    <livewire:admin.reports />
</x-layout>
