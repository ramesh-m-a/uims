<?php

namespace App\Livewire\Profile;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IdCardControllerdelete  extends Component
{
    public function render()
    {
        if (Auth::user()->id == 1) {
            $university_name = 'Rajiv Gandhi University of Health Sciences, Karnataka';
            $name = "Name";
            $designation = "Designation";
            $college = "College Name";
            $profilePicture = "/123/1//16840/Mrs.%20AshikaD.JPG";
            $collegeLogo = asset('images/RGUHS-logo-AA.png');
            $stream = "Stream";
            $department = "Department";
            $teacherId = "1234";
            $electoralListId = "4599";
            //  $tin = "12345678900";
        } else {
            // Check if Aadhar (bank details) exists

            $get_user_id = DB::table('basic_details')->where('basic_details_user_id', Auth::user()->id)->first();

            $check_aadhar = DB::table('bank_details as bd')->join('basic_details as tbd', 'tbd.id', '=', 'bd.bank_details_basic_details_id') // join on ID
            ->where('tbd.basic_details_user_id', $get_user_id->basic_details_user_id) // ✅ use field
            ->count();

            if ($check_aadhar == 0) {
                $card_generated = 0;
                return view('livewire.profile.id-card-table', compact('card_generated'));
            } else {
                $card_generated = 1;
                $get_details = DB::table('basic_details as tbd')->leftJoin('mas_department as mdt', 'tbd.basic_details_department_id', '=', 'mdt.id')->leftJoin('users as usr', 'tbd.basic_details_user_id', '=', 'usr.id')->leftJoin('mas_designation as mds', 'usr.user_designation_id', '=', 'mds.id')->leftJoin('mas_college as mc', 'usr.user_college_id', '=', 'mc.id')->leftJoin('mas_stream as ms', 'usr.user_stream_id', '=', 'ms.id')->select('tbd.*', 'mds.mas_designation_name', 'mdt.mas_department_name', 'usr.name', 'mc.mas_college_name', 'photo_path', 'mas_stream_name', 'usr.user_tin')->where('tbd.basic_details_user_id', Auth::user()->id)->first();

                $university_name = 'Rajiv Gandhi University of Health Sciences, Karnatakaasasaasasaasa';
                $name = "{$get_details->name}";
                $designation = $get_details->mas_designation_name;
                $college = $get_details->mas_college_name;
                $profilePicture = $get_details->photo_path;
                $collegeLogo = asset('images/RGUHS-logo-AA.png');
                $stream = $get_details->mas_stream_name;
                $teacherId = $get_details->basic_details_user_id;
                $department = $get_details->mas_department_name;
                $tin = $get_details->user_tin;

                // Generate QR Code
                $text = "College: $college\nTIN: $tin\nName: $name\nDesignation: $designation\nStream: $stream";
                $renderer = new ImageRenderer(new RendererStyle(100, 4), new SvgImageBackEnd());
                $writer = new Writer($renderer);
                $qrCode = $writer->writeString($text);

                return view('livewire.profile.id-card-table', compact('name', 'designation', 'college', 'profilePicture', 'collegeLogo', 'university_name', 'stream', 'department', 'teacherId', 'tin', 'qrCode', 'card_generated'));
            }
        }
    }
}
