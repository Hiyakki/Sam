<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function privacy(): View
    {
        $settings = SiteSetting::allCached();

        return view('pages.privacy', compact('settings'));
    }
}
