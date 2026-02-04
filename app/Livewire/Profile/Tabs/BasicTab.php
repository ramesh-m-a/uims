<?php

namespace App\Livewire\Profile\Tabs;

use App\Support\UpperCase;
use Livewire\Component;
use App\Models\UserProfileDraft;
use App\Models\Master\Common\Gender;
use App\Models\Master\Common\Religion;
use App\Models\Master\Common\Category;
use Carbon\Carbon;
use App\Models\Admin\AdminRole;
use App\Models\Master\Config\Academic\Stream;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\Config\Academic\Department;;

class BasicTab extends Component
{
    public UserProfileDraft $draft;

    public $genders = [];
    public $religions = [];
    public $categories = [];

    public $designationName = null;
    public $departmentName  = null;

    public $adminRoles = [];
    public $selectedAdminRole = null;

    public $departments = [];
    public $selectedDepartment = null;

    public array $basic = [
        'dob' => null,
        'gender_id' => null,
        'father_name' => null,
        'religion_id' => null,
        'category_id' => null,
    ];

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;

        $this->genders = Uppercase::upper(
            Gender::orderBy('mas_gender_name')->get(),
            'mas_gender_name'
        );

        $this->religions = Uppercase::upper(
            Religion::orderBy('mas_religion_name')->get(),
            'mas_religion_name'
        );

        $this->categories = Uppercase::upper(
            Category::orderBy('mas_category_name')->get(),
            'mas_category_name'
        );

        // -------------------------------
        // BASIC (DRAFT)
        // -------------------------------
        $this->basic = array_merge(
            $this->basic,
            $draft->data['basic'] ?? []
        );

        // -------------------------------
        // USER (STRICT SOURCE)
        // -------------------------------
        $user = $draft->user ?? Auth::user();

        // ✅ Designation (READ ONLY)
        $this->designationName =
            $user?->designation?->mas_designation_name;

        // -------------------------------
        // DEPARTMENT (FILTERED BY STREAM)
        // -------------------------------
        if ($user?->user_stream_id) {
            $this->departments = UpperCase::upper(
                Department::where(
                    'mas_department_stream_id',
                    $user->user_stream_id
                )
                    ->orderBy('mas_department_name')
                    ->get(),
                'mas_department_name'
            );
        }

        $this->selectedDepartment =
            $draft->data['basic']['department_id'] ?? null;

        // -------------------------------
        // ADMIN ROLES
        // -------------------------------
        $this->adminRoles = UpperCase::upper(
            AdminRole::active()->orderBy('mas_admin_role_name')->get(),
            'mas_admin_role_name'
        );

        $this->selectedAdminRole =
            $draft->data['admin_role_id'] ?? null;

        // =====================================================
        // 🔍 DEBUG DUMP — END OF METHOD (AS REQUESTED)
        // =====================================================

       /* dd([
            'draft_id' => $draft->id,

            // USER
            'user_id' => $user?->id,
            'user_stream_id' => $user?->user_stream_id,
            'user_designation_id' => $user?->user_designation_id,

            // DESIGNATION
            'designation_name' => $this->designationName,

            // DEPARTMENT
            'departments_count' => $this->departments?->count(),
            'departments_list' => $this->departments?->pluck('mas_department_name', 'id'),
            'selected_department' => $this->selectedDepartment,

            // ADMIN ROLES
            'admin_roles_count' => $this->adminRoles?->count(),
            'admin_roles_list' => $this->adminRoles?->pluck('mas_admin_role_name', 'id'),
            'selected_admin_role' => $this->selectedAdminRole,

            // BASIC DRAFT DATA
            'basic_from_draft' => $this->basic,

            // RAW DRAFT DATA (FULL)
            'draft_data' => $draft->data,
        ]);*/

    }

    /** LIVEWIRE COMPUTED PROPERTY */
    public function getCalculatedAgeProperty(): string
    {
        if (empty($this->basic['dob'])) {
            return '0 Years 0 Months 0 Days';
        }

        $diff = Carbon::parse($this->basic['dob'])->diff(now());
        return "{$diff->y} Years {$diff->m} Months {$diff->d} Days";
    }

    public function save(): void
    {
        $this->validate([
            'basic.dob' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(30)->format('Y-m-d'),
            ],
            'basic.gender_id' => 'required|integer|min:1',
            'basic.father_name' => 'required|string|min:3',
            'basic.religion_id' => 'required|integer|min:1',
            'basic.category_id' => 'required|integer|min:1',
            'selectedDepartment' => 'required|integer|min:1',
        ]);

        $data = $this->draft->data;

        // Persist basic fields
        $data['basic'] = $this->basic;

        // Persist department
        $data['basic']['department_id'] = $this->selectedDepartment;

        // ✅ FIX: persist designation into draft (from user, since it's read-only)
        $user = $this->draft->user ?? Auth::user();

        $data['basic']['designation_id'] = $user?->user_designation_id;
        $data['basic']['designation_name'] = $user?->designation?->mas_designation_name;

        // Persist admin role
        $data['admin_role_id'] = $this->selectedAdminRole;

        $completed = $this->draft->completed_tabs ?? [];

        if (! in_array('basic', $completed)) {
            $completed[] = 'basic';
        }

        $this->draft->update([
            'data'           => $data,
            'completed_tabs' => $completed,
            'current_tab'    => 'address',
        ]);

        $this->dispatch('switch-tab', tab: 'address');
    }

    public function render()
    {
        return view('livewire.profile.tabs.basic-tab');
    }

    /* ================= COMPLETION CHECK ================= */

    public function getIsCompleteProperty(): bool
    {
        return filled($this->basic['dob'])
            && filled($this->basic['gender_id'])
            && filled($this->basic['father_name'])
            && filled($this->basic['religion_id'])
            && filled($this->basic['category_id']);
    }

    public function getDobMaxProperty(): string
    {
        return now()->subYears(25)->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        // Re-apply uppercase after any Livewire update
        $this->genders = \App\Support\UpperCase::upper(
            $this->genders,
            'mas_gender_name'
        );

        $this->religions = \App\Support\UpperCase::upper(
            $this->religions,
            'mas_religion_name'
        );

        $this->categories = \App\Support\UpperCase::upper(
            $this->categories,
            'mas_category_name'
        );

        $this->departments = \App\Support\UpperCase::upper(
            $this->departments,
            'mas_department_name'
        );

        $this->adminRoles = \App\Support\UpperCase::upper(
            $this->adminRoles,
            'mas_admin_role_name'
        );
    }

}
