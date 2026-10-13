{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($sitemaps as $sitemap)
    <sitemap>
        <loc>{{ $sitemap }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
    </sitemap>
@endforeach
</sitemapindex>
