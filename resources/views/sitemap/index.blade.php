{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Homepage --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Search Page --}}
    <url>
        <loc>{{ url('/search') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- All Hadiths --}}
    @foreach($hadiths as $hadith)
    <url>
        <loc>{{ url('/hadith/' . $hadith->id) }}</loc>
        <lastmod>{{ $hadith->updated_at ? $hadith->updated_at->toDateString() : now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    @endforeach

    {{-- All Books --}}
    @foreach($books as $book)
    <url>
        <loc>{{ url('/book/' . $book->id) }}</loc>
        <lastmod>{{ $book->updated_at ? $book->updated_at->toDateString() : now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
