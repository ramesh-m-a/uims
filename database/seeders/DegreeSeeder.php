<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\DegreeLevel;

class DegreeSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // Resolve levels dynamically
        // =========================
        $levels = DegreeLevel::pluck('id', 'mas_degree_level_code')->toArray();

        // =========================
        // Resolve streams dynamically (NO hardcoding IDs)
        // =========================
        $streams = DB::table('mas_stream')
            ->pluck('id', 'mas_stream_short_code')
            ->toArray();

        // Safety check (fail early if streams missing)
        if (empty($streams)) {
            throw new \RuntimeException('mas_stream table is empty. Run stream seeder first.');
        }

        // =========================
        // Degrees master data
        // =========================
        $degrees = [

            // MEDICAL (M)
            ['code'=>'MBBS','name'=>'Bachelor of Medicine and Bachelor of Surgery','level'=>'UG','stream'=>'M', 'mode'=>'0',],
            ['code'=>'MD','name'=>'Doctor of Medicine','level'=>'PG','stream'=>'M','mode'=>'2',],
            ['code'=>'MS','name'=>'Master of Surgery','level'=>'PG','stream'=>'M','mode'=>'2',],
            ['code'=>'DM','name'=>'Doctorate of Medicine','level'=>'SS','stream'=>'M','mode'=>'2',],
            ['code'=>'MCH','name'=>'Magister Chirurgiae','level'=>'SS','stream'=>'M','mode'=>'2',],
            ['code'=>'DNB','name'=>'Diplomate of National Board','level'=>'PG','stream'=>'M','mode'=>'2',],
            ['code'=>'DNBSS','name'=>'Diplomate of National Board (Super Speciality)','level'=>'SS','stream'=>'M','mode'=>'2',],
            ['code'=>'FNB','name'=>'Fellow of National Board','level'=>'SS','stream'=>'M','mode'=>'2',],

            // DENTAL (D)
            ['code'=>'BDS','name'=>'Bachelor of Dental Surgery','level'=>'UG','stream'=>'D','mode'=>'0',],
            ['code'=>'MDS','name'=>'Master of Dental Surgery','level'=>'PG','stream'=>'D','mode'=>'2',],

            // AYURVEDA (A)
            ['code'=>'BAMS','name'=>'Bachelor of Ayurvedic Medicine and Surgery','level'=>'UG','stream'=>'A','mode'=>'0',],
            ['code'=>'MDAY','name'=>'Doctor of Medicine (Ayurveda)','level'=>'PG','stream'=>'A','mode'=>'2',],
            ['code'=>'MSAY','name'=>'Master of Surgery (Ayurveda)','level'=>'PG','stream'=>'A','mode'=>'2',],

            // HOMEOPATHY (H)
            ['code'=>'BHMS','name'=>'Bachelor of Homeopathic Medicine and Surgery','level'=>'UG','stream'=>'H','mode'=>'0',],

            // UNANI (U)
            ['code'=>'BUMS','name'=>'Bachelor of Unani Medicine and Surgery','level'=>'UG','stream'=>'U','mode'=>'0',],

            // NATUROPATHY (O)
            ['code'=>'BNYS','name'=>'Bachelor of Naturopathy and Yogic Sciences','level'=>'UG','stream'=>'O','mode'=>'0',],

            // NURSING (N)
            ['code'=>'BSCN','name'=>'Bachelor of Science in Nursing','level'=>'UG','stream'=>'N','mode'=>'0',],
            ['code'=>'PBSCN','name'=>'Post Basic Bachelor of Science in Nursing','level'=>'UG','stream'=>'N','mode'=>'2',],
            ['code'=>'MSCNU','name'=>'Master of Science in Nursing','level'=>'PG','stream'=>'N','mode'=>'2',],

            // PHYSIOTHERAPY (T)
            ['code'=>'BPT','name'=>'Bachelor of Physiotherapy','level'=>'UG','stream'=>'T','mode'=>'0',],
            ['code'=>'MPT','name'=>'Master of Physiotherapy','level'=>'PG','stream'=>'T','mode'=>'2',],
            ['code'=>'BOT','name'=>'Bachelor of Occupational Therapy','level'=>'UG','stream'=>'T','mode'=>'2',],
            ['code'=>'MOT','name'=>'Master of Occupational Therapy','level'=>'PG','stream'=>'T','mode'=>'2',],

            // PHARMACY (P)
            ['code'=>'BPHARM','name'=>'Bachelor of Pharmacy','level'=>'UG','stream'=>'P','mode'=>'2',],
            ['code'=>'MPHARM','name'=>'Master of Pharmacy','level'=>'PG','stream'=>'P','mode'=>'2',],
            ['code'=>'PHARMD','name'=>'Doctor of Pharmacy','level'=>'UG','stream'=>'P','mode'=>'2',],
            ['code'=>'PHARMDPB','name'=>'Post Baccalaureate Doctor of Pharmacy','level'=>'PG','stream'=>'P','mode'=>'2',],

            // ALLIED (L)
            ['code'=>'BSCAL','name'=>'Bachelor of Science (Allied Health Sciences)','level'=>'UG','stream'=>'L','mode'=>'2',],
            ['code'=>'MSCAL','name'=>'Master of Science (Allied Health Sciences)','level'=>'PG','stream'=>'L','mode'=>'2',],
            ['code'=>'MSCLT','name'=>'Master of Science in Laboratory Technology','level'=>'PG','stream'=>'L','mode'=>'2',],

            // FELLOWSHIP (F)
            ['code'=>'FELLOW','name'=>'Fellowship','level'=>'SS','stream'=>'F','mode'=>'2',],

           /* // OTHERS (X)
            ['code'=>'PHD','name'=>'Doctor of Philosophy','level'=>'SS','stream'=>'X'],
            ['code'=>'MPHIL','name'=>'Master of Philosophy','level'=>'SS','stream'=>'X'],
            ['code'=>'DIP','name'=>'Diploma','level'=>'UG','stream'=>'X'],*/
        ];

        // =========================
        // Insert safely
        // =========================
        foreach ($degrees as $d) {

            if (! isset($streams[$d['stream']])) {
                throw new \RuntimeException("Missing stream short code in mas_stream: {$d['stream']}");
            }

            $degree = Degree::updateOrCreate(
                ['mas_degree_code' => $d['code']],
                [
                    'mas_degree_stream_id' => $streams[$d['stream']],
                    'mas_degree_name'      => $d['name'],
                    'mas_degree_level_id'  => $levels[$d['level']],
                    'mas_degree_status_id' => 1,
                    'mas_degree_specialisation_mode' => $d['mode'],
                ]
            );

            DB::table('mas_degree_stream')->updateOrInsert(
                [
                    'mas_degree_id' => $degree->id,
                    'mas_stream_id' => $streams[$d['stream']],
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
