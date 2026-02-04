<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\Specialisation;
use Illuminate\Support\Facades\DB;

class DegreeSpecialisationSeeder extends Seeder
{
    public function run(): void
    {
        $degrees = Degree::with(['level'])->get();

        $specialisations = Specialisation::where('mas_specialisation_status_id', 1)->get();

        foreach ($degrees as $degree) {

            $degreeCode  = $degree->mas_degree_code;
            $level       = $degree->level?->mas_degree_level_code; // UG / PG / SS
            $mode        = $degree->mas_degree_specialisation_mode;

            $eligibleSpecs = collect();

            // ============================
            // RULE ENGINE
            // ============================

            // UG degrees → only GEN
            if ($mode == 0) {
                $eligibleSpecs = $specialisations->where('mas_specialisation_code', 'GEN');
            }

            // PG / SS degrees
            if ($mode == 2) {

                // Medical stream
                if (in_array($degreeCode, ['MD', 'DNB'])) {
                    $eligibleSpecs = $specialisations->filter(fn ($s) =>
                    !in_array($s->mas_specialisation_code, [
                        'ORAL','PROS','PERI','ORTHO','PEDO','ENDO_D','OMFS', // dental
                        'ORTHP','NEURP','CARDP','SPORT', // physio
                        'PHCL','PHCO','PHRM','PHAN','PHQA', // pharma
                    ])
                    );
                }

                // Surgical
                if (in_array($degreeCode, ['MS'])) {
                    $eligibleSpecs = $specialisations->whereIn('mas_specialisation_code', [
                        'SURG','ORTH','ENT','OPHT','OBGY','ANES','OMFS'
                    ]);
                }

                // Super speciality
                if (in_array($degreeCode, ['DM', 'MCH', 'DNBSS', 'FNB'])) {
                    $eligibleSpecs = $specialisations->whereIn('mas_specialisation_code', [
                        'CARD','NEUR','NEPH','GAST','ENDO','ONCO','HEMA'
                    ]);
                }

                // Dental
                if (in_array($degreeCode, ['MDS'])) {
                    $eligibleSpecs = $specialisations->whereIn('mas_specialisation_code', [
                        'ORAL','PROS','PERI','ORTHO','PEDO','ENDO_D','OMFS'
                    ]);
                }

                // Physiotherapy
                if (in_array($degreeCode, ['MPT'])) {
                    $eligibleSpecs = $specialisations->whereIn('mas_specialisation_code', [
                        'ORTHP','NEURP','CARDP','SPORT'
                    ]);
                }

                // Pharmacy
                if (in_array($degreeCode, ['MPHARM'])) {
                    $eligibleSpecs = $specialisations->whereIn('mas_specialisation_code', [
                        'PHCL','PHCO','PHRM','PHAN','PHQA'
                    ]);
                }

                // Allied
                if (in_array($degreeCode, ['MSCAL','MSCLT'])) {
                    $eligibleSpecs = $specialisations->whereIn('mas_specialisation_code', [
                        'MLT','RDT','OT','DIAL','EMER'
                    ]);
                }
            }

            // ============================
            // Persist pivot
            // ============================
            foreach ($eligibleSpecs as $spec) {
                DB::table('mas_degree_specialisation')->updateOrInsert([
                    'degree_id'         => $degree->id,
                    'specialisation_id' => $spec->id,
                ]);
            }

            $this->command?->info("Mapped {$degreeCode} → " . $eligibleSpecs->count() . " specs");
        }
    }
}
