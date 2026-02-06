<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ExaminerRequestApprovalService;
use App\Services\ExaminerRequestRejectService;

class ExaminerRequestController extends Controller
{
    private const STATUS_PENDING = 26;
    private const STATUS_APPROVED = 27;
    private const STATUS_REJECTED = 28;

    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = DB::table('college_examiner_request_details');

        if ($status) {
            $query->where(
                'college_examiner_request_details_status_id',
                $status
            );
        }

        $requests = $query
            ->orderByDesc('id')
            ->paginate(20);

        return view('examiner.requests.index', [
            'requests' => $requests,
            'status' => $status
        ]);
    }

    public function approve($id, ExaminerRequestApprovalService $service)
    {
        $service->approve($id, auth()->id());

        return back()->with('success', 'Approved Successfully');
    }

    public function reject($id, ExaminerRequestRejectService $service)
    {
        $service->reject($id, auth()->id());

        return back()->with('success', 'Rejected Successfully');
    }


}
