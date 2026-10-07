<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        return view('pages.appointment');
    }
}
