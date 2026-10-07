<?php

namespace App\Http\Controllers;

use App\Services\HomePageData;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman Beranda (Home) dengan data terpusat.
     */
    public function index(): View
    {
        return view('pages.home', HomePageData::get());
    }
}
