<?php

namespace Database\Seeders;

use App\Models\Master\Config\Academic\Stream;
use Illuminate\Database\Seeder;

class StreamSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'ALLIED HEALTH SCIENCES',     'code' => 'L', 's-name' => 'ALLIED'],
            ['name' => 'DENTAL',  'code' => 'D', 's-name' => 'DENTAL'],
            ['name' => 'NATUROPATHY AND YOGA', 'code' => 'O', 's-name' => 'NATURO'],
            ['name' => 'FELLOWSHIP', 'code' => 'F', 's-name' => 'FELLOW'],
            ['name' => 'AYURVEDA', 'code' => 'A',   's-name' => 'AYUR'],
            ['name' => 'MEDICAL', 'code' => 'M',   's-name' => 'MED'],
            ['name' => 'NURSING', 'code' => 'N',   's-name' => 'NURSING'],
            ['name' => 'PHARMACY', 'code' => 'P',   's-name' => 'PHARMA'],
            ['name' => 'PHYSIOTHERAPY', 'code' => 'T',   's-name' => 'PHYSIO'],
            ['name' => 'HOMEOPATHY', 'code' => 'H',   's-name' => 'HOMEO'],
            ['name' => 'UNANI', 'code' => 'U',   's-name' => 'UNANI'],
            ['name' => 'SIDDA', 'code' => 'S',   's-name' => 'SIDDA'],
        ];

        foreach ($data as $row) {
            Stream::updateOrCreate(
                ['mas_stream_name' => $row['name']],
                [
                    'mas_stream_short_code' => $row['code'],
                    'mas_stream_short_name' => $row['s-name'],
                    'mas_stream_status_id'  => 1,
                    'created_by'            => null,
                    'updated_by'            => null,
                ]
            );
        }
    }
}
