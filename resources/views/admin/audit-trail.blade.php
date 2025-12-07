<x-layout>
    <x-navigation.navbar module="Audit Trail">
        <x-slot:sidebar>
            <livewire:sidebar.sidebar-admin :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navigation.navbar>

    <livewire:admin.audit-trail />
</x-layout>
