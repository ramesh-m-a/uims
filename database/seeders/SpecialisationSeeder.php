<?php

namespace Database\Seeders;

use App\Models\Master\Config\Academic\Specialisation;
use Illuminate\Database\Seeder;

// database/seeders/DegreeLevelSeeder.php

class SpecialisationSeeder extends Seeder
{
    public function run(): void
    {
        $specialisations = [

            // ============ CORE MEDICAL ============
            ['code' => 'CARD', 'name' => 'Cardiology'], ['code' => 'DERM', 'name' => 'Dermatology'], ['code' => 'BIOC', 'name' => 'Biochemistry'], ['code' => 'ALLS', 'name' => 'Allied Sciences'], ['code' => 'ANAT', 'name' => 'Anatomy'], ['code' => 'PHYS', 'name' => 'Physiology'], ['code' => 'PHAR', 'name' => 'Pharmacology'], ['code' => 'PATH', 'name' => 'Pathology'], ['code' => 'MICR', 'name' => 'Microbiology'], ['code' => 'FMT', 'name' => 'Forensic Medicine'], ['code' => 'PSYC', 'name' => 'Psychiatry'], ['code' => 'PEDI', 'name' => 'Paediatrics'], ['code' => 'ORTH', 'name' => 'Orthopaedics'], ['code' => 'ENT', 'name' => 'ENT'], ['code' => 'OPHT', 'name' => 'Ophthalmology'], ['code' => 'ANES', 'name' => 'Anaesthesiology'], ['code' => 'OBGY', 'name' => 'Obstetrics and Gynaecology'], ['code' => 'MEDI', 'name' => 'General Medicine'], ['code' => 'SURG', 'name' => 'General Surgery'], ['code' => 'RADI', 'name' => 'Radiology'], ['code' => 'ONCO', 'name' => 'Oncology'], ['code' => 'NEPH', 'name' => 'Nephrology'], ['code' => 'NEUR', 'name' => 'Neurology'], ['code' => 'GAST', 'name' => 'Gastroenterology'], ['code' => 'ENDO', 'name' => 'Endocrinology'], ['code' => 'PULM', 'name' => 'Pulmonology'], ['code' => 'RHEU', 'name' => 'Rheumatology'], ['code' => 'HEMA', 'name' => 'Haematology'], ['code' => 'IMM', 'name' => 'Immunology'],

            // ============ DENTAL ============
            ['code' => 'ORAL', 'name' => 'Oral Medicine'], ['code' => 'PROS', 'name' => 'Prosthodontics'], ['code' => 'PERI', 'name' => 'Periodontics'], ['code' => 'ORTHO', 'name' => 'Orthodontics'], ['code' => 'PEDO', 'name' => 'Paedodontics'], ['code' => 'ENDO_D', 'name' => 'Endodontics'], ['code' => 'OMFS', 'name' => 'Oral and Maxillofacial Surgery'],

            // ============ AYUSH ============
            ['code' => 'KAYA', 'name' => 'Kayachikitsa'], ['code' => 'SHAL', 'name' => 'Shalya Tantra'], ['code' => 'SHLK', 'name' => 'Shalakya Tantra'], ['code' => 'PRAS', 'name' => 'Prasuti Tantra'], ['code' => 'PANC', 'name' => 'Panchakarma'], ['code' => 'DRAV', 'name' => 'Dravyaguna'],

            // ============ ALLIED / LAB ============
            ['code' => 'MLT', 'name' => 'Medical Laboratory Technology'], ['code' => 'RDT', 'name' => 'Radiology Technology'], ['code' => 'OT', 'name' => 'Operation Theatre Technology'], ['code' => 'DIAL', 'name' => 'Dialysis Technology'], ['code' => 'EMER', 'name' => 'Emergency Care Technology'],

            // ============ PHYSIO ============
            ['code' => 'ORTHP', 'name' => 'Orthopaedic Physiotherapy'], ['code' => 'NEURP', 'name' => 'Neurological Physiotherapy'], ['code' => 'CARDP', 'name' => 'Cardio Pulmonary Physiotherapy'], ['code' => 'SPORT', 'name' => 'Sports Physiotherapy'],

            // ============ PHARMACY ============
            ['code' => 'PHCL', 'name' => 'Pharmaceutical Chemistry'], ['code' => 'PHCO', 'name' => 'Pharmacology'], ['code' => 'PHRM', 'name' => 'Pharmaceutics'], ['code' => 'PHAN', 'name' => 'Pharmacognosy'], ['code' => 'PHQA', 'name' => 'Pharmaceutical Analysis'],

            // ============ GENERIC ============
            ['code' => 'GEN', 'name' => 'General'], ['code' => 'RES', 'name' => 'Research'], ['code' => 'CLIN', 'name' => 'Clinical'],];

        foreach ($specialisations as $s) {
            Specialisation::updateOrCreate(['mas_specialisation_code' => $s['code']], ['mas_specialisation_name' => $s['name'], 'mas_specialisation_status_id' => 1,]);
        }
    }
}
