<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Department;

class PeopleDirectory extends Component
{
    use WithPagination;

    public $search = '';
    public $filterDepartment = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with('department')
            ->where('status', 'active');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('job_title', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        return view('livewire.people-directory', [
            'people' => $query->orderBy('name')->paginate(12),
            'departments' => Department::all()
        ])->layout('components.layouts.app');
    }
}
