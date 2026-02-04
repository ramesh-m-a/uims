<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SubjectDetailsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Same safety guard as MyDetails
        if (! $user->basic_details_id || ! $user->user_stream_id) {
            abort(403, 'Profile incomplete.');
        }

        return redirect()->route('subject-details-table.view');
    }

    public function view()
    {
        return view('subject_details.view');
    }
}
