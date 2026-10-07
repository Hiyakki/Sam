<?php

namespace App\Http\Controllers;

use App\Services\AboutPageData;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Tampilkan halaman Tentang Kami (About) dengan data terpusat.
     */
    public function index(): View
    {
        return view('pages.about', AboutPageData::get());
    }
}
