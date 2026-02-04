<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    protected $table = 'bank_details';

    protected $fillable = [
        'bank_details_basic_details_id',
        'bank_details_pan_number',
        'bank_details_pan_name',
    //    'bank_details_epf_number',
        'bank_details_account_type_id',
        'bank_details_account_number',
        'bank_details_account_name',
        'bank_details_bank_id',
        'bank_details_branch_id',
        'bank_details_ifscode',
     //   'bank_details_basic_pay',
      //  'bank_details_gross_pay',
        'bank_details_salary_mode_id',
        'bank_details_aadhar_number',
     //   'bank_details_pay_scale',
    ];

    public $timestamps = true;
}
