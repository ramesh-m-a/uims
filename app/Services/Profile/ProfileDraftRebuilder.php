<?php

namespace App\Services\Profile;

use App\Models\Admin\User;
use App\Models\Teacher\BasicDetails;
use App\Models\Teacher\AddressDetails;
use App\Models\Teacher\QualificationDetails;
use App\Models\Teacher\WorkDetails;
use App\Models\Teacher\BankDetails;
use App\Models\Teacher\DocumentDetails;
use App\Models\UserProfileDraft;
use App\Models\Master\Config\Academic\Designation;

class ProfileDraftRebuilder
{
    public static function rebuildForUser(int $userId): void
    {
        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $basic = BasicDetails::where('basic_details_user_id', $userId)->first();

        if (! $basic) {
            return;
        }

        $address = AddressDetails::where('address_details_basic_details_id', $basic->id)->first();
        $bank    = BankDetails::where('bank_details_basic_details_id', $basic->id)->first();

        $qualifications = QualificationDetails::where('qualification_details_basic_details_id', $basic->id)->get();
        $work           = WorkDetails::where('work_details_basic_details_id', $basic->id)->get();
        $documents      = DocumentDetails::where('document_details_basic_details_id', $basic->id)->get();

        // ✅ Resolve designation safely
        $designationId   = $user->user_designation_id;
        $designationName = Designation::where('id', $designationId)
            ->value('mas_designation_name');

        $data = [
            'basic' => [
                'gender_id'   => $basic->basic_details_gender_id,
                'dob'         => $basic->basic_details_dob,
                'father_name' => $basic->basic_details_father_name,
                'religion_id' => $basic->basic_details_religion_id,
                'category_id' => $basic->basic_details_category_id,
                'department_id' => $basic->basic_details_department_id,
                'administrative_id' => $basic->basic_details_is_administrative_id,

                // ✅ FIXED: persist designation properly
                'designation_id'   => $designationId,
                'designation_name' => $designationName ?? '-',
            ],

            'address' => [
                'same_address' => $address?->address_details_same_address ?? true,
                'permanent' => [
                    'address_1' => $address?->address_details_p_address_1,
                    'address_2' => $address?->address_details_p_address_2,
                    'address_3' => $address?->address_details_p_address_3,
                    'district'  => $address?->address_details_p_district,
                    'state_id'  => $address?->address_details_p_state_id,
                    'pincode'   => $address?->address_details_p_pincode,
                ],
                'temporary' => [
                    'address_1' => $address?->address_details_t_address_1,
                    'address_2' => $address?->address_details_t_address_2,
                    'address_3' => $address?->address_details_t_address_3,
                    'district'  => $address?->address_details_t_district,
                    'state_id'  => $address?->address_details_t_state_id,
                    'pincode'   => $address?->address_details_t_pincode,
                ],
            ],

            'qualification' => $qualifications->map(fn ($q) => [
                'stream_id'         => $q->qualification_details_stream_id,
                'degree_id'         => $q->qualification_details_degree_id,
                'specialisation_id' => $q->qualification_details_specialisation_id,
                'institution'       => $q->qualification_details_university_name,
                'year_of_award'     => $q->qualification_details_year_of_award,
                'qualification_details_year_of_exam' => $q->qualification_details_year_of_exam,
            ])->values()->all(),

            'work' => $work->map(fn ($w) => [
                'designation_id'  => $w->work_details_work_designation_id,
                'department_id'   => $w->work_details_work_department_id,
                'institution_name'=> $w->work_details_last_institution_name,
                'from_date'        => $w->work_details_from_date,
                'to_date'          => $w->work_details_to_date,
                'till_date'        => $w->work_details_till_date,
                'work_details_date_of_appointment' => $w->work_details_date_of_appointment,
                'work_details_date_of_joining' => $w->work_details_date_of_joining,
                'qualification_details_state_registration_number' => $w->qualification_details_state_registration_number,
                'qualification_details_registration_date' => $w->qualification_details_registration_date,
                'qualification_details_specialisation_id' => $w->qualification_details_specialisation_id,
            ])->values()->all(),

            'bank' => [
                'identity' => [
                    'pan_number'     => $bank?->bank_details_pan_number,
                    'pan_name'       => $bank?->bank_details_pan_name,
                    'aadhar_number'  => $bank?->bank_details_aadhar_number,
                    'epf_number'     => $bank?->bank_details_epf_number,
                ],
                'salary' => [
                    'basic_pay'      => $bank?->bank_details_basic_pay,
                    'salary_mode_id' => $bank?->bank_details_salary_mode_id,
                ],
                'account' => [
                    'account_type_id' => $bank?->bank_details_account_type_id,
                    'account_number'  => $bank?->bank_details_account_number,
                    'account_name'    => $bank?->bank_details_account_name,
                    'ifs_code'        => $bank?->bank_details_ifs_code,
                    'bank_id'         => $bank?->bank_details_bank_id,
                    'branch_id'       => $bank?->bank_details_branch_id,
                ],
            ],

            'documents' => $documents->map(fn ($d) => [
                'document_id' => $d->document_details_document_id,
                'file_path'   => $d->document_details_file_path,
            ])->values()->all(),
        ];

        UserProfileDraft::updateOrCreate(
            ['user_id' => $userId],
            [
                'basic_details_id' => $basic->id,
                'current_tab'      => 'basic',
                'completed_tabs'   => ['basic','address','qualification','work','bank','documents'],
                'status_id'        => 'draft',
                'data'             => $data,
            ]
        );
    }

    public static function rebuildForAll(): void
    {
        BasicDetails::whereNotNull('basic_details_user_id')
            ->pluck('basic_details_user_id')
            ->unique()
            ->filter(fn ($id) => is_numeric($id))
            ->each(fn ($id) => self::rebuildForUser((int) $id));
    }
}
