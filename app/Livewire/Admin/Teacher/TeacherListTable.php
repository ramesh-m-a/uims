<?php

namespace App\Livewire\Admin\Teacher;

use App\Livewire\Master\MasterTableBase;
use App\Models\Admin\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class TeacherListTable extends MasterTableBase
{
    protected string $modelClass = User::class;

    public string $pageRoute = '';

    public bool $showPhotoModal = false;
    public ?string $photo = null;

    public ?int $selectedStream = null;
    public ?int $selectedCollege = null;

    public $streams = [];
    public $colleges = [];

    public function mount(): void
    {
        $this->pageRoute = request()->route()->getName();

        // Step 1: Load all streams
        $this->streams = DB::table('mas_stream')
            ->where('mas_stream_status_id', 1)
            ->orderBy('mas_stream_name')
            ->get();
    }

    public function updatedSelectedStream($value)
    {
        // Reset college + table when stream changes
        $this->selectedCollege = null;

        // Step 2: Load colleges for selected stream
        $this->colleges = DB::table('mas_college')
            ->where('mas_college_stream_id', $value)
            ->where('mas_college_status_id', 1)
            ->orderBy('mas_college_name')
            ->get();
    }

    protected function permissionPrefix(): string
    {
        return 'admin.teachers';
    }

    protected function configKey(): string
    {
        return 'admin.teacher';
    }

    protected function breadcrumbMode(): string
    {
        return 'View';
    }

    public function render()
    {
        // If college not selected → don't load table
        if (!$this->selectedCollege) {
            return view('livewire.admin.teacher.teacher-list-table', [
                'rows' => collect(), // empty
                'streams' => $this->streams,
                'colleges' => $this->colleges,
                'pageRoute' => $this->pageRoute,
                'breadcrumbMode' => $this->breadcrumbMode(),
            ]);
        }

        $rows = $this->baseQuery()

            ->with([
                'stream:id,mas_stream_name',
                'college:id,mas_college_name',
                'designation:id,mas_designation_name',
                'basicDetails.department:id,mas_department_name',
                'basicDetails.examinerDetails:id,examiner_details_basic_details_id,examiner_details_rank,examiner_details_type',
            ])

            ->where('user_college_id', $this->selectedCollege)

            ->select([
                'id',
                'name',
                'mobile',
                'photo_path',
                'user_stream_id',
                'user_college_id',
                'user_designation_id',
            ])

            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function ($user) {

            $url = $user->avatar_url
                ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name);

            $html = <<<HTML
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
HTML;

            $user->photo_path = new HtmlString($html);
            return $user;
        });

        return view('livewire.admin.teacher.teacher-list-table', [
            'rows' => $rows,
            'streams' => $this->streams,
            'colleges' => $this->colleges,
            'pageRoute' => $this->pageRoute,
            'breadcrumbMode' => $this->breadcrumbMode(),
        ]);
    }
}
