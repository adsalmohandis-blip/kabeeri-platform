<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
</head>
<body>
    <main>
        <a href="{{ route('mall.travel.index') }}">Travel and Tourism</a>

        <h1>{{ $listing->title }}</h1>

        @if ($listing->destination)
            <p>{{ $listing->destination }}</p>
        @endif

        @if ($listing->price_from)
            <p>{{ $listing->currency }} {{ $listing->price_from }}</p>
        @endif

        @if ($listing->description)
            <p>{{ $listing->description }}</p>
        @endif
    </main>
</body>
</html>
