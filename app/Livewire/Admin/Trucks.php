<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\DisinfectionSlip;
use App\Models\Location;
use Livewire\WithPagination;

class Trucks extends Component
{
    use WithPagination;

    public $search = '';
    public $showFilters = false;
    
    // Filter fields
    public $filterStatus = '';
    public $filterOrigin = '';
    public $filterDestination = '';
    public $filterDriver = '';
    public $filterCreatedFrom = '';
    public $filterCreatedTo = '';
    
    // Applied filters (stored separately)
    public $appliedStatus = '';
    public $appliedOrigin = '';
    public $appliedDestination = '';
    public $appliedDriver = '';
    public $appliedCreatedFrom = null;
    public $appliedCreatedTo = null;
    
    public $filtersActive = false;
    
    public $availableStatuses = [
        0 => 'Ongoing',
        1 => 'Disinfected',
        2 => 'Completed',
    ];

    public function mount()
    {
        // Don't initialize date filters - let them be null by default
    }

    // Computed property for locations
    public function getLocationsProperty()
    {
        return Location::orderBy('location_name')->get();
    }

    // Computed property for drivers
    public function getDriversProperty()
    {
        return \App\Models\Driver::orderBy('first_name')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->appliedStatus = $this->filterStatus;
        $this->appliedOrigin = $this->filterOrigin;
        $this->appliedDestination = $this->filterDestination;
        $this->appliedDriver = $this->filterDriver;
        $this->appliedCreatedFrom = $this->filterCreatedFrom;
        $this->appliedCreatedTo = $this->filterCreatedTo;
        
        $this->updateFiltersActive();
        
        $this->showFilters = false;
        $this->resetPage();
    }

    public function removeFilter($filterName)
    {
        // Clear both the applied and filter values
        switch($filterName) {
            case 'status':
                $this->appliedStatus = '';
                $this->filterStatus = '';
                break;
            case 'origin':
                $this->appliedOrigin = '';
                $this->filterOrigin = '';
                break;
            case 'destination':
                $this->appliedDestination = '';
                $this->filterDestination = '';
                break;
            case 'driver':
                $this->appliedDriver = '';
                $this->filterDriver = '';
                break;
            case 'createdFrom':
                $this->appliedCreatedFrom = null;
                $this->filterCreatedFrom = null;
                break;
            case 'createdTo':
                $this->appliedCreatedTo = null;
                $this->filterCreatedTo = null;
                break;
        }
        
        $this->updateFiltersActive();
        $this->resetPage();
    }

    public function updateFiltersActive()
    {
        // Check if any filters are actually applied
        $this->filtersActive = 
            $this->appliedStatus !== '' ||
            $this->appliedOrigin ||
            $this->appliedDestination ||
            $this->appliedDriver ||
            $this->appliedCreatedFrom ||
            $this->appliedCreatedTo;
    }

    public function cancelFilters()
    {
        $this->showFilters = false;
    }

    public function clearFilters()
    {
        $this->filterStatus = '';
        $this->filterOrigin = '';
        $this->filterDestination = '';
        $this->filterDriver = '';
        $this->filterCreatedFrom = null;
        $this->filterCreatedTo = null;
        
        $this->appliedStatus = '';
        $this->appliedOrigin = '';
        $this->appliedDestination = '';
        $this->appliedDriver = '';
        $this->appliedCreatedFrom = null;
        $this->appliedCreatedTo = null;
        
        $this->filtersActive = false;
        $this->resetPage();
    }

    public function render()
    {
        $slips = DisinfectionSlip::with(['truck', 'location', 'destination', 'driver'])
            // Search
            ->when($this->search, function($query) {
                $query->where('slip_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('truck', function($q) {
                        $q->where('plate_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('driver', function($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                          ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('location', function($q) {
                        $q->where('location_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('destination', function($q) {
                        $q->where('location_name', 'like', '%' . $this->search . '%');
                    });
            })
            // Status filter
            ->when($this->filtersActive && $this->appliedStatus !== '', function($query) {
                $query->where('status', $this->appliedStatus);
            })
            // Origin filter
            ->when($this->filtersActive && $this->appliedOrigin, function($query) {
                $query->where('location_id', $this->appliedOrigin);
            })
            // Destination filter
            ->when($this->filtersActive && $this->appliedDestination, function($query) {
                $query->where('destination_id', $this->appliedDestination);
            })
            // Driver filter
            ->when($this->filtersActive && $this->appliedDriver, function($query) {
                $query->where('driver_id', $this->appliedDriver);
            })
            // Created date range filter
            ->when($this->filtersActive && $this->appliedCreatedFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->appliedCreatedFrom);
            })
            ->when($this->filtersActive && $this->appliedCreatedTo, function($query) {
                $query->whereDate('created_at', '<=', $this->appliedCreatedTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.trucks', [
            'slips' => $slips,
            'locations' => $this->locations,
            'drivers' => $this->drivers,
            'availableStatuses' => $this->availableStatuses,
        ]);
    }
}