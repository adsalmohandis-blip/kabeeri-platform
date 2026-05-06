<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <a href="{{ route('mall.talent.index') }}">Talent</a>

        <h1>{{ $talent->display_name }}</h1>

        @if ($talent->headline)
            <p>{{ $talent->headline }}</p>
        @endif

        @if ($talent->bio)
            <p>{{ $talent->bio }}</p>
        @endif

        @if ($talent->skills)
            <p>{{ implode(', ', $talent->skills) }}</p>
        @endif

        @if ($talent->location_label)
            <p>{{ $talent->location_label }}</p>
        @endif
    </main>
</body>
</html>
