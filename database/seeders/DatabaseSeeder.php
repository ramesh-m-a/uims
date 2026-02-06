<?php

namespace Database\Seeders;

use App\Services\Profile\ProfileDraftRebuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('==============================');
        $this->command->info('Starting full database seeding');
        $this->command->info('==============================');

        $this->call([
            /*
            |--------------------------------------------------------------------------
            | 1. SYSTEM / CORE
            |--------------------------------------------------------------------------
            */
            StatusSeeder::class,
            SalaryModeSeeder::class,
            AccountTypeSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 2. GEO MASTERS
            |--------------------------------------------------------------------------
            */
            StateSeeder::class,
            DistrictSeeder::class,
            TalukSeeder::class,
            CitySeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 3. ACADEMIC / HR MASTERS
            |--------------------------------------------------------------------------
            */
            YearSeeder::class,
            MonthSeeder::class,
            GenderSeeder::class,
            NationalitySeeder::class,
            ReligionSeeder::class,
            CategorySeeder::class,
            StreamSeeder::class,
            DegreeLevelSeeder::class,
            DegreeSeeder::class,
            SpecialisationSeeder::class,
            DegreeSpecialisationSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            AdminRoleSeeder::class,
            StreamDesignationSeeder::class,
            RevisedSchemeStreamSeeder::class,

          /*  DegreeGroupSeeder::class,
            SpecialisationGroupSeeder::class,
            DegreeGroupMapSeeder::class,
            SpecialisationGroupMapSeeder::class,
            DegreeGroupSpecialisationGroupSeeder::class,*/

            /*
            |--------------------------------------------------------------------------
            | 4. BANKING MASTERS (CRITICAL ORDER)
            |--------------------------------------------------------------------------
            */
            BankSeeder::class,
            BankBranchSeeder::class,
            IFSCodeSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 5. DOCUMENT MASTERS
            |--------------------------------------------------------------------------
            */
            DocumentSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 6. COLLEGE / ORGANISATION
            |--------------------------------------------------------------------------
            */
            CollegeSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 7. RBAC
            |--------------------------------------------------------------------------
            */
            RoleSeeder::class,
            PermissionSeeder::class,
            MasterPermissionsSeeder::class,
            RolePermissionSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 8. USERS
            |--------------------------------------------------------------------------
            */

            UserSeeder::class,
           // UsersFromCsvSeeder::class,
            UserRoleSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 9. EXAMINER MODULE
            |--------------------------------------------------------------------------
            */
            RevisedSchemeSeeder::class,
            SubjectSeeder::class,
            StudentPerBatchSeeder::class,
            ExaminerSchemeDistributionSeeder::class,
            ExaminerDetailsSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 10.  PROFILE CORE TABLES (FK SAFE ORDER)
            |--------------------------------------------------------------------------
            */
            BasicDetailsSeeder::class,
            AddressDetailsSeeder::class,
            QualificationDetailsSeeder::class,
            WorkDetailsSeeder::class,
            BankDetailsSeeder::class,
            DocumentDetailsSeeder::class,

            /*
            |--------------------------------------------------------------------------
            | 11. DEMO / DEV DATA (OPTIONAL)
            |--------------------------------------------------------------------------
            */

        ]);

        $this->command->info('==============================');
        $this->command->info('Database seeding completed');
        $this->command->info('==============================');

        // 👇 Post-seed maintenance jobs
        $this->command->info('Running post-seed sync jobs...');

      /*  Artisan::call('profile:cleanup-ifsc');
        Artisan::call('db:resequence-banks');
       // Artisan::call('sync:masters');
        Artisan::call('sync:departments');
        Artisan::call('sync:users-colleges');*/

        $this->command->info('Post-seed jobs completed.');

        $this->command->info('Running Profile Draft Rebuilder...');
        // Run AFTER everything seeded
        ProfileDraftRebuilder::rebuildForAll();
        $this->command->info('Profile Draft Rebuilder completed.');
    }
}
