<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>
@foreach ($ads as $ad)
    <url>
        <loc>{{ route('ads.show', $ad) }}</loc>
        <lastmod>{{ $ad->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
