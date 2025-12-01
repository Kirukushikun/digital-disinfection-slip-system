<x-layout>
    <x-navbar module="Change Password">
        <x-slot:sidebar>
            @switch(auth()->user()->user_type)
                @case(0)
                    @livewire('sidebar-user', ['currentRoute' => request()->route()->getName()])
                    @break
                @case(1)
                    @livewire('sidebar-admin')
                    @break
                @case(2)
                    @livewire('sidebar-super-admin')
                    @break
            @endswitch
        </x-slot:sidebar>
    </x-navbar>

    <div class="p-4 bg-white min-h-screen flex flex-col items-center">
        @livewire('change-password')
    </div>
</x-layout>

