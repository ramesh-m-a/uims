<?php

namespace App\Livewire\User;

use App\Livewire\Base\AdminTableBase;
use App\Models\Admin\Role;
use App\Models\Admin\User;
use App\Models\Master\Common\Status;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Config\Academic\Designation;
use App\Models\Master\Config\Academic\Stream;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserTable extends AdminTableBase
{
    /* ==========================
    | MODEL
    ========================== */
    protected string $modelClass = User::class;

    /* ==========================
     | PAGE / UI STATE
     ========================== */
    public string $pageRoute = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public array $filters = [];

    public array $form = [
        'first_name'          => '',
        'middle_name'         => '',
        'last_name'           => '',
        'mobile'              => '',
        'email'               => '',
        'user_stream_id'      => null,
        'user_college_id'     => null,
        'user_designation_id' => null,
        'user_role_id'        => null,
        'user_status_id'      => 1,
    ];

    /* ===================== */
    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();
    }

    protected function permissionPrefix(): string
    {
        return 'user';
    }

    /* ==========================
       🔥 FIXED COLUMNS CONTRACT
       ========================== */
    protected function columns(): array
    {
        return [
            [
                'field'      => 'name',
                'label'      => 'Name',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'mobile',
                'label'      => 'Mobile',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'email',
                'label'      => 'Email',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'stream.mas_stream_name',
                'label'      => 'Stream',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'college.mas_college_name',
                'label'      => 'College',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'designation.mas_designation_name',
                'label'      => 'Designation',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'department.mas_department_name',
                'label'      => 'Department',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
            [
                'field'      => 'role.name',
                'label'      => 'Role',
                'type'       => 'text',
                'sortable'   => true,
                'filterable' => true,
            ],
        ];
    }

    protected function baseQuery(): Builder
    {
        return parent::baseQuery()
            ->with(['stream', 'college', 'designation', 'role', 'department']);
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.user.user-table', array_merge([
            'rows'         => $rows,
            'pageRoute'    => $this->pageRoute,
            'streams'      => Stream::where('mas_stream_status_id', 1)->get(),
            'colleges'     => College::where('mas_college_status_id', 1)->get(),
            'designations' => Designation::where('mas_designation_status_id', 1)->get(),
            'roles'        => Role::all(),
            'statuses'     => Status::where('is_active', 1)->get(),
        ], $this->permissions()));
    }

    /* ===================== */
    public function create(): void
    {
        abort_unless($this->can('create'), 403);

        $this->editingId = null;
        $this->form = $this->emptyForm();

        $this->resetValidation();
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function syncAndSave(): void
    {
        $this->save();
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->form;

        if (empty($data['user_status_id'])) {
            $data['user_status_id'] = 1;
        }

        $data['name'] = trim(
            "{$data['first_name']} {$data['middle_name']} {$data['last_name']}"
        );

        unset($data['first_name'], $data['middle_name'], $data['last_name']);

        if (!$this->editingId) {
            $plain = config('app.temp_user_password');

            if (empty($plain)) {
                throw new \RuntimeException('TEMP_USER_PASSWORD is not configured');
            }

            $data['password'] = Hash::make($plain);
            $data['force_password_change'] = 1;
        }

        User::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $this->js("Alpine.store('toast').push('success','User saved successfully')");

        $this->cancelEdit();
        $this->resetPage();
    }

    public function cancelEdit(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->form = $this->emptyForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    protected function emptyForm(): array
    {
        return [
            'first_name'          => '',
            'middle_name'         => '',
            'last_name'           => '',
            'mobile'              => '',
            'email'               => '',
            'user_stream_id'      => null,
            'user_college_id'     => null,
            'user_designation_id' => null,
            'user_role_id'        => null,
            'user_status_id'      => 1,
        ];
    }

    protected function permissions(): array
    {
        return [
            'canView'   => $this->can('view'),
            'canCreate' => $this->can('create'),
            'canEdit'   => $this->can('edit'),
        ];
    }

    public function hasDeleted(): bool
    {
        return false;
    }
}
