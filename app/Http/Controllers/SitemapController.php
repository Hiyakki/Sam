<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $staticUrls = [
            route('home'),
            route('about'),
            route('catalog.index'),
            route('faq'),
            route('appointment'),
            route('privacy'),
        ];

        $products = Product::active()->select(['slug', 'updated_at'])->get();

        $content = view('seo.sitemap', compact('staticUrls', 'products'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = <<<TXT
User-agent: *
Allow: /
Disallow: /kelola-sam/

Sitemap: {$sitemapUrl}
TXT;

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
