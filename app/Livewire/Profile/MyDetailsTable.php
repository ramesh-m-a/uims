<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Master\Config\Academic\Department;
use App\Models\Master\Config\Academic\College;
use App\Models\AuditLog;

class MyDetailsTable extends Component
{
    public bool $showPhotoModal = false;

    /** 🔍 Unified audit timeline */
    public $auditLogs = [];

    public function mount(): void
    {
        $this->loadAuditLogs();
    }

    /**
     * Load unified audit timeline
     * ✔ users
     * ✔ user_profile_drafts
     * ✔ approval / rejection actions
     */
    protected function loadAuditLogs(): void
    {
        $user = Auth::user();

        // Draft primary key (NOT user_id)
        $draftId = $user->profileDraft?->id;

        $this->auditLogs = AuditLog::where(function ($q) use ($user, $draftId) {

            // ===============================
            // USERS TABLE AUDITS
            // ===============================
            $q->where(function ($q) use ($user) {
                $q->where('table_name', 'users')
                    ->where('record_id', $user->id);
            });

            // ===============================
            // USER PROFILE DRAFT AUDITS
            // ===============================
            if ($draftId) {
                $q->orWhere(function ($q) use ($draftId) {
                    $q->where('table_name', 'user_profile_drafts')
                        ->where('record_id', $draftId);
                });
            }

        })
            ->orderBy('created_at', 'asc')
            ->get();

    //    dd($draftId, $this->auditLogs);
    }


    public function render()
    {
        $user  = Auth::user();
        $draft = $user->profileDraft;

      //  dd($draft,$user->photo_path );

        // ================= PHOTO =================
        $photo = $user->photo_path
            ? asset('storage/' . ltrim($user->photo_path, '/')) . '?v=' . strtotime($user->updated_at)
            : asset('images/default-user.png');

        // ================= MOBILE =================
        $mobile = $user->mobile;

        // ================= DESIGNATION =================
        $designationName = $user->designation?->mas_designation_name;

        // ================= DEPARTMENT =================
        $departmentName = null;
   //     $departmentId   = (int) data_get($draft, 'data.department_id');
        $departmentId = (int) data_get($draft, 'data.basic.department_id');

//        dd($departmentId);
        if ($departmentId > 0) {
            $departmentName = Department::where('id', $departmentId)
                ->value('mas_department_name') ?? '-';
        }

        // ================= COLLEGE =================
        $collegeName = null;
        if ($user->user_stream_id) {
            $collegeName = College::where(
                'id',
                $user->user_college_id
            )->value('mas_college_name');
        }

        return view(
            'livewire.profile.my-details-table',
            compact(
                'user',
                'draft',
                'photo',
                'mobile',
                'designationName',
                'departmentName',
                'collegeName'
            )
        );
    }
}
