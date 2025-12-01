<x-layout>
    <x-navbar module="Incoming Trucks">
        <x-slot:sidebar>
            @livewire('sidebar-user', ['currentRoute' => request()->route()->getName()])
        </x-slot:sidebar>
    </x-navbar>

    @livewire('truck-list', ['type' => 'incoming'])
</x-layout>
