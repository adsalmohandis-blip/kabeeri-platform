<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use App\Models\Site;
use Illuminate\Contracts\View\View;

class PublicContentEntryController extends Controller
{
    public function __invoke(Site $site, ContentEntry $contentEntry): View
    {
        if ($contentEntry->site_id !== $site->id) {
            abort(404);
        }

        if ($contentEntry->status !== 'published' || $contentEntry->visibility !== 'public') {
            abort(404);
        }

        return view('themes.kabeeri-starter.content-entry', [
            'site' => $site,
            'contentEntry' => $contentEntry,
        ]);
    }
}
