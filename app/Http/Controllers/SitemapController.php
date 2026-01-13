<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\Book;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap
     */
    public function index(): Response
    {
        $hadiths = Hadith::select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $books = Book::select('id', 'updated_at')
            ->orderBy('id')
            ->get();

        $content = view('sitemap.index', [
            'hadiths' => $hadiths,
            'books' => $books,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Serve robots.txt
     */
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = <<<ROBOTS
User-agent: *
Allow: /

# Sitemap
Sitemap: {$sitemapUrl}

# Disallow admin areas
Disallow: /admin
Disallow: /dashboard
Disallow: /login
Disallow: /register
ROBOTS;

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
