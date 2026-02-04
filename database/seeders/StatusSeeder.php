<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{

    public function run(): void
    {
        $statuses = [

            // ================= SYSTEM =================
            ['code' => 'ACTIVE', 'name' => 'Active', 'module' => 'System', 'order' => 1], ['code' => 'INACTIVE', 'name' => 'Inactive', 'module' => 'System', 'order' => 2], ['code' => 'DRAFT', 'name' => 'Draft', 'module' => 'System', 'order' => 3], ['code' => 'PROCESSING', 'name' => 'Processing', 'module' => 'System', 'order' => 4], ['code' => 'APPROVED', 'name' => 'Approved', 'module' => 'System', 'order' => 5], ['code' => 'REJECTED', 'name' => 'Rejected', 'module' => 'System', 'order' => 6], ['code' => 'ACCEPTED', 'name' => 'Accepted', 'module' => 'System', 'order' => 7], ['code' => 'APPROVAL_PENDING', 'name' => 'Approval Pending', 'module' => 'System', 'order' => 8], ['code' => 'ASSIGNED', 'name' => 'Assigned', 'module' => 'System', 'order' => 9], ['code' => 'UNASSIGNED', 'name' => 'Unassigned', 'module' => 'System', 'order' => 10], ['code' => 'DUPLICATE', 'name' => 'Duplicated Record', 'module' => 'System', 'order' => 11],

            // ================= PROFILE =================
            ['code' => 'UPLOAD_PHOTO', 'name' => 'Upload Profile Photo', 'module' => 'Profile', 'order' => 1], ['code' => 'UPDATE_BASIC', 'name' => 'Update Your Basic Details', 'module' => 'Profile', 'order' => 2], ['code' => 'PROFILE_COMPLETED', 'name' => 'Profile Completed', 'module' => 'Profile', 'order' => 3], ['code' => 'SUBMITTED_TO_PRINCIPAL', 'name' => 'Submitted for Verification by Principal', 'module' => 'Profile', 'order' => 4], ['code' => 'NEED_MORE_INFO', 'name' => 'Need More Information', 'module' => 'Profile', 'order' => 5], ['code' => 'CONTACT_RGUHS', 'name' => 'Contact RGUHS', 'module' => 'Profile', 'order' => 6],

            // ================= COLLEGE =================
            ['code' => 'PRINCIPAL_RECOMMENDED', 'name' => 'Principal Recommended', 'module' => 'College', 'order' => 1], ['code' => 'APPLICATION_REJECTED_COLLEGE', 'name' => 'Application Rejected by Principal', 'module' => 'College', 'order' => 2], ['code' => 'APPLICATION_APPROVED_COLLEGE', 'name' => 'Application Approved by College', 'module' => 'College', 'order' => 3], ['code' => 'DOCUMENT_ACCEPTED_COLLEGE', 'name' => 'Document Accepted by College', 'module' => 'College', 'order' => 4], ['code' => 'DOCUMENT_REJECTED_COLLEGE', 'name' => 'Document Rejected by College', 'module' => 'College', 'order' => 5],

            // ================= RGUHS =================
            ['code' => 'APPLICATION_APPROVED_RGUHS', 'name' => 'Application Approved by RGUHS', 'module' => 'RGUHS', 'order' => 1], ['code' => 'APPLICATION_REJECTED_RGUHS', 'name' => 'Application Rejected by RGUHS', 'module' => 'RGUHS', 'order' => 2], ['code' => 'DOCUMENT_ACCEPTED_RGUHS', 'name' => 'Document Accepted by RGUHS', 'module' => 'RGUHS', 'order' => 3], ['code' => 'DOCUMENT_REJECTED_RGUHS', 'name' => 'Document Rejected by RGUHS', 'module' => 'RGUHS', 'order' => 4], ['code' => 'PRINCIPAL_ROLE_APPROVED_RGUHS', 'name' => 'Principal Role Approved by RGUHS', 'module' => 'RGUHS', 'order' => 5], ['code' => 'PRINCIPAL_ROLE_REJECTED_RGUHS', 'name' => 'Principal Role Rejected by RGUHS', 'module' => 'RGUHS', 'order' => 6],

            // ================= EXAMINER =================
            ['code' => 'ASSIGN', 'name' => 'Assign', 'module' => 'Examiner', 'order' => 1], ['code' => 'REQUEST_CHANGE_EXAMINER', 'name' => 'Request Change in Examiner', 'module' => 'Examiner', 'order' => 2], ['code' => 'REQUESTED_FOR_CHANGE', 'name' => 'Requested for Change', 'module' => 'Examiner', 'order' => 3], ['code' => 'CHANGE_PENDING', 'name' => 'Change Request Approval Pending', 'module' => 'Examiner', 'order' => 4], ['code' => 'CHANGE_APPROVED', 'name' => 'Change Request Approved', 'module' => 'Examiner', 'order' => 5], ['code' => 'CHANGE_REJECTED', 'name' => 'Change Request Rejected', 'module' => 'Examiner', 'order' => 6], ['code' => 'EXAMINER_APPROVED', 'name' => 'Examiner Approved', 'module' => 'Examiner', 'order' => 7],

            // ================= EXAM =================
            ['code' => 'DUTY_COMPLETED', 'name' => 'Duty Completed', 'module' => 'Exam', 'order' => 1], ['code' => 'EXAM_COMPLETED', 'name' => 'Examination Completed', 'module' => 'Exam', 'order' => 2],

            // ================= COMMITTEE =================
            ['code' => 'RELEASED_TO_BOS', 'name' => 'Released to BOS', 'module' => 'Committee', 'order' => 1], ['code' => 'SUGGESTED_BY_BOS', 'name' => 'Suggested by BOS', 'module' => 'Committee', 'order' => 2], ['code' => 'ACCEPTED_BY_BOS', 'name' => 'Accepted by BOS', 'module' => 'Committee', 'order' => 3], ['code' => 'BOS_SUGGESTION_ACCEPTED', 'name' => 'BOS Suggestion Accepted', 'module' => 'Committee', 'order' => 4],

            ['code' => 'RELEASED_TO_DEAN', 'name' => 'Released to Dean', 'module' => 'Committee', 'order' => 5], ['code' => 'SUGGESTED_BY_DEAN', 'name' => 'Suggested by Dean', 'module' => 'Committee', 'order' => 6], ['code' => 'ACCEPTED_BY_DEAN', 'name' => 'Accepted by Dean', 'module' => 'Committee', 'order' => 7], ['code' => 'DEAN_SUGGESTION_ACCEPTED', 'name' => 'Dean Suggestion Accepted', 'module' => 'Committee', 'order' => 8],

            // ================= GOVERNANCE =================
            ['code' => 'ELIGIBLE_FOR_VOTING', 'name' => 'Eligible for Voting', 'module' => 'Governance', 'order' => 1],];

        foreach ($statuses as $s) {
            DB::table('mas_status')->updateOrInsert(['mas_status_code' => $s['code']], ['mas_status_name' => $s['name'], 'mas_status_module' => $s['module'], 'is_active' => 1, 'sort_order' => $s['order'], 'updated_at' => now(), 'created_at' => now(),]);
        }
    }
}
