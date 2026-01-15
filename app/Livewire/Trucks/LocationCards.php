<?php

namespace App\Livewire\Trucks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Location;
use App\Models\DisinfectionSlip;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LocationCards extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'tailwind';
    
    public $search = '';
    
    protected $listeners = ['refreshLocationCards' => '$refresh'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Cache locations list (without counts) since locations are relatively static
        // Bypass cache when searching to ensure accurate results
        if (empty($this->search)) {
            // Get all active locations from cache (5 minutes TTL)
            $allLocations = Cache::remember('locations_all', 300, function () {
                return Location::where('disabled', '=', false, 'and')
                    ->with('attachment')
                    ->orderBy('location_name', 'asc')
                    ->get();
            });
            
            // Manually paginate the cached collection
            $currentPage = request()->get('page', 1);
            $perPage = 9;
            $offset = ($currentPage - 1) * $perPage;
            $items = $allLocations->slice($offset, $perPage)->values();
            
            // Create a LengthAwarePaginator manually
            $locations = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $allLocations->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            // Bypass cache when searching - always fetch fresh results
            $locationsQuery = Location::where('disabled', '=', false, 'and')
                ->with('attachment')
                ->where('location_name', 'like', '%' . $this->search . '%', 'and')
                ->orderBy('location_name', 'asc');
            
            // Paginate the locations - 9 items per page for both mobile and desktop
            $locations = $locationsQuery->paginate(9)->withQueryString();
        }
    
        // Get all in-transit slip counts in a single query for better performance
        // Only query if there are locations to avoid empty whereIn
        // Status 2 (In-Transit) - incoming slips ready for completion
        $inTransitCounts = collect();
        if ($locations->isNotEmpty()) {
            $locationIds = $locations->pluck('id')->toArray();
            if (!empty($locationIds)) {
                $inTransitCounts = DisinfectionSlip::whereIn('destination_id', $locationIds, 'and', false)
                    ->whereDate('created_at', today())
                    ->where('status', '=', 2, 'and') // In-Transit - incoming slips
                    ->selectRaw('destination_id, COUNT(*) as count')
                    ->groupBy('destination_id')
                    ->pluck('count', 'destination_id');
            }
        }
    
        // Map counts to locations - modify items in place to preserve paginator
        $locations->getCollection()->transform(function ($location) use ($inTransitCounts) {
            $location->in_transit_count = $inTransitCounts->get($location->id, 0);
            return $location;
        });

        // Get default logo path from settings
        $setting = Setting::where('setting_name', '=', 'default_location_logo', 'and')->first();
        $defaultLogoPath = $setting && !empty($setting->value) ? $setting->value : 'images/logo/BGC.png';
    
        return view('livewire.trucks.location-cards', [
            'locations' => $locations,
            'defaultLogoPath' => $defaultLogoPath,
        ]);
    }    
}