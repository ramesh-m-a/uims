<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Model;

class AddressDetails extends Model
{
    protected $table = 'address_details';

    protected $fillable = [
        'address_details_basic_details_id',
        'address_details_same_address',

        'address_details_p_address_1',
        'address_details_p_address_2',
   //     'address_details_p_address_3',
   //     'address_details_p_district',
   //     'address_details_p_state_id',
    //    'address_details_p_pincode',

        'address_details_t_address_1',
        'address_details_t_address_2',
   //     'address_details_t_address_3',
   //     'address_details_t_district',
   //     'address_details_t_state_id',
   //     'address_details_t_pincode',
    ];

    public $timestamps = true;
}
