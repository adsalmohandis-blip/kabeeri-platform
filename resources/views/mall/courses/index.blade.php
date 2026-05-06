<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <h1>Courses</h1>

        @forelse ($courses as $course)
            <article>
                <h2>
                    <a href="{{ route('mall.courses.show', $course) }}">{{ $course->course_name }}</a>
                </h2>

                @if ($course->training_type)
                    <p>{{ $course->training_type }}</p>
                @endif

                @if ($course->description)
                    <p>{{ $course->description }}</p>
                @endif
            </article>
        @empty
            <p>No courses are published yet.</p>
        @endforelse

        {{ $courses->links() }}
    </main>
</body>
</html>
