@props(['data', 'filters', 'sorting'])

<x-prints.layout title="Locations List">
    <x-slot name="filters">
        <x-prints.filters :filters="$filters ?? []" :sorting="$sorting ?? []" />
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Location Name</th>
                <th>Status</th>
                <th>Can Create Slip</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $location)
                <tr>
                    <td>{{ $location->location_name ?? ($location['location_name'] ?? '') }}</td>
                    <td>{{ $location->disabled ?? ($location['disabled'] ?? false) ? 'Disabled' : 'Enabled' }}</td>
                    <td>{{ $location->create_slip ?? ($location['create_slip'] ?? false) ? 'Yes' : 'No' }}</td>
                    <td>
                        @if (isset($location->created_at))
                            {{ \Carbon\Carbon::parse($location->created_at)->format('M d, Y h:i A') }}
                        @elseif(isset($location['created_at']))
                            {{ \Carbon\Carbon::parse($location['created_at'])->format('M d, Y h:i A') }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No locations found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <x-slot name="footer">
        <p>Total Records: {{ count($data) }}</p>
    </x-slot>
</x-prints.layout>
