<?php

namespace App\Actions\Fortify;

use App\Actions\User\CreateUser;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        // Minimal Fortify validation
        Validator::make($input, [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ])->validate();

        // Delegate to central service
        return app(CreateUser::class)->create(
            $input,
            false // PUBLIC REGISTRATION MODE
        );
    }
}
