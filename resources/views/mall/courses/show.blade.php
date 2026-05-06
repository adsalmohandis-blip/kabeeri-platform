<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <a href="{{ route('mall.courses.index') }}">Courses</a>

        <h1>{{ $course->course_name }}</h1>

        @if ($course->training_type)
            <p>{{ $course->training_type }}</p>
        @endif

        @if ($course->delivery_mode)
            <p>{{ $course->delivery_mode }}</p>
        @endif

        @if ($course->price)
            <p>{{ $course->currency }} {{ $course->price }}</p>
        @endif

        @if ($course->description)
            <p>{{ $course->description }}</p>
        @endif
    </main>
</body>
</html>
