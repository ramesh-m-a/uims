<?php

namespace App\Services\Profile;

use App\Models\Admin\User;
use App\Models\Master\Common\Status;
use App\Models\Teacher\AddressDetails;
use App\Models\Teacher\BankDetails;
use App\Models\Teacher\BasicDetails;
use App\Models\Teacher\DocumentDetails;
use App\Models\Teacher\QualificationDetails;
use App\Models\Teacher\WorkDetails;
use App\Models\UserProfileDraft;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;

class ProfileCommitService
{
    /**
     * Commit draft profile → submitted state
     */
    public function commit(User $user, ?UserProfileDraft $draft = null): void
    {
        $draft ??= UserProfileDraft::where('user_id', $user->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        DB::transaction(function () use ($user, $draft) {

            // =========================
            // LOAD DATA SAFELY
            // =========================
            $data  = $draft->data ?? [];
            $basic = $data['basic'] ?? [];

            // =========================
            // STATUS IDS
            // =========================
            $submittedStatusId = Status::where('mas_status_name', 'Submitted')->value('id');
            $draftStatusId     = Status::where('mas_status_name', 'Draft')->value('id');

            // =========================
            // 1️⃣ BASIC DETAILS
            // =========================
            $basicModel = BasicDetails::updateOrCreate(
                ['basic_details_user_id' => $user->id],
                [
                    'basic_details_dob'                   => $basic['dob'],
                    'basic_details_gender_id'            => $basic['gender_id'],
                    'basic_details_father_name'          => $basic['father_name'],
                    'basic_details_religion_id'          => $basic['religion_id'],
                    'basic_details_category_id'          => $basic['category_id'],
                    'basic_details_department_id'        => $basic['department_id'],
                    'basic_details_is_administrative_id' => $data['admin_role_id'] ?? 0,
                    'updated_by'                         => $user->id,
                ]
            );

            // =========================
            // 2️⃣ ADDRESS
            // =========================
            AddressDetails::updateOrCreate(
                ['address_details_basic_details_id' => $basicModel->id],
                [
                    'address_details_same_address' => data_get($data, 'address.same_address', 1),

                    // Permanent
                    'address_details_p_address_1' => data_get($data, 'address.permanent.address_1'),
                    'address_details_p_address_2' => data_get($data, 'address.permanent.address_2'),
                    'address_details_p_address_3' => data_get($data, 'address.permanent.address_3'),
                    'address_details_p_district'  => data_get($data, 'address.permanent.district'),
                    'address_details_p_state_id'  => data_get($data, 'address.permanent.state_id'),
                    'address_details_p_pincode'   => data_get($data, 'address.permanent.pincode'),

                    // Temporary
                    'address_details_t_address_1' => data_get($data, 'address.temporary.address_1'),
                    'address_details_t_address_2' => data_get($data, 'address.temporary.address_2'),
                    'address_details_t_address_3' => data_get($data, 'address.temporary.address_3'),
                    'address_details_t_district'  => data_get($data, 'address.temporary.district'),
                    'address_details_t_state_id'  => data_get($data, 'address.temporary.state_id'),
                    'address_details_t_pincode'   => data_get($data, 'address.temporary.pincode'),

                    'updated_by' => $user->id,
                ]
            );

            // =========================
            // 3️⃣ QUALIFICATIONS
            // =========================
            QualificationDetails::where(
                'qualification_details_basic_details_id',
                $basicModel->id
            )->delete();

            foreach (data_get($data, 'qualification', []) as $row) {
                QualificationDetails::create([
                    'qualification_details_basic_details_id' => $basicModel->id,
                    'qualification_details_stream_id'         => $row['stream_id'] ?? null,
                    'qualification_details_degree_id'         => $row['degree_id'] ?? null,
                    'qualification_details_specialisation_id' => $row['specialisation_id'] ?? null,
                    'qualification_details_university_name'   => $row['institution'] ?? null,
                    'qualification_details_year_of_award'     => $row['year_of_award'] ?? null,
                    'created_by'                               => $user->id,
                ]);
            }

            // =========================
            // 4️⃣ WORK EXPERIENCE
            // =========================
            WorkDetails::where(
                'work_details_basic_details_id',
                $basicModel->id
            )->delete();

            foreach (data_get($data, 'work', []) as $row) {
                WorkDetails::create([
                    'work_details_basic_details_id'       => $basicModel->id,
                    'work_details_work_designation_id'    => $row['designation_id'] ?? null,
                    'work_details_work_department_id'     => $row['department_id'] ?? null,
                    'work_details_last_institution_name'   => $row['institution_name'] ?? null,
                    'work_details_from_date'               => $row['from_date'] ?? null,
                    'work_details_to_date'                 => $row['to_date'] ?? null,
                    'work_details_till_date'               => $row['till_date'] ?? 0,
                    'created_by'                           => $user->id,
                ]);
            }

            // =========================
            // 5️⃣ BANK
            // =========================
            BankDetails::updateOrCreate(
                ['bank_details_basic_details_id' => $basicModel->id],
                [
                    'bank_details_account_number' => data_get($data, 'bank.account.account_number'),
                    'bank_details_account_name'   => data_get($data, 'bank.account.account_name'),
                    'bank_details_bank_id'        => data_get($data, 'bank.account.bank_id'),
                    'bank_details_ifs_code'        => data_get($data, 'bank.account.ifsc'),
                    'bank_details_salary_mode_id' => data_get($data, 'bank.salary.salary_mode_id'),
                    'updated_by'                  => $user->id,
                ]
            );

            // =========================
            // 6️⃣ DOCUMENTS
            // =========================
            DocumentDetails::where(
                'document_details_basic_details_id',
                $basicModel->id
            )->delete();

            foreach (data_get($data, 'documents', []) as $docId => $doc) {
                DocumentDetails::create([
                    'document_details_basic_details_id' => $basicModel->id,
                    'document_details_document_id'      => $docId,
                    'document_details_file_path'        => $doc['file_path'],
                    'created_by'                        => $user->id,
                ]);
            }

            // =========================
            // 7️⃣ FINALIZE DRAFT
            // =========================
            $draft->update([
                'basic_details_id' => $basicModel->id,
                'status_id'        => $submittedStatusId,
                'locked_by'        => $user->id,
                'locked_at'        => now(),
            ]);

            // =========================
            // 8️⃣ AUDIT LOG
            // =========================
            AuditLogger::log(
                table: 'user_profile_drafts',
                recordId: $draft->id,
                action: 'submit',
                oldValues: [
                    'status_id' => $draftStatusId,
                    'status'    => 'Draft',
                ],
                newValues: [
                    'status_id' => $submittedStatusId,
                    'status'    => 'Submitted',
                ]
            );
        });
    }
}
