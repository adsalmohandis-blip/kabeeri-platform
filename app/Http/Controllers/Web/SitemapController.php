<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use Illuminate\Http\Response;
use Illuminate\Support\HtmlString;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $entries = ContentEntry::query()
            ->with('site')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where('noindex', false)
            ->orderBy('site_id')
            ->orderBy('slug')
            ->get();

        $urls = $entries->map(function (ContentEntry $entry): string {
            $location = route('public.content-entry.show', [
                'site' => $entry->site,
                'contentEntry' => $entry,
            ]);

            return implode("\n", [
                '    <url>',
                '        <loc>'.e($location).'</loc>',
                '        <lastmod>'.e($entry->updated_at?->toAtomString() ?? now()->toAtomString()).'</lastmod>',
                '    </url>',
            ]);
        })->implode("\n");

        $xml = new HtmlString(implode("\n", [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
            $urls,
            '</urlset>',
            '',
        ]));

        return response((string) $xml, 200, ['Content-Type' => 'application/xml']);
    }
}
