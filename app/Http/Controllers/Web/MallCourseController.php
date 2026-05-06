<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MallMirrorCourse;
use Illuminate\Contracts\View\View;

class MallCourseController extends Controller
{
    public function index(): View
    {
        $courses = MallMirrorCourse::query()
            ->where('mirror_status', 'published')
            ->orderBy('course_name')
            ->paginate(24);

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
