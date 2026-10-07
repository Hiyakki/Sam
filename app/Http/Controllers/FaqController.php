<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\SiteSetting;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::allCached();

        $faqs = Faq::active()->get();

        $groupedFaqs = $faqs->groupBy(function ($faq) {
            return filled($faq->category) ? $faq->category : 'Umum';
        });

        return view('pages.faq', compact('groupedFaqs', 'faqs', 'settings'));
    }
}
