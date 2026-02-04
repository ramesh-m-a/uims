<?php

namespace App\Http\Controllers;

class ExaminerController extends Controller
{
    public function index()
    {
        $userStreamCode = auth()->user()->user_stream_id;

        return view('exam.index', compact('userStreamCode'));
    }
}
