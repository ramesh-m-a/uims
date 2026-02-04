<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Config\Academic\Degree;
use Illuminate\Support\Facades\DB;

class DegreeGroupMapSeeder extends Seeder
{
    public function run(): void
    {
        $groups = DB::table('mas_degree_group')->pluck('id', 'mas_degree_group_code');

        $map = [
            'MBBS'   => 'MED_UG',
            'MD'     => 'MED_PG',
            'MS'     => 'MED_PG',
            'DM'     => 'MED_SS',
            'MCH'    => 'MED_SS',
            'DNB'    => 'MED_PG',
            'DNBSS'  => 'MED_SS',
            'FNB'    => 'MED_SS',

            'MDS'    => 'DENTAL_PG',
            'MPT'    => 'PHYSIO_PG',
            'MPHARM' => 'PHARMA_PG',

            'MSCAL'  => 'ALLIED_PG',
            'MSCLT'  => 'ALLIED_PG',
        ];

        foreach ($map as $degreeCode => $groupCode) {
            $degree = Degree::where('mas_degree_code', $degreeCode)->first();
            if (! $degree) continue;

            DB::table('mas_degree_group_map')->updateOrInsert([
                'degree_id' => $degree->id,
                'degree_group_id' => $groups[$groupCode],
            ]);
        }
    }
}
