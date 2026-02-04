<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;

class MyDetailsIdCardController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        /* ==============================
         | BASIC DETAILS
         ============================== */
        $basic = DB::table('basic_details')
            ->where('basic_details_user_id', $user->id)
            ->first();

        if (! $basic) {
            return view('my-details.id-card', ['card_generated' => false]);
        }

        /* ==============================
         | AADHAAR CHECK (CRITICAL)
         ============================== */
        $hasAadhaar = DB::table('bank_details as bd')
            ->join(
                'basic_details as tbd',
                'tbd.id',
                '=',
                'bd.bank_details_basic_details_id'
            )
            ->where('tbd.basic_details_user_id', $user->id)
            ->exists();

        if (! $hasAadhaar) {
            return view('my-details.id-card', ['card_generated' => false]);
        }

        /* ==============================
         | FETCH FINAL DETAILS
         ============================== */
        $details = DB::table('basic_details as tbd')
            ->leftJoin('users as u', 'u.id', '=', 'tbd.basic_details_user_id')
            ->leftJoin('mas_designation as d', 'd.id', '=', 'u.user_designation_id')
            ->leftJoin('mas_department as dept', 'dept.id', '=', 'tbd.basic_details_department_id')
            ->leftJoin('mas_college as c', 'c.id', '=', 'u.user_college_id')
            ->leftJoin('mas_stream as s', 's.id', '=', 'u.user_stream_id')
            ->select(
                'u.name',
                'u.photo_path',
                'u.user_tin_new',
                'd.mas_designation_name',
                'dept.mas_department_name',
                'c.mas_college_name',
                's.mas_stream_name'
            )
            ->where('tbd.basic_details_user_id', $user->id)
            ->first();

        /* ==============================
         | QR CODE
         ============================== */
        $qrText =
            "College: {$details->mas_college_name}\n" .
            "TIN: {$details->user_tin_new}\n" .
            "Name: {$details->name}\n" .
            "Designation: {$details->mas_designation_name}\n" .
            "Stream: {$details->mas_stream_name}";

        $qr = Builder::create()
            ->writer(new SvgWriter())
            ->data($qrText)
            ->size(120)
            ->margin(4)
            ->build()
            ->getString();

        return view('my-details.id-card', [
            'card_generated' => true,
            'details' => $details,
            'qr' => $qr,
        ]);
    }
}
