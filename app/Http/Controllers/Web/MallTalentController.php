<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorTalent;
use Illuminate\Contracts\View\View;

class MallTalentController extends Controller
{
    public function index(): View
    {
        $talent = MallMirrorTalent::query()
            ->where('mirror_status', 'published')
            ->orderBy('display_name')
            ->paginate(24);

        return view('mall.talent.index', [
            'talent' => $talent,
            'pageTitle' => 'Talent',
        ]);
    }

    public function show(MallMirrorTalent $talent): View
    {
        if ($talent->mirror_status !== 'published') {
            abort(404);
        }

        return view('mall.talent.show', [
            'talent' => $talent,
            'pageTitle' => $talent->display_name,
        ]);
    }
}
