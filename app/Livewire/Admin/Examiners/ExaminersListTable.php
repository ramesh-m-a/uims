<?php

namespace App\Livewire\Admin\Examiners;

use App\Livewire\Master\MasterTableBase;
use App\Models\Admin\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ExaminersListTable extends MasterTableBase
{
    /* ==========================
    | MODEL
    ========================== */
    protected string $modelClass = User::class;

    /* ==========================
    | PAGE / UI STATE
    ========================== */
    public string $pageRoute = '';

    public bool $showPhotoModal = false;
    public ?string $photo = null;

    public ?int $selectedCollege = null;

    public array $streams = [];
    public array $colleges = [];

    // ✅ FIX: allow null
    protected ?int $streamId = null;

    /* ===================== */
    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();

        // May be null for super admin
        $this->streamId = Auth::user()->user_stream_id;

        // --------------------------
        // STREAM DROPDOWN DATA
        // --------------------------
        if ($this->streamId) {
            // Locked user → show only their stream
            $this->streams = DB::table('mas_stream')
                ->where('id', $this->streamId)
                ->get()
                ->toArray();
        } else {
            // Super admin → show all streams
            $this->streams = DB::table('mas_stream')
                ->orderBy('mas_stream_name')
                ->get()
                ->toArray();
        }

        // --------------------------
        // COLLEGES DROPDOWN DATA
        // --------------------------
        $collegeQuery = DB::table('users')
            ->join('mas_college', 'mas_college.id', '=', 'users.user_college_id')
            ->where('mas_college.mas_college_status_id', 1);

        if ($this->streamId) {
            $collegeQuery
                ->where('users.user_stream_id', $this->streamId)
                ->where('mas_college.mas_college_stream_id', $this->streamId);
        }

        $this->colleges = $collegeQuery
            ->select('mas_college.id', 'mas_college.mas_college_name')
            ->distinct()
            ->orderBy('mas_college.mas_college_name')
            ->get()
            ->toArray();
    }

    protected function permissionPrefix(): string
    {
        return 'admin.examiners';
    }

    protected function configKey(): string
    {
        return 'admin.examiners';
    }

    protected function breadcrumbMode(): string
    {
        return 'View';
    }

    /* ==========================
       COLUMNS CONTRACT
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
                'field'      => 'basicDetails.department.mas_department_name',
                'label'      => 'Department',
                'type'       => 'text',
                'sortable'   => false,
                'filterable' => false,
            ],
            [
                'field'      => 'basicDetails.examinerDetails.examiner_details_rank',
                'label'      => 'Rank',
                'type'       => 'text',
                'sortable'   => false,
                'filterable' => false,
            ],
            [
                'field'      => 'basicDetails.examinerDetails.examiner_details_type',
                'label'      => 'Type',
                'type'       => 'text',
                'sortable'   => false,
                'filterable' => false,
            ],
        ];
    }

    /* ==========================
       BASE QUERY
       ========================== */
    protected function baseQuery(): Builder
    {
        $query = parent::baseQuery()
            ->with([
                'stream:id,mas_stream_name',
                'college:id,mas_college_name',
                'designation:id,mas_designation_name',
                'basicDetails:id,basic_details_user_id,basic_details_department_id',
                'basicDetails.department:id,mas_department_name',
                'basicDetails.examinerDetails:id,examiner_details_basic_details_id,examiner_details_rank,examiner_details_type',
            ]);

        // 🔒 Apply stream lock only if exists
        if ($this->streamId) {
            $query->where('user_stream_id', $this->streamId);
        }

        return $query;
    }

    /* ===================== */
    public function render()
    {
        $rows = $this->baseQuery()

            ->when(
                $this->selectedCollege,
                fn ($q) => $q->where('user_college_id', $this->selectedCollege)
            )

            ->select([
                'id',
                'name',
                'mobile',
                'email',
                'photo_path',
                'user_stream_id',
                'user_college_id',
                'user_designation_id',
            ])

            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Photo rendering
        $rows->getCollection()->transform(function ($user) {

            $url = $user->photo_path
                ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name);

            $user->photo_path = new HtmlString(<<<HTML
<img
    src="{$url}"
    class="h-8 w-8 rounded-full object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-blue-500"
    alt="Profile Photo"
    onclick="
        const c = Livewire.find('{$this->getId()}');
        if (c) {
            c.set('photo', '{$url}');
            c.set('showPhotoModal', true);
        }
    "
/>
HTML);

            return $user;
        });

        return view('livewire.admin.examiners.examiners-list-table', [
            'rows' => $rows,
            'pageRoute' => $this->pageRoute,
            'breadcrumbMode' => $this->breadcrumbMode(),
        ]);
    }
}
