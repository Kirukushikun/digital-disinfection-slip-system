<x-layout>
    <x-navigation.navbar module="Audit Trail">
        <x-slot:sidebar>
            <livewire:sidebar.sidebar-superadmin :currentRoute="Route::currentRouteName()" />
        </x-slot:sidebar>
    </x-navigation.navbar>

    <livewire:super-admin.audit-trail />
</x-layout>
