<x-layout>
    <x-navbar module="Incoming Trucks">
        <x-slot:sidebar>
            <livewire:sidebar-user :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navbar>

    <livewire:truck-list type="incoming" />
</x-layout>
