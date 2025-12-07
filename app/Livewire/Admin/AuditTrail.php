<?php

namespace App\Livewire\Admin;

use App\Models\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Response;

class AuditTrail extends Component
{
    use WithPagination;

    public $search = '';
    public $showFilters = false;
    
    // Sorting properties
    public $sortColumns = ['created_at' => 'desc']; // Default sort by created_at descending
    
    // Filter properties
    public $filterAction = null;
    public $filterModelType = null;
    public $filterUserType = null;
    public $filterCreatedFrom = '';
    public $filterCreatedTo = '';
    
    // Applied filters
    public $appliedAction = null;
    public $appliedModelType = null;
    public $appliedUserType = null;
    public $appliedCreatedFrom = '';
    public $appliedCreatedTo = '';
    
    public $filtersActive = false;
    
    public $availableActions = [
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'restore' => 'Restore',
    ];
    
    public $availableModelTypes = [
        'App\\Models\\DisinfectionSlip' => 'Disinfection Slip',
        'App\\Models\\User' => 'User',
        'App\\Models\\Driver' => 'Driver',
        'App\\Models\\Location' => 'Location',
        'App\\Models\\Truck' => 'Truck',
        'App\\Models\\Setting' => 'Setting',
    ];
    
    public $availableUserTypes = [
        0 => 'Guard',
        1 => 'Admin',
    ];
    
    protected $queryString = ['search'];
    
    public function applySort($column)
    {
        if (!is_array($this->sortColumns)) {
            $this->sortColumns = [];
        }
        
        // Toggle sort direction
        if (isset($this->sortColumns[$column])) {
            $currentDir = $this->sortColumns[$column];
            if ($currentDir === 'asc') {
                $this->sortColumns[$column] = 'desc';
            } else {
                unset($this->sortColumns[$column]);
            }
        } else {
            $this->sortColumns[$column] = 'asc';
        }
        
        // Ensure at least one sort column
        if (empty($this->sortColumns)) {
            $this->sortColumns = ['created_at' => 'desc'];
        }
        
        $this->resetPage();
    }
    
    public function getSortDirection($column)
    {
        return $this->sortColumns[$column] ?? null;
    }
    
    public function applyFilters()
    {
        $this->appliedAction = $this->filterAction;
        $this->appliedModelType = $this->filterModelType;
        $this->appliedUserType = $this->filterUserType;
        $this->appliedCreatedFrom = $this->filterCreatedFrom;
        $this->appliedCreatedTo = $this->filterCreatedTo;
        
        $this->filtersActive = !empty($this->appliedAction) || 
                               !empty($this->appliedModelType) || 
                               !empty($this->appliedUserType) || 
                               !empty($this->appliedCreatedFrom) || 
                               !empty($this->appliedCreatedTo);
        
        $this->showFilters = false;
        $this->resetPage();
    }
    
    public function removeFilter($filterName)
    {
        switch ($filterName) {
            case 'action':
                $this->appliedAction = null;
                $this->filterAction = null;
                break;
            case 'model_type':
                $this->appliedModelType = null;
                $this->filterModelType = null;
                break;
            case 'user_type':
                $this->appliedUserType = null;
                $this->filterUserType = null;
                break;
            case 'created_from':
                $this->appliedCreatedFrom = '';
                $this->filterCreatedFrom = '';
                break;
            case 'created_to':
                $this->appliedCreatedTo = '';
                $this->filterCreatedTo = '';
                break;
        }
        
        $this->filtersActive = !empty($this->appliedAction) || 
                               !empty($this->appliedModelType) || 
                               !empty($this->appliedUserType) || 
                               !empty($this->appliedCreatedFrom) || 
                               !empty($this->appliedCreatedTo);
        
        $this->resetPage();
    }
    
    public function clearFilters()
    {
        $this->filterAction = null;
        $this->filterModelType = null;
        $this->filterUserType = null;
        $this->filterCreatedFrom = '';
        $this->filterCreatedTo = '';
        
        $this->appliedAction = null;
        $this->appliedModelType = null;
        $this->appliedUserType = null;
        $this->appliedCreatedFrom = '';
        $this->appliedCreatedTo = '';
        
        $this->filtersActive = false;
        $this->resetPage();
    }
    
    public function exportCSV()
    {
        $logs = $this->getFilteredLogsQuery()->get();
        
        $filename = 'audit_trail_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'Date & Time',
                'User',
                'User Type',
                'Action',
                'Model Type',
                'Description',
                'IP Address',
            ]);
            
            // Data rows
            foreach ($logs as $log) {
                $userName = trim(implode(' ', array_filter([
                    $log->user_first_name,
                    $log->user_middle_name,
                    $log->user_last_name
                ])));
                
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $userName ?: 'N/A',
                    $this->availableUserTypes[$log->user_type] ?? 'N/A',
                    $this->availableActions[$log->action] ?? ucfirst($log->action),
                    $this->availableModelTypes[$log->model_type] ?? $log->model_type,
                    $log->description ?? 'N/A',
                    $log->ip_address ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    public function openPrintView()
    {
        // Audit trail doesn't support print view
        // This method exists to satisfy the export button component
        return;
    }
    
    private function getFilteredLogsQuery()
    {
        $query = Log::query();
        
        // Exclude superadmin actions (user_type != 2)
        $query->where('user_type', '!=', 2);
        
        // Search
        if (!empty($this->search)) {
            $searchTerm = trim($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('user_first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('user_last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('user_username', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filters
        if (!empty($this->appliedAction)) {
            $query->where('action', $this->appliedAction);
        }
        
        if (!empty($this->appliedModelType)) {
            $query->where('model_type', $this->appliedModelType);
        }
        
        if (!is_null($this->appliedUserType)) {
            $query->where('user_type', $this->appliedUserType);
        }
        
        if (!empty($this->appliedCreatedFrom)) {
            $query->whereDate('created_at', '>=', $this->appliedCreatedFrom);
        }
        
        if (!empty($this->appliedCreatedTo)) {
            $query->whereDate('created_at', '<=', $this->appliedCreatedTo);
        }
        
        // Sorting
        foreach ($this->sortColumns as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        
        return $query;
    }
    
    public function render()
    {
        $logs = $this->getFilteredLogsQuery()->paginate(15);
        
        return view('livewire.admin.audit-trail', [
            'logs' => $logs,
        ]);
    }
}
