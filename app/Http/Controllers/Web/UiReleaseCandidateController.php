<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\Ui\V14UiReleaseCandidate;
use Illuminate\View\View;

class UiReleaseCandidateController extends Controller
{
    public function __invoke(): View
    {
        return view('ui.v14-release-candidate', [
            'data' => V14UiReleaseCandidate::all(),
        ]);
    }
}
