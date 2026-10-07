<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SiteSetting;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::allCached();

        $branches = Branch::active()
            ->with([
                'schedules' => fn ($q) => $q->orderBy('day_of_week'),
                'services',
            ])
            ->get();

        return view('pages.about', compact('settings', 'branches'));
    }
}
