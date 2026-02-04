<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class IdCardService
{
    /**
     * Get full ID card data for a user
     */
    public function getByUserId(int $userId): ?array
    {
        // Check if basic details exist
        $basic = DB::table('basic_details')
            ->where('basic_details_user_id', $userId)
            ->first();

        /*   if (! $basic) {
               return null;
           }*/

        // Check eligibility (bank details exists)
        $eligible = DB::table('bank_details as bd')
            ->join('basic_details as tbd', 'tbd.id', '=', 'bd.bank_details_basic_details_id')
            ->where('tbd.basic_details_user_id', $userId)
            ->exists();

        /*if (! $eligible) {
            return null;
        }*/

        // Fetch full joined data
        $data = DB::table('basic_details as tbd')
            ->leftJoin('mas_department as dept', 'tbd.basic_details_department_id', '=', 'dept.id')
            ->leftJoin('users as usr', 'tbd.basic_details_user_id', '=', 'usr.id')
            ->leftJoin('mas_designation as des', 'usr.user_designation_id', '=', 'des.id')
            ->leftJoin('mas_college as col', 'usr.user_college_id', '=', 'col.id')
            ->leftJoin('mas_stream as str', 'usr.user_stream_id', '=', 'str.id')
            ->select([
                'usr.id as user_id',
                'usr.name',
                'usr.photo_path',
                'usr.user_tin',
                'des.mas_designation_name as designation',
                'dept.mas_department_name as department',
                'col.mas_college_name as college',
                'str.mas_stream_name as stream',
                'tbd.basic_details_user_id as teacher_id',
            ])
            ->where('tbd.basic_details_user_id', $userId)
            ->first();
//dd($data);
        if (! $data) {
            return null;
        }

        return [
            'user_id'     => $data->user_id,
            'teacher_id'  => (string) $data->teacher_id,
            'name'        => $data->name ?? '',
            'designation'=> $data->designation ?? '',
            'department' => $data->department ?? '',
            'college'    => $data->college ?? '',
            'stream'     => $data->stream ?? '',
            'tin'        => $data->user_tin ?? '',
            'photo'      => $this->resolveProfileImage($data->photo_path),
        ];
    }

    /**
     * Resolve webp if exists, else fallback
     */

    protected function resolveProfileImage(?string $path): string
    {
        if (! $path) {
            return asset('images/default-avatar.png');
        }

        // Normalize safely
        $path = trim($path);
        $path = preg_replace('#/+#', '/', $path);
        $path = ltrim($path, '/');

        $baseDir  = dirname($path);
        $filename = pathinfo($path, PATHINFO_FILENAME); // without extension

        $diskDir = public_path('storage/' . $baseDir);

        if (! is_dir($diskDir)) {
            return asset('images/default-avatar.png');
        }

        // Find any matching file (jpg, jpeg, webp, png etc)
        $matches = glob($diskDir . '/' . $filename . '.*');

        if (empty($matches)) {
            return asset('images/default-avatar.png');
        }

        // Take the first real file found
        $realFile = basename($matches[0]);

        return asset('storage/' . $baseDir . '/' . rawurlencode($realFile));
    }
}
