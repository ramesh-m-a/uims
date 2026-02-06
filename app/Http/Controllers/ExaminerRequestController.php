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
        $status = $request->get('status', self::STATUS_PENDING);

        $userId   = auth()->id();
        $yearId   = session('allocation.yearId');
        $monthId  = session('allocation.monthId');
        $schemeId = session('allocation.schemeId');
        $degreeId = session('allocation.degreeId');

        /**
         * ======================================================
         * REQUEST BASE QUERY
         * ======================================================
         */

        $requestsRows = DB::table('college_examiner_request_details as cer')

            ->join(
                'temp_examiner_assigned_details as temp',
                function ($join) use ($userId, $yearId, $monthId, $schemeId, $degreeId) {

                    $join->on('temp.batch_id', '=', 'cer.college_examiner_request_details_batch_id')
                        ->on('temp.examiner_id', '=', 'cer.college_examiner_request_details_examiner_id')

                        ->where('temp.user_id', $userId)
                        ->where('temp.year_id', $yearId)
                        ->where('temp.month_id', $monthId)
                        ->where('temp.scheme_id', $schemeId)
                        ->where('temp.degree_id', $degreeId);
                }
            )

            ->join(
                'request_status_master as rsm',
                'rsm.id',
                '=',
                'cer.college_examiner_request_details_status_id'
            )

            ->leftJoin(
                'mas_status as ms',
                'ms.id',
                '=',
                'rsm.status_id'
            )

            ->leftJoin(
                'users as new_user',
                'new_user.id',
                '=',
                'cer.college_examiner_request_details_new_examiner_id'
            )

            ->where(
                'cer.college_examiner_request_details_status_id',
                $status
            )

            ->select([

                'cer.id as request_id',

                'temp.centre_name',
                'temp.batch_name',
                'temp.from_date',
                'temp.examiner_type',

                'temp.examiner_name as old_examiner_name',
                'temp.mobile as old_examiner_mobile',

                'new_user.name as new_examiner_name',
                'new_user.mobile as new_examiner_mobile',

                'cer.college_examiner_request_details_comments as reason',

                'rsm.label as status_label',

                DB::raw("
                    COALESCE(ms.mas_status_label_colour, 'bg-gray-400')
                    as status_colour
                "),
            ])

            ->orderBy('temp.centre_name')
            ->orderBy('temp.batch_name')
            ->orderBy('temp.from_date')

            ->get();

        return view('examiner.requests.index', [
            'requestsRows' => $requestsRows,
            'centre' => null
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
