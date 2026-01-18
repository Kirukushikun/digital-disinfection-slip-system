<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Services\Logger;
use Illuminate\Support\Facades\Cache;
class Admins extends Component
{
    use WithPagination;

    public $search = '';
    public $showFilters = false;
    
    // Sorting properties - supports multiple columns
    public $sortColumns = ['first_name' => 'asc']; // Default sort by first_name ascending
    
    // Filter properties
    public $filterStatus = null; // null = All Admins, 0 = Enabled, 1 = Disabled
    public $filterCreatedFrom = '';
    public $filterCreatedTo = '';
    
    // Applied filters
    public $appliedStatus = null; // null = All Admins, 0 = Enabled, 1 = Disabled
    public $appliedCreatedFrom = '';
    public $appliedCreatedTo = '';
    
    
    public $availableStatuses = [
        0 => 'Enabled',
        1 => 'Disabled',
    ];
    
    // Ensure filterStatus is properly typed when updated
    public function updatedFilterStatus($value)
    {
        // Handle null, empty string, or numeric values (0, 1)
        // null/empty = All Admins, 0 = Enabled, 1 = Disabled
        // The select will send values as strings, so we convert to int
        if ($value === null || $value === '' || $value === false) {
            $this->filterStatus = null;
        } elseif (is_numeric($value)) {
            $intValue = (int)$value;
            if ($intValue >= 0 && $intValue <= 1) {
                // Store as integer (0 or 1)
                $this->filterStatus = $intValue;
            } else {
                $this->filterStatus = null;
            }
        } else {
            $this->filterStatus = null;
        }
    }
    
    public $showDeleted = false; // Toggle to show deleted items

    protected $listeners = [
        'admin-created' => '$refresh',
        'admin-updated' => '$refresh',
        'admin-deleted' => '$refresh',
        'admin-status-toggled' => '$refresh',
        'admin-password-reset' => '$refresh',
        'admin-restored' => '$refresh',
    ];

    protected $queryString = ['search'];
    
    public function applySort($column)
    {
        // Initialize sortColumns if it's not an array (for backward compatibility)
        if (!is_array($this->sortColumns)) {
            $this->sortColumns = [];
        }
        
        // Single-column sorting: clear all other sorts and only sort by the clicked column
        if (isset($this->sortColumns[$column])) {
            $currentDirection = $this->sortColumns[$column];
            if ($currentDirection === 'asc') {
                // Toggle to desc
                $this->sortColumns = [$column => 'desc'];
            } else {
                // Remove from sort (cycle: asc -> desc -> remove -> default to first_name)
                $this->sortColumns = ['first_name' => 'asc'];
            }
        } else {
            // Add column with ascending direction (clear all others)
            $this->sortColumns = [$column => 'asc'];
        }
        
        $this->resetPage();
    }
    
    // Helper method to get sort direction for a column
    public function getSortDirection($column)
    {
        return $this->sortColumns[$column] ?? null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->appliedStatus = $this->filterStatus;
        $this->appliedCreatedFrom = $this->filterCreatedFrom;
        $this->appliedCreatedTo = $this->filterCreatedTo;
        $this->showFilters = false;
        $this->resetPage();
    }

    public function removeFilter($filterName)
    {
        if ($filterName === 'status') {
            $this->appliedStatus = null;
            $this->filterStatus = null;
        } elseif ($filterName === 'createdFrom') {
            $this->appliedCreatedFrom = '';
            $this->filterCreatedFrom = '';
        } elseif ($filterName === 'createdTo') {
            $this->appliedCreatedTo = '';
            $this->filterCreatedTo = '';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->appliedStatus = null;
        $this->appliedCreatedFrom = '';
        $this->appliedCreatedTo = '';
        $this->filterStatus = null;
        $this->filterCreatedFrom = '';
        $this->filterCreatedTo = '';
        $this->resetPage();
    }

    public function openEditModal($userId)
    {
        $this->dispatch('openEditModal', $userId);
    }

    public function openDisableModal($userId)
    {
        $this->dispatch('openDisableModal', $userId);
    }

    public function openResetPasswordModal($userId)
    {
        $this->dispatch('openResetPasswordModal', $userId);
    }

    public function openDeleteModal($userId)
    {
        $this->dispatch('openDeleteModal', $userId);
    }

    public function openRestoreModal($userId)
    {
        $this->dispatch('openRestoreModal', $userId);
    }

    public function openCreateModal()
    {
        $this->dispatch('openCreateModal');
    }


    public function toggleDeletedView()
    {
        $this->showDeleted = !$this->showDeleted;
        
        // Don't clear filters - keep date filters active, they'll filter by deleted_at or created_at based on mode
        // Only clear Status filter when entering restore mode (it can't be applied to deleted records)
        if ($this->showDeleted) {
            // Entering restore mode - clear status filter
            $this->filterStatus = null;
            $this->appliedStatus = null;
        }
        
        $this->resetPage();
    }


    public function render()
    {
        $users = User::where('user_type', 1)
            ->when(!$this->showDeleted, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when($this->showDeleted, function ($query) {
                $query->onlyTrashed();
            })
            ->when($this->search, function ($query) {
                $searchTerm = $this->search;
                
                // Sanitize search term to prevent SQL injection
                $searchTerm = trim($searchTerm);
                $searchTerm = preg_replace('/[%_]/', '', $searchTerm); // Remove LIKE wildcards for safety
                
                if (empty($searchTerm)) {
                    return;
                }
                
                // Escape special characters for LIKE
                $escapedSearchTerm = str_replace(['%', '_'], ['\%', '\_'], $searchTerm);
                
                // Check if search term starts with @
                if (str_starts_with($searchTerm, '@')) {
                    // Search only username (remove @ symbol)
                    $cleanedSearchTerm = ltrim($searchTerm, '@');
                    $escapedCleanedSearchTerm = str_replace(['%', '_'], ['\%', '\_'], $cleanedSearchTerm);
                    $query->where('username', 'like', '%' . $escapedCleanedSearchTerm . '%');
                } else {
                    // Search only names (first, middle, last, and combinations)
                    // Use parameterized CONCAT to prevent SQL injection
                    $query->where(function ($q) use ($escapedSearchTerm) {
                        $q->where('first_name', 'like', '%' . $escapedSearchTerm . '%')
                          ->orWhere('middle_name', 'like', '%' . $escapedSearchTerm . '%')
                          ->orWhere('last_name', 'like', '%' . $escapedSearchTerm . '%')
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $escapedSearchTerm . '%'])
                          ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ['%' . $escapedSearchTerm . '%']);
                    });
                }
            })
            ->when($this->appliedCreatedFrom, function ($query) {
                // Use deleted_at when in restore mode, created_at otherwise
                $dateColumn = $this->showDeleted ? 'deleted_at' : 'created_at';
                $query->whereDate($dateColumn, '>=', $this->appliedCreatedFrom);
            })
            ->when($this->appliedCreatedTo, function ($query) {
                // Use deleted_at when in restore mode, created_at otherwise
                $dateColumn = $this->showDeleted ? 'deleted_at' : 'created_at';
                $query->whereDate($dateColumn, '<=', $this->appliedCreatedTo);
            })
            ->when($this->appliedStatus !== null && !$this->showDeleted, function ($query) {
                if ($this->appliedStatus === 0) {
                    // Enabled (disabled = false)
                    $query->where('disabled', false);
                } elseif ($this->appliedStatus === 1) {
                    // Disabled (disabled = true)
                    $query->where('disabled', true);
                }
            })
            // Apply multi-column sorting
            ->when(!empty($this->sortColumns), function($query) {
                // Initialize sortColumns if it's not an array
                if (!is_array($this->sortColumns)) {
                    $this->sortColumns = ['first_name' => 'asc'];
                }
                
                $firstSort = true;
                foreach ($this->sortColumns as $column => $direction) {
                    if ($column === 'created_at' && $firstSort && !$this->showDeleted) {
                        // Special handling for created_at when it's the primary sort (only when not in deleted mode)
                        // First: prioritize recent records (within 5 minutes) over older ones
                        $query->orderByRaw("CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN 0 ELSE 1 END")
                            // Second: sort recent records by created_at DESC, older records also by created_at (to avoid NULL sorting issues)
                            ->orderByRaw("CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN created_at ELSE created_at END DESC")
                            ->orderBy('created_at', $direction);
                    } else {
                        $query->orderBy($column, $direction);
                    }
                    $firstSort = false;
                }
            })
            ->when(empty($this->sortColumns) && !$this->showDeleted, function($query) {
                // Default sort if no sorts are set (only when not in deleted mode)
                $query->orderBy('first_name', 'asc');
            })
            ->when(empty($this->sortColumns) && $this->showDeleted, function ($query) {
                // Default sort when in deleted mode and no user sorting
                $query->orderBy('deleted_at', 'desc');
            })
            ->paginate(10);

        $filtersActive = $this->appliedStatus !== null || !empty($this->appliedCreatedFrom) || !empty($this->appliedCreatedTo);

        return view('livewire.super-admin.admins', [
            'users' => $users,
            'filtersActive' => $filtersActive,
            'availableStatuses' => $this->availableStatuses,
        ]);
    }

    public function getExportData()
    {
        return User::where('user_type', 1)->whereNull('deleted_at')
            ->when($this->search, function ($query) {
                $searchTerm = trim($this->search);
                $searchTerm = preg_replace('/[%_]/', '', $searchTerm);
                if (empty($searchTerm)) {
                    return;
                }
                $escapedSearchTerm = str_replace(['%', '_'], ['\%', '\_'], $searchTerm);
                if (str_starts_with($searchTerm, '@')) {
                    $cleanedSearchTerm = ltrim($searchTerm, '@');
                    $escapedCleanedSearchTerm = str_replace(['%', '_'], ['\%', '\_'], $cleanedSearchTerm);
                    $query->where('username', 'like', '%' . $escapedCleanedSearchTerm . '%');
                } else {
                    $query->where(function ($q) use ($escapedSearchTerm) {
                        $q->where('first_name', 'like', '%' . $escapedSearchTerm . '%')
                          ->orWhere('middle_name', 'like', '%' . $escapedSearchTerm . '%')
                          ->orWhere('last_name', 'like', '%' . $escapedSearchTerm . '%')
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $escapedSearchTerm . '%'])
                          ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) LIKE ?", ['%' . $escapedSearchTerm . '%']);
                    });
                }
            })
            ->when($this->appliedCreatedFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->appliedCreatedFrom);
            })
            ->when($this->appliedCreatedTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->appliedCreatedTo);
            })
            ->when($this->appliedStatus !== null, function ($query) {
                if ($this->appliedStatus === 0) {
                    $query->where('disabled', false);
                } elseif ($this->appliedStatus === 1) {
                    $query->where('disabled', true);
                }
            })
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->get();
    }

    public function exportCSV()
    {
        $data = $this->getExportData();
        $filename = 'admins_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'Photo; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Name', 'Username', 'Status', 'Created Date']);
            
            foreach ($data as $user) {
                $name = trim(implode(' ', array_filter([$user->first_name, $user->middle_name, $user->last_name])));
                $status = $user->disabled ? 'Disabled' : 'Enabled';
                fputcsv($file, [
                    $name,
                    $user->username,
                    $status,
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function openPrintView()
    {
        $data = $this->getExportData();
        $exportData = $data->map(function($user) {
            return [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'disabled' => $user->disabled,
                'created_at' => $user->created_at->toIso8601String(),
            ];
        })->toArray();
        
        $filters = [
            'search' => $this->search,
            'status' => $this->appliedStatus,
            'created_from' => $this->appliedCreatedFrom,
            'created_to' => $this->appliedCreatedTo,
        ];
        
        $sorting = $this->sortColumns ?? ['first_name' => 'asc'];
        
        $token = Str::random(32);
        Session::put("export_data_{$token}", $exportData);
        Session::put("export_filters_{$token}", $filters);
        Session::put("export_sorting_{$token}", $sorting);
        Session::put("export_data_{$token}_expires", now()->addMinutes(10));
        
        $printUrl = route('superadmin.print.admins', ['token' => $token]);
        
        $this->dispatch('open-print-window', ['url' => $printUrl]);
    }
}

