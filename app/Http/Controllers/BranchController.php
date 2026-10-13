<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SiteSetting;
use App\Support\Seo;
use App\Support\StructuredData;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function show(string $slug): View
    {
        $settings = SiteSetting::allCached();

        $branch = Branch::active()
            ->where('slug', $slug)
            ->with([
                'services' => fn ($q) => $q->where('is_active', true),
                'schedules' => fn ($q) => $q->orderBy('day_of_week', 'asc'),
            ])
            ->firstOrFail();

        $seoData = Seo::make($settings)->forBranch(
            $branch->name,
            $branch->slug,
            $branch->address
        );

        $structuredData = StructuredData::make($settings);
        $schemas = [
            $structuredData->localBusiness($branch),
            $structuredData->breadcrumbs($seoData->getBreadcrumbs()),
        ];

        return view('pages.branch.show', compact('branch', 'settings', 'seoData', 'schemas'));
    }
}
