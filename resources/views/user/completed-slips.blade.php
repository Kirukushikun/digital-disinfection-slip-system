<x-layout>
    <x-navigation.navbar module="Completed Slips" />

    <!-- Slip Arrival Monitor -->
    <livewire:slip-arrival-monitor />

    <livewire:slips.slip-list type="incoming" :view-mode="'completed'" />
</x-layout>
