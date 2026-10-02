<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        return view('pages.catalog.index');
    }

    public function show(string $slug): View
    {
        return view('pages.catalog.show', compact('slug'));
    }
}
