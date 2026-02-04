<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPermissionsSeeder extends Seeder
{
    /**
     * All masters that follow MasterTableBase
     */
    protected array $masters = [
        // Common
        'master.common.gender',
        'master.common.religion',
        'master.common.nationality',
        'master.common.category',
        'master.common.document',
        'master.common.bank',
        'master.common.bank-branch',
        'master.common.ifsc',
        'master.common.state',
        'master.common.district',
        'master.common.city',
        'master.common.taluk',
        'master.common.status',

        // Config / Academic
        'master.config.academic.stream',
        'master.config.academic.college',
        'master.config.academic.degree',
        'master.config.academic.department',
        'master.config.academic.designation',
        'master.config.academic.degree-stream',
    ];

    protected array $actions = [
        'view',
        'create',
        'edit',
        'delete',
        'restore',
    ];

    public function run(): void
    {
        $count = 0;

        foreach ($this->masters as $prefix) {
            foreach ($this->actions as $action) {

                $permission = "{$prefix}.{$action}";

                DB::table('permissions')->updateOrInsert(
                    ['name' => $permission],
                    [
                        'module'     => $prefix,
                        'action'     => $action,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $count++;

                if ($count % 100 === 0) {
                    echo "Processed {$count} permissions...\n";
                }
            }
        }

        echo "\nDONE ✅\n";
        echo "Permissions Inserted/Updated: {$count}\n";
    }
}
