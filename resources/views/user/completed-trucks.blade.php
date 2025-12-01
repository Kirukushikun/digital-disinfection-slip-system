<x-layout>
    <x-navbar module="Completed Trucks">
        <x-slot:sidebar>
            @livewire('sidebar-user', ['currentRoute' => request()->route()->getName()])
        </x-slot:sidebar>
    </x-navbar>

    @livewire('truck-list', ['type' => 'completed'])
</x-layout>
