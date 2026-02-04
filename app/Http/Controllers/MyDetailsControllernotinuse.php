<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MyDetailsControllernotinuse extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // User registered but no basic details yet → force wizard
        if ($user->user_is_registered == 1 && ! $user->basicDetails) {
            return redirect()->route('my_details.editr');
        }

        // Otherwise show view page
        return redirect()->route('my_details.view');
    }

    public function view()
    {
        return view('my_details.view');
    }
}
