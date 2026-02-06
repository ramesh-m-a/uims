<?php

namespace App\Services\Exam\College;

use Illuminate\Support\Facades\DB;

class CollegeExaminerRequestService
{
    public function createExaminerChangeRequest(array $data): void
    {
        DB::table('college_examiner_request_details')->insert([

            'college_examiner_request_details_year_id' => $data['year_id'],
            'college_examiner_request_details_month_id' => $data['month_id'],
            'college_examiner_request_details_batch_id' => $data['batch_id'],
            'college_examiner_request_details_batch_range_id' => $data['batch_range_id'],
            'college_examiner_request_details_revised_scheme_id' => $data['scheme_id'],

            'college_examiner_request_details_college_id' => $data['college_id'],
            'college_examiner_request_details_stream_id' => $data['stream_id'],

            'college_examiner_request_details_examiner_id' => $data['current_examiner_id'],
            'college_examiner_request_details_new_examiner_id' => $data['new_examiner_id'],

            'college_examiner_request_details_status_id' => 26, // Requested

            'created_by' => auth()->user()->name,
            'updated_by' => auth()->id(),

            'college_examiner_request_details_comments' => $data['comments'] ?? null,

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
