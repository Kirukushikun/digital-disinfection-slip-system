<?php

namespace App\Livewire\Slips;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Renderless;
use App\Models\DisinfectionSlip;
use App\Models\Vehicle;
use App\Models\Location;
use App\Models\Driver;
use App\Models\Reason;
use App\Services\Logger;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
/**
 * @method void resetPage()
 * @method void dispatch(string $event, mixed ...$params)
 * @method void resetErrorBag()
 * @method array validate(array $rules)
 * NOTE: vehicles, locations, drivers properties removed - using paginated methods instead
 */
class SlipList extends Component
{
    use WithPagination;

    public $type = 'incoming'; // incoming or outgoing
    public $viewMode = 'active'; // 'active' or 'completed'
    protected $paginationTheme = 'tailwind';
    
    public $search = '';
    public $showFilters = false;
        
    // Active mode filters
    public $filterDateFrom;
    public $filterDateTo;
    public $filterStatus = '';
    
    public $appliedDateFrom = null;
    public $appliedDateTo = null;
    public $appliedStatus = '';
    
    // Completed mode filters
    public $filterDestination = [];
    public $filterDriver = [];
    public $filterVehicle = [];
    public $filterCompletedFrom = '';
    public $filterCompletedTo = '';
    public $filterStatusCompleted = 'all'; // 'all', 'completed', 'incomplete'
    
    public $appliedDestination = [];
    public $appliedDriver = [];
    public $appliedVehicle = [];
    public $appliedCompletedFrom = null;
    public $appliedCompletedTo = null;
    public $appliedStatusCompleted = 'all';
    
    // Search properties for completed mode filter dropdowns
    public $searchFilterVehicle = '';
    public $searchFilterDriver = '';
    public $searchFilterDestination = '';
    
    public $filtersActive = false;
    public $sortDirection = null; // null, 'asc', 'desc' (applied)
    public $filterSortDirection = null; // null, 'asc', 'desc' (temporary, in filter modal)
    
    public $availableStatuses = [
        0 => 'Pending',
        1 => 'Disinfecting',
        2 => 'In-Transit',
    ];

    // Create functionality moved to Shared/Slips/Create component

    // Reason management properties (for super guards)
    public $showReasonsModal = false;
    public $showCreateReasonModal = false;
    public $newReasonText = '';
    public $editingReasonId = null;
    public $editingReasonText = '';
    public $originalReasonText = '';
    public $showSaveConfirmation = false;
    public $showUnsavedChangesConfirmation = false;
    public $savingReason = false;
    public $showDeleteReasonConfirmation = false;
    public $reasonToDelete = null;
    public $searchReasonSettings = '';
    public $filterReasonStatus = 'all'; // Filter: 'all', 'enabled', 'disabled'
    public $reasonsPage = 1; // Page for reasons pagination

    
    public function updatedSearchReasonSettings()
    {
        $this->reasonsPage = 1; // Reset to first page when search changes
    }

    public function updatedFilterReasonStatus()
    {
        $this->reasonsPage = 1; // Reset to first page when filter changes
    }

    public function mount($type = 'incoming', $viewMode = 'active')
    {
        $this->type = $type;
        $this->viewMode = $viewMode; // 'active' or 'completed'
        
        // Initialize filters based on current mode
        if ($this->viewMode === 'completed') {
            // Completed mode: Initialize completed filters
            $this->filterDestination = $this->appliedDestination ?? [];
            $this->filterDriver = $this->appliedDriver ?? [];
            $this->filterVehicle = $this->appliedVehicle ?? [];
            $this->filterCompletedFrom = $this->appliedCompletedFrom ?? '';
            $this->filterCompletedTo = $this->appliedCompletedTo ?? '';
            $this->filterStatusCompleted = $this->appliedStatusCompleted ?? 'all';
        } else {
            // Active mode: Initialize active filters
            // Outgoing: set default filter values to today (for UI), but don't apply them automatically
            // Incoming: no default date filter
            if ($this->type === 'outgoing') {
                $today = now()->format('Y-m-d');
                $this->filterDateFrom = $today;
                $this->filterDateTo = $today;
                $this->appliedDateFrom = null;
                $this->appliedDateTo = null;
            } else {
                $this->filterDateFrom = null;
                $this->filterDateTo = null;
            }
        }
        
        $this->filterSortDirection = $this->sortDirection; // Initialize filter sort with current sort
        $this->checkFiltersActive();

        // Check if we should open create modal from route parameter (only if location allows)
        if (request()->has('openCreate') && $this->type === 'outgoing' && $this->canCreateSlip) {
            $this->dispatch('openCreateModal');
        }
    }
    
    public function isSuperGuard()
    {
        $user = Auth::user();
        // Allow super guards (user_type 0 with super_guard = true) OR super admins (user_type 2)
        // This matches the EnsureSuperGuard middleware logic
        return $user && ($user->super_guard || $user->user_type === 2);
    }

    // NOTE: Computed properties removed - now using paginated dropdowns via getPaginatedVehicles, getPaginatedLocations, getPaginatedDrivers
    // These methods are called on-demand by the searchable-dropdown-paginated component

    public function getCanCreateSlipProperty()
    {
        if ($this->type !== 'outgoing') {
            return false;
        }
        
        $currentLocationId = Session::get('location_id');
        if (!$currentLocationId) {
            return false;
        }
        
        $location = Location::find($currentLocationId, ['id', 'create_slip']);
        return $location && ($location->create_slip ?? false);
    }
    
    // Helper method to ensure selected values are always included in filtered options
    private function ensureSelectedInOptions($options, $selectedValue, $allOptions)
    {
        if (empty($selectedValue)) {
            return $options;
        }
        
        $allOptionsArray = is_array($allOptions) ? $allOptions : $allOptions->toArray();
        $optionsArray = is_array($options) ? $options : $options->toArray();
        
        // Add selected value if it's not already in the filtered options
        if (isset($allOptionsArray[$selectedValue]) && !isset($optionsArray[$selectedValue])) {
            $optionsArray[$selectedValue] = $allOptionsArray[$selectedValue];
        }
        
        return $optionsArray;
    }
    
    // NOTE: Cached collection methods removed - now using paginated methods that only load data on-demand
    // This prevents loading all records at once, improving performance with large datasets
    
    // NOTE: Old computed properties removed - now using paginated dropdowns
    
    // Paginated data fetching methods for searchable dropdowns
    #[Renderless]
    public function getPaginatedTrucks($search = '', $page = 1, $perPage = 20, $includeIds = [])
    {
        // Alias for getPaginatedVehicles for compatibility
        return $this->getPaginatedVehicles($search, $page, $perPage, $includeIds);
    }

    #[Renderless]
    public function getPaginatedVehicles($search = '', $page = 1, $perPage = 20, $includeIds = [])
    {
        $query = Vehicle::query()
            ->whereNull('deleted_at')
            ->where('disabled', false)
            ->select(['id', 'vehicle']);

        if (!empty($search)) {
            $query->where('vehicle', 'like', '%' . $search . '%');
        }

        if (!empty($includeIds)) {
            $includedItems = Vehicle::whereIn('id', $includeIds)
                ->select(['id', 'vehicle'])
                ->orderBy('vehicle', 'asc')
                ->get()
                ->pluck('vehicle', 'id')
                ->toArray();
            return ['data' => $includedItems, 'has_more' => false, 'total' => count($includedItems)];
        }

        $query->orderBy('vehicle', 'asc');
        $offset = ($page - 1) * $perPage;
        $total = $query->count();
        $results = $query->skip($offset)->take($perPage)->get();
        
        return [
            'data' => $results->pluck('vehicle', 'id')->toArray(),
            'has_more' => ($offset + $perPage) < $total,
            'total' => $total,
        ];
    }

    #[Renderless]
    public function getPaginatedDrivers($search = '', $page = 1, $perPage = 20, $includeIds = [])
    {
        $query = Driver::query()
            ->whereNull('deleted_at')
            ->where('disabled', false)
            ->select(['id', 'first_name', 'middle_name', 'last_name']);

        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                  ->orWhere('middle_name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm);
            });
        }

        if (!empty($includeIds)) {
            $includedItems = Driver::whereIn('id', $includeIds)
                ->select(['id', 'first_name', 'middle_name', 'last_name'])
                ->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')
                ->get()
                ->mapWithKeys(fn($d) => [$d->id => trim("{$d->first_name} {$d->middle_name} {$d->last_name}")])
                ->toArray();
            return ['data' => $includedItems, 'has_more' => false, 'total' => count($includedItems)];
        }

        $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
        $offset = ($page - 1) * $perPage;
        $total = $query->count();
        $results = $query->skip($offset)->take($perPage)->get();
        
        return [
            'data' => $results->mapWithKeys(fn($d) => [$d->id => trim("{$d->first_name} {$d->middle_name} {$d->last_name}")])->toArray(),
            'has_more' => ($offset + $perPage) < $total,
            'total' => $total,
        ];
    }

    #[Renderless]
    public function getPaginatedLocations($search = '', $page = 1, $perPage = 20, $includeIds = [])
    {
        $currentLocationId = Session::get('location_id');
        $query = Location::query()
            ->where('id', '!=', $currentLocationId)
            ->whereNull('deleted_at')
            ->where('disabled', false)
            ->select(['id', 'location_name']);

        if (!empty($search)) {
            $query->where('location_name', 'like', '%' . $search . '%');
        }

        if (!empty($includeIds)) {
            $includedItems = Location::whereIn('id', $includeIds)
                ->select(['id', 'location_name'])
                ->orderBy('location_name', 'asc')
                ->get()
                ->pluck('location_name', 'id')
                ->toArray();
            return ['data' => $includedItems, 'has_more' => false, 'total' => count($includedItems)];
        }

        $query->orderBy('location_name', 'asc');
        $offset = ($page - 1) * $perPage;
        $total = $query->count();
        $results = $query->skip($offset)->take($perPage)->get();
        
        return [
            'data' => $results->pluck('location_name', 'id')->toArray(),
            'has_more' => ($offset + $perPage) < $total,
            'total' => $total,
        ];
    }

    #[Renderless]
    public function getPaginatedReasons($search = '', $page = 1, $perPage = 20, $includeIds = [])
    {
        $query = Reason::query()
            ->where('is_disabled', false)
            ->select(['id', 'reason_text']);

        if (!empty($search)) {
            $query->where('reason_text', 'like', '%' . $search . '%');
        }

        if (!empty($includeIds)) {
            $includedItems = Reason::whereIn('id', $includeIds)
                ->select(['id', 'reason_text'])
                ->orderBy('reason_text', 'asc')
                ->get()
                ->pluck('reason_text', 'id')
                ->toArray();
            return ['data' => $includedItems, 'has_more' => false, 'total' => count($includedItems)];
        }

        $query->orderBy('reason_text', 'asc');
        $offset = ($page - 1) * $perPage;
        $total = $query->count();
        $results = $query->skip($offset)->take($perPage)->get();
        
        return [
            'data' => $results->pluck('reason_text', 'id')->toArray(),
            'has_more' => ($offset + $perPage) < $total,
            'total' => $total,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        if ($this->viewMode === 'completed') {
            $this->appliedDestination = $this->filterDestination;
            $this->appliedDriver = $this->filterDriver;
            $this->appliedVehicle = $this->filterVehicle;
            $this->appliedCompletedFrom = $this->filterCompletedFrom;
            $this->appliedCompletedTo = $this->filterCompletedTo;
            $this->appliedStatusCompleted = $this->filterStatusCompleted ?: 'all';
            $this->sortDirection = $this->filterSortDirection;
        } else {
            $this->appliedDateFrom = $this->filterDateFrom;
            $this->appliedDateTo = $this->filterDateTo;
            $this->appliedStatus = $this->filterStatus;
            $this->sortDirection = $this->filterSortDirection;
        }
        $this->checkFiltersActive();
        $this->resetPage();
        $this->showFilters = false;
    }
    
    private function checkFiltersActive()
    {
        if ($this->viewMode === 'completed') {
            $this->filtersActive = !empty($this->appliedDestination) ||
                                  !empty($this->appliedDriver) ||
                                  !empty($this->appliedVehicle) ||
                                  !empty($this->appliedCompletedFrom) ||
                                  !empty($this->appliedCompletedTo) ||
                                  ($this->appliedStatusCompleted !== 'all' && $this->appliedStatusCompleted !== null) ||
                                  ($this->sortDirection !== null && $this->sortDirection !== 'desc');
        } else {
            // Only check actual filters, not sorts (sorts are separate from filters)
            $this->filtersActive = !empty($this->appliedDateFrom) ||
                                  !empty($this->appliedDateTo) ||
                                  $this->appliedStatus !== '';
        }
    }

    public function cancelFilters()
    {
        if ($this->filtersActive) {
            $this->filterDateFrom = $this->appliedDateFrom;
            $this->filterDateTo = $this->appliedDateTo;
            $this->filterStatus = $this->appliedStatus;
        } else {
            $this->filterDateFrom = null;
            $this->filterDateTo = null;
            $this->filterStatus = '';
        }
    }


    public function clearFilters()
    {
        if ($this->viewMode === 'completed') {
            $this->filterDestination = [];
            $this->filterDriver = [];
            $this->filterVehicle = [];
            $this->filterCompletedFrom = '';
            $this->filterCompletedTo = '';
            $this->filterStatusCompleted = 'all';
            $this->filterSortDirection = null;
            
            $this->appliedDestination = [];
            $this->appliedDriver = [];
            $this->appliedVehicle = [];
            $this->appliedCompletedFrom = null;
            $this->appliedCompletedTo = null;
            $this->appliedStatusCompleted = 'all';
            $this->sortDirection = null;
        } else {
            $this->filterDateFrom = null;
            $this->filterDateTo = null;
            $this->filterStatus = '';
            $this->filterSortDirection = null;
            $this->appliedDateFrom = null;
            $this->appliedDateTo = null;
            $this->appliedStatus = '';
            $this->sortDirection = null;
        }
        $this->filtersActive = false;
        $this->resetPage();
    }
    
    public function removeFilter($filterName)
    {
        if ($this->viewMode === 'completed') {
            switch ($filterName) {
                case 'destination':
                    $this->filterDestination = [];
                    $this->appliedDestination = [];
                    break;
                case 'driver':
                    $this->filterDriver = [];
                    $this->appliedDriver = [];
                    break;
                case 'vehicle':
                    $this->filterVehicle = [];
                    $this->appliedVehicle = [];
                    break;
                case 'completedFrom':
                    $this->filterCompletedFrom = '';
                    $this->appliedCompletedFrom = null;
                    break;
                case 'completedTo':
                    $this->filterCompletedTo = '';
                    $this->appliedCompletedTo = null;
                    break;
            }
        } else {
            // Active mode remove filter logic (if needed)
        }
        $this->checkFiltersActive();
        $this->resetPage();
    }
    
    public function removeSpecificFilter($filterName, $value)
    {
        if ($this->viewMode === 'completed') {
            switch ($filterName) {
                case 'destination':
                    $this->filterDestination = array_values(array_filter($this->filterDestination, fn($id) => $id != $value));
                    $this->appliedDestination = array_values(array_filter($this->appliedDestination, fn($id) => $id != $value));
                    break;
                case 'driver':
                    $this->filterDriver = array_values(array_filter($this->filterDriver, fn($id) => $id != $value));
                    $this->appliedDriver = array_values(array_filter($this->appliedDriver, fn($id) => $id != $value));
                    break;
                case 'vehicle':
                    $this->filterVehicle = array_values(array_filter($this->filterVehicle, fn($id) => $id != $value));
                    $this->appliedVehicle = array_values(array_filter($this->appliedVehicle, fn($id) => $id != $value));
                    break;
            }
        }
        $this->checkFiltersActive();
        $this->resetPage();
    }

    public function openCreateModal()
    {
        // Dispatch event to shared create component
        $this->dispatch('openCreateModal');
    }

    // Create functionality moved to Shared/Slips/Create component
    
    public function render()
    {
        $location = Session::get('location_id');
        $isCompletedMode = $this->viewMode === 'completed';

        // Optimize relationship loading by only selecting needed fields
        $query = DisinfectionSlip::with([
            'vehicle' => function($q) {
                $q->select('id', 'vehicle', 'disabled', 'deleted_at')->withTrashed();
            },
            'location' => function($q) {
                $q->select('id', 'location_name', 'disabled', 'deleted_at')->withTrashed();
            },
            'destination' => function($q) {
                $q->select('id', 'location_name', 'disabled', 'deleted_at')->withTrashed();
            },
            'driver' => function($q) {
                $q->select('id', 'first_name', 'middle_name', 'last_name', 'disabled', 'deleted_at')->withTrashed();
            },
            'hatcheryGuard' => function($q) {
                $q->select('id', 'first_name', 'middle_name', 'last_name', 'username', 'disabled', 'deleted_at')->withTrashed();
            },
            'receivedGuard' => function($q) {
                $q->select('id', 'first_name', 'middle_name', 'last_name', 'username', 'disabled', 'deleted_at')->withTrashed();
            }
        ]);

        if ($isCompletedMode) {
            // Completed mode query
            $query->whereIn('status', [3, 4])
                ->where(function($query) use ($location) {
                    $query->where(function($q) use ($location) {
                        $q->where('location_id', $location)
                          ->where('hatchery_guard_id', Auth::id());
                    })
                    ->orWhere(function($q) use ($location) {
                        $q->where('destination_id', $location)
                          ->where('received_guard_id', Auth::id());
                    });
                })
                ->when($this->search, function($q) {
                    $q->where('slip_id', 'like', '%' . $this->search . '%');
                })
                ->when(!empty($this->appliedDestination), function($q) {
                    $q->whereIn('destination_id', $this->appliedDestination);
                })
                ->when(!empty($this->appliedDriver), function($q) {
                    $q->whereIn('driver_id', $this->appliedDriver);
                })
                ->when(!empty($this->appliedVehicle), function($q) {
                    $q->whereIn('vehicle_id', $this->appliedVehicle);
                })
                ->when($this->appliedCompletedFrom, function($q) {
                    $q->whereDate('completed_at', '>=', $this->appliedCompletedFrom);
                })
                ->when($this->appliedCompletedTo, function($q) {
                    $q->whereDate('completed_at', '<=', $this->appliedCompletedTo);
                })
                ->when(in_array($this->appliedStatusCompleted, ['completed', 'incomplete']), function($q) {
                    $statusValue = $this->appliedStatusCompleted === 'completed' ? 3 : 4;
                    $q->where('status', $statusValue);
                })
                ->when($this->sortDirection === 'asc', function($q) {
                    $q->orderBy('completed_at', 'asc');
                })
                ->when($this->sortDirection === 'desc', function($q) {
                    $q->orderBy('completed_at', 'desc');
                })
                ->when($this->sortDirection === null, function($q) {
                    $q->orderBy('completed_at', 'desc');
                });
            
            $slips = $query->paginate(10);
        } else {
            // Active mode query
            if ($this->type === 'incoming') {
                $query->where('destination_id', $location)
                      ->where('location_id', '!=', $location)
                      ->where('status', 2)
                      ->where(function($q) {
                          $q->whereNull('received_guard_id')
                            ->orWhere('received_guard_id', Auth::id());
                      });
            } else {
                $query->where('location_id', $location)
                      ->where('hatchery_guard_id', Auth::id())
                      ->whereIn('status', [0, 1, 2]);
            }

            $slips = $query
                ->when($this->search, function($q) {
                    $q->where('slip_id', 'like', '%' . $this->search . '%');
                })
                ->when($this->filtersActive && $this->appliedDateFrom, function($q) {
                    $q->whereDate('created_at', '>=', $this->appliedDateFrom);
                })
                ->when($this->filtersActive && $this->appliedDateTo, function($q) {
                    $q->whereDate('created_at', '<=', $this->appliedDateTo);
                })
                ->when($this->filtersActive && $this->appliedStatus !== '', function($q) {
                    $q->where('status', $this->appliedStatus);
                })
                ->when($this->sortDirection === 'asc', function($q) {
                    $q->orderBy('slip_id', 'asc');
                })
                ->when($this->sortDirection === 'desc', function($q) {
                    $q->orderBy('slip_id', 'desc');
                })
                ->when($this->sortDirection === null, function($q) {
                    $q->orderBy('created_at', 'desc');
                })
                ->paginate(5);
        }

        return view('livewire.slips.slip-list', [
            'slips' => $slips,
            'viewMode' => $this->viewMode,
            'availableStatuses' => $this->availableStatuses,
            // NOTE: vehicles, locations, drivers removed - views use searchable-dropdown-paginated component
            // which calls getPaginatedVehicles, getPaginatedLocations, getPaginatedDrivers on-demand
        ]);
    }
    
    // Reason management methods (for super guards - no delete)
    public function getReasonsProperty()
    {
        $query = Reason::query()
            ->select(['id', 'reason_text', 'is_disabled'])
            ->orderBy('reason_text', 'asc');
        
        // Filter by status if not 'all'
        if ($this->filterReasonStatus !== 'all') {
            $isDisabled = $this->filterReasonStatus === 'disabled';
            $query->where('is_disabled', $isDisabled);
        }
        
        // Filter by search term if provided
        if (!empty($this->searchReasonSettings)) {
            $searchTerm = strtolower(trim($this->searchReasonSettings));
            $query->whereRaw('LOWER(reason_text) LIKE ?', ['%' . $searchTerm . '%']);
        }
        
        // Use database pagination
        $perPage = 5;
        return $query->paginate($perPage, ['*'], 'page', $this->reasonsPage);
    }
    
    // Separate pagination methods for reasons (don't override default pagination)
    public function gotoReasonsPage($page)
    {
        $this->reasonsPage = $page;
    }
    
    public function previousReasonsPage()
    {
        if ($this->reasonsPage > 1) {
            $this->reasonsPage--;
        }
    }
    
    public function nextReasonsPage()
    {
        $this->reasonsPage++;
    }
    
    public function openCreateReasonModal()
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        $this->newReasonText = '';
        $this->showCreateReasonModal = true;
    }
    
    public function closeCreateReasonModal()
    {
        $this->newReasonText = '';
        $this->showCreateReasonModal = false;
        $this->resetErrorBag();
    }
    
    public function createReason()
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        $this->validate([
            'newReasonText' => [
                'required',
                'string',
                'max:255',
                'min:1',
                function ($attribute, $value, $fail) {
                    $trimmedValue = trim($value);
                    $exists = Reason::whereRaw('LOWER(reason_text) = ?', [strtolower($trimmedValue)], 'and')
                        ->exists();
                    if ($exists) {
                        $fail('This reason already exists.');
                    }
                },
            ],
        ], [], [
            'newReasonText' => 'Reason text',
        ]);
        
        $reason = Reason::create([
            'reason_text' => trim($this->newReasonText),
            'disabled' => false,
        ]);
        
        Logger::create(
            Reason::class,
            $reason->id,
            "Added new reason: {$reason->reason_text}",
            $reason->only(['reason_text', 'is_disabled'])
        );
        
        $this->dispatch('toast', message: 'Reason created successfully.', type: 'success');
        $this->closeCreateReasonModal();
        $this->resetPage();
    }
    
    public function startEditingReason($reasonId)
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        $reason = Reason::find($reasonId, ['id', 'reason_text']);
        if ($reason) {
            $this->editingReasonId = $reasonId;
            $this->editingReasonText = $reason->reason_text;
            $this->originalReasonText = $reason->reason_text;
        }
    }
    
    public function saveReasonEdit()
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        try {
            $this->validate([
                'editingReasonText' => [
                    'required',
                    'string',
                    'max:255',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $trimmedValue = trim($value);
                        $exists = Reason::where('id', '!=', $this->editingReasonId, 'and')
                            ->whereRaw('LOWER(reason_text) = ?', [strtolower($trimmedValue)], 'and')
                            ->exists();
                        if ($exists) {
                            $fail('This reason already exists.');
                        }
                    },
                ],
            ], [], [
                'editingReasonText' => 'Reason text',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $firstError = collect($errors)->flatten()->first();
            if ($firstError) {
                $this->dispatch('toast', message: $firstError, type: 'error');
            }
            throw $e;
        }
        
        if (trim($this->editingReasonText) === $this->originalReasonText) {
            $this->dispatch('toast', message: 'No changes detected.', type: 'info');
            $this->cancelEditing();
            return;
        }
        
        $this->showSaveConfirmation = true;
    }
    
    public function confirmSaveReasonEdit()
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        $this->savingReason = true;
        $reason = Reason::find($this->editingReasonId, ['id', 'reason_text', 'is_disabled']);
        
        if ($reason) {
            $oldValues = $reason->only(['reason_text', 'is_disabled']);
            $reason->reason_text = trim($this->editingReasonText);
            $reason->save();
            
            Logger::update(
                Reason::class,
                $reason->id,
                "Updated reason: {$reason->reason_text}",
                $oldValues,
                $reason->only(['reason_text', 'is_disabled'])
            );
            
            $this->dispatch('toast', message: 'Reason updated successfully.', type: 'success');
        }
        
        $this->showSaveConfirmation = false;
        $this->cancelEditing();
        $this->resetPage();
        $this->savingReason = false;
    }
    
    public function cancelEditing()
    {
        $this->editingReasonId = null;
        $this->editingReasonText = '';
        $this->originalReasonText = '';
    }
    
    public function toggleReasonDisabled($reasonId)
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        $reason = Reason::find($reasonId, ['id', 'reason_text', 'is_disabled']);
        if ($reason) {
            $oldValues = $reason->only(['reason_text', 'is_disabled']);
            $reason->disabled = !$reason->disabled;
            $reason->save();
            
            Logger::update(
                Reason::class,
                $reason->id,
                ($reason->disabled ? "Disabled reason: {$reason->reason_text}" : "Enabled reason: {$reason->reason_text}"),
                $oldValues,
                $reason->only(['reason_text', 'is_disabled'])
            );
            
            $status = $reason->disabled ? 'disabled' : 'enabled';
            $this->dispatch('toast', message: "Reason {$status} successfully.", type: 'success');
            $this->resetPage();
        }
    }
    
    public function attemptCloseReasonsModal()
    {
        if ($this->editingReasonId !== null) {
            $this->showUnsavedChangesConfirmation = true;
        } else {
            $this->closeReasonsModal();
        }
    }
    
    public function closeWithoutSaving()
    {
        $this->showUnsavedChangesConfirmation = false;
        $this->cancelEditing();
        $this->closeReasonsModal();
    }
    
    public function closeReasonsModal()
    {
        $this->newReasonText = '';
        $this->searchReasonSettings = '';
        $this->cancelEditing();
        $this->showReasonsModal = false;
        $this->showSaveConfirmation = false;
        $this->showUnsavedChangesConfirmation = false;
        $this->showDeleteReasonConfirmation = false;
        $this->reasonToDelete = null;
    }
    
    public function openReasonsModal()
    {
        if (!$this->isSuperGuard()) {
            return;
        }
        
        $this->newReasonText = '';
        $this->searchReasonSettings = '';
        $this->cancelEditing();
        $this->showReasonsModal = true;
        $this->showSaveConfirmation = false;
        $this->showUnsavedChangesConfirmation = false;
        $this->showDeleteReasonConfirmation = false;
        $this->reasonToDelete = null;
    }
    
    // Delete reason method (not used by super guards, but needed for component compatibility)
    public function confirmDeleteReason($reasonId)
    {
        // Super guards cannot delete reasons
        // This method exists only for component compatibility
        // The delete button is hidden for non-superadmins in the component
    }
}