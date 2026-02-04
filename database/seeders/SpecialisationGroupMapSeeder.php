<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Config\Academic\Specialisation;
use Illuminate\Support\Facades\DB;

class SpecialisationGroupMapSeeder extends Seeder
{
    public function run(): void
    {
        $groups = DB::table('mas_specialisation_group')->pluck('id', 'mas_specialisation_group_code');

        $rules = [
            'MED_CORE'  => ['ANAT','PHYS','PATH','PHAR','BIOC','MICR','FMT','PSYC','PEDI','MEDI','SURG','ORTH','ENT','OPHT','OBGY','ANES','RADI'],
            'MED_SUPER' => ['CARD','NEUR','NEPH','GAST','ENDO','ONCO','HEMA'],
            'DENTAL'    => ['ORAL','PROS','PERI','ORTHO','PEDO','ENDO_D','OMFS'],
            'PHYSIO'    => ['ORTHP','NEURP','CARDP','SPORT'],
            'PHARMA'    => ['PHCL','PHCO','PHRM','PHAN','PHQA'],
            'ALLIED'    => ['MLT','RDT','OT','DIAL','EMER'],
            'GENERIC'   => ['GEN','CLIN','RES'],
        ];

        foreach ($rules as $groupCode => $specCodes) {
            foreach ($specCodes as $code) {
                $spec = Specialisation::where('mas_specialisation_code', $code)->first();
                if (! $spec) continue;

                DB::table('mas_specialisation_group_map')->updateOrInsert([
                    'specialisation_id' => $spec->id,
                    'specialisation_group_id' => $groups[$groupCode],
                ]);
            }
        }
    }
}
