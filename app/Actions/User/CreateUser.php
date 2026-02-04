<?php

namespace App\Actions\User;

use App\Mail\UserTemporaryPasswordMail;
use App\Models\Admin\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateUser
{
    /**
     * Create a new user (Admin or Public registration)
     *
     * @param  array  $data
     * @param  bool   $isAdmin
     * @return User
     */
    public function create(array $data, bool $isAdmin = false): User
    {
        /**
         * -------------------------------------------------
         * VALIDATION (SINGLE SOURCE OF TRUTH)
         * -------------------------------------------------
         */
        Validator::make($data, [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],

            'mobile' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'mobile'),
            ],

            'first_name'  => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],

            'user_stream_id'      => ['required', 'exists:mas_stream,id'],
            'user_college_id'     => ['required', 'exists:mas_college,id'],
            'user_designation_id' => ['required', 'exists:mas_designation,id'],

            'user_role_id' => ['nullable', 'exists:roles,id'],
            'user_status_id' => ['nullable', 'exists:mas_status,id'],

            // Password only required for public registration
            'password' => $isAdmin
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],
        ])->validate();

        /**
         * -------------------------------------------------
         * PASSWORD HANDLING
         * -------------------------------------------------
         */
        $plainPassword = $data['password'] ?? null;

        if ($isAdmin && empty($plainPassword)) {
            // Generate temporary password for admin-created users
            $plainPassword = Str::random(10);
        }

        /**
         * -------------------------------------------------
         * USER CREATE
         * -------------------------------------------------
         */
        $user = User::create([
            'name'                => trim(
                $data['first_name'] . ' ' .
                ($data['middle_name'] ?? '') . ' ' .
                $data['last_name']
            ),

            'email'               => $data['email'],
            'mobile'              => $data['mobile'],
            'password'            => Hash::make($plainPassword),

            'user_stream_id'      => $data['user_stream_id'],
            'user_college_id'     => $data['user_college_id'],
            'user_designation_id' => $data['user_designation_id'],

            'user_role_id'        => $data['user_role_id'] ?? null,
            'user_status_id'      => $data['user_status_id'] ?? 1, // Active by default
        ]);

        /**
         * -------------------------------------------------
         * RBAC ROLE MAPPING (OPTIONAL)
         * -------------------------------------------------
         */
        if (!empty($data['rbac_roles']) && is_array($data['rbac_roles'])) {
            $user->roles()->sync($data['rbac_roles']);
        }

        /**
         * -------------------------------------------------
         * EMAIL TEMP PASSWORD (ADMIN MODE ONLY)
         * -------------------------------------------------
         */
        if ($isAdmin) {
            Mail::to($user->email)->send(
                new UserTemporaryPasswordMail($user, $plainPassword)
            );
        }

        return $user;
    }
}
