<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $assigned = 0;
        $skipped  = 0;
        $errors   = 0;

        $email = 'admin@uims.com';
        $roleName = 'super-admin';

        try {
            $user = User::where('email', $email)->first();
            if (! $user) {
                echo "⚠️  User not found: {$email} (skipped)\n";
                $skipped++;
            }

            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                echo "⚠️  Role not found: {$roleName} (skipped)\n";
                $skipped++;
            }

            if ($user && $role) {
                $user->roles()->syncWithoutDetaching([$role->id]);

                if (method_exists($user, 'flushPermissionCache')) {
                    $user->flushPermissionCache();
                }

                $assigned++;
                echo "✅ Role '{$roleName}' assigned to {$email}\n";
            }

        } catch (\Throwable $e) {
            $errors++;
            echo "❌ Error: {$e->getMessage()}\n";
        }

        echo "\nDONE ✅\n";
        echo "Assigned: {$assigned}\n";
        echo "Skipped: {$skipped}\n";
        echo "Errors: {$errors}\n";
    }
}
