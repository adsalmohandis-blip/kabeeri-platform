<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorCourse;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MallCourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = V13ExternalExperience::applyMallSearch(
            MallMirrorCourse::query()->where('mirror_status', 'published'),
            'courses',
            $request->query('q'),
        )
            ->orderBy('course_name')
            ->paginate(24)
            ->withQueryString();

        return view('mall.courses.index', [
            'courses' => $courses,
            'pageTitle' => 'Courses',
        ]);
    }

    public function show(MallMirrorCourse $course): View
    {
        if ($course->mirror_status !== 'published') {
            abort(404);
        }

        return view('mall.courses.show', [
            'course' => $course,
            'pageTitle' => $course->course_name,
        ]);
    }
}
