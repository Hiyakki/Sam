{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($staticUrls as $url)
    <url>
        <loc>{{ $url }}</loc>
        <changefreq>weekly</changefreq>
        <priority>{{ $url === url('/') ? '1.0' : '0.8' }}</priority>
    </url>
@endforeach
@foreach ($products as $product)
    <url>
        <loc>{{ route('catalog.show', $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at?->toAtomString() ?? now()->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach
</urlset>
