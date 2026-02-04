<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\AuditLog;

class ProfileAuditController extends Controller
{
    public function show(User $user)
    {
        $logs = AuditLog::where('table_name', 'users')
            ->where('record_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('audit.profile-timeline', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }
}
