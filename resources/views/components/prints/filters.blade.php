@props(['filters', 'sorting'])

@if (!empty($filters) || !empty($sorting))
    <div style="margin-top: 15px; text-align: left; font-size: 11px; border-top: 1px solid #ddd; padding-top: 10px;">
        @if (!empty($filters['search']))
            <p><strong>Search:</strong> {{ $filters['search'] }}</p>
        @endif

        @if (isset($filters['status']) && $filters['status'] !== null)
            @if (is_array($filters['status']))
                @php
                    $statusLabels = [];
                    $statuses = ['Pending', 'Disinfecting', 'In-Transit', 'Completed', 'Incomplete'];
                    foreach ($filters['status'] as $statusId) {
                        if (isset($statuses[$statusId])) {
                            $statusLabels[] = $statuses[$statusId];
                        }
                    }
                @endphp
                @if (!empty($statusLabels))
                    <p><strong>Status:</strong> {{ implode(', ', $statusLabels) }}</p>
                @endif
            @else
                <p><strong>Status:</strong> {{ $filters['status'] == 0 ? 'Enabled' : 'Disabled' }}</p>
            @endif
        @endif

        @if (isset($filters['create_slip']) && $filters['create_slip'] !== null)
            <p><strong>Create Slip Permission:</strong> {{ $filters['create_slip'] ? 'Can Create Slip' : 'Cannot Create Slip' }}</p>
        @endif

        @if (isset($filters['guard_type']) && $filters['guard_type'] !== null)
            <p><strong>Guard Type:</strong> {{ $filters['guard_type'] == 0 ? 'Regular Guards' : 'Super Guards' }}</p>
        @endif


        @if (!empty($filters['origin']) && is_array($filters['origin']))
            @php
                $originNames = \App\Models\Location::whereIn('id', $filters['origin'])->pluck('location_name')->toArray();
            @endphp
            <p><strong>Origin:</strong> {{ implode(', ', $originNames) }}</p>
        @endif

        @if (!empty($filters['destination']) && is_array($filters['destination']))
            @php
                $destinationNames = \App\Models\Location::whereIn('id', $filters['destination'])->pluck('location_name')->toArray();
            @endphp
            <p><strong>Destination:</strong> {{ implode(', ', $destinationNames) }}</p>
        @endif

        @if (!empty($filters['driver']) && is_array($filters['driver']))
            @php
                $driverNames = \App\Models\Driver::whereIn('id', $filters['driver'])->get()->map(function($d) {
                    return trim(implode(' ', array_filter([$d->first_name, $d->middle_name, $d->last_name])));
                })->toArray();
            @endphp
            <p><strong>Driver:</strong> {{ implode(', ', $driverNames) }}</p>
        @endif

        @if (!empty($filters['vehicle']) && is_array($filters['vehicle']))
            @php
                $vehicles = \App\Models\Vehicle::whereIn('id', $filters['vehicle'])->pluck('vehicle')->toArray();
            @endphp
            <p><strong>Vehicle:</strong> {{ implode(', ', $vehicles) }}</p>
        @endif

        @if (!empty($filters['hatchery_guard']) && is_array($filters['hatchery_guard']))
            @php
                $guardNames = \App\Models\User::whereIn('id', $filters['hatchery_guard'])->get()->map(function($g) {
                    return trim(implode(' ', array_filter([$g->first_name, $g->middle_name, $g->last_name])));
                })->toArray();
            @endphp
            <p><strong>Hatchery Guard:</strong> {{ implode(', ', $guardNames) }}</p>
        @endif

        @if (!empty($filters['received_guard']) && is_array($filters['received_guard']))
            @php
                $guardNames = \App\Models\User::whereIn('id', $filters['received_guard'])->get()->map(function($g) {
                    return trim(implode(' ', array_filter([$g->first_name, $g->middle_name, $g->last_name])));
                })->toArray();
            @endphp
            <p><strong>Received Guard:</strong> {{ implode(', ', $guardNames) }}</p>
        @endif

        @if (!empty($filters['created_from']))
            <p><strong>Created From:</strong> {{ \Carbon\Carbon::parse($filters['created_from'])->format('M d, Y') }}</p>
        @endif

        @if (!empty($filters['created_to']))
            <p><strong>Created To:</strong> {{ \Carbon\Carbon::parse($filters['created_to'])->format('M d, Y') }}</p>
        @endif

        @if (!empty($sorting))
            <p><strong>Sorted By:</strong>
                @if (isset($sorting['sort_by']))
                    {{ ucfirst(str_replace('_', ' ', $sorting['sort_by'])) }} ({{ strtoupper($sorting['sort_direction'] ?? 'asc') }})
                @else
                    @foreach ($sorting as $column => $direction)
                        {{ ucfirst(str_replace('_', ' ', $column)) }} ({{ strtoupper($direction) }})@if (!$loop->last), @endif
                    @endforeach
                @endif
            </p>
        @endif
    </div>
@endif
