<x-layout>
    <x-navbar module="Outgoing Trucks">
        <x-slot:sidebar>
            <livewire:sidebar-user :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navbar>

    <livewire:truck-list type="outgoing" />
</x-layout>
