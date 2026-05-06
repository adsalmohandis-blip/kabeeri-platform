<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorTalent;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MallTalentController extends Controller
{
    public function index(Request $request): View
    {
        $talent = V13ExternalExperience::applyMallSearch(
            MallMirrorTalent::query()->where('mirror_status', 'published'),
            'talent',
            $request->query('q'),
        )
            ->orderBy('display_name')
            ->paginate(24)
            ->withQueryString();

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
